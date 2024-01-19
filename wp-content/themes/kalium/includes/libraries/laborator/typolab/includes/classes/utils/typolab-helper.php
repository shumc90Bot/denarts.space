<?php
/**
 * TypoLab - ultimate font management library.
 *
 * Helper class of TypoLab.
 *
 * @author Laborator
 * @link   https://laborator.co
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class TypoLab_Helper {

	/**
	 * Chrome useragent.
	 *
	 * @const string
	 */
	const USER_AGENT_CHROME = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.114 Safari/537.36';

	/**
	 * IE useragent.
	 *
	 * @const string
	 */
	const USER_AGENT_IE = 'Mozilla/5.0 (Windows NT 10.0; WOW64; Trident/7.0; rv:11.0) like Gecko';

	/**
	 * Get template path from relative to templates directory.
	 *
	 * @param string $relative_path
	 *
	 * @return string
	 */
	public static function get_template_path( $relative_path ) {
		return trailingslashit( TypoLab::$typolab_dir ) . 'includes/templates/' . $relative_path;
	}

	/**
	 * Prepare font selector tabs from $font_list.
	 *
	 * @param array $font_list
	 *
	 * @return array
	 */
	public static function prepare_font_selector_tabs( $font_list ) {
		$tabs_counter = [];
		$tabs_list    = [];

		$font_list = array_reverse( $font_list );

		foreach ( $font_list as $item ) {
			$first_letter = strtoupper( substr( $item['font_family'], 0, 1 ) );
			if ( ! isset( $tabs_counter[ $first_letter ] ) ) {
				$tabs_counter[ $first_letter ] = 0;
			}

			$tabs_counter[ $first_letter ] ++;
		}

		// Sort by keys
		ksort( $tabs_counter );

		// Prepare tabs list
		foreach ( $tabs_counter as $first_letter => $count ) {
			$tabs_list[] = [
				'value'      => $first_letter,
				'title'      => $first_letter,
				'title-attr' => sprintf( '%s fonts', number_format( $count ) ),
			];
		}

		return $tabs_list;
	}

	/**
	 * Prepare font selector categories from $font_list.
	 *
	 * @param array $font_list
	 *
	 * @return array
	 */
	public static function prepare_font_selector_categories( $font_list ) {
		$categories_counter = [];
		$categories         = [];

		foreach ( $font_list as $font ) {
			$category = $font['category'];

			if ( ! isset( $categories_counter[ $category ] ) ) {
				$categories_counter[ $category ] = 0;
			}

			$categories_counter[ $category ] ++;
		}

		// Sort by keys
		ksort( $categories_counter );

		// Prepare tabs list
		foreach ( $categories_counter as $category => $count ) {
			$categories[] = [
				'name'  => $category,
				'title' => self::font_category_nicename( $category ),
				'count' => $count,
			];
		}

		return $categories;
	}

	/**
	 * Get nicename for font category.
	 *
	 * @param string $category
	 */
	public static function font_category_nicename( $category ) {
		switch ( $category ) {
			case 'display':
				$category = 'Display';
				break;

			case 'handwriting':
				$category = 'Handwriting';
				break;

			case 'monospace':
				$category = 'Monospace';
				break;

			case 'sans-serif':
				$category = 'Sans Serif';
				break;

			case 'serif':
				$category = 'Serif';
				break;
		}

		return $category;
	}

	/**
	 * Get conditional loading statements.
	 *
	 * @return array
	 */
	public static function get_conditional_loading_statements() {
		$general_statements = $post_types_statements = $taxonomies_statements = [];

		// Page types
		$page_types = [
			'frontpage' => 'Front Page',
			'blog'      => 'Blog Page',
			'search'    => 'Search Page',
			'not_found' => '404 Page',
		];

		// Post types
		$post_types            = [];
		$registered_post_types = get_post_types( [
			'public' => true,
		], 'objects' );

		// Taxonomies
		$taxonomies = [];

		/**
		 * @var WP_Post_Type $post_type
		 */
		foreach ( $registered_post_types as $post_type_id => $post_type ) {

			// Skip attachments
			if ( in_array( $post_type_id, [ 'attachment' ] ) ) {
				continue;
			}

			// Post types
			$post_types[ $post_type_id ] = $post_type->labels->name;

			// Posts
			$posts_list      = [];
			$post_type_posts = get_posts( [
				'post_type'      => $post_type_id,
				'posts_per_page' => 200, // Maximum items,
			] );

			foreach ( $post_type_posts as $post ) {
				$posts_list[ $post->ID ] = $post->post_title;
			}

			// Add to statements group
			if ( ! empty( $posts_list ) ) {
				$post_types_statements[] = [
					'name'   => $post_type_id,
					'title'  => $post_type->labels->singular_name,
					'values' => $posts_list,
				];
			}

			// Taxonomies
			foreach ( get_object_taxonomies( $post_type_id, 'objects' ) as $taxonomy ) {
				$taxonomy_name = $taxonomy->name;

				// Unsupported taxonomies
				if ( isset( $taxonomies[ $taxonomy_name ] ) || in_array( $taxonomy_name, [
						'product_visibility',
						'product_shipping_class',
						'product_type',
						'post_format',
					] ) ) {
					continue;
				}

				$terms_list     = [];
				$taxonomy_terms = get_terms( [
					'taxonomy'   => $taxonomy_name,
					'hide_empty' => true,
				] );

				foreach ( $taxonomy_terms as $term ) {
					$terms_list[ $term->slug ] = $term->name;
				}

				// Add to statements group
				if ( ! empty( $terms_list ) ) {
					$taxonomies_statements[] = [
						'name'   => $taxonomy_name,
						'title'  => $taxonomy->labels->name,
						'values' => $terms_list,
					];
				}

				// Taxonomies list
				$taxonomies[ $taxonomy_name ] = $taxonomy->labels->name;
			}
		}

		// Page templates
		$page_templates = [];

		foreach ( get_page_templates() as $template_name => $template_filename ) {
			$page_templates[ $template_filename ] = $template_name;
		}

		// General statements
		$general_statements[] = [
			'name'   => 'post_type',
			'title'  => 'Post Type',
			'values' => $post_types,
		];

		$general_statements[] = [
			'name'   => 'page_type',
			'title'  => 'Page Type',
			'values' => $page_types,
		];

		$general_statements[] = [
			'name'   => 'page_template',
			'title'  => 'Page Template',
			'values' => $page_templates,
		];

		$general_statements[] = [
			'name'   => 'taxonomy',
			'title'  => 'Taxonomy',
			'values' => $taxonomies,
		];

		return [
			'General'     => $general_statements,
			'Single Post' => $post_types_statements,
			'Taxonomies'  => $taxonomies_statements,
		];
	}

	/**
	 * Extract font sources from stylesheet.
	 *
	 * @return array
	 */
	public static function extract_font_sources( $stylesheet ) {
		$font_sources = [];

		if ( preg_match_all( '/url\(\K[^\)]+(?=\))/', $stylesheet, $matches ) ) {
			foreach ( $matches[0] as $font_source ) {
				$file_info = pathinfo( $font_source );

				$font_sources[] = [
					'url'       => $font_source,
					'filename'  => kalium_get_array_key( $file_info, 'filename' ),
					'extension' => kalium_get_array_key( $file_info, 'extension' ),
					'basename'  => basename( $font_source ),
				];
			}
		}

		return $font_sources;
	}

	/**
	 * Quote string. Usable for wrapping font family name.
	 *
	 * @param string $str
	 *
	 * @return string
	 */
	public static function quote( $str ) {
		return sprintf( '"%s"', $str );
	}

	/**
	 * Compress CSS.
	 *
	 * @param string $css
	 *
	 * @return string
	 */
	public static function minimize_css( $css ) {
		$css = preg_replace( '/\/\*((?!\*\/).)*\*\//', '', $css ); // negative look ahead
		$css = preg_replace( '/\s{2,}/', ' ', $css );
		$css = preg_replace( '/\s*([:;{}])\s*/', '$1', $css );
		$css = preg_replace( '/;}/', '}', $css );

		return $css;
	}
}
