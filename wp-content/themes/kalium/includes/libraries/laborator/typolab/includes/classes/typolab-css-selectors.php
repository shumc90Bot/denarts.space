<?php
/**
 * TypoLab - ultimate font management library.
 *
 * List of selectors.
 *
 * @author Laborator
 * @link   https://laborator.co
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class TypoLab_CSS_Selectors {

	/**
	 * Sitewide selectors.
	 *
	 * @var string[]
	 */
	public static $SELECTORS_SITEWIDE = [
		'body',
	];

	/**
	 * H1 selectors.
	 *
	 * @var string[]
	 */
	public static $SELECTORS_H1 = [
		'h1',
		'.h1',
		'.section-title h1',
	];

	/**
	 * H2 selectors.
	 *
	 * @var string[]
	 */
	public static $SELECTORS_H2 = [
		'h2',
		'.h2',
		'.single-post .post-comments--section-title h2',
		'.section-title h2',
	];

	/**
	 * H3 selectors.
	 *
	 * @var string[]
	 */
	public static $SELECTORS_H3 = [
		'h3',
		'.h3',
		'.section-title h3',
	];

	/**
	 * H4 selectors.
	 *
	 * @var string[]
	 */
	public static $SELECTORS_H4 = [
		'h4',
		'.h4',
		'.section-title h4',
	];

	/**
	 * H5 selectors.
	 *
	 * @var string[]
	 */
	public static $SELECTORS_H5 = [
		'h5',
		'.h5',
	];

	/**
	 * H6 selectors.
	 *
	 * @var string[]
	 */
	public static $SELECTORS_H6 = [
		'h6',
		'.h6',
	];

	/**
	 * Paragraphs.
	 *
	 * @var string[]
	 */
	public static $SELECTORS_PARAGRAPHS = [
		'p',
		'.section-title p',
	];

	/**
	 * Blockquote.
	 *
	 * @var string[]
	 */
	public static $SELECTORS_BLOCKQUOTE = [
		'blockquote',
	];

	/**
	 * Form inputs.
	 *
	 * @var string[]
	 */
	public static $SELECTORS_FORM_INPUTS = [
		'input',
		'select',
	];

	/**
	 * Menus.
	 *
	 * @var string[]
	 */
	public static $SELECTORS_MENUS = [
		'ul.menu',
	];

	/**
	 * Menu items.
	 *
	 * @var string[]
	 */
	public static $SELECTORS_MENU_ITEMS = [
		'.standard-menu-container .menu > li > a > span',
		'.fullscreen-menu .menu > li > a > span',
		'.top-menu-container .top-menu ul li a',
		'.sidebar-menu-wrapper .sidebar-menu-container .sidebar-main-menu .menu > li > a > span',
	];

	/**
	 * Sub menu items.
	 *
	 * @var string[]
	 */
	public static $SELECTORS_SUB_MENU_ITEMS = [
		'.standard-menu-container .menu li li a span',
		'.fullscreen-menu .menu li li a span',
		'.top-menu-container .top-menu ul li li a',
		'.sidebar-menu-wrapper .sidebar-menu-container .sidebar-main-menu .menu li li a span',
	];

	/**
	 * Header.
	 *
	 * @var string[]
	 */
	public static $SELECTORS_HEADER = [
		'.site-header .header-block__item',
	];

	/**
	 * Top header bar.
	 *
	 * @var string[]
	 */
	public static $SELECTORS_HEADER_TOP_BAR = [
		'.site-header .top-header-bar .header-block__item',
	];

	/**
	 * Standard menu.
	 *
	 * @var string[]
	 */
	public static $SELECTORS_MENU_STANDARD = [
		'.main-header.menu-type-standard-menu .standard-menu-container div.menu>ul>li>a',
		'.main-header.menu-type-standard-menu .standard-menu-container ul.menu>li>a',
	];

	/**
	 * Standard sub menus.
	 *
	 * @var string[]
	 */
	public static $SELECTORS_MENU_STANDARD_SUB = [
		'.main-header.menu-type-standard-menu .standard-menu-container div.menu>ul ul li a',
		'.main-header.menu-type-standard-menu .standard-menu-container ul.menu ul li a',
	];

	/**
	 * Fullscreen menu.
	 *
	 * @var string[]
	 */
	public static $SELECTORS_MENU_FULLSCREEN = [
		'.main-header.menu-type-full-bg-menu .fullscreen-menu nav ul li a',
	];

	/**
	 * Fullscreen sub menus.
	 *
	 * @var string[]
	 */
	public static $SELECTORS_MENU_FULLSCREEN_SUB = [
		'.main-header.menu-type-full-bg-menu .fullscreen-menu nav div.menu>ul ul li a',
		'.main-header.menu-type-full-bg-menu .fullscreen-menu nav ul.menu ul li a',
	];

	/**
	 * Top menu.
	 *
	 * @var string[]
	 */
	public static $SELECTORS_MENU_TOP = [
		'.top-menu-container .top-menu ul li a',
	];

	/**
	 * Top sub menus.
	 *
	 * @var string[]
	 */
	public static $SELECTORS_MENU_TOP_SUB = [
		'.top-menu div.menu>ul>li ul>li>a',
		'.top-menu ul.menu>li ul>li>a',
	];

	/**
	 * Top menu widgets title.
	 *
	 * @var string[]
	 */
	public static $SELECTORS_MENU_TOP_WIDGETS_TITLE = [
		'.top-menu-container .widget h3',
	];

	/**
	 * Top menu widgets content.
	 *
	 * @var string[]
	 */
	public static $SELECTORS_MENU_TOP_WIDGETS_CONTENT = [
		'.top-menu-container .widget',
		'.top-menu-container .widget p',
		'.top-menu-container .widget div',
	];

	/**
	 * Sidebar menu.
	 *
	 * @var string[]
	 */
	public static $SELECTORS_MENU_SIDEBAR = [
		'.sidebar-menu-wrapper .sidebar-menu-container .sidebar-main-menu div.menu>ul>li>a',
		'.sidebar-menu-wrapper .sidebar-menu-container .sidebar-main-menu ul.menu>li>a',
	];

	/**
	 * Sidebar sub menus.
	 *
	 * @var string[]
	 */
	public static $SELECTORS_MENU_SIDEBAR_SUB = [
		'.sidebar-menu-wrapper .sidebar-menu-container .sidebar-main-menu div.menu>ul li ul li:hover>a',
		'.sidebar-menu-wrapper .sidebar-menu-container .sidebar-main-menu ul.menu li ul li>a',
	];

	/**
	 * Sidebar widgets title.
	 *
	 * @var string[]
	 */
	public static $SELECTORS_MENU_SIDEBAR_WIDGETS_TITLE = [
		'.sidebar-menu-wrapper .sidebar-menu-container .sidebar-menu-widgets .widget .widget-title',
	];

	/**
	 * Sidebar widgets content.
	 *
	 * @var string[]
	 */
	public static $SELECTORS_MENU_SIDEBAR_WIDGETS_CONTENT = [
		'.sidebar-menu-wrapper .widget',
		'.sidebar-menu-wrapper .widget p',
		'.sidebar-menu-wrapper .widget div',
	];

	/**
	 * Mobile menu.
	 *
	 * @var string[]
	 */
	public static $SELECTORS_MENU_MOBILE = [
		'.mobile-menu-wrapper .mobile-menu-container div.menu>ul>li>a',
		'.mobile-menu-wrapper .mobile-menu-container ul.menu>li>a',
		'.mobile-menu-wrapper .mobile-menu-container .cart-icon-link-mobile-container a',
		'.mobile-menu-wrapper .mobile-menu-container .search-form input',
	];

	/**
	 * Mobile sub menus.
	 *
	 * @var string[]
	 */
	public static $SELECTORS_MENU_MOBILE_SUB = [
		'.mobile-menu-wrapper .mobile-menu-container div.menu>ul>li ul>li>a',
		'.mobile-menu-wrapper .mobile-menu-container ul.menu>li ul>li>a',
	];

	/**
	 * Portfolio title.
	 *
	 * @var string[]
	 */
	public static $SELECTORS_PORTFOLIO_ITEM_TITLE = [
		'.portfolio-holder .thumb .hover-state .info h3',
		'.portfolio-holder .item-box .info h3',
	];

	/**
	 * Portfolio title (single page).
	 *
	 * @var string[]
	 */
	public static $SELECTORS_PORTFOLIO_ITEM_TITLE_SINGLE = [
		'.single-portfolio-holder .title h1',
		'.single-portfolio-holder.portfolio-type-5 .portfolio-description-container .portfolio-description-showinfo h3',
	];

	/**
	 * Portfolio categories.
	 *
	 * @var string[]
	 */
	public static $SELECTORS_PORTFOLIO_ITEM_CATEGORIES = [
		'.portfolio-holder .thumb .hover-state .info p',
		'.portfolio-holder .item-box .info h3',
	];

	/**
	 * Portfolio sub titles.
	 *
	 * @var string[]
	 */
	public static $SELECTORS_PORTFOLIO_ITEM_SUB_TITLES = [
		'.single-portfolio-holder .section-title p',
	];

	/**
	 * Portfolio content.
	 *
	 * @var string[]
	 */
	public static $SELECTORS_PORTFOLIO_ITEM_CONTENT = [
		'.portfolio-description-showinfo p',
		'.single-portfolio-holder .details .project-description p',
		'.gallery-item-description .post-formatting p',
	];

	/**
	 * Portfolio checklist titles.
	 *
	 * @var string[]
	 */
	public static $SELECTORS_PORTFOLIO_ITEM_CHECKLIST_TITLES = [
		'.single-portfolio-holder .details .services h3',
	];

	/**
	 * Portfolio checklist content.
	 *
	 * @var string[]
	 */
	public static $SELECTORS_PORTFOLIO_ITEM_CHECKLIST_CONTENT = [
		'.single-portfolio-holder .details .services ul li',
	];

	/**
	 * Shop title.
	 *
	 * @var string[]
	 */
	public static $SELECTORS_SHOP_PRODUCT_TITLE = [
		'.woocommerce .product .item-info h3 a',
		'.woocommerce .product .item-info .price ins',
		'.woocommerce .product .item-info .price>.amount',
	];

	/**
	 * Shop title (single page).
	 *
	 * @var string[]
	 */
	public static $SELECTORS_SHOP_PRODUCT_TITLE_SINGLE = [
		'.woocommerce .item-info h1',
		'.woocommerce .single-product .summary .single_variation_wrap .single_variation>.price>.amount',
		'.woocommerce .single-product .summary div[itemprop=offers]>.price>.amount',
	];

	/**
	 * Shop categories.
	 *
	 * @var string[]
	 */
	public static $SELECTORS_SHOP_PRODUCT_CATEGORIES = [
		'.woocommerce .product.catalog-layout-transparent-bg .item-info .product-terms a',
	];

	/**
	 * Shop product content.
	 *
	 * @var string[]
	 */
	public static $SELECTORS_SHOP_PRODUCT_PRODUCT_CONTENT = [
		'.woocommerce .item-info p',
		'.woocommerce .item-info .product_meta',
		'.woocommerce .single-product .summary .variations .label label',
		'.woocommerce .summary p',
		'.woocommerce-tabs .woocommerce-Tabs-panel',
	];

	/**
	 * Blog post title.
	 *
	 * @var string[]
	 */
	public static $SELECTORS_BLOG_POST_TITLE = [
		'.blog-posts .box-holder .post-info h2',
		'.wpb_wrapper .lab-blog-posts .blog-post-entry .blog-post-content-container .blog-post-title',
	];

	/**
	 * Blog post title (single page).
	 *
	 * @var string[]
	 */
	public static $SELECTORS_BLOG_POST_TITLE_SINGLE = [
		'.single-blog-holder .blog-title h1',
	];

	/**
	 * Blog post post excerpt.
	 *
	 * @var string[]
	 */
	public static $SELECTORS_BLOG_POST_EXCERPT = [
		'.blog-post-excerpt p',
		'.post-info p',
	];

	/**
	 * Blog post post content.
	 *
	 * @var string[]
	 */
	public static $SELECTORS_BLOG_POST_CONTENT = [
		'.blog-content-holder .post-content',
	];

	/**
	 * Footer.
	 *
	 * @var string[]
	 */
	public static $SELECTORS_FOOTER = [
		'.site-footer *',
	];

	/**
	 * Footer widgets title.
	 *
	 * @var string[]
	 */
	public static $SELECTORS_FOOTER_WIDGETS_TITLE = [
		'.site-footer .footer-widgets .widget h1',
		'.site-footer .footer-widgets .widget h2',
		'.site-footer .footer-widgets .widget h3',
	];

	/**
	 * Footer widgets content.
	 *
	 * @var string[]
	 */
	public static $SELECTORS_FOOTER_WIDGETS_CONTENT = [
		'.site-footer .footer-widgets .widget .textwidget',
		'.site-footer .footer-widgets .widget p',
	];

	/**
	 * Footer copyrights.
	 *
	 * @var string[]
	 */
	public static $SELECTORS_FOOTER_COPYRIGHTS = [
		'.copyrights, .site-footer .footer-bottom-content a',
		'.site-footer .footer-bottom-content p',
	];

	/**
	 * Group two or more selectors.
	 *
	 * @param $args,...
	 *
	 * @return array
	 */
	public static function group( $args ) {
		$group = [];

		foreach ( func_get_args() as $arg ) {
			if ( is_array( $arg ) ) {
				$group = array_merge( $group, $arg );
			} else if ( is_string( $arg ) ) {
				$group = array_merge( $group, [ $arg ] );
			}
		}

		return $group;
	}
}
