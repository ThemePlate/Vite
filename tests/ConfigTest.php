<?php

/**
 * @package ThemePlate
 */

namespace Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use ThemePlate\Vite\Config;

class ConfigTest extends AbstractTester {
	protected Config $config;

	protected function setUp(): void {
		parent::setUp();

		$this->config = new Config( $this->rootDir() );
	}

	public static function for_test_entry_name(): array {
		// phpcs:disable WordPress.Arrays.MultipleStatementAlignment.DoubleArrowNotAligned
		return array(
			'with known name' => array(
				'sub',
				true,
			),
			'with unknown name' => array(
				'shared',
				false,
			),
			'with bad name' => array(
				'',
				false,
			),
		);
		// phpcs:enable WordPress.Arrays.MultipleStatementAlignment.DoubleArrowNotAligned
	}

	protected function do_assert_entry_name( string $name, bool $is_known ): void {
		if ( $is_known ) {
			$this->assertNotEmpty( $this->config->entry( $name ) );
		} else {
			$this->assertEmpty( $this->config->entry( $name ) );
		}
	}

	#[DataProvider( 'for_test_entry_name' )]
	public function test_build_entry_name( string $name, bool $is_known ): void {
		$this->do_assert_entry_name( $name, $is_known );
	}

	#[DataProvider( 'for_test_entry_name' )]
	public function test_dev_entry_name( string $name, bool $is_known ): void {
		$this->do_assert_entry_name( $name, $is_known );
	}

	#[DataProvider( 'for_test_entry_name' )]
	public function test_unnamed_entry_name( string $name ): void {
		$this->do_assert_entry_name( $name, false );
	}

	public static function for_test_name_entry(): array {
		// phpcs:disable WordPress.Arrays.MultipleStatementAlignment.DoubleArrowNotAligned
		return array(
			'with known asset' => array(
				'../src/main.css',
				true,
			),
			'with bad asset' => array(
				'../src/bad.css',
				false,
			),
			'with unknown asset' => array(
				'../src/test.css',
				false,
			),
		);
		// phpcs:enable WordPress.Arrays.MultipleStatementAlignment.DoubleArrowNotAligned
	}

	protected function do_assert_name_entry( string $asset, bool $is_known ): void {
		if ( $is_known ) {
			$this->assertNotEmpty( $this->config->name( $asset ) );
		} else {
			$this->assertEmpty( $this->config->name( $asset ) );
		}
	}

	#[DataProvider( 'for_test_name_entry' )]
	public function test_build_name_entry( string $asset, bool $is_known ): void {
		$this->do_assert_name_entry( $asset, $is_known );
	}

	#[DataProvider( 'for_test_name_entry' )]
	public function test_dev_name_entry( string $asset, bool $is_known ): void {
		$this->do_assert_name_entry( $asset, $is_known );
	}

	#[DataProvider( 'for_test_name_entry' )]
	public function test_unnamed_name_entry( string $asset ): void {
		$this->do_assert_name_entry( $asset, false );
	}

	protected function do_assert_handle( string $asset, bool $is_known ): void {
		if ( $is_known ) {
			$this->assertNotEmpty( $this->config->handle( $asset ) );
		} else {
			$this->assertEmpty( $this->config->handle( $asset ) );
		}
	}

	#[DataProvider( 'for_test_entry_name' )]
	#[DataProvider( 'for_test_name_entry' )]
	public function test_build_handle( string $asset, bool $is_known ): void {
		$this->do_assert_handle( $asset, $is_known );
	}

	#[DataProvider( 'for_test_entry_name' )]
	#[DataProvider( 'for_test_name_entry' )]
	public function test_dev_handle( string $asset, bool $is_known ): void {
		$this->do_assert_handle( $asset, $is_known );
	}

	#[DataProvider( 'for_test_entry_name' )]
	public function test_unnamed_handle_random_strings( string $asset ): void {
		$this->do_assert_handle( $asset, false );
	}

	#[DataProvider( 'for_test_name_entry' )]
	public function test_unnamed_handle_by_entry( string $asset, bool $is_known ): void {
		$this->do_assert_handle( $asset, $is_known );
	}
}
