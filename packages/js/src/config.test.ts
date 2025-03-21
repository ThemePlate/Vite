import { describe, expect, it } from 'vitest';

import { ensure } from './config';

describe( 'ensure', () => {
	it( 'should return the same string if it is already wrapped', () => {
		expect( ensure( '/*! comment */' ) ).toBe( '/*! comment */' );
	} );

	it( 'should wrap the string with /*! and */', () => {
		expect( ensure( 'comment' ) ).toBe( '/*! comment */' );
	} );

	it( 'should trim the string before wrapping', () => {
		expect( ensure( '  comment  ' ) ).toBe( '/*! comment */' );
	} );

	it( 'should handle incomplete strings', () => {
		expect( ensure( '/*! test' ) ).toBe( '/*! test */' );
		expect( ensure( 'test */' ) ).toBe( '/*! test */' );
	} );
} );
