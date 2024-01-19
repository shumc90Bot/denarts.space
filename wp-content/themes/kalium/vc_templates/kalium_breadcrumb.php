<?php
/**
 * Kalium WordPress Theme
 *
 * Breadcrumb template.
 *
 * @author Laborator
 * @link   https://kaliumtheme.com
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

// Attributes
$atts             = vc_map_get_attributes( $this->getShortcode(), $atts );
$background_color = kalium_get_array_key( $atts, 'background_color' );
$text_color       = kalium_get_array_key( $atts, 'text_color' );
$border_color     = kalium_get_array_key( $atts, 'border_color' );
$border_type      = kalium_get_array_key( $atts, 'border_type' );
$text_alignment   = kalium_get_array_key( $atts, 'text_alignment' );


// CSS and extra classes
$css       = vc_shortcode_custom_css_class( $atts['css'], ' ' );
$el_class  = $this->getExtraClass( $atts['el_class'] );
$css_class = trim( preg_replace( '/\s+/', ' ', 'kalium-wpb-breadcrumb ' . $el_class ) );
$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $css_class, $this->settings['base'], $atts );
?>
<div class="<?php echo esc_attr( $css_class ); ?>">
	<?php
	/**
	 * Breadcrumb.
	 */
	kalium_breadcrumb( [
		'container' => false,
		'class'     => $css,
	] );
	?>
</div>
