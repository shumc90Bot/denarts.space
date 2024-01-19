<?php
/**
 * Kalium WordPress Theme
 *
 * External Font object.
 *
 * @author Laborator
 * @link   https://kaliumtheme.com
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

class TypoLab_External_Font extends TypoLab_Font {

	/**
	 * Stylesheet URL.
	 *
	 * @var string
	 */
	public $stylesheet_url;

	/**
	 * Constructor.
	 *
	 * @param array $args
	 */
	public function __construct( $args = [] ) {
		parent::__construct( $args );

		// Set Kit ID
		$this->stylesheet_url = kalium_get_array_key( $args, 'stylesheet_url' );
	}

	/**
	 * Get font family name.
	 *
	 * @return string
	 */
	public function get_family_name() {
		$family_name = $this->family_name;

		// Use default's variant name (if it has)
		if ( empty( $family_name ) ) {
			$variant = $this->get_default_variant();

			if ( $variant && ! empty( $variant->name ) ) {
				return $variant->name;
			}
		}

		return $family_name;
	}

	/**
	 * Get stylesheet URL.
	 *
	 * @return string
	 */
	public function get_stylesheet_url() {
		return $this->stylesheet_url;
	}

	/**
	 * Print font styles.
	 */
	public function print_styles() {
		$font_faces = [];

		// Add font faces
		foreach ( $this->get_variants() as $variant ) {
			$font_faces[] = $variant->generate_font_face();
		}

		// Print styles
		if ( count( $font_faces ) ) {
			echo sprintf( '<style data-external-font>%s</style>', implode( PHP_EOL, $font_faces ) );
		}
	}

	/**
	 * Add JSON data for External font.
	 */
	protected function to_json() {
		parent::to_json();

		// Add font name from Adobe (if not empty)
		if ( $adobe_font = TypoLab_Adobe_Fonts_Provider::get_font( $this->get_family_name() ) ) {
			$this->name = $adobe_font['name'];
		}

		// Stylesheet URL
		$this->json['stylesheet_url'] = $this->get_stylesheet_url();
	}
}
