<?php
/**
 * TypoLab - ultimate font management library.
 *
 * Hosted Font object.
 *
 * @author Laborator
 * @link   https://laborator.co
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class TypoLab_Hosted_Font extends TypoLab_Font {

	/**
	 * Font preload support.
	 */
	use TypoLab_Font_Preload;

	/**
	 * Print font styles.
	 */
	public function print_styles() {
		$font_faces = [];

		// Add font faces
		foreach ( $this->get_variants() as $variant ) {
			$font_faces[] = $variant->generate_font_face();
		}

		// Print styles
		if ( count( $font_faces ) ) {
			echo sprintf( '<style data-hosted-font>%s</style>', implode( PHP_EOL, $font_faces ) );
		}
	}

	/**
	 * Always fetch preload variants.
	 *
	 * @return bool
	 */
	public function do_fetch_preloads() {
		return true;
	}

	/**
	 * Preload font.
	 */
	public function preload() {

		// Current font variants
		$variants = $this->get_variants();

		// Filter preload variants
		$preload_variants = $this->filter_preload_variants( $variants, [
			'load_type' => 'optimal',
		] );

		// Set font preload variants
		$this->set_preload_variants( $preload_variants );
	}

	/**
	 * Font import.
	 */
	public function import() {
		$site_url_host = parse_url( get_site_url(), PHP_URL_HOST );

		// Download sources from external host
		foreach ( $this->get_variants() as $variant ) {
			foreach ( $variant->src as $type => $source_url ) {
				$source_url_host = parse_url( $source_url, PHP_URL_HOST );

				// Download source if its from external host
				if ( $site_url_host !== $source_url_host ) {
					$download_source = wp_remote_get( $source_url );

					if ( is_array( $download_source ) ) {
						$file = wp_upload_bits( wp_basename( $source_url ), null, wp_remote_retrieve_body( $download_source ) );

						if ( empty( $file['error'] ) ) {
							$variant->src[ $type ] = $file['url'];
						}
					}
				}
			}
		}
	}
}
