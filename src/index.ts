import { mergeConfig, ResolvedConfig, ResolvedServerUrls } from 'vite';
import { extname, relative, resolve } from 'path';
import { existsSync, mkdirSync, rmSync, writeFileSync } from 'fs';

import type { Plugin, UserConfig, ViteDevServer } from 'vite';

const configFile = 'vite.themeplate.json';
const defaultUrls = {
	local: [],
	network: [],
};

export default function themeplate(): Plugin {
	let resolvedConfig: ResolvedConfig;

	function writeConfig( urls: ResolvedServerUrls = defaultUrls ) {
		const file = resolve( resolvedConfig.root, configFile );
		const isBuild = resolvedConfig.isProduction;
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
			writeConfig();
		},

		configureServer( server: ViteDevServer ) {
			const { config, httpServer, ws, watcher } = server;
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

						writeConfig( server.resolvedUrls );
						clearInterval( checker );
					}
				}, 0 );
			} );

			const reload = ( path: string ) => {
				if ( '.php' === extname( path ) ) {
					config.logger.info( `page reload ${ relative( config.root, path ) }`, { timestamp: true } );
					ws.send( {
						type: 'full-reload',
						path
					} );
				}
			}

			watcher.on( 'add', reload )
			watcher.on( 'change', reload )
		},
	};
}
