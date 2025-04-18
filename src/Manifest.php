<?php

/**
 * @package ThemePlate
 * @since   0.1.0
 */

namespace ThemePlate\Vite;

/**
 * @phpstan-type AssetArray array{
 *     file: string,
 *     name: string,
 *     isEntry: bool,
 *     isDynamicEntry: bool,
 *     src: string|null,
 *     imports: string[]|null,
 *     dynamicImports: string[]|null,
 *     css: string[]|null,
 * }
 *
 * @phpstan-type Assets array{}|array<string, AssetArray>
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
		$this->assets = array_map(
			fn( array $asset ) => (array) Asset::create( $asset ),
			$this->parse( $this->config->root . $this->path( '' ) . static::FILE ) ?? static::DEFAULTS
		);

	}


	/** @return ?AssetArray */
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
