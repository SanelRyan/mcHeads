# Minecraft Head Generator

A PHP-based API that generates Minecraft player head images from usernames or UUIDs. This tool fetches player skin data from Mojang's API and creates a 2D or 3D head image.

## Features

-   Generate Minecraft player head images from usernames or UUIDs
-   Supports only 2D
-   Optional layer rendering for more detailed heads
-   Error handling for invalid usernames or failed requests

## Requirements

-   PHP 7.0 or higher
-   GD Library enabled in PHP
-   Allow URL fopen enabled in PHP

## Installation

1. Clone this repository to your web server
2. Ensure the PHP GD extension is installed and enabled
3. Make sure your server has permission to make external HTTP requests

## Usage

### Basic Usage

```
http://your-domain/mcHead.php?username=PlayerName
```

or

```
http://your-domain/mcHead.php?uuid=PlayerUUID
```

### Parameters

-   `username`: Minecraft player username
-   `uuid`: Minecraft player UUID
-   `withLayers`: (Optional) Set to true to include the second layer of the head

### Example

```
http://your-domain/mcHead.php?username=Notch&withLayers=true
```

## API Response

The API returns a PNG image of the player's head. In case of errors, it returns a JSON response with an error message.

### Success Response

-   Content-Type: image/png
-   Returns: PNG image of the player's head

### Error Response

-   Content-Type: application/json
-   Returns: JSON object with error message

```json
{
	"error": "Error message here"
}
```

## Error Handling

The API handles various error cases:

-   Missing username or UUID
-   Invalid username
-   Failed skin data fetch
-   Image generation failure

## Security

-   Input is properly sanitized

## License

This project is open source and available under the MIT License.

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.
