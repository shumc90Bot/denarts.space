<?php
/**
 * Kalium WordPress Theme
 *
 * About page.
 *
 * @author Laborator
 * @link   https://kaliumtheme.com
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

/**
 * @property string admin_page
 */
class Kalium_About {

	/**
	 * Get link for about page tab.
	 *
	 * @param string $tab
	 *
	 * @return string
	 */
	public static function get_tab_link( $tab ) {
		$url = admin_url( 'admin.php?page=kalium' );

		switch ( $tab ) {

			// Whats new
			case 'whats-new':
				$url .= '&tab=whats-new';
				break;

			// Register theme
			case 'theme-registration':
				$url .= '&tab=theme-registration';
				break;

			// Plugins
			case 'plugins':
				$url .= '&tab=plugins';
				break;

			// Demos
			case 'demos':
				$url .= '&tab=demos';
				break;

			// Demos
			case 'system-status':
				$url .= '&tab=system-status';
				break;

			// Help
			case 'help':
				$url .= '&tab=help';
				break;

			// FAQ
			case 'faq':
				$url .= '&tab=faq';
				break;
		}

		return $url;
	}

	/**
	 * Construct.
	 */
	public function __construct() {

		// Hooks
		add_action( 'admin_menu', [ $this, '_admin_menu' ] );
		add_action( 'admin_menu', [ $this, '_admin_menu_sort' ], 100 );
		add_action( 'admin_menu', [ $this, '_admin_menu_current_item' ], 100 );
		add_action( 'admin_enqueue_scripts', [ $this, '_register_scripts_and_styles' ] );
	}

	/**
	 * Admin menu.
	 *
	 * @return void
	 */
	public function _admin_menu() {

		// About main tab
		add_submenu_page(
			'laborator_options',
			'About',
			'About',
			'edit_theme_options',
			'kalium',
			[
				$this,
				'page_index',
			]
		);

		// Registration
		add_submenu_page( 'laborator_options', 'License', 'License', 'edit_theme_options', 'admin.php?page=kalium&tab=theme-registration' );

		// Plugin updates
		$plugin_updates = kalium_plugin_updates_count();
		$plugins_title  = $plugins_menu_title = 'Plugins';

		if ( $plugin_updates > 0 ) {
			$plugins_title      = 'Update Plugins';
			$plugins_menu_title = sprintf( 'Update Plugins <span class="kalium-update-badge">%d</span>', $plugin_updates );
		}

		// Plugins
		add_submenu_page( 'laborator_options', $plugins_title, $plugins_menu_title, 'edit_theme_options', 'admin.php?page=kalium&tab=plugins' );

		// Demos
		add_submenu_page( 'laborator_options', 'Demos', 'Demos', 'edit_theme_options', 'admin.php?page=kalium&tab=demos' );

		// System status
		add_submenu_page( 'laborator_options', 'System Status', 'System Status', 'edit_theme_options', 'admin.php?page=kalium&tab=system-status' );

		// Help
		add_submenu_page( 'laborator_options', 'Help', 'Help', 'edit_theme_options', 'admin.php?page=kalium&tab=help' );
	}

	/**
	 * Sort admin menu items for Laborator admin menu item.
	 *
	 * @return void
	 */
	public function _admin_menu_sort() {
		global $submenu;

		if ( ! isset( $submenu['laborator_options'] ) || ! is_array( $submenu['laborator_options'] ) ) {
			return;
		}

		// Laborator menu items
		$laborator_menu_items = &$submenu['laborator_options'];

		// Order count
		$order_id = 0;

		// Order table
		$order_menu_items = [
			'Laborator'      => $order_id ++,
			'Typography'     => $order_id ++,
			'License'        => $order_id ++,
			'Plugins'        => $order_id ++,
			'Update Plugins' => $order_id ++,
			'Demos'          => $order_id ++,
			'System Status'  => $order_id ++,
			'Help'           => $order_id ++,
			'About'          => $order_id ++,
		];

		// Sort submenu items
		uasort(
			$laborator_menu_items,
			function ( $a, $b ) use ( $order_menu_items ) {
				if ( isset( $order_menu_items[ $a[3] ], $order_menu_items[ $b[3] ] ) ) {
					return $order_menu_items[ $a[3] ] < $order_menu_items[ $b[3] ] ? - 1 : 1;
				}
			}
		);

		// Rename Laborator to Theme Options
		foreach ( $laborator_menu_items as & $menu_item ) {
			if ( 'Laborator' === $menu_item[3] ) {
				$menu_item[0] = $menu_item[3] = 'Theme Options';
			}
		}
	}

	/**
	 * Set current menu item for Laborator admin menu item.
	 *
	 * @return void
	 */
	public function _admin_menu_current_item() {
		global $submenu;

		if ( ! isset( $submenu['laborator_options'] ) || ! is_array( $submenu['laborator_options'] ) ) {
			return;
		}

		// Laborator menu items
		$laborator_menu_items = &$submenu['laborator_options'];

		// Only on About page
		if ( 'kalium' === $this->admin_page ) {

			// Current tab
			$current_tab = $this->get_current_tab();

			foreach ( $laborator_menu_items as & $menu_item ) {
				$link = wp_parse_args( $menu_item[2] );

				// Current menu item
				if ( kalium_get_array_key( $link, 'tab' ) === $current_tab ) {
					$menu_item[4]    = 'current';
					$current_tab_set = true;
				}
			}

			// Remove current tab for "laborator_options"
			if ( isset( $current_tab_set ) ) {
				foreach ( $laborator_menu_items as & $menu_item ) {
					if ( 'kalium' === $menu_item[2] ) {
						$menu_item[4] = 'tab-not-active';
					}
				}
			}
		}
	}

	/**
	 * Register scripts and styles.
	 *
	 * @return void
	 */
	public function _register_scripts_and_styles() {

		// Enqueue scripts and styles
		if ( 'kalium' === $this->admin_page ) {

			// Enqueue scripts and style
			kalium_enqueue( 'admin-about' );
		}
	}

	/**
	 * About page index.
	 *
	 * @return void
	 */
	public function page_index() {

		// Page title
		$page_title = 'Welcome to Kalium';

		if ( kalium()->request->has( 'updated' ) ) {
			$page_title = 'Kalium has been updated';
		}

		// About text
		$about_text  = 'Kalium theme is installed and activated on your WordPress site.';
		$about_text .= "\n";

		// License status text
		if ( kalium()->theme_license->is_theme_registered() ) {
			$about_text .= 'Product is registered and you will get latest theme and premium plugin updates upon their availability.';
		} else {
			$about_text .= 'We kindly ask you to register the theme to get automatic updates, install premium plugins and demo content.';
		}

		/**
		 * Show custom content from Kalium theme plugins.
		 *
		 * Hook: kalium_page_about.
		 *
		 * @param string $current_tab
		 */
		do_action( 'kalium_page_about', $this->get_current_tab() );

		if ( has_action( 'kalium_page_about' ) ) {
			return;
		}

		?>
		<div class="wrap about-wrap about-kalium">
			<h1><?php echo esc_html( $page_title ); ?></h1>

			<p class="about-text">
				<?php echo esc_html( $about_text ); ?>
			</p>

			<p class="wp-badge wp-kalium-badge">
				Version: <?php echo kalium()->get_version(); ?>
			</p>

			<?php
			// About tabs
			kalium()->require_file(
				'includes/admin-templates/about-tabs.php',
				[
					'page'        => $this->admin_page,
					'tabs'        => $this->get_tabs(),
					'current_tab' => $this->get_current_tab(),
				]
			);

			// Tab content
			$this->tab_content();

			// Footer
			$this->footer();
			?>

		</div>
		<?php
	}

	/**
	 * Tabs list.
	 *
	 * @return array
	 */
	public function get_tabs() {
		return [
			'about'              => 'About',
			'whats-new'          => 'What&#8217;s New',
			'theme-registration' => 'License',
			'plugins'            => 'Plugins',
			'demos'              => 'Demos',
			'system-status'      => 'System Status',
			'help'               => 'Help',
			'faq'                => 'F.A.Q',
		];
	}

