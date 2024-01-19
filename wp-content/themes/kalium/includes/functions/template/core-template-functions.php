<?php
/**
 * Kalium WordPress Theme
 *
 * Core template functions.
 *
 * @author Laborator
 * @link   https://kaliumtheme.com
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

if ( ! function_exists( 'kalium_logo_element' ) ) {

	/**
	 * Logo element.
	 *
	 * @param int $attachment_id
	 * @param int $max_width
	 */
	function kalium_logo_element( $attachment_id = null, $max_width = null ) {

		// Vars
		$args = [
			'logo_image' => [],
			'logo_name'  => kalium_get_theme_option( 'logo_text' ),
			'link'       => apply_filters( 'kalium_logo_url', home_url() ),
		];

		// Classes
		$classes = [
			'header-logo',
		];

		// Logo vars
		$use_image_logo        = kalium_get_theme_option( 'use_uploaded_logo' );
		$logo_attachment_id    = kalium_get_theme_option( 'custom_logo_image' );
		$logo_max_width        = kalium_get_theme_option( 'custom_logo_max_width' );
		$logo_max_width_mobile = kalium_get_theme_option( 'custom_logo_mobile_max_width' );

		// Get logo from arguments
		if ( $attachment_id && wp_get_attachment_image_src( $attachment_id ) ) {
			$use_image_logo     = true;
			$logo_attachment_id = $attachment_id;
			$logo_max_width     = $max_width;
		}

		// Logo image
		if ( $use_image_logo && ( $logo_image = wp_get_attachment_image_src( $logo_attachment_id, 'full' ) ) ) {

			// Image details
			$image_url    = $logo_image[0];
			$image_width  = $logo_image[1];
			$image_height = $logo_image[2];

			// Logo max width
			if ( is_numeric( $logo_max_width ) && $logo_max_width > 0 ) {
				$resized      = kalium()->helpers->resize_by_width( $image_width, $image_height, $logo_max_width );
				$image_width  = $resized[0];
				$image_height = $resized[1];

				// Resize logo CSS
				kalium_append_custom_css( '.logo-image', sprintf( 'width:%dpx;height:%dpx;', $image_width, $image_height ) );
			}

			// Logo max width on mobile
			if ( is_numeric( $logo_max_width_mobile ) && $logo_max_width_mobile > 0 ) {
				$resized = kalium()->helpers->resize_by_width( $image_width, $image_height, $logo_max_width_mobile );

				// Resize logo CSS
				kalium_append_custom_css( '.logo-image', sprintf( 'width:%dpx;height:%dpx;', $resized[0], $resized[1] ), sprintf( 'screen and (max-width: %dpx)', kalium_get_mobile_menu_breakpoint() ) );
			}

			// Define logo image
			$args['logo_image'] = [
				'src'    => $image_url,
				'width'  => $image_width,
				'height' => $image_height,
			];

			// Add logo image class
			$classes[] = 'logo-image';
		} else {

			// Add logo text class
			$classes[] = 'logo-text';

			// Logo skin
			$classes[] = kalium_get_theme_option( 'custom_header_default_skin' );
		}

		// Pass classes as template argument
		$args['classes'] = $classes;

		// Logo element
		kalium_get_template( 'elements/logo.php', $args );
	}
}

if ( ! function_exists( 'kalium_dynamic_sidebar' ) ) {

	/**
	 * Dynamic sidebar implementation for Kalium.
	 *
	 * @param string       $sidebar_id
	 * @param array|string $class
	 *
	 * @return void
	 */
	function kalium_dynamic_sidebar( $sidebar_id, $class = '' ) {
		$classes = [ 'widget-area' ];

		if ( is_array( $class ) ) {
			$classes = array_merge( $classes, $class );
		} else if ( ! empty( $class ) ) {
			$classes[] = $class;
		}

		?>
        <div <?php kalium_class_attr( apply_filters( 'kalium_widget_area_classes', $classes, $sidebar_id ) ); ?> role="complementary">
			<?php
			// Show sidebar widgets
			dynamic_sidebar( $sidebar_id );
			?>
        </div>
		<?php
	}
}

