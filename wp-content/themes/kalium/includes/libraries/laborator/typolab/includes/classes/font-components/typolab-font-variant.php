<?php
/**
 * TypoLab - ultimate font management library.
 *
 * Font variant.
 *
 * @author Laborator
 * @link   https://laborator.co
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class TypoLab_Font_Variant {

	/**
	 * Uses exportable.
	 */
	use TypoLab_Exportable;

	/**
	 * Font variant name.
	 *
	 * @var string
	 */
	public $name;

	/**
	 * Font variant style.
	 *
	 * @var string
	 */
	public $style;

	/**
	 * Font variant weight.
	 *
	 * @var int|string
	 */
	public $weight;

	/**
	 * Font display.
	 *
	 * @var string
	 */
	public $display;

	/**
	 * Font files.
	 *
	 * @var array
	 */
	public $src = [];

	/**
	 * Unicode range.
	 *
	 * @var string|array
	 */
	public $unicode_range;

	/**
	 * Variant data object.
	 *
	 * @var array
	 */
	public $data = [];

	/**
	 * Font instance.
	 *
	 * @var TypoLab_Font
	 */
	public $font;

	/**
	 * Create font variant instance.
	 *
	 * @param array        $args
	 * @param TypoLab_Font $font
	 *
	 * @return TypoLab_Font_Variant
	 */
	public static function create_instance( $args = [], $font = null ) {
		return new self( $args );
	}

	/**
	 * Constructor.
	 *
	 * @param array        $args
	 * @param TypoLab_Font $font
	 */
	public function __construct( $args = [], $font = null ) {

		// Filter font files source
		if ( isset( $args['src'] ) && is_array( $args['src'] ) ) {
			$args['src'] = array_filter( $args['src'] );
		}

		// Set props
		foreach ( get_object_vars( $this ) as $prop_name => $prop_value ) {
			$this->{$prop_name} = isset( $args[ $prop_name ] ) ? $args[ $prop_name ] : $prop_value;
		}

		// Weight validate (use "normal" instead of 400)
		if ( is_numeric( $this->weight ) ) {
			$this->weight = 400 === intval( $this->weight ) ? 'normal' : $this->weight;
		}

		// Empty font style
		if ( empty( $this->style ) ) {
			$this->style = 'normal';
		}

		// Empty font weight
		if ( empty( $this->weight ) ) {
			$this->weight = 'normal';
		}

		// Font reference
		if ( $font instanceof TypoLab_Font ) {
			$this->font = &$font;
		}
	}

	/**
	 * Get font this variant is assigned to.
	 *
	 * @return TypoLab_Font
	 */
	public function get_font() {
		return $this->font;
	}

	/**
	 * Check if is regular.
	 *
	 * @return bool
	 */
	public function is_regular() {
		return 'normal' === $this->weight;
	}

	/**
	 * Check if is italic.
	 *
	 * @return bool
	 */
	public function is_italic() {
		return 'italic' === $this->style;
	}

	/**
	 * Generate font face style.
	 *
	 * @return string
	 */
	public function generate_font_face() {
		$font_face    = [];
		$family_name  = $this->name ?: ( $this->font ? $this->font->get_family_name() : null );
		$font_display = 'inherit' === $this->display ? TypoLab::$font_display : $this->display;

		// When font family name does not exists
		if ( ! $family_name ) {
			return null;
		}

		$font_face[] = '@font-face {';

		// Font details
		$font_face[] = sprintf( "\tfont-family: %s;", TypoLab_Helper::quote( esc_attr( $family_name ) ) );

		if ( $this->style ) {
			$font_face[] = sprintf( "\tfont-style: %s;", $this->style );
		}

		if ( $this->weight ) {
			$font_face[] = sprintf( "\tfont-weight: %s;", $this->weight );
		}

		if ( $font_display ) {
			$font_face[] = sprintf( "\tfont-display: %s;", $font_display );
		}

		// Font source
		$font_files = $this->src;
		$sources    = [];
		$formats    = [
			'eot' => 'embedded-opentype',
			'ttf' => 'truetype',
		];

		foreach ( $font_files as $format => $font_file ) {
			$sources[] = sprintf( "url('%s') format('%s')", $font_file, kalium_get_array_key( $formats, $format, $format ) );
		}

		// Add sources
		if ( count( $sources ) ) {
			$font_face[] = sprintf( "\tsrc: %s;", implode( ', ', $sources ) );
		}

		// Unicode range
		if ( $this->unicode_range ) {
			$font_face[] = sprintf( "\tunicode-range: %s;", $this->unicode_range );
		}

		$font_face[] = '}';

		return implode( PHP_EOL, $font_face );
	}

	/**
	 * Get CSS props.
	 *
	 * @return array
	 */
	public function get_css_props() {
		$props             = [];
		$font_family_stack = $this->font ? $this->font->get_font_family_stack() : null;

		// Font family
		if ( $font_family_stack ) {
			$props['font-family'] = implode( ', ', $font_family_stack );
		}

		// Font family from variant
		if ( $this->name ) {
			$props['font-family'] = TypoLab_Helper::quote( $this->name );
		}

		// Font style
		if ( $this->style ) {
			$props['font-style'] = $this->style;
		}

		// Font weight
		if ( $this->weight ) {
			$props['font-weight'] = $this->weight;
		}

		return $props;
	}

	/**
	 * Get variant value as string.
	 *
	 * @return string
	 */
	public function to_string() {

		// Get variant value from assigned font reference
		if ( $this->font ) {
			return $this->font->get_variant_value( $this );
		}

		return implode( '-', [ $this->style, $this->weight ] );
	}
}
