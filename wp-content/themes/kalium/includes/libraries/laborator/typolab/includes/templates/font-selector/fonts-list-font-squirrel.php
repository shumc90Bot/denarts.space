<?php
/**
 * TypoLab - ultimate font management library.
 *
 * Font Squirrel fonts list.
 *
 * @var TypoLab_Font $font
 *
 * @author Laborator
 * @link   https://laborator.co
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$fonts_list = TypoLab_Font_Squirrel_Provider::get_fonts_list();

TypoLab_UI_Components::font_selector( $fonts_list, [
	'provider'       => TypoLab_Font_Squirrel_Provider::$provider_id,
	'search_filters' => TypoLab_Helper::prepare_font_selector_categories( $fonts_list ),
	'input_value'    => $font->get_family_name(),
	'data'           => [
		'selected_variants' => $font->get_variants_values(),
	],
] );
