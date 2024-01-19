<?php
/**
 * TypoLab - ultimate font management library.
 *
 * Typekit loader and manager.
 *
 * @author Laborator
 * @link   https://laborator.co
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class TypoLab_Adobe_Fonts_Provider {

	/**
	 * Provider ID.
	 *
	 * @var string
	 */
	public static $provider_id = 'adobe-fonts';

	/**
	 * Loaded Adobe Fonts.
	 *
	 * @var array
	 */
	public static $fonts_list;

	/**
	 * Make an API request.
	 *
	 * @param string $endpoint
	 *
	 * @return array|WP_Error
	 */
	public static function api( $endpoint ) {
		if ( ! TypoLab::get_adobe_fonts_api_token() ) {
			return new WP_Error( 'missing_api_token', sprintf( 'API token is missing! Please set Adobe Fonts API token in <a href="%s">settings page</a>!', admin_url( 'admin.php?page=typolab&typolab-page=settings' ) ) );
		}

		// API
		$api = 'https://typekit.com/api/v1/json' . $endpoint;

		$request = wp_remote_get( $api, [
			'headers' => [
				'X-Typekit-Token' => TypoLab::get_adobe_fonts_api_token(),
			],
		] );

		// Stop on error
		if ( is_wp_error( $request ) ) {
			return $request;
		}

		// Response
		$response = json_decode( wp_remote_retrieve_body( $request ), true );

		// Errors from API
		if ( ! empty( $response['errors'] ) ) {
			$response['errors'][] = 'Invalid Adobe Fonts API Token provided!';

			return new WP_Error( 'response_errors', implode( '. ', $response['errors'] ) );
		}

		// Response
		return $response;
	}

	/**
	 * Fetch font kits.
	 *
	 * @return array|WP_Error
	 */
	public static function fetch_kits() {
		$kits = self::api( '/kits' );

		return $kits;
	}

	/**
	 * Fetch font details.
	 *
	 * @return array|WP_Error
	 */
	public static function fetch_fonts() {
		$kits = self::fetch_kits();

		// Stop on error
		if ( is_wp_error( $kits ) ) {
			return $kits;
		}

		// Fonts
		$fonts = [];

		if ( isset( $kits['kits'] ) && is_array( $kits['kits'] ) ) {
			foreach ( $kits['kits'] as $kit ) {
				$kit_id      = $kit['id'];
				$kit_details = self::api( sprintf( '/kits/%s/published', $kit_id ) );

				if ( is_wp_error( $kit_details ) ) {
					continue;
				}

				if ( ! empty( $kit_details['kit'] ) ) {
					$fonts[] = $kit_details['kit'];
				}
			}
		}

		return $fonts;
	}

	/**
	 * Load and return Adobe fonts.
	 * This function will cache the fonts list the first time is called.
	 *
	 * @return array|WP_Error
	 */
	public static function get_fonts() {
		if ( is_null( self::$fonts_list ) ) {
			$fonts = get_site_transient( 'typolab_adobe_fonts' );

			// Transient has expired
			if ( false === $fonts ) {
				$fonts = self::fetch_fonts();

				// Store in database
				set_site_transient( 'typolab_adobe_fonts', $fonts, MONTH_IN_SECONDS );
			}

			self::$fonts_list = $fonts;
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

		foreach ( self::get_fonts() as $kit ) {
			$font_families = kalium_get_array_key( $kit, 'families', [] );

			foreach ( $font_families as $font_family ) {
				$font_data = array_merge( $font_family, [
					'kit_id'    => $kit['id'],
					'kit_name'  => $kit['name'],
					'published' => date( 'Y-m-d', strtotime( $kit['published'] ) ),
				] );

				$list[] = [
					'font_family' => $font_family['slug'],
					'title'       => $font_family['name'],
					'category'    => $kit['name'],
					'font_data'   => $font_data,
				];
			}
		}

		// Sort fonts
		usort( $list, function ( $a, $b ) {
			return strcmp( $a['font_family'], $b['font_family'] );
		} );

		return $list;
	}

	/**
	 * Get single font.
	 *
	 * @param string $font_family
	 *
	 * @return array|null
	 */
	public static function get_font( $font_family ) {
		$fonts = self::get_fonts_list();

		foreach ( $fonts as $font ) {
			if ( $font['font_family'] === $font_family ) {
				return kalium_get_array_key( $font, 'font_data', [] );
			}
		}

		return null;
	}

	/**
	 * Font options fields.
	 */
	public static function options_fields() {
		$kit_id = TypoLab::get_current_font()->get_kit_id();
		echo sprintf( '<input type="hidden" class="font-option-field" name="kit_id" value="%s" />', esc_attr( $kit_id ) );
	}

	/**
	 * Delete fonts transient.
	 */
	public static function reset_fonts_cache() {
		delete_site_transient( 'typolab_adobe_fonts' );
	}
}
