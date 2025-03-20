import { relative } from "path";
import { InputOption } from "rollup";
import { normalizePath } from "vite";

export function normalizeEntryNames( input: InputOption, root: string = process.cwd() ): { [ name: string ]: string } {
	if ( typeof input !== 'object' || Array.isArray( input ) ) {
		return {};
	}

	return Object.entries( input ).reduce( ( acc: { [ name: string ]: string }, [ key, value ] ) => {
		acc[ key ] = normalizePath( relative( root, value ) );
		return acc;
	}, {} );
}

export function normalizeEntries( input: InputOption, root: string = process.cwd() ): string[] {
	const paths: string[] = Array.isArray( input ) ? input as string[] : ( typeof input === 'object' ? Object.values( input ) : [input as string] );

	return [ ...new Set( paths.filter( path => path ).map( path => relative( root, path ) ).map( normalizePath ) )];
}
