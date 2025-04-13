<?php

/**
 * @package ThemePlate
 * @since   0.1.0
 */

namespace ThemePlate\Vite;

/**
 * @phpstan-type Asset array{
 *     file: string,
 *     css: string[],
 * }
 *
 * @phpstan-type Assets array{}|array<string, Asset>
 */
readonly class Manifest {

	/** @use Parseable<Assets> */
	use Parseable;

	public Config $config;

	/** @var Assets */
	public array $assets;

	public const FILE = '.vite/manifest.json';

	public const DEFAULTS = array();


	public function __construct( Config $config ) {

		$this->config = $config;
		$this->assets = $this->parse( $this->config->root . $this->path( '' ) . static::FILE, static::DEFAULTS );

	}


	/** @return ?Asset */
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
