# ThemePlate Vite

## Usage

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
