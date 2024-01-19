<?php
/**
 * TypoLab - ultimate font management library.
 *
 * Google Font object.
 *
 * @author Laborator
 * @link   https://laborator.co
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class TypoLab_Google_Font extends TypoLab_Font {

	/**
	 * Font preload support.
	 */
	use TypoLab_Font_Preload;

	/**
	 * Get stylesheet URL.
	 *
	 * @return string
	 */
	public function get_stylesheet_url() {
		return TypoLab_Google_Fonts_Provider::build_stylesheet_url( [
			'family_name' => $this->get_family_name(),
			'variants'    => $this->get_variants(),
			'display'     => $this->get_font_display(),
		] );
	}
}
