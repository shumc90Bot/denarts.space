<?php
/**
 * Kalium WordPress Theme
 *
 * Breadcrumb template.
 *
 * @var array  $classes
 * @var bool   $container
 * @var array  $container_classes
 * @var string $breadcrumb_html
 *
 * @author  Laborator
 * @version 3.2
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

?>
<nav <?php kalium_class_attr( $classes ); ?>>

	<?php if ( $container ) : ?>
    <div class="container">
	<?php endif; ?>

        <div <?php kalium_class_attr( $container_classes ); ?>>

            <div class="breadcrumb__row">

				<?php echo $breadcrumb_html; ?>

            </div>

        </div>

    <?php if ( $container ) : ?>
    </div>
    <?php endif; ?>

</nav>
