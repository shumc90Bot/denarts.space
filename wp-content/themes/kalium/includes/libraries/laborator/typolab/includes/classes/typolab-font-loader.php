<?php
/**
 * TypoLab - ultimate font management library.
 *
 * Font loader.
 *
 * @author Laborator
 * @link   https://laborator.co
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class TypoLab_Font_Loader {

	/**
	 * Fonts to load.
	 *
	 * @var TypoLab_Font[]|TypoLab_Font_Preload[]
	 */
	public static $fonts = [];

	/**
	 * Load fonts.
	 */
	public static function load_fonts() {
		if ( ! TypoLab::is_enabled() ) {
			return;
		}

		/** @var TypoLab_Font[] fonts */
		self::$fonts = TypoLab::get_fonts( [
			'status' => 'active',
		] );

		// Filter fonts that have conditional loading
		self::$fonts = array_filter( self::$fonts, function ( $font ) {
			return $font->do_load();
		} );

		// Do enqueues
		self::enqueue();
	}

	/**
	 * Enqueues.
	 */
	public static function enqueue() {

		// Before loading fonts
		self::before_loading_fonts();

		// Preload fonts
		self::preload_fonts();

		// Enqueue head fonts
		self::enqueue_fonts( self::get_head_fonts() );

		// Print font appearance styles
		add_action( 'wp_print_scripts', [ self::class, 'print_font_appearance_styles' ] );

		// Print font base selectors and custom selectors on head
		add_action( 'wp_print_scripts', [ self::class, 'print_head_font_styles' ] );
		add_action( 'wp_print_scripts', [ self::class, 'print_head_font_selectors' ] );

		// Enqueue footer fonts
		add_action( 'wp_footer', [ self::class, 'enqueue_footer_fonts' ] );
		add_action( 'wp_footer', [ self::class, 'print_footer_font_selectors' ], 20 );
	}

	/**
	 * Get head fonts.
	 *
	 * @return TypoLab_Font[]
	 */
	public static function get_head_fonts() {
		return array_filter( self::$fonts, function ( $font ) {
			return ! $font->in_footer();
		} );
	}

	/**
	 * Get footer fonts.
	 *
	 * @return TypoLab_Font[]
	 */
	public static function get_footer_fonts() {
		return array_filter( self::$fonts, function ( $font ) {
			return $font->in_footer();
		} );
	}

	/**
	 * Before loading fonts.
	 */
	public static function before_loading_fonts() {
		$has_google_fonts = false;

		foreach ( self::$fonts as $font ) {
			if ( $font instanceof TypoLab_Google_Font && ! $font instanceof TypoLab_Hosted_Google_Font ) {
				$has_google_fonts = true;
			}
		}

		// Google fonts preconnect
		if ( $has_google_fonts ) {
			echo '<link rel="preconnect" href="https://fonts.googleapis.com">', PHP_EOL;
			echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>', PHP_EOL;
		}
	}

	/**
	 * Preload fonts.
	 */
	public static function preload_fonts() {
		$mime_types = [
			'woff2' => 'font/woff2',
			'woff'  => 'font/woff',
			'ttf'   => 'font/ttf',
			'svg'   => 'image/svg+xml',
			'eot'   => 'application/vnd.ms-fontobject',
		];

		foreach ( self::$fonts as $font ) {
			if ( $font->supports_preload() && $font->do_preload() ) {

				// Load unique URLs
				$preload_urls = array_unique( $font->get_preload_urls() );

				if ( ! empty( $preload_urls ) ) {
					foreach ( $preload_urls as $preload_url ) {
						$extension = pathinfo( $preload_url, PATHINFO_EXTENSION );
						$mime      = kalium_get_array_key( $mime_types, $extension );

						// Local fonts
						if ( 0 !== strpos( $preload_url, 'http' ) ) {
							$preload_url = trailingslashit( TypoLab::$fonts_url ) . $preload_url;
						}

						// Preload tag
						echo sprintf( '<link rel="preload" href="%s" as="font" type="%s" crossorigin>', $preload_url, $mime );
						echo PHP_EOL;
					}
				}
			}
		}
	}

	/**
	 * Enqueue fonts.
	 *
	 * @param TypoLab_Font[] $fonts
	 */
	public static function enqueue_fonts( $fonts ) {
		foreach ( $fonts as $font ) {
			$handle         = 'typolab-' . sanitize_title( $font->get_family_name() ) . '-' . $font->get_id();
			$stylesheet_url = $font->get_stylesheet_url();

			// Enqueue Stylesheet
			if ( $stylesheet_url ) {
				wp_enqueue_style( $handle, $stylesheet_url, null, kalium()->get_version() );
			}
		}
	}

	/**
	 * Print font styles.
	 *
	 * @param TypoLab_Font[] $fonts
	 */
	public static function print_styles( $fonts ) {
		foreach ( $fonts as $font ) {
			$font->print_styles();
		}
	}

	/**
	 * Print font selectors.
	 *
	 * @param TypoLab_Font[] $fonts
	 */
	public static function print_selectors( $fonts ) {
		foreach ( $fonts as $font ) {
			$base_selectors   = TypoLab_CSS_Generator::parse( $font->get_base_selectors(), $font, true );
			$custom_selectors = TypoLab_CSS_Generator::parse( $font->get_custom_selectors(), $font, true );

			// Minify CSS selectors
			if ( apply_filters( 'typolab_minify_css_selectors', true ) ) {
				$base_selectors   = TypoLab_Helper::minimize_css( $base_selectors );
				$custom_selectors = TypoLab_Helper::minimize_css( $custom_selectors );
			}

			if ( $base_selectors ) {
				echo PHP_EOL, sprintf( '<style data-base-selectors>%s</style>', $base_selectors );
			}

			if ( $custom_selectors ) {
				echo PHP_EOL, sprintf( '<style data-custom-selectors>%s</style>', $custom_selectors );
			}
		}
	}

	/**
	 * Print font appearance styles.
	 *
	 * Hook: self::print_font_appearance_styles()
	 */
	public static function print_font_appearance_styles() {
		$font_appearance_settings = TypoLab_CSS_Generator::parse( TypoLab_Font_Appearance_Settings::get_settings(), null, true );

		if ( $font_appearance_settings ) {
			echo PHP_EOL, sprintf( '<style data-font-appearance-settings>%s</style>', $font_appearance_settings );
		}
	}

	/**
	 * Enqueue footer fonts.
	 *
	 * Hook: self::enqueue_footer_fonts()
	 */
	public static function enqueue_footer_fonts() {
		self::enqueue_fonts( self::get_footer_fonts() );
	}

	/**
	 * Print head font styles.
	 *
	 * Hook: self::print_head_font_styles()
	 */
	public static function print_head_font_styles() {
		self::print_styles( self::get_head_fonts() );
	}

	/**
	 * Print font base selectors and custom selectors on head.
	 *
	 * Hook: self::print_head_font_selectors()
	 */
	public static function print_head_font_selectors() {
		self::print_selectors( self::get_head_fonts() );
	}

	/**
	 * Print footer font styles.
	 *
	 * Hook: self::print_footer_font_styles()
	 */
	public static function print_footer_font_styles() {
		self::print_styles( self::get_footer_fonts() );
	}

	/**
	 * Print font base selectors and custom selectors on head.
	 *
	 * Hook: self::print_footer_font_selectors()
	 */
	public static function print_footer_font_selectors() {
		self::print_selectors( self::get_footer_fonts() );
	}
}
