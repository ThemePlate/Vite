# ThemePlate Vite

## Usage

```php
use ThemePlate\Vite;

add_action( 'wp_enqueue_scripts', function() {
	$vite = new Vite( get_stylesheet_directory() . '/dist/manifest.json', get_stylesheet_directory_uri() );

	$vite->style( 'main-style', 'src/main.css' );
	$vite->script( 'main-script', 'src/main.js' );
	$vite->action();
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
