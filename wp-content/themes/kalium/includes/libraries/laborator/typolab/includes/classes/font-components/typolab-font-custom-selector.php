<?php
/**
 * TypoLab - ultimate font management library.
 *
 * Custom selector entry.
 *
 * @author Laborator
 * @link   https://laborator.co
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class TypoLab_Font_Custom_Selector extends TypoLab_Font_Selector {

	/**
	 * Selector type.
	 *
	 * @var string
	 */
	public $type = 'custom-selector';

	/**
	 * Selectors.
	 *
	 * @var array
	 */
	public $selectors = [];

	/**
	 * Text transform.
	 *
	 * @var TypoLab_Responsive_Value
	 */
	public $text_transform;

	/**
	 * Font size.
	 *
	 * @var TypoLab_Responsive_Value
	 */
	public $font_size;

	/**
	 * Line height.
	 *
	 * @var TypoLab_Responsive_Value
	 */
	public $line_height;

	/**
	 * Letter spacing.
	 *
	 * @var TypoLab_Responsive_Value
	 */
	public $letter_spacing;

	/**
	 * Constructor.
	 *
	 * @param array $args
	 */
	public function __construct( $args = [] ) {
		parent::__construct( $args );

		// Selectors
		$this->selectors = kalium_get_array_key( $args, 'selectors', [] );

		// Remove backslashes from selectors
		if ( is_array( $this->selectors ) ) {
			$this->selectors = array_map( 'stripslashes', $this->selectors );
		}

		// Text transform
		$this->text_transform = new TypoLab_Responsive_Value( kalium_get_array_key( $args, 'text_transform' ) );

		// Font size
		$this->font_size = new TypoLab_Responsive_Value( kalium_get_array_key( $args, 'font_size' ) );

		// Line height
		$this->line_height = new TypoLab_Responsive_Value( kalium_get_array_key( $args, 'line_height' ) );

		// Letter spacing
		$this->letter_spacing = new TypoLab_Responsive_Value( kalium_get_array_key( $args, 'letter_spacing' ) );
	}

	/**
	 * Get selectors.
	 *
	 * @return array
	 */
	public function get_selectors() {
		return array_map( [ $this, 'map_predefined_selectors' ], $this->selectors );
	}

	/**
	 * Get props values.
	 *
	 * @param TypoLab_Font $font
	 *
	 * @return TypoLab_Responsive_Value[]
	 */
	public function get_css_props( $font ) {
		$variant   = $font->get_variant( $this->get_variant() );
		$css_props = $variant ? $variant->get_css_props() : [];

		return array_merge( $css_props, [
			'text-transform' => $this->text_transform,
			'font-size'      => $this->font_size,
			'line-height'    => $this->line_height,
			'letter-spacing' => $this->letter_spacing,
		] );
	}

	/**
	 * Map predefined selectors.
	 *
	 * @param string $selector
	 *
	 * @return string
	 */
	public function map_predefined_selectors( $selector ) {
		if ( preg_match( '/\:(?<id>.*?):/', $selector, $matches ) ) {
			$predefined_selector = TypoLab_Data::get_predefined_selector( $matches['id'] );

			return implode( ', ', $predefined_selector['selectors'] );
		}

		return $selector;
	}
}
