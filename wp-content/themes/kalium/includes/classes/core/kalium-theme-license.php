<?php
/**
 * Kalium WordPress Theme
 *
 * Theme license class.
 *
 * @author Laborator
 * @link   https://kaliumtheme.com
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

/**
 * @property string $admin_page Current admin screen.
 */
class Kalium_Theme_License {

	/**
	 * License details.
	 *
	 * @var stdClass
	 */
	private static $license;

	/**
	 * Laborator API Server URL.
	 *
	 * @var string
	 */
	public $api_server = 'https://api.laborator.co';

	/**
	 * Constructor.
	 */
	public function __construct() {

		// Transfer license from theme mods to options
		if ( $current_license = get_theme_mod( 'license' ) ) {
			update_option( 'kalium_license', $current_license );
			remove_theme_mod( 'license' );
		}

		// Admin init
		add_action( 'admin_init', [ $this, '_admin_init' ] );
		add_action( 'admin_print_scripts', [ $this, 'admin_print_scripts' ] );
		add_action( 'wp_ajax_kalium-dismiss-support-subscription', [ $this, 'dismiss_support_subscription_notice' ] );
		add_action( 'wp_ajax_kalium-recheck-support-subscription', [ $this, 'recheck_support_subscription_notice' ] );

		// Initialize License
		$this->init_license_var();
	}

	/**
	 * Admin actions for this class.
	 *
	 * @return void
	 */
	public function _admin_init() {

		// Do not execute on AJAX
		if ( defined( 'DOING_AJAX' ) ) {
			return;
		}

		// Vars
		$is_about_page = 'kalium' === kalium()->request->query( 'page' );

		// Theme registration tab
		if ( $is_about_page ) {

			// Register theme page
			if ( $license_key = kalium()->request->query( 'license_key' ) ) {

				// Verify theme registration
				if ( kalium()->request->has( 'perform_verification' ) ) {
					$verify_license_response = wp_remote_get(
						sprintf( '%1$s/verify-license/%2$s/', $this->get_api_server_url(), $license_key ),
						[
							'timeout' => 30,
						]
					);

					// Check for connection errors
					if ( is_wp_error( $verify_license_response ) ) {
						wp_die( $verify_license_response );
					}

					// Parse license data
					$license_data = json_decode( wp_remote_retrieve_body( $verify_license_response ) );

					if ( is_null( $license_data ) ) {
						wp_die( "\u{49}nv\u{61}lid \u{4a}SON!" );
					} elseif ( $license_data->valid && false !== stripos( $this->convert_idn_to_ascii( home_url() ), $license_data->domain ) ) {
						unset( $license_data->valid );
						delete_option( 'kalium_upgrader' );
						update_option( 'kalium_license', $license_data );
						delete_site_transient( 'kalium_theme_plugins_data' );
						wp_redirect( remove_query_arg( 'perform_verification' ) );
						die();
					}

					// Expired link
					wp_nonce_ays( '' );
				} // Check if current registration is valid
				elseif ( $this->is_theme_registered() && $license_key === kalium()->theme_license->get_license_key() ) {
					add_filter( 'admin_title', kalium_hook_return_value( sprintf( 'Theme registration complete &lsaquo; %s', get_bloginfo( 'name' ) ) ) );
					add_filter( 'admin_body_class', kalium_hook_concat_string_value( ' about-kalium--theme-registration-success' ) );
					add_action( 'kalium_page_about', [ $this, '_product_registration_success_page' ] );
				}
			}

			// Theme registration vars
			add_action( 'kalium_theme_registration_tab', [ $this, 'theme_registration_vars' ] );
		}

		// Theme Backups
		if ( $is_about_page && $this->is_theme_registered() && kalium()->request->has( 'theme_backups', 'request' ) ) {
			self::$license->save_backups = boolval( kalium()->request->request( 'theme_backups' ) );
			update_option( 'kalium_license', self::$license );
			kalium()->helpers->add_admin_notice( 'Theme backup settings have been saved!' );
		}

		// Theme registration actions
		if ( $is_about_page && kalium()->request->has( 'action' ) ) {

			switch ( kalium()->request->query( 'action' ) ) {

				// Remove Theme Registration
				case 'remove-theme-registration':
					kalium()->helpers->add_admin_notice( 'Theme registration has been removed!', 'warning' );

					if ( isset( $_GET['_nonce'] ) && wp_verify_nonce( $_GET['_nonce'], 'remove-theme-registration' ) ) {
						delete_option( 'kalium_license' );
						wp_redirect( remove_query_arg( [ '_nonce' ] ) );
						die();
					}
					break;

				// Validate Theme Activation
				case 'validate-theme-registration':
					kalium()->helpers->add_admin_notice( 'Theme registration status has been reloaded!', 'info' );

					if ( wp_verify_nonce( kalium()->request->query( '_wpnonce' ), 'validate-theme-registration' ) ) {
						$this->validate_license();
						wp_redirect( remove_query_arg( '_wpnonce' ) );
						die();
					}
					break;

			}
		}

		// Nearly expiring notification
		if ( $this->is_theme_registered() && ( ! $this->has_valid_support() || $this->nearly_expiring() ) ) {
			$this->display_subscription_notice();
		}

		// Theme registration reminder
		if ( false === get_transient( 'kalium_theme_registration_reminder' ) ) {
			set_transient( 'kalium_theme_registration_reminder', time() + HOUR_IN_SECONDS, YEAR_IN_SECONDS );
		}

		$this->theme_registration_reminder();
	}

