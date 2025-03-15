<?php

/**
 * Straight-forward Vite integration for WordPress
 *
 * @package ThemePlate
 * @since   0.1.0
 */

namespace ThemePlate\Vite;

use ThemePlate\Enqueue\CustomData;
use ThemePlate\Resource\Handler;

readonly class Project {

	public Config $config;
	public Manifest $manifest;
	public CustomData $custom_data;
	public Handler $res_handler;
	public string $public_base;

	public const CLIENT_HANDLE = '@vite/client';


	public function __construct( string $root_directory, string $public_base ) {

		$this->config   = new Config( $root_directory );
		$this->manifest = new Manifest( $this->config );

		$this->custom_data = new CustomData();
		$this->res_handler = new Handler();
		$this->public_base = trailingslashit( $public_base );

	}


	protected function public_path( string $file ): string {

		if ( ! $this->development() ) {
			return $this->public_base . $file;
		}

		return trailingslashit( $this->config->data['urls']['local'][0] ) . $file;

	}


	public function development(): bool {

		return ! $this->config->data['isBuild'];

	}


	public function action(): void {

		if ( $this->development() ) {
			// phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion
			wp_enqueue_script( self::CLIENT_HANDLE, $this->public_path( self::CLIENT_HANDLE ), array(), null, false );
			$this->custom_data->script( self::CLIENT_HANDLE, array( 'type' => 'module' ) );
			$this->res_handler->script( self::CLIENT_HANDLE, 'modulepreload' );
		}

		$this->custom_data->action();
		add_action( 'wp_head', array( $this->res_handler, 'action' ), 2 );

	}


	public function path( string $name, bool $uri = true ): string {

		if ( ! $this->development() ) {
			$asset = $this->manifest->asset( $name );

			if ( ! empty( $asset ) ) {
				$name = $this->manifest->path( $asset['file'] );
			}
		}

		return $uri ? $this->public_path( $name ) : ( $this->config->root . $name );

	}


	protected function handle_path_entry( string $src ): array {

		$handle = $this->config->handle( $src );
		$entry  = $this->config->entry( $src );

		if ( $handle !== $src ) {
			$entry = $src;
		}

		$path = $this->path( $entry );

		return array( $this->config->prefix() . $handle, $path, $entry );

	}


	public function style( string $src, array $deps = array(), array $args = array() ): string {

		[ $handle, $path, $entry ] = $this->handle_path_entry( $src );

		$media    = $args['media'] ?? 'all';
		$loader   = $args['loader'] ?? array();
		$resource = $args['resource'] ?? array();

		// phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion
		wp_register_style( $handle, $path, $deps, null, $media );
		$this->custom_data->style( $handle, $loader );
		$this->res_handler->style( $handle, 'preload', $resource );

		if ( in_array( $entry, $this->config->data['entries'], true ) ) {
			wp_enqueue_style( $handle );
		}

		return $handle;

	}


	public function script( string $src, array $deps = array(), array $args = array() ): string {

		if ( $this->development() ) {
			$deps[] = self::CLIENT_HANDLE;
		}

		if ( empty( $args ) ) {
			$args = array(
				'in_footer' => true,
			);
		}

		$loader   = array( 'type' => 'module' );
		$resource = array();

		if ( isset( $args['loader'] ) ) {
			$loader = array_merge( $args['loader'], $loader );

			unset( $args['loader'] );
		}

		if ( isset( $args['resource'] ) ) {
			$resource = $args['resource'];

			unset( $args['resource'] );
		}

		[ $handle, $path, $entry ] = $this->handle_path_entry( $src );

		// phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion
		wp_register_script( $handle, $path, $deps, null, $args );
		$this->custom_data->script( $handle, $loader );
		$this->res_handler->script( $handle, 'modulepreload', $resource );
		$this->chunk( $entry );

		if ( in_array( $entry, $this->config->data['entries'], true ) ) {
			wp_enqueue_script( $handle );
		}

		return $handle;

	}


	protected function chunk( string $src ): void {

		if ( $this->development() ) {
			return;
		}

		foreach ( $this->manifest->asset( $src )['css'] ?? array() as $import ) {
			$this->style( $this->manifest->path( $import ) );
		}

	}

}
