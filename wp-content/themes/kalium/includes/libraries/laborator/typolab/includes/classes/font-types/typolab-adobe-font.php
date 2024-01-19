<?php
/**
 * Kalium WordPress Theme
 *
 * Custom Font object.
 *
 * @author Laborator
 * @link   https://kaliumtheme.com
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

class TypoLab_Adobe_Font extends TypoLab_Font {

	/**
	 * Kit ID.
	 *
	 * @var string
	 */
	public $kit_id;

	/**
	 * Font name.
	 *
	 * @var string
	 */
	public $name;

	/**
	 * Parse variant.
	 *
	 * @param string       $variant
	 * @param TypoLab_Font $font
	 *
	 * @return TypoLab_Font_Variant
	 */
	public static function parse_variant( $variant, $font = null ) {
		$variant_arr = str_split( $variant );
		$style       = kalium_get_array_key( $variant_arr, 0, 'n' );
		$weight      = (int) kalium_get_array_key( $variant_arr, 1, 4 );

		return new TypoLab_Font_Variant( [
			'style'  => 'i' === $style ? 'italic' : 'normal',
			'weight' => 4 === $weight ? 'normal' : 100 * $weight,
		], $font );
	}

	/**
	 * Constructor.
	 *
	 * @param array $args
	 */
	public function __construct( $args = [] ) {
		parent::__construct( $args );

		// Set Kit ID
		$this->kit_id = kalium_get_array_key( $args, 'kit_id' );

		// Font name
		$this->name = kalium_get_array_key( $args, 'name' );
	}

	/**
	 * Get font title.
	 *
	 * @return string
	 */
	public function get_title() {
		if ( $this->name ) {
			return $this->name;
		}

		return parent::get_title();
	}

	/**
	 * Get variant as string representation.
	 *
	 * @param TypoLab_Font_Variant $variant
	 */
	public function get_variant_value( $variant ) {
		$variant_name = $variant->is_italic() ? 'i' : 'n'; // Style
		$variant_name .= is_numeric( $variant->weight ) ? intval( $variant->weight / 100 ) : 4; // Weight

		return $variant_name;
	}

	/**
	 * Get Kit ID.
	 *
	 * @return string
	 */
	public function get_kit_id() {
		return $this->kit_id;
	}

	/**
	 * Get stylesheet URL.
	 *
	 * @return string
	 */
	public function get_stylesheet_url() {
		$kit_id = $this->get_kit_id();

		if ( ! $kit_id ) {
			return null;
		}

		return sprintf( 'https://use.typekit.net/%s.css', $kit_id );
	}

	/**
	 * Add JSON data for Adobe font.
	 */
	protected function to_json() {
		parent::to_json();

		// Add font name from Adobe (if not empty)
		if ( $adobe_font = TypoLab_Adobe_Fonts_Provider::get_font( $this->get_family_name() ) ) {
			$this->name = $adobe_font['name'];
		}

		// Kit ID
		$this->json['kit_id'] = $this->get_kit_id();

		// Font name
		$this->json['name'] = $this->name;
	}
}