	/**
	 * Changelog.
	 *
	 * @return array
	 */
	public function get_changelog() {
		return [

			// Changelog entry
            [
                'expand'  => true,
                'version' => '3.11',
                'date'    => '2023-11-21',
                'changes' => [

                    // New
                    'New'    => [
						'WordPress 6.4.x compatibility added',
                        'WooCommerce 8.3.x compatibility added',
                    ],

                    // Fix
                    'Fix'    => [
                        'Product image gallery disappearing in WooCommerce 8.3.0',
                        'TypoLab producing invalid value font sizes',
                        'Default unit reverting back to pixels in TypoLab when saved',
                        'WPBakery Portfolio widget not showing in custom post type',
                        'Videos that autoplay in portfolio items will have "playsinline" attribute',
                        'Other bug fixes and improvements',
                    ],

                    // Update
                    'Update' => [
                        'Rebranded Twitter to X in social icons',
                        'Google Fonts list updated to the latest version',
                        'Advanced Custom Fields PRO plugin updated to the latest version in theme package – 6.2.3',
                        'WPBakery Page Builder plugin updated to the latest version in theme package – 7.2',
                        'Slider Revolution plugin updated to the latest version in theme package – 6.6.18',
                        'Layer Slider plugin updated to the latest version in theme package – 7.9.5',
                        'VideoJS library updated to latest version – 8.6.1',
                        'Font Awesome library updated to latest version - 6.4.2',
                    ],

					// Note
					'Note'   => [
						'If you want to contribute in our language translations here is our GIT repository: https://github.com/arl1nd/Kalium-Translations',
					],
                ],
            ],

			// Changelog entry
            [
                'expand'  => false,
                'version' => '3.10',
                'date'    => '2023-08-10',
                'changes' => [

                    // New
                    'New'    => [
						'WordPress 6.3 compatibility added',
                        'WooCommerce 8.x compatibility added',
                    ],

                    // Update
                    'Update' => [
                        'Advanced Custom Fields PRO plugin updated to the latest version in theme package – 6.1.8',
                        'WPBakery Page Builder plugin updated to the latest version in theme package – 7.0',
                        'Slider Revolution plugin updated to the latest version in theme package – 6.6.15',
                        'Layer Slider plugin updated to the latest version in theme package – 7.7.11',
                    ],
                ],
            ],

			// Changelog entry
			[
				'expand'  => false,
				'version' => '3.9',
				'date'    => '2023-06-20',
				'changes' => [

					// New
					'New'    => [
						'WooCommerce 7.8.x compatibility added',
					],

					// Fix
					'Fix'    => [
						'Mini-cart functionality broken in WooCommerce 7.8',
                        'Squashed product and category images in Safari',
                        'Slider Revolution refuses to import slides that have .ogv videos',
						'Other bug fixes and improvements',
					],

					// Update
					'Update' => [
						'Advanced Custom Fields PRO plugin updated to the latest version in theme package – 6.1.6',
						'WPBakery Page Builder plugin updated to the latest version in theme package – 6.13.0',
						'Slider Revolution plugin updated to the latest version in theme package – 6.6.14',
						'Layer Slider plugin updated to the latest version in theme package – 7.7.7',
                        'Widget Importer/Exporter library updated to the latest version – 1.6.1',
						'VideoJS library updated to latest version - 8.3.0',
						'Vimeo JS library updated to latest version – 2.20.1',
					],
				],
			],

			// Changelog entry
			[
				'expand'  => false,
				'version' => '3.8',
				'date'    => '2023-04-05',
				'changes' => [

					// New
					'New'    => [
						'WordPress 6.2 compatibility added',
						'WooCommerce 7.5.x compatibility added',
					],

					// Fix
					'Fix'    => [
                        'Parameters and Options style is broken in ACF PRO 6.1.1',
                        'Resolved some Typography warnings',
						'Other bug fixes and improvements',
					],

					// Update
					'Update' => [
						'Advanced Custom Fields PRO plugin updated to the latest version in theme package – 6.1.1',
						'Slider Revolution plugin updated to the latest version in theme package – 6.6.12',
						'Layer Slider plugin updated to the latest version in theme package – 7.6.6',
						'VideoJS library updated to latest version - 8.0.4',
					],
				],
			],

			// Changelog entry
			[
				'expand'  => false,
				'version' => '3.7.1',
				'date'    => '2023-01-20',
				'changes' => [

					// New
					'New'    => [
						'WooCommerce 7.3.x compatibility added',
					],

					// Fix
					'Fix'    => [
						'Performance: Slider Revolution and Layer Slider will not load on all pages (unless set by the user)',
                        'Performance: Google Fonts that are called inside Slider Revolution are now loaded as local fonts by default',
						'Videos always playing muted',
						'Other bug fixes and improvements',
					],

					// Update
					'Update' => [
						'Advanced Custom Fields PRO plugin updated to the latest version in theme package – 6.0.7',
						'Slider Revolution plugin updated to the latest version in theme package – 6.6.8',
						'Layer Slider plugin updated to the latest version in theme package – 7.6.7',
						'Demo Content Importer plugin updated to latest version',
						'Demo Content Packages updated to latest version',
						'Google Fonts list updated to the latest version',
					],
				],
			],

			// Changelog entry
			[
				'expand'  => false,
				'version' => '3.7',
				'date'    => '2022-11-08',
				'changes' => [

					// New
					'New'    => [
						'WordPress 6.1 compatibility added',
						'WooCommerce 7.x compatibility added',
					],

					// Update
					'Update' => [
						'Demo Content Packages updated to latest version',
						'Advanced Custom Fields PRO plugin updated to the latest version in theme package – 6.0.3',
						'WPBakery Page Builder plugin updated to the latest version in theme package – 6.10',
						'Slider Revolution plugin updated to the latest version in theme package – 6.6.5',
						'Layer Slider plugin updated to the latest version in theme package – 7.5.3',
						'Vimeo JS library updated to latest version – 2.18',
						'Danish translations updated, thanks to Karsten Ronge for contribution',
					],

					// Fix
					'Fix'    => [
						'Few bug fixes and improvements',
					],
				],
			],

			// Changelog entry
			[
				'expand'  => false,
				'version' => '3.6',
				'date'    => '2022-09-20',
				'changes' => [

					// Fix
					'Fix'     => [
						'Scrolling issue on iOS devices with Sticky Header enabled',
						'Unstable “Related posts” layout when Post Format is enabled',
						'Header bottom spacing not applied if value is set to zero',
						'Header Builder in Theme Options issue with z-index',
						'Font Squirrel fonts slowing down some servers',
						'After a successful demo import the “View your site” link is not opening',
						'Changing unit for font sizes in Typography not working when the value is empty',
						'Font without any base selector or custom selector will be excluded',
						'Sub menus in Top Header not showing',
						'Failsafe kalium_woocommerce_get_items_in_cart function added',
						'Other bug fixes and improvements',
					],

					// Update
					'Update'  => [
						'WooCommerce 6.9.x compatibility added',
						'Advanced Custom Fields PRO plugin updated to the latest version in theme package – 5.12.3',
						'Layer Slider plugin updated to the latest version in theme package – 7.3',
						'WooCommerce Product Size Guide plugin updated to the latest version in theme package — 3.9',
						'Slider Revolution plugin updated to the latest version in theme package – 6.5.31',
						'Google Fonts list updated to the latest version',
						'Demo Content Packages updated to latest version',
						'VideoJS library updated to latest version - 7.20.3',
						'Vimeo JS library updated to latest version – 2.17.1',
					],

					// Removed
					'Removed' => [
						'Typography font source "Font Squirrel" is no longer supported (they are retired), although existing fonts from "Font Squirrel" that you might have installed will still be available',
					],
				],
			],

			// Changelog entry
			[
				'expand'  => false,
				'version' => '3.5',
				'date'    => '2022-05-18',
				'changes' => [

					// New
					'New'    => [
						'WordPress 6.0 compatibility added',
						'WooCommerce 6.5 compatibility added',
					],

					// Fix
					'Fix'    => [
						'Site logo not showing correctly when .svg image was used',
						'Custom selectors fields not visible in smaller screen in Edit Font page',
						'Compatibility issues on PHP 8.0.x and 8.1',
						'Other bug fixes and improvements',
					],

					// Update
					'Update' => [
						'Advanced Custom Fields PRO plugin updated to the latest version in theme package – 5.12.2',
						'Slider Revolution plugin updated to the latest version in theme package – 6.5.22',
						'Layer Slider plugin updated to the latest version in theme package – 7.2.1',
						'WPBakery Page Builder plugin updated to the latest version in theme package – 6.9',
						'Vimeo JS library updated to latest version – 2.16.4',
						'Font Awesome library updated to latest version - 6.1.1',
					],
				],
			],

			// Changelog entry
			[
				'expand'  => false,
				'version' => '3.4.5',
				'date'    => '2022-02-23',
				'changes' => [

					// New
					'New'    => [
						'WordPress 5.9 compatibility added',
						'WooCommerce 6.3 compatibility added',
						'PHP 8.1 compatibility added (for Kalium files)',
						'Option to show or hide Related Posts in for individual posts – https://d.pr/i/bFT1tn',
						'Performance: Lazy loading is disabled for portfolio and product featured images to increase performance',
						'Option to disable Kalium\'s built-in image lazy loading library - https://d.pr/i/XpUvkd',
					],

					// Fix
					'Fix'    => [
						'Private Vimeo videos not working on portfolio galleries',
						'Missing Adobe fonts causing Internal Server Error (500 error code)',
						'Sidebar alignment theme option not showing in certain cases',
						'ReCaptcha v3 not working after plugin update',
						'WooCommerce Checkout throws Error 500 if WPBakery is not activated',
						'Other bug fixes and improvements',
					],

					// Update
					'Update' => [
						'Advanced Custom Fields PRO plugin updated to the latest version in theme package – 5.11.4',
						'WPBakery Page Builder plugin updated to the latest version in theme package – 6.8',
						'Slider Revolution plugin updated to the latest version in theme package – 6.5.17',
						'Layer Slider plugin updated to the latest version in theme package – 7.1.1',
						'Product Filter for WooCommerce plugin updated to the latest version in theme package – 8.3.0',
						'Flickity library updated to latest version - 2.3.0',
						'Perfect Scrollbar library updated to latest version – 1.5.5',
						'Vimeo JS library updated to latest version – 2.16.3',
						'Translation files updated to latest version',
					],
				],
			],

			// Changelog entry
			[
				'expand'  => false,
				'version' => '3.4.4',
				'date'    => '2021-11-24',
				'changes' => [

					// New
					'New'    => [
						'Blog related posts - https://d.pr/i/m9bBBS',
						'WooCommerce 5.9 compatibility added',
					],

					// Update
					'Update' => [
						'Advanced Custom Fields PRO plugin updated to the latest version in theme package – 5.11.1',
						'Slider Revolution plugin updated to the latest version in theme package – 6.5.11',
						'Product Filter for WooCommerce plugin updated to the latest version in theme package – 8.2.1',
						'Perfect Scrollbar library updated to latest version – 1.5.3',
						'VideoJS library updated to latest version - 7.17.1',
						'Language files updated updated to the latest version',
						'Google Fonts and Font Squirrel font list updated to the latest version',
					],

					// Fix
					'Fix'    => [
						'Preselected portfolio item type in ACF not working - https://d.pr/i/hfMMm3',
						'Minified JS file for jQuery Throttle and Debounce',
						'Other bug fixes and improvements',
					],
				],
			],

			// Changelog entry
			[
				'expand'  => false,
				'version' => '3.4.3',
				'date'    => '2021-10-13',
				'changes' => [

					// New
					'New'    => [
						'WooCommerce 5.8 compatibility added',
						'Performance: Lazy loading is disabled for single blog post featured images to increase page loading score',
						'Performance: Option to preload font awesome icons brands separately – https://d.pr/i/tI5T7D',
					],

					// Fix
					'Fix'    => [
						'Some Google fonts preloading hundreds of variants unnecessarily',
						'Coming soon and maintenance mode page showing page heading title incorrectly',
						'Support for gutenberg content in WPBakery Portfolio Item type',
						'Editing font causing memory limit overflow in certain cases',
						'Fit to viewport videos not working in mobile Safari and Chrome',
						'Other bug fixes and improvements',
					],

					// Update
					'Update' => [
						'Slider Revolution plugin updated to the latest version in theme package – 6.5.9',
						'Layer Slider plugin updated to the latest version in theme package – 6.11.9',
					],
				],
			],

			// Changelog entry
			[
				'expand'  => false,
				'version' => '3.4.2',
				'date'    => '2021-09-10',
				'changes' => [

					// Fix
					'Fix'    => [
						'WooCommerce demo store notice showing unstyled on some pages',
						'Conditional font loading not working properly for post types',
						'WooCommerce block styles not disabled when "Gutenberg Block Library" is disabled',
						'Autohide sticky menu appearing over the content when window is resized',
						'Other minor bug fixes and improvements',
					],

					// Update
					'Update' => [
						'Advanced Custom Fields PRO plugin updated to the latest version in theme package – 5.10.2',
						'Slider Revolution plugin updated to the latest version in theme package – 6.5.8',
						'Product Filter for WooCommerce plugin updated to the latest version in theme package – 8.2.0',
					],
				],
			],

			// Changelog entry
			[
				'expand'  => false,
				'version' => '3.4.1',
				'date'    => '2021-08-18',
				'changes' => [

					// New
					'New'    => [
						'Select default unit for font sizes, line heights and letter spacing – https://d.pr/i/rvtEUn',
						'Performance: Image lazy loading support for WPBakery Clients widget – https://d.pr/i/aGVLBG',
					],

					// Update
					'Update' => [
						'Main Demo content package updated to latest version',
					],

					// Fix
					'Fix'    => [
						'Portfolio single pages showing error 404 (in some cases) when Elementor is active',
						'Portfolio items not showing in Elementor for Kalium Portfolio Items widget',
						'WooCommerce Terms & Conditions showing unformatted WPBakery content',
						'Google reCaptcha not working on Kalium Contact Form',
						'Typography sitewide font incorrectly replacing font-weights for all elements',
						'Typography font size section does not accept decimal numbers for EM/REM units',
						'Font preloading not working for "Font Squirrel" font provider',
						'Other minor bug fixes and improvements',
					],
				],
			],

			// Changelog entry
			[
				'expand'  => false,
				'version' => '3.4',
				'date'    => '2021-08-11',
				'changes' => [

					// New
					'New'     => [
						'Redesigned Typography area: Simplified UI with more customization options and less distractions.',
						'WordPress 5.8 compatibility added',
						'WooCommerce 5.6 compatibility added',
						'Letter-spacing option in Typography',
						'Performance tab in Theme Options area — https://d.pr/i/vWvt0U',
						'Performance: Icon preloading settings in Theme Options – https://d.pr/i/mMzqNn',
						'Performance: Option to disable Gutenberg editor styles',
						'Performance: Option to disable jQuery Migrate script',
						'Performance: Option to disable WordPress Emoji script',
						'Performance: Option to disable WordPress Embed script',
						'Performance: Option to set JPEG image quality – https://d.pr/i/mCifnu',
						'Performance: Option to preload all fonts or specific font-family and make sure no unneeded fonts get preloaded.',
						'Performance: Load Google Fonts locally with one click (Typography > Setting).',
						'Performance: System Fonts font source, use font of your operating system as your website font',
						'Performance: Font Face Rendering options (swap, block, auto, fallback, optional) choose the method that works best for you.',
						'Performance: Image lazy loading support for WPB Single Image and WPB Image Gallery elements – https://d.pr/i/Mw8U49',
						'Performance: VideoJS and YouTube player API load only when is needed in Portfolio areas.',
						'Performance: Portfolio and WooCommerce styles are loaded only when necessary',
					],

					// Improve
					'Improve' => [
						'Adobe Fonts are now much simple to add because of the new API implementation.',
						'SEO: Images from ACF plugin will appear in sitemap generated by Rank Math plugin',
						'Improved performance on some admin pages',
					],

					// Fix
					'Fix'     => [
						'WPML and portfolio items issues with translation',
						'Demo content and theme bundled plugins couldn\'t be installed when FTPEXT filesystem method is used',
						'Videos aspect ratio in portfolio listing page causing incorrect ordering of items',
						'Header search form reverting to default language in some cases',
						'Sticky Header overlapping content when a higher bottom margin is applied',
						'Few shortcodes not supported in the header and top header bar',
						'Featured Video not showing properly on masonry portfolio items page',
						'Portfolio items grid not declaring the aria-label attribute',
						'Short YouTube links (youtu.be) not working on the Lightbox portfolio type',
						'Featured Video not working as it should when autoplay is disabled',
						'Kalium Premium Font (Function Pro) calling unnecessary font variants',
						'Popup Builder plugin causing problems with Theme Options reset options button',
						'Blog categories showing incorrectly sub categories',
						'TikTok icon not showing on Header',
						'Fatal error caused when running Elementor Data Updater',
						'Show More pagination button in Shop not working when jQuery migrate is not included',
						'PHP warning shown on Customizer',
						'Removed WPML Embedder, now backend load less files.',
						'Incorrect scrollbar length in Zig-Zag portfolio item type',
						'Lots of other bug fixes and improvements',
					],

					// Update
					'Update'  => [
						'Slider Revolution plugin updated to the latest version in theme package – 6.5.6',
						'Layer Slider plugin updated to the latest version in theme package – 6.11.8',
						'Advanced Custom Fields PRO plugin updated to the latest version in theme package – 5.9.9',
						'WPBakery Page Builder plugin updated to the latest version in theme package – 6.7',
						'Product Filter for WooCommerce plugin updated to the latest version in theme package – 8.1.1',
						'WooCommerce Product Size Guide plugin updated to the latest version in theme package — 3.7',
						'Perfect Scrollbar library updated to latest version – 1.5.2',
						'Google Fonts and Font Squirrel font list updated to the latest version',
						'Kalium Premium Font (Function Pro) font updated to version 1.1.',
						'Demo Content Packages updated to the latest version',
						'GreenSock Animation Platform updated to latest version – 3.7.1',
						'Perfect Scrollbar library updated to latest version – 1.5.1',
					],
				],
			],

			// Changelog entry
			[
				'expand'  => false,
				'version' => '3.3.1',
				'date'    => '2021-04-06',
				'changes' => [

					// Update
					'Update' => [
						'Slider Revolution plugin updated to the latest version in theme package – 6.4.6',
						'Product Filter for WooCommerce plugin updated to the latest version in theme package – 8.1.0',
						'Lazysizes (lazy loader library) updated to latest version - 5.3.1',
					],

					// Fix
					'Fix'    => [
						'Network administrator couldn\'t install plugins when "install_plugins" capability is granted',
						'Premium fonts preloading unnecessary files on front-end',
						'Portfolio like button not aligned properly in certain portfolio item types',
						'Product sticky description wrong placement when Sticky Header is enabled',
						'Improved accessibility score for better SEO',
						'Minor bug fixes and improvements',
					],
				],
			],

			// Changelog entry
			[
				'expand'  => false,
				'version' => '3.3',
				'date'    => '2021-03-13',
				'changes' => [

					// New
					'New'    => [
						'WordPress 5.7 compatibility added',
						'WooCommerce 5.1 compatibility added',
						'Auto updates support – https://d.pr/i/JooTNK',
						'Font files preloading (supported for theme icons and self hosted fonts)',
						'Share in Telegram link – https://d.pr/i/IkEjwb https://d.pr/i/46XcEn',
					],

					// Update
					'Update' => [
						'Slider Revolution plugin updated to the latest version in theme package – 6.4.3',
						'Layer Slider plugin updated to the latest version in theme package – 6.11.6',
						'WPBakery Page Builder plugin updated to the latest version in theme package – 6.6',
						'Architecture demo content package updated to latest version',
						'VideoJS library updated to latest version - 7.11.4',
						'ImagesLoaded library updated to latest version - 4.1.4',
						'GreenSock Animation Platform updated to latest version – 3.6.0',
						'New color palette for Theme Options matching WordPress 5.7',
						'Google Fonts list updated to latest version',
						'Font Squirrel list updated to latest version',
					],

					// Fix
					'Fix'    => [
						'Ordering of Portfolio Items widget not working in Elementor',
						'Broken grid for columned galleries in blog posts',
						'Content going underneath the Sticky Header in some cases',
						'Fixed to bottom footer not showing in certain cases',
						'Print page link not working in portfolio item page',
						'Product images showing in small size when opened in lightbox',
						'AutoType incorrectly rendering content and adding extra vertical margin',
						'Multiple JavaScript warnings in Theme Options page',
						'Other bug fixes and improvements',
					],
				],
			],

			// Changelog entry
			[
				'expand'  => false,
				'version' => '3.2.1',
				'date'    => '2021-02-17',
				'changes' => [

					// Fix
					'Fix' => [
						'Theme update failing in PHP 8 if backups are enabled',
						'Sticky Header not working properly when Top Header Bar is present',
						'Incorrect direction of prev-next navigation in single Portfolio item',
						'Minor bug fixes and improvements',
					],
				],
			],

			// Changelog entry
			[
				'expand'  => false,
				'version' => '3.2',
				'date'    => '2021-02-12',
				'changes' => [

					// New
					'New'     => [
						'Featured Video for Portfolio Items – https://d.pr/i/TjZdhF',
						'Breadcrumbs Support – https://d.pr/i/xnxWbP https://d.pr/i/Ng4kCj',
						'Line height added in Typography – https://d.pr/i/sR3rUh https://d.pr/i/P1lnou',
						'Add videos in Portfolio Fullscreen item type – https://d.pr/i/YluWKp',
						'Add custom background for Fullscreen Menu - https://d.pr/v/19K3Va https://d.pr/i/Gy5Mjl',
						'Improved animation for the Like feature on portfolio projects – https://d.pr/v/iICLqv',
						'PHP 8.0 compatibility added (for Kalium files)',
						'WooCommerce 5.0 compatibility added',
						'Phone and TikTok social network added – https://d.pr/i/Wkhv2b',
						'Retina option for Single Image and Clients WPBakery Page Builder elements – https://d.pr/i/AErnOh https://d.pr/i/xlA07S',
						'More layout types for Blog posts element in WPBakery - https://d.pr/i/kFNeGW https://d.pr/i/988el4',
						'Added opacity on hover effect for Client logos element in WPBakery Page Builder - https://d.pr/i/9k6vpJ',
						'Support for adjustable image size for single and catalog product image size – https://d.pr/i/iAXL49',
					],

					// Update
					'Update'  => [
						'Advanced Custom Fields PRO plugin updated to the latest version in theme package – 5.9.5',
						'Slider Revolution plugin updated to the latest version in theme package – 6.3.9',
						'Layer Slider plugin updated to the latest version in theme package – 6.11.5',
						'WPBakery Page Builder plugin updated to the latest version in theme package – 6.5',
						'Product Filter for WooCommerce plugin updated to the latest version in theme package – 8.0.2',
						'Google Fonts list updated to latest version',
						'Font Squirrel list updated to latest version',
						'Font Awesome library updated to latest version - 5.15.2',
						'Lazysizes (lazy loader library) updated to latest version - 5.3.0',
						'Flickity library updated to latest version - 2.2.2',
					],

					// Fix
					'Fix'     => [
						'Google Maps randomly not loading/showing in page',
						'Search input in header not hidden when moving the focus out of input field',
						'Compatibility fix for "reCaptcha by BestWebSoft" plugin',
						'Pingbacks not styled in the comment section',
						'Sticky description on portfolio hidden behind the sticky header',
						'Sticky Header skin color issue on Safari/iOS',
						'Excerpts in search page showing images',
						'Lots of improvements and reported bug fixes',
					],

					// Removed
					'Removed' => [
						'Template file "tpls/page-heading-title.php" is replaced with "global/page-heading.php"',
					],
				],
			],

			// Changelog entry
			[
				'expand'  => false,
				'version' => '3.1.3',
				'date'    => '2020-12-11',
				'changes' => [

					// Hotfix
					'Hotfix' => [
						'Infinite scroll pagination not working in WordPress 5.6',
						'Shop page infinite scroll throwing fatal PHP error',
						'Product variation image not changed on variation selection',
					],

					// Update
					'Update' => [
						'Slider Revolution plugin updated to the latest version in theme package – 6.3.3',
						'Product Filter for WooCommerce plugin updated to the latest version in theme package – 8.0.1',
					],

					// Fixes
					'Fix'    => [
						'Deprecated function warning in Cart page',
						'Arrows not showing on Lightbox Portfolio',
						'Excluded post types from search not working – https://d.pr/i/BHZQWa',
						'Theme Options failing to import for some users',
						'Minor bug fixes and improvements',
					],
				],
			],

			// Changelog entry
			[
				'expand'  => false,
				'version' => '3.1',
				'date'    => '2020-11-27',
				'changes' => [

					// New
					'New'     => [
						'WordPress 5.6 compatibility added',
						'WooCommerce 4.8 compatibility added',
						'Added Top Bar on some of the demos such as Bookstore, Medical, Law, Automotive, Construction and Fitness',
						'New widgets in Top Header Bar: Breadcrumb, Date, My Account, Search Field',
						'Dominant image color on hover for portfolio items – https://d.pr/i/lriLxy',
						'Support for multiple contact form email receivers separated by comma',
						'Font Awesome 5 icons',
					],

					// Update
					'Update'  => [
						'WPBakery Page Builder plugin updated to the latest version in theme package – 6.4.2',
						'Advanced Custom Fields PRO plugin updated to the latest version in theme package – 5.9.3',
						'Slider Revolution plugin updated to the latest version in theme package – 6.3.1',
						'Product Filter for WooCommerce plugin updated to the latest version in theme package – 7.3.4',
						'Demo Content Packages updated to latest version',
						'Google Fonts list updated to latest version',
						'Font Squirrel list updated to latest version',
						'VideoJS library updated to latest version - 7.10.2',
						'Light Gallery library updated to latest version - 1.10.0',
						'GreenSock Animation Platform updated to latest version – 3.4.2',
						'Vimeo JS library updated to latest version – 2.14.1',
						'Animate.css library updated to latest version – 4.1.1',
					],

					// Fix
					'Fix'     => [
						'Premium fonts could not be installed in Windows operating system',
						'Automotive and Main demo content failing to import Theme Options',
						'Standard menu items broken into rows when no menu is selected to show',
						'Post format "Link" not opened in _blank target when specified',
						'Portfolio gallery columns gap creating horizontal scroll in mobile devices',
						'Kalium Contact Form now supports ReCaptcha v3',
						'Sticky Header logo switch not working with Slider Revolution',
						'Share post to social networks not supporting special characters',
						'Top Header Bar responsive issues on smaller screen sizes',
						'Menu items showing for Toggle Menu Items menu type when no menu is selected in Appearance',
						'Lots of improvements and reported bug fixes',
					],

					// Removed
					'Removed' => [
						'Font Awesome 4 icon files from assets directory',
					],
				],
			],

			// Changelog entry
			[
				'expand'  => false,
				'version' => '3.0.8.1',
				'date'    => '2020-10-13',
				'changes' => [

					// Fix
					'Fix' => [
						'Reverted theme registration in 3.0.8 in some particular PHP versions',
						'Dark and white skin not applied for Standard Menu',
					],
				],
			],

			// Changelog entry
			[
				'expand'  => false,
				'version' => '3.0.8',
				'date'    => '2020-10-10',
				'changes' => [

					// New
					'New'    => [
						'Custom container width (global or specific page) - https://d.pr/i/KCLpsJ - https://d.pr/i/8WvwxY',
						'Dynamic portfolio heading title/description - https://d.pr/i/iuUQfu',
						'WooCommerce 4.6 compatibility',
						'WPML 4.4 compatibility',
					],

					// Update
					'Update' => [
						'WPBakery Page Builder plugin updated to the latest version in theme package – 6.4.1',
						'Slider Revolution plugin updated to the latest version in theme package – 6.2.23',
					],

					// Fix
					'Fix'    => [
						'Support for Cloud Typography by typography.com multiple font variants in TypoLab – https://d.pr/i/0HXzY4',
						'Theme Registration not working on IONOS hosted sites',
						'Sticky Description in portfolio prevents scrolling on Android devices',
						'Bookstore demo content failing to import Theme Options',
						'Page scroll menu links not working with WPBakery columns',
						'Sticky Header not applying skin/color for textual logo',
						'Slider Revolution not supported in Portfolio Gallery in Portfolio Items',
						'Bug fixes and improvements',
					],
				],
			],

			// Changelog entry
			[
				'expand'  => false,
				'version' => '3.0.7',
				'date'    => '2020-09-11',
				'changes' => [

					// New
					'New'    => [
						'WooCommerce 4.5 compatibility added',
					],

					// Update
					'Update' => [
						'WPBakery Page Builder plugin updated to the latest version in theme package – 6.3',
						'Slider Revolution plugin updated to the latest version in theme package – 6.2.22',
						'Advanced Custom Fields PRO plugin updated to the latest version in theme package – 5.9.1',
						'Product Filter for WooCommerce plugin updated to the latest version in theme package – 7.3.3',
						'Google Fonts list updated to latest version, 12 new fonts added',
						'Font Squirrel list updated to latest version, 1 new font added',
						'WPML Embed library updated to latest version - 2.4.4',
						'Automotive and Bookstore demo content packs updated to latest version',
						'VideoJS library updated to latest version - 7.8.4',
						'Light Gallery library updated to latest version - 1.7.3',
						'ScrollMagic library updated to latest version - 2.0.8',
					],

					// Fix
					'Fix'    => [
						'Scroll position bouncing when "Load More" button is clicked',
						'AutoType text not working on new page title containers',
						'Portfolio gallery custom items spacing not applied on mobile',
						'YouTube videos not showing in portfolio item pages (portfolio gallery)',
						'Portfolio like button throwing JS error',
						'Some color picker inputs in Theme Options not showing properly',
						'Scrollbar showing when fullscreen menu is active',
						'Mobile menu showing on mobile devices after rotating viewport',
						'Portfolio lightbox not reacting to Back button',
						'Bug fixes and improvements',
					],
				],
			],

			// Changelog entry
			[
				'expand'  => false,
				'version' => '3.0.6',
				'date'    => '2020-08-13',
				'changes' => [

					// New
					'New'    => [
						'WordPress 5.5 compatibility added',
						'WooCommerce 4.4 compatibility added',
						'Support for YITH WooCommerce Badge Management plugin',
					],

					// Update
					'Update' => [
						'Slider Revolution plugin updated to the latest version in theme package – 6.2.18',
						'Layer Slider plugin updated to the latest version in theme package – 6.11.2',
						'Advanced Custom Fields PRO plugin updated to the latest version in theme package – 5.8.13',
					],

					// Fix
					'Fix'    => [
						'Broken Parameters and Options layout in WordPress 5.5',
						'Maximum call stack exceed error when clicking Theme Options save button',
						'Theme translations reappearing when they are already updated',
						'Portfolio Image Slider not working correctly on RTL languages',
						'Minor bug fixes and improvements',
					],

					// Note
					'Note'   => [
						'If WooCommerce releases a plugin update that is newer than version 4.4 please wait for compatible theme update.',
						'If you want to contribute in our language translations here is our GIT repository: https://github.com/arl1nd/Kalium-Translations',
					],
				],
			],

			// Changelog entry
			[
				'expand'  => false,
				'version' => '3.0.5',
				'date'    => '2020-07-02',
				'changes' => [

					// New
					'New'    => [
						'WooCommerce 4.3 compatibility added',
						'Structured Data management on theme level via "kalium_structured_data_for_page" hook',
					],

					// Update
					'Update' => [
						'Slider Revolution plugin updated to the latest version in theme package – 6.2.15',
						'WooCommerce Product Size Guide plugin updated to the latest version in theme package – 3.6',
						'GSAP library updated to latest version - 3.3.3',
						'Demo Content Packages updated to latest version',
					],

					// Fix
					'Fix'    => [
						'Fixed an XSS issue on contact form page, thanks to Mohamed O. Medo for reporting this security glitch',
						'Fixed Structured Data "Organization" issue reported on Google Structured Data Testing tool',
						'Fixed issue with 2 non-unique IDs on Theme Options page – https://d.pr/i/xIws5Z',
						'WPB Laborator Button element now supports icons with <i> markup',
						'Fatal error appearing (kalium-base.php) after previous update on some servers only',
						'Wrong ordering of header menu elements for standard header type',
						'Fullscreen menu toggle not visible when skin color is to main theme color',
						'PHP warning appearing on 404 page when portfolio prefix is not present',
						'Portfolio item link not working for reverse hover state option in Masonry Mode',
						'Mobile menu not showing close icon (X)',
						'No menu showing on 768 pixels viewport size',
						'Image loading placeholder color not applied on some preloaders',
						'WPBakery Page Builder content not showing on blog page',
						'Dropdown menu color not inheriting the color from custom skin',
						'PHP warning showing when WPML plugin is active and Custom Header is in use',
						'Header search input not working the second time you click search icon',
						'Mobile menu breakpoint causing few issues in new Custom Header builder',
						'Mini cart (on header) not showing on hover the content',
						'Parent menu link in fullscreen menu not working',
						'Save Changes button (on top) in Theme Options not working when clicking save',
					],
				],
			],

			// Changelog entry
			[
				'expand'  => false,
				'version' => '3.0.4',
				'date'    => '2020-06-23',
				'changes' => [],
			],

			// Changelog entry
			[
				'expand'  => false,
				'version' => '3.0.3',
				'date'    => '2020-06-13',
				'changes' => [

					// Fix
					'Fix' => [
						'Issues with menu toggle (hamburger menu) on 3.0.2',
					],
				],
			],

			// Changelog entry
			[
				'expand'  => false,
				'version' => '3.0.2',
				'date'    => '2020-06-12',
				'changes' => [

					// Update
					'Update' => [
						'WooCommerce 4.2 compatibility added',
						'Slider Revolution plugin updated to the latest version in theme package – 6.2.12',
						'Lazysizes (lazy loader library) updated to latest version - 5.2.2',
						'VideoJS library updated to latest version - 7.8.2',
						'WPML Embed library updated to latest version - 2.4.1',
					],

					// Fix
					'Fix'    => [
						'WPML translation support for Header Builder entries with raw text',
						'WPBakery Page Builder "Isotope" library conflict with Kalium\'s "Isotope" library',
						'Top header bar showing incorrectly when absolute header position is selected',
						'Added predefined selectors for Custom Header Builder in Typography – https://d.pr/i/MOYBcM',
						'Sticky header extra offset on devices where it is non-active for scrollable links',
						'Cart totals (top header bar widget) causing JS error on first page load',
						'Horizontal scroll appearing in Columned Portfolio Item when gap is over 50 pixels',
						'Products with single image showing unnecessary thumbnail carousel',
						'Fullscreen background color not changing with custom skin color',
						'Custom Skin improvements for Header Builder',
						'Minor bug fixes and improvements',
					],
				],
			],

			// Changelog entry
			[
				'expand'  => false,
				'version' => '3.0.1',
				'date'    => '2020-05-14',
				'changes' => [

					// New
					'New'    => [
						'Reload theme registration status - https://d.pr/i/GJ9AX4',
					],

					// Update
					'Update' => [
						'Advanced Custom Fields PRO plugin updated to the latest version in theme package – 5.8.12',
						'Slider Revolution plugin updated to the latest version in theme package – 6.2.6',
						'GSAP library updated to latest version - 3.2.6',
						'Google Fonts list updated to latest version, 3 new fonts added',
						'Changelog document in download file is now moved to Laborator online site (shortcut included)',
					],

					// Fix
					'Fix'    => [
						'Top header bar not displayed except for Custom Header type',
						'WPBakery gallery lightbox link not working after 3.0',
						'PHP warning showing on WP admin for non-administrators',
						'An issue with theme registration page which would not allow users to register the theme',
						'Sticky logo switch for specific sections not working',
						'Fullscreen menu showing the first created menu instead of main menu',
						'Custom menu skin not applied when Custom Header type is selected - https://d.pr/i/HUiwi6',
						'Increased number of allowed widgets in Custom Header Builder from 3 to 6 widgets per column',
						'Portfolio before/after image comparison not working on touch devices',
						'Header mini-cart not showing selected variant added on cart',
						'Plugin updates count badge not showing in admin menu',
						'Responsive issues with Centered Header type',
						'WPBakery content not supported in 3.0 in Blog page',
						'Portfolio sub category filter description not displayed on select',
						'Links not clickable in HTML gallery field type when lightbox images are disabled',
						'Incorrect background color applied for Fullscreen menu when color palette is set to Dark',
						'Mobile menu toggle button not shown for plain menus in Custom Header Builder',
						'Fixed an issue with itemprop="url" reported on Google Structured Data Testing tool',
						'Fixed an issue with Theme Registration popup on Microsoft IIS servers',
						'Fixed few words in German translations (thanks to Jens Olaf)',
					],
				],
			],

			// Changelog entry
			[
				'expand'  => false,
				'version' => '3.0',
				'date'    => '2020-05-01',
				'changes' => [

					// New
					'New'        => [
						'Build your header with our new Custom Header Builder',
						'Portfolio Items elementor widget – https://d.pr/i/e9OZgJ',
						'Top Header Bar with 5 elements types (above header) - https://d.pr/i/fNXYfK',
						'Centered Header implementation',
						'New Demo Content Importer with new features and uninstall support',
						'Theme plugins manager page, easy theme plugins management – https://d.pr/i/bCYBMN',
						'Restructured "Kalium About" admin page for better control of the theme',
						'Sticky Header reimplementation: faster, smaller and more features',
						'Theme Options got a new refreshing look with icons and accessibility improvements',
						'Extended Theme API with new functions, classes and WP hooks',
						"New default font for the theme, it doesn't load from external website",
						'Bulk actions for selected fonts in Typography - https://d.pr/i/WDeuWy',
						'Conditional loading of theme style files, up to 30% smaller CSS load size',
						'Conditional loading of theme JS files, up to 75% smaller JS load size',
						'Decreased theme execution time in PHP (around 40%)',
						'WordPress 5.4 compatibility added',
						'WooCommerce 4.1 compatibility added',
						'PHP 7.4 compatibility added (for Kalium files)',
						'Password reveal button on WooCommerce password inputs - https://d.pr/v/dmf0a6',
						'GreenSock Animation Platform 3 implementation',
						'Romanian translation added (contributor Mihai Poenaru)',
						'Restructured code base for code readability, usefulness and speed',
						'Recaptcha support for WPB Contact Form element – https://d.pr/i/Rm9b3y',
						'Sticky menu logo switch for specific slide in Slider Revolution - https://d.pr/i/XvLtNJ',
						'Added wp_body_open hook support after &lt;body&gt; tag opens',
						'Set custom user text in single post author info – https://d.pr/v/ICPukY',
						'Option to show gallery above description on Columned Portfolio item - https://d.pr/i/e7i9QR',
						'Option to set Sticky Header transition progress on mouse wheel or smooth tween – https://d.pr/i/sBsvaa',
					],

					// Update
					'Update'     => [
						'Advanced Custom Fields PRO plugin updated to the latest version in theme package – 5.8.9',
						'Product Filter for WooCommerce plugin updated to the latest version in theme package – 7.2.9',
						'Product Size Guide plugin updated to the latest version in theme package – 3.5',
						'Layer Slider plugin updated to the latest version in theme package – 6.11.1',
						'WPBakery Page Builder plugin updated to the latest version in theme package – 6.2',
						'Google Fonts list updated to latest version, 28 new fonts added',
						'Font Squirrel list updated to latest version, 18 new fonts added',
						'VideoJS library updated to latest version - 7.8.1',
						'Lazysizes (lazy loader library) updated to latest version - 5.2.0',
						'Perfect Scrollbar library updated to latest version – 1.5.0',
					],

					// Fix
					'Fix'        => [
						'Caption links in portfolio gallery not clickable when lightbox is disabled',
						'Common issues with sticky header',
						'Extra class not added for Clients WPB element',
						'Product gallery images not aligned properly on mobile',
						'ACF 5.8.9 breaking the style of Kalium grouped meta boxes',
						'Portfolio filter pulling out default description on custom Portfolio Page template',
						'Portfolio reverse thumbnail hover layer not working',
						'WooCommerce register form password layout broken',
						'Improvements on System Status page for more informative server report',
						'Fixed notices and warnings that appear on PHP 7.4.2 (for Kalium files)',
						'WPBakery Page Builder not showing by default for portfolio projects',
						'Infinite scroll not loading all the portfolio items',
						'Lots of improvements and reported bug fixes',
					],

					// Deprecated
					'Deprecated' => [
						'Template files in "kalium/tpls" folder are deprecated, new location is "kalium/templates"',
						'Deprecated theme PHP functions: get_data(), get_array_key(), generate_custom_style(), laborator_get_svgs()',
					],
				],
			],

			// Changelog entry
			[
				'version' => '2.9.4',
				'date'    => '2019-11-05',
			],

			// Changelog entry
			[
				'version' => '2.9.3',
				'date'    => '2019-10-24',
			],

			// Changelog entry
			[
				'version' => '2.9.2',
				'date'    => '2019-08-16',
			],
		];
	}

