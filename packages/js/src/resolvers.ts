import { existsSync } from 'fs';
import { dirname, relative, resolve } from 'path';
import { normalizePath } from 'vite';

import type { UserConfig } from 'vite';

export function resolveWpRoot( configRoot: string ) {
	let directory = resolve( process.cwd(), configRoot );

	const exists = ( directory: string ) => {
		return (
			existsSync( resolve( directory, 'wp-config.php' ) ) ||
			(
				existsSync( resolve( directory, 'wp-blog-header.php' ) ) &&
				existsSync( resolve( directory, 'wp-load.php' ) )
			)
		);
	}

	while ( dirname( directory ) !== directory && ! exists( directory ) ) {
		directory = dirname( directory );
	}

	if ( exists( directory ) ) {
		return `/${ normalizePath( relative( directory, configRoot ) ) }/`;
	}

	return '/';
}

export function resolveBase( mode: string, config: UserConfig ) {
	if ( 'development' === mode ) {
		return '/';
	}

	return resolveWpRoot( config?.root ?? '' ) + ( config.build?.outDir ?? 'dist' );
}