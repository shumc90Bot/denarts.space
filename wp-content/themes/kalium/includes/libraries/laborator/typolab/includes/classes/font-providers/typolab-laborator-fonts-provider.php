<?php
/**
 * TypoLab - ultimate font management library.
 *
 * Laborator Fonts provider.
 *
 * @author Laborator
 * @link   https://laborator.co
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class TypoLab_Laborator_Fonts_Provider {

	/**
	 * Provider ID.
	 *
	 * @var string
	 */
	public static $provider_id = 'laborator-fonts';

	/**
	 * Loaded Laborator Fonts.
	 *
	 * @var array
	 */
	public static $fonts_list;

	/**
	 * Load and return Laborator fonts.
	 * This function will cache the fonts list the first time is called.
	 *
	 * @return array
	 */
	public static function get_fonts() {
		if ( is_null( self::$fonts_list ) ) {
			$laborator_fonts = file_get_contents( sprintf( '%s/assets/json/laborator-fonts.json', TypoLab::$typolab_dir ) );
			$fonts_json      = json_decode( $laborator_fonts, true );

			self::$fonts_list = kalium_get_array_key( $fonts_json, 'fonts', [] );
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
			if ( $family_name === $font['family'] ) {
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
				'font_family' => $font['family'],
				'category'    => $font['category'],
				'font_data'   => $font,
			];
		}


		return $list;
	}
}