	/**
	 * Admin print scripts.
	 *
	 * @since 3.4
	 */
	public function admin_print_scripts() {
		$css_style = '<style>#toplevel_page_laborator_options .wp-submenu a[href*="theme-registration"]:after { %s }</style>';

		if ( ! $this->is_theme_registered() ) {
			echo sprintf(
				$css_style,
				kalium()->helpers->build_css_props(
					[
						'display'     => 'inline-block',
						'font-family' => '"KaliumAdminIcons"',
						'content'     => '"\e933"',
						'margin-left' => '5px',
					]
				)
			);
		} elseif ( $this->nearly_expiring() || $this->get_remaining_support() <= 0 ) {
			echo sprintf(
				$css_style,
				kalium()->helpers->build_css_props(
					[
						'position'      => 'relative',
						'top'           => '1px',
						'display'       => 'inline-block',
						'content'       => '""',
						'background'    => $this->nearly_expiring() ? '#F0BC2D' : '#D73638',
						'border-radius' => '10px',
						'width'         => '10px',
						'height'        => '10px',
						'margin-left'   => '5px',
					]
				)
			);
		}
	}

	/**
	 * Dismiss support subscription notice.
	 *
	 * @since 3.7.1
	 */
	public function dismiss_support_subscription_notice() {
		update_user_meta( get_current_user_id(), 'kalium-dismiss-support-subscription', 1 );

		die( '1' );
	}

	/**
	 * Soft dismiss support notice.
	 *
	 * @since 3.8
	 */
	public function recheck_support_subscription_notice() {
		if ( current_user_can( 'manage_options' ) ) {
			set_transient( 'kalium_dismiss_expiration', true, HOUR_IN_SECONDS );
			set_transient( 'kalium_reload_license_check', time() + HOUR_IN_SECONDS, DAY_IN_SECONDS );

			die( '1' );
		}
	}

	/**
	 * Theme registration success page.
	 *
	 * @return void
	 */
	public function _product_registration_success_page() {

		// Enqueue theme registration
		kalium_enqueue( 'theme-registration-js' );

		// Load template
		kalium()->require_file(
			'includes/admin-templates/about/product-registration-success.php',
			[
				'theme_name'   => wp_get_theme()->display( 'Name' ),
				'save_backups' => kalium()->theme_license->get_backups_status(),
			]
		);
	}

	/**
	 * Theme registration JSON data.
	 *
	 * @return void
	 */
	public function theme_registration_vars() {
		?>
		<script id="kalium-theme-register-form-data" type="text/template">
		<?php
		echo wp_json_encode(
			[
				// Request product registration
				'action'   => 'register-theme',

				// This theme
				'theme_id' => 'kalium',

				// Laborator API site url to go for activation
				'api'      => $this->api_server,

				// Theme version
				'version'  => kalium()->get_version(),

				// URL for the site to create license
				'url'      => $this->convert_idn_to_ascii( home_url() ),

				// Laborator API will send back to this URL to verify license
				'ref_url'  => admin_url( sprintf( 'admin.php?page=%s&tab=%s', $this->admin_page, kalium()->request->query( 'tab' ) ) ),
			]
		);
		?>
			</script>
		<?php
	}

