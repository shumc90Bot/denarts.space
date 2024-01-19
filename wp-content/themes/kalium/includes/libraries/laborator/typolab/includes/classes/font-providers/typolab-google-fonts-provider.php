<?php
/**
 * TypoLab - ultimate font management library.
 *
 * Google Fonts provider.
 *
 * @author Laborator
 * @link   https://laborator.co
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class TypoLab_Google_Fonts_Provider {

	/**
	 * Provider ID.
	 *
	 * @var string
	 */
	public static $provider_id = 'google-fonts';

	/**
	 * Loaded Google Fonts.
	 *
	 * @var array
	 */
	public static $fonts_list;

	/**
	 * Load and return Google fonts.
	 * This function will cache the fonts list the first time is called.
	 *
	 * @return array
	 */
	public static function get_fonts() {
		if ( is_null( self::$fonts_list ) ) {
			$google_fonts = file_get_contents( sprintf( '%s/assets/json/google-fonts.json', TypoLab::$typolab_dir ) );
			$fonts_json   = json_decode( $google_fonts, true );

			self::$fonts_list = kalium_get_array_key( $fonts_json, 'items', [] );
		}

		return self::$fonts_list;
	}

	/**
	 * Get fonts list.
	 * Suitable for Font Selector field.
	 *
	 * @return array
	 */
	public static function get_fonts_list() {
		$list = [];

		foreach ( self::get_fonts() as $font ) {
			$list[] = [
				'font_family' => $font['family'],
				'category'    => $font['category'],
				'font_data'   => $font,
			];
		}


		return $list;
	}

	/**
	 * Build stylesheet URL.
	 *
	 * @param array                 $args {
	 *
	 * @type string                 $family_name
	 * @type TypoLab_Font_Variant[] $variants
	 * @type string                 $display
	 * @type string                 $text
	 * }
	 *
	 * @return string
	 */
	public static function build_stylesheet_url( $args = [] ) {
		if ( isset( $args['family_name'] ) ) {
			$args = [ $args ];
		}

		$url = [];

		foreach ( $args as $arg ) {
			$arg = wp_parse_args( $arg, [
				'family_name' => '',
				'variants'    => [],
				'display'     => 'swap',
				'text'        => null,
			] );

			// Font data
			$family_name = $arg['family_name'];
			$variants    = $arg['variants'];
			$display     = $arg['display'];
			$text        = $arg['text'];

			// Other vars
			$family_url_arg = [];
			$wght           = [];
			$ital           = [];
			$has_wght       = false;

			// Add font family to URL
			$family_url_arg[] = 'family=' . urlencode( $family_name );

			// Wrap variants with array
			if ( ! is_array( $variants ) ) {
				$variants = [ $variants ];
			}

			// Loop through variants
			foreach ( $variants as $variant ) {
				/** @var TypoLab_Font_Variant $variant */
				if ( ! $variant->is_regular() ) {
					$has_wght = true;
				}

				if ( ! $variant->is_regular() ) {
					if ( $variant->is_italic() ) {
						$ital[] = $variant->weight;
					} else {
						$wght[] = $variant->weight;
					}
				} else if ( $variant->is_italic() ) {
					$ital[] = 400;
				} else {
					$wght[] = 400;
				}
			}

			// Unique sorted values
			$ital = array_unique( $ital );
			$wght = array_unique( $wght );

			sort( $ital );
			sort( $wght );

			// Lengths
			$ital_length = count( $ital );
			$wght_length = count( $wght );

			// Map functions
			$ital_tupple_mapper = function ( $tuple ) use ( $ital, $wght, $ital_length, $wght_length ) {
				if ( 1 === $ital_length && 400 === $ital[0] ) {
					if ( $wght_length > 1 || ( 1 === $wght_length && 400 !== $wght[0] ) ) {
						return implode( ',', [ 1, $tuple ] );
					}

					return 1;
				}

				return implode( ',', [ 1, $tuple ] );
			};

			$wght_tupple_mapper = function ( $tuple ) use ( $ital, $wght, $ital_length, $wght_length ) {
				if ( 1 === $ital_length && 1 === $wght_length && 400 === $ital[0] ) {
					return 0;
				}

				if ( $ital_length && $wght_length ) {
					return implode( ',', [ 0, $tuple ] );
				}

				return $tuple;
			};

			// Axis tag list
			if ( ( $wght_length > 1 || $ital_length > 0 ) || ( 1 === $wght_length && 400 !== $wght[0] ) ) {
				$family_url_arg[] = ':';

				// Axis tag: ital
				if ( $ital_length > 0 ) {
					$family_url_arg[] = 'ital';
				}

				// Axis tag: wght
				if ( $wght_length > 0 && ( $wght_length > 1 || 400 !== $wght[0] ) || $has_wght ) {
					$family_url_arg[] = $ital_length ? ',' : '';
					$family_url_arg[] = 'wght';
				}

				// Variants
				$family_url_arg[] = '@';
				$family_url_arg[] = implode( ';', array_map( $wght_tupple_mapper, $wght ) );
				$family_url_arg[] = $wght_length && $ital_length ? ';' : '';
				$family_url_arg[] = implode( ';', array_map( $ital_tupple_mapper, $ital ) );
			}

			// Add family to URL
			if ( count( $url ) ) {
				$url[] = '&';
			}

			$url[] = implode( '', $family_url_arg );
		}

		// Display
		if ( $display ) {
			$url[] = '&display=' . urlencode( $display );
		}

		// Text
		if ( $text ) {
			$url[] = '&text=' . urlencode( $text );
		}

		// API URL
		array_unshift( $url, 'https://fonts.googleapis.com/css2?' );

		return implode( '', $url );
	}

	/**
	 * Get stylesheet content.
	 *
	 * @param string $stylesheet_url
	 * @param string $font_type
	 *
	 * @return string|WP_Error
	 */
	public static function get_stylesheet_content( $stylesheet_url, $font_type = 'woff2' ) {

		// Get stylesheet
		$request = wp_remote_get( $stylesheet_url, [
			'headers' => [
				'User-Agent' => 'woff' === $font_type ? TypoLab_Helper::USER_AGENT_IE : TypoLab_Helper::USER_AGENT_CHROME,
			],
		] );

		// On errors
		if ( is_wp_error( $request ) ) {
			return $request;
		}

		// Other HTTP errors
		$response_code = wp_remote_retrieve_response_code( $request );

		if ( 200 !== $response_code ) {
			$error_message = wp_remote_retrieve_response_message( $request );

			// Missing font family
			if ( 400 === $response_code ) {
				$error_message = 'Bad Request 400: Missing font family or invalid selector!';
			}

			return new WP_Error( 'http_error', $error_message );
		}

		return wp_remote_retrieve_body( $request );
	}
}
