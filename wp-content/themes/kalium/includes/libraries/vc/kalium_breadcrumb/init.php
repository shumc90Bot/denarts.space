<?php
/**
 * Kalium WordPress Theme
 *
 * Breadcrumb widget.
 *
 * @author Laborator
 * @link   https://kaliumtheme.com
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

// Plugin warning
$breadcrumb_navxt_warning = '';

if ( ! kalium()->is->breadcrumb_navxt_active() ) {
	$breadcrumb_navxt_warning = '<br><br><div class="wpb_element_wrapper">
		<div class="wpb_element_wrapper vc_message_box vc_message_box-square vc_message_box-solid vc_color-info" style="padding: 0.5em;">
			Breadcrumb NavXT plugin is not installed or activated!<br> 
			To configure <strong>Breadcrumb</strong> you need to install <a href="' . admin_url( 'plugin-install.php?s=breadcrumb+navxt&tab=search&type=term' ) . '">Breadcrumb NavXT</a> plugin first.
		</div>
	</div>';
}

vc_map( [
	'base'        => 'kalium_breadcrumb',
	'name'        => 'Breadcrumb',
	"description" => "Page location",
	'category'    => 'Laborator',
	'icon'        => kalium()->locate_file_url( 'includes/libraries/vc/kalium_breadcrumb/breadcrumb.svg' ),
	'params'      => [


		// Extra class
		[
			'type'        => 'textfield',
			'heading'     => 'Extra class name',
			'param_name'  => 'el_class',
			'description' => 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.' . $breadcrumb_navxt_warning
		],

		// CSS Editor
		[
			'type'       => 'css_editor',
			'heading'    => 'Css',
			'param_name' => 'css',
			'group'      => 'Design options'
		],
	],
] );

class WPBakeryShortCode_Kalium_Breadcrumb extends WPBakeryShortCode {
}