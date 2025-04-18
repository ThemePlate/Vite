<?php

/**
 * @package ThemePlate
 * @since   0.1.0
 *
 * @phpcs:disable WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
 */

namespace ThemePlate\Vite;

readonly class ManifestAsset {

	public function __construct(
		public readonly string $file,
		public readonly string $name,
		public readonly bool $isEntry,
		public readonly bool $isDynamicEntry,
		public readonly ?string $src,
		/** @var ?string[] */
		public readonly ?array $imports,
		/** @var ?string[] */
		public readonly ?array $dynamicImports,
		/** @var ?string[] */
		public readonly ?array $css,
	) {}


	/** @param array<mixed> $data */
	public static function create( array $data ): self {

		return new self(
			$data['file'] ?? '',
			$data['name'] ?? '',
			$data['isEntry'] ?? false,
			$data['isDynamicEntry'] ?? false,
			$data['src'] ?? null,
			$data['imports'] ?? null,
			$data['dynamicImports'] ?? null,
			$data['css'] ?? null,
		);

	}
}