	/**
	 * Help sections.
	 *
	 * @return array
	 */
	public function get_help_links() {
		return [
			[
				'icon'  => '1-social-profile-click',
				'title' => 'Getting Started',
				'link'  => 'https://documentation.laborator.co/item/kalium/getting-started-kalium/',
			],
			[
				'icon'  => '2-password-desktop-approved',
				'title' => 'Installation',
				'link'  => 'https://documentation.laborator.co/item/kalium/installation/',
			],
			[
				'icon'  => '3-layout-module',
				'title' => 'Portfolio',
				'link'  => 'https://documentation.laborator.co/item/kalium/portfolio-kalium/',
			],
			[
				'icon'  => '4-layout-content',
				'title' => 'Blog',
				'link'  => 'https://documentation.laborator.co/item/kalium/blog-kalium/',
			],
			[
				'icon'  => '5-layout-top1',
				'title' => 'Header',
				'link'  => 'https://documentation.laborator.co/item/kalium/header/',
			],
			[
				'icon'  => '6-layout-top2',
				'title' => 'Footer',
				'link'  => 'https://documentation.laborator.co/item/kalium/footer-kalium/',
			],
			[
				'icon'  => '7-browser-page-text-1',
				'title' => 'Pages &amp; Elements',
				'link'  => 'https://documentation.laborator.co/item/kalium/pages-elements/',
			],
			[
				'icon'  => '8-ui-browser-slider',
				'title' => 'Sliders',
				'link'  => 'https://documentation.laborator.co/item/kalium/sliders-kalium/',
			],
			[
				'icon'  => '9-color-rolling-brush',
				'title' => 'Theme Styling',
				'link'  => 'https://documentation.laborator.co/item/kalium/theme-styling/',
			],
			[
				'icon'  => '10-font-size',
				'title' => 'Typography',
				'link'  => 'https://documentation.laborator.co/item/kalium/typography/',
			],
			[
				'icon'  => '11-shopping-cart-full',
				'title' => 'Shop',
				'link'  => 'https://documentation.laborator.co/item/kalium/shop-kalium/',
			],
			[
				'icon'  => '12-chat-translate',
				'title' => 'Translation',
				'link'  => 'https://documentation.laborator.co/item/kalium/translation/',
			],
			[
				'icon'  => '13-cog-double-2',
				'title' => 'Settings',
				'link'  => 'https://documentation.laborator.co/item/kalium/other-settings-kalium/',
			],
			[
				'icon'  => '14-question-circle',
				'title' => 'F.A.Q.',
				'link'  => 'https://documentation.laborator.co/item/kalium/faq/',
			],
		];
	}

