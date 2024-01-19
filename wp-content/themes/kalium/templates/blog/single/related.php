<?php
/**
 * Kalium WordPress Theme
 *
 * Related posts.
 *
 * @var int   $columns
 * @var int[] $related_posts
 *
 * @author  Laborator
 * @link    https://kaliumtheme.com
 * @version 3.4.4
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

global $blog_options;

if ( ! empty( $related_posts ) ) :

	// Title
	$title = apply_filters( 'kalium_related_products_heading', __( 'Related posts', 'kalium' ) );

	if ( $title ) :
		?>
        <h3 class="related-posts-title"><?php echo esc_html( $title ); ?></h3>
	<?php

	endif;

    // Use standard blog posts layout
	$blog_options['blog_template'] = 'standard';

	// Container classes
	$container_classes = kalium_blog_get_option( 'loop/container_classes' );

	// Related posts class
	$container_classes[] = 'related-posts';

	// Columns
	$container_classes[] = 'columns-' . $columns;

	// Fit rows layout
	if ( 'fit-rows' === kalium_blog_get_option( 'loop/row_layout_mode' ) ) {
		$container_classes[] = 'fit-rows';
	}

	// Gap
	$columns_gap = kalium_blog_get_option( 'loop/other/columns_gap' );

	if ( 'standard' == kalium_blog_get_template() && '' !== $columns_gap ) {
		$columns_gap         = intval( $columns_gap );
		$container_classes[] = sprintf( 'columns-gap-%s', $columns_gap >= 0 ? $columns_gap : 'none' );
	}

	// Borderless layout
	if ( 'standard' === kalium_blog_get_option( 'blog_template' ) && 'no' === kalium_blog_get_option( 'loop/other/borders' ) ) {
		$container_classes[] = 'blog-posts--borderless';
	}

	?>
    <ul <?php kalium_class_attr( $container_classes ); ?>>
		<?php
		// Show related posts
		foreach ( $related_posts as $post_id ) :
			$post_object = get_post( $post_id );
			setup_postdata( $GLOBALS['post'] =& $post_object );

			// Post item template
			kalium_blog_loop_post_template();
		endforeach;
		?>
    </ul>
<?php
endif;

wp_reset_postdata();
