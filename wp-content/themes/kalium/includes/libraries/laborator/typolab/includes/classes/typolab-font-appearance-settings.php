<?php
/**
 * TypoLab - ultimate font management library.
 *
 * Font appearance settings.
 *
 * @author Laborator
 * @link   https://laborator.co
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class TypoLab_Font_Appearance_Settings {

	/**
	 * Get raw font appearance settings (directly from database, not processed).
	 *
	 * @return array
	 */
	public static function get_settings_raw() {
		return TypoLab_Legacy_Migration::legacy_font_sizes( TypoLab::get_option( 'font_appearance', [] ) );
	}

	/**
	 * Get settings as objects.
	 *
	 * @param bool $to_array
	 *
	 * @return TypoLab_Font_Appearance_Element[]|array
	 */
	public static function get_settings( $to_array = false ) {

		// Return as array
		if ( $to_array ) {
			return array_map( function ( $element ) {
				return $element->to_array();
			}, self::get_settings() );
		}

		// Return array of objects
		$settings = [];

		foreach ( self::get_settings_raw() as $group_id => $elements ) {
			foreach ( $elements as $element_id => $element ) {
				$settings[] = new TypoLab_Font_Appearance_Element( array_merge( $element, [
					'id'       => $element_id,
					'group_id' => $group_id,
				] ) );
			}
		}

		return $settings;
	}

	/**
	 * Save font sizes.
	 *
	 * @param array $settings
	 */
	public static function set_settings( $settings ) {
		TypoLab::set_option( 'font_appearance', $settings );
	}
}
