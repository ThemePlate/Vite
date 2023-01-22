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
	protected array $config;
	protected CustomData $customdata;

	public const CLIENT = '@vite/client';
	public const CONFIG = 'vite.themeplate.json';


	public function __construct( string $basedir, string $baseurl ) {

		$this->basedir = $basedir;
		$this->baseurl = $baseurl;

		$this->customdata = new CustomData();

		$this->init();

	}


	protected function parse( string $file, array $default ): array {

		$file = trailingslashit( $this->basedir ) . $file;

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


	protected function init(): void {

		$default = array(
			'outDir'  => 'dist',
			'isBuild' => true,
			'urls'    => array(
				'local'   => array(),
				'network' => array(),
			),
		);

		$this->config = $this->parse( self::CONFIG, $default );
		$this->assets = $this->parse( trailingslashit( $this->config['outDir'] ) . 'manifest.json', array() );

		if ( ! $this->config['isBuild'] ) {
			$this->baseurl = $this->config['urls']['local'][0];
		}

	}


	public function action(): void {

		if ( ! $this->config['isBuild'] ) {
			// phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion
			wp_enqueue_script( self::CLIENT, trailingslashit( $this->baseurl ) . self::CLIENT, array(), null, false );
			$this->customdata->add( 'script', self::CLIENT, array( 'type' => 'module' ) );
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

		if ( $this->config['isBuild'] ) {
			$asset = $this->asset( $name );

			if ( ! empty( $asset ) ) {
				$name = trailingslashit( $this->config['outDir'] ) . $asset['file'];
			}
		}

		return trailingslashit( $this->baseurl ) . $name;

	}


	public function style( string $handle, string $src, array $deps = array(), string $media = 'all' ): void {

		// phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion
		wp_enqueue_style( $handle, $this->path( $src ), $deps, null, $media );

	}


	public function script( string $handle, string $src, array $deps = array(), bool $in_footer = false ): void {

		if ( ! $this->config['isBuild'] ) {
			$deps[] = self::CLIENT;
		}

		// phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion
		wp_enqueue_script( $handle, $this->path( $src ), $deps, null, $in_footer );
		$this->customdata->add( 'script', $handle, array( 'type' => 'module' ) );

	}

}

