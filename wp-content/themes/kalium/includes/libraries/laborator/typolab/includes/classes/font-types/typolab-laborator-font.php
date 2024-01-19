<?php
/**
 * TypoLab - ultimate font management library.
 *
 * Laborator Font object.
 *
 * @author Laborator
 * @link   https://laborator.co
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class TypoLab_Laborator_Font extends TypoLab_Font implements TypoLab_Installable_Font {

	/**
	 * Use font assets.
	 */
	use TypoLab_Font_Assets;

	/**
	 * Font preload support.
	 */
	use TypoLab_Font_Preload;

	/**
	 * Sort font subsets in the correct order.
	 *
	 * @param string $a
	 * @param string $b
	 *
	 * @return int
	 */
	public static function sort_subsets( $a, $b ) {
		$order = [
			'latin',
			'latin-ext',
			'greek',
			'greek-ext',
			'cyrillic',
			'cyrillic-ext',
		];

		$index_a = array_search( $a, $order );
		$index_b = array_search( $b, $order );

		if ( is_numeric( $index_a ) && is_numeric( $index_b ) ) {
			return $index_a - $index_b;
		}
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

		return $this->get_font_url( $stylesheet );
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

				// Loop through font subsets
				foreach ( $variant_files as $subset => $web_fonts ) {
					$variant_file_exists = false;

					foreach ( $web_fonts as $file ) {
						if ( file_exists( trailingslashit( TypoLab::$fonts_dir ) . $file ) ) {
							$variant_file_exists = true;
							break;
						}
					}

					// Font variant file (webfont) doesn't exists
					if ( ! $variant_file_exists ) {
						return false;
					}
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
		$family_name = $this->get_family_name();

		// Theme registration is required
		if ( ! kalium()->theme_license->is_theme_registered() ) {
			return new WP_Error( 'theme_not_registered', sprintf( 'Font files could not be downloaded, theme must be registered in order to install premium fonts! <p>Go to <a href="%1$s" class="kalium-theme-registration-link">Laborator &raquo; Registration</a> to register your theme.</p>', esc_url( Kalium_About::get_tab_link( 'theme-registration' ) ) ) );
		}

		if ( $laborator_font = TypoLab_Laborator_Fonts_Provider::get_font_by_family_name( $family_name ) ) {
			$font_id          = $laborator_font['id'];
			$font_package_url = str_replace( '{license-key}', kalium()->theme_license->get_license_key(), $laborator_font['package'] );
			$files            = TypoLab_Font_Installer::get_files_from_archive( $font_package_url );
			$subsets          = $laborator_font['subsets'];

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
				$variant_name     = $this->get_variant_value( $variant );
				$variant_webfonts = [];

				// Populate variant webfonts for each subset
				foreach ( $subsets as $subset ) {
					foreach ( $files as $file ) {
						if ( preg_match( '/\\/' . $variant_name . '\\/' . $subset . '\\//', $file ) ) {
							$file_info     = pathinfo( $file );
							$extension     = strtolower( kalium_get_array_key( $file_info, 'extension' ) );
							$new_file_name = sprintf( '%s-%s-%s-webfont.%s', $font_id, $variant_name, $subset, $extension );

							// Add supported webfont file to variant array
							if ( in_array( $extension, [ 'woff2', 'woff' ] ) ) {
								$variant_webfonts[ $subset ][ $extension ] = TypoLab_Font_Installer::copy_file_to_font_directory( $this, $file, $new_file_name );
							}
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
	}

	/**
	 * Generate stylesheet.
	 */
	private function generate_stylesheet() {
		$font_assets    = $this->get_font_assets();
		$font_faces     = $css_contents = [];
		$laborator_font = TypoLab_Laborator_Fonts_Provider::get_font_by_family_name( $this->get_family_name() );
		$unicode_ranges = kalium_get_array_key( $laborator_font, 'unicode_range', [] );

		foreach ( $this->get_variants() as $variant ) {
			$variant_name = $this->get_variant_value( $variant );
			$subsets      = kalium_get_array_key( $font_assets, $variant_name, [] );

			// Sort subsets
			uksort( $subsets, [ 'self', 'sort_subsets' ] );

			// Loop through subsets
			foreach ( $subsets as $subset => $web_fonts ) {
				$font_face = [
					'font-family' => sprintf( '"%s"', esc_attr( $this->get_family_name() ) ),
					'font-weight' => 400 === $variant->weight ? 'normal' : $variant->weight,
					'font-style'  => $variant->is_italic() ? 'italic' : 'normal',
				];

				// Give priority to WOFF2
				if ( isset( $web_fonts['woff2'] ) ) {
					$font_face['src'] = sprintf( 'url("%s") format("woff2")', esc_attr( $web_fonts['woff2'] ) );
				} else if ( isset( $web_fonts['woff'] ) ) {
					$font_face['src'] = sprintf( 'url("%s") format("woff")', esc_attr( $web_fonts['woff'] ) );
				}

				// Unicode range
				if ( isset( $unicode_ranges[ $subset ] ) ) {
					$font_face['unicode-range'] = $unicode_ranges[ $subset ];
				}

				// If there is source
				if ( ! empty( $font_face['src'] ) ) {
					$font_face['font-display'] = $this->get_font_display();

					// Add font face to list
					$font_faces[] = $subset;
					$font_faces[] = $font_face;
				}
			}
		}

		// Generate CSS style
		foreach ( $font_faces as $font_face ) {
			if ( is_string( $font_face ) ) {
				$css_contents[] = sprintf( '/* %s */', $font_face );
			} else {
				$css_contents[] = '@font-face {';
				foreach ( $font_face as $prop_name => $prop_value ) {
					$css_contents[] = "\t" . $prop_name . ': ' . $prop_value . ';';
				}
				$css_contents[] = '}';
				$css_contents[] = ''; // New line
			}
		}

		// Generate file
		$file_name = TypoLab_Font_Installer::create_font_file( $this, implode( PHP_EOL, $css_contents ) );

		// Set stylesheet option
		if ( ! is_wp_error( $file_name ) ) {
			$this->set_option( 'stylesheet', $file_name );
		}
	}
}
