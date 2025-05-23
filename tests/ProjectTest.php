<?php

/**
 * @package ThemePlate
 */

namespace Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use ThemePlate\Vite\Project;
use function Brain\Monkey\Functions\expect;

class ProjectTest extends AbstractTester {
	protected Project $vite;
	protected string $root;


	protected function setUp(): void {
		parent::setUp();

		$root = $this->rootDir();
		$base = self::BASE_URL;

		if ( str_ends_with( $root, '-banner' ) ) {
			$base = $root;
		}

		$this->vite = new Project( $root, $base );
	}

	public function test_dev_mode_action_has_wanted_actions(): void {
		expect( 'wp_enqueue_script' )->once();

		$this->vite->action();
		$this->assertTrue( $this->vite->development() );
		$this->assertSame( 2, has_action( 'wp_head', 'ThemePlate\Resource\Handler->action()' ) );
	}

	public function test_build_mode_action_has_wanted_actions(): void {
		expect( 'wp_enqueue_script' )->never();

		$this->vite->action();
		$this->assertFalse( $this->vite->development() );
		$this->assertSame( 2, has_action( 'wp_head', 'ThemePlate\Resource\Handler->action()' ) );
	}

	/** @return array<string, array{0: string, 1: bool}> */
	public static function for_test_asset(): array {
		return array(
			'with known asset'   => array(
				'../src/main.css',
				true,
			),
			'with unknown asset' => array(
				'../src/test.css',
				false,
			),
		);
	}

	#[DataProvider( 'for_test_asset' )]
	public function test_dev_mode_path( string $entry ): void {
		$parse = parse_url( $this->vite->path( $entry ) ); // phpcs:ignore WordPress.WP.AlternativeFunctions.parse_url_parse_url

		$this->assertNotFalse( $parse );
		$this->assertArrayHasKey( 'scheme', $parse );
		$this->assertArrayHasKey( 'host', $parse );
		$this->assertNotEmpty( $parse['scheme'] ?? '' );
		$this->assertNotEmpty( $parse['host'] ?? '' );
		$this->assertNotSame( self::BASE_URL, $parse['scheme'] . '://' . $parse['host'] );
	}

	#[DataProvider( 'for_test_asset' )]
	public function test_build_mode_path( string $entry, bool $is_known ): void {
		$path  = $this->vite->path( $entry );
		$parse = parse_url( $path ); // phpcs:ignore WordPress.WP.AlternativeFunctions.parse_url_parse_url

		$this->assertNotFalse( $parse );
		$this->assertArrayHasKey( 'scheme', $parse );
		$this->assertArrayHasKey( 'host', $parse );
		$this->assertNotEmpty( $parse['scheme'] ?? '' );
		$this->assertNotEmpty( $parse['host'] ?? '' );
		$this->assertSame( self::BASE_URL, $parse['scheme'] . '://' . $parse['host'] );

		$slashed = trailingslashit( self::BASE_URL );

		if ( $is_known ) {
			$this->assertNotSame( $slashed . $entry, $path );
		} else {
			$this->assertSame( $slashed . $entry, $path );
		}
	}

	#[DataProvider( 'for_test_asset' )]
	public function test_dev_non_uri_path( string $entry ): void {
		$path = $this->vite->path( $entry, false );

		$slashed = trailingslashit( $this->vite->config->root );

		$this->assertSame( $slashed . $entry, $path );
	}

	#[DataProvider( 'for_test_asset' )]
	public function test_build_non_uri_path( string $entry, bool $is_known ): void {
		$path = $this->vite->path( $entry, false );

		$slashed = trailingslashit( $this->vite->config->root );

		if ( $is_known ) {
			$this->assertNotSame( $slashed . $entry, $path );
		} else {
			$this->assertSame( $slashed . $entry, $path );
		}
	}

	public function test_dev_mode_enqueue_entry_chunk_with_imports_and_css(): void {
		expect( 'wp_register_script' )->once();
		expect( 'wp_enqueue_script' )->once();
		expect( 'wp_enqueue_style' )->never();

		$this->vite->script( '../src/main.js' );
		$this->expectNotToPerformAssertions();
	}

	public function test_build_mode_enqueue_entry_chunk_with_imports_and_css(): void {
		expect( 'wp_register_script' )->once();
		expect( 'wp_enqueue_script' )->once();
		expect( 'wp_register_style' )->twice();
		expect( 'wp_enqueue_style' )->once();

		$this->vite->script( '../src/main.js' );
		$this->vite->style( '../src/main.css' );
		$this->expectNotToPerformAssertions();
	}

	public function test_dev_mode_register_and_enqueue_chunks(): void {
		expect( 'wp_register_script' )->once();
		expect( 'wp_enqueue_script' )->never();

		$this->vite->script( '../src/views/foo.js' );
		$this->expectNotToPerformAssertions();
	}

	public function test_build_mode_register_only_non_entry_chunk(): void {
		expect( 'wp_register_script' )->once();
		expect( 'wp_enqueue_script' )->never();

		$this->vite->script( '../src/views/foo.js' );
		$this->expectNotToPerformAssertions();
	}

	/** @return array<string, array{0: string, 1: bool}> */
	public static function for_build_banner_possibly(): array {
		return array(
			'with a css entry' => array(
				'../src/main.css',
				true,
			),
			'with a js entry'  => array(
				'../src/sub.js',
				true,
			),
			'with a non entry' => array(
				'../src/shared.js',
				false,
			),
		);
	}

	#[DataProvider( 'for_build_banner_possibly' )]
	public function test_build_maybe_banner( string $entry, bool $has_banner ): void {
		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		$content = file_get_contents( $this->vite->path( $entry ) );

		$this->assertIsString( $content );

		$position = strpos( $content, '/*! ThemePlate Vite' );

		if ( $has_banner ) {
			$this->assertSame( 0, $position );
		} else {
			$this->assertFalse( $position );
		}
	}
}
