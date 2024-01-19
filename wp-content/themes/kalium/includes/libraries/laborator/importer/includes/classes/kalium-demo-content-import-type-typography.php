<?php
/**
 * Kalium WordPress Theme
 *
 * Demo Content Type - Typography class.
 *
 * @author Laborator
 * @link   https://kaliumtheme.com
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

class Kalium_Demo_Content_Import_Type_Typography extends Kalium_Demo_Content_Import_Type {

	/**
	 * Get content pack name.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'Typography';
	}

	/**
	 * Backup current font sizes.
	 *
	 * @return void
	 */
	public function do_backup() {

		// Vars
		$backup_manager  = $this->get_content_pack()->backup_manager();
		$font_appearance = TypoLab_Font_Appearance_Settings::get_settings_raw();

		// Save backup option
		$backup_manager->set_backup_option_once( 'typolab_font_appearance_settings', $font_appearance );
	}

	/**
	 * Import typography and font sizes.
	 *
	 * @return void
	 */
	public function do_import() {

		// Execute parent do_import
		parent::do_import();

		// Do not run if there are errors reported or option is unchecked
		if ( $this->errors->has_errors() || ! $this->is_checked() ) {
			return;
		}

		// Vars
		$content_pack    = $this->get_content_pack();
		$import_manager  = $content_pack->import_manager();
		$backup_manager  = $content_pack->backup_manager();
		$import_instance = $content_pack->get_import_instance();

		// Loop through each source
		foreach ( $this->get_sources() as $source ) {

			// Typography options
			$typography_file = $import_manager->get_content_pack_import_source_path( $source['name'] );

			// Check if typography file exists
			if ( true === kalium()->filesystem->exists( $typography_file ) ) {
				$typography_json = kalium()->filesystem->get_contents( $typography_file );

				// Import object
				$typography      = json_decode( $typography_json, true );
				$fonts           = kalium_get_array_key( $typography, 'fonts' );
				$font_appearance = kalium_get_array_key( $typography, 'font_appearance' );

				// Import TypoLab
				TypoLab_Font_Export_Import::import( [
					'fonts'           => $fonts,
					'font_appearance' => $font_appearance,
					'install_fonts'   => true,
					'preload_fonts'   => true,
				] );


				// Imported font IDs
				$imported_font_ids = TypoLab_Font_Export_Import::$imported_font_ids;

				// Backup imported font ids
				$backup_font_ids = $backup_manager->get_backup_option( 'typolab_fonts', [] );
				$backup_font_ids = array_merge( $backup_font_ids, $imported_font_ids );
				$backup_manager->update_backup_option( 'typolab_fonts', $backup_font_ids );

				// Mark as successful import
				$import_instance->set_import_success();
			} else {

				// Theme options file doesn't exists
				$this->errors->add( 'kalium_demo_content_typolab_not_exists', 'Typography file doesn\'t exists!' );
			}
		}

		// Add errors to import instance
		if ( $this->errors->has_errors() ) {
			$import_instance->add_error( $this->errors );
		}
	}

	/**
	 * Remove installed fonts and restore previous font sizes.
	 *
	 * @return void
	 */
	public function do_remove() {

		// Vars
		$backup_manager  = $this->get_content_pack()->backup_manager();
		$imported_fonts  = $backup_manager->get_backup_option( 'typolab_fonts' );
		$font_appearance = $backup_manager->get_backup_option( 'typolab_font_appearance_settings' );

		// Remove installed fonts
		if ( is_array( $imported_fonts ) ) {
			foreach ( $imported_fonts as $font_id ) {
				TypoLab::delete_font( $font_id );
			}
		}

		// Restore font appearance settings
		TypoLab_Font_Appearance_Settings::set_settings( $font_appearance );

		// Mark as removed
		parent::do_remove();
	}
}
