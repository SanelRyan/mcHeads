<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

function getUUIDFromUsername($username) {
    $url = "https://api.mojang.com/users/profiles/minecraft/" . urlencode($username);
    $response = file_get_contents($url);
    if ($response === false) {
        return null;
    }
    $data = json_decode($response, true);
    return $data['id'] ?? null;
}

function getSkinData($uuid) {
    $url = "https://sessionserver.mojang.com/session/minecraft/profile/" . $uuid;
    $response = file_get_contents($url);
    if ($response === false) {
        return null;
    }
    $data = json_decode($response, true);
    $textures = json_decode(base64_decode($data['properties'][0]['value']), true);
    return $textures['textures']['SKIN']['url'] ?? null;
}

function generateHeadImage($skinUrl, $is3D = false) {
    $skin = imagecreatefrompng($skinUrl);
    if (!$skin) {
        return null;
    }

    $headSize = 64;
    $output = imagecreatetruecolor($headSize, $headSize);
    imagealphablending($output, false);
    imagesavealpha($output, true);
    $transparent = imagecolorallocatealpha($output, 0, 0, 0, 127);
    imagefill($output, 0, 0, $transparent);

    for ($x = 0; $x < $headSize; $x++) {
        for ($y = 0; $y < $headSize; $y++) {
            $srcX = 8 + ($x / $headSize) * 8;
            $srcY = 8 + ($y / $headSize) * 8;
            $rgb = imagecolorat($skin, $srcX, $srcY);
            $alpha = ($rgb >> 24) & 0xFF;
            if ($alpha < 127) {
                $rgb = imagecolorallocate($output, 
                    ($rgb >> 16) & 0xFF,
                    ($rgb >> 8) & 0xFF,
                    $rgb & 0xFF
                );
                imagesetpixel($output, $x, $y, $rgb);
            }
        }
    }

    if (isset($_GET['withLayers'])) {
        for ($x = 0; $x < $headSize; $x++) {
            for ($y = 0; $y < $headSize; $y++) {
                $srcX = 40 + ($x / $headSize) * 8;
                $srcY = 8 + ($y / $headSize) * 8;
                $rgb = imagecolorat($skin, $srcX, $srcY);
                $alpha = ($rgb >> 24) & 0xFF;
                if ($alpha < 127) {
                    $rgb = imagecolorallocate($output, 
                        ($rgb >> 16) & 0xFF,
                        ($rgb >> 8) & 0xFF,
                        $rgb & 0xFF
                    );
                    imagesetpixel($output, $x, $y, $rgb);
                }
            }
        }
    }

    imagedestroy($skin);
    return $output;
}

$username = $_GET['username'] ?? null;
$uuid = $_GET['uuid'] ?? null;
$withLayers = isset($_GET['withLayers']);

if (!$username && !$uuid) {
    echo json_encode(['error' => 'Please provide either username or UUID']);
    exit;
}

if ($username) {
    $uuid = getUUIDFromUsername($username);
    if (!$uuid) {
        echo json_encode(['error' => 'Username not found']);
        exit;
    }
}

$skinUrl = getSkinData($uuid);
if (!$skinUrl) {
    echo json_encode(['error' => 'Could not fetch skin data']);
    exit;
}

$headImage = generateHeadImage($skinUrl);
if (!$headImage) {
    echo json_encode(['error' => 'Could not generate head image']);
    exit;
}

header('Content-Type: image/png');
imagepng($headImage);
imagedestroy($headImage);
