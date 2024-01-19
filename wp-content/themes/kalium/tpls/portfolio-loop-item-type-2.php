<?php
/**
 * Kalium WordPress Theme
 *
 * Laborator.co
 * www.laborator.co
 *
 * @deprecated 3.0 This template file will be removed or replaced with new one in templates/ folder.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

// Get Portfolio Item Details
include locate_template( 'tpls/portfolio-loop-item-details.php' );

// Main Vars
$portfolio_image_size = 'portfolio-img-2';
$hover_effect         = $portfolio_args['layouts']['type_2']['hover_effect'];
$hover_transparency   = $portfolio_args['layouts']['type_2']['hover_transparency'];

// Item Classes
if ( 'normal' == $portfolio_args['layouts']['type_2']['grid_spacing'] ) {
	$item_class[] = 'has-padding';
}

// Custom value for Transparency
if ( in_array( $custom_hover_color_transparency, [ 'opacity', 'no-opacity' ] ) ) {
	$hover_transparency = $custom_hover_color_transparency;
}

// Hover effect style
$custom_hover_effect_style = '';

if ( ! in_array( $hover_effect_style, [ 'inherit', '' ] ) ) {
    $hover_effect = $hover_effect_style;
}

// Columns
$columns = $portfolio_args['columns'];

// Get column width for Masonry Portfolio Mode
$box_size = '';

if ( isset( $portfolio_args['masonry_items'][ $portfolio_item_id ] ) ) {
	$box_size = $portfolio_args['masonry_items'][ $portfolio_item_id ]['box_size'];

	if ( $post_featured_video_element ) {
		$box_size = explode( 'x', $box_size );
		$columns  = 12 / $box_size[0];
		$box_size = false;
	}
}

// Custom Box Size (Masonry Portfolio Mode)
if ( $box_size ) {
	$grid_spacing = 30;

	// Apply custom spacing
	if ( $portfolio_args['layouts']['type_2']['default_spacing'] ) {
		$grid_spacing = $portfolio_args['layouts']['type_2']['default_spacing'];
	}

	// Merged Images
	if ( $portfolio_args['layouts']['type_2']['grid_spacing'] == 'merged' ) {
		$grid_spacing = 0;
	}

	// Columns Size for Masonry Grid
	$cw = apply_filters( 'kalium_portfolio_masonry_col_width', 120 );
	$ch = apply_filters( 'kalium_portfolio_masonry_col_height', 120 );

	// Split Box Size
	$bs        = explode( 'x', $box_size );
	$bs_width  = $bs[0];
	$bs_height = $bs[1];

	$portfolio_image_size = $masonry_asel_size = [
		floor( $cw * $bs_width ),
		floor( $ch * $bs_height )
	];

	$portfolio_image_size[0] -= $grid_spacing;

	// Size by CSS Class
	$item_class[] = 'masonry-portfolio-item';
	$item_class[] = 'w' . $bs_width;

	// Mobile Image
	$mobile_image_size = apply_filters( 'kalium_portfolio_masonry_mobile_image', [ 768, 500 ], $portfolio_image_size );
	$mobile_image      = kalium_get_attachment_image( $post_thumbnail_id, $mobile_image_size );

	// Support for Masonry with proportional thumbs
	if ( apply_filters( 'kalium_portfolio_masonry_proportional_thumbs', false ) ) {
		$portfolio_image_size = array( $portfolio_image_size[0], 0 );
	}
} // Default Column Size
else {
	$item_class[] = kalium_portfolio_get_columns_class( $columns );

	// Dynamic Image Height
	if ( $portfolio_args['layouts']['type_2']['dynamic_image_height'] && ! preg_match( "/^[a-z_-]+$/i", $portfolio_image_size ) ) {
		$portfolio_image_size = 'portfolio-img-3';
	}
}


// Hover State Class
$hover_state_class = [];

$hover_state_class[] = 'hover-state';
$hover_state_class[] = 'padding';
$hover_state_class[] = 'hover-eff-fade-slide';

$hover_state_class[] = 'position-' . $portfolio_args['layouts']['type_2']['hover_text_position'];
$hover_state_class[] = 'hover-' . ( $custom_hover_effect_style ? $custom_hover_effect_style : $hover_effect );
$hover_state_class[] = 'hover-style-' . $portfolio_args['layouts']['type_2']['hover_style'];
$hover_state_class[] = 'opacity-' . ( $hover_transparency == 'opacity' ? 'yes' : 'no' );

// Custom Hover Layer Options
if ( in_array( $hover_layer_options, [ 'always-hover', 'hover-reverse' ] ) ) {
	$hover_state_class[] = 'hover-is-visible';

	if ( $hover_layer_options == 'hover-reverse' ) {
		$hover_state_class[] = 'hover-reverse';
	}
} else if ( 'none' == $hover_layer_options ) {
	$hover_effect = 'none';
}

// Disable linking
if ( 'external' == $item_linking && '#' == $item_launch_link_href ) {
	$portfolio_item_href = '#';
	$item_class[]        = 'not-clickable';
}

// No Hover
if ( 'none' == $hover_effect ) {
	$item_class[] = 'hover-disabled';
}

// Item Thumbnail
$image = kalium_get_attachment_image( $post_thumbnail_id, apply_filters( 'kalium_portfolio_loop_thumbnail_size', $portfolio_image_size, 'type-2' ) );

// Hide hover layer when featured video is shown and few settings are toggled
if ( $post_featured_video_element && ( $featured_video_controls || ( ! $featured_video_autoplay && ! $featured_video_controls ) ) ) {
	$hover_effect = 'none';

	ob_start();
	include locate_template( 'tpls/portfolio-loop-item-categories.php' );
	$categories = ob_get_clean();

	$post_featured_video_element .= sprintf(
		'<div class="portfolio-video-info"><h3><a href="%2$s"%3$s>%1$s</a></h3>%4$s</div>',
		get_the_title(),
		get_permalink(),
		kalium_conditional( $portfolio_item_new_window, ' target="_blank" rel="noopener"', '' ),
		$categories
	);
}

// WOW effect attributes
$wow_attributes = '';

if ( $reveal_delay ) {
	$wow_attributes .= ' data-wow-delay="' . esc_attr( $reveal_delay ) . 's"';
}

// Like Icon Class
$like_icon_default = 'far fa-heart';
$like_icon_liked   = 'fas fa-heart';

switch ( $portfolio_args['likes_icon'] ) {
	// Star Icon
	case 'star':
		$like_icon_default = 'far fa-star';
		$like_icon_liked   = 'fas fa-star';
		break;

	// Thumb Up Icon
	case 'thumb':
		$like_icon_default = 'far fa-thumbs-up';
		$like_icon_liked   = 'fas fa-thumbs-up';
		break;
}
?>
<div <?php post_class( $item_class ); ?> data-portfolio-item-id="<?php echo $portfolio_item_id; ?>"<?php if ( $portfolio_terms_slugs ) : ?> data-terms="<?php echo implode( ' ', $portfolio_terms_slugs ); ?>"<?php endif; ?>>

	<?php
	// Custom Background
	if ( $custom_hover_background_color ) {
		kalium_append_custom_css( "#{$portfolio_args['id']} .post-{$portfolio_item_id} .item-box .thumb .hover-state", "background-color: {$custom_hover_background_color} !important;" );
	}
	?>

	<?php do_action( 'kalium_portfolio_item_before', $portfolio_item_type ); ?>

	<?php
	// When using Masonry Portfolio Mode
	if ( $box_size ) :
		if ( is_string( $mobile_image_size ) && preg_match_all( '(width=\"([0-9]+)\"|height="([0-9]+))', $mobile_image, $mobile_image_matches ) ) {
			$width             = $mobile_image_matches[1][0];
			$height            = $mobile_image_matches[2][1];
			$mobile_image_size = [ $width, $height ];
		}

		$masonry_box_size_asel      = laborator_generate_as_element( $masonry_asel_size );
		$masonry_mobile_box_size_el = laborator_generate_as_element( $mobile_image_size );
		?>
        <div class="<?php echo esc_attr( "masonry-box {$masonry_box_size_asel} {$show_effect}" ); ?>"<?php echo $wow_attributes; ?>>
			<?php
			if ( $post_featured_video_element ) :
				echo $post_featured_video_element;
			else:
				?>
                <a href="<?php echo esc_url( $portfolio_item_href ); ?>" class="item-link masonry-thumb" aria-label="<?php echo esc_html( $portfolio_item_title ); ?>">
					<?php echo $image; ?>
                </a>
			<?php endif; ?>
        </div>

        <div class="masonry-box masonry-mobile-box <?php echo $masonry_mobile_box_size_el; ?>">
            <a href="<?php echo esc_url( $portfolio_item_href ); ?>" class="item-link masonry-thumb" aria-label="<?php echo esc_html( $portfolio_item_title ); ?>">
				<?php echo $mobile_image; ?>
            </a>
        </div>
	<?php
	endif;
	// End: When using Portfolio Masonry Mode
	?>

    <div class="item-box-container">
        <div class="<?php echo esc_attr( "item-box {$show_effect}" ); ?>"<?php echo $wow_attributes; ?>>
            <div class="thumb">
				<?php if ( $hover_effect != 'none' ) : ?>
                    <div class="<?php echo implode( ' ', $hover_state_class ); ?>">

						<?php if ( $portfolio_args['likes'] && $portfolio_args['layouts']['type_2']['show_likes'] ) : $likes = get_post_likes(); ?>
                            <div class="likes">
								<?php
								/**
								 * Like button.
								 */
								kalium_like_button( [
									'icon' => $portfolio_args['likes_icon'],
								] );
								?>
                            </div>
						<?php endif; ?>

                        <div class="info">
                            <h3>
                                <a href="<?php echo esc_url( $portfolio_item_href ); ?>" class="item-link" aria-label="<?php echo esc_html( $portfolio_item_title ); ?>"<?php echo when_match( $portfolio_item_new_window, 'target="_blank" rel="noopener"' ); ?>>
									<?php echo wp_kses_post( $portfolio_item_title ); ?>
                                </a>
                            </h3>
							<?php include locate_template( 'tpls/portfolio-loop-item-categories.php' ); ?>
                        </div>
                    </div>
				<?php endif; ?>

				<?php if ( ! $box_size ) : ?>
					<?php
					if ( $post_featured_video_element ) :
						echo $post_featured_video_element;
					else:
						?>
                        <a href="<?php echo esc_url( $portfolio_item_href ); ?>" class="item-link" aria-label="<?php echo esc_html( $portfolio_item_title ); ?>"<?php echo when_match( $portfolio_item_new_window, 'target="_blank" rel="noopener"' ); ?>>
							<?php echo $image; ?>
                        </a>
					<?php endif; ?>
				<?php else: ?>
                    <a href="<?php echo esc_url( $portfolio_item_href ); ?>" class="thumb-placeholder <?php echo esc_attr( $masonry_box_size_asel ); ?>"></a>
				<?php endif; ?>
            </div>
        </div>
    </div>

	<?php do_action( 'kalium_portfolio_item_after' ); ?>

</div>
