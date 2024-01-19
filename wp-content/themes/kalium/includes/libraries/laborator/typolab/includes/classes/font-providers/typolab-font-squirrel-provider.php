<?php
/**
 * TypoLab - ultimate font management library.
 *
 * Font Squirrel provider.
 *
 * @author Laborator
 * @link   https://laborator.co
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class TypoLab_Font_Squirrel_Provider {

	/**
	 * Font provider ID.
	 *
	 * @var string
	 */
	public static $provider_id = 'font-squirrel';

	/**
	 * Loaded Font Squirrel fonts.
	 *
	 * @return array
	 */
	public static $fonts_list;

	/**
	 * Load and return Font Squirrel fonts.
	 * This function will cache the fonts list the first time is called.
	 *
	 * @return array
	 */
	public static function get_fonts() {
		if ( is_null( self::$fonts_list ) ) {
			$font_squirrel = file_get_contents( sprintf( '%s/assets/json/font-squirrel.json', TypoLab::$typolab_dir ) );
			$fonts_json    = json_decode( $font_squirrel, true );

			self::$fonts_list = $fonts_json;
		}

		return self::$fonts_list;
	}

	/**
	 * Get font info by font family name.
	 *
	 * @param string $family_name
	 *
	 * @return array|null
	 */
	public static function get_font_by_family_name( $family_name ) {
		$fonts = self::get_fonts();

		foreach ( $fonts as $font ) {
			if ( $family_name === $font['family_name'] ) {
				return $font;
			}
		}

		return null;
	}

	/**
	 * Get fonts list.
	 * Suitable for Font Selector field.
	 *
	 * @return array
	 */
	public static function get_fonts_list() {
		$list = [];

		foreach ( self::get_fonts() as $font ) {
			$list[] = [
				'font_family' => $font['family_name'],
				'category'    => $font['classification'],
				'font_data'   => $font,
			];
		}


		return $list;
	}

	/**
	 * Get font variants.
	 *
	 * @param string $family_urlname
	 *
	 * @return array|WP_Error|false
	 */
	public static function get_font_variants( $family_urlname ) {
		$url     = sprintf( 'https://www.fontsquirrel.com/api/familyinfo/%s', $family_urlname );
		$request = wp_remote_get( $url );

		// On errors
		if ( is_wp_error( $request ) ) {
			return $request;
		}

		// Variants
		$variants = json_decode( wp_remote_retrieve_body( $request ), true );

		return is_array( $variants ) ? $variants : false;
	}
}
