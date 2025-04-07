import { existsSync, mkdirSync, rmSync, writeFileSync } from 'fs';
import { extname, relative, resolve } from 'path';
import { mergeConfig } from 'vite';
import { configFile, defaultConfig, defaultUrls, ensure } from './config';
import { normalizeEntries, normalizeEntryNames } from './helpers';
import { resolveBase } from './resolvers';

import type { NormalizedOutputOptions, OutputBundle } from 'rollup';
import type { ConfigEnv, ResolvedConfig, ResolvedServerUrls, UserConfig, ViteDevServer } from 'vite';

export default function themeplate( path: string | readonly string[] = [], banner?: string ) {
	let resolvedConfig: ResolvedConfig;

	function writeConfig( urls: ResolvedServerUrls = defaultUrls ) {
		const file = resolve( resolvedConfig.root, configFile );
		const entryNames = normalizeEntryNames( resolvedConfig.build.rollupOptions.input!, resolvedConfig.root );
		const entries = normalizeEntries( resolvedConfig.build.rollupOptions.input!, resolvedConfig.root );
		const isBuild = resolvedConfig.isProduction;
		const outDir = resolvedConfig.build.outDir;
		const data = {
			outDir,
			isBuild,
			urls,
			entryNames,
			entries,
		};

		writeFileSync( file, JSON.stringify( data, null, 2 ), 'utf8' );
	}

	return {
		name: 'vite-plugin-themeplate',
		enforce: 'post' as 'post' | 'pre' | undefined,

		config( config: UserConfig, env: ConfigEnv ) {
			const base = config.base ?? resolveBase( env.mode, config );

			return mergeConfig( config, {
				base,
				...defaultConfig,
			} )
		},

		configResolved( config: ResolvedConfig ) {
			if ( undefined === config.build.rollupOptions.input ) {
				throw new Error( 'You must supply options.input to rollup' );
			}

			resolvedConfig = config;
		},

		writeBundle( options: NormalizedOutputOptions, bundle: OutputBundle ) {
			writeConfig();

			if ( undefined === banner ) {
				return;
			}

			for ( const [ fileName, output ] of Object.entries( bundle ) ) {
				if (
					( 'chunk' === output.type && output.isEntry ) ||
					( 'asset' === output.type && '.css' === extname( fileName ) )
				) {
					const { root, build: { outDir } } = resolvedConfig;
					const file = resolve( root, outDir, fileName );
					const data = 'chunk' === output.type ? output.code : output.source;

					writeFileSync( file, `${ ensure( banner ) }\n${ data }` );
				}
			}
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
			process.on( 'SIGINT', () => process.exit() );
			process.on( 'SIGTERM', () => process.exit() );
			process.on( 'SIGHUP', () => process.exit() );

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
