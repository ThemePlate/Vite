# ThemePlate Vite

## Usage

`composer require themeplate/vite`

### functions.php

```php
use ThemePlate\Vite\Project;

$vite = new Project( get_stylesheet_directory(), get_stylesheet_directory_uri() );

// $vite->config->prefix( 'custom-' );

add_action( 'wp_enqueue_scripts', function() use ( $vite ) {
	$vite->style( 'main-style' );
	$vite->script( 'main-script' );
	$vite->action();
} );

add_action( 'enqueue_block_editor_assets', function() use ( $vite ) {
	$vite->style( 'editor-style' );
	$vite->script( 'editor-script' );
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
			input: {
				"main-style": "src/main.css",
				"main-script": "src/main.js",
				"editor-style": "src/editor.css",
				"editor-script": "src/editor.js",
			},
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