	/**
	 * FAQ articles.
	 *
	 * @return array
	 */
	public function get_faq_articles() {
		return [

			// What are the requirements for using Kalium?
			[
				'id'      => 'theme-requirements',
				'title'   => 'Server requirements for Kalium',
				'content' => 'Kalium requirements can be found in our documentation site, click the link below to learn more.',
				'link'    => 'https://documentation.laborator.co/kb/kalium/kalium-server-requirements',
			],

			// Activating the theme
			[
				'id'      => 'kalium-registration',
				'title'   => 'Kalium license registration',
				'content' => 'Your Kalium purchase requires <strong>license registration</strong> to install demo content, premium plugin updates and automatic theme updates. You can easily register your purchase of Kalium on the <a href="' . admin_url( 'admin.php?page=kalium&amp;tab=theme-registration' ) . '">License</a> tab.',
				'link'    => 'https://documentation.laborator.co/kb/kalium/activating-the-theme/',
			],

			// Reset license
			[
				'id'      => 'reset-license',
				'title'   => 'Reset license',
				'content' => 'If you have registered Kalium in a staging domain and want to move it to your final domain, you can reset the registration. To do that, submit <a href="https://laborator.ticksy.com/" target="_blank" rel="noreferrer noopener">a ticket</a> and include your purchase code.',
				'link'    => '',
			],

			// Recommended plugins
			[
				'id'      => 'recommended-plugins',
				'title'   => 'Required and recommended plugins for Kalium',
				'content' => 'Kalium can be used itself without any additional plugin. However, to utilize all the features Kalium offers, <strong>Advanced Custom Fields Pro</strong> and <strong>WPBakery Page Builder</strong> plugins must be installed and activated. 
		
		The plugins mentioned above are fundamental in order to use core theme features as demonstrated in <a href="https://kaliumtheme.com" target="_blank">our demo sites</a>. 
		
		Recommended plugins are either premium plugins we bundle with the theme such as <em>Slider Revolution</em> and <em>Layer Slider</em>, or other Kalium compatible plugins such as <em>WooCommerce</em>.
		
		Some of these plugins can be installed on the <strong>Appearance</strong> &gt; <strong>Install Plugins</strong> section.',
				'link'    => 'https://documentation.laborator.co/kb/general/installing-and-updating-premium-plugins/',
			],

			// Importing demo content
			[
				'id'      => 'demo-content-import',
				'title'   => 'Importing demo content',
				'content' => 'Firstly, you need to <strong>register the theme</strong> in order to import any of demo content packages for Kalium.
		
		After you have successfully registered the theme, you can import demo content from <strong>Laborator &gt; Demos</strong> and choose any of the demo content packages available.
		
		For detailed instructions click on the link below to learn more.',
				'link'    => 'https://documentation.laborator.co/kb/kalium/demo-content-import/',
			],

			// Before updating to new woocommerce
			[
				'id'      => 'updating-woocommerce',
				'title'   => 'Before updating to new WooCommerce version',
				'content' => 'Every time when there is new update for WooCommerce, make sure that Kalium is compatible with that version <em>(in our <a href="https://1.envato.market/KYm9a" target="_blank" rel="noopener">item page</a>)</em> before updating to latest version of WooCommerce.
		
		Kalium is fully compatible with WooCommerce and it takes few days to release a compatibility patch for WooCommerce, especially when there is a big update.',
				'link'    => 'https://documentation.laborator.co/kb/general/theme-contains-outdated-copies-of-some-woocommerce-template-files/',
			],

			// Regenerate thumbnails
			[
				'id'      => 'regenerate-thumbnails',
				'title'   => 'Regenerate thumbnails',
				'content' => 'If your thumbnails are not correctly cropped, you can regenerate them by following these steps:
		
		<ul>
			<li>Go to <strong>Plugins > Add New</strong></li>
			<li>Search for <strong>Regenerate Thumbnails</strong> (created by Viper007Bond)</li>
			<li>Install and activate that plugin</li>
			<li>Go to <strong>Tools > Regen. Thumbnails</strong></li>
			<li>Click <strong>Regenerate All Thumbnails</strong> button and let the regeneration process <strong>finish to 100%</strong></li>
		</ul>',
				'link'    => 'https://documentation.laborator.co/kb/kalium/regenerate-thumbnails-kalium/',
			],

			// Flush rewrite rules
			[
				'id'      => 'flush-rewrite-rules',
				'title'   => 'Flush rewrite rules',
				'content' => 'Flushing rewrite rules is required when you are receiving <strong>error 404</strong> on pages you know they exist or you activate any new plugin and its not accessible on front-end. 
		
		This is a simple task and you don’t need to change anything, just click a button. On your admin page go to <strong>Settings &gt; Permalinks</strong> and click <strong>Save Changes</strong> button, thats all.',
				'link'    => 'https://documentation.laborator.co/kb/kalium/flush-rewrite-rules/',
			],

			// Google API key
			[
				'id'      => 'google-map-not-displaying',
				'title'   => 'Google map is not displaying',
				'content' => 'Google maps requires an <strong>API key</strong> in order to show the map. 
		
		If you see an error: <em>Ooops! Something went wrong...</em> then you have to add a Google API key to your site that will allow you to use Google maps. Click the link below to learn more.',
				'link'    => 'https://documentation.laborator.co/kb/kalium/fix-the-missing-google-maps-api-key/',
			],

			// Google API key
			[
				'id'      => 'speed-up-the-site',
				'title'   => 'How to speed up the site',
				'content' => 'Recommendations to speed up the site can be found on the link below.',
				'link'    => 'https://documentation.laborator.co/kb/kalium/how-to-speed-up-my-site/',
			],

			// Google API key
			[
				'id'      => 'custom-css-not-being-apploed',
				'title'   => 'Custom CSS is not being applied',
				'content' => 'This issue mainly happens when you have forgotten to add a closing/opening bracket <strong>{</strong> or <strong>}</strong> in your CSS code or when other CSS rule is taking the precedence over yours and <strong>!important</strong> is not applied.

Another reason why Custom CSS might not be applied is because it is disabled on <a href="admin.php?page=laborator_custom_css&tab=settings">Settings</a> section which is intended to use for debug purposes.',
				'link'    => 'https://documentation.laborator.co/kb/kalium/custom-css-is-not-being-applied/',
			],
		];
	}

