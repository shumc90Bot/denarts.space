<?php
/**
 * TypoLab - ultimate font management library.
 *
 * Fonts appearance page.
 *
 * @author Laborator
 * @link   https://laborator.co
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Font appearance groups
$font_appearance_groups = TypoLab_Data::get_font_appearance_groups();

?>
<form method="post" enctype="application/x-www-form-urlencoded" class="font-appearance-groups">
	<?php
	// Render font appearance groups
    array_walk( $font_appearance_groups, [ 'TypoLab_UI_Components', 'font_appearance_group_form' ] );

	// JSON data container
	TypoLab_UI_Components::encode_json( 'font_appearance', [
		'responsive' => TypoLab_Data::get_responsive_breakpoints(),
		'groups'     => $font_appearance_groups,
		'values'     => TypoLab_Font_Appearance_Settings::get_settings( true ),
	] );
	?>

	<?php wp_nonce_field( 'typolab-save-font-appearance-settings' ); ?>
	<?php submit_button( 'Save Changes', 'primary', 'save_font_appearance_settings' ); ?>
</form>
