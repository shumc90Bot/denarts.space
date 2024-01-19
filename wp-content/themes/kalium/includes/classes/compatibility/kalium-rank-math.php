<?php
/**
 * Kalium WordPress Theme
 *
 * Rank Math compatibility class.
 *
 * @author Laborator
 * @link   https://kaliumtheme.com
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

class Kalium_Rank_Math {

	/**
	 * Constructor.
	 */
	public function __construct() {

		// Only run if plugin is active
		if ( ! kalium()->is->rank_math_active() ) {
			return;
		}

		// Add ACF images in sitemap
		add_filter( 'rank_math/sitemap/urlimages', [ $this, 'add_acf_images_to_sitemap' ], 10, 2 );
	}

	/**
	 * Add ACF images to sitemap.
	 *
	 * @param array $images
	 * @param int   $post_id
	 *
	 * @return array
	 */
	public function add_acf_images_to_sitemap( $images, $post_id ) {
		$gallery = kalium_get_field( 'gallery', $post_id );
		if ( ! empty( $gallery ) ) {
			$acf_images  = [];
			$find_images = function ( $entry ) use ( & $find_images, & $acf_images ) {
				if ( is_array( $entry ) ) {
					foreach ( $entry as $key => $value ) {
						if ( 'filename' === $key && ! empty( $entry['url'] ) && preg_match( '/\.(jpe?g|png|gif|svg|webp)$/i', $entry['url'] ) ) {
							$acf_images[] = [
								'src'   => $entry['url'],
								'title' => get_the_title( $entry['id'] ),
								'alt'   => get_post_meta( $entry['id'], '_wp_attachment_image_alt', true ),
							];
						} else if ( is_array( $value ) ) {
							$find_images( $value );
						}
					}
				}
			};
			$find_images( $gallery );
			if ( ! empty( $acf_images ) ) {
				$images = array_merge( $images, $acf_images );
			}
		}

		return $images;
	}
}
