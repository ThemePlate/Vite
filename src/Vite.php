<?php

/**
 * Straight-forward Vite integration for WordPress
 *
 * @package ThemePlate
 * @since   0.1.0
 */

namespace ThemePlate;

class Vite {

	protected string $basedir;
	protected array $assets;


	public function __construct( string $manifest ) {

		$this->basedir = basename( dirname( $manifest ) );
		$this->assets  = $this->parse( $manifest );

	}


	protected function parse( string $file ): array {

		$default = array();

		if ( ! file_exists( $file ) ) {
			return $default;
		}

		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		$contents = file_get_contents( $file );

		if ( false === $contents ) {
			return $default;
		}

		$decoded = json_decode( $contents, true );

		if ( null === $decoded ) {
			return $default;
		}

		return $decoded;

	}


	public function asset( string $name ): array {

		if ( ! isset( $this->assets[ $name ] ) ) {
			return array();
		}

		return $this->assets[ $name ];

	}


	public function path( string $name ): string {

		$asset = $this->asset( $name );

		if ( ! empty( $asset ) ) {
			$name = trailingslashit( $this->basedir ) . $asset['file'];
		}

		return $name;

	}

}