if ( ! function_exists( 'kalium_social_network_link' ) ) {

	/**
	 * Kalium social network link.
	 *
	 * @param string $social_network
	 * @param array  $args
	 *
	 * @return void
	 */
	function kalium_social_network_link( $social_network, $args = [] ) {

		// Social networks list
		static $social_networks;

		if ( empty( $social_networks ) ) {
			$social_networks = apply_filters( 'kalium_social_network_link_list', [
				'facebook'    => [
					'title' => 'Facebook',
					'icon'  => 'fab fa-facebook',
				],
				'instagram'   => [
					'title' => 'Instagram',
					'icon'  => 'fab fa-instagram',
				],
				'twitter'     => [
					'title' => 'Twitter X',
					'icon'  => 'fab fa-x-twitter',
				],
				'behance'     => [
					'title' => 'Behance',
					'icon'  => 'fab fa-behance',
				],
				'youtube'     => [
					'title' => 'YouTube',
					'icon'  => 'fab fa-youtube',
				],
				'github'      => [
					'title' => 'GitHub',
					'icon'  => 'fab fa-github',
				],
				'linkedin'    => [
					'title' => 'LinkedIn',
					'icon'  => 'fab fa-linkedin',
				],
				'vimeo'       => [
					'title' => 'Vimeo',
					'icon'  => 'fab fa-vimeo',
				],
				'whatsapp'    => [
					'title' => 'WhatsApp',
					'icon'  => 'fab fa-whatsapp',
				],
				'snapchat'    => [
					'title' => 'Snapchat',
					'icon'  => 'fab fa-snapchat-ghost',
				],
				'dribbble'    => [
					'title' => 'Dribbble',
					'icon'  => 'fab fa-dribbble'
				],
				'pinterest'   => [
					'title' => 'Pinterest',
					'icon'  => 'fab fa-pinterest',
				],
				'spotify'     => [
					'title' => 'Spotify',
					'icon'  => 'fab fa-spotify',
				],
				'skype'       => [
					'title' => 'Skype',
					'icon'  => 'fab fa-skype',
				],
				'tumblr'      => [
					'title' => 'Tumblr',
					'icon'  => 'fab fa-tumblr',
				],
				'soundcloud'  => [
					'title' => 'SoundCloud',
					'icon'  => 'fab fa-soundcloud',
				],
				'500px'       => [
					'title' => '500px',
					'icon'  => 'fab fa-500px',
				],
				'xing'        => [
					'title' => 'Xing',
					'icon'  => 'fab fa-xing',
				],
				'email'       => [
					'title' => __( 'Email', 'kalium' ),
					'icon'  => 'far fa-envelope',
				],
				'yelp'        => [
					'title' => 'Yelp',
					'icon'  => 'fab fa-yelp',
				],
				'tripadvisor' => [
					'title' => 'TripAdvisor',
					'icon'  => 'fab fa-tripadvisor',
				],
				'twitch'      => [
					'title' => 'Twitch',
					'icon'  => 'fab fa-twitch',
				],
				'houzz'       => [
					'title' => 'Houzz',
					'icon'  => 'fab fa-houzz',
				],
				'deviantart'  => [
					'title' => 'DeviantArt',
					'icon'  => 'fab fa-deviantart',
				],
				'vkontakte'   => [
					'title' => 'VKontakte',
					'icon'  => 'fab fa-vk',
				],
				'flickr'      => [
					'title' => 'Flickr',
					'icon'  => 'fab fa-flickr',
				],
				'foursquare'  => [
					'title' => 'Foursquare',
					'icon'  => 'fab fa-foursquare',
				],
				'tiktok'      => [
					'title' => 'Tik Tok',
					'icon'  => 'fab fa-tiktok',
				],
			] );
		}

		// Social network link args
		$args = wp_parse_args( $args, [

			// Link
			'link'                   => '',
			'link_target'            => '_blank',

			// Elements to include
			'include_icon'           => true,
			'include_title'          => false,

			// Rounded style
			'rounded'                => false,

			// Style
			'color_text'             => false,
			'color_text_hover'       => false,
			'color_background'       => false,
			'color_background_hover' => false,

			// Other args
			'skin'                   => 'default', // default, light, dark
			'hover_underline'        => false,
		] );

		// Social network entry
		if ( is_array( $social_network ) ) {

			if ( isset( $social_network['id'] ) && isset( $social_network['title'] ) && isset( $social_network['icon'] ) ) {
				$social_network_args = [
					'title' => $social_network['title'],
					'icon'  => $social_network['icon'],
				];
				$social_network      = $social_network['id'];
			} else {
				return;
			}
		} else if ( isset( $social_networks[ $social_network ] ) ) {
			$social_network_args = $social_networks[ $social_network ];
		} else {
			return;
		}

		// Link
		$link = $args['link'];

		// Empty link
		if ( empty( $link ) ) {
			$link = '#';
		}

		// Link classes
		$classes = [
			'social-network-link',
			'sn-' . $social_network,
		];

		// Valid skins
		$skins = [
			// Skins
			'default'         => 'default',
			'dark'            => 'dark',
			'light'           => 'light',

			// Fallback for header skin
			'menu-skin-main'  => 'default',
			'menu-skin-dark'  => 'dark',
			'menu-skin-light' => 'light',
		];

		// Add skin if exists
		if ( isset( $skins[ $args['skin'] ] ) ) {
			$classes[] = 'sn-skin-' . $skins[ $args['skin'] ];
		}

		/**
		 * Config
		 */
		// Icon and title cannot be removed together
		if ( ! $args['include_icon'] && ! $args['include_title'] ) {
			$args['include_icon'] = true;
		}

		// Rounded icon
		if ( $args['rounded'] ) {
			$classes[] = 'sn-rounded';

			// Disable title, include only icon
			$args['include_icon']  = true;
			$args['include_title'] = false;
		}

		// Disable hover underline for icon only
		if ( $args['include_icon'] && ! $args['include_title'] ) {
			$args['hover_underline'] = false;
		}

		// When has color background
		if ( $args['color_background'] || $args['color_background_hover'] ) {
			$classes[] = 'sn-has-color-background';
		}

		// When both icon and title are showing
		if ( $args['include_icon'] && $args['include_title'] ) {
			$classes[] = 'sn-icon-and-title';
		}

		// Hover underline
		if ( $args['hover_underline'] ) {
			$classes[] = 'sn-hover-underline';
		}

		/**
		 * Style
		 */
		// Color text
		if ( $args['color_text'] ) {
			$classes[] = 'sn-style-color-text';
		}

		// Color text on hover
		if ( $args['color_text_hover'] ) {
			$classes[] = 'sn-style-color-text-hover';
		}

		// Color background
		if ( $args['color_background'] ) {
			$classes[] = 'sn-style-color-background';
		}

		// Color background on hover
		if ( $args['color_background_hover'] ) {
			$classes[] = 'sn-style-color-background-hover';
		}

		// Icon classes
		$icon_classes = [
			'sn-column',
			'sn-icon',
			'sn-text',
		];

		// Title classes
		$title_classes = [
			'sn-column',
			'sn-title',
			'sn-text',
		];

		// DOM Element
		?>
        <a href="<?php echo esc_url( $link ); ?>" target="<?php echo esc_attr( $args['link_target'] ); ?>" rel="noopener noreferrer" <?php kalium_class_attr( $classes ); ?>>
			<?php if ( $args['include_icon'] ) : ?>
                <span <?php kalium_class_attr( $icon_classes ); ?>>
                <i class="<?php echo esc_attr( apply_filters( 'kalium_social_network_link_icon', $social_network_args['icon'], $social_network_args ) ); ?>"></i>
                </span>
			<?php endif; ?>

			<?php if ( $args['include_title'] ) : ?>
                <span <?php kalium_class_attr( $title_classes ); ?>>
					<?php echo esc_html( apply_filters( 'kalium_social_network_link_title', $social_network_args['title'], $social_network_args ) ); ?>
                </span>
			<?php endif; ?>
        </a>
		<?php
	}
}

