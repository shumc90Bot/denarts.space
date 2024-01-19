<?php
/**
 * TypoLab - ultimate font management library.
 *
 * Tabs list.
 *
 * @author Laborator
 * @link   https://laborator.co
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$page           = kalium()->request->query( 'page' );
$current_page   = kalium()->request->query( 'typolab-page' );
$typolab_active = in_array( $current_page, [ 'settings', 'fonts-appearance' ] ) ? $current_page : 'fonts';

$tabs = [
	[
		'title'  => 'Fonts',
		'url'    => sprintf( 'admin.php?page=%s', $page ),
		'active' => 'fonts' === $typolab_active,
	],
	[
		'title'  => 'Font Sizes',
		'url'    => sprintf( 'admin.php?page=%s&typolab-page=fonts-appearance', $page ),
		'active' => 'fonts-appearance' === $typolab_active,
	],
	[
		'title'  => 'Settings',
		'url'    => sprintf( 'admin.php?page=%s&typolab-page=settings', $page ),
		'active' => 'settings' === $typolab_active,
	],
];

?>
<nav class="nav-tab-wrapper typolab-tabs about__header-navigation wp-clearfix">
	<?php
	foreach ( $tabs as $tab ) :
		$classes = [
			'nav-tab',
		];

		// Active tab
		if ( $tab['active'] ) {
			$classes[] = 'nav-tab-active';
		}
		?>
        <a href="<?php echo esc_url( admin_url( $tab['url'] ) ); ?>"<?php kalium_class_attr( $classes ); ?>><?php echo esc_html( $tab['title'] ); ?></a>
	<?php endforeach; ?>
</nav>