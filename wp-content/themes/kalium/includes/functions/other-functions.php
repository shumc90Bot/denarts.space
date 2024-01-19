<?php
/**
 * Kalium WordPress Theme
 *
 * Other/uncategorized functions.
 *
 * @author Laborator
 * @link   https://kaliumtheme.com
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

/**
 * Null function.
 *
 * @return void
 */
function kalium_null_function() {
}

/**
 * Conditional return value.
 *
 * @param bool  $condition
 * @param mixed $if
 * @param mixed $else
 *
 * @return mixed
 */
function kalium_conditional( $condition, $if, $else = null ) {
	return $condition ? $if : $else;
}

/**
 * Enqueue GSAP library.
 *
 * @return void
 */
function kalium_enqueue_gsap_library() {
	kalium_enqueue( 'gsap' );
}

/**
 * Enqueue Isotope & Packery library.
 *
 * @return void
 */
function kalium_enqueue_isotope_and_packery_library() {
	kalium_enqueue( 'isotope' );

	// Workaround for WPBakery
	if ( wp_script_is( 'isotope', 'registered' ) ) {
		wp_dequeue_script( 'isotope' );
		wp_deregister_script( 'isotope' );
	}
}

/**
 * Enqueue media library.
 *
 * @return void
 */
function kalium_enqueue_media_library() {
	kalium()->media->enqueue_media_library();
}

/**
 * Enqueue flickity carousel library.
 *
 * @return void
 */
function kalium_enqueue_flickity_library() {
	kalium_enqueue( 'flickity' );
}

/**
 * Enqueue flickity fade library.
 *
 * @return void
 */
function kalium_enqueue_flickity_fade_library() {
	kalium_enqueue( 'flickity-fade' );
}

/**
 * Enqueue Slick Gallery.
 *
 * @return void
 */
function kalium_enqueue_slick_slider_library() {
	kalium_enqueue( 'slick' );
}

/**
 * Enqueue Lightbox Gallery.
 *
 * @return void
 */
function kalium_enqueue_lightbox_library() {
	kalium_enqueue( 'light-gallery' );
}

/**
 * Enqueue ScrollMagic library.
 *
 * @return void
 */
function kalium_enqueue_scrollmagic_library() {
	kalium_enqueue( 'scrollmagic' );

	if ( defined( 'KALIUM_DEBUG' ) ) {
		kalium_enqueue( 'scrollmagic-debug-js' );
	}
}

/**
 * Enqueue Kalium Sticky Header.
 *
 * @return void
 */
function kalium_enqueue_sticky_header() {
	kalium_enqueue( 'sticky-header-js' );
}

/**
 * Is holiday season (13 dec – 05 jan).
 *
 * @return bool
 */
function kalium_is_holiday_season() {
	$current_year = (int) date( 'Y' );
	$date_start   = sprintf( '%d-12-13', $current_year );
	$date_end     = sprintf( '%d-01-04', $current_year + 1 );

	return strtotime( $date_start ) <= time() && strtotime( $date_end ) >= time();
}

/**
 * Register dynamic translatable string for WPML.
 *
 * @param string $name
 * @param string $value
 *
 * @return void
 */
function kalium_wpml_register_single_string( $name, $value ) {
	do_action( 'wpml_register_single_string', 'kalium', $name, $value );
}

/**
 * WPML dynamic translatable string.
 *
 * @param string $original_value
 * @param string $name
 *
 * @return string
 */
function kalium_wpml_translate_single_string( $original_value, $name ) {
	return apply_filters( 'wpml_translate_single_string', $original_value, 'kalium', $name );
}

/**
 * Share post to social networks.
 *
 * @param string   $social_network_id
 * @param null|int $post_id
 * @param array    $args
 */
