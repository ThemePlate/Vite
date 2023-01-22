<?php

/**
 * Straight-forward Vite integration for WordPress
 *
 * @package ThemePlate
 * @since   0.1.0
 */

namespace ThemePlate;

use ThemePlate\Enqueue\CustomData;

class Vite {

	protected string $basedir;
	protected string $baseurl;
	protected array $assets;
	protected CustomData $customdata;

	public const CLIENT = '@vite/client';


	public function __construct( string $manifest, string $baseurl ) {

		$this->basedir = basename( dirname( $manifest ) );
		$this->baseurl = $baseurl;
		$this->assets  = $this->parse( $manifest );

		$this->customdata = new CustomData();

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


	public function action(): void {

		if ( wp_script_is( self::CLIENT, 'registered' ) ) {
			wp_enqueue_script( self::CLIENT );
		}

		$this->customdata->action();

	}


	public function asset( string $name ): array {

		if ( ! isset( $this->assets[ $name ] ) ) {
			return array();
		}

		return $this->assets[ $name ];

	}


	public function path( string $name ): string {

		if ( ! wp_script_is( self::CLIENT, 'registered' ) ) {
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
		$this->customdata->add( 'script', self::CLIENT, array( 'type' => 'module' ) );

		$this->baseurl = $localurl;

	}


	public function style( string $handle, string $src, array $deps = array(), string $media = 'all' ): void {

		// phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion
		wp_enqueue_style( $handle, $this->path( $src ), $deps, null, $media );

	}


	public function script( string $handle, string $src, array $deps = array(), bool $in_footer = false ): void {

		if ( wp_script_is( self::CLIENT, 'registered' ) ) {
			$deps[] = self::CLIENT;
		}

		// phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion
		wp_enqueue_script( $handle, $this->path( $src ), $deps, null, $in_footer );
		$this->customdata->add( 'script', $handle, array( 'type' => 'module' ) );

	}

}

