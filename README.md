# ThemePlate Vite

## Usage

```php
use ThemePlate\Vite;

add_action( 'wp_enqueue_scripts', function() {
	$vite = new Vite( get_stylesheet_directory() . '/dist/manifest.json', get_stylesheet_directory_uri() );

	// // For development
	// wp_enqueue_script( Vite::CLIENT, $vite->development( 'http://localhost:5173' ) );

	wp_enqueue_style( 'main-style', $vite->path( 'src/main.css' ) );
	wp_enqueue_script( 'main-script', $vite->path( 'src/main.js' ) );
} );
```
