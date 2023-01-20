# ThemePlate Vite

## Usage

```php
use ThemePlate\Vite;

add_action( 'wp_enqueue_scripts', function() {
	$vite = new Vite( get_stylesheet_directory() . '/dist/manifest.json', get_stylesheet_directory_uri() );

	wp_enqueue_script( Vite::CLIENT );
	wp_enqueue_style( 'main-style', $vite->path( 'src/main.css' ) );
	wp_enqueue_script( 'main-script', $vite->path( 'src/main.js' ) );
} );
```

```js
import { defineConfig } from 'vite';
import themeplate from 'vite-plugin-themeplate';

export default defineConfig( {
	plugins: [
		themeplate(),
	],
	build: {
		rollupOptions: {
			input: [
				'src/main.css',
				'src/main.js',
			],
		},
	},
	...
} );
```
