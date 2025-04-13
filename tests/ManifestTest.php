<?php

/**
 * @package ThemePlate
 */

namespace Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use ThemePlate\Vite\Config;
use ThemePlate\Vite\Manifest;

class ManifestTest extends AbstractTester {
	protected Manifest $manifest;

	protected function setUp(): void {
		parent::setUp();

		$this->manifest = new Manifest( new Config( $this->rootDir() ) );
	}

	/** @return array<string, array{0: string, 1: bool}> */
	public static function for_test_asset_path(): array {
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

	#[DataProvider( 'for_test_asset_path' )]
	public function test_dev_mode_asset( string $entry ): void {
		$this->assertEmpty( $this->manifest->asset( $entry ) );
	}

	#[DataProvider( 'for_test_asset_path' )]
	public function test_build_mode_asset( string $entry, bool $is_known ): void {
		if ( $is_known ) {
			$this->assertNotEmpty( $this->manifest->asset( $entry ) );
		} else {
			$this->assertEmpty( $this->manifest->asset( $entry ) );
		}
	}

	#[DataProvider( 'for_test_asset_path' )]
	public function test_dev_mode_path( string $entry ): void {
		$this->assertSame( $entry, $this->manifest->path( $entry ) );
	}

	#[DataProvider( 'for_test_asset_path' )]
	public function test_build_mode_path( string $entry ): void {
		$this->assertSame( $entry, $this->manifest->path( $entry ) );
	}

	#[DataProvider( 'for_test_asset_path' )]
	public function test_outdir_custom_path( string $entry ): void {
		$this->assertSame( 'custom/' . $entry, $this->manifest->path( $entry ) );
	}
}
