import { mergeConfig, ResolvedConfig, ResolvedServerUrls } from 'vite';
import { resolve } from 'path';
import { existsSync, mkdirSync, rmSync, writeFileSync } from 'fs';

import type { Plugin, UserConfig, ViteDevServer } from 'vite';

const configFile = 'vite.themeplate.json';
const defaultUrls = {
	local: [],
	network: [],
};

export default function themeplate(): Plugin {
	let resolvedConfig: ResolvedConfig;

	function writeConfig( isBuild: boolean, urls: ResolvedServerUrls = defaultUrls ) {
		const file = resolve( resolvedConfig.root, configFile );
		const outDir = resolvedConfig.build.outDir;
		const data = {
			outDir,
			isBuild,
			urls,
		};

		writeFileSync( file, JSON.stringify( data, null, 2 ), 'utf8' );
	}

	return {
		name: 'vite-plugin-themeplate',
		enforce: 'post',

		config( config: UserConfig ) {
			return mergeConfig( config, {
				build: {
					manifest: true,
				},
			} )
		},

		configResolved( config: ResolvedConfig ) {
			resolvedConfig = config;
		},

		writeBundle() {
			writeConfig( true );
		},

		configureServer( server: ViteDevServer ) {
			const { config, httpServer } = server;
			const outFile = resolve( config.root, configFile );

			const clean = () => {
				if ( existsSync( outFile ) ) {
					rmSync( outFile );
				}
			}

			process.on( 'exit', clean );
			process.on( 'SIGINT', process.exit );
			process.on( 'SIGTERM', process.exit );
			process.on( 'SIGHUP', process.exit );

			httpServer?.once( 'listening', () => {
				const checker = setInterval( () => {
					if ( null !== server.resolvedUrls ) {
						if ( ! existsSync( config.root ) ) {
							mkdirSync( config.root );
						}

						writeConfig( false, server.resolvedUrls );
						clearInterval( checker );
					}
				}, 0 );
			} );
		},
	};
}
