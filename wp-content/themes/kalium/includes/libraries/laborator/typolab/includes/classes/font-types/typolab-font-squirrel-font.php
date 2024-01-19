<?php
/**
 * TypoLab - ultimate font management library.
 *
 * Font Squirrel font object.
 *
 * @author Laborator
 * @link   https://laborator.co
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class TypoLab_Font_Squirrel_Font extends TypoLab_Font implements TypoLab_Installable_Font {

	/**
	 * Use font assets.
	 */
	use TypoLab_Font_Assets;

	/**
	 * Font preload support.
	 */
	use TypoLab_Font_Preload;

	/**
	 * Font download source.
	 *
	 * @var string
	 */
	public static $font_download_source = 'https://www.fontsquirrel.com/fontfacekit/%s';

	/**
	 * Parse variant.
	 *
	 * @param string       $variant
	 * @param TypoLab_Font $font
	 *
	 * @return TypoLab_Font_Variant
	 */
	public static function parse_variant( $variant, $font = null ) {
		return new TypoLab_Font_Variant( [
			'name' => $variant,
		], $font );
	}

	/**
	 * Get variant as string representation.
	 *
	 * @param TypoLab_Font_Variant $variant
	 */
	public function get_variant_value( $variant ) {
		return $variant->name;
	}

	/**
	 * Get stylesheet URL.
	 *
	 * @return string
	 */
	public function get_stylesheet_url() {
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

		// Verify font assets
		foreach ( $this->get_variants() as $variant ) {
			$variant_name  = $this->get_variant_value( $variant );
			$variant_files = kalium_get_array_key( $font_assets, $variant_name );

			if ( ! empty( $variant_files ) && is_array( $variant_files ) ) {
				$variant_file_exists = false;

				foreach ( $variant_files as $file ) {
					if ( file_exists( trailingslashit( TypoLab::$fonts_dir ) . $file ) ) {
						$variant_file_exists = true;
						break;
					}
				}

				// Font variant file (webfont) doesn't exists
				if ( ! $variant_file_exists ) {
					return false;
				}
			} else {
				return false; // Font is not installed or partially installed
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
		/*
		$family_name = $this->get_family_name();

		if ( $font_squirrel_font = TypoLab_Font_Squirrel_Provider::get_font_by_family_name( $family_name ) ) {
			$family_urlname   = $font_squirrel_font['family_urlname'];
			$font_package_url = sprintf( self::$font_download_source, $family_urlname );
			$files            = TypoLab_Font_Installer::get_files_from_archive( $font_package_url );

			// Stop on error
			if ( is_wp_error( $files ) ) {
				return $files;
			}

			// Delete current stylesheet
			if ( $current_stylesheet = $this->get_option( 'stylesheet' ) ) {
				TypoLab_Font_Installer::delete_file( $current_stylesheet );
			}

			// Delete current font assets
			$this->delete_font_assets();

			// Iterate through registered variants and install each of them
			foreach ( $this->get_variants() as $variant ) {
				$variant_webfonts = [];
				$variant_name     = $this->get_variant_value( $variant );

				// Populate variant webfonts
				foreach ( $files as $file ) {
					if ( preg_match( '/\\/' . preg_quote( $variant_name ) . '-webfont/', $file ) ) {
						$file_info = pathinfo( $file );
						$extension = strtolower( kalium_get_array_key( $file_info, 'extension' ) );

						// Add supported webfont file to variant array
						if ( in_array( $extension, [ 'woff2', 'woff' ] ) ) {
							$variant_webfonts[ $extension ] = TypoLab_Font_Installer::copy_file_to_font_directory( $this, $file );
						}
					}
				}

				// Add font asset
				$this->add_font_asset( $variant_name, $variant_webfonts );
			}

			// Delete TMP files
			TypoLab_Font_Installer::delete_tmp_files();

			// Generate stylesheet
			$this->generate_stylesheet();

			return true;
		}

		return new WP_Error( 'font_family_not_exists', sprintf( 'Font family "%s" does not exists!', $family_name ) );
		*/

		// Font provider is no longer supported
		return true;
	}

	/**
	 * Generate stylesheet.
	 */
	private function generate_stylesheet() {
		/*
		$font_assets = $this->get_font_assets();
		$font_faces  = $css_contents = [];

		foreach ( $this->get_variants() as $variant ) {
			$variant_name = $this->get_variant_value( $variant );
			$font_family  = $variant_name;
			$font_face    = [
				'font-family' => sprintf( '"%s"', esc_attr( $font_family ) ),
			];

			if ( isset( $font_assets[ $variant_name ] ) ) {
				$web_fonts = $font_assets[ $variant_name ];

				// Give priority to WOFF2
				if ( isset( $web_fonts['woff2'] ) ) {
					$font_face['src'] = sprintf( 'url("%s") format("woff2")', esc_attr( $web_fonts['woff2'] ) );
				} else if ( isset( $web_fonts['woff'] ) ) {
					$font_face['src'] = sprintf( 'url("%s") format("woff")', esc_attr( $web_fonts['woff'] ) );
				}

				// If there is source
				if ( ! empty( $font_face['src'] ) ) {
					$font_face['font-weight']  = 'normal';
					$font_face['font-style']   = 'normal';
					$font_face['font-display'] = $this->get_font_display();

					// Add font face to list
					$font_faces[] = $font_face;
				}
			}
		}

		// Generate CSS style
		foreach ( $font_faces as $font_face ) {
			$css_contents[] = '@font-face {';
			foreach ( $font_face as $prop_name => $prop_value ) {
				$css_contents[] = "\t" . $prop_name . ': ' . $prop_value . ';';
			}
			$css_contents[] = '}';
			$css_contents[] = ''; // New line
		}

		// Generate file
		$file_name = TypoLab_Font_Installer::create_font_file( $this, implode( PHP_EOL, $css_contents ) );

		// Set stylesheet option
		if ( ! is_wp_error( $file_name ) ) {
			$this->set_option( 'stylesheet', $file_name );
		}
		*/
	}
}
