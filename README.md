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