	/**
	 * Get current tab.
	 *
	 * @return string
	 */
	private function get_current_tab() {
		$current_tab    = kalium()->request->query( 'tab', 'about' );
		$available_tabs = array_keys( $this->get_tabs() );

		if ( in_array( $current_tab, $available_tabs ) ) {
			return $current_tab;
		}

		return reset( $available_tabs );
	}

	/**
	 * Tab content.
	 *
	 * @return void
	 */
	private function tab_content() {

		// Vars
		$support_remaining       = kalium()->theme_license->get_remaining_support();
		$supported_until         = kalium()->theme_license->get_license()->supported_until ?? null;
		$support_nearly_expiring = kalium()->theme_license->nearly_expiring();
		$is_theme_registered     = kalium()->theme_license->is_theme_registered();

		// Current user
		$user_id      = get_current_user_id();
		$current_user = wp_get_current_user();

		// Calculate remaining support
		$support_left = human_time_diff( strtotime( $supported_until ), time() ) . ' left';

		// Support status
		$support_status       = 'Supported';
		$support_status_class = 'supported';
		$support_badge_class  = 'success';

		// Renew support button
		$buy_support_button = sprintf( '<a href="%1$s" class="button about-kalium__purchase-another-license renew-support-button" target="_blank" rel="noopener">%2$s <span class="kalium-admin-icon-arrow-right"></span></a>', kalium()->theme_license->get_renew_support_link(), kalium()->theme_license->get_renew_support_title() );

		if ( 'help' === $this->get_current_tab() ) {
			$buy_support_button = '';
		}

		// Nearly expiring
		if ( $support_nearly_expiring ) {
			$support_left         = sprintf( 'Support for the Kalium theme is about to expire (%s left) %s', human_time_diff( strtotime( kalium()->theme_license->get_license()->supported_until ?? null ), time() ), $buy_support_button );
			$support_status       = 'Expiring soon';
			$support_status_class = 'expiring';
			$support_badge_class  = 'warning';
		} // Expired support
		elseif ( 0 === $support_remaining ) {
			$support_left         = 'Support subscription for the Kalium theme has expired!' . $buy_support_button;
			$support_status       = 'Expired';
			$support_status_class = 'expired';
			$support_badge_class  = 'danger';
		}

		// Validate license link
		$validate_license_link = wp_nonce_url( add_query_arg( 'action', 'validate-theme-registration' ), 'validate-theme-registration' );

		// Wrapper start
		echo '<div class="about-kalium__tab-content">';

		// Tab content
		switch ( $this->get_current_tab() ) {

			// About tab
			case 'about':
				kalium()->require_file(
					'includes/admin-templates/about/about.php',
					[
						'welcome'             => kalium()->request->has( 'welcome' ),
						'version'             => kalium()->get_version(),
						'is_theme_registered' => $is_theme_registered,
						'display_name'        => $user_id ? $current_user->display_name : null,
					]
				);
				break;

			// What's new tab
			case 'whats-new':
				kalium()->require_file(
					'includes/admin-templates/about/whats-new.php',
					[
						'version'   => kalium()->get_version(),
						'changelog' => $this->get_changelog(),
					]
				);
				break;

			// Theme registration tab
			case 'theme-registration':
				// Enqueue theme registration
				kalium_enqueue( 'theme-registration-js' );

				// Theme is registered
				if ( $is_theme_registered ) {

					// Enqueue Tooltipster
					kalium_enqueue( 'tooltipster' );

					// License
					$license = kalium()->theme_license->get_license();

					// Load template
					kalium()->require_file(
						'includes/admin-templates/about/product-registered.php',
						[
							'licensed_domain'          => $license->domain,
							'licensee'                 => $license->licensee,
							'license_key'              => kalium()->theme_license->get_license_key(),
							'registration_date'        => date_i18n( 'F j, Y - H:i', $license->timestamp ),
							'support_left'             => $support_left,
							'support_status'           => $support_status,
							'support_status_class'     => $support_status_class,
							'support_badge_class'      => $support_badge_class,
							'support_nearly_expiring'  => $support_nearly_expiring,
							'support_remaining'        => $support_remaining,
							'validate_license_link'    => $validate_license_link,
							'theme_backups'            => $this->get_saved_backups(),
							'remove_registration_link' => add_query_arg(
								[
									'action' => 'remove-theme-registration',
									'_nonce' => wp_create_nonce( 'remove-theme-registration' ),
								]
							),
						]
					);
				} // Register theme
				else {

					// Load template
					kalium()->require_file( 'includes/admin-templates/about/product-registration.php' );

					/**
					 * Hook: kalium_theme_registration_tab.
					 */
					do_action( 'kalium_theme_registration_tab' );
				}
				break;

			// Plugins install tab
			case 'plugins':
				kalium()->require_file(
					'includes/admin-templates/about/plugins.php',
					[
						'is_theme_registered' => kalium()->theme_license->is_theme_registered(),
					]
				);
				break;

			// Demo install tab
			case 'demos':
				kalium()->require_file( 'includes/admin-templates/about/demos.php' );
				break;

			// System status tab
			case 'system-status':
				// Enqueue Tooltipster
				kalium_enqueue( 'tooltipster' );

				// Clipboard
				wp_enqueue_script( 'clipboard' );

				// Init system status vars
				Laborator_System_Status::init_vars();

				// Load template
				kalium()->require_file(
					'includes/admin-templates/about/system-status.php',
					[
						'active_plugins' => Laborator_System_Status::get_active_plugins(),
					]
				);
				break;

			// Help tab
			case 'help':
				// Load template
				kalium()->require_file(
					'includes/admin-templates/about/help.php',
					[
						'help_links'              => $this->get_help_links(),
						'is_theme_registered'     => $is_theme_registered,
						'validate_license_link'   => $validate_license_link,
						'support_remaining'       => $support_remaining,
						'support_nearly_expiring' => $support_nearly_expiring,
						'support_left'            => $support_left,
						'support_status'          => $support_status,
						'support_status_class'    => $support_status_class,
						'support_badge_class'     => $support_badge_class,
					]
				);
				break;

			// FAQ's tab
			case 'faq':
				kalium()->require_file(
					'includes/admin-templates/about/faq.php',
					[
						'faq_articles' => $this->get_faq_articles(),
					]
				);
				break;
		}

		// Wrapper end
		echo '</div>';
	}

