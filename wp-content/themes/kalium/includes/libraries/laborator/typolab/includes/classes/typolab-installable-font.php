<?php
/**
 * TypoLab - ultimate font management library.
 *
 * Installable font interface.
 *
 * @author Laborator
 * @link   https://laborator.co
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

interface TypoLab_Installable_Font {

	/**
	 * Check if font is installed.
	 *
	 * @return bool
	 */
	public function is_installed();

	/**
	 * Install font.
	 *
	 * @return true|WP_Error
	 */
	public function install();
}
