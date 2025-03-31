import { defineConfig } from 'vite';
import themeplate from '../src/index';
import { baseConfig, mockedBanner } from './config';

const withBanner = 'true' === process.env.BANNER;
const banner = withBanner ? mockedBanner : undefined;
const directory = 'production' === process.env.NODE_ENV ? 'build' : 'dev';

export default defineConfig( {
	root: directory + ( withBanner ? '-banner' : '' ),
	plugins: [
		themeplate( undefined, banner ),
	],
	...baseConfig,
} );
