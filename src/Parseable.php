<?php

/**
 * @package ThemePlate
 * @since   0.1.0
 */

namespace ThemePlate\Vite;

trait Parseable {

	protected function parse( string $file, array $defaults ): array {

		if ( ! file_exists( $file ) ) {
			return $defaults;
		}

		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		$contents = file_get_contents( $file );

		if ( false === $contents ) {
			return $defaults;
		}

		$decoded = json_decode( $contents, true );

		if ( null === $decoded ) {
			return $defaults;
		}

		return $decoded;

	}

}
