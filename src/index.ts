import { mergeConfig, normalizePath } from 'vite';
import { extname, relative, resolve, dirname } from 'path';
import { existsSync, mkdirSync, rmSync, writeFileSync } from 'fs';

import type { ConfigEnv, Plugin, ResolvedConfig, ResolvedServerUrls, UserConfig, ViteDevServer } from 'vite';
import type { InputOption } from 'rollup';

const configFile = 'vite.themeplate.json';
const defaultUrls = {
	local: [],
	network: [],
};

export default function themeplate( path: string | readonly string[] = [] ): Plugin {
	let resolvedConfig: ResolvedConfig;

	function writeConfig( urls: ResolvedServerUrls = defaultUrls ) {
		const file = resolve( resolvedConfig.root, configFile );
		const entries = normalizeEntries( resolvedConfig.build.rollupOptions.input! );
		const isBuild = resolvedConfig.isProduction;
		const outDir = resolvedConfig.build.outDir;
		const data = {
			outDir,
			isBuild,
			urls,
			entries,
		};

		function normalizeEntries( input: InputOption ): string[] {
			const paths: string[] = Array.isArray( input ) ? input as string[] : [input as string];

			return [ ...new Set( paths.map( path => relative( resolvedConfig.root, path ) ).map( normalizePath ) ) ];
		}

		writeFileSync( file, JSON.stringify( data, null, 2 ), 'utf8' );
	}

	function resolveWpRoot() {
		let directory = process.cwd();

		const exists = ( directory: string ) => {
			return existsSync( resolve( directory, 'wp-config.php' ) );
		}

		while ( dirname( directory ) !== directory && ! exists( directory ) ) {
			directory = dirname( directory );
		}

		if ( exists( directory ) ) {
			return `/${ normalizePath( relative( directory, '' ) ) }/`;
		}

		return '/';
	}

	function resolveBase( mode: string, config: UserConfig ) {
		if ( 'development' === mode ) {
			return '/';
		}

		return resolveWpRoot() + ( config.build?.outDir ?? 'dist' );
	}

	return {
		name: 'vite-plugin-themeplate',
		enforce: 'post',

		config( config: UserConfig, env: ConfigEnv ) {
			return mergeConfig( config, {
				base: resolveBase( env.mode, config ),
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

			watcher.add( path );
			watcher.on( 'add', reload )
			watcher.on( 'change', reload )
		},
	};
}