function kalium_social_network_share_post_link( $social_network_id, $post_id = null, $args = [] ) {
	$post = get_post( $post_id );

	if ( ! ( $post instanceof WP_Post ) ) {
		return;
	}

	// Link args
	$args = wp_parse_args( $args, [
		'icon_only' => false,
		'class'     => '',
	] );

	/**
	 * Filters list of providers for social networks share.
	 *
	 * @param array $networks_list
	 * @param int   $post_id
	 */
	$networks = apply_filters( 'kalium_social_network_share_post_link_providers', [
		'fb'  => [
			'id'      => 'facebook',
			'url'     => 'https://www.facebook.com/sharer.php?u={PERMALINK}',
			'tooltip' => 'Facebook',
			'icon'    => 'fab fa-facebook'
		],
		'tw'  => [
			'id'      => 'twitter',
			'url'     => 'https://twitter.com/share?text={TITLE}&url={PERMALINK}',
			'tooltip' => 'Twitter X',
			'icon'    => 'fab fa-x-twitter'
		],
		'tlr' => [
			'id'      => 'tumblr',
			'url'     => 'https://www.tumblr.com/share/link?url={PERMALINK}&name={TITLE}&description={EXCERPT}',
			'tooltip' => 'Tumblr',
			'icon'    => 'fab fa-tumblr'
		],
		'lin' => [
			'id'      => 'linkedin',
			'url'     => 'https://linkedin.com/shareArticle?mini=true&url={PERMALINK}&title={TITLE}',
			'tooltip' => 'LinkedIn',
			'icon'    => 'fab fa-linkedin'
		],
		'pi'  => [
			'id'      => 'pinterest',
			'url'     => 'https://pinterest.com/pin/create/button/?url={PERMALINK}&description={TITLE}&media={FEATURED_IMAGE}',
			'tooltip' => 'Pinterest',
			'icon'    => 'fab fa-pinterest'
		],
		'vk'  => [
			'id'      => 'vk',
			'url'     => 'https://vkontakte.ru/share.php?url={PERMALINK}&title={TITLE}&description={EXCERPT}',
			'tooltip' => 'VKontakte',
			'icon'    => 'fab fa-vk'
		],
		'wa'  => [
			'id'      => 'whatsapp',
			'url'     => 'https://api.whatsapp.com/send?text={TITLE} - {PERMALINK}',
			'tooltip' => 'WhatsApp',
			'icon'    => 'fab fa-whatsapp'
		],
		'te'  => [
			'id'      => 'telegram',
			'url'     => 'https://t.me/share/url?url={PERMALINK}&text={TITLE}',
			'tooltip' => 'Telegram',
			'icon'    => 'fab fa-telegram'
		],
		'xi'  => [
			'id'      => 'xing',
			'url'     => 'https://www.xing.com/spi/shares/new?url={PERMALINK}',
			'tooltip' => 'Xing',
			'icon'    => 'fab fa-xing',
		],
		'pr'  => [
			'id'      => 'print',
			'url'     => 'javascript:window.print();',
			'tooltip' => __( 'Print', 'kalium' ),
			'icon'    => 'fas fa-print'
		],
		'em'  => [
			'id'      => 'mail',
			'url'     => 'mailto:?subject={TITLE}&body={EMAIL_BODY}',
			'tooltip' => __( 'Email', 'kalium' ),
			'icon'    => 'fas fa-envelope'
		],
	], $post_id );

	// Network entry exists
	if ( $network_entry = kalium_get_array_key( $networks, $social_network_id ) ) {

		// Share URL
		$url = $network_entry['url'];

		// URL vars to replace
		$url_vars = [
			'PERMALINK'      => get_permalink( $post ),
			'TITLE'          => get_the_title( $post ),
			'EXCERPT'        => wp_trim_words( kalium_clean_excerpt( $post->post_excerpt, true ), 40, '&hellip;' ),
			'FEATURED_IMAGE' => wp_get_attachment_url( get_post_thumbnail_id( $post ) ),
			'EMAIL_BODY'     => sprintf( __( 'Check out what I just spotted: %s', 'kalium' ), get_permalink( $post ) ),
		];

		foreach ( $url_vars as $var_name => $value ) {
			$url = str_replace( '{' . $var_name . '}', $value, $url );
		}

		// Link attributes
		$link_atts = [
			'class'      => [
				$network_entry['id'],
				$args['class'],
			],
			'href'       => esc_url( $url ),
			'target'     => '_blank',
			'rel'        => 'noopener',
			'aria-label' => $network_entry['tooltip'],
		];

		// Print Page link
		if ( 'print' === $network_entry['id'] ) {
			$link_atts['href'] = '#print';
		}

		// Content
		$link_content = esc_html( $network_entry['tooltip'] );

		// Show icon only
		if ( $args['icon_only'] ) {
			$link_content = kalium()->helpers->build_dom_element( 'i', [
				'class' => [
					'icon',
					$network_entry['icon'],
				],
			] );
		}

		/**
		 * Filters social network share link markup.
		 *
		 * @param string   $link
		 * @param int|null $post_id
		 * @param array    $args
		 */
		echo apply_filters( 'kalium_social_network_share_post_link', kalium()->helpers->build_dom_element( 'a', $link_atts, $link_content ), $post_id, $args );
	}
}

/**
 * Search page url.
 *
 * @return string
 */
function kalium_search_url() {
	global $polylang;

	// Default search page URL
	$url = home_url( '/' );

	// Polylang Search URL
	if ( ! empty( $polylang ) ) {
		$url = $polylang->curlang->search_url;
	}

	return apply_filters( 'kalium_search_url', $url );
}

/**
 * Render video as DOM element.
 *
 * @param [] $video_sources
 * @param [] $args
 *
 * @return string|null|void
 *
 * @since 3.2
 */
function kalium_render_video_element( $video_sources, $args = [] ) {
	if ( empty( $video_sources ) ) {
		return null;
	}

	// Args
	$args = wp_parse_args( $args, [
		'poster'      => '',
		'poster_size' => 'large',
		'controls'    => false,
		'autoplay'    => true,
		'muted'       => true,
		'loop'        => false,
		'posterplay'  => false,
		'echo'        => false,
		'atts'        => [],
	] );

	// Setup single video source
	if ( is_array( $video_sources ) && isset( $video_sources['url'] ) ) {
		$video_sources = [ $video_sources ];
	}

	// Video vars
	$video_atts   = [
		'class'                      => [
			'video-js-el',
			'vjs-default-skin',
			'vjs-minimal-skin',
		],
		'preload'                    => 'auto',
		'data-vsetup'                => [
			'controlBar' => [
				'fullscreenToggle'       => false,
				'pictureInPictureToggle' => false,
			],
		],
		'data-autosize'              => 'true',
		'data-autoplay-pause-others' => 'no',
		'playsinline'                => '',
	];
	$sources      = [];
	$video_width  = '';
	$video_height = '';
	$has_source   = false;

	// Attributes
	if ( is_array( $args['atts'] ) ) {
		foreach ( $args['atts'] as $attr_name => $attr_value ) {
			if ( is_string( $attr_name ) ) {
				$video_atts[ $attr_name ] = $attr_value;
			}
		}
	}

	// Video controls
	if ( $args['controls'] ) {
		$video_atts['controls'] = '';
	}

	// Autoplay
	if ( $args['autoplay'] ) {
		$video_atts['data-autoplay'] = 'on-viewport';
	}

	// Muted
	if ( $args['muted'] ) {
		$video_atts['muted'] = '';
	}

	// Loop
	if ( $args['loop'] ) {
		$video_atts['loop'] = '';
	}

	// Play when clicking poster and hide controls
	if ( $args['posterplay'] ) {

		// Remove volume and progress bar controls
		$video_atts['data-vsetup']['controlBar']['volumePanel'] = $video_atts['data-vsetup']['controlBar']['progressControl'] = false;

		// Assign attributes
		$video_atts['data-hide-controls-on-play'] = $video_atts['controls'] = $video_atts['data-autoplay'] = '';
	}

	// Enqueue media library (VideoJS)
	kalium_enqueue( 'videojs' );

	// Build video sources
	if ( is_array( $video_sources ) ) {
		foreach ( $video_sources as $video ) {
			$video_source = kalium_get_array_key( $video, 'src', $video );

			if ( ! empty( $video_source ) && ! empty( $video_source['url'] ) ) {
				$has_source = true;
				$sources[]  = sprintf( '<source src="%s" type="%s" />', esc_attr( $video_source['url'] ), esc_attr( $video_source['mime_type'] ) );

				// Video width and height
				if ( ( ! $video_width || ! $video_height ) && ! empty( $video_source['width'] ) && ! empty( $video_source['height'] ) ) {
					$video_width  = $video_source['width'];
					$video_height = $video_source['height'];
				}
			}
		}
	} // URL video source
	else if ( is_string( $video_sources ) ) {
		$source_urls                  = wp_extract_urls( $video_sources );
		$load_videojs_youtube_library = false;

		// YouTube video
		if ( kalium()->media->is_youtube( $video_sources ) ) {
			$has_source                             = true;
			$video_atts['data-vsetup']['techOrder'] = [ 'youtube' ];
			$video_atts['data-vsetup']['sources']   = [
				[
					'type' => 'video/youtube',
					'src'  => $video_sources,
				],
			];

			// Video dimensions
			if ( ( $youtube_embed = wp_oembed_get( $video_sources ) ) && preg_match_all( '/(width|height)="(?<size>\d+)"/i', $youtube_embed, $matches ) ) {
				$video_width  = $matches['size'][0];
				$video_height = $matches['size'][1];
			}

			// Load VideoJS YouTube library
			$load_videojs_youtube_library = true;
		} // YouTube embed
		else if ( $source_urls && kalium()->media->is_youtube( $source_urls[0] ) && preg_match( '/embed\/(?<youtube_id>[^\/\?]+)/', $source_urls[0], $matches ) ) {
			$has_source  = true;
			$youtube_url = 'https://youtube.com/watch?v=' . $matches['youtube_id'];

			$video_atts['data-vsetup']['techOrder'] = [ 'youtube' ];
			$video_atts['data-vsetup']['sources']   = [
				[
					'type' => 'video/youtube',
					'src'  => $youtube_url,
				],
			];

			// Video dimensions
			if ( preg_match_all( '/(width|height)="(?<size>\d+)"/i', $video_sources, $matches ) ) {
				$video_width  = $matches['size'][0];
				$video_height = $matches['size'][1];
			}

			// Load VideoJS YouTube library
			$load_videojs_youtube_library = true;
		}

		// Load VideoJS YouTube library
		if ( $load_videojs_youtube_library ) {
			kalium_enqueue( 'videojs-youtube-js' );
		}
	}

	// If there are no sources
	if ( ! $has_source ) {
		return null;
	}

	// Set video dimensions
	if ( ! empty( $video_width ) && ! empty( $video_height ) ) {
		$video_atts['width']  = $video_width;
		$video_atts['height'] = $video_height;
	}

	// Video Poster
	if ( is_numeric( $args['poster'] ) && ( $poster_image = wp_get_attachment_image_src( $args['poster'], $args['poster_size'] ) ) ) {
		$video_atts['poster'] = $poster_image[0];

		// Image placeholder color
		if ( kalium()->images->placeholder_dominant_color && ( $image_placeholder_color = kalium()->images->get_dominant_color( $args['poster'] ) ) ) {
			static $image_placeholder_id = 1;
			$image_placeholder_class = 'image-placeholder-color-' . $image_placeholder_id;

			$video_atts['class'][] = $image_placeholder_class;

			kalium_append_custom_css( '.' . $image_placeholder_class, [
				'background-color' => $image_placeholder_color,
			] );

			$image_placeholder_id ++;
		}
	}

	// Encode VideoJS setup attribute
	$video_atts['data-vsetup'] = json_encode( $video_atts['data-vsetup'] );

	$output = kalium()->helpers->build_dom_element( 'video', $video_atts, implode( PHP_EOL, $sources ) );

	// Aspect ratio wrap
	$output = kalium()->helpers->build_dom_element( 'div', [
		'class' => 'image-placeholder video',
		'style' => sprintf( 'padding-bottom: %s%%;', kalium()->images->calculate_aspect_ratio( $video_width, $video_height ) ),
	], $output );

	// Echo output
	if ( $args['echo'] ) {
		echo $output;

		return;
	}

	return $output;
}

/**
 * Check if its coming soon mode.
 *
 * @return bool
 * @since 3.4.3
 */
function kalium_is_coming_soon_mode() {
	$coming_soon_mode = kalium_get_theme_option( 'coming_soon_mode' );

	// Do not show for administrators
	if ( current_user_can( 'manage_options' ) ) {
		$coming_soon_mode = kalium()->request->has( 'view-coming-soon' );
	}

	return ! ! $coming_soon_mode;
}

/**
 * Check if its maintenance mode.
 *
 * @return bool
 * @since 3.4.3
 */
function kalium_is_maintenance_mode() {
	$maintenance_mode = kalium_get_theme_option( 'maintenance_mode' );

	// Do not show for administrators
	if ( current_user_can( 'manage_options' ) ) {
		$maintenance_mode = kalium()->request->has( 'view-maintenance' );
	}

	return ! ! $maintenance_mode;
}
