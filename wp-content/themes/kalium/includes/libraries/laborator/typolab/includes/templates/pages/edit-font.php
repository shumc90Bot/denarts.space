<?php
/**
 * TypoLab - ultimate font management library.
 *
 * Edit font template.
 *
 * @author Laborator
 * @link   https://laborator.co
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Current editing font
$font = TypoLab::get_current_font();

?>
    <form id="edit-font-form" method="post" enctype="application/x-www-form-urlencoded">
		<?php
		/**
		 * Font edit form hook.
		 */
		do_action( 'typolab_edit_font_form' );
		?>
        <div class="row-layout-flex font-select-and-preview">
            <div class="col col-7">
				<?php
				/**
				 * Font select form.
				 */
				TypoLab_UI_Components::font_select_form( $font );
				?>
            </div>
            <div class="col col-5">
				<?php
				/**
				 * Font preview.
				 */
				TypoLab_UI_Components::font_variants_form( $font );
				?>
            </div>
        </div>

		<?php
		/**
		 * Font base selectors.
		 */
		TypoLab_UI_Components::font_base_selectors_form( $font );

		/**
		 * Custom selectors.
		 */
		TypoLab_UI_Components::font_custom_selectors_form( $font );
		?>

        <a href="#" id="typolab-toggle-advanced-options">
            Advanced Options
            <i class="kalium-admin-icon-arrow-down kalium-icon-size-12"></i>
        </a>

        <div id="typolab-advanced-options">
			<?php
			TypoLab_UI_Components::page_title( 'Advanced Options', 'Font visibility and other options' );
			?>
            <div class="row-layout-flex">
                <div class="col col-7">
					<?php
					/**
					 * Conditional loading manager.
					 */
					TypoLab_UI_Components::font_conditional_loading_form( $font );
					?>
                </div>
                <div class="col col-5">
					<?php
					/**
					 * Font other options form.
					 */
					TypoLab_UI_Components::font_other_options_form( $font );
					?>
                </div>
            </div>
        </div>

        <div class="save-changes-container">
			<?php wp_nonce_field( 'typolab-save-font' ); ?>
			<?php submit_button( 'Save Changes', 'primary', 'save_font' ); ?>
        </div>
    </form>

<?php
/**
 * Font source select dropdown.
 */
TypoLab_UI_Components::font_source_select_dropdown( $font->get_source() );
