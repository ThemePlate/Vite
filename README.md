# ThemePlate Vite

## Usage

```php
use ThemePlate\Vite;

add_action( 'wp_enqueue_scripts', function() {
	$vite = new Vite( get_stylesheet_directory() . '/dist/manifest.json' );
	$base = get_stylesheet_directory_uri() . '/';

	wp_enqueue_style( 'main-style', $base . $vite->path( 'src/main.css' ) );
	wp_enqueue_script( 'main-script', $base . $vite->path( 'src/main.js' ) );
} );
```
