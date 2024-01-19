<?php
/**
 * TypoLab - ultimate font management library.
 *
 * Add new font form - select font source template.
 *
 * @author Laborator
 * @link   https://laborator.co
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$font_sources    = array_keys( TypoLab_Data::get_font_sources() );
$selected_source = reset( $font_sources );
?>
<form id="typolab-add-new" method="post" enctype="application/x-www-form-urlencoded">

    <p>Select font source first and then continue to font options, each font source has different configuration set.</p>

    <div class="row-layout-flex">
        <div class="col col-7">
            <table class="typolab-table typolab-select-font-source horizontal-borders">
                <thead>
                <tr>
                    <th colspan="2">Font Source</th>
                </tr>
                </thead>
                <tbody>
				<?php
				foreach ( TypoLab_Data::get_font_sources() as $source_id => $font_source ) :
                    // Font Squirrel is no longer supported
                    if ( 'font-squirrel' === $source_id ) {
                        continue;
                    }
					?>
                    <tr>
                        <td class="radio-input">
                            <input type="radio" name="font_source" id="font_source_<?php echo esc_attr( $source_id ); ?>" value="<?php echo esc_attr( $source_id ); ?>"<?php echo checked( $source_id === $selected_source ); ?>>
                        </td>
                        <td>
							<?php
							/**
							 * Font source logo and name element.
							 */
							TypoLab_UI_Components::font_source_logo_and_name( $source_id, $font_source );
							?>
                        </td>
                    </tr>
				<?php
				endforeach;
				?>
                </tbody>
            </table>
        </div>
        <div class="col col-flex col-flex-column">
            <table class="typolab-table typolab-selected-font-source">
                <thead>
                <tr>
                    <th>Selected Font Source</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>
						<?php
						foreach ( TypoLab_Data::get_font_sources() as $source_id => $font_source ) :
							?>
                            <div class="font-source-description font-source-description-<?php echo esc_attr( $source_id ); ?>">
                                <h3><?php echo esc_html( $font_source['name'] ); ?></h3>
								<?php echo kalium_format_content( $font_source['description'] ); ?>
                            </div>
						<?php

						endforeach;
						?>
                    </td>
                </tr>
                </tbody>
            </table>

			<?php
			/**
			 * Submit button.
			 */
			wp_nonce_field( 'typolab-add-font' );
			?>
            <button type="submit" class="button button-primary" name="typolab_add_font" id="typolab_add_font">
                Continue
                <span class="kalium-admin-icon-arrow-right"></span>
            </button>
        </div>
    </div>

</form>
