<?php
/**
 * TypoLab - ultimate font management library.
 *
 * Selectors.
 *
 * @author Laborator
 * @link   https://laborator.co
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class TypoLab_Font_Selector {

	/**
	 * Uses exportable.
	 */
	use TypoLab_Exportable;

	/**
	 * Selector type.
	 *
	 * @var string
	 */
	public $type = 'base-selector';

	/**
	 * Selector ID.
	 *
	 * @var string
	 */
	public $id;

	/**
	 * Variant.
	 *
	 * @var string
	 */
	public $variant;

	/**
	 * Include selector or not.
	 *
	 * @var bool
	 */
	public $include = true;

	/**
	 * Constructor.
	 *
	 * @param array $args
	 */
	public function __construct( $args = [] ) {

		// Set props
		foreach ( get_object_vars( $this ) as $prop_name => $prop_value ) {
			$this->{$prop_name} = isset( $args[ $prop_name ] ) ? $args[ $prop_name ] : $prop_value;
		}
	}

	/**
	 * Get selector ID.
	 *
	 * @return string
	 */
	public function get_id() {
		return $this->id;
	}

	/**
	 * Get assigned variant.
	 *
	 * @return string
	 */
	public function get_variant() {
		return $this->variant;
	}

	/**
	 * Check if selector is base selector.
	 *
	 * @return bool
	 */
	public function is_base_selector() {
		return 'base-selector' === $this->type;
	}

	/**
	 * Should selector be included or not.
	 *
	 * @return bool
	 */
	public function do_include() {
		return $this->include;
	}

	/**
	 * Get selectors.
	 *
	 * @return array
	 */
	public function get_selectors() {
		$base_selector = TypoLab_Data::get_base_selector( $this->get_id() );

		return kalium_get_array_key( $base_selector, 'selectors' );
	}
}