if ( ! function_exists( 'kalium_social_networks' ) ) {

	/**
	 * Show social networks list from theme options.
	 *
	 * @param array $args {
	 *
	 * @type string skin
	 * @type string style
	 * @type string target
	 * @type bool include_icon
	 * @type bool include_title
	 * @type bool rounded
	 * @type bool hover_underline
	 * }
	 */
	function kalium_social_networks( $args = [] ) {

		// Social network args
		$args = wp_parse_args( $args, [
			'skin'            => '',
			'style'           => '',
			'target'          => '_blank',
			'include_icon'    => true,
			'include_title'   => false,
			'rounded'         => false,
			'hover_underline' => false,
		] );

		// Ordered social networks
		$social_networks_list = kalium_get_social_networks_list();

		// List social network links
		if ( ! empty( $social_networks_list ) ) {

			// Classes
			$classes = [
				'social-networks-links',
			];

			echo sprintf( '<ul %s>', kalium_class_attr( $classes, false ) );

			foreach ( $social_networks_list as $social_network => $social_network_args ) {

				// Link URL
				$link_url = $social_network_args['link'];

				// Email link
				if ( ! empty( $social_network_args['data']['is_email'] ) ) {
					$subject  = $social_network_args['data']['email_subject'];
					$link_url = "mailto:{$link_url}";

					// Email subject
					if ( ! empty( $subject ) ) {
						$link_url .= "?subject={$subject}";
					}
				}

				// Phone link
				if ( ! empty( $social_network_args['data']['is_phone'] ) ) {
					$link_url = "tel:{$link_url}";
				}

				// Style configuration
				$style_args = [

					// Style
					'color_text'             => false,
					'color_text_hover'       => false,
					'color_background'       => false,
					'color_background_hover' => false,
				];

				switch ( $args['style'] ) {

					// Colored text
					case 'color-text':
						$style_args['color_text'] = true;
						$args['hover_underline']  = true;
						break;

					// Colored text on hover
					case 'color-text-hover':
						$style_args['color_text_hover'] = true;
						$args['hover_underline']        = true;
						break;

					// Colored background
					case 'color-background':
						$style_args['color_background'] = true;
						break;

					// Colored background on hover
					case 'color-background-hover':
						$style_args['color_background_hover'] = true;
						break;

					// Default
					default:
						if ( $args['include_title'] ) {
							$args['hover_underline'] = true;
						}
				}

				// Social network link args
				$social_network_link_args = [

					// Link
					'link'                   => $link_url,
					'target'                 => $args['target'],

					// Title and icon
					'include_icon'           => $args['include_icon'],
					'include_title'          => $args['include_title'],

					// Rounded icons
					'rounded'                => $args['rounded'],

					// Hover underline
					'hover_underline'        => $args['hover_underline'],

					// Skin
					'skin'                   => $args['skin'],

					// Style
					'color_text'             => $style_args['color_text'],
					'color_text_hover'       => $style_args['color_text_hover'],
					'color_background'       => $style_args['color_background'],
					'color_background_hover' => $style_args['color_background_hover'],
				];

				// Custom social network
				if ( 'custom' === $social_network ) {
					$social_network = [
						'id'    => 'custom',
						'title' => $social_network_args['data']['title'],
						'icon'  => $social_network_args['data']['icon'],
					];
				}

				// Entry wrapper start
				echo sprintf( '<li %s>', kalium_class_attr( [ 'social-networks-links--entry' ], false ) );

				// Social network link
				kalium_social_network_link( $social_network, $social_network_link_args );

				// Entry wrapper end
				echo '</li>';
			}

			echo '</ul>';
		}
	}
}