	/**
	 * Convert domain name to ASCII.
	 *
	 * @param string $url
	 *
	 * @return string
	 */
	public function convert_idn_to_ascii( $url ) {
		$protocol = '';

		if ( preg_match( '/^(https?:\/\/)/', $url, $matches ) ) {
			$protocol = $matches[1];
			$url      = substr( $url, strlen( $protocol ) );
		}

		// IDN to ASCII
		if ( function_exists( 'idn_to_ascii' ) ) {
			$variant = 0;

			if ( defined( 'INTL_IDNA_VARIANT_UTS46' ) ) {
				$variant = INTL_IDNA_VARIANT_UTS46;
			} elseif ( version_compare( phpversion(), '7.2', '>=' ) ) {
				$variant = 1;
			}

			$url_converted = idn_to_ascii( $url, 0, $variant );

			// Fallback for INTL_IDNA_VARIANT_UTS46
			if ( ! $url_converted ) {
				$url_converted = @idn_to_ascii( $url );
			}

			// Replace current URL with converted URL
			if ( $url_converted ) {
				$url = $url_converted;
			}
		}

		return $protocol . utf8_uri_encode( $url );
	}

	/**
	 * Get API Server URL.
	 *
	 * @return string
	 */
	public function get_api_server_url() {

		// When OpenSSL version is not supported, remove https protocol
		if ( function_exists( 'kalium_get_openssl_version_number' ) && version_compare( kalium_get_openssl_version_number(), '1.0', '<' ) ) {
			return str_replace( 'https://', 'http://', $this->api_server );
		}

		return $this->api_server;
	}

	/**
	 * Get remaining support in days.
	 *
	 * @return int
	 */
	public function get_remaining_support() {
		$license = $this->get_license();

		// Retrieve remaining days in support package
		if ( $license && $license->support_available ) {
			$supported_until = strtotime( $license->supported_until );
			$days_remaining  = ceil( ( $supported_until - time() ) / ( 3600 * 24 ) );

			if ( $days_remaining > 0 ) {
				return intval( $days_remaining );
			}
		}

		return 0;
	}

	/**
	 * Get backups status, returns true if theme backups are saved before updating the theme.
	 *
	 * @return bool
	 */
	public function get_backups_status() {
		$license = $this->get_license();

		if ( isset( $license->save_backups ) ) {
			return $license->save_backups;
		}

		return false;
	}

	/**
	 * Check if theme has valid support.
	 *
	 * @return bool
	 * @since 3.7.1
	 */
	public function has_valid_support() {
        $license = $this->get_license();

        if ( ! empty( $license->supported_until ) ) {
            return strtotime( $license->supported_until ) > time();
		}

        return false;
	}

	/**
	 * Check if license is nearly expiring.
	 *
	 * @return boolean
	 */
	public function nearly_expiring() {
        $remaining_support = $this->get_remaining_support();

		return $remaining_support <= 28 && $remaining_support > 0;
	}

	/**
	 * Get current license.
	 *
	 * @return stdClass
	 */
	public function get_license() {
		return self::$license;
	}

	/**
	 * Get license key.
	 *
	 * @return string
	 */
	public function get_license_key() {
		if ( $license = $this->get_license() ) {
			return $license->license_key;
		}

		return '';
	}

	/**
	 * Check if theme is registered.
	 *
	 * @return boolean
	 */
	public function is_theme_registered() {
		return $this->get_license_key() && false !== stripos( $this->convert_idn_to_ascii( home_url() ), $this->get_license()->domain );
	}

	/**
	 * Get purchase link.
	 *
	 * @return string
	 * @since 3.7.1
	 */
	public function get_purchase_link() {
		return 'https://1.envato.market/kalium-buy-license';
	}

	/**
	 * Get renew support link.
	 *
	 * @return string
	 * @since 3.7.1
	 */
	public function get_renew_support_link() {
		if ( $this->nearly_expiring() ) {
			return 'https://1.envato.market/kalium-support-extend';
		}

		return 'https://1.envato.market/kalium-support-renew';
	}

	/**
	 * Renew support title.
	 *
	 * @return string
	 * @since 3.7.1
	 */
	public function get_renew_support_title() {
		if ( $this->nearly_expiring() ) {
			return 'Extend Support';
		}

		return 'Renew Support';
	}

	/**
	 * Get renew support description.
	 *
	 * @return string
	 * @since 3.7.1
	 */
	public function get_renew_support_description() {
		if ( $this->nearly_expiring() ) {
			return sprintf( 'Support for the Kalium theme is about to expire (%s left). Extend support before it expires and get a 35%% discount.', human_time_diff( strtotime( $this->get_license()->supported_until ?? null ), time() ) );
		}

		return 'Support subscription for the Kalium theme has expired! Renew now for extra 6 months support.';
	}

