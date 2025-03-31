
import type { ResolvedConfig, UserConfig } from 'vite';

import pkg from '../package.json';

export const mockedBanner = [
	`${pkg.title} ${pkg.version}`,
	`Copyright (c) ${new Date().getFullYear()} ${pkg.author.name}`,
	`Licensed under ${pkg.license}.`,
].join( '\n * ' );

export const mockedInputs = {
	style: 'src/main.css',
	script: 'src/main.js',
	sub: 'src/sub.js',
};

export const baseConfig: UserConfig = {
	build: {
		outDir: '',
		rollupOptions: {
			input: mockedInputs,
		},
	},
};

export const resolvedConfig: ResolvedConfig = {
	isProduction: true,
	root: 'out',
	...baseConfig,
} as unknown as ResolvedConfig;

export const resolvedInputs = {
	entryNames: Object.entries( mockedInputs ).reduce( ( acc, [ name, path ] ) => {
		acc[ name ] = `${resolvedConfig.root}/${path}`;
		return acc;
	}, {} as { [ name: string ]: string } ),
	entries: Object.values( mockedInputs ).map( path => `${resolvedConfig.root}/${path}` ),
}
