<?php
/**
 * TypoLab - ultimate font management library.
 *
 * Font appearance element.
 *
 * @author Laborator
 * @link   https://laborator.co
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class TypoLab_Font_Appearance_Element {

	/**
	 * Uses exportable.
	 */
	use TypoLab_Exportable;

	/**
	 * Element Id.
	 *
	 * @var string
	 */
	public $id;

	/**
	 * Group Id.
	 *
	 * @var string
	 */
	public $group_id;

	/**
	 * Font size.
	 *
	 * @var TypoLab_Responsive_Value $font_size
	 */
	public $font_size;

	/**
	 * Line height.
	 *
	 * @var TypoLab_Responsive_Value $line_height
	 */
	public $line_height;

	/**
	 * Letter spacing.
	 *
	 * @var TypoLab_Responsive_Value $letter_spacing
	 */
	public $letter_spacing;

	/**
	 * Text transform.
	 *
	 * @var TypoLab_Responsive_Value $text_transform
	 */
	public $text_transform;

	/**
	 * Constructor.
	 *
	 * @param array $args
	 */
	public function __construct( $args = [] ) {

		// Identifier
		$this->id = kalium_get_array_key( $args, 'id' );

		// Belonging group
		$this->group_id = kalium_get_array_key( $args, 'group_id' );

		// Values
		$this->font_size      = new TypoLab_Responsive_Value( kalium_get_array_key( $args, 'font_size' ) );
		$this->line_height    = new TypoLab_Responsive_Value( kalium_get_array_key( $args, 'line_height' ) );
		$this->letter_spacing = new TypoLab_Responsive_Value( kalium_get_array_key( $args, 'letter_spacing' ) );
		$this->text_transform = new TypoLab_Responsive_Value( kalium_get_array_key( $args, 'text_transform' ) );
	}

	/**
	 * Get CSS selectors list.
	 *
	 * @return array
	 */
	public function get_selectors() {
		$element = TypoLab_Data::get_font_appearance_element( $this->group_id, $this->id );

		return kalium_get_array_key( $element, 'selectors', [] );
	}

	/**
	 * Get props values.
	 *
	 * @return TypoLab_Responsive_Value[]
	 */
	public function get_css_props() {
		return [
			'font-size'      => $this->font_size,
			'line-height'    => $this->line_height,
			'letter-spacing' => $this->letter_spacing,
			'text-transform' => $this->text_transform,
		];
	}

	/**
	 * Export appearance element.
	 *
	 * @return array
	 */
	public function export() {
		return $this->to_array();
	}
}
