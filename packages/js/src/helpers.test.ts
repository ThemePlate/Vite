import { describe, expect, it } from 'vitest';

import { normalizeEntries, normalizeEntryNames } from './helpers';

const cwdLevel = process.cwd().split( '/' ).length - 1;
const pathParts = Array.from( { length: cwdLevel }, () => '..' );

const inputValue = {
	main: './src/index.js',
	secondary: 'src/secondary.js',
	absolute: process.cwd() + '/index.ts',
	invalid: '/path/to/src/index.js',
};
const expectedValue = {
	main: 'src/index.js',
	secondary: 'src/secondary.js',
	absolute: 'index.ts',
	invalid: pathParts.join( '/' ) + '/path/to/src/index.js',
};

describe( 'normalizeEntryNames', () => {
	it( 'should return an object with normalized entry names', () => {
		expect( normalizeEntryNames( inputValue ) ).toEqual( expectedValue );
	} );

	it( 'should return an empty object if input is a string', () => {
		expect( normalizeEntryNames( 'string' ) ).toEqual( {} );
	} );

	it( 'should return an empty object if input is an array', () => {
		expect( normalizeEntryNames( [ 'string' ] ) ).toEqual( {} );
	} );

	it( 'should return an empty object if input is an empty object', () => {
		expect( normalizeEntryNames( {} ) ).toEqual( {} );
	} );

	it( 'should return an empty object for non-object input', () => {
		expect( normalizeEntryNames( '' ) ).toEqual( {} );
		expect( normalizeEntryNames( [] ) ).toEqual( {} );
		expect( normalizeEntryNames( 'string' ) ).toEqual( {} );
		expect( normalizeEntryNames( ['string', 'test'] ) ).toEqual( {} );
	} );
} );

describe( 'normalizeEntries', () => {
	it( 'should return an array with normalized entry paths if input is object', () => {
		expect( normalizeEntries( inputValue ) ).toEqual( Object.values( expectedValue ) );
	} );

	it( 'should return an array with normalized entry paths if input is array', () => {
		expect( normalizeEntries( Object.values( inputValue ) ) ).toEqual( Object.values( expectedValue ) );
	} );

	it( 'should return an array with normalized entry paths if input is string', () => {
		expect( normalizeEntries( 'string' ) ).toEqual( [ 'string' ] );
	} );

	it( 'should return an empty array for empty input values', () => {
		expect( normalizeEntries( '' ) ).toEqual( [] );
		expect( normalizeEntries( [] ) ).toEqual( [] );
		expect( normalizeEntries( {} ) ).toEqual( [] );
	} );
} );