if ( ! function_exists( 'wp_body_open' ) ) {

	/**
	 * Fire the wp_body_open action, backward compatibility to support pre-5.2.0 WordPress versions.
	 *
	 * @since 3.0
	 */
	function wp_body_open() {
		do_action( 'wp_body_open' );
	}
}

if ( ! function_exists( 'kalium_display_page_borders' ) ) {

	/**
	 * Theme borders.
	 */
	function kalium_display_page_borders() {

		// Theme borders
		if ( kalium_get_theme_option( 'theme_borders' ) ) {
			get_template_part( 'tpls/borders' );
		}
	}
}

if ( ! function_exists( 'kalium_display_footer' ) ) {

	/**
	 * Display theme footer.
	 */
	function kalium_display_footer() {

		// Footer template
		if ( apply_filters( 'kalium_show_footer', true ) ) {
			get_template_part( 'tpls/footer-main' );
		}
	}
}

if ( ! function_exists( 'kalium_page_heading_title_display' ) ) {

	/**
	 * Page heading title display.
	 */
	function kalium_page_heading_title_display() {

		// Queried object
		$queried_object_id = kalium_get_queried_object_id();

		// Do not show on archive pages
		if ( ! is_singular() || ! $queried_object_id || kalium_is_coming_soon_mode() || kalium_is_maintenance_mode() ) {
			return;
		}

		// Show heading title if allowed
		if ( kalium_get_field( 'heading_title', $queried_object_id ) ) {

			// Template args
			$args = [
				'heading_tag' => 'h1',
			];

			// Vars
			$current_post       = get_post( $queried_object_id );
			$title_type         = kalium_get_field( 'page_heading_title_type', $queried_object_id );
			$description_type   = kalium_get_field( 'page_heading_description_type', $queried_object_id );
			$custom_title       = kalium_get_field( 'page_heading_custom_title', $queried_object_id );
			$custom_description = kalium_get_field( 'page_heading_custom_description', $queried_object_id );

			// Sanitize title and description
			$custom_title       = wp_kses_post( $custom_title );
			$custom_description = kalium_format_content( wp_kses_post( $custom_description ) );

			// Set current post
			setup_postdata( $current_post );

			// Inherit from post title
			if ( 'post_title' === $title_type ) {
				$custom_title = apply_filters( 'the_title', get_the_title() );
			}

			// Inherit from post content
			if ( 'post_content' === $description_type ) {
				$custom_description = apply_filters( 'the_content', get_the_content() );
			}

			// Pass as template args
			$args['title']       = $custom_title;
			$args['description'] = $custom_description;

			/* @deprecated 3.1 */
			define( 'HEADING_TITLE_DISPLAYED', true );

			// Reset post data
			wp_reset_postdata();

			// Load page heading template
			kalium_get_template( 'global/page-heading.php', $args );
		}
	}
}

