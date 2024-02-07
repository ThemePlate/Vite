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
	protected string $project_root;
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
		'entries' => array(),

		'entryNames' => array(),
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

		$manifest = 'manifest.json';

		if ( file_exists( $project_root . $this->outpath( '.vite/' . $manifest ) ) ) {
			$manifest = '.vite/' . $manifest;
		}

		$this->assets = $this->parse( $project_root . $this->outpath( $manifest ) );

		if ( $this->development() ) {
			$this->public_base = trailingslashit( $this->config['urls']['local'][0] );
		}

		$this->project_root = $project_root;

	}


	public function action(): void {

		if ( $this->development() ) {
			// phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion
			wp_enqueue_script( self::CLIENT, $this->public_base . self::CLIENT, array(), null, false );
			$this->custom_data->script( self::CLIENT, array( 'type' => 'module' ) );
			$this->res_handler->script( self::CLIENT, 'modulepreload' );
		}

		$this->custom_data->action();
		add_action( 'wp_head', array( $this->res_handler, 'action' ), 2 );

	}


	public function entry( string $name ): string {

		if ( empty( $this->config['entryNames'] ) || ! isset( $this->config['entryNames'][ $name ] ) ) {
			return '';
		}

		return $this->config['entryNames'][ $name ];

	}


	public function name( string $entry ): string {

		if ( empty( $this->config['entryNames'] ) ) {
			return '';
		}

		return array_search( $entry, $this->config['entryNames'], true ) ?: '';

	}


	public function asset( string $name ): array {

		if ( $this->development() || ! isset( $this->assets[ $name ] ) ) {
			return array();
		}

		return $this->assets[ $name ];

	}


	public function path( string $name, bool $uri = true ): string {

		if ( ! $this->development() ) {
			$asset = $this->asset( $name );

			if ( ! empty( $asset ) ) {
				$name = $this->outpath( $asset['file'] );
			}
		}

		return ( $uri ? $this->public_base : $this->project_root ) . $name;

	}

	protected function handle_path_entry( string $src ): array {

		$entry = $this->entry( $src );

		// Provided $src is an entry name
		if ( '' !== $entry ) {
			$handle = $src;
			$src    = $entry;
		}

		$path = $this->path( $src );

		// Provided $src is an entry point
		if ( '' === $entry ) {
			$entry  = $src;
			$handle = $this->name( $src );

			// No entry name found
			if ( '' === $handle ) {
				$handle = md5( $src );
			}
		}

		return array( $handle, $path, $entry );

	}


	public function style( string $src, array $deps = array(), $args = array() ): string {

		[ $handle, $path, $entry ] = $this->handle_path_entry( $src );

		if ( ! is_array( $args ) ) {
			_deprecated_argument( __METHOD__, '1.4.0', 'Pass the `$media` inside an array.' );

			$args = array(
				'media' => $args,
			);
		}

		$args = array_merge(
			array(
				'media'    => 'all',
				'loader'   => array(),
				'resource' => array(),
			),
			$args
		);

		// phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion
		wp_register_style( $handle, $path, $deps, null, $args['media'] );
		$this->custom_data->style( $handle, $args['loader'] );
		$this->res_handler->style( $handle, 'preload', $args['resource'] );

		if ( in_array( $entry, $this->config['entries'], true ) ) {
			wp_enqueue_style( $handle );
		}

		return $handle;

	}


	public function script( string $src, array $deps = array(), $args = array() ): string {

		if ( $this->development() ) {
			$deps[] = self::CLIENT;
		}

		if ( ! is_array( $args ) ) {
			_deprecated_argument( __METHOD__, '1.4.0', 'Pass the `$in_footer` inside an array.' );

			$args = array(
				'in_footer' => (bool) $args,
			);
		}

		$args = array_merge(
			array(
				'in_footer' => false,
				'loader'    => array(),
				'resource'  => array(),
			),
			$args
		);

		[ $handle, $path, $entry ] = $this->handle_path_entry( $src );

		// phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion
		wp_register_script( $handle, $path, $deps, null, $args );
		$this->custom_data->script( $handle, array_merge( array( 'type' => 'module' ), $args['loader'] ) );
		$this->res_handler->script( $handle, 'modulepreload', $args['resource'] );
		$this->chunk( $entry, $handle );

		if ( in_array( $entry, $this->config['entries'], true ) ) {
			wp_enqueue_script( $handle );
		}

		return $handle;

	}


	protected function chunk( string $src, string $entry ): void {

		foreach ( $this->asset( $src )['imports'] ?? array() as $import ) {
			$this->script( $import, array( $entry ) );
		}

		foreach ( $this->asset( $src )['css'] ?? array() as $import ) {
			$this->style( $this->outpath( $import ) );
		}

	}

}
