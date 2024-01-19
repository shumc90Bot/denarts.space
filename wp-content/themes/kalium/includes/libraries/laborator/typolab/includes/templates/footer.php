<?php
/**
 * TypoLab - ultimate font management library.
 *
 * TypoLab footer template.
 *
 * @author Laborator
 * @link   https://laborator.co
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="typolab-footer">
    &copy; TypoLab 2.0 &ndash; an ultimate font management library developed by <a href="https://laborator.co/" target="_blank">Laborator.co</a>
</div>
<script>
	var typolab_settings = {
		previewText: '<?php echo esc_attr( TypoLab::$font_preview_str ); ?>',
		units: <?php echo wp_json_encode( TypoLab_Data::get_units() ); ?>,
		defaultUnit: '<?php echo esc_attr( TypoLab::$default_unit ); ?>',
	};
</script>