if ( ! function_exists( 'kalium_breadcrumb' ) ) {

	/**
	 * Breadcrumb display.
	 *
	 * @param array $args
	 *
	 * @return string|void
	 * @since 3.2
	 */
	function kalium_breadcrumb( $args = [] ) {
		if ( ! kalium()->is->breadcrumb_navxt_active() ) {
			return;
		}

		// Breadcrumb instance ID
		static $breadcrumb_instance_id = 1;

		// Breadcrumb args
		$args = wp_parse_args( $args, [
			'container'        => true,
			'class'            => '',
			'background_color' => '',
			'text_color'       => '',
			'border_color'     => '',
			'border_type'      => '',
			'text_alignment'   => '',
			'echo'             => true,
		] );

		// Current Object ID
		$object_id = kalium_get_queried_object_id();

		// Breadcrumb classes
		$classes = [
			'breadcrumb',
		];

		// Container classes
		$container_classes = [
			'breadcrumb__container',
		];

		// Style props
		$style_props = [];

		// Style
		$background_color = kalium_get_theme_option( 'breadcrumb_background_color' );
		$text_color       = kalium_get_theme_option( 'breadcrumb_text_color' );
		$border_color     = kalium_get_theme_option( 'breadcrumb_border_color' );
		$border_type      = kalium_get_theme_option( 'breadcrumb_border_type' );
		$border_radius    = kalium_get_theme_option( 'breadcrumb_border_radius' );
		$text_align       = kalium_get_theme_option( 'breadcrumb_alignment' );
		$margin_top       = kalium_get_theme_option( 'breadcrumb_margin_top' );
		$margin_bottom    = kalium_get_theme_option( 'breadcrumb_margin_bottom' );

		// Responsive
		$responsive = array_map( 'kalium_validate_boolean', [
			'desktop' => kalium_get_theme_option( 'breadcrumb_support_desktop', true ),
			'tablet'  => kalium_get_theme_option( 'breadcrumb_support_tablet', true ),
			'mobile'  => kalium_get_theme_option( 'breadcrumb_support_mobile', true ),
		] );

		// Custom breadcrumb parameters for from post meta fields
		if ( is_singular() && 'enable' === kalium_get_field( 'breadcrumb', $object_id ) ) {

			// Custom background color
			if ( $custom_background_color = kalium_get_field( 'breadcrumb_background_color', $object_id ) ) {
				$background_color = $custom_background_color;
			}

			// Custom text color
			if ( $custom_text_color = kalium_get_field( 'breadcrumb_text_color', $object_id ) ) {
				$text_color = $custom_text_color;
			}

			// Custom border color
			if ( $custom_border_color = kalium_get_field( 'breadcrumb_border_color', $object_id ) ) {
				$border_color = $custom_border_color;
			}

			// Custom border type
			if ( ( $custom_border_type = kalium_get_field( 'breadcrumb_border_type', $object_id ) ) && in_array( $custom_border_type, [
					'border',
					'border-horizontal',
				] ) ) {
				$border_type = $custom_border_type;
			}

			// Custom text alignment
			if ( ( $custom_text_align = kalium_get_field( 'breadcrumb_text_alignment', $object_id ) ) && in_array( $custom_text_align, [
					'left',
					'center',
					'right',
				] ) ) {
				$text_align = $custom_text_align;
			}

			// Custom border radius
			$custom_border_radius = kalium_get_field( 'breadcrumb_border_radius', $object_id );

			if ( is_numeric( $custom_border_radius ) ) {
				$border_radius = $custom_border_radius;
			}

			// Custom margin top
			$custom_margin_top = kalium_get_field( 'breadcrumb_margin_top', $object_id );

			if ( is_numeric( $custom_margin_top ) ) {
				$margin_top = $custom_margin_top;
			}

			// Custom margin top
			$custom_margin_bottom = kalium_get_field( 'breadcrumb_margin_bottom', $object_id );

			if ( is_numeric( $custom_margin_bottom ) ) {
				$margin_bottom = $custom_margin_bottom;
			}
		}

		// Background color from $args
		if ( ! empty( $args['background_color'] ) ) {
			$background_color = $args['background_color'];
		}

		// Text color from $args
		if ( ! empty( $args['text_color'] ) ) {
			$text_color = $args['text_color'];
		}

		// Border color from $args
		if ( ! empty( $args['border_color'] ) ) {
			$border_color = $args['border_color'];
		}

		// Border type from $args
		if ( ! empty( $args['border_type'] ) && in_array( $args['border_type'], [
				'border',
				'border-horizontal',
			] ) ) {
			$border_type = $args['border_type'];
		}

		// Text alignment from $args
		if ( ! empty( $args['text_alignment'] ) && in_array( $args['text_alignment'], [
				'left',
				'center',
				'right',
			] ) ) {
			$text_align = $args['text_alignment'];
		}

		// Style: Background
		if ( $background_color ) {
			$container_classes[] = 'breadcrumb__container--has-background';
			$container_classes[] = 'breadcrumb__container--has-padding';

			$style_props['background-color'] = $background_color;
		}

		// Style: Border Radius
		if ( is_numeric( $border_radius ) ) {
			$style_props['border-radius'] = $border_radius . 'px';
		}

		// Style: Border
		if ( 'border-horizontal' === $border_type ) {
			$container_classes[] = 'breadcrumb__container--border-horizontal';
			$container_classes[] = 'breadcrumb__container--has-padding-horizontal';

			$style_props['border-color'] = $border_color;
			unset( $style_props['border-radius'] );
		} else if ( 'border' === $border_type ) {
			$container_classes[] = 'breadcrumb__container--border';
			$container_classes[] = 'breadcrumb__container--has-padding';

			$style_props['border-color'] = $border_color;
		}

		// Style: Text
		if ( $text_color ) {
			$container_classes[] = 'breadcrumb__container--has-text-color';

			$style_props['color'] = $text_color;
		}

		// Style: Text alignment
		if ( in_array( $text_align, [ 'left', 'center', 'right' ] ) ) {
			$container_classes[] = 'breadcrumb__container--align-' . $text_align;
		}

		// Style: Margin Top
		if ( is_numeric( $margin_top ) ) {
			$style_props['margin-top'] = $margin_top . 'px';
		}

		// Style: Margin Bottom
		if ( is_numeric( $margin_bottom ) ) {
			$classes[] = 'breadcrumb--no-bottom-margin';

			$style_props['margin-bottom'] = $margin_bottom . 'px';
		}

		// Responsive settings
		if ( ! $responsive['desktop'] ) {
			$classes[] = 'breadcrumb--hide-on-desktop';
		}

		if ( ! $responsive['tablet'] ) {
			$classes[] = 'breadcrumb--hide-on-tablet';
		}

		if ( ! $responsive['mobile'] ) {
			$classes[] = 'breadcrumb--hide-on-mobile';
		}

		// Selector
		$selector = 'breadcrumb-' . $breadcrumb_instance_id;

		// Breadcrumb ID
		$classes[] = $selector;

		// Extra classes
		if ( ! empty( $args['class'] ) ) {
			$extra_classes = $args['class'];

			if ( is_string( $extra_classes ) ) {
				$extra_classes = explode( ' ', $extra_classes );
			}

			$classes = array_merge( $classes, $extra_classes );
		}

		// Breadcrumb trail
		$breadcrumb_html = bcn_display( true );

		if ( ! $breadcrumb_html ) {
			return;
		}

		// Template args
		$template_args = [
			'classes'           => $classes,
			'container'         => $args['container'],
			'container_classes' => array_unique( $container_classes ),
			'breadcrumb_html'   => $breadcrumb_html,
		];

		// Buffer the output
		ob_start();

		// Load template
		kalium_get_template( 'global/breadcrumb.php', $template_args );

		// Style
		if ( ! empty( $style_props ) ) {
			kalium_append_custom_css( ".{$selector} .breadcrumb__container", $style_props );
		}

		// Output
		$output = ob_get_clean();

		// Increment instance Id
		$breadcrumb_instance_id ++;

		// Print the output
		if ( $args['echo'] ) {
			echo $output;

			return;
		}

		return $output;
	}
}

