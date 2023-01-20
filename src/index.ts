import { resolve } from 'path';
import { existsSync, mkdirSync, writeFileSync } from 'fs';

import type { Plugin, ViteDevServer } from 'vite';

export default function themeplate(): Plugin {
	return {
		name: 'vite-plugin-themeplate',
		enforce: 'post',

		configureServer( server: ViteDevServer ) {
			const { config, httpServer } = server;

			httpServer?.once( 'listening', () => {
				const checker = setInterval( () => {
					if ( null !== server.resolvedUrls ) {
						const outDir = resolve( config.root, config.build.outDir );
						const outFile = resolve( outDir, 'themeplate' );

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
