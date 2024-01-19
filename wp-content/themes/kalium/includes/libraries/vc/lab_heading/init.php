<?php
/**
 *    Heading Title
 *
 *    Laborator.co
 *    www.laborator.co
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

// Element Information
$lab_vc_element_icon = kalium()->locate_file_url( 'includes/libraries/vc/lab_heading/heading.svg' );

vc_map( [
	'base'        => 'lab_heading',
	'name'        => 'Heading',
	"description" => "Title and description",
	'category'    => 'Laborator',
	'icon'        => $lab_vc_element_icon,
	'params'      => [
		[
			'type'        => 'dropdown',
			'heading'     => 'Title Tag',
			'param_name'  => 'title_tag',
			'admin_label' => true,
			'std'         => 'H2',
			'value'       => [
				'H1',
				'H2',
				'H3',
				'H4',
				'H5',
				'H6'
			],
			'description' => 'Set heading title container tag for SEO purpose.'
		],
		[
			'type'        => 'textfield',
			'heading'     => 'Title',
			'param_name'  => 'title',
			'admin_label' => true,
			'value'       => 'Heading title'
		],
		[
			'type'       => 'textarea',
			'heading'    => 'Content',
			'param_name' => 'content',
			'value'      => 'Enter your description about the heading title here.'
		],
		[
			'type'        => 'textfield',
			'heading'     => 'Heading ID',
			'param_name'  => 'el_id',
			'description' => sprintf( 'Optional. Enter element ID (Note: make sure it is unique and valid according to %sw3c specification%s).', '<a href="https://www.w3schools.com/tags/att_global_id.asp" target="_blank">', '</a>' )
		],
		[
			'type'        => 'textfield',
			'heading'     => 'Extra class name',
			'param_name'  => 'el_class',
			'description' => 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.'
		],
		[
			'type'       => 'css_editor',
			'heading'    => 'Css',
			'param_name' => 'css',
			'group'      => 'Design options'
		]
	]
] );

class WPBakeryShortCode_Lab_Heading extends WPBakeryShortCode {
}