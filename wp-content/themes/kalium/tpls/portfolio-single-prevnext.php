<?php
/**
 *    Kalium WordPress Theme
 *
 *    Laborator.co
 *    www.laborator.co
 *
 * @deprecated 3.0 This template file will be removed or replaced with new one in templates/ folder.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

if ( ! kalium_get_theme_option( 'portfolio_prev_next' ) ) {
	return;
}

// Prev and next text
$prev_text = __( 'Previous project', 'kalium' );
$next_text = __( 'Next project', 'kalium' );

// Archive link
$portfolio_archive_link = $portfolio_args['url'];

// Custom archive link
if ( ! empty( $portfolio_custom_archive_url ) ) {
	$portfolio_archive_link = $portfolio_custom_archive_url;
} // Archive link to category page
elseif ( $portfolio_args['archive_url_to_category'] ) {
	$portfolio_item_terms = get_the_terms( $post_id, 'portfolio_category' );

	if ( ! empty( $portfolio_item_terms ) ) {
		$portfolio_archive_link = get_term_link( reset( $portfolio_item_terms ) );
	}
}

// Prev-next navigation options
$prev_next_type        = $portfolio_args['single']['prev_next']['type'];
$prev_next_show_titles = $portfolio_args['single']['prev_next']['show_titles'];
$navigation_position   = $portfolio_args['single']['prev_next']['position'];
$in_same_term          = $portfolio_args['single']['prev_next']['include_categories'];

// Display post title as link text
if ( $prev_next_show_titles ) {
	$prev_next_show_titles = 'title';
}

// Show related items
if ( $in_same_term ) {
	$item_object_terms = wp_get_object_terms( $post_id, 'portfolio_category' );

	if ( is_wp_error( $item_object_terms ) || empty( $item_object_terms ) ) {
		$in_same_term = false;
	}
}

// In Full background portfolio set prev/next navigation to fixed-right side mode
if ( ! empty( $portfolio_type_full_bg ) ) {
	$prev_next_type      = 'fixed';
	$navigation_position = 'right-side';
}

if ( 'simple' === $prev_next_type ) :

	?>
    <div class="row">
        <div class="col-xs-12">
            <div class="portfolio-big-navigation portfolio-navigation-type-simple wow fadeIn<?php echo $image_spacing == 'nospacing' ? ' with-margin' : ''; ?>">
                <div class="row">
                    <div class="col-xs-5">
						<?php
						/**
						 * Previous post link.
						 */
						kalium_adjacent_post_link( [
							'prev'         => true,
							'next_text'    => $prev_text,
							'display'      => $prev_next_show_titles,
							'in_same_term' => $in_same_term,
							'taxonomy'     => 'portfolio_category',
							'arrow'        => 'left',
						] );
						?>
                    </div>

                    <div class="col-xs-2 text-on-center">
                        <a class="back-to-portfolio" href="<?php echo esc_url( $portfolio_archive_link ); ?>">
                            <i class="flaticon-four60"></i>
                        </a>
                    </div>

                    <div class="col-xs-5 text-align-right">
						<?php
						/**
						 * Next post link.
						 */
						kalium_adjacent_post_link( [
							'next'         => true,
							'prev_text'    => $next_text,
							'display'      => $prev_next_show_titles,
							'in_same_term' => $in_same_term,
							'taxonomy'     => 'portfolio_category',
							'arrow'        => 'right',
						] );
						?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php

endif;

if ( 'fixed' === $prev_next_type ) :

	$prev = kalium_adjacent_post_link( [
		'prev'         => true,
		'post_object'  => true,
		'in_same_term' => $in_same_term,
		'taxonomy'     => 'portfolio_category',
	] );

	$next = kalium_adjacent_post_link( [
		'next'         => true,
		'post_object'  => true,
		'in_same_term' => $in_same_term,
		'taxonomy'     => 'portfolio_category',
	] );

	?>
    <div class="portfolio-navigation portfolio-navigation-type-fixed <?php echo esc_attr( $navigation_position ); ?> wow fadeIn" data-wow-duration="0.5s" data-wow-delay="0.9s">

        <a class="previous<?php echo ! $prev ? ' not-clickable' : ''; ?>" href="<?php echo get_permalink( $prev ); ?>" title="<?php echo esc_attr( $prev_text ); ?>">
            <i class="fa flaticon-arrow427"></i>
        </a>

        <a class="back-to-portfolio" href="<?php echo esc_url( $portfolio_archive_link ); ?>" title="<?php esc_attr_e( 'Go to portfolio archive', 'kalium' ); ?>">
            <i class="fa flaticon-four60"></i>
        </a>

        <a class="next<?php echo ! $next ? ' not-clickable' : ''; ?>" href="<?php echo get_permalink( $next ); ?>" title="<?php echo esc_attr( $next_text ); ?>">
            <i class="fa flaticon-arrow413"></i>
        </a>

    </div>
<?php

endif;
