import { defineConfig } from 'vite';
import themeplate from '../src/index';

export default defineConfig( {
	root: 'production' === process.env.NODE_ENV ? 'build' : 'dev',
	plugins: [
		themeplate(),
	],
	build: {
		outDir: '',
		rollupOptions: {
			input: [
				'src/main.css',
				'src/main.js',
			],
		},
	},
} );
