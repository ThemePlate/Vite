import { defineConfig } from 'vite';
import themeplate from '../src/index';

export default defineConfig( {
	plugins: [
		themeplate(),
	],
} );
