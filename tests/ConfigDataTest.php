<?php

/**
 * @package ThemePlate
 */

namespace Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ThemePlate\Vite\Config;
use ThemePlate\Vite\ConfigData;

class ConfigDataTest extends TestCase {

	/** @return array<string, array{0: array<mixed>, 1: array<mixed>}> */
	public static function for_test_create(): array {
		// phpcs:disable WordPress.Arrays.MultipleStatementAlignment.DoubleArrowNotAligned
		return array(
			'with empty value' => array(
				array(),
				Config::DEFAULTS,
			),
			'with missing keys' => array(
				array(
					'outDir' => 'dist',
					'isBuild' => true,
					'entries' => array(),
				),
				Config::DEFAULTS,
			),
			'with bad keys' => array(
				array(
					'unknown' => true,
					'fail' => false,
					'errors' => array(),
				),
				Config::DEFAULTS,
			),
		);
		// phpcs:enable WordPress.Arrays.MultipleStatementAlignment.DoubleArrowNotAligned
	}

	/**
	 * @param array<mixed> $data
	 * @param array<mixed> $expected
	 */
	#[DataProvider( 'for_test_create' )]
	public function test_create( array $data, array $expected ): void {
		$data = ConfigData::create( $data );

		$this->assertEquals( $expected, (array) $data );
	}
}
