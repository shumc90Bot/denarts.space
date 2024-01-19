<?php
/**
 * Kalium WordPress Theme
 *
 * Other Kalium integrations.
 *
 * @author Laborator
 * @link   https://kaliumtheme.com
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

class Kalium_Integrations {

	/**
	 * Constructor.
	 *
	 * @return void
	 */
	public function __construct() {

		// Theme demo
		$this->theme_demo_integration();

		// Laborator libraries
		$this->laborator_libraries();

		// TGM plugin activation
		$this->tgmpa_integration();

		// Sidekick integration
		$this->sidekick_integration();

		// Other integrations
		$this->other_integrations();
	}

	/**
	 * Theme demo options.
	 *
	 * @return void
	 */
	private function theme_demo_integration() {
		$theme_demo_file = kalium()->locate_file( 'theme-demo/theme-demo.php' );

		if ( true === file_exists( $theme_demo_file ) ) {
			require_once $theme_demo_file;
		}
	}

	/**
	 * Laborator libraries.
	 *
	 * @return void
	 */
	private function laborator_libraries() {
		kalium()->require_file( 'includes/libraries/laborator/typolab/typolab.php' );
		kalium()->require_file( 'includes/libraries/laborator/importer/importer.php' );
		kalium()->require_file( 'includes/libraries/laborator/custom-css/custom-css.php' );
	}

	/**
	 * TGMPA integration.
	 *
	 * @return void
	 */
	private function tgmpa_integration() {
		kalium()->require_file( 'includes/libraries/class-tgm-plugin-activation.php' );
	}

	/**
	 * Sidekick config vars.
	 *
	 * @return void
	 */
	private function sidekick_integration() {

		// Sidekick configuration
		define( 'SK_PRODUCT_ID', 454 );
		define( 'SK_ENVATO_PARTNER', 'iZmD68ShqUyvu7HzjPWPTzxGSJeNLVxGnRXM/0Pqxv4=' );
		define( 'SK_ENVATO_SECRET', 'RqjBt/YyaTOjDq+lKLWhL10sFCMCJciT9SPUKLBBmso=' );
	}

	/**
	 * Other integrations.
	 *
	 * @return void
	 */
	private function other_integrations() {
		kalium()->require_file( 'includes/libraries/dynamic-image-downsize.php' );
		kalium()->require_file( 'includes/libraries/post-link-plus.php' );
	}
}