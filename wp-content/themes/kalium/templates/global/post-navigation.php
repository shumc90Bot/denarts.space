<?php
/**
 * Kalium WordPress Theme
 *
 * Previous and next post links.
 *
 * @author  Laborator
 * @link    https://kaliumtheme.com
 * @version 3.2
 *
 * @var string $next_post
 * @var string $prev_post
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}
?>
<nav class="post-navigation">

    <div class="post-navigation__column">
		<?php
		/**
		 * Previous post.
		 */
		echo $prev_post;
		?>
    </div>

    <div class="post-navigation__column post-navigation__column--right">
		<?php
		/**
		 * Next post.
		 */
		echo $next_post;
		?>
    </div>

</nav>