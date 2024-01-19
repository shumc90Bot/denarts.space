<?php
/**
 * TypoLab - ultimate font management library.
 *
 * Data holder class.
 *
 * @author Laborator
 * @link   https://laborator.co
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class TypoLab_Data {

	/**
	 * Get font sources.
	 *
	 * @return array
	 */
	public static function get_font_sources() {
		return apply_filters( 'typolab_font_sources', [
			'google'        => [
				'name'        => 'Google Fonts',
				'description' => "Google's free font directory is one of the most exciting developments in web typography in a very long time.\n\nGoogle Fonts catalog are published under licenses that allow you to use them on any website, whether it’s commercial or personal.\n\nChoose between <strong>1000+</strong> available fonts to use with your site."
			],
			'font-squirrel' => [
				'name'        => 'Font Squirrel',
				'description' => "Font Squirrel is a collection of free fonts for commercial use.\n\nApart from Google fonts, Font Squirrel requires to download and install fonts in order to use them. Installation process is automatic, just hit the <strong>Download</strong> button.\n\nChoose between <strong>1000+</strong> available fonts to use with your site."
			],
			'laborator'     => [
				'name'        => 'Premium Fonts',
				'description' => "Premium fonts worth of <strong>$149</strong> (per site) are available for Laborator customers only.\n\nIt has the same installation procedures as Font Squirrel, you need to download and install fonts that you want to use in this site.\n\nTheme registration is required in order to install and use fonts from this source."
			],
			'adobe'         => [
				'name'        => 'Adobe Fonts',
				'description' => "Adobe Fonts is a subscription service for fonts which you can use on a website.\n\nInstead of licensing individual fonts, you can sign up for the plan that best suits your needs and get a library of fonts from which to choose.\n\nTo import Adobe Fonts fonts in your site, simply enter the <strong>Adobe Fonts API Token</strong> in settings page and you are all set."
			],
			'hosted'        => [
				'name'        => 'Self-Hosted Fonts',
				'description' => "We have made it easier to upload web font formats such WOFF2, WOFF, TTF, EOT and SVG.\n\nFor better support you can upload all file formats, however WOFF2 is enough for modern browsers.\n\nThis method also complies with GDPR regulations by hosting the font on your website rather than fetching from external sources.",
			],
			'external'      => [
				'name'        => 'External Fonts',
				'description' => "If you can't find the right font from above sources then Custom Fonts got covered you.\n\nTo import a custom font, simply enter the stylesheet URL that includes @font-face's and specify font variant names.\n\nThis font type is suitable for services that provide stylesheet URL only and not the web fonts individually."
			],
			'system'        => [
				'name'        => 'System Fonts',
				'description' => "A System Font is a font which is compatible with the respective operating system. What that means is that when a website loads, the font doesn’t have to be downloaded by the browser.

They are used by Weather.com, GitHub, Bootstrap, Medium, Ghost, Booking.com and even this WordPress dashboard. This can help reduce the overall page weight on your website. While this is not huge, remember every little optimization you make adds up to a speedy website.",
			],
		] );
	}

	/**
	 * Get font source.
	 *
	 * @param string $font_source_id
	 *
	 * @return array|null
	 */
	public static function get_font_source( $font_source_id ) {
		return kalium_get_array_key( self::get_font_sources(), $font_source_id );
	}

	/**
	 * Get base selectors.
	 *
	 * @return array
	 */
	public static function get_base_selectors() {
		return apply_filters( 'typolab_base_selectors', [

			// Sitewide
			'sitewide'   => [
				'name'        => 'Sitewide',
				'description' => 'Applied as default font for all elements in the page.',
				'selectors'   => TypoLab_CSS_Selectors::$SELECTORS_SITEWIDE,
				'selected'    => true,
			],

			// Headings
			'headings'   => [
				'name'        => 'Headings',
				'description' => 'Applied on all title headings: h1, h2, h3, h4, h5, h6.',
				'selectors'   => TypoLab_CSS_Selectors::group(
					TypoLab_CSS_Selectors::$SELECTORS_H1,
					TypoLab_CSS_Selectors::$SELECTORS_H2,
					TypoLab_CSS_Selectors::$SELECTORS_H3,
					TypoLab_CSS_Selectors::$SELECTORS_H4,
					TypoLab_CSS_Selectors::$SELECTORS_H5,
					TypoLab_CSS_Selectors::$SELECTORS_H6
				),
			],

			// Paragraphs
			'paragraphs' => [
				'name'        => 'Paragraphs',
				'description' => 'Applied all over the site on paragraphs and plain text.',
				'selectors'   => TypoLab_CSS_Selectors::$SELECTORS_PARAGRAPHS,
			],
		] );
	}

	/**
	 * Get single base selector.
	 *
	 * @param string $id
	 *
	 * @return array|null
	 */
	public static function get_base_selector( $id ) {
		foreach ( self::get_base_selectors() as $base_selector_id => $base_selector ) {
			if ( $base_selector_id === $id ) {
				return $base_selector;
			}
		}

		return null;
	}

	/**
	 * Get predefined selectors for use in custom selectors.
	 *
	 * @return array
	 */
	public static function get_predefined_selectors() {
		return apply_filters( 'typolab_predefined_selectors', [
			'headings'       => [
				'name'      => 'Headings',
				'selectors' => TypoLab_CSS_Selectors::group(
					TypoLab_CSS_Selectors::$SELECTORS_H1,
					TypoLab_CSS_Selectors::$SELECTORS_H2,
					TypoLab_CSS_Selectors::$SELECTORS_H3,
					TypoLab_CSS_Selectors::$SELECTORS_H4,
					TypoLab_CSS_Selectors::$SELECTORS_H5,
					TypoLab_CSS_Selectors::$SELECTORS_H6
				),
			],
			'paragraphs'     => [
				'name'      => 'Paragraphs',
				'selectors' => TypoLab_CSS_Selectors::$SELECTORS_PARAGRAPHS,
			],
			'blockquotes'    => [
				'name'      => 'Blockquotes',
				'selectors' => TypoLab_CSS_Selectors::$SELECTORS_BLOCKQUOTE,
			],
			'form_inputs'    => [
				'name'      => 'Form Inputs',
				'selectors' => TypoLab_CSS_Selectors::$SELECTORS_FORM_INPUTS,
			],
			'footer'         => [
				'name'      => 'Footer',
				'selectors' => TypoLab_CSS_Selectors::$SELECTORS_FOOTER,
			],
			'menus'          => [
				'name'      => 'Menus',
				'selectors' => TypoLab_CSS_Selectors::$SELECTORS_MENUS,
			],
			'menu-items'     => [
				'name'      => 'Menu Items',
				'selectors' => TypoLab_CSS_Selectors::$SELECTORS_MENU_ITEMS,
			],
			'sub-menu-items' => [
				'name'      => 'Submenu Items',
				'selectors' => TypoLab_CSS_Selectors::$SELECTORS_SUB_MENU_ITEMS,
			],
		] );
	}

	/**
	 * Get single predefined selector.
	 *
	 * @param string $id
	 *
	 * @return array|null
	 */
	public static function get_predefined_selector( $id ) {
		foreach ( self::get_predefined_selectors() as $selector_id => $selector ) {
			if ( $selector_id === $id ) {
				return $selector;
			}
		}

		return null;
	}

	/**
	 * Get font appearance groups.
	 *
	 * @return array
	 */
	public static function get_font_appearance_groups() {
		return apply_filters( 'typolab_font_appearance_groups', [

			// Headings group
			[
				'id'       => 'headings',
				'name'     => 'Headings',
				'elements' => [
					'h1' => [
						'name'      => 'H1',
						'selectors' => TypoLab_CSS_Selectors::$SELECTORS_H1,
					],
					'h2' => [
						'name'      => 'H2',
						'selectors' => TypoLab_CSS_Selectors::$SELECTORS_H2,
					],
					'h3' => [
						'name'      => 'H3',
						'selectors' => TypoLab_CSS_Selectors::$SELECTORS_H3,
					],
					'h4' => [
						'name'      => 'H4',
						'selectors' => TypoLab_CSS_Selectors::$SELECTORS_H4,
					],
					'h5' => [
						'name'      => 'H5',
						'selectors' => TypoLab_CSS_Selectors::$SELECTORS_H5,
					],
					'h6' => [
						'name'      => 'H6',
						'selectors' => TypoLab_CSS_Selectors::$SELECTORS_H6,
					],
				],
			],

			// Paragraphs group
			[
				'id'       => 'paragraphs',
				'name'     => 'Paragraphs',
				'elements' => [
					'p' => [
						'name'      => 'P',
						'selectors' => TypoLab_CSS_Selectors::$SELECTORS_PARAGRAPHS,
					],
				],
			],

			// Header group
			[
				'id'       => 'header',
				'name'     => 'Header',
				'elements' => [
					'header_text'    => [
						'name'      => 'Default Text',
						'selectors' => TypoLab_CSS_Selectors::$SELECTORS_HEADER,
					],
					'header_top_bar' => [
						'name'      => 'Top Header Bar',
						'selectors' => TypoLab_CSS_Selectors::$SELECTORS_HEADER_TOP_BAR,
					],
				],
			],

			// Footer group
			[
				'id'       => 'footer',
				'name'     => 'Footer',
				'elements' => [
					'footer_widgets_title'   => [
						'name'      => 'Widgets Title',
						'selectors' => TypoLab_CSS_Selectors::$SELECTORS_FOOTER_WIDGETS_TITLE,
					],
					'footer_widgets_content' => [
						'name'      => 'Widgets Content',
						'selectors' => TypoLab_CSS_Selectors::$SELECTORS_FOOTER_WIDGETS_CONTENT,
					],
					'footer_copyrights'      => [
						'name'      => 'Copyrights',
						'selectors' => TypoLab_CSS_Selectors::$SELECTORS_FOOTER_COPYRIGHTS,
					],
				],
			],

			// Standard Menu group
			[
				'id'       => 'standard-menu',
				'name'     => 'Standard Menu',
				'elements' => [
					'standard_menu'     => [
						'name'      => 'Main Menu Items',
						'selectors' => TypoLab_CSS_Selectors::$SELECTORS_MENU_STANDARD,
					],
					'standard_menu_sub' => [
						'name'      => 'Sub Menu Items',
						'selectors' => TypoLab_CSS_Selectors::$SELECTORS_MENU_STANDARD_SUB,
					],
				],
			],

			// Fullscreen Menu group
			[
				'id'       => 'fullscreen-menu',
				'name'     => 'Fullscreen Menu',
				'elements' => [
					'fullscreen_menu'     => [
						'name'      => 'Main Menu Items',
						'selectors' => TypoLab_CSS_Selectors::$SELECTORS_MENU_FULLSCREEN,
					],
					'fullscreen_menu_sub' => [
						'name'      => 'Sub Menu Items',
						'selectors' => TypoLab_CSS_Selectors::$SELECTORS_MENU_FULLSCREEN_SUB,
					],
				],
			],

			// Top Menu group
			[
				'id'       => 'top-menu',
				'name'     => 'Top Menu',
				'elements' => [
					'top_menu'                 => [
						'name'      => 'Main Menu Items',
						'selectors' => TypoLab_CSS_Selectors::$SELECTORS_MENU_TOP,
					],
					'top_menu_sub'             => [
						'name'      => 'Sub Menu Items',
						'selectors' => TypoLab_CSS_Selectors::$SELECTORS_MENU_TOP_SUB,
					],
					'top_menu_widgets_title'   => [
						'name'      => 'Widgets Title',
						'selectors' => TypoLab_CSS_Selectors::$SELECTORS_MENU_TOP_WIDGETS_TITLE,
					],
					'top_menu_widgets_content' => [
						'name'      => 'Widgets Content',
						'selectors' => TypoLab_CSS_Selectors::$SELECTORS_MENU_TOP_WIDGETS_CONTENT,
					],
				],
			],

			// Sidebar Menu group
			[
				'id'       => 'sidebar-menu',
				'name'     => 'Sidebar Menu',
				'elements' => [
					'sidebar_menu'                 => [
						'name'      => 'Main Menu Items',
						'selectors' => TypoLab_CSS_Selectors::$SELECTORS_MENU_SIDEBAR,
					],
					'sidebar_menu_sub'             => [
						'name'      => 'Sub Menu Items',
						'selectors' => TypoLab_CSS_Selectors::$SELECTORS_MENU_SIDEBAR_SUB,
					],
					'sidebar_menu_widgets_title'   => [
						'name'      => 'Widgets Title',
						'selectors' => TypoLab_CSS_Selectors::$SELECTORS_MENU_SIDEBAR_WIDGETS_TITLE,
					],
					'sidebar_menu_widgets_content' => [
						'name'      => 'Widgets Content',
						'selectors' => TypoLab_CSS_Selectors::$SELECTORS_MENU_SIDEBAR_WIDGETS_CONTENT,
					],
				],
			],

			// Mobile Menu group
			[
				'id'       => 'mobile-menu',
				'name'     => 'Mobile Menu',
				'elements' => [
					'mobile_menu'     => [
						'name'      => 'Main Menu Items',
						'selectors' => TypoLab_CSS_Selectors::$SELECTORS_MENU_MOBILE,
					],
					'mobile_menu_sub' => [
						'name'      => 'Sub Menu Items',
						'selectors' => TypoLab_CSS_Selectors::$SELECTORS_MENU_MOBILE_SUB,
					],
				],
			],

			// Portfolio group
			[
				'id'       => 'portfolio',
				'name'     => 'Portfolio',
				'elements' => [
					'portfolio_item_title'        => [
						'name'      => 'Item Title',
						'selectors' => TypoLab_CSS_Selectors::$SELECTORS_PORTFOLIO_ITEM_TITLE,
					],
					'portfolio_item_title_single' => [
						'name'      => 'Item Title (Single)',
						'selectors' => TypoLab_CSS_Selectors::$SELECTORS_PORTFOLIO_ITEM_TITLE_SINGLE,
					],
					'portfolio_item_categories'   => [
						'name'      => 'Item Categories',
						'selectors' => TypoLab_CSS_Selectors::$SELECTORS_PORTFOLIO_ITEM_CATEGORIES,
					],
					'portfolio_item_sub_titles'   => [
						'name'      => 'Sub Titles',
						'selectors' => TypoLab_CSS_Selectors::$SELECTORS_PORTFOLIO_ITEM_SUB_TITLES,
					],
					'portfolio_item_content'      => [
						'name'      => 'Item Content',
						'selectors' => TypoLab_CSS_Selectors::$SELECTORS_PORTFOLIO_ITEM_CONTENT,
					],
					'portfolio_checklist_title'   => [
						'name'      => 'Services Title',
						'selectors' => TypoLab_CSS_Selectors::$SELECTORS_PORTFOLIO_ITEM_CHECKLIST_TITLES,
					],
					'portfolio_checklist_content' => [
						'name'      => 'Services Content',
						'selectors' => TypoLab_CSS_Selectors::$SELECTORS_PORTFOLIO_ITEM_CHECKLIST_CONTENT,
					],
				],
			],

			// Shop group
			[
				'id'       => 'shop',
				'name'     => 'Shop',
				'elements' => [
					'shop_product_title'        => [
						'name'      => 'Product Title',
						'selectors' => TypoLab_CSS_Selectors::$SELECTORS_SHOP_PRODUCT_TITLE,
					],
					'shop_product_title_single' => [
						'name'      => 'Product Title (Single)',
						'selectors' => TypoLab_CSS_Selectors::$SELECTORS_SHOP_PRODUCT_TITLE_SINGLE,
					],
					'shop_product_categories'   => [
						'name'      => 'Product Categories',
						'selectors' => TypoLab_CSS_Selectors::$SELECTORS_SHOP_PRODUCT_CATEGORIES,
					],
					'shop_product_content'      => [
						'name'      => 'Product Content',
						'selectors' => TypoLab_CSS_Selectors::$SELECTORS_SHOP_PRODUCT_PRODUCT_CONTENT,
					],
				],
			],

			// Blog group
			[
				'id'       => 'blog',
				'name'     => 'Blog',
				'elements' => [
					'blog_post_title'        => [
						'name'      => 'Post Title',
						'selectors' => TypoLab_CSS_Selectors::$SELECTORS_BLOG_POST_TITLE,
					],
					'blog_post_title_single' => [
						'name'      => 'Post Title (Single)',
						'selectors' => TypoLab_CSS_Selectors::$SELECTORS_BLOG_POST_TITLE_SINGLE,
					],
					'blog_post_excerpt'      => [
						'name'      => 'Post Excerpt',
						'selectors' => TypoLab_CSS_Selectors::$SELECTORS_BLOG_POST_EXCERPT,
					],
					'blog_post_content'      => [
						'name'      => 'Post Content',
						'selectors' => TypoLab_CSS_Selectors::$SELECTORS_BLOG_POST_CONTENT,
					],
				],
			],
		] );
	}

	/**
	 * Get font appearance element.
	 *
	 * @param string $group_id
	 * @param string $id
	 *
	 * @return array|null
	 */
	public static function get_font_appearance_element( $group_id, $id ) {
		foreach ( self::get_font_appearance_groups() as $group ) {
			if ( $group['id'] === $group_id ) {
				foreach ( $group['elements'] as $element_id => $element ) {
					if ( $element_id === $id ) {
						return $element;
					}
				}
			}
		}

		return null;
	}

	/**
	 * Get responsive breakpoints.
	 *
	 * @return array
	 */
	public static function get_responsive_breakpoints() {
		return apply_filters( 'typolab_responsive_breakpoints', [

			// General
			'general' => [
				'name'     => 'General',
				'icon'     => 'kalium-admin-icon-device-desktop kalium-icon-size-13',
				'min_size' => null,
				'max_size' => null,
				'default'  => true,
			],

			// Tablet
			'tablet'  => [
				'name'     => 'Tablet',
				'icon'     => 'kalium-admin-icon-device-tablet kalium-icon-size-13',
				'min_size' => null,
				'max_size' => 992,
				'inherit'  => 'general',
			],

			// Mobile
			'mobile'  => [
				'name'     => 'Mobile',
				'icon'     => 'kalium-admin-icon-device-phone kalium-icon-size-13',
				'min_size' => null,
				'max_size' => 768,
				'inherit'  => 'tablet',
			],
		] );
	}

	/**
	 * Get single responsive breakpoint.
	 *
	 * @param string $id
	 *
	 * @return array|null
	 */
	public static function get_responsive_breakpoint( $id ) {
		foreach ( self::get_responsive_breakpoints() as $breakpoint_id => $responsive_breakpoint ) {
			if ( $id === $breakpoint_id ) {
				return $responsive_breakpoint;
			}
		}

		return null;
	}

	/**
	 * Get font units.
	 *
	 * @return array
	 */
	public static function get_units() {
		return apply_filters( 'typolab_units', [
			'px'  => 'PX',
			'em'  => 'EM',
			'rem' => 'REM',
			'pt'  => 'PT',
			'vw'  => 'VW',
			'%'   => '%',
		] );
	}
}
