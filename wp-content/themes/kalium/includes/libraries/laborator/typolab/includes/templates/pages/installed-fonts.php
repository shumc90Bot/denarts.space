<?php
/**
 * TypoLab - ultimate font management library.
 *
 * Fonts list.
 *
 * @author Laborator
 * @link   https://laborator.co
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<form method="post" class="typolab-fonts-list">
	<?php $GLOBALS['fonts_list_table']->display(); ?>
</form>