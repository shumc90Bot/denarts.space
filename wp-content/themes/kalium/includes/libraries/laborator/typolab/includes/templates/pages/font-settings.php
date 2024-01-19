<?php
/**
 * TypoLab - ultimate font management library.
 *
 * TypoLab settings page.
 *
 * @author Laborator
 * @link   https://laborator.co
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Create fonts directory if not exists
if ( false === file_exists( TypoLab::$fonts_dir ) ) {
	wp_mkdir_p( TypoLab::$fonts_dir );
}

// Font display list
$font_display_list = [
	'auto'     => 'Auto',
	'swap'     => 'Swap (Default)',
	'block'    => 'Block',
	'fallback' => 'Fallback',
	'optional' => 'Optional',
];

// Fonts directory and permissions
$fonts_directory  = str_replace( ABSPATH, '~/', TypoLab::$fonts_dir );
$is_writable      = file_exists( TypoLab::$fonts_dir ) && true === is_writable( TypoLab::$fonts_dir );
$permissions_text = kalium_conditional( $is_writable, 'Writable', 'Not writable' );

?>
<form id="typolab-settings-form" method="post" enctype="application/x-www-form-urlencoded">

    <table class="typolab-table typolab-table--settings mutual-column-width">
        <tbody>
        <tr class="hover">
            <th class="no-bg">
                <label for="typolab_enabled">
                    Typography Status
                    <i title="This setting is helpful for debugging purpose." class="info kalium-admin-icon-alert-info"></i>
                </label>
                <p>Enable or disable fonts on front-end.</p>
            </th>
            <td>
				<?php TypoLab_UI_Components::checkbox_toggle( 'typolab_enabled', TypoLab::is_enabled() ); ?>
            </td>
        </tr>
        <tr class="hover">
            <th class="no-bg">
                <label for="font_preload">
                    Font Preload
                    <i title="This setting can be overridden individually for each font (in Advanced Options area)." class="info kalium-admin-icon-alert-info"></i>
                </label>
                <p>Improve page loading speed and user experience.</p>
            </th>
            <td>
				<?php TypoLab_UI_Components::checkbox_toggle( 'font_preload', TypoLab::$font_preload ); ?>
            </td>
        </tr>
        <tr class="hover">
            <th class="no-bg">
                <label for="pull_google_fonts">
                    Pull Google Fonts
                    <i title="All Google Fonts will be downloaded from Google and stored locally on your server." class="info kalium-admin-icon-alert-info"></i>
                </label>
                <p>Load Google fonts locally.</p>
            </th>
            <td>
				<?php TypoLab_UI_Components::checkbox_toggle( 'pull_google_fonts', TypoLab::$pull_google_fonts ); ?>
            </td>
        </tr>
        <tr class="hover">
            <th class="no-bg">
                <label for="font_placement">
                    Font Import Placement
                    <i title="This setting can be overridden individually for each font (in font settings page)." class="info kalium-admin-icon-alert-info"></i>
                </label>
                <p>Set default placement for font import code in HTML document.</p>
            </th>
            <td>
                <select name="font_placement" id="font_placement">
                    <option value="head"<?php selected( 'head', TypoLab::$font_placement ); ?>>Before page renders (Inside &lt;head&gt;)</option>
                    <option value="body"<?php selected( 'body', TypoLab::$font_placement ); ?>>After page renders (Inside &lt;body&gt;)</option>
                </select>
            </td>
        </tr>
        <tr class="hover">
            <th class="no-bg">
                <label for="font_display">Font Face Rendering</label>
                <p>Choose how to render the font faces for a better performance.</p>
            </th>
            <td>
                <select name="font_display" id="font_display">
					<?php
					foreach ( $font_display_list as $value => $title ) {
						?>
                        <option value="<?php echo esc_attr( $value ); ?>"<?php selected( TypoLab::$font_display, $value ); ?>><?php echo esc_html( $title ); ?></option>
						<?php
					}
					?>
                </select>
            </td>
        </tr>
        <tr class="hover">
            <th class="no-bg">
                <label for="adobe_fonts_api_token">
                    Adobe Fonts API Token
                </label>
                <p>
                    In order to use Adobe Fonts you need to provide API token
                    that will fetch your font projects and integrate them in the theme.
                </p>
            </th>
            <td>
                <input type="text" name="adobe_fonts_api_token" id="adobe_fonts_api_token" value="<?php echo esc_attr( TypoLab::get_adobe_fonts_api_token() ); ?>">
                <a href="https://fonts.adobe.com/account/tokens" class="link" target="_blank" rel="noreferrer noopener">Get your Adobe Fonts API token &raquo;</a>
            </td>
        </tr>
        <tr class="hover">
            <th class="no-bg">
                <label for="default_unit">Default Unit</label>
                <p>Select default unit for font sizes, line heights and letter spacing.</p>
            </th>
            <td>
                <select name="default_unit" id="default_unit">
			        <?php
			        foreach ( TypoLab_Data::get_units() as $value => $title ) {
				        ?>
                        <option value="<?php echo esc_attr( $value ); ?>"<?php selected( TypoLab::$default_unit, $value ); ?>><?php echo esc_html( $title ); ?></option>
				        <?php
			        }
			        ?>
                </select>
            </td>
        </tr>
        <tr class="hover">
            <th class="no-bg">
                <label for="font_preview_text">
                    Font Preview Text
                </label>
                <p>Preview text to display font variants.</p>
            </th>
            <td>
                <input type="text" name="font_preview_text" id="font_preview_text" value="<?php echo esc_attr( TypoLab::$font_preview_str ); ?>">
            </td>
        </tr>
        <tr class="hover">
            <th class="no-bg">
                <label for="font_preview_size">
                    Font Preview Size
                </label>
                <p>Enter font size for preview text in pixels unit.</p>
            </th>
            <td>
                <input type="number" name="font_preview_size" id="font_preview_size" value="<?php echo esc_attr( TypoLab::$font_preview_size ); ?>">
            </td>
        </tr>
        <tr class="hover">
            <th class="no-bg">
                <label>Fonts Directory</label>
                <p>Fonts directory and permissions.</p>
            </th>
            <td>
                <div class="directory-permissions <?php echo $is_writable ? 'writable' : 'not-writable'; ?>">
                    <input type="text" value="<?php echo esc_attr( $fonts_directory ); ?>" class="code" readonly>
                    <span><?php echo esc_html( $permissions_text ) ?></span>
                </div>

				<?php if ( ! $is_writable ) : ?>
                    <p>If you don't know how to make directory writable, <a href="https://www.dummies.com/web-design-development/wordpress/navigation-customization/how-to-change-file-permissions-using-filezilla-on-your-ftp-site/" target="_blank">click here</a> to learn more.</p>
				<?php endif; ?>
            </td>
        </tr>
        </tbody>
    </table>

    <div class="row-layout-flex font-export-import">
        <div class="col col-6">

            <table class="typolab-table typolab-table--alt font-export-import--export horizontal-borders">
                <thead>
                <tr>
                    <th>Export Fonts</th>
                </tr>
                </thead>
                <tbody>
                <tr class="vtop">
                    <td>
                        <div class="font-export-checkboxes">
                            <p>Choose what type of settings you want to export:</p>

                            <ul>
                                <li>
                                    <label>
                                        <input type="checkbox" name="font_export" value="fonts" checked>
                                        Fonts
                                    </label>
                                </li>
                                <li>
                                    <label>
                                        <input type="checkbox" name="font_export" value="font_appearance" checked>
                                        Font Sizes
                                    </label>
                                </li>
                                <li>
                                    <label>
                                        <input type="checkbox" name="font_export" value="font_settings">
                                        Font Settings
                                    </label>
                                </li>
                            </ul>
                        </div>

                        <div class="font-export-loading">Font export in progress&hellip;</div>

                        <div class="font-export-data">
                            <textarea name="font_export_data" id="font_export_data" rows="5" readonly></textarea>
                        </div>
                    </td>
                </tr>
                </tbody>
                <tfoot>
                <tr>
                    <th>
                        <button class="button" type="button">
                            <span class="kalium-admin-icon kalium-admin-icon-import"></span>
                            Export
                        </button>
                    </th>
                </tr>
                </tfoot>
            </table>

        </div>
        <div class="col col-6">

            <table class="typolab-table typolab-table--alt font-export-import--import horizontal-borders">
                <thead>
                <tr>
                    <th>Import Fonts</th>
                </tr>
                </thead>
                <tbody>
                <tr class="vtop">
                    <td>
                        <div class="font-import-data">
                            <label for="font_import">Paste the font export string in the field below to import fonts:</label>

                            <textarea name="font_import" id="font_import" rows="4"></textarea>
                        </div>

                        <div class="font-import-loading">
                            <i class="kalium-admin-icon-refresh"></i>
                            Import in progress&hellip; <span class="progress"></span>
                        </div>

                        <div class="font-import-finished">
                            <i class="kalium-admin-icon-check"></i>
                            Import finished successfully
                        </div>
                    </td>
                </tr>
                </tbody>
                <tfoot>
                <tr>
                    <th>
                        <button class="button" type="button">
                            <span class="kalium-admin-icon kalium-admin-icon-export"></span>
                            Import
                        </button>
                    </th>
                </tr>
                </tfoot>
            </table>

        </div>
    </div>

	<?php wp_nonce_field( 'typolab-save-settings' ); ?>
	<?php submit_button( 'Save Changes', 'primary', 'save_settings' ); ?>
</form>