if ( ! function_exists( 'kalium_adjacent_post_link' ) ) {

	/**
	 * Adjacent post link.
	 *
	 * @param array       $args    {
	 *
	 * @type string       $prev_text
	 * @type string       $next_text
	 * @type string       $secondary_text
	 * @type bool         $prev
	 * @type bool         $next
	 * @type bool         $in_same_term
	 * @type string|array $excluded_terms
	 * @type string       $taxonomy
	 * @type string       $display Accepted values: 'title' or empty
	 * @type bool         $post_object
	 * @type string       $arrow   Arrow direction. Accepted values: 'left', 'right'
	 * @type bool         $echo
	 * }
	 *
	 * @return string|WP_Post|null|void
	 *
	 * @since 3.2
	 */
	function kalium_adjacent_post_link( $args = [] ) {
		$args = wp_parse_args( $args, [

			// Labels
			'prev_text'      => '',
			'next_text'      => '',
			'secondary_text' => '',

			// Direction
			'prev'           => false,
			'next'           => true,

			// Adjacent post args
			'in_same_term'   => false,
			'excluded_terms' => '',
			'taxonomy'       => 'category',

			// Other options
			'display'        => '',
			'post_object'    => false,
			'arrow'          => '',
			'echo'           => true,
		] );

		/**
		 * Filter adjacent post link args to allow third-party users to apply their own configuration.
		 *
		 * @param array $args
		 */
		$args = apply_filters( 'kalium_adjacent_post_link_args', $args );

		// Labels
		$prev_text = __( 'Previous', 'kalium' );
		$next_text = __( 'Next', 'kalium' );

		// Custom previous text
		if ( ! empty( $args['prev_text'] ) ) {
			$prev_text = $args['prev_text'];
		}

		// Custom next text
		if ( ! empty( $args['next_text'] ) ) {
			$next_text = $args['next_text'];
		}

		// Direction
		$is_prev = $args['prev'] || ! $args['next'];

		// Post object
		$post = get_adjacent_post( $args['in_same_term'], $args['excluded_terms'], $is_prev, $args['taxonomy'] );

		// Custom post object defined in Parameters & Options
		if ( kalium_get_field( 'custom_prevnext_links' ) && ( $custom_post = kalium_get_field( kalium_conditional( $is_prev, 'prevnext_previous_id', 'prevnext_next_id' ) ) ) instanceof WP_Post ) {
			$post = $custom_post;
		}

		/**
		 * Filter post object to allow third-party users to modify it.
		 *
		 * @param WP_Post|null $post
		 * @param bool         $is_prev
		 * @param array        $args
		 */
		$post = apply_filters( 'kalium_adjacent_post_object', $post, $is_prev, $args );

		// Return post object if requested
		if ( $args['post_object'] ) {
			return $post;
		}

		// Link and text
		$adjacent_post_link = '#';
		$adjacent_post_text = kalium_conditional( $is_prev, $prev_text, $next_text );

		// Adjacent post exists
		if ( $post instanceof WP_Post ) {

			// Adjacent post link
			$adjacent_post_link = get_permalink( $post );
		}

		// Display titles
		if ( 'title' === $args['display'] ) {

			if ( $post instanceof WP_Post ) {
				$adjacent_post_text = get_the_title( $post );
			} else {
				return null;
			}
		}

		// Classes
		$classes = [
			'adjacent-post-link',
			'adjacent-post-link--' . kalium_conditional( $is_prev, 'prev', 'next' ),
		];

		// When no link is present
		if ( '#' === $adjacent_post_link ) {
			$classes[] = 'disabled';
		}

		// Icon
		$icon       = false;
		$icon_class = '';

		if ( 'left' === $args['arrow'] ) {
			$icon       = true;
			$icon_class = 'flaticon-arrow427';

			$classes[] = 'adjacent-post-link--has-icon';
			$classes[] = 'adjacent-post-link--arrow-left';
		} else if ( 'right' === $args['arrow'] ) {
			$icon       = true;
			$icon_class = 'flaticon-arrow413';

			$classes[] = 'adjacent-post-link--has-icon';
			$classes[] = 'adjacent-post-link--arrow-right';
		}

		// Template args
		$template_args = [
			'classes'            => $classes,
			'adjacent_post_link' => $adjacent_post_link,
			'adjacent_post_text' => $adjacent_post_text,
			'icon'               => $icon,
			'icon_class'         => $icon_class,
			'secondary_text'     => $args['secondary_text'],
		];

		// Template output
		ob_start();
		kalium_get_template( 'global/adjacent-link.php', $template_args );
		$output = ob_get_clean();

		// Echo output
		if ( $args['echo'] ) {
			echo $output;

			return;
		}

		return $output;
	}
}
