# ThemePlate Vite

## Usage

`composer require themeplate/vite`

### functions.php
```php
use ThemePlate\Vite;

add_action( 'wp_enqueue_scripts', function() {
	$vite = new Vite( get_stylesheet_directory(), get_stylesheet_directory_uri() );

	$vite->style( 'src/main.css' );
	$vite->script( 'src/main.js' );
	$vite->action();
} );
```
`npm install vite-plugin-themeplate`

### vite.config.js
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
} );
```
#### Optional Parameters
```js
...
const watchPath = '../../plugins/custom-plugin';
const customBanner = '/*! Custom Theme v1.0 */';

themeplate( watchPath, customBanner );
...
```
> *Watch paths are relative to root and only for PHP files changes
