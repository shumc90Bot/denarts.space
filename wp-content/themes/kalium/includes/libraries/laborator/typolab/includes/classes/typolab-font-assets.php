<?php
/**
 * TypoLab - ultimate font management library.
 *
 * Font assets trait.
 *
 * @author Laborator
 * @link   https://laborator.co
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

trait TypoLab_Font_Assets {

	/**
	 * Font assets array key name.
	 *
	 * @var string
	 */
	private $font_assets_key_name = 'font_assets';

	/**
	 * Add font asset.
	 *
	 * @param string $name
	 * @param array  $files
	 */
	public function add_font_asset( $name, $files = [] ) {
		$font_assets          = $this->get_font_assets();
		$font_assets[ $name ] = $files;
		$this->set_option( $this->font_assets_key_name, $font_assets );
	}

	/**
	 * Get font assets.
	 *
	 * @return array
	 */
	public function get_font_assets() {
		return $this->get_option( $this->font_assets_key_name, [] );
	}

	/**
	 * Get font asset by name.
	 *
	 * @param string $name
	 *
	 * @return array
	 */
	public function get_font_asset( $name ) {
		$font_assets = $this->get_font_assets();

		return kalium_get_array_key( $font_assets, $name );
	}

	/**
	 * Delete font asset.
	 */
	public function delete_font_assets() {
		$font_assets = $this->get_font_assets();
		$directories = [];

		foreach ( $font_assets as $name => $files ) {
			foreach ( $files as $web_fonts ) {
				$web_fonts = is_array( $web_fonts ) ? $web_fonts : [ $web_fonts ];

				foreach ( $web_fonts as $file ) {
					TypoLab_Font_Installer::delete_file( $file );
					$directories[] = dirname( $file );
				}
			}

			// Remove asset from list
			unset( $font_assets[ $name ] );
		}

		// Remove directories
		foreach ( array_unique( $directories ) as $directory ) {
			TypoLab_Font_Installer::delete_directory( $directory );
		}

		// Update list
		$this->set_option( $this->font_assets_key_name, $font_assets );
	}
}
