<?php

/**
 * Straight-forward Vite integration for WordPress
 *
 * @package ThemePlate
 * @since   0.1.0
 */

namespace ThemePlate;

use ThemePlate\Enqueue\CustomData;
use ThemePlate\Resource\Handler;

class Vite {

	protected string $public_base;
	protected array $assets;
	protected array $config;
	protected CustomData $custom_data;
	protected Handler $res_handler;

	public const CLIENT = '@vite/client';
	public const CONFIG = 'vite.themeplate.json';

	public const DEFAULTS = array(
		'outDir'  => 'dist',
		'isBuild' => true,
		'urls'    => array(
			'local'   => array(),
			'network' => array(),
		),
	);


	public function __construct( string $project_root, string $public_base ) {

		$this->public_base = trailingslashit( $public_base );
		$this->custom_data = new CustomData();
		$this->res_handler = new Handler();

		$this->init( trailingslashit( $project_root ) );

	}


	protected function parse( string $file, array $default = array() ): array {

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


	protected function outpath( string $file ): string {

		if ( '' === $this->config['outDir'] ) {
			return $file;
		}

		return trailingslashit( $this->config['outDir'] ) . $file;

	}


	public function development(): bool {

		return ! $this->config['isBuild'];

	}


	protected function init( string $project_root ): void {

		$this->config = $this->parse( $project_root . self::CONFIG, self::DEFAULTS );
		$this->assets = $this->parse( $project_root . $this->outpath( 'manifest.json' ) );

		if ( $this->development() ) {
			$this->public_base = trailingslashit( $this->config['urls']['local'][0] );
		}

	}


	public function action(): void {

		if ( $this->development() ) {
			// phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion
			wp_enqueue_script( self::CLIENT, $this->public_base . self::CLIENT, array(), null, false );
			$this->custom_data->add( 'script', self::CLIENT, array( 'type' => 'module' ) );
		}

		$this->custom_data->action();
		$this->res_handler->init();
		add_action( 'wp_head', array( $this->res_handler, 'action' ), 2 );

	}


	public function asset( string $name ): array {

		if ( $this->development() || ! isset( $this->assets[ $name ] ) ) {
			return array();
		}

		return $this->assets[ $name ];

	}


	public function path( string $name ): string {

		if ( ! $this->development() ) {
			$asset = $this->asset( $name );

			if ( ! empty( $asset ) ) {
				$name = $this->outpath( $asset['file'] );
			}
		}

		return $this->public_base . $name;

	}


	public function style( string $src, array $deps = array(), string $media = 'all' ): string {

		$srcpath = $this->path( $src );
		$handle  = md5( $srcpath );

		// phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion
		wp_enqueue_style( $handle, $srcpath, $deps, null, $media );
		$this->res_handler->style( $handle, 'preload' );

		return $handle;

	}


	public function script( string $src, array $deps = array(), bool $in_footer = false ): string {

		if ( $this->development() ) {
			$deps[] = self::CLIENT;
		}

		$srcpath = $this->path( $src );
		$handle  = md5( $srcpath );

		// phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion
		wp_enqueue_script( $handle, $srcpath, $deps, null, $in_footer );
		$this->custom_data->add( 'script', $handle, array( 'type' => 'module' ) );
		$this->res_handler->script( $handle, 'modulepreload' );

		return $handle;

	}

}
