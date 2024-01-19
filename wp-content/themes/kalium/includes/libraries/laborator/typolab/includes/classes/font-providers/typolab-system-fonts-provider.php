<?php
/**
 * TypoLab - ultimate font management library.
 *
 * System fonts provider.
 *
 * @author Laborator
 * @link   https://laborator.co
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class TypoLab_System_Fonts_Provider {

	/**
	 * Provider ID.
	 *
	 * @var string
	 */
	public static $provider_id = 'system-fonts';

	/**
	 * Get system fonts.
	 *
	 * @return array
	 */
	public static function get_fonts() {
		$all_variants = [
			'100',
			'100italic',
			'200',
			'200italic',
			'300',
			'300italic',
			'400',
			'400italic',
			'500',
			'500italic',
			'600',
			'600italic',
			'700',
			'700italic',
			'800',
			'800italic',
			'900',
			'900italic',
		];

		return apply_filters( 'typolab_system_fonts_list', [
			// System Font
			[
				'id'         => 'system-font',
				'family'     => 'System Font',
				'variants'   => $all_variants,
				'font_stack' => [

					// Safari for OS X and iOS (San Francisco)
					'-apple-system',

					// Chrome < 56 for OS X (San Francisco)
					'BlinkMacSystemFont',

					// Windows
					TypoLab_Helper::quote( 'Segoe UI' ),

					// Android
					TypoLab_Helper::quote( 'Roboto' ),

					// Basic web fallback
					TypoLab_Helper::quote( 'Helvetica Neue' ),
					'Arial',
					'sans-serif',

					// Emoji fonts
					TypoLab_Helper::quote( 'Apple Color Emoji' ),
					TypoLab_Helper::quote( 'Segoe UI Emoji' ),
					TypoLab_Helper::quote( 'Segoe UI Symbol' ),
				],
			],
			// Helvetica
			[
				'id'         => 'helvetica',
				'family'     => 'Helvetica',
				'variants'   => $all_variants,
				'font_stack' => [
					'Helvetica',
					'Verdana',
					'Arial',
					'sans-serif',
				],
			],
			// Verdana
			[
				'id'         => 'verdana',
				'family'     => 'Verdana',
				'variants'   => $all_variants,
				'font_stack' => [
					'Verdana',
					'Helvetica',
					'Arial',
					'sans-serif',
				],
			],
			// Arial
			[
				'id'         => 'arial',
				'family'     => 'Arial',
				'variants'   => $all_variants,
				'font_stack' => [
					'Arial',
					'Helvetica',
					'Verdana',
					'sans-serif',
				],
			],
			// Times
			[
				'id'         => 'times',
				'family'     => 'Times',
				'variants'   => $all_variants,
				'font_stack' => [
					'Times',
					'Georgia',
					'serif',
				],
			],
			// Georgia
			[
				'id'         => 'georgia',
				'family'     => 'Georgia',
				'variants'   => $all_variants,
				'font_stack' => [
					'Georgia',
					'Times',
					'serif',
				],
			],
			// Courier
			[
				'id'         => 'courier',
				'family'     => 'Courier',
				'variants'   => $all_variants,
				'font_stack' => [
					'Courier',
					'monospace',
				],
			],
		] );
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
				'category'    => null,
				'font_data'   => $font,
			];
		}


		return $list;
	}

	/**
	 * Get single font.
	 *
	 * @param string $font_family
	 *
	 * @return array|null
	 */
	public static function get_font( $font_family ) {
		$fonts = self::get_fonts_list();

		foreach ( $fonts as $font ) {
			if ( $font['font_family'] === $font_family ) {
				return kalium_get_array_key( $font, 'font_data', [] );
			}
		}

		return null;
	}
}
