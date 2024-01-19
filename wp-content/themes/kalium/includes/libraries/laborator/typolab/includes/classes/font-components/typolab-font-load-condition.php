<?php
/**
 * TypoLab - ultimate font management library.
 *
 * Font load condition class.
 *
 * @author Laborator
 * @link   https://laborator.co
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class TypoLab_Font_Load_Condition {

	/**
	 * Uses exportable.
	 */
	use TypoLab_Exportable;

	/**
	 * Match functions.
	 *
	 * @var array
	 */
	public static $match_functions = [
		'post_type'     => 'match_post_type',
		'page_type'     => 'match_page_type',
		'page_template' => 'match_page_template',
		'taxonomy'      => 'match_taxonomy',
		'post'          => 'match_singular',
		'page'          => 'match_singular',
		'category'      => 'match_singular_taxonomy',
		'tag'           => 'match_singular_taxonomy',
	];

	/**
	 * Statement type.
	 *
	 * @var string
	 */
	public $type;

	/**
	 * Compare operator.
	 *
	 * @var string
	 */
	public $operator;

	/**
	 * Value to match.
	 *
	 * @var string
	 */
	public $value;

	/**
	 * Constructor.
	 *
	 * @param array $args
	 */
	public function __construct( $args = [] ) {

		// Set props
		foreach ( get_object_vars( $this ) as $prop_name => $prop_value ) {
			$this->{$prop_name} = isset( $args[ $prop_name ] ) ? $args[ $prop_name ] : $prop_value;
		}
	}

	/**
	 * Check if operator type is "not equals".
	 *
	 * @return bool
	 */
	public function is_not_equals() {
		return 'not-equals' === $this->operator;
	}

	/**
	 * Compare values.
	 *
	 * @param mixed $var1
	 * @param mixed $var2
	 *
	 * @return bool
	 */
	public function compare( $var1, $var2 = null ) {
		$return = $var1;

		// Compare against $var2
		if ( ! is_null( $var2 ) ) {
			$return = $var2 === $var1;
		}

		if ( $this->is_not_equals() ) {
			$return = ! $return;
		}

		return ! ! $return;
	}

	/**
	 * Check if condition matches current criteria.
	 *
	 * @return bool
	 */
	public function match() {
		static $custom_post_types, $custom_taxonomies;

		// Add custom post types match functions
		if ( ! isset( $custom_post_types ) ) {
			$custom_post_types = get_post_types( [
				'public'   => true,
				'_builtin' => false,
			] );
		}

		// Add custom taxonomy match functions
		if ( ! isset( $custom_taxonomies ) ) {
			$custom_taxonomies = get_taxonomies( [
				'public'   => true,
				'_builtin' => false,
			] );
		}

		// Process custom post types single pages with 'match_singular'
		foreach ( $custom_post_types as $post_type ) {
			self::$match_functions[ $post_type ] = "match_singular";
		}

		// Process custom taxonomy pages with 'match_singular_taxonomy'
		foreach ( $custom_taxonomies as $taxonomy ) {
			self::$match_functions[ $taxonomy ] = "match_singular_taxonomy";
		}

		// Get match function
		$match_function = kalium_get_array_key( self::$match_functions, $this->type );

		if ( method_exists( $this, $match_function ) ) {
			$match = (bool) call_user_func( [ $this, $match_function ] );

			// Only one needs to match
			if ( $match ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Match function: Post type.
	 *
	 * @return bool
	 */
	public function match_post_type() {
		$value = is_post_type_archive( $this->value ) || is_singular( $this->value );

		return $this->compare( $value );
	}

	/**
	 * Match function: Page type.
	 *
	 * @return bool
	 */
	public function match_page_type() {
		$value = false;

		switch ( $this->value ) {

			// Font page
			case 'frontpage':
				$value = is_front_page();
				break;

			// Blog page
			case 'blog':
				$value = is_home();
				break;

			// Search page
			case 'search':
				$value = is_search();
				break;

			// Not found (404) page
			case 'not_found':
				$value = is_404();
				break;
		}

		return $this->compare( $value );
	}

	/**
	 * Match function: Page template.
	 *
	 * @return bool
	 */
	public function match_page_template() {
		$value = is_page_template( $this->value );

		return $this->compare( $value );
	}

	/**
	 * Match function: Taxonomy archive or single item.
	 *
	 * @param int|string $id
	 *
	 * @return bool
	 */
	public function match_taxonomy( $id = '' ) {

		// Match taxonomy type
		switch ( $this->value ) {

			// Categories
			case 'category':
				$value = is_category( $id );
				break;

			// Tags
			case 'tags':
				$value = is_tag( $id );
				break;

			// Default (custom post type)
			default:
				$value = is_tax( $this->value, $id );
		}

		return $this->compare( $value );
	}

	/**
	 * Match function: Singular.
	 *
	 * @return bool
	 */
	public function match_singular() {
		$value = ( is_single() || is_page() || is_singular() ) && (int) $this->value === get_queried_object_id();

		return $this->compare( $value );
	}

	/**
	 * Match function: Singular.
	 *
	 * @return bool
	 */
	public function match_singular_taxonomy() {
		$value = ( is_category() || is_tag() || is_tax() ) && $this->value === get_queried_object()->slug;

		return $this->compare( $value );
	}
}
