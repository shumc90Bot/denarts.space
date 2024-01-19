<?php
/**
 * TypoLab - ultimate font management library.
 *
 * Adobe fonts list.
 *
 * @var TypoLab_Font $font
 *
 * @author Laborator
 * @link   https://laborator.co
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$fonts_list = TypoLab_Adobe_Fonts_Provider::get_fonts_list();

TypoLab_UI_Components::font_selector( $fonts_list, [
	'provider'         => TypoLab_Adobe_Fonts_Provider::$provider_id,
	'search_filters' => TypoLab_Helper::prepare_font_selector_categories( $fonts_list ),
	'input_value'    => $font->get_family_name(),
	'filters_label'  => 'Font Kit',
	'data'           => [
		'selected_variants' => $font->get_variants_values(),
	],
] );

