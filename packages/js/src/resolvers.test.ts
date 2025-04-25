import * as fs from 'fs';
import { describe, expect, it, vi } from 'vitest';

import { resolveBase, resolveWpRoot } from './resolvers';

function lastDirectory( path: string, levels: number ) {
	return `/${path.split( '/' ).filter( part => part !== '' ).slice( -levels ).join( '/' )}/`;
}

describe( 'lastDirectory', () => {
	it( 'should return the correct path on specified levels', () => {
		expect( lastDirectory( process.cwd(), 0 ) ).toBe( process.cwd() + '/' );
		expect( lastDirectory( process.cwd(), 1 ) ).toBe( '/js/' );
		expect( lastDirectory( process.cwd(), 2 ) ).toBe( '/packages/js/' );
	} );
} );

describe( 'resolveWpRoot', () => {
	it( 'should return the correct path for a valid directory', () => {
		vi.mock( 'fs' );
		vi.spyOn( fs, 'existsSync' )
			.mockReturnValueOnce( false )
			.mockReturnValueOnce( false )
			.mockReturnValue( true )

		expect( resolveWpRoot( process.cwd() ) ).toBe( lastDirectory( process.cwd(), 1 ) );
		vi.clearAllMocks().resetAllMocks();
	} );

	it( 'should return / for an invalid or non-existent directory', () => {
		vi.mock( 'fs' );
		vi.spyOn( fs, 'existsSync' ).mockReturnValue( false );

		expect( resolveWpRoot( 'path/to/invalid/or/nonexistent' ) ).toBe( '/' );
		vi.clearAllMocks().resetAllMocks();
	} );

	it( 'should return normalized path "/"', () => {
		vi.mock( 'fs' );
		vi.spyOn( fs, 'existsSync' )
			.mockReturnValue( true )

		expect( resolveWpRoot( process.cwd() ) ).toBe( '/' );
		vi.clearAllMocks().resetAllMocks();
	} );

	it( 'should handle an empty provided path value', () => {
		expect( resolveWpRoot( '' ) ).toBe( '/' );
	} )
} );

describe( 'resolveBase', () => {
	it( 'should return / in development mode', () => {
		expect( resolveBase( 'development', {} ) ).toBe( '/' );
	} );

	it( 'should return the correct path in production mode', () => {
		vi.mock( 'fs' );
		vi.spyOn( fs, 'existsSync' )
			.mockReturnValueOnce( false )
			.mockReturnValueOnce( false )
			.mockReturnValueOnce( false )
			.mockReturnValueOnce( false )
			.mockReturnValue( true );

		const config = { root: process.cwd(), build: { outDir: 'dist' } };

		expect( resolveBase( 'production', config ) ).toBe( lastDirectory( process.cwd(), 2 ) + 'dist' );
		vi.clearAllMocks().resetAllMocks();
	} );

	it( 'should return / for an invalid or non-existent directory', () => {
		vi.mock( 'fs' );
		vi.spyOn( fs, 'existsSync' ).mockReturnValue( false );

		expect( resolveBase( 'production', {} ) ).toBe( '/dist' );
		vi.clearAllMocks().resetAllMocks();
	} );

	it( 'should handle missing config properties gracefully', () => {
		vi.mock( 'fs' );
		vi.spyOn( fs, 'existsSync' )
			.mockReturnValueOnce( false )
			.mockReturnValueOnce( false )
			.mockReturnValueOnce( false )
			.mockReturnValueOnce( false )
			.mockReturnValue( true );

		expect( resolveBase( 'production', {} ) ).toBe( lastDirectory( process.cwd(), 2 ) + 'dist' );
		vi.clearAllMocks().resetAllMocks();
	} );
} );