	/**
	 * Display nearly expiring notices.
	 *
	 * @return void
     * @since 3.7.1
	 */
	private function display_subscription_notice() {
		// Hide on specific pages or dismissed by the user
		if ( get_user_meta( get_current_user_id(), 'kalium-dismiss-support-subscription', true ) || 'kalium' === kalium()->request->query( 'page' ) && in_array( kalium()->request->query( 'tab' ), [ 'theme-registration', 'help' ] ) ) {
			return;
		}

		// Display expiration notice if it's not dismissed
		if ( ! get_transient( 'kalium_dismiss_expiration' ) ) {
			$notice = '<div class="kalium-support-notice">
    <div class="support-info">
        <h3>%1$s</h3>
        %2$s
    </div>
    <div class="action-buttons">
        %3$s
    </div>
</div>';

			// Display notice
			kalium()->helpers->add_admin_notice(
				sprintf(
					$notice,
					'Kalium Support Status',
					$this->get_renew_support_description(),
					implode(
						' ',
						[
							sprintf( '<a href="%s" class="button-primary" target="_blank" rel="nopener noreferer">%s</a>', esc_attr( $this->get_renew_support_link() ), $this->get_renew_support_title() ),
							sprintf( '<a href="%s" class="button-link">Remind me later</a>', add_query_arg( [ 'dismiss-support-status' => wp_create_nonce( 'kalium_dismiss_expiration' ) ] ) ),
						]
					)
				),
				$this->nearly_expiring() ? 'warning' : 'error',
				true,
				'support-subscription'
			);

			// Dismiss the notice
			if ( kalium()->request->has( 'dismiss-support-status' ) && check_admin_referer( 'kalium_dismiss_expiration', 'dismiss-support-status' ) ) {
				set_transient( 'kalium_dismiss_expiration', true, 2 * WEEK_IN_SECONDS );
				wp_redirect( remove_query_arg( 'dismiss-support-status' ) );
				die();
			}
		}
	}

	/**
	 * Initialize Current Activated License.
	 *
	 * @return void
	 */
	private function init_license_var() {
		$license = get_option( 'kalium_license' );

		if ( is_object( $license ) && ! empty( $license->license_key ) && isset( $license->purchase_date ) && isset( $license->save_backups ) ) {
			$license->support_available = ! empty( $license->supported_until );

			// Support availability
			if ( $license->support_available ) {
				$supported_until_time = strtotime( $license->supported_until );
				$support_expired      = $supported_until_time < time();

				$license->support_expired = $support_expired;
			}

			self::$license = $license;
		}
	}

	/**
	 * Validate current license
	 *
	 * @return boolean
	 */
	private function validate_license() {
		if ( $this->is_theme_registered() ) {
			$license     = $this->get_license();
			$license_key = $license->license_key;

			$validate_license_response = wp_remote_post( $this->get_api_server_url() . "/validate-license/{$license_key}/" );
			$validated_license_data    = json_decode( wp_remote_retrieve_body( $validate_license_response ) );

			set_theme_mod( 'theme_license_last_validation', time() );

			if ( isset( $validated_license_data->valid ) ) {
				$updated_license = (object) array_merge( (array) $license, (array) $validated_license_data );
				update_option( 'kalium_license', $updated_license );

				return true;
			}

			return false;
		}
	}

	/**
	 * Theme registration reminder.
	 *
	 * @since 3.4.4
	 */
	private function theme_registration_reminder() {
		$reminder = get_transient( 'kalium_theme_registration_reminder' );

		if ( ! $this->get_license() && $reminder && 'kalium' !== kalium()->request->query( 'page' ) && $reminder < time() ) {
			$display_name = wp_get_current_user() ? wp_get_current_user()->display_name : '';
			$message      = sprintf(
				'<h3>Hello %1$s &#128075;</h3><p>To complete the installation and unlock more theme features such as automatic updates, premium plugins and demo sites please register the theme.</p><p><a href="%2$s" class="button button-primary">Register Kalium Theme</a>&nbsp;&nbsp;&nbsp;or&nbsp;&nbsp;&nbsp;<a href="%3$s" target="_blank" rel="noopener noreferrer" class="button button-secondary">Buy another license</a></p>',
				$display_name,
				admin_url( 'admin.php?page=kalium&tab=theme-registration' ),
				'https://1.envato.market/kalium-buy-license'
			);

			kalium()->helpers->add_admin_notice( $message, 'warning', false );
		}
	}
}
