<?php

/**
 * @package ThemePlate
 * @since   0.1.0
 */

namespace ThemePlate\Vite;

trait Parseable {

	protected function parse( string $root ): array {

		$file = $root . static::FILE;

		if ( ! file_exists( $file ) ) {
			return static::DEFAULTS;
		}

		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		$contents = file_get_contents( $file );

		if ( false === $contents ) {
			return static::DEFAULTS;
		}

		$decoded = json_decode( $contents, true );

		if ( null === $decoded ) {
			return static::DEFAULTS;
		}

		return $decoded;

	}

}
