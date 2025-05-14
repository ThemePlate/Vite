<?php

/**
 * @package ThemePlate
 * @since   0.1.0
 *
 * @phpcs:disable WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
 */

namespace ThemePlate\Vite;

readonly class Data {

	public function __construct(
		public readonly string $baseUrl,
		public readonly string $outDir,
		public readonly bool $isBuild,
		/** @var array{local: string[], network: string[]} */
		public readonly array $urls,
		/** @var string[] */
		public readonly array $entries,
		/** @var array<string, string> */
		public readonly array $entryNames,
	) {}


	/** @param array<mixed> $data */
	public static function create( array $data ): self {

		return new self(
			$data['baseUrl'] ?? Config::DEFAULTS['baseUrl'],
			$data['outDir'] ?? Config::DEFAULTS['outDir'],
			$data['isBuild'] ?? Config::DEFAULTS['isBuild'],
			$data['urls'] ?? Config::DEFAULTS['urls'],
			$data['entries'] ?? Config::DEFAULTS['entries'],
			$data['entryNames'] ?? Config::DEFAULTS['entryNames'],
		);

	}

}
