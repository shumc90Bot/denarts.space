<?php
/**
 * TypoLab - ultimate font management library.
 *
 * Fonts list table.
 *
 * @author Laborator
 * @link   https://laborator.co
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Load WP list table
if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * Fonts list table.
 */
class TypoLab_Fonts_List_Table extends WP_List_Table {

	/**
	 * Constructor.
	 */
	function __construct() {
		parent::__construct( [
			'singular' => 'Font',
			'plural'   => 'Fonts',
			'ajax'     => false
		] );
	}

	/**
	 * Prepare items.
	 */
	public function prepare_items() {
		$columns  = $this->get_columns();
		$hidden   = [];
		$sortable = $this->get_sortable_columns();
		$orderby  = ( isset( $_REQUEST['orderby'] ) ) ? $_REQUEST['orderby'] : '';
		$order    = ( isset( $_REQUEST['order'] ) ) ? $_REQUEST['order'] : '';

		// Table columns
		$this->_column_headers = [
			$columns,
			$hidden,
			$sortable,
		];

		// Process bulk actions
		$this->process_bulk_actions();

		// Fonts args
		$args = [];

		// Order by
		if ( in_array( $orderby, [ 'name', 'source', 'date', 'source-name' ] ) ) {
			$args['orderby'] = $orderby;
		}

		// Order
		$args['order'] = $order;

		// Fonts
		$per_page    = $this->get_items_per_page( 'typolab_fonts_list' );
		$pagenum     = $this->get_pagenum();
		$fonts_list  = TypoLab::get_fonts( $args );
		$total_fonts = count( $fonts_list );

		// Add items
		$this->items = array_slice( $fonts_list, $per_page * ( $pagenum - 1 ), $per_page );

		// Pagination
		$this->set_pagination_args( [
			'total_items' => $total_fonts,
			'per_page'    => $per_page,
		] );
	}

	/**
	 * Process bulk actions.
	 */
	public function process_bulk_actions() {
		$fonts_list = kalium()->request->input( 'fonts_list', [] );

		// When there are selected fonts
		if ( count( $fonts_list ) ) {
			$notice_message = '';
			$notice_type    = 'success';

			// Loop through selected IDs
			foreach ( $fonts_list as $font_id ) {
				$font = TypoLab::get_font( $font_id );

				// If font doesn't exists
				if ( ! $font ) {
					continue;
				}

				// Deactivate fonts
				if ( 'deactivate' === $this->current_action() ) {
					$font->set_active( false );
					$font->save();
					$notice_message = 'Selected fonts deactivated.';
				} // Activate fonts
				else if ( 'activate' === $this->current_action() ) {
					$font->set_active( true );
					$font->save();
					$notice_message = 'Selected fonts activated.';
				} // Delete fonts
				else if ( 'delete' === $this->current_action() ) {
					if ( true === $font->delete() ) {
						$notice_message = 'Selected fonts deleted.';
					}
				} // Install fonts (flush)
				else if ( 'install' === $this->current_action() ) {
					if ( $font instanceof TypoLab_Installable_Font && true === $font->install() ) {
						if ( $font->supports_preload() && $font->do_preload() ) {
							$font->preload();
						}

						$font->save();
						$notice_message = 'Selected fonts reinstalled.';
					}
				}
			}

			// Admin notice
			if ( $notice_message ) {
				kalium()->helpers->add_admin_notice( $notice_message, $notice_type );
			}
		}
	}

	/**
	 * Get columns.
	 *
	 * @return array
	 */
	public function get_columns() {
		return [
			'cb'           => '<input type="checkbox">',
			'font_family'  => 'Name',
			'font_preview' => 'Font Preview',
			'font_source'  => 'Source',
			'font_status'  => 'Status',
		];
	}

	/**
	 * Display.
	 */
	public function display() {
		$total_items = $this->get_pagination_arg( 'total_items' );

		if ( $total_items ) {
			parent::display();
		} else {
			$add_font_url = TypoLab::add_new_font_url();
			?>
            <div class="no-fonts-installed">
                <h3>No Fonts Installed</h3>
                <p>You have no fonts installed in your site yet, to add a font click the button below.</p>
                <a href="<?php echo esc_url( $add_font_url ); ?>" class="button button-primary">
                    <i class="kalium-admin-icon-plus"></i>
                    Add Font
                </a>
            </div>
			<?php
		}
	}

