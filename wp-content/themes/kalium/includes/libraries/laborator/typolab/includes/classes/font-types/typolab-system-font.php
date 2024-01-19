<?php
/**
 * TypoLab - ultimate font management library.
 *
 * System Font object.
 *
 * @author Laborator
 * @link   https://laborator.co
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class TypoLab_System_Font extends TypoLab_Font {

	/**
	 * Get font family stack.
	 *
	 * @return string[]
	 */
	public function get_font_family_stack() {
		$font = TypoLab_System_Fonts_Provider::get_font( $this->get_family_name() );

		// Existing font stack
		if ( ! empty( $font['font_stack'] ) ) {
			return $font['font_stack'];
		}

		return [
			$this->quote( $this->get_family_name() ),
		];
	}
}
