<?php
/**
 * TypoLab - ultimate font management library.
 *
 * Exportable class var trait.
 *
 * @author Laborator
 * @link   https://laborator.co
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

trait TypoLab_Exportable {

	/**
	 * Export.
	 *
	 * @return array
	 */
	public function to_array() {
		return $this->to_array_recursively( get_object_vars( $this ) );
	}

	/**
	 * To array recursively.
	 *
	 * @return array
	 */
	private function to_array_recursively( $props ) {
		$export_arr = [];
		foreach ( $props as $prop_name => $prop_value ) {
			if ( $prop_value instanceof TypoLab_Font ) {
				continue; // Props with font instance are not supported
			}

			if ( is_object( $prop_value ) && method_exists( $prop_value, 'to_array' ) ) {
				$export_arr[ $prop_name ] = $this->to_array_recursively( $prop_value->to_array() );
			} else if ( is_array( $prop_value ) ) {
				$export_arr[ $prop_name ] = $this->to_array_recursively( $prop_value );
			} else {
				$export_arr[ $prop_name ] = $prop_value;
			}
		}

		return $export_arr;
	}
}
