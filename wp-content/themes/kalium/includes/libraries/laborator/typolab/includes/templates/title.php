<?php
/**
 * TypoLab - ultimate font management library.
 *
 * Title template.
 *
 * @author Laborator
 * @link   https://laborator.co
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Show page title
TypoLab_UI_Components::page_title( TypoLab::$page_title, TypoLab::$page_sub_title );
