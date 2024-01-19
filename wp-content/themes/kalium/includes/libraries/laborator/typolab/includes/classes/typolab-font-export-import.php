<?php
/**
 * TypoLab - ultimate font management library.
 *
 * Font Export/Import Manager.
 *
 * @author Laborator
 * @link   https://laborator.co
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class TypoLab_Font_Export_Import {

	/**
	 * Created font IDs.
	 *
	 * @var array
	 */
	public static $imported_font_ids = [];

	/**
	 * Export.
	 *
	 * @param array $args
	 *
	 * @return array
	 */
	public static function export( $args = [] ) {
		$args = wp_parse_args( $args, [
			'fonts'           => true,
			'font_appearance' => true,
			'font_settings'   => false,
		] );

		$export = [];

		// Fonts export
		if ( $args['fonts'] ) {
			$export['fonts'] = [];

			foreach ( TypoLab::get_fonts() as $font ) {
				$export['fonts'][] = $font->export();
			}
		}

		// Font appearance settings
		if ( $args['font_appearance'] ) {
			$export['font_appearance'] = TypoLab_Font_Appearance_Settings::get_settings_raw();
		}

		// Font settings
		if ( $args['font_settings'] ) {
			$export['font_settings'] = TypoLab::get_font_settings();
		}

		return $export;
	}

	/**
	 * Import.
	 *
	 * @param array $data
	 *
	 * @return bool
	 */
	public static function import( $data ) {
		$data   = wp_parse_args( $data, [
			'fonts'           => null,
			'font_appearance' => null,
			'font_settings'   => null,
			'install_fonts'   => false,
			'preload_fonts'   => false,
		] );
		$result = false;

		// Font settings
		if ( is_array( $data['font_settings'] ) ) {
			$result = self::import_font_settings( $data['font_settings'] );

			// Reset typolab vars
			TypoLab::instance()->init();
		}

		// Fonts
		if ( is_array( $data['fonts'] ) ) {
			$result = self::import_fonts( $data['fonts'] );
		}

		// Font appearance
		if ( is_array( $data['font_appearance'] ) ) {
			$result = self::import_font_appearance( $data['font_appearance'] );
		}

		// Install fonts that implement TypoLab_Installable_Font interface
		if ( $data['install_fonts'] ) {
			$result = self::install_fonts();

			// Stop on error
			if ( is_wp_error( $result ) || ! $result ) {
				return $result;
			}
		}

		// Preload fonts if preloading is enabled
		if ( $data['preload_fonts'] ) {
			$result = self::preload_fonts();

			// Stop on error
			if ( is_wp_error( $result ) || ! $result ) {
				return $result;
			}
		}

		return $result;
	}

	/**
	 * Import fonts.
	 *
	 * @param array $fonts
	 *
	 * @return bool
	 */
	public static function import_fonts( $fonts ) {
		if ( is_array( $fonts ) ) {
			foreach ( $fonts as $font ) {

				// Unset font ID to create it as new font
				unset( $font['id'] );

				// TypoLab_Font object
				$font_object = TypoLab_Font::create_instance( $font );

				// Trigger import method
				$font_object->import();

				// Save to database
				$font_object->save();

				// Add to imported font ids
				self::$imported_font_ids[] = $font_object->get_id();
			}

			return true;
		}

		return false;
	}

	/**
	 * Import font appearance settings.
	 *
	 * @param array $font_appearance_settings
	 *
	 * @return bool
	 */
	public static function import_font_appearance( $font_appearance_settings ) {
		if ( is_array( $font_appearance_settings ) ) {

			// Set new font appearance settings
			TypoLab_Font_Appearance_Settings::set_settings( $font_appearance_settings );

			return true;
		}

		return false;
	}

	/**
	 * Import font settings.
	 *
	 * @return bool
	 */
	public static function import_font_settings( $settings ) {
		if ( is_array( $settings ) ) {
			$current_settings = TypoLab::get_font_settings();
			$new_settings     = array_merge( $current_settings, $settings );

			// Fix numeric and boolean values
			foreach ( $new_settings as $key => $value ) {
				if ( in_array( $value, [ 'true', 'false' ] ) ) {
					$new_settings[ $key ] = kalium_validate_boolean( $value );
				} else if ( is_numeric( $value ) ) {
					$new_settings[ $key ] = +$value;
				}
			}

			// Save new font settings
			TypoLab::set_option( 'font_settings', $new_settings );

			return true;
		}

		return false;
	}

	/**
	 * Install fonts.
	 *
	 * @return bool
	 */
	public static function install_fonts() {
		$fonts  = TypoLab::get_fonts();
		$errors = [];

		foreach ( $fonts as $font ) {
			if ( $font instanceof TypoLab_Installable_Font ) {
				if ( false === $font->is_installed() ) {
					$result = $font->install();

					// Add error
					if ( is_wp_error( $result ) ) {
						$errors[] = $result;
					}

					// Save installed font
					$font->save();
				}
			}
		}

		return empty( $errors );
	}

	/**
	 * Install fonts.
	 *
	 * @return bool
	 */
	public static function preload_fonts() {
		$fonts = TypoLab::get_fonts();

		foreach ( $fonts as $font ) {
			if ( $font->supports_preload() && $font->do_preload() ) {
				$font->preload();
			}
		}

		return true;
	}

	/**
	 * Export fonts and settings (AJAX).
	 */
	public static function export_ajax() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// Exports
		$exports = kalium()->request->input( 'exports' );

		// Export data
		$export_data = self::export( $exports );
		$export_data = wp_json_encode( $export_data );

		wp_send_json_success( $export_data );
	}

	/**
	 * Import fonts and settings (AJAX).
	 */
	public static function import_ajax() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// Action type
		$type = kalium()->request->input( 'type' );
		$data = kalium()->request->input( 'data' );

		// Response
		$json_response = [
			'success' => false,
		];

		// Execute action by type
		switch ( $type ) {

			// Import fonts
			case 'fonts':
				$json_response['success'] = self::import_fonts( $data );
				break;

			// Import font appearance settings
			case 'font_appearance':
				$json_response['success'] = self::import_font_appearance( $data );
				break;

			// Import font settings
			case 'font_settings':
				$json_response['success'] = self::import_font_settings( $data );
				break;

			// Install fonts
			case 'install_fonts':
				$json_response['success'] = self::install_fonts();
				break;

			// Preload fonts
			case 'preload_fonts':
				$json_response['success'] = self::preload_fonts();
				break;
		}

		wp_send_json( $json_response );
		die();
	}
}
