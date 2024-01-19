<?php
/**
 * Kalium WordPress Theme
 *
 * Group ACF metaboxes plugin.
 *
 * @author Laborator
 * @link   https://kaliumtheme.com
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

class Kalium_Grouped_Metaboxes {

	/**
	 * Metaboxes to apply grouping.
	 *
	 * @var array
	 */
	private $field_groups = [
		'group_5ba0c4875e914', // Video Post Format Settings
		'group_5ba0c486ef604', // Audio Post Format Settings
		'group_5ba0c486f384b', // Portfolio Item Type
		'group_5ba0c4870387e', // Portfolio Settings
		'group_5ba0c48759d39', // Post Slider Images
		'group_5ba0c48768d0b', // Side Portfolio (Portfolio Type 1)
		'group_5ba0c48780e3d', // Columned (Portfolio Type 2)
		'group_5ba0c48790af7', // Carousel (Portfolio Type 3)
		'group_5ba0c48794e83', // Zig Zag (Portfolio Type 4)
		'group_5ba0c4879adbe', // Fullscreen (Portfolio Type 5)
		'group_5ba0c487a27c2', // Lightbox (Portfolio Type 6)
		'group_5ba0c487b1831', // General Details
		'group_5ba0c487ca369', // Project Link
		'group_5ba0c487d4320', // Checklists
		'group_5ba0c487e4256', // Project Gallery
		'group_5ba0c48846cf6', // Project Gallery
		'group_5ba0c48866e5d', // Project Gallery
		'group_5ba0c488c86e4', // Project Gallery
		'group_5fc75ed114e2b', // Featured Video
		'group_5ba0c488dda57', // Page Options
		'group_5ba0c488cf1ee', // Other Settings
		'group_5ba0c4893cf5d', // Post Settings
		'group_5ba0c48949e0a', // Custom CSS
	];

	/**
	 * ACF group field groups (tabs).
	 *
	 * @var array
	 */
	private $tabs = [];

	/**
	 * Constructor.
	 */
	public function __construct() {

		// Init when ACF is active
		add_action( 'acf/init', [ $this, 'init' ] );
	}

	/**
	 * Metabox title.
	 *
	 * @return string
	 */
	public function get_title() {
		return apply_filters( 'kalium_acfpro_gm_get_title', 'Parameters and Options' );
	}

	/**
	 * Get field groups.
	 *
	 * @return array
	 */
	public function get_field_groups() {
		return $this->field_groups;
	}

	/**
	 * Get allowed post types for grouped metaboxes plugin.
	 *
	 * @return array
	 */
	public function get_allowed_post_types() {
		return apply_filters( 'kalium_acfpro_gm_get_allowed_post_types', [ 'post', 'page', 'portfolio', 'product' ] );
	}

	/**
	 * Get field group icon.
	 *
	 * @param string $group_name
	 *
	 * @return string
	 */
	public function get_field_group_icon( $group_name ) {
		$icon = '';

		switch ( $group_name ) {
			case 'Audio Post Format Settings':
				$icon = 'kalium-admin-icon-audio-settings';
				break;

			case 'Post Slider Images':
				$icon = 'kalium-admin-icon-project-gallery';
				break;

			case 'Portfolio Settings':
			case 'Portfolio Item Type':
				$icon = 'kalium-admin-icon-portfolio';
				break;

			case 'Side Portfolio (Portfolio Type 1)':
			case 'Columned (Portfolio Type 2)':
			case 'Carousel (Portfolio Type 3)':
			case 'Zig Zag (Portfolio Type 4)':
			case 'Fullscreen (Portfolio Type 5)':
			case 'Lightbox (Portfolio Type 6)':
				$icon = 'kalium-admin-icon-check';
				break;

			case 'General Details':
				$icon = 'kalium-admin-icon-maintenance';
				break;

			case 'Project Link':
				$icon = 'kalium-admin-icon-project-link';
				break;

			case 'Checklists':
				$icon = 'kalium-admin-icon-checklist';
				break;

			case 'Portfolio Gallery':
			case 'Project Gallery':
				$icon = 'kalium-admin-icon-project-gallery';
				break;

			case 'Other Settings':
				$icon = 'kalium-admin-icon-settings';
				break;

			case 'Page Options':
				$icon = 'kalium-admin-icon-page-options';
				break;

			case 'Post Settings':
				$icon = 'kalium-admin-icon-post-settings';
				break;

			case 'Custom CSS':
				$icon = 'kalium-admin-icon-custom-css';
				break;

			case 'Video Post Format Settings':
			case 'Featured Video':
				$icon = 'kalium-admin-icon-featured-video';
				break;
		}

		return $icon;
	}

	/**
	 * Init metabox group plugin.
	 *
	 * @return void
	 */
	public function init() {

        if ( isset( $_GET['xx'] ) ) {
			var_dump( did_action( 'acf/init' ) );exit;
		}

		// Load on post.php and post-new.php page
		add_action( 'load-post.php', [ $this, '_load_grouped_metaboxes' ] );
		add_action( 'load-post-new.php', [ $this, '_load_grouped_metaboxes' ] );

		/**
		 * Development use: Generate code for supported Kalium metaboxes to group to avoid grouping other custom metaboxes.
		 */
		if ( defined( 'KALIUM_DEV' ) && current_user_can( 'manage_options' ) && kalium()->request->has( 'list-acf-groups' ) ) {
			$this->field_groups_array_export();
		}
	}

	/**
	 * Load grouped metaboxes.
	 *
	 * @return void
	 */
	public function _load_grouped_metaboxes() {

		// Valid field groups
		$field_groups = $this->get_field_groups();

		// Get field groups
		if ( function_exists( 'acf_get_field_groups' ) ) {
			$loaded_field_groups = acf_get_field_groups();
		} else {
			$loaded_field_groups = apply_filters( 'acf/load_field_groups', [] );
		}

		foreach ( $loaded_field_groups as $group_id => $group ) {
			if ( in_array( $group['key'], $field_groups ) ) {
				$this->tabs[ $group['key'] ] = [
					'title' => $group['title'],
					'icon'  => $this->get_field_group_icon( $group['title'] ),
				];
			}
		}

		// Hooks
		add_action( 'add_meta_boxes', [ $this, '_metabox_create' ], 10 );
		add_action( 'admin_enqueue_scripts', [ $this, '_enqueue_assets' ] );
		add_action( 'admin_footer', [ $this, '_wp_footer' ] );
	}

	/**
	 * Enqueue assets.
	 *
	 * @return void
	 */
	public function _enqueue_assets() {
		wp_enqueue_script( 'jquery' );
		kalium_enqueue( 'grouped-metaboxes-store', kalium()->locate_file_url( 'includes/libraries/laborator/grouped-metaboxes/assets/store.modern.min.js' ) );
		kalium_enqueue( 'grouped-metaboxes-js', kalium()->locate_file_url( 'includes/libraries/laborator/grouped-metaboxes/assets/grouped-metaboxes.min.js' ) );
		kalium_enqueue( 'grouped-metaboxes-css', kalium()->locate_file_url( 'includes/libraries/laborator/grouped-metaboxes/assets/grouped-metaboxes.min.css' ) );

		// FontAwesome Icons library
		kalium_enqueue( 'fontawesome-css' );
	}

	/**
	 * Add grouped metaboxes container for field groups.
	 *
	 * @param string $post_type
	 *
	 * @return void
	 */
	public function _metabox_create( $post_type ) {
		if ( in_array( $post_type, $this->get_allowed_post_types() ) ) {
			$title     = sprintf( '<span class="panel-loading-indicator"><i class="loading-icon kalium-admin-icon-refresh"></i></span>%s', $this->get_title() );
			$container = [ $this, '_metabox_container' ];

			add_meta_box( 'kalium-acfpro-grouped-metaboxes', $title, $container, $post_type, 'normal', 'high' );
		}
	}

	/**
	 * Metabox container.
	 *
	 * @return void
	 */
	public function _metabox_container() {
		?>
        <div class="kalium-acfpro-grouped-metaboxes-container">
            <div class="kalium-acfpro-grouped-metaboxes-inner">
                <div class="kalium-acfpro-grouped-metaboxes-loading-indicator">
				<span>
					<i class="fas fa-circle-notch fa-spin"></i>
					Loading Options...
				</span>
                </div>
                <ul class="kalium-acfpro-grouped-metaboxes-tabs"></ul>
                <div class="kalium-acfpro-grouped-metaboxes-body"></div>
            </div>
        </div>
		<?php
	}

	/**
	 * Code append on footer.
	 *
	 * @return void
	 */
	public function _wp_footer() {

		// Parse JS variable to use in the JS lib
		kalium_define_js_variable( 'groupedMetaboxes', $this->tabs );

		// Hide metaboxes initially until grouped
		echo '<style>';
		foreach ( $this->tabs as $metabox_id => $metabox ) {
			echo ".postbox-container .meta-box-sortables > #acf-{$metabox_id} { display: none; }\n";
		}
		echo '</style>';
	}

	/**
	 * Generate acf field groups array export.
	 *
	 * @return void
	 */
	private function field_groups_array_export() {
		$groups = new WP_Query( 'post_type=acf-field-group&posts_per_page=-1' );
		$nl     = PHP_EOL;
		$code   = 'return [' . $nl;

		foreach ( $groups->posts as $acf_group ) {
			$code .= "\t'" . $acf_group->post_name . "', // $acf_group->post_title" . $nl;
		}
		$code .= '];';

		echo $code;
		die();
	}
}

// Init Grouped Metaboxes
new Kalium_Grouped_Metaboxes();
