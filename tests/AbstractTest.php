<?php

/**
 * @package ThemePlate
 */

namespace Tests;

use Brain\Monkey;
use PHPUnit\Framework\TestCase;

abstract class AbstractTest extends TestCase {
	public const BASE_URL = 'http://themeplate.local';

	protected function rootDir(): string {
		$name = explode( '_', $this->getName( false ), 3 );
		// $name[1] is going to be 'dev' or 'build'
		$root = __DIR__ . DIRECTORY_SEPARATOR . $name[1];

		if ( 'maybe_banner' === $name[2] ) {
			$root .= '-banner';
		}

		return $root;
	}

	protected function setUp(): void {
		parent::setUp();
		Monkey\setUp();
	}

	protected function tearDown(): void {
		Monkey\tearDown();
		parent::tearDown();
	}
}
