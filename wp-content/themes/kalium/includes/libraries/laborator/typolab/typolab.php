<?php
/**
 * TypoLab - ultimate font management library.
 *
 * Main Typolab class.
 *
 * @author  Laborator
 * @link    https://laborator.co
 * @version 2.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class TypoLab {

	/**
	 * Version.
	 *
	 * @const string
	 */
	const VERSION = '2.0';

	/**
	 * Option name where TypoLab data are stored.
	 *
	 * @const string
	 */
	const OPTION_NAME = 'typolab_fonts';

	/**
	 * TypoLab status.
	 *
	 * @var bool
	 */
	public static $enabled = true;

	/**
	 * TypoLab directory.
	 *
	 * @var string
	 */
	public static $typolab_dir;

	/**
	 * Fonts directory.
	 *
	 * @var string
	 */
	public static $fonts_dir;

	/**
	 * Fonts directory URL.
	 *
	 * @var string
	 */
	public static $fonts_url;

	/**
	 * Assets directory URL of TypoLab.
	 *
	 * @var string
	 */
	public static $typolab_assets_url;

	/**
	 * Font Preview String.
	 *
	 * @var string
	 */
	public static $font_preview_str = 'Almost before we knew it, we had left the ground.';

	/**
	 * Font Preview Size.
	 *
	 * @var int
	 */
	public static $font_preview_size = 16;

	/**
	 * Default font import code placement.
	 *
	 * @var string
	 */
	public static $font_placement = 'head';

	/**
	 * Font preloading.
	 *
	 * @var bool
	 */
	public static $font_preload = true;

	/**
	 * Pull Google fonts.
	 *
	 * @var bool
	 */
	public static $pull_google_fonts = false;

	/**
	 * Font display.
	 *
	 * @var string
	 */
	public static $font_display = 'swap';

	/**
	 * Default unit.
	 *
	 * @var string
	 */
	public static $default_unit = 'px';

	/**
	 * Adobe fonts API token.
	 *
	 * @var string
	 */
	public static $adobe_fonts_api_token;

	/**
	 * Current editing font.
	 *
	 * @var TypoLab_Font
	 */
	public static $current_font;

	/**
	 * Page title.
	 *
	 * @var string
	 */
	public static $page_title;

	/**
	 * Page subtitle.
	 *
	 * @var string
	 */
	public static $page_sub_title;

	/**
	 * Instance of TypoLab.
	 *
	 * @var self
	 */
	private static $instance;

	/**
	 * Create TypoLab instance.
	 *
	 * @return self
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Get option or all options.
	 *
	 * @param string $var
	 * @param string $default
	 *
	 * @return array|mixed
	 */
	public static function get_option( $var = null, $default = '' ) {
		$typolab_settings = get_option( self::OPTION_NAME, [] );

		// Get all settings
		if ( is_null( $var ) ) {
			return $typolab_settings;
		}

		// Get Single Var
		if ( isset( $typolab_settings[ $var ] ) ) {
			return $typolab_settings[ $var ];
		}

		return $default;
	}

	/**
	 * Save Variable in Settings Array.
	 *
	 * @param string $var
	 * @param mixed  $value
	 */
	public static function set_option( $var, $value = '' ) {
		$settings = self::get_option();

		$settings[ $var ] = $value;

		update_option( self::OPTION_NAME, $settings );
	}

	/**
	 * Check if TypoLab is enabled.
	 *
	 * @return bool
	 */
	public static function is_enabled() {
		return self::$enabled;
	}

	/**
	 * Get Adobe fonts API token.
	 *
	 * @return string
	 */
	public static function get_adobe_fonts_api_token() {
		return self::$adobe_fonts_api_token;
	}

	/**
	 * Check if current page is typolab admin page.
	 *
	 * @return bool
	 */
	public static function is_typolab_admin_page() {
		return is_admin() && 'typolab' === kalium()->request->query( 'page' );
	}

	/**
	 * Get registered fonts as TypoLab font objects.
	 *
	 * @param array $args
	 *
	 * @return TypoLab_Font[]
	 * @since 3.3.2
	 */
	public static function get_fonts( $args = [] ) {
		$args = wp_parse_args( $args, [
			'status'  => 'any', // values: any,active,inactive
			'orderby' => 'source-name', // values: name,source,date,source-name
			'order'   => 'asc', // values: asc,desc
		] );

		// Fonts list
		$fonts        = self::get_option( 'registered_fonts', [] );
		$fonts_list   = [];
		$font_sources = array_keys( TypoLab_Data::get_font_sources() );

		// Filter fonts
		foreach ( $fonts as $font_arr ) {
			$font = TypoLab_Font::create_instance( $font_arr );

			// Invalid font
			if ( ! $font ) {
				continue;
			}

			// Skip fonts of unknown source or without font family name
			if ( ! in_array( $font->get_source(), $font_sources ) || ! $font->get_family_name() ) {
				continue;
			}

			// Status match
			if ( 'active' === $args['status'] && ! $font->is_active() || 'inactive' === $args['status'] && $font->is_active() ) {
				continue;
			}

			$fonts_list[] = $font;
		}

		// Sort
		$orderby = $args['orderby'];
		$order   = strtolower( $args['order'] );

		usort( $fonts_list, function ( $a, $b ) use ( $orderby, $order, $font_sources ) {
			/** @var TypoLab_Font $a */
			/** @var TypoLab_Font $b */

			$order = 'desc' === $order ? - 1 : 1;

			// Order by date default
			$index = $a->get_created_time() - $b->get_created_time();

			// Order by name
			if ( 'name' === $orderby ) {
				$index = strcasecmp( $a->get_family_name(), $b->get_family_name() );
			} // Order by source
			else if ( 'source' === $orderby || 'source-name' === $orderby ) {
				$index = array_search( $a->get_source(), $font_sources ) - array_search( $b->get_source(), $font_sources );
			}

			// Sort by source and name
			if ( 'source-name' === $orderby && $a->get_source() === $b->get_source() ) {
				$index = strcasecmp( $a->get_family_name(), $b->get_family_name() );
			}

			// Do nothing
			if ( 0 === $index ) {
				return $index;
			}

			return $index * $order;
		} );

		return $fonts_list;
	}

	/**
	 * Get font by id.
	 *
	 * @return TypoLab_Font|TypoLab_Google_Font|TypoLab_Hosted_Google_Font|TypoLab_Font_Squirrel_Font|TypoLab_Laborator_Font|TypoLab_Adobe_Font|TypoLab_Hosted_Font|TypoLab_External_Font|null
	 */
	public static function get_font( $id ) {
		$fonts = self::get_option( 'registered_fonts', [] );

		foreach ( $fonts as $font ) {
			if ( $id === kalium_get_array_key( $font, 'id' ) ) {
				return TypoLab_Font::create_instance( $font );
			}
		}

		return null;
	}

	/**
	 * Delete font bu id.
	 *
	 * @return bool
	 */
	public static function delete_font( $id ) {
		$font = self::get_font( $id );

		if ( $font ) {
			$font->delete();

			return true;
		}

		return false;
	}

	/**
	 * Get current editing font.
	 *
	 * @return TypoLab_Font|TypoLab_Google_Font|TypoLab_Hosted_Google_Font|TypoLab_Font_Squirrel_Font|TypoLab_Laborator_Font|TypoLab_Adobe_Font|TypoLab_Hosted_Font|TypoLab_External_Font
	 */
	public static function get_current_font() {
		return self::$current_font;
	}

	/**
	 * Get Font Settings.
	 *
	 * @return array
	 */
	public static function get_font_settings() {
		return self::get_option( 'font_settings', [] );
	}

	/**
	 * Generate Unique Font ID.
	 *
	 * @return string
	 */
	public static function new_font_id() {
		$new_id  = self::get_option( 'id_iterator', 1 );
		$font_id = sprintf( 'font-%s', $new_id );

		// Increment ID iterator
		self::set_option( 'id_iterator', $new_id + 1 );

		// Unique ID
		if ( self::get_font( $font_id ) ) {
			return self::new_font_id();
		}

		return $font_id;
	}

	/**
	 * Get font action link.
	 *
	 * @param string              $action
	 * @param string|TypoLab_Font $font
	 *
	 * @return string
	 */
	public static function get_font_action_link( $action, $font ) {
		$font_id = $font instanceof TypoLab_Font ? $font->get_id() : $font;
		$url     = admin_url( sprintf( 'admin.php?page=%s&typolab-action=%s&font-id=%s', kalium()->request->query( 'page' ), $action, $font_id ) );

		// Orderby
		if ( $orderby = kalium()->request->query( 'orderby' ) ) {
			$url = add_query_arg( 'orderby', $orderby, $url );
		}

		// Order
		if ( $order = kalium()->request->query( 'order' ) ) {
			$url = add_query_arg( 'order', $order, $url );
		}

		// Nonce links
		if ( in_array( $action, [ 'delete-font', 'activate-font', 'deactivate-font', 'install-font' ] ) ) {
			$url = wp_nonce_url( $url, $action );
		}

		return $url;
	}

	/**
	 * Add new font URL.
	 *
	 * @return string
	 */
	public static function add_new_font_url() {
		return admin_url( sprintf( 'admin.php?page=%s&typolab-action=add-font', kalium()->request->query( 'page' ) ) );
	}

	/**
	 * Redirect and show notice once.
	 * Will remove 'typolab-action', 'font-id', '_wpnonce' parameters from URL query arguments.
	 *
	 * @param string $notice_message
	 * @param string $notice_type
	 *
	 * @uses exit
	 */
	public static function redirect_notice( $notice_message = '', $notice_type = '' ) {
		if ( ! is_admin() ) {
			return;
		}

		if ( $notice_message ) {
			self::set_option( 'show_notice_once', [
				'type'      => $notice_type,
				'message'   => $notice_message,
				'timestamp' => time(),
			] );
		}

		wp_redirect( remove_query_arg( [ 'typolab-action', 'font-id', '_wpnonce' ] ) );
		exit;
	}

	/**
	 * Font preview (single variant).
	 *
	 * @param TypoLab_Font|TypoLab_Google_Font|TypoLab_Hosted_Google_Font|TypoLab_Font_Squirrel_Font|TypoLab_Laborator_Font|TypoLab_Adobe_Font|TypoLab_Hosted_Font|TypoLab_External_Font $font
	 *
	 * @return string
	 */
	public static function preview( $font ) {
		$output = '';

		// Only fonts are supported
		if ( ! $font instanceof TypoLab_Font ) {
			return $output;
		}

		// Vars
		$stylesheet_url  = $font->get_stylesheet_url();
		$default_variant = $font->get_default_variant();

		// Stylesheet fonts
		if ( $stylesheet_url ) {
			$output .= sprintf( '<link rel="stylesheet" href="%s" media="print" onload="%s">', esc_attr( $stylesheet_url ), "this.media='all'" );
		} // Inline @font-face
		else if ( $default_variant ) {
			$output .= sprintf( '<style>%s</style>', $default_variant->generate_font_face() );
		}

		// Variant preview CSS
		$variant_preview_css = new TypoLab_CSS_Generator( '.font-' . $font->get_id() );
		$variant_preview_css->add_props( $default_variant ? $default_variant->get_css_props() : null );
		$variant_preview_css->add_prop( 'font-size', self::$font_preview_size . 'px' );

		// Variant preview class
		$output .= sprintf( '<style>%s</style>', $variant_preview_css );

		// Preview element
		$output .= sprintf( '<span class="font-preview font-%s">%s</span>', $font->get_id(), wp_kses_post( TypoLab::$font_preview_str ) );

		return $output;
	}

	/**
	 * Initialize TypoLab.
	 *
	 * @return void
	 */
	public function __construct() {

		// Uploads dir
		$uploads = wp_upload_dir();

		// TypoLab Path
		self::$typolab_dir = __DIR__;

		// TypoLab Assets URL
		self::$typolab_assets_url = kalium()->locate_file_url( 'includes/libraries/laborator/typolab/assets' );

		// Fonts directory
		self::$fonts_dir = $uploads['basedir'] . '/typolab-fonts/';

		// Fonts directory URL
		self::$fonts_url = $uploads['baseurl'] . '/typolab-fonts/';

		// Utils
		require_once( __DIR__ . '/includes/classes/utils/typolab-helper.php' );
		require_once( __DIR__ . '/includes/classes/utils/typolab-ui-components.php' );
		require_once( __DIR__ . '/includes/classes/utils/typolab-css-generator.php' );
		require_once( __DIR__ . '/includes/classes/utils/typolab-legacy-migration.php' );

		// Core Classes
		require_once( __DIR__ . '/includes/classes/typolab-data.php' );
		require_once( __DIR__ . '/includes/classes/typolab-css-selectors.php' );
		require_once( __DIR__ . '/includes/classes/typolab-exportable.php' );
		require_once( __DIR__ . '/includes/classes/typolab-installable-font.php' );
		require_once( __DIR__ . '/includes/classes/typolab-font-appearance-settings.php' );
		require_once( __DIR__ . '/includes/classes/typolab-font-appearance-element.php' );
		require_once( __DIR__ . '/includes/classes/typolab-font-installer.php' );
		require_once( __DIR__ . '/includes/classes/typolab-font-export-import.php' );
		require_once( __DIR__ . '/includes/classes/typolab-font-assets.php' );
		require_once( __DIR__ . '/includes/classes/typolab-font-preload.php' );
		require_once( __DIR__ . '/includes/classes/typolab-responsive-value.php' );
		require_once( __DIR__ . '/includes/classes/typolab-font-loader.php' );

		// Font Providers
		require_once( __DIR__ . '/includes/classes/font-providers/typolab-google-fonts-provider.php' );
		require_once( __DIR__ . '/includes/classes/font-providers/typolab-font-squirrel-provider.php' );
		require_once( __DIR__ . '/includes/classes/font-providers/typolab-laborator-fonts-provider.php' );
		require_once( __DIR__ . '/includes/classes/font-providers/typolab-hosted-fonts-provider.php' );
		require_once( __DIR__ . '/includes/classes/font-providers/typolab-adobe-fonts-provider.php' );
		require_once( __DIR__ . '/includes/classes/font-providers/typolab-external-fonts-provider.php' );
		require_once( __DIR__ . '/includes/classes/font-providers/typolab-system-fonts-provider.php' );

		// Font Types
		require_once( __DIR__ . '/includes/classes/font-types/typolab-font.php' );
		require_once( __DIR__ . '/includes/classes/font-types/typolab-google-font.php' );
		require_once( __DIR__ . '/includes/classes/font-types/typolab-hosted-google-font.php' );
		require_once( __DIR__ . '/includes/classes/font-types/typolab-font-squirrel-font.php' );
		require_once( __DIR__ . '/includes/classes/font-types/typolab-laborator-font.php' );
		require_once( __DIR__ . '/includes/classes/font-types/typolab-hosted-font.php' );
		require_once( __DIR__ . '/includes/classes/font-types/typolab-external-font.php' );
		require_once( __DIR__ . '/includes/classes/font-types/typolab-adobe-font.php' );
		require_once( __DIR__ . '/includes/classes/font-types/typolab-system-font.php' );

		// Font Components
		require_once( __DIR__ . '/includes/classes/font-components/typolab-font-variant.php' );
		require_once( __DIR__ . '/includes/classes/font-components/typolab-font-selector.php' );
		require_once( __DIR__ . '/includes/classes/font-components/typolab-font-load-condition.php' );
		require_once( __DIR__ . '/includes/classes/font-components/typolab-font-custom-selector.php' );

		// Fonts list table (load on admin only)
		if ( self::is_typolab_admin_page() ) {
			require_once( __DIR__ . '/includes/classes/typolab-fonts-list-table.php' );
		}

		// Init hooks
		add_action( 'init', [ $this, 'init' ] );
		add_action( 'admin_init', [ $this, 'admin_init' ] );
		add_action( 'admin_menu', [ $this, 'add_menu_item' ] );

		// Install and preload legacy fonts
		add_action( 'init', [ 'TypoLab_Legacy_Migration', 'legacy_install_fonts' ] );
		add_action( 'init', [ 'TypoLab_Legacy_Migration', 'legacy_preload_fonts' ] );

		// Font-end font loader
		add_action( 'wp_enqueue_scripts', [ 'TypoLab_Font_Loader', 'load_fonts' ], 1000 );

		// Export/import
		add_action( 'wp_ajax_typolab_export', [ 'TypoLab_Font_Export_Import', 'export_ajax' ] );
		add_action( 'wp_ajax_typolab_import', [ 'TypoLab_Font_Export_Import', 'import_ajax' ] );

		// Allow font file types
		add_filter( 'upload_mimes', [ $this, 'upload_mime_types' ] );
		add_filter( 'wp_check_filetype_and_ext', [ $this, 'update_inconsistent_mime_types' ], 10, 3 );

		// Reload Adobe Fonts
		add_action( 'wp_ajax_typolab_reload_adobe_fonts', [ $this, 'reload_adobe_fonts' ] );

		// Safe stylesheet loader
		add_action( 'wp_ajax_typolab-safe-stylesheet', [ $this, 'load_safe_stylesheet' ] );
	}

	/**
	 * Init.
	 */
	public function init() {

		// Font Settings
		$font_settings = self::get_font_settings();

		// TypoLab status
		self::$enabled = kalium_get_array_key( $font_settings, 'typolab_enabled', self::$enabled );

		// Font preload
		self::$font_preload = kalium_get_array_key( $font_settings, 'font_preload', self::$font_preload );

		// Pull Google fonts
		self::$pull_google_fonts = kalium_get_array_key( $font_settings, 'pull_google_fonts', self::$pull_google_fonts );

		// Font placement
		self::$font_placement = kalium_get_array_key( $font_settings, 'font_placement', self::$font_placement );

		// Font display
		self::$font_display = kalium_get_array_key( $font_settings, 'font_display', self::$font_display );

		// Adobe fonts API token
		self::$adobe_fonts_api_token = kalium_get_array_key( $font_settings, 'adobe_fonts_api_token', self::$adobe_fonts_api_token );

		// Default unit
		self::$default_unit = kalium_get_array_key( $font_settings, 'default_unit', self::$default_unit );

		// Font preview text
		self::$font_preview_str = kalium_get_array_key( $font_settings, 'font_preview_str', self::$font_preview_str );

		// Font preview size
		self::$font_preview_size = kalium_get_array_key( $font_settings, 'font_preview_size', self::$font_preview_size );
	}

	/**
	 * Init TypoLab and set it up.
	 */
	public function admin_init() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// Register scripts and styles
		wp_register_style( 'typolab-select2', self::$typolab_assets_url . '/js/select2/select2.min.css', null, self::VERSION );
		wp_register_script( 'typolab-select2', self::$typolab_assets_url . '/js/select2/select2.full.min.js', null, self::VERSION );

		wp_register_style( 'typolab', self::$typolab_assets_url . '/css/typolab.min.css', [ 'wp-components' ], self::VERSION );
		wp_register_script( 'typolab', self::$typolab_assets_url . '/js/typolab.min.js', [
			'wp-util',
			'underscore',
			'backbone',
		], self::VERSION, true );

		// Process actions
		if ( self::is_typolab_admin_page() ) {
			$this->process_actions();
		}
	}

	/**
	 * TypoLab admin page template.
	 *
	 * @return void
	 */
	public function page_template() {
		$font   = self::get_current_font();
		$page   = kalium()->request->query( 'typolab-page' );
		$action = kalium()->request->query( 'typolab-action' );

		// Default page title
		TypoLab::$page_title     = 'Installed Fonts';
		TypoLab::$page_sub_title = 'Manage and preview installed fonts';

		// Page template
		$page_template = 'installed-fonts';

		// Font edit
		if ( $font ) {
			TypoLab::$page_title     = 'Edit Font';
			TypoLab::$page_sub_title = 'Source: ' . $font->get_source_title() . '<span class="separator"></span><a href="#" class="change-source">Change Source</a>';

			// Editing selected font
			if ( $font->get_family_name() ) {
				TypoLab::$page_title .= sprintf( ': "%s"', $font->get_title() );
			}

			$page_template = 'edit-font';
		}

		// Add new font
		if ( 'add-font' === $action ) {
			TypoLab::$page_title = 'Add New Font';
			$page_template       = 'add-font';
		}

		// Settings page
		if ( 'settings' === $page ) {
			TypoLab::$page_title     = 'Font Settings';
			TypoLab::$page_sub_title = 'Configure how fonts work on your site';
			$page_template           = 'font-settings';
		}

		// Fonts appearance page
		if ( 'fonts-appearance' === $page ) {
			TypoLab::$page_title     = 'Fonts Appearance';
			TypoLab::$page_sub_title = 'Customize font sizes and their appearance';
			$page_template           = 'fonts-appearance';
		}

		// If there are no fonts on Installed Fonts page, hide page title
		if ( 'installed-fonts' === $page_template && ! count( TypoLab::get_fonts() ) ) {
			TypoLab::$page_title = null;
		}

		// Template parts
		$template_parts = [
			'heading',
			'tabs',
			'title',
			"pages/{$page_template}",
			'footer',
			'templates',
		];

		// Wrapper start
		echo '<div id="typolab-wrapper" class="wrap typolab">';

		// Load template parts
		foreach ( $template_parts as $template_part ) {
			$template_file = TypoLab_Helper::get_template_path( $template_part . '.php' );
			require_once( $template_file );
		}

		// Wrapper end
		echo '</div>';
	}

	/**
	 * Typography menu item.
	 *
	 * @return void
	 */
	public function add_menu_item() {
		add_submenu_page( 'laborator_options', 'Typography', 'Typography', 'edit_theme_options', 'typolab', [
			$this,
			'page_template'
		] );
	}

	/**
	 * Add font file types to allowed mimes.
	 *
	 * @param array $mime_types
	 *
	 * @return array
	 */
	public function upload_mime_types( $mime_types ) {
		$mime_types['woff']  = 'application/x-font-woff';
		$mime_types['woff2'] = 'application/x-font-woff2';
		$mime_types['ttf']   = 'application/x-font-ttf';
		$mime_types['svg']   = 'image/svg+xml';
		$mime_types['eot']   = 'application/vnd.ms-fontobject';
		$mime_types['otf']   = 'font/otf';

		return $mime_types;
	}

	/**
	 * A workaround for upload validation which relies on a PHP extension (fileinfo) with inconsistent reporting behaviour.
	 *
	 * @param array  $filetype_and_ext
	 * @param string $file
	 * @param string $filename
	 *
	 * @return array
	 */
	public function update_inconsistent_mime_types( $filetype_and_ext, $file, $filename ) {
		if ( 'ttf' === pathinfo( $filename, PATHINFO_EXTENSION ) ) {
			$filetype_and_ext['type'] = 'application/x-font-ttf';
			$filetype_and_ext['ext']  = 'ttf';
		}

		if ( 'otf' === pathinfo( $filename, PATHINFO_EXTENSION ) ) {
			$filetype_and_ext['type'] = 'application/x-font-otf';
			$filetype_and_ext['ext']  = 'otf';
		}

		return $filetype_and_ext;
	}

	/**
	 * Reload Adobe Fonts list.
	 */
	public function reload_adobe_fonts() {
		TypoLab_Adobe_Fonts_Provider::reset_fonts_cache();
		$fonts = TypoLab_Adobe_Fonts_Provider::get_fonts();

		if ( is_wp_error( $fonts ) ) {
			wp_send_json_error( $fonts->get_error_message() );
		}

		wp_send_json_success( $fonts );
	}

	/**
	 * Safe stylesheet loader.
	 */
	public function load_safe_stylesheet() {
		$font_id = kalium()->request->query( 'font-id' );
		$font    = self::get_font( $font_id );

		if ( $font instanceof TypoLab_Font && ( $stylesheet_url = $font->get_stylesheet_url() ) ) {
			$request = wp_remote_get( $stylesheet_url, [
				'headers' => [
					'User-Agent' => $_SERVER['HTTP_USER_AGENT'],
					'Referer'    => home_url(),
				],
			] );

			// When request is allowed
			if ( 200 === wp_remote_retrieve_response_code( $request ) ) {
				$stylesheet = wp_remote_retrieve_body( $request );

				// Parse only @font-faces
				if ( preg_match_all( '/@font-face\s*{.*?}/s', $stylesheet, $matches ) ) {
					$stylesheet_safe = implode( "\n\n", $matches[0] );
				}

				// Set content type
				header( 'Content-Type: text/css' );

				// Print font faces (if available)
				if ( isset( $stylesheet_safe ) ) {
					echo $stylesheet_safe;
				}
			} else {

				// Otherwise redirect
				wp_redirect( $stylesheet_url, 301 );
			}

			die();
		}
	}

	/**
	 * Process actions on TypoLab page.
	 */
	private function process_actions() {
		$typolab_page = kalium()->request->query( 'typolab-page' );
		$font_id      = kalium()->request->query( 'font-id' );
		$font_source  = kalium()->request->query( 'font-source' );

		// Enqueues
		wp_enqueue_style( 'typolab' );
		wp_enqueue_script( 'typolab' );

		// Other enqueues
		kalium_enqueue( 'tooltipster' );

		// Current font
		$current_font = $font_id ? self::get_font( $font_id ) : null;

		// Execute certain actions for current font
		if ( $current_font ) {

			// Activate font
			if ( $this->verify_current_action( 'activate-font' ) ) {
				$current_font->set_active( true );
				$current_font->save();
				self::redirect_notice( 'Font activated.', 'success' );
			}

			// Deactivate font
			if ( $this->verify_current_action( 'deactivate-font' ) ) {
				$current_font->set_active( false );
				$current_font->save();
				self::redirect_notice( 'Font deactivated.', 'success' );
			}

			// Install font
			if ( $this->verify_current_action( 'install-font' ) ) {

				// Only if font is "installable"
				if ( $current_font instanceof TypoLab_Installable_Font ) {
					$result = $current_font->install();

					if ( is_wp_error( $result ) ) {
						self::redirect_notice( $result->get_error_message(), 'warning' );
					} else {

						// Preload
						if ( $current_font->supports_preload() && $current_font->do_preload() ) {
							$current_font->preload();
						}

						// Save font
						$current_font->save();
						self::redirect_notice( 'Font reinstalled successfully.', 'success' );
					}
				}
			}

			// Delete font
			if ( $this->verify_current_action( 'delete-font' ) ) {
				$deleted = $current_font->delete();

				// Deleted or not
				if ( is_wp_error( $deleted ) ) {
					self::redirect_notice( $deleted, 'error' );
				} elseif ( $deleted ) {
					self::redirect_notice( 'Font deleted.', 'success' );
				} else {
					self::redirect_notice( 'Font could not be deleted.', 'warning' );
				}
			}
		}

		// Edit font
		if ( 'edit-font' === $this->current_action() ) {

			// Create new font
			if ( ! $current_font ) {
				$new_font = TypoLab_Font::create_instance( [
					'id'     => $font_id,
					'source' => $font_source,
				] );

				// Only valid font-source accepted
				if ( $font_source === $new_font->get_source() ) {
					$current_font = $new_font;
				}
			} // Change font source
			else if ( $font_source ) {
				$GLOBALS['previous_font_source'] = $current_font->get_source();
				$GLOBALS['new_font_source']      = $font_source;

				$switched_source = $current_font->get_source() !== $font_source;
				$current_font    = $current_font->switch_source( $font_source );

				if ( $switched_source ) {
					add_action( 'typolab_edit_font_form', [ 'TypoLab_UI_Components', 'switched_font_source_notice' ] );
				}
			}

			// Editing font
			if ( $current_font ) {

				// Adobe Fonts API Token warning
				if ( 'adobe' === $current_font->get_source() ) {
					$fonts = TypoLab_Adobe_Fonts_Provider::get_fonts();

					if ( is_wp_error( $fonts ) ) {
						kalium()->helpers->add_admin_notice( $fonts->get_error_message(), 'warning' );
					}
				}

				// Set current editing font
				self::$current_font = $current_font;

				// Enqueues
				wp_enqueue_style( 'typolab-select2' );
				wp_enqueue_script( 'typolab-select2' );

				// Save font changes
				if ( kalium()->request->input( 'save_font' ) && check_admin_referer( 'typolab-save-font' ) ) {
					$this->save_font( $current_font );
				}
			}
		}

		// Save font appearance settings
		if ( kalium()->request->input( 'save_font_appearance_settings' ) && check_admin_referer( 'typolab-save-font-appearance-settings' ) ) {
			$this->save_font_appearance_settings();
		}

		// Save TypoLab settings
		if ( kalium()->request->input( 'save_settings' ) && check_admin_referer( 'typolab-save-settings' ) ) {
			$this->save_settings();
		}

		// Add a new font
		if ( kalium()->request->has( 'typolab_add_font', 'post' ) && check_admin_referer( 'typolab-add-font' ) ) {
			$font_source = kalium()->request->input( 'font_source' );

			if ( in_array( $font_source, array_keys( TypoLab_Data::get_font_sources() ) ) ) {
				wp_redirect( add_query_arg( 'font-source', $font_source, self::get_font_action_link( 'edit-font', self::new_font_id() ) ) );
				exit;
			}
		}

		// Typolab disabled notice
		if ( ! self::is_enabled() && 'settings' !== $typolab_page ) {
			kalium()->helpers->add_admin_notice( sprintf( 'Fonts are currently disabled. Go to <a href="%s">Settings</a> to enable fonts &raquo;', esc_url( admin_url( 'admin.php?page=typolab&typolab-page=settings&typolab-advanced-settings' ) ) ), 'warning' );
		}

		// Fonts list table
		if ( self::is_typolab_admin_page() && ! in_array( $typolab_page, [ 'fonts-appearance', 'settings' ] ) ) {
			$GLOBALS['fonts_list_table'] = new TypoLab_Fonts_List_Table();
			$GLOBALS['fonts_list_table']->prepare_items();
		}

		// Show admin notice
		if ( $notice = self::get_option( 'show_notice_once' ) ) {
			if ( time() - $notice['timestamp'] < 10 ) {
				kalium()->helpers->add_admin_notice( $notice['message'], $notice['type'] );
			}

			self::set_option( 'show_notice_once', null );
		}
	}

	/**
	 * Current action.
	 *
	 * @return string
	 */
	private function current_action() {
		return kalium()->request->query( 'typolab-action' );
	}

	/**
	 * Verify action.
	 *
	 * @param string $action
	 * @param string $query_arg
	 *
	 * @return int|false
	 */
	private function verify_action( $action, $query_arg = '_wpnonce' ) {
		return check_admin_referer( $action, $query_arg );
	}

	/**
	 * Current action verify.
	 *
	 * @param string $action
	 *
	 * @return int|false
	 */
	private function verify_current_action( $action ) {
		return $action === $this->current_action() && $this->verify_action( $action );
	}

	/**
	 * Save current font.
	 *
	 * @param TypoLab_Font|TypoLab_Google_Font|TypoLab_Hosted_Google_Font|TypoLab_Font_Squirrel_Font|TypoLab_Laborator_Font|TypoLab_Adobe_Font|TypoLab_Hosted_Font|TypoLab_External_Font $font
	 */
	private function save_font( $font ) {
		$font_family            = kalium()->request->input( 'font_family' );
		$font_variants          = kalium()->request->input( 'font_variants' );
		$font_base_selectors    = kalium()->request->input( 'font_base_selectors' );
		$font_custom_selectors  = kalium()->request->input( 'font_custom_selectors' );
		$conditional_statements = kalium()->request->input( 'conditional_statements' );
		$font_placement         = kalium()->request->input( 'font_placement' );
		$font_preload           = kalium()->request->input( 'font_preload' );
		$font_status            = kalium()->request->input( 'font_status' );

		// Set font data
		$font->family_name = $font_family;
		$font->active      = 'active' === $font_status;
		$font->placement   = $font_placement;
		$font->preload     = $font_preload;

		// Options by font source
		switch ( $font->get_source() ) {

			// Google font
			case 'google':
				$variants = [];

				if ( is_array( $font_variants ) ) {
					foreach ( $font_variants as $font_variant ) {
						$variants[] = TypoLab_Google_Font::parse_variant( $font_variant, $font );
					}
				}

				// Set variants
				$font->variants = $variants;

				// Pull Google font
				if ( $font instanceof TypoLab_Hosted_Google_Font ) {

					// Install font if its not installed
					if ( false === $font->is_installed() ) {
						$installed = $font->install();

						if ( is_wp_error( $installed ) ) {
							$errors = $installed;
						}
					}
				}
				break;

			// Font Squirrel font
			case 'font-squirrel':
				$variants = [];

				if ( is_array( $font_variants ) ) {
					foreach ( $font_variants as $font_variant ) {
						$variants[] = TypoLab_Font_Squirrel_Font::parse_variant( $font_variant, $font );
					}
				}

				// Set variants
				$font->variants = $variants;

				// Install font if its not installed
				if ( false === $font->is_installed() ) {
					$installed = $font->install();

					if ( is_wp_error( $installed ) ) {
						$errors = $installed;
					}
				}
				break;

			// Laborator font
			case 'laborator':
				$variants = [];

				if ( is_array( $font_variants ) ) {
					foreach ( $font_variants as $font_variant ) {
						$variants[] = TypoLab_Laborator_Font::parse_variant( $font_variant, $font );
					}
				}

				// Set variants
				$font->variants = $variants;

				// Install font if its not installed
				if ( false === $font->is_installed() ) {
					$installed = $font->install();

					if ( is_wp_error( $installed ) ) {
						$errors = $installed;
					}
				}
				break;

			// System font
			case 'system':
				$variants    = [];
				$system_font = TypoLab_System_Fonts_Provider::get_font( $font->get_family_name() );

				if ( isset( $system_font['variants'] ) ) {
					foreach ( $system_font['variants'] as $font_variant ) {
						$variants[] = TypoLab_System_Font::parse_variant( $font_variant, $font );
					}
				}

				// Set variants
				$font->variants = $variants;
				break;

			// Adobe font
			case 'adobe':
				$variants   = [];
				$adobe_font = TypoLab_Adobe_Fonts_Provider::get_font( $font->get_family_name() );

				if ( isset( $adobe_font['variations'] ) ) {
					foreach ( $adobe_font['variations'] as $font_variant ) {
						$variants[] = TypoLab_Adobe_Font::parse_variant( $font_variant, $font );
					}
				}

				// Set variants
				$font->variants = $variants;

				// Set Kit ID
				$font->kit_id = kalium()->request->input( 'kit_id' );
				break;

			// Hosted font
			case 'hosted':
				$variants = [];

				if ( is_array( $font_variants ) ) {
					foreach ( $font_variants as $font_variant ) {
						$variants[] = new TypoLab_Font_Variant( $font_variant, $font );
					}
				}

				// Set variants
				$font->variants = $variants;
				break;

			// External font
			case 'external':
				$variants = [];

				if ( is_array( $font_variants ) ) {
					foreach ( $font_variants as $font_variant ) {
						$variants[] = new TypoLab_Font_Variant( $font_variant, $font );
					}
				}

				// Set variants
				$font->variants = $variants;

				// Stylesheet URL
				$font->stylesheet_url = kalium()->request->input( 'stylesheet_url' );
				break;
		}

		// Font preload
		if ( $font->supports_preload() && $font->do_preload() ) { // && $font->do_fetch_preloads()
			$font->preload();
		}

		// Selectors
		$base_selectors = $custom_selectors = [];

		// Assign base selectors
		if ( is_array( $font_base_selectors ) ) {
			foreach ( $font_base_selectors as $base_selector_id => $base_selector ) {
				$base_selectors[] = new TypoLab_Font_Selector( [
					'id'      => $base_selector_id,
					'variant' => kalium_get_array_key( $base_selector, 'variant' ),
					'include' => isset( $base_selector['checked'] ),
				] );
			}
		}

		$font->base_selectors = $base_selectors;

		// Custom selectors
		if ( is_array( $font_custom_selectors ) ) {
			foreach ( $font_custom_selectors as $selector_id => $selector ) {
				if ( ! empty( $selector['selectors'] ) ) { // Prevent empty selectors
					$custom_selectors[] = new TypoLab_Font_Custom_Selector( array_merge( [ 'id' => $selector_id ], $selector ) );
				}
			}
		}

		$font->custom_selectors = $custom_selectors;

		// Conditional loading
		$font_load_conditions = [];

		if ( is_array( $conditional_statements ) ) {
			foreach ( $conditional_statements as $conditional_statement ) {
				$font_load_conditions[] = new TypoLab_Font_Load_Condition( $conditional_statement );
			}
		}

		$font->conditional_loading_statements = $font_load_conditions;

		// Remove "legacy_font" font option
		$font->remove_option( 'legacy_font' );

		// Save font
		$font->save();

		// Show Font Updated Message
		if ( isset( $errors ) ) {
			kalium()->helpers->add_admin_notice( $errors->get_error_message(), 'error' );
		} else {
			if ( $font->get_family_name() ) {
				kalium()->helpers->add_admin_notice( 'Font updated.' );
			} else {
				kalium()->helpers->add_admin_notice( 'Please select font family!', 'info' );
			}
		}
	}

	/**
	 * Save font appearance settings.
	 */
	private function save_font_appearance_settings() {
		$font_appearance_groups = kalium()->request->input( 'font_appearance_groups', [] );

		// Only array
		if ( is_array( $font_appearance_groups ) ) {
			TypoLab_Font_Appearance_Settings::set_settings( $font_appearance_groups );
		}

		// Remove legacy font sizes (on save)
		if ( TypoLab::get_option( 'font_sizes' ) ) {
			TypoLab::set_option( 'font_sizes', null );
		}

		// Show notice
		kalium()->helpers->add_admin_notice( 'Font sizes have been saved.' );
	}

	/**
	 * Save TypoLab settings.
	 */
	private function save_settings() {
		$typolab_enabled       = kalium()->request->input( 'typolab_enabled' );
		$font_preload          = kalium()->request->input( 'font_preload' );
		$pull_google_fonts     = kalium()->request->input( 'pull_google_fonts' );
		$font_placement        = kalium()->request->input( 'font_placement' );
		$font_display          = kalium()->request->input( 'font_display' );
		$adobe_fonts_api_token = kalium()->request->input( 'adobe_fonts_api_token' );
		$default_unit          = kalium()->request->input( 'default_unit' );
		$font_preview_text     = kalium()->request->input( 'font_preview_text' );
		$font_preview_size     = kalium()->request->input( 'font_preview_size' );

		// Font Settings
		$font_settings = self::get_font_settings();

		// Fonts
		$fonts = self::get_fonts();

		// Google fonts pull status
		$previous_pull_google_fonts = kalium_get_array_key( $font_settings, 'pull_google_fonts' );

		// TypoLab status
		self::$enabled = $font_settings['typolab_enabled'] = kalium_validate_boolean( $typolab_enabled );

		// Font preloading
		self::$font_preload = $font_settings['font_preload'] = kalium_validate_boolean( $font_preload );

		// Pull Google fonts
		self::$pull_google_fonts = $font_settings['pull_google_fonts'] = kalium_validate_boolean( $pull_google_fonts );

		// Font placement
		self::$font_placement = $font_settings['font_placement'] = 'body' === $font_placement ? $font_placement : 'head';

		// Font display
		self::$font_display = $font_settings['font_display'] = $font_display;

		// Adobe fonts API token
		self::$adobe_fonts_api_token = $font_settings['adobe_fonts_api_token'] = trim( $adobe_fonts_api_token );

		// Default unit
		self::$default_unit = $font_settings['default_unit'] = $default_unit;

		// Font preview text
		self::$font_preview_str = $font_settings['font_preview_str'] = $font_preview_text ?: self::$font_preview_str;

		// Font preview size
		self::$font_preview_size = $font_settings['font_preview_size'] = is_numeric( $font_preview_size ) ? $font_preview_size : self::$font_preview_size;

		// Save font settings
		self::set_option( 'font_settings', $font_settings );

		// Verify Adobe fonts API token
		if ( self::$adobe_fonts_api_token ) {
			$api_result = TypoLab_Adobe_Fonts_Provider::fetch_kits();

			if ( is_wp_error( $api_result ) ) {
				kalium()->helpers->add_admin_notice( $api_result->get_error_message(), 'warning' );
			}
		}

		// Pull Google fonts (if option is switched)
		if ( $previous_pull_google_fonts !== $pull_google_fonts ) {
			foreach ( $fonts as $font ) {

				// Pull Google fonts (if not installed)
				if ( $font instanceof TypoLab_Hosted_Google_Font ) {
					if ( ! $font->is_installed() && $font->install() ) {
						$font->save();
					}
				}
			}
		}

		// Generate preloads and save fonts
		if ( $font_preload ) {
			foreach ( $fonts as $font ) {
				if ( $font->supports_preload() && $font->do_preload() ) {
					$font->preload();
					$font->save();
				}
			}
		}

		// Show notice
		kalium()->helpers->add_admin_notice( 'Font settings have been saved.' );

		// Delete Adobe fonts transient
		TypoLab_Adobe_Fonts_Provider::reset_fonts_cache();
	}
}

// Initialize Typolab
TypoLab::instance();
