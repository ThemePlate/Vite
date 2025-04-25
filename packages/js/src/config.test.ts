import { describe, expect, it } from 'vitest';

import { ensure } from './config';

describe( 'ensure', () => {
	it( 'should handle empty strings', () => {
		expect( ensure( '' ) ).toBe( '' );
		expect( ensure( ' ' ) ).toBe( '' );
		expect( ensure( '\n' ) ).toBe( '' );
		expect( ensure( ' \n' ) ).toBe( '' );
		expect( ensure( '\t' ) ).toBe( '' );
		expect( ensure( ' \t' ) ).toBe( '' );
		expect( ensure( ' \t\n' ) ).toBe( '' );
	} );

	it( 'should return the same string if it is already wrapped', () => {
		expect( ensure( '/*! comment */' ) ).toBe( '/*! comment */\n' );
	} );

	it( 'should wrap the string with /*! and */', () => {
		expect( ensure( 'comment' ) ).toBe( '/*! comment */\n' );
	} );

	it( 'should trim the string before wrapping', () => {
		expect( ensure( '  comment  ' ) ).toBe( '/*! comment */\n' );
		expect( ensure( '\ncomment\n\n' ) ).toBe( '/*! comment */\n' );
		expect( ensure( ' \n comment	\t' ) ).toBe( '/*! comment */\n' );
	} );

	it( 'should handle incomplete strings', () => {
		expect( ensure( '/*! test' ) ).toBe( '/*! test */\n' );
		expect( ensure( 'test */' ) ).toBe( '/*! test */\n' );
	} );
} );
