<?php
/**
 * Kalium WordPress Theme
 *
 * Other template functions.
 *
 * @author Laborator
 * @link   https://kaliumtheme.com
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

/**
 * Header WPML Language Switcher.
 */
if ( ! function_exists( 'kalium_wpml_language_switcher' ) ) {

	/**
	 * @param array|string
	 *
	 * @return void
	 */
	function kalium_wpml_language_switcher( $args = [] ) {

		// Do not run if WPML is not active
		if ( ! kalium()->is->wpml_active() ) {
			return;
		}

		// Old usage of function with first parameter as skin (string)
		if ( is_string( $args ) ) {
			$args = [
				'skin' => $args,
			];
		}

		// Args
		$args = wp_parse_args( $args, [
			'skin'          => '',
			'flag_position' => kalium_get_theme_option( 'header_wpml_language_flag_position' ),
			'display_text'  => kalium_get_theme_option( 'header_wpml_language_switcher_text_display_type' ),
			'show_on'       => kalium_get_theme_option( 'header_wpml_language_trigger' ),
			'skip_missing'  => false,
		] );

		// Languages list
		$languages = apply_filters( 'wpml_active_languages', null, [
			'skip_missing' => $args['skip_missing'],
		] );

		// Show language switcher with two or more languages
		if ( count( $languages ) > 1 ) {
			$current_language = null;

			// Filter current language
			foreach ( $languages as $lang_code => $language ) {
				if ( ICL_LANGUAGE_CODE === $lang_code ) {
					$current_language = $language;
					unset( $languages[ $lang_code ] );
				}
			}

			// Classes
			$classes = [
				'kalium-wpml-language-switcher',
			];

			// Append skin
			if ( ! empty( $args['skin'] ) ) {
				$classes[] = $args['skin'];
			}
			?>
            <div <?php kalium_class_attr( $classes ); ?> data-show-on="<?php echo esc_attr( $args['show_on'] ); ?>">

                <div class="languages-list">
					<?php
					// Current language
					kalium_wpml_language_switcher_item( $current_language, $args );

					// Languages list
					foreach ( $languages as $lang_code => $language ) {
						kalium_wpml_language_switcher_item( $language, $args );
					}
					?>

                </div>

            </div>
			<?php
		}
	}
}

/**
 * Inner function of kalium_wpml_language_switcher function.
 *
 * @see kalium_wpml_language_switcher()
 */
if ( ! function_exists( 'kalium_wpml_language_switcher_item' ) ) {

	function kalium_wpml_language_switcher_item( $language, $args = [] ) {

		// Args
		$args = wp_parse_args( $args, [
			'flag_position' => '',
			'display_text'  => '',
		] );

		// Details
		$code            = $language['code'];
		$native_name     = $language['native_name'];
		$translated_name = $language['translated_name'];
		$flag_url        = $language['country_flag_url'];
		$url             = $language['url'];

		// Check if given language is currently active
		$is_active = boolval( $language['active'] );

		// Display name
		$name = '';

		switch ( $args['display_text'] ) {
			case 'name':
				$name = $native_name;
				break;

			case 'translated':
				$name = $translated_name;
				break;

			case 'initials':
				$name = strtoupper( $code );
				break;

			case 'name-translated':
				$name = "{$native_name} <em>({$translated_name})</em>";
				break;

			case 'translated-name':
				$name = "{$translated_name} <em>({$native_name})</em>";
				break;
		}

		$classes = [
			'language-entry',
		];

		if ( $is_active ) {
			$classes[] = 'current-language';
		}

		$classes[] = 'flag-' . $args['flag_position'];
		$classes[] = 'text-' . $args['display_text'];

		?>
        <a href="<?php echo $url; ?>" class="<?php echo implode( ' ', $classes ); ?>">
            <span class="language-title">
                <?php if ( 'hide' !== $args['flag_position'] ) : ?>
                    <span class="flag"><img src="<?php echo $flag_url; ?>" alt="<?php echo $code; ?>"></span>
				<?php endif; ?>

				<?php if ( $name ) : ?>
                    <span class="text"><?php echo apply_filters( 'kalium_wpml_language_switcher_language_name', $name, $language ); ?></span>
				<?php endif; ?>
            </span>
        </a>
		<?php
	}
}

/**
 * Print theme execution time.
 */
if ( ! function_exists( 'kalium_print_theme_execution_time' ) ) {

	function kalium_print_theme_execution_time() {
		echo PHP_EOL;
		echo sprintf(
			'<!-- TET: %1$s / %2$s%3$s -->',
			number_format( microtime( true ) - kalium()->get_start_time(), 6 ),
			kalium()->get_version(),
			is_child_theme() ? 'ch' : ''
		);
		echo PHP_EOL;
	}
}

/**
 * Like button markup.
 */
if ( ! function_exists( 'kalium_like_button_markup' ) ) {

	/**
	 * @param array $args
	 *
	 * @return void
	 *
	 * @since 3.2
	 */
	function kalium_like_button( $args = [] ) {
		$args = wp_parse_args( $args, [
			'display_count' => false,
			'echo'          => true,
			'icon'          => '',
			'class'         => '',
			'post_id'       => null,
		] );

		// Icon
		switch ( $args['icon'] ) {

			// Star
			case 'star':
				$icon_file = 'assets/images/icons/icon-star.svg';
				break;

			// Thumb
			case 'thumb':
				$icon_file = 'assets/images/icons/icon-thumb-up.svg';
				break;

			// Default
			default:
				$icon_file    = 'assets/images/icons/icon-heart.svg';
				$args['icon'] = 'heart';
		}

		// Vars
		$likes   = get_post_likes();
		$liked   = kalium_validate_boolean( $likes['liked'] );
		$count   = $likes['count'];
		$post_id = is_null( $args['post_id'] ) ? get_the_ID() : $args['post_id'];

		// Container classes
		$classes = [
			'like-button',
			'like-button--icon-' . $args['icon'],
		];

		// Liked
		if ( $liked ) {
			$classes[] = 'liked';
		}

		// Extra classes
		if ( ! empty( $args['class'] ) ) {
			$classes = array_merge( $classes, explode( ' ', kalium()->helpers->list_classes( $args['class'] ) ) );
		}

		// Button content/markup
		$content = '';

		// Like icon
		$content = '<span class="like-icon">';
		$content .= '<span class="like-icon__icon">';
		$content .= kalium_get_svg_file( $icon_file, $args['icon'] );
		$content .= '</span>';

		// Bubbles
		for ( $i = 1; $i <= 8; $i ++ ) {
			$content .= sprintf( '<span class="like-icon__bubble like-icon__bubble--index-%d"></span>', $i );
		}

		// End of like icon
		$content .= '</span>';

		// Count
		if ( $args['display_count'] ) {
			$content .= sprintf( '<span class="like-button__count">%s</span>', $count );
		}

		// Output
		$output = sprintf(
			'<a href="#" %1$s data-post-id="%2$d" aria-label="Like">%3$s</a>',
			kalium_class_attr( $classes, false ),
			$post_id,
			$content
		);

		// Echo output
		if ( $args['echo'] ) {
			echo $output;
		}

		return $output;
	}
}
