<?php
/**
 * TypoLab - ultimate font management library.
 *
 * Font upload form.
 *
 * @var TypoLab_Font $font
 *
 * @author Laborator
 * @link   https://laborator.co
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Font Face name
TypoLab_UI_Components::font_face_name_form( $font->get_family_name() );

// Font face variants
TypoLab_UI_Components::font_face_variants_form( $font->get_variants() );

// Add font variant button
TypoLab_UI_Components::button( 'Add font variant', 'kalium-icon kalium-admin-icon-plus', 'add-font-variant' );