	/**
	 * About footer.
	 *
	 * @return void
	 */
	private function footer() {
		?>
		<div class="about-kalium__footer wp-clearfix">
			<div class="about-kalium__footer-column">
				Copyright &copy; <?php echo date_i18n( 'Y' ); ?> &ndash; Kalium theme by
				<a href="https://laborator.co" target="_blank" rel="noreferrer noopener">Laborator</a>
			</div>

			<div class="about-kalium__footer-column about-kalium__footer-column--right">
				<ul class="about-kalium__footer-links">
					<li>
						<a href="https://documentation.laborator.co/" target="_blank" rel="noreferrer noopener"><i class="kalium-admin-icon-documentation"></i>Documentation</a>
					</li>
					<li>
						<a href="https://www.facebook.com/laboratorcreative" target="_blank" rel="noreferrer noopener"><i class="kalium-admin-icon-social-facebook"></i>Facebook</a>
					</li>
					<li>
						<a href="https://twitter.com/thelaborator" target="_blank" rel="noreferrer noopener"><i class="kalium-admin-icon-social-twitter"></i>Twitter X</a>
					</li>
				</ul>
			</div>
		</div>
		<?php
	}

	/**
	 * Get saved backups.
	 *
	 * @return array
	 */
	private function get_saved_backups() {

		// Backups
		$backups = [];

		// Load backups
		$upload_dir    = wp_upload_dir();
		$theme_backups = glob( $upload_dir['basedir'] . '/' . preg_replace( '/\{\w+\}/i', '*', Kalium_Theme_Upgrader::$backup_file_name ) );

		// Insert backups
		if ( is_array( $theme_backups ) ) {
			foreach ( $theme_backups as $theme_backup ) {
				$relative_backup_file = str_replace( ABSPATH, '', $theme_backup );
				$base_name            = basename( $theme_backup );

				$backups[] = [
					'relative_path' => $relative_backup_file,
					'base_name'     => $base_name,
					'time'          => filemtime( $theme_backup ),
					'size'          => size_format( filesize( $theme_backup ) ),
					'url'           => $upload_dir['baseurl'] . '/' . $base_name,
				];
			}
		}

		// Sort by created date
		uasort(
			$backups,
			function ( $a, $b ) {
				return $a['time'] > $b['time'] ? - 1 : 1;
			}
		);

		return $backups;
	}
}
