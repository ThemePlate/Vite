<?php

/**
 * @package ThemePlate
 */

namespace Tests;

use ThemePlate\Vite\Config;

class ConfigTest extends AbstractTest {
	protected Config $config;

	protected function setUp(): void {
		parent::setUp();

		$this->config = new Config( $this->rootDir() );
	}

	public function for_test_entry_name(): array {
		return array(
			'with known name'   => array(
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
	}

	protected function do_assert_entry_name( string $name, bool $is_known ): void {
		if ( $is_known ) {
			$this->assertNotEmpty( $this->config->entry( $name ) );
		} else {
			$this->assertEmpty( $this->config->entry( $name ) );
		}
	}

	/**
	 * @dataProvider for_test_entry_name
	 */
	public function test_build_entry_name( string $name, bool $is_known ): void {
		$this->do_assert_entry_name( $name, $is_known );
	}

	/**
	 * @dataProvider for_test_entry_name
	 */
	public function test_dev_entry_name( string $name, bool $is_known ): void {
		$this->do_assert_entry_name( $name, $is_known );
	}

	/**
	 * @dataProvider for_test_entry_name
	 */
	public function test_unnamed_entry_name( string $name ): void {
		$this->do_assert_entry_name( $name, false );
	}

	public function for_test_name_entry(): array {
		return array(
			'with known asset'   => array(
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
	}

	protected function do_assert_name_entry( string $asset, bool $is_known ): void {
		if ( $is_known ) {
			$this->assertNotEmpty( $this->config->name( $asset ) );
		} else {
			$this->assertEmpty( $this->config->name( $asset ) );
		}
	}

	/**
	 * @dataProvider for_test_name_entry
	 */
	public function test_build_name_entry( string $asset, bool $is_known ): void {
		$this->do_assert_name_entry( $asset, $is_known );
	}

	/**
	 * @dataProvider for_test_name_entry
	 */
	public function test_dev_name_entry( string $asset, bool $is_known ): void {
		$this->do_assert_name_entry( $asset, $is_known );
	}

	/**
	 * @dataProvider for_test_name_entry
	 */
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

	/**
	 * @dataProvider for_test_entry_name
	 * @dataProvider for_test_name_entry
	 */
	public function test_build_handle( string $asset, bool $is_known ): void {
		$this->do_assert_handle( $asset, $is_known );
	}

	/**
	 * @dataProvider for_test_entry_name
	 * @dataProvider for_test_name_entry
	 */
	public function test_dev_handle( string $asset, bool $is_known ): void {
		$this->do_assert_handle( $asset, $is_known );
	}

	/**
	 * @dataProvider for_test_entry_name
	 */
	public function test_unnamed_handle_random_strings( string $asset ): void {
		$this->do_assert_handle( $asset, false );
	}

	/**
	 * @dataProvider for_test_name_entry
	 */
	public function test_unnamed_handle_by_entry( string $asset, bool $is_known ): void {
		$this->do_assert_handle( $asset, $is_known );
	}
}
