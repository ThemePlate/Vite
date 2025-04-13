<?php

/**
 * @package ThemePlate
 * @since   0.1.0
 */

namespace ThemePlate\Vite;

/** @template T of array */
trait Parseable {

	/** @return ?T */
	protected function parse( string $file ): ?array {

		if ( ! file_exists( $file ) ) {
			return null;
		}

		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		$contents = file_get_contents( $file );

		if ( false === $contents ) {
			return null;
		}

		$decoded = json_decode( $contents, true );

		if ( null === $decoded ) {
			return null;
		}

		return $decoded;

	}

}
