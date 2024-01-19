<?php
/**
 * TypoLab - ultimate font management library.
 *
 * UI components.
 *
 * @author Laborator
 * @link   https://laborator.co
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class TypoLab_UI_Components {

	/**
	 * Encode JSON within container.
	 *
	 * @param string $container_id
	 * @param mixed  $data
	 */
	public static function encode_json( $container_id, $data = null ) {

		// JSON data container
		echo sprintf( '<script type="text/template" id="%s">%s</script>', esc_attr( $container_id ), wp_json_encode( $data ) );
	}

	/**
	 * Font face name input.
	 *
	 * @param string $font_family_name
	 */
	public static function font_face_name_form( $font_family_name = '' ) {
		?>
        <table class="typolab-table">
            <tbody>
            <tr class="hover">
                <th width="35%">
                    <label for="font_url">Font Face Name:</label>
                </th>
                <td class="no-bg">
                    <input type="text" name="font_family" id="font_family" value="<?php echo esc_attr( $font_family_name ); ?>" required="required" placeholder="(Single font family name, no quotes required)">
                </td>
            </tr>
            </tbody>
        </table>
		<?php
	}

	/**
	 * Font stylesheet URL form.
	 *
	 * @param string $stylesheet_url
	 */
	public static function font_stylesheet_url_form( $stylesheet_url = '' ) {
		?>
        <table class="typolab-table">
            <thead>
            <th colspan="2">Font Source</th>
            </thead>
            <tbody>
            <tr class="hover vtop">
                <th width="35%">
                    <label for="stylesheet_url">Font Stylesheet URL:</label>
                </th>
                <td class="no-bg">
                    <input type="text" name="stylesheet_url" id="stylesheet_url" value="<?php echo esc_attr( $stylesheet_url ); ?>" required="required" autocomplete="off">
                    <p class="description">Enter stylesheet URL that contains one or more web fonts (@font-face).</p>
                </td>
            </tr>
            </tbody>
        </table>
		<?php
	}

	/**
	 * Font face variants template.
	 *
	 * @param TypoLab_Font_Variant[] $font_variants
	 */
	public static function font_face_variants_form( $font_variants = [] ) {

		// Enqueue script libs
		wp_enqueue_media();
		wp_enqueue_script( 'jquery-ui-core' );
		wp_enqueue_script( 'jquery-ui-sortable' );

		// Defined variants
		$font_variants_arr = [];

		foreach ( $font_variants as $variant ) {
			$font_variants_arr[] = $variant->to_array();
		}

		// Root element for font-face variants entries
		echo '<div class="font-face-variants"></div>';

		// JSON data container
		self::encode_json( 'font_face_variants', $font_variants_arr );
	}

	/**
	 * Font select form.
	 *
	 * @param TypoLab_Font $font
	 */
	public static function font_select_form( $font ) {
		switch ( $font->get_source() ) {

			// Google font
			case 'google':
				require_once TypoLab_Helper::get_template_path( 'font-selector/fonts-list-google.php' );
				break;

			// Font Squirrel font
			case 'font-squirrel':
				require_once TypoLab_Helper::get_template_path( 'font-selector/fonts-list-font-squirrel.php' );
				break;

			// Laborator font
			case 'laborator':
				require_once TypoLab_Helper::get_template_path( 'font-selector/fonts-list-laborator.php' );
				break;

			// Adobe font
			case 'adobe':
				require_once TypoLab_Helper::get_template_path( 'font-selector/fonts-list-adobe.php' );
				break;

			// Hosted font
			case 'hosted':
				require_once TypoLab_Helper::get_template_path( 'font-selector/fonts-add-hosted-font-form.php' );
				break;

			// External font
			case 'external':
				require TypoLab_Helper::get_template_path( 'font-selector/fonts-add-external-font-form.php' );
				break;

			// System font
			case 'system':
				require TypoLab_Helper::get_template_path( 'font-selector/fonts-list-system.php' );
				break;
		}
	}

	/**
	 * Font variants form (with preview).
	 *
	 * @param TypoLab_Font $font
	 */
	public static function font_variants_form( $font ) {
		$help_text = [
			'general'  => 'Select a font from list to preview it here.',
			'external' => 'Font preview will be shown here after you fill font stylesheet URL and font variants.',
			'hosted'   => 'Add font variants to create preview.',
		];

		// Options fields for font
		$options_fields = [
			'adobe' => TypoLab_Adobe_Fonts_Provider::class,
		];

		// Get options fields by provider
		if ( isset( $options_fields[ $font->get_source() ] ) && method_exists( $options_fields[ $font->get_source() ], 'options_fields' ) ) {
			$options_fields[ $font->get_source() ]::options_fields();
		}

		?>
        <div id="font_variants_select_and_preview">
            <p class="description">
				<?php echo kalium_get_array_key( $help_text, $font->get_source(), $help_text['general'] ); ?>
            </p>
        </div>
		<?php
	}

	/**
	 * Font base selectors form.
	 *
	 * @param TypoLab_Font $font
	 */
	public static function font_base_selectors_form( $font ) {

		// Title
		TypoLab_UI_Components::page_title( 'Apply Font', 'Select where the font will be applied' );

		// Base selectors
		$base_selectors = TypoLab_Data::get_base_selectors();

		// Font base selectors data
		$font_base_selectors = [
			'selectors' => $base_selectors,
			'values'    => $font->get_base_selectors( true ),
		];

		// Default value
		if ( empty( $font_base_selectors['values'] ) && ! $font->get_option( 'legacy_font' ) ) {
			$base_selectors_keys = array_keys( $base_selectors );

			$font_base_selectors['values'][] = [
				'id'      => reset( $base_selectors_keys ),
				'include' => true,
			];
		}
		?>
        <div class="font-base-selectors">
			<?php
			foreach ( $base_selectors as $base_selector_id => $base_selector ) {
				$name        = $base_selector['name'];
				$description = $base_selector['description'];
				?>
                <div class="font-base-selector-col">
                    <div class="font-base-selector" data-id="<?php echo esc_attr( $base_selector_id ); ?>">
                        <label class="selector-title">
                            <h3><?php echo esc_html( $name ); ?></h3>
                            <input type="checkbox" name="font_base_selectors[<?php echo esc_attr( $base_selector_id ); ?>][checked]" value="<?php echo esc_attr( $base_selector_id ); ?>">
                        </label>
                        <p><?php echo esc_html( $description ); ?></p>
                        <div class="font-base-selector-variants">
                            <select name="font_base_selectors[<?php echo esc_attr( $base_selector_id ); ?>][variant]" class="select-font-variant"></select>
                        </div>
                    </div>
                </div>
				<?php
			}
			?>
        </div>
		<?php

		// JSON data container
		self::encode_json( 'font_base_selectors', $font_base_selectors );

	}

	/**
	 * Font custom selectors form.
	 *
	 * @param TypoLab_Font $font
	 */
	public static function font_custom_selectors_form( $font ) {

		// Enqueue jQuery UI Sortable
		wp_enqueue_script( 'jquery-ui-core' );
		wp_enqueue_script( 'jquery-ui-sortable' );

		// Title
		TypoLab_UI_Components::page_title( 'Custom Selectors', 'Add your own custom font selectors' );

		?>
        <div class="font-custom-selectors">
            <div class="typolab-table-responsive">
                <table class="typolab-table typolab-table--alt typolab-table--custom-selectors horizontal-borders">
                    <thead>
                    <tr>
                        <th class="column-sort"></th>
                        <th class="column-selectors">CSS Selector</th>
                        <th class="column-font-variant">Font Variant</th>
                        <th class="column-font-case">Text Transform</th>
                        <th class="column-font-size">Font Size</th>
                        <th class="column-line-height">Line Height</th>
                        <th class="column-letter-spacing">Letter Spacing</th>
                        <th class="column-actions"></th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                    <tfoot>
                    <tr>
                        <td colspan="8">
                            <a href="#" class="button" id="add-new-selector">
                                <i class="kalium-admin-icon-plus"></i>
                                Add New Selector
                            </a>
                        </td>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>
		<?php

		// JSON data container
		self::encode_json( 'font_custom_selectors', [
			'responsive'           => TypoLab_Data::get_responsive_breakpoints(),
			'predefined_selectors' => TypoLab_Data::get_predefined_selectors(),
			'values'               => $font->get_custom_selectors( true ),
		] );
	}

	/**
	 * Conditional loading.
	 *
	 * @param TypoLab_Font $font
	 */
	public static function font_conditional_loading_form( $font ) {
		$conditional_statements = [
			'options'    => TypoLab_Helper::get_conditional_loading_statements(),
			'statements' => $font->get_conditional_loading_statements( true ),
		];

		?>
        <div class="typolab-table-responsive">
            <table class="typolab-table typolab-table--alt typolab-table--conditional-loading">
                <thead>
                <tr>
                    <th>Conditional Loading</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td class="no-padding">

                        <table class="font-conditional-loading">
                            <thead>
                            <tr>
                                <th class="statement">Include Font When</th>
                                <th class="operator">Operator</th>
                                <th class="criteria">Criteria</th>
                                <th class="actions"></th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr class="no-statements">
                                <td colspan="4">
                                    No conditional statements. Font will be loaded on all pages.
                                </td>
                            </tr>
                            </tbody>
                        </table>

                    </td>
                </tr>
                <tr class="hover">
                    <td>
                        <a href="#" id="add-new-conditional-statement" class="button">
                            <i class="kalium-admin-icon-plus"></i>
                            Add Conditional Statement
                        </a>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
		<?php

		// JSON data container
		self::encode_json( 'conditional_statements', $conditional_statements );
	}

	/**
	 * Font other options form.
	 *
	 * @param TypoLab_Font $font
	 */
	public static function font_other_options_form( $font ) {
		$font_preload   = $font->get_preload();
		$font_placement = $font->get_placement();
		$is_active      = $font->is_active();

		?>
        <table class="typolab-table typolab-table--alt horizontal-borders">
            <thead>
            <tr>
                <th colspan="2">Other Options</th>
            </tr>
            </thead>
            <tbody>
			<?php if ( $font->supports_preload() ) : ?>
                <tr>
                    <th>
                        <label for="font_preload">Font preload</label>
                    </th>
                    <td>
                        <div class="grouped-input no-border">
                            <div class="grouped-input-col select">
                                <select name="font_preload" id="font_preload">
                                    <option value="inherit">Inherit from settings</option>
                                    <option value="yes"<?php selected( 'yes', $font_preload ); ?>>Yes</option>
                                    <option value="no"<?php selected( 'no', $font_preload ); ?>>No</option>
                                </select>
                            </div>
                        </div>
                    </td>
                </tr>
			<?php endif; ?>
            <tr>
                <th>
                    <label for="font_placement">Import font</label>
                </th>
                <td>
                    <div class="grouped-input no-border">
                        <div class="grouped-input-col select">
                            <select name="font_placement" id="font_placement">
                                <option value="inherit">Inherit from settings</option>
                                <option value="head"<?php selected( 'head', $font_placement ); ?>>Before page renders (Inside &lt;head&gt;)</option>
                                <option value="body"<?php selected( 'body', $font_placement ); ?>>After page renders (Inside &lt;body&gt;)</option>
                            </select>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <th>
                    <label for="font_status">Font status</label>
                </th>
                <td>
                    <div class="grouped-input no-border">
                        <div class="grouped-input-col select">
                            <select name="font_status" id="font_status">
                                <option value="active">Active</option>
                                <option value="inactive"<?php selected( ! $is_active ); ?>>Inactive</option>
                            </select>
                        </div>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
		<?php
	}

	/**
	 * Font size group form.
	 *
	 * @param array $font_size_group {
	 *
	 * @type string $id
	 * @type string $group
	 * @type array  $elements
	 * }
	 */
	public static function font_appearance_group_form( $font_size_group ) {
		$font_size_group = wp_parse_args( $font_size_group, [
			'id'   => null,
			'name' => null,
		] );

		?>
        <div class="font-appearance-group typolab-table-responsive" data-group-id="<?php echo esc_attr( $font_size_group['id'] ); ?>">
            <table class="typolab-table horizontal-borders typolab-table--font-appearance-group mutual-column-width">
                <thead>
                <tr class="heading">
                    <th colspan="5">
                        <h3><?php echo esc_html( $font_size_group['name'] ); ?></h3>
                    </th>
                </tr>
                <tr class="columns">
                    <th class="column-element"></th>
                    <th class="column-font-size">Font Size</th>
                    <th class="column-line-height">Line Height</th>
                    <th class="column-letter-spacing">Letter Spacing</th>
                    <th class="column-font-case">Text Transform</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td colspan="5" class="text-center">&hellip;</td>
                </tr>
                </tbody>
            </table>
        </div>
		<?php
	}

	/**
	 * Font selector list element.
	 *
	 * @param array $fonts_list
	 * @param array $args
	 */
	public static function font_selector( $fonts_list = [], $args = [] ) {
		$args = wp_parse_args( $args, [
			'title'          => 'Select Font',
			'provider'       => '',
			'tabs'           => [],
			'active_tab'     => '',
			'search_bar'     => true,
			'search_filters' => [],
			'filters_label'  => 'Category',
			'input_name'     => 'font_family',
			'input_value'    => '',
			'value_prop'     => 'font_family',
			'items'          => $fonts_list,
			'data'           => [],
		] );
		?>
        <div class="fonts-list-select">
            <input type="hidden" name="<?php echo esc_attr( $args['input_name'] ); ?>" value="<?php echo esc_attr( $args['input_value'] ); ?>" class="font-list-selector-value">

			<?php if ( ! empty( $args['tabs'] ) ) : ?>
                <div class="tabs-list">
					<?php foreach ( $args['tabs'] as $tab ) : ?>
                        <a href="#" data-tab="<?php echo esc_attr( $tab['value'] ); ?>" title="<?php echo esc_attr( $tab['title-attr'] ); ?>"><?php echo esc_html( $tab['title'] ); ?></a>
					<?php endforeach; ?>
                </div>
			<?php endif; ?>

            <div class="title-container">
                <h3><?php echo esc_html( $args['title'] ); ?></h3>

				<?php if ( $args['search_bar'] ) : ?>
                    <div class="search-bar">
                        <input type="text" name="search-fonts" class="regular-text" placeholder="Search fonts..." autocomplete="off">

						<?php if ( ! empty( $args['search_filters'] ) ) : ?>
                            <select name="search-category">
                                <option value=""><?php echo esc_html( $args['filters_label'] ); ?></option>
								<?php
								foreach ( $args['search_filters'] as $filter ) :
									?>
                                    <option value="<?php echo esc_attr( $filter['name'] ); ?>"><?php echo esc_html( sprintf( "%s (%s)", kalium_get_array_key( $filter, 'title', $filter['name'] ), $filter['count'] ) ); ?></option>
								<?php endforeach; ?>
                            </select>
						<?php endif; ?>
                    </div>
				<?php endif; ?>
            </div>

            <div class="font-list">
                <span class="loading-fonts">Loading fonts...</span>
            </div>
			<?php
			// JSON data container
			self::encode_json( 'font_selector_fonts_list', $args );
			?>
        </div>
		<?php
	}

	/**
	 * Font source selector.
	 *
	 * @param string $current_source
	 *
	 */
	public static function font_source_select_dropdown( $current_source = '' ) {
		?>

        <div class="typolab-dropdown font-source-select">
            <h3>Font Source</h3>

            <ul>
				<?php
				foreach ( TypoLab_Data::get_font_sources() as $source_id => $font_source ) :
					$classes = [
						'typolab-dropdown__item',
					];

					if ( $current_source === $source_id ) {
						$classes[] = 'current';
					}
					?>
                    <li <?php kalium_class_attr( $classes ); ?>>
                        <a href="<?php echo add_query_arg( 'font-source', $source_id ); ?>">
							<?php TypoLab_UI_Components::font_source_logo_and_name( $source_id, $font_source ); ?>
                        </a>
                    </li>
				<?php
				endforeach;
				?>
            </ul>
        </div>
		<?php
	}

	/**
	 * Button with icon.
	 *
	 * @param string      $title
	 * @param string|bool $icon
	 * @param string      $id
	 */
	public static function button( $title, $icon = false, $id = '' ) {
		?>
        <a href="#" class="typolab-button button"<?php echo $id ? sprintf( ' id="%s"', $id ) : ''; ?>>
			<?php if ( ! empty( $icon ) ) : ?>
                <i class="<?php echo esc_attr( $icon ); ?>"></i>
			<?php endif; ?>

			<?php echo esc_html( $title ); ?>
        </a>
		<?php
	}

	/**
	 * Toggle checkbox.
	 *
	 * @param string $name
	 * @param string $title
	 */
	public static function checkbox_toggle( $name, $checked = false, $title = '' ) {
		?>
        <span class="components-form-toggle<?php echo $checked ? ' is-checked' : ''; ?>">
            <input class="components-form-toggle__input" name="<?php echo esc_attr( $name ); ?>" id="<?php echo esc_attr( $name ); ?>" type="checkbox"<?php echo checked( $checked ); ?>>
            <span class="components-form-toggle__track"></span>
            <span class="components-form-toggle__thumb"></span>
        </span>
		<?php
	}

	/**
	 * Page title.
	 *
	 * @param string $title
	 * @param string $sub_title
	 */
	public static function page_title( $title, $sub_title = '' ) {
		$sub_title = wp_kses_post( $sub_title );

		// If there is no title, do not show
		if ( ! $title ) {
			return;
		}

		// Sub title
		if ( $sub_title ) {
			$title .= sprintf( '<small>%s</small>', $sub_title );
		}

		// Output title
		echo sprintf( '<h2 class="page-title">%s</h2>', $title );
	}

	/**
	 * Switched font source notice.
	 */
	public static function switched_font_source_notice() {
		$previous_font_source = TypoLab_Data::get_font_source( $GLOBALS['previous_font_source'] );
		$new_font_source      = TypoLab_Data::get_font_source( $GLOBALS['new_font_source'] );
		?>
        <div class="typolab-notice">
            <i class="kalium-admin-icon-alert-info"></i>
            Changes to your previous font source (<strong><?php echo esc_html( $previous_font_source['name'] ); ?></strong>) won't be lost until you click <strong>Save Changes</strong> button.
        </div>
		<?php
	}

	/**
	 * Font source name.
	 *
	 * @param string $source_id
	 * @param array  $source
	 */
	public static function font_source_logo_and_name( $source_id, $source ) {
		$logo_image = TypoLab::$typolab_dir . '/assets/images/' . $source_id . '.svg'
		?>
        <div class="font-source-name">
			<?php echo kalium_get_svg_file( $logo_image ); ?>
            <span><?php echo esc_html( $source['name'] ); ?></span>
        </div>
		<?php
	}
}
