<?php
/**
 * TypoLab - ultimate font management library.
 *
 * Hosted Google font class.
 *
 * @author Laborator
 * @link   https://laborator.co
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class TypoLab_Hosted_Google_Font extends TypoLab_Google_Font implements TypoLab_Installable_Font {

	/**
	 * Use font assets.
	 */
	use TypoLab_Font_Assets;

	/**
	 * Get stylesheet URL.
	 *
	 * @param bool $remote_stylesheet_url
	 *
	 * @return string
	 */
	public function get_stylesheet_url( $remote_stylesheet_url = false ) {

		// Remote stylesheet URL
		if ( $remote_stylesheet_url ) {
			return parent::get_stylesheet_url();
		}

		// Local stylesheet
		$stylesheet = $this->get_stylesheet();

		if ( ! $stylesheet ) {
			return null;
		}

		return trailingslashit( TypoLab::$fonts_url ) . $stylesheet;
	}

	/**
	 * Check if font is installed.
	 *
	 * @return bool
	 */
	public function is_installed() {
		$font_assets = $this->get_font_assets();
		$stylesheet  = $this->get_stylesheet();

		// Verify stylesheet
		if ( ! $stylesheet || ! file_exists( trailingslashit( TypoLab::$fonts_dir ) . $stylesheet ) ) {
			return false;
		}

		// Get stylesheet
		$stylesheet = TypoLab_Google_Fonts_Provider::get_stylesheet_content( $this->get_stylesheet_url( true ) );

		// Stop on error
		if ( is_wp_error( $stylesheet ) ) {
			return false;
		}

		// Font files
		$font_files = kalium_get_array_key( $font_assets, 'font_files', [] );

		// Font sources
		$font_sources = TypoLab_Helper::extract_font_sources( $stylesheet );

		foreach ( $font_sources as $font_source ) {
			$filter = function ( $relative_url ) use ( $font_source ) {
				return false !== stripos( $relative_url, $font_source['basename'] );
			};

			// Variation doesn't exists
			if ( empty( array_filter( $font_files, $filter ) ) ) {
				return false;
			}
		}

		return count( $this->get_variants() ) > 0;
	}

	/**
	 * Install font.
	 *
	 * @return true|WP_Error
	 */
	public function install() {
		$stylesheet = TypoLab_Google_Fonts_Provider::get_stylesheet_content( $this->get_stylesheet_url( true ) );

		// Stop on error
		if ( is_wp_error( $stylesheet ) ) {
			return $stylesheet;
		}

		// Delete current stylesheet
		if ( $current_stylesheet = $this->get_option( 'stylesheet' ) ) {
			TypoLab_Font_Installer::delete_file( $current_stylesheet );
		}

		// Delete current font assets
		$this->delete_font_assets();

		// Downloaded font files
		$font_files = [];

		// Font sources
		$font_sources = TypoLab_Helper::extract_font_sources( $stylesheet );

		foreach ( $font_sources as $font_source ) {
			$source_url = $font_source['url'];
			$file       = TypoLab_Font_Installer::download_url( $source_url );
			$file_name  = $font_source['basename'];

			// Add file to font directory
			$font_file = TypoLab_Font_Installer::copy_file_to_font_directory( $this, $file, $file_name );

			// Replace font source with new relative path
			$stylesheet = str_replace( $source_url, $font_file, $stylesheet );

			// Add to font files list
			$font_files[] = $font_file;
		}

		// Delete tmp files
		TypoLab_Font_Installer::delete_tmp_files();

		// Store font assets
		$this->add_font_asset( 'font_files', $font_files );

		// Generate stylesheet
		$this->generate_stylesheet( $stylesheet );

		return true;
	}

	/**
	 * Generate stylesheet.
	 *
	 * @param string $stylesheet
	 */
	private function generate_stylesheet( $stylesheet ) {

		// Generate file
		$file_name = TypoLab_Font_Installer::create_font_file( $this, $stylesheet );

		// Set stylesheet option
		if ( ! is_wp_error( $file_name ) ) {
			$this->set_option( 'stylesheet', $file_name );
		}
	}
}
