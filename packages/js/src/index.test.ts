import * as fs from 'fs';
import * as path from 'path';
import { afterEach, beforeEach, describe, expect, it, vi } from "vitest";

import type { NormalizedOutputOptions, OutputBundle } from 'rollup';
import type { ConfigEnv, ResolvedConfig, ResolvedServerUrls, ViteDevServer } from 'vite';

import { baseConfig, mockedBanner, mockedInputs, resolvedConfig, resolvedInputs } from '../tests/config';
import { configFile, defaultConfig, defaultUrls } from './config';
import themeplate from './index';

let mockedUrls: ResolvedServerUrls = defaultUrls
let mockedEntries = resolvedInputs

beforeEach( () => {
	vi.mock( 'fs' );
	vi.mock( 'path' );

	vi.mocked( fs.writeFileSync ).mockImplementation(
		( path: fs.PathOrFileDescriptor, data: string|ArrayBufferView ) => {
			if (
				typeof path === 'string' &&
				path.includes( configFile )
			) {
				expect( data ).toBe(
					JSON.stringify( {
						outDir: resolvedConfig.build.outDir,
						isBuild: resolvedConfig.isProduction,
						urls: mockedUrls,
						...mockedEntries
					}, null, 2 )
				);
			}
		}
	);

	vi.mocked( path.relative ).mockImplementation( ( ...paths: string[] ) =>
		paths.join('/')
	);
	vi.mocked( path.resolve ).mockImplementation( ( ...paths: string[] ) =>
		paths.join('/')
	);

	vi.mocked( path.extname ).mockImplementation( ( path: string ) => {
		const ext = path.split( '.' ).pop() || ''

		return ext ? `.${ext}` : '';
	} );
} );

afterEach( () => {
	vi.resetAllMocks();

	mockedUrls = defaultUrls
	mockedEntries = resolvedInputs
	resolvedConfig.build.rollupOptions.input = mockedInputs
} );

describe( 'config', () => {
	const mockedEnv = { mode: 'development' } as ConfigEnv;

	it( 'should always set the defaults', () => {
		expect( themeplate().config( {}, mockedEnv ) ).toEqual( {
			base: '/',
			...defaultConfig,
		} );
	} );

	it( 'should merge user configuration', () => {
		expect(
			themeplate().config(
				{
					base: '/custom-base',
					build: { ...baseConfig.build, outDir: 'custom' },
					root: 'custom-root',
				},
				mockedEnv,
			)
		).toEqual( {
			base: '/custom-base',
			build: {
				...baseConfig.build,
				manifest: true,
				outDir: 'custom',
			},
			server: {
				headers: {
					'Access-Control-Allow-Origin': '*',
				},
			},
			root: 'custom-root',
		} );
	} );

	it( 'should fail if no input is provided', () => {
		expect(
			() => themeplate().configResolved(
				{ build: { rollupOptions: {} } } as ResolvedConfig
			)
		).toThrowError();
	} );
} )

describe( 'build', () => {
	it( 'should write resolved config file with expected data', () => {
		const plugin = themeplate();

		plugin.configResolved( resolvedConfig );
		plugin.writeBundle( {} as NormalizedOutputOptions, {} as OutputBundle );
	} );

	it( 'should write resolved config file with un-named entries', () => {
		const plugin = themeplate();

		mockedEntries = { ...mockedEntries, entryNames: {} };
		resolvedConfig.build.rollupOptions.input = Object.values( mockedInputs );

		plugin.configResolved( resolvedConfig );
		plugin.writeBundle( {} as NormalizedOutputOptions, {} as OutputBundle );
	} );

	it( 'should only prepend banner to entry chunks and css assets', () => {
		const plugin = themeplate( undefined, mockedBanner );
		const bundle = {
			'main.js': {
				type: 'chunk',
				isEntry: true,
				code: 'console.log(true)'
			},
			'main.css': {
				type: 'asset',
				source: 'body{color:red}'
			},
			'shared.js': {
				type: 'chunk',
				isEntry: false,
				code: 'export default {}'
			}
		} as unknown as OutputBundle;
		const bannerMap = {
			'main.js': true,
			'main.css': true,
			'shared.js': false
		} as const;

		plugin.configResolved( resolvedConfig );
		plugin.writeBundle( {} as NormalizedOutputOptions, bundle );

		for ( const [ fileName, output ] of Object.entries( bundle ) ) {
			const shouldAddBanner = bannerMap[fileName as keyof typeof bannerMap];
			const codeSource = output.type === 'chunk' ? output.code : output.source;
			let assertion = expect( fs.writeFileSync )

			if ( ! shouldAddBanner ) {
				assertion = assertion.not;
			}

			assertion.toHaveBeenCalledWith(
				expect.stringContaining( `${resolvedConfig.build.outDir}/${fileName}` ),
				expect.stringContaining(`/*! ${mockedBanner} */\n${codeSource}`)
			);
		}
	} );
} );

describe( 'dev', () => {
	const devServer: ViteDevServer = {
		config: { ...resolvedConfig, isProduction: false },
		httpServer: {
			once: vi.fn()
		},
		ws: {},
		watcher: {add: vi.fn(), on: vi.fn()},
		resolvedUrls: {
			local: ['http://localhost:5173'],
			network: []
		}
	} as unknown as ViteDevServer;

	beforeEach( () => {
		vi.useFakeTimers();
	} );

	it( 'should set up server listeners and clean up on exit', () => {
		const plugin = themeplate();

		plugin.configResolved( resolvedConfig );
		plugin.configureServer( devServer );

		vi.spyOn( fs, 'existsSync' ).mockReturnValue( true )
		process.emit( 'exit', 0 );
		expect( devServer.httpServer?.once ).toHaveBeenCalledWith(
			'listening',
			expect.any( Function )
		);
		expect( fs.rmSync ).toHaveBeenCalledWith(
			expect.stringContaining( configFile )
		);
	} );

	it( 'should write config after the server is ready', () => {
		const plugin = themeplate();

		mockedUrls = devServer.resolvedUrls!;
		plugin.configResolved( resolvedConfig );
		plugin.configureServer( devServer );

		// @ts-ignore
		devServer.httpServer?.once.mock.calls[0][1]();
		vi.runAllTimers();

		expect( fs.existsSync ).toHaveBeenCalledWith( resolvedConfig.root );
		expect( fs.mkdirSync ).toHaveBeenCalledWith( resolvedConfig.root );
	} );
} );
