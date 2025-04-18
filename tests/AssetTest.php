<?php

/**
 * @package ThemePlate
 */

namespace Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ThemePlate\Vite\Asset;

class AssetTest extends TestCase {
	public const DEFAULTS = array(
		'file'           => '',
		'name'           => '',
		'isEntry'        => false,
		'isDynamicEntry' => false,
		'src'            => null,
		'imports'        => null,
		'dynamicImports' => null,
		'css'            => null,
	);

	/** @return array<string, array{0: array<mixed>, 1: array<mixed>}> */
	public static function for_test_create(): array {
		// phpcs:disable WordPress.Arrays.MultipleStatementAlignment.DoubleArrowNotAligned
		return array(
			'with empty value' => array(
				array(),
				self::DEFAULTS,
			),
			'with missing keys' => array(
				array(
					'isEntry' => false,
					'isDynamicEntry' => false,
				),
				self::DEFAULTS,
			),
			'with bad keys' => array(
				array(
					'unknown' => true,
					'fail' => false,
				),
				self::DEFAULTS,
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
		$data = Asset::create( $data );

		$this->assertEquals( $expected, (array) $data );
	}
}
