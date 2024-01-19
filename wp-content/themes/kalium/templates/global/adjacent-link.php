<?php
/**
 * Kalium WordPress Theme
 *
 * Adjacent link for page or any post type.
 *
 * @author   Laborator
 * @link     https://kaliumtheme.com
 * @version  3.2
 *
 * @var array   $classes
 * @var string  $adjacent_post_link
 * @var string  $adjacent_post_text
 * @var boolean $icon
 * @var string  $icon_class
 * @var string  $secondary_text
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

?>
<a href="<?php echo esc_url( $adjacent_post_link ); ?>" <?php kalium_class_attr( $classes ); ?>>
	<?php if ( $icon ) : ?>
        <span class="adjacent-post-link__icon">
            <i class="<?php echo esc_html( $icon_class ); ?>"></i>
        </span>
	<?php endif; ?>

    <span class="adjacent-post-link__text">
        <?php if ( $secondary_text ) : ?>
            <span class="adjacent-post-link__text-secondary">
                <?php echo esc_html( $secondary_text ); ?>
            </span>
		<?php endif; ?>

        <span class="adjacent-post-link__text-primary">
            <?php echo esc_html( $adjacent_post_text ); ?>
        </span>
    </span>
</a>
