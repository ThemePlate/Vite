<?php

/**
 * @package ThemePlate
 * @since   0.1.0
 */

namespace ThemePlate\Vite;

readonly class Manifest {

	use Parseable;

	public Config $config;
	public array $assets;

	public const FILE = '.vite/manifest.json';

	public const DEFAULTS = array();


	public function __construct( Config $config ) {

		$this->config = $config;
		$this->assets = $this->parse( $this->config->root . $this->path( '' ) );

	}


	public function asset( string $name ): ?array {

		if ( ! isset( $this->assets[ $name ] ) ) {
			return null;
		}

		return $this->assets[ $name ];

	}


	public function path( string $file ): string {

		if ( '' === $this->config->data['outDir'] ) {
			return $file;
		}

		return trailingslashit( $this->config->data['outDir'] ) . $file;

	}

}
