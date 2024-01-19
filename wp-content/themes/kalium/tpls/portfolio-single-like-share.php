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

if ( ! ( isset( $portfolio_args['share'] ) && $portfolio_args['share'] || isset( $portfolio_args['likes'] ) && $portfolio_args['likes'] ) ) {
	return;
}

$portfolio_like_share_layout = $portfolio_args['share_layout'];

// Default Layout
if ( $portfolio_like_share_layout == 'default' ) :

	?>
    <div class="social-links-plain">

		<?php if ( $portfolio_args['likes'] ) : $likes = get_post_likes(); ?>
            <div class="likes">
				<?php
				/**
				 * Like button.
				 */
				kalium_like_button( [
					'display_count' => true,
					'icon'          => $portfolio_args['likes_icon'],
				] );
				?>
            </div>
		<?php endif; ?>

		<?php if ( $portfolio_args['share'] ) : ?>
            <div class="share-social">
                <h4><?php _e( 'Share', 'kalium' ); ?></h4>
                <div class="social-links">
					<?php
					foreach ( $portfolio_args['share_networks']['visible'] as $network_id => $network ) :
						if ( $network_id == 'placebo' ) {
							continue;
						}

						kalium_social_network_share_post_link( $network_id, $post_id );
					endforeach;
					?>
                </div>
            </div>
		<?php endif; ?>

    </div>
<?php

endif;

// Rounded Buttons
if ( $portfolio_like_share_layout == 'rounded' ) :

	?>
    <div class="social-links-rounded">

        <div class="social-links">
			<?php if ( $portfolio_args['likes'] ) : $likes = get_post_likes(); ?>
				<?php
				/**
				 * Like button.
				 */
				kalium_like_button( [
					'display_count' => true,
					'icon'          => $portfolio_args['likes_icon'],
					'class'         => [
						'social-share-icon',
						'like-button--small-bubbles',
					],
				] );
				?>
			<?php endif; ?>

			<?php
			if ( $portfolio_args['share'] ) :

				foreach ( $portfolio_args['share_networks']['visible'] as $network_id => $network ) :

					if ( 'placebo' == $network_id ) {
						continue;
					}

					kalium_social_network_share_post_link( $network_id, $post_id, [
						'icon_only' => true,
						'class'     => 'social-share-icon',
					] );

				endforeach;

			endif;
			?>
        </div>

    </div>
<?php

endif;
