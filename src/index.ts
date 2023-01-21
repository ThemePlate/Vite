import { mergeConfig } from 'vite';
import { resolve } from 'path';
import { existsSync, mkdirSync, rmSync, writeFileSync } from 'fs';

import type { Plugin, UserConfig, ViteDevServer } from 'vite';

export default function themeplate(): Plugin {
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

		configureServer( server: ViteDevServer ) {
			const { config, httpServer } = server;
			const outDir = resolve( config.root, config.build.outDir );
			const outFile = resolve( outDir, 'themeplate' );

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
						if ( ! existsSync( outDir ) ) {
							mkdirSync( outDir );
						}

						writeFileSync( outFile, server.resolvedUrls.local[0] );
						clearInterval( checker );
					}
				}, 0 );
			} );
		},
	};
}
