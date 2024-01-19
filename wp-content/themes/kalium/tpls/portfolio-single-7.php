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

include locate_template( 'tpls/portfolio-single-item-details.php' );

$classes = [
	'vc-container',
	'portfolio-vc-type-container',
	'single-portfolio-holder',
	'portfolio-type-7',
];

// If there is no Visual Composer Content
if ( ! preg_match( "/\[vc_row.*?\]/i", get_queried_object()->post_content ) ) {
	$classes[] = 'container';
}

do_action( 'kalium_portfolio_item_before', 'type-7' );
?>

<div <?php kalium_class_attr( $classes ); ?>>
	<?php the_content(); ?>
</div>

<div class="container">

    <div class="page-container">

        <div class="single-portfolio-holder">

			<?php include locate_template( 'tpls/portfolio-single-prevnext.php' ); ?>

        </div>

    </div>

</div>