	/**
	 * Sortable columns.
	 *
	 * @return array
	 */
	protected function get_sortable_columns() {
		return [
			'font_family' => [ 'name', 'asc' ],
			'font_source' => [ 'source', 'asc' ],
			'font_status' => [ 'date', 'desc' ],
		];
	}

	/**
	 * Bulk actions.
	 *
	 * @return array
	 */
	protected function get_bulk_actions() {
		return [
			'activate'   => 'Activate',
			'deactivate' => 'Deactivate',
			'flush'      => 'Flush',
			'delete'     => 'Delete',
		];
	}

	/**
	 * Checkbox.
	 *
	 * @param TypoLab_Font $item
	 */
	public function column_cb( $item ) {
		?>
        <input type="checkbox" name="fonts_list[]" value="<?php echo $item->get_id(); ?>"/>
		<?php
	}

	/**
	 * Font family column.
	 *
	 * @param TypoLab_Font $font
	 */
	public function column_font_family( $font ) {
		echo sprintf( '<strong><a href="%s" class="row-title">%s</a></strong>', TypoLab::get_font_action_link( 'edit-font', $font ), $font->get_title() );

		if ( $font instanceof TypoLab_Laborator_Font && ! kalium()->theme_license->is_theme_registered() ) {
			echo '<i class="laborator-font-locked kalium-admin-icon-register tooltip" title="Premium fonts can be installed only when theme is registered."></i>';
		}
	}

	/**
	 * Font preview column.
	 *
	 * @param TypoLab_Font $font
	 */
	public function column_font_preview( $font ) {
		echo TypoLab::preview( $font );
	}

	/**
	 * Font source column.
	 *
	 * @param TypoLab_Font $font
	 */
	public function column_font_source( $font ) {
		// Font source logo and name element.
		TypoLab_UI_Components::font_source_logo_and_name( $font->get_source(), TypoLab_Data::get_font_source( $font->get_source() ) );
	}

	/**
	 * Font status column.
	 *
	 * @param TypoLab_Font $font
	 */
	public function column_font_status( $font ) {
		$status       = $font->is_active() ? 'active' : 'inactive';
		$status_title = $font->is_active() ? 'Active' : 'Inactive';

		echo sprintf( '<span class="font-status-badge font-status-%s">%s</span>', $status, $status_title );
	}

	/**
	 * Generate and display row actions links.
	 *
	 * @param TypoLab_Font $font
	 * @param string       $column_name
	 * @param string       $primary
	 *
	 * @return string
	 */
	protected function handle_row_actions( $font, $column_name, $primary ) {
		if ( $primary !== $column_name ) {
			return '';
		}

		$status_title     = 'Deactivate';
		$edit_font_link   = TypoLab::get_font_action_link( 'edit-font', $font );
		$delete_font_link = TypoLab::get_font_action_link( 'delete-font', $font );
		$status_font_link = TypoLab::get_font_action_link( 'deactivate-font', $font );
		$flush_font_link  = TypoLab::get_font_action_link( 'install-font', $font );

		if ( ! $font->is_active() ) {
			$status_title     = 'Activate';
			$status_font_link = TypoLab::get_font_action_link( 'activate-font', $font );
		}

		$sep    = ' | ';
		$output = '<div class="row-actions">';
		$output .= sprintf( '<span><a href="%s">Edit</a></span>', $edit_font_link );
		$output .= $sep;
		$output .= sprintf( '<span><a href="%s">%s</a></span>', $status_font_link, $status_title );
		$output .= $sep;

		// Font flush
		if ( method_exists( $font, 'install' ) ) {
			$output .= sprintf( '<span class="flush"><a href="%s">Flush</a></span>', $flush_font_link );
			$output .= $sep;
		}

		$output .= sprintf( '<span class="delete"><a href="%s">Delete</a></span>', $delete_font_link );
		$output .= '</div>';

		return $output;
	}
}