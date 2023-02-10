import { defineConfig } from 'vite';
import themeplate from '../src/index';

const withBanner = 'true' === process.env.BANNER;

const pkg = require( '../package.json' );
const banner = withBanner ? [
	`/*! ${pkg.title} ${pkg.version}`,
	` * Copyright (c) ${new Date().getFullYear()} ${pkg.author.name}`,
	` * Licensed under ${pkg.license}.`,
	' */',
].join( '\n' ) : undefined;
const directory = 'production' === process.env.NODE_ENV ? 'build' : 'dev';

export default defineConfig( {
	root: directory + ( withBanner ? '-banner' : '' ),
	plugins: [
		themeplate( undefined, banner ),
	],
	build: {
		outDir: '',
		rollupOptions: {
			input: [
				'src/main.css',
				'src/main.js',
				'src/sub.js',
			],
		},
	},
} );
