<?php

/**
 * @package ThemePlate
 * @since   0.1.0
 */

namespace ThemePlate\Vite;

readonly class Config {

	use Parseable;

	public string $root;
	public array $data;

	public const FILE = 'vite.themeplate.json';

	// phpcs:disable WordPress.Arrays.MultipleStatementAlignment.DoubleArrowNotAligned
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
	// phpcs:enable WordPress.Arrays.MultipleStatementAlignment.DoubleArrowNotAligned


	public function __construct( string $root ) {

		$this->root = trailingslashit( $root );
		$this->data = $this->parse( $this->root );

	}


	public function entry( string $name ): string {

		if ( array() === $this->data['entryNames'] || ! isset( $this->data['entryNames'][ $name ] ) ) {
			return '';
		}

		return $this->data['entryNames'][ $name ];

	}


	public function name( string $entry ): string {

		if ( array() === $this->data['entryNames'] ) {
			return '';
		}

		// phpcs:ignore Universal.Operators.DisallowShortTernary
		return array_search( $entry, $this->data['entryNames'], true ) ?: '';

	}


	public function handle( string $value ): string {

		if ( '' !== $this->entry( $value ) ) {
			return $value;
		}

		$name = $this->name( $value );

		if ( '' !== $name ) {
			return $name;
		}

		return in_array( $value, $this->data['entries'], true ) ? md5( $value ) : '';

	}


	public function prefix( ?string $handle = null ): string {

		static $value = '';

		if ( null !== $handle ) {
			$value = $handle;
		}

		return $value;

	}

}
