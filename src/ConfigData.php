<?php

/**
 * @package ThemePlate
 * @since   0.1.0
 *
 * @phpcs:disable WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
 */

namespace ThemePlate\Vite;

readonly class ConfigData {

	public function __construct(
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
			$data['outDir'],
			$data['isBuild'],
			$data['urls'],
			$data['entries'],
			$data['entryNames'],
		);

	}

}
