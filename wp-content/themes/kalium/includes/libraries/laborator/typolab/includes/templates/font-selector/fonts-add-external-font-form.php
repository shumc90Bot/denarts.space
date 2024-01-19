<?php
/**
 * TypoLab - ultimate font management library.
 *
 * Add External Font Form.
 *
 * @var TypoLab_Font $font
 *
 * @author Laborator
 * @link   https://laborator.co
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Font stylesheet input
TypoLab_UI_Components::font_stylesheet_url_form( $font->get_stylesheet_url() );

// Font variants
TypoLab_UI_Components::font_face_variants_form( $font->get_variants() );

// Fetch font variants
TypoLab_UI_Components::button( 'Fetch font variants', 'kalium-icon kalium-admin-icon-refresh', 'fetch-font-variants' );

// Add font variant
TypoLab_UI_Components::button( 'Add font variant', 'kalium-icon kalium-admin-icon-plus', 'add-font-variant' );
