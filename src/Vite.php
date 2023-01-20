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
	protected string $baseurl;
	protected array $assets;

	public const CLIENT = '@vite/client';


	public function __construct( string $manifest, string $baseurl ) {

		$this->basedir = basename( dirname( $manifest ) );
		$this->baseurl = $baseurl;
		$this->assets  = $this->parse( $manifest );

		$this->init( $manifest );

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


	protected function init( string $manifest ): void {

		$urlfile = dirname( $manifest ) . '/themeplate';

		if ( ! file_exists( $urlfile ) ) {
			return;
		}

		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		$localurl = file_get_contents( $urlfile );

		if ( false === $localurl ) {
			return;
		}

		$this->development( $localurl );

	}


	public function asset( string $name ): array {

		if ( ! isset( $this->assets[ $name ] ) ) {
			return array();
		}

		return $this->assets[ $name ];

	}


	public function path( string $name ): string {

		if ( ! wp_script_is( self::CLIENT ) ) {
			$asset = $this->asset( $name );

			if ( ! empty( $asset ) ) {
				$name = trailingslashit( $this->basedir ) . $asset['file'];
			}
		}

		return trailingslashit( $this->baseurl ) . $name;

	}


	public function development( string $localurl ): void {

		// phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion
		wp_register_script( self::CLIENT, trailingslashit( $localurl ) . self::CLIENT, array(), null, false );

		$this->baseurl = $localurl;

	}

}

