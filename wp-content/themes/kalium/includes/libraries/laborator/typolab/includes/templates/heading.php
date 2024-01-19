<?php
/**
 * TypoLab - ultimate font management library.
 *
 * Heading title and description across all pages.
 *
 * @var string $page_template
 *
 * @author Laborator
 * @link   https://laborator.co
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="typolab-heading about-wrap">
    <h1>
        Typography
		<?php
		// Add font button (on fonts page only)
		if ( 'installed-fonts' === $page_template ) {
			echo sprintf( '<a href="%s" class="button add-new-font">Add Font</a>', esc_url( TypoLab::add_new_font_url() ) );
		}
		?>
    </h1>
    <p class="about-text">
        TypoLab is an ultimate font management tool built for Kalium which provides unlimited fonts from multiple sources, appearance customization and font performance.
    </p>
</div>
