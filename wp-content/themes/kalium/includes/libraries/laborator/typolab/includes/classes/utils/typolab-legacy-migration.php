<?php
/**
 * TypoLab - ultimate font management library.
 *
 * Legacy migration class.
 *
 * @author Laborator
 * @link   https://laborator.co
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class TypoLab_Legacy_Migration {

	/**
	 * Legacy font.
	 *
	 * @param array $font_data
	 *
	 * @return array
	 */
	public static function legacy_font( $font_data ) {

		// Determine if this font is "legacy font"
		if ( ! isset( $font_data['valid'] ) ) {
			return $font_data;
		}

		// Ignore legacy "not valid" fonts
		if ( ! $font_data['valid'] ) {
			return null;
		}

		// Font source
		$font_source = kalium_get_array_key( $font_data, 'source' );

		// Preload
		if ( ! isset( $font_data['preload'] ) ) {
			$font_data['preload'] = 'inherit';
		}

		// Placement
		if ( ! isset( $font_data['placement'] ) ) {
			$font_data['placement'] = kalium_get_array_key( $font_data, 'font_placement', 'head' );

			// Empty placement
			if ( empty( $font_data['placement'] ) ) {
				$font_data['placement'] = 'head';
			}
		}

		// Active
		if ( ! isset( $font_data['active'] ) ) {
			$font_data['active'] = 'published' === kalium_get_array_key( $font_data, 'font_status', 'published' );
		}

		// Date created
		if ( ! isset( $font_data['created_time'] ) ) {
			$font_data['created_time'] = isset( $font_data['options']['created_time'] ) ? $font_data['options']['created_time'] : time();
		}

		// Other options by font source
		switch ( $font_source ) {

			// Google font
			case 'google':
				$font_data = self::legacy_google_font( $font_data );
				break;

			// Font Squirrel
			case 'font-squirrel':
				$font_data = self::legacy_font_squirrel( $font_data );
				break;

			// Uploaded font
			case 'uploaded-font':
				$font_data = self::legacy_uploaded_font( $font_data );
				break;

			// Custom font
			case 'custom-font':
				$font_data = self::legacy_custom_font( $font_data );
				break;

			// Typekit font
			case 'typekit':
				$font_data = self::legacy_typekit_font( $font_data );
				break;

			// Premium font
			case 'premium':
				$font_data = self::legacy_premium_font( $font_data );
				break;
		}

		// Custom Selectors
		$font_data = self::legacy_font_custom_selectors( $font_data );

		// Conditional loading
		$font_data = self::legacy_font_conditional_loading( $font_data );

		// Reset font data (options)
		$font_data['options'] = [

			// Mark as legacy font
			'legacy_font' => true,
		];

		return $font_data;
	}

	/**
	 * Legacy Google font.
	 *
	 * @param array $font_data
	 *
	 * @return array
	 */
	public static function legacy_google_font( $font_data ) {
		$variants = [];

		// Selected legacy variants
		$legacy_variants = kalium_get_array_key( $font_data, 'variants' );

		// Determine font variants
		if ( is_array( $legacy_variants ) ) {
			foreach ( $legacy_variants as $legacy_variant ) {
				$variants[] = TypoLab_Font::parse_variant( $legacy_variant )->to_array();
			}
		}

		// Replace variants with new format
		$font_data['variants'] = $variants;

		return $font_data;
	}

	/**
	 * Legacy font squirrel font.
	 *
	 * @param array $font_data
	 *
	 * @return array
	 */
	public static function legacy_font_squirrel( $font_data ) {
		$variants = [];

		// Available font variants
		$legacy_available_variants = kalium_get_array_key( $font_data['options'], 'data' );

		// Selected legacy variants
		$legacy_variants = kalium_get_array_key( $font_data, 'variants' );

		// Determine font variants
		if ( is_array( $legacy_variants ) && is_array( $legacy_available_variants ) ) {
			foreach ( $legacy_variants as $legacy_variant ) {
				foreach ( $legacy_available_variants as $legacy_available_variant ) {
					if ( isset( $legacy_available_variant->fontface_name ) && $legacy_variant === $legacy_available_variant->fontface_name ) {
						$variants[] = [
							'name' => preg_replace( '/\.[a-z]+$/i', '', $legacy_available_variant->filename ), // Variant identifier
						];
					}
				}
			}
		}

		// Replace variants with new format
		$font_data['variants'] = $variants;

		return $font_data;
	}

	/**
	 * Legacy uploaded font.
	 *
	 * @param array $font_data
	 *
	 * @return array
	 */
	public static function legacy_uploaded_font( $font_data ) {
		$variants = [];

		// Font source fix
		$font_data['source'] = 'hosted';

		// Defined legacy variants
		$legacy_variants = kalium_get_array_key( $font_data['options'], 'font_variants' );

		// Determine font variants
		if ( is_array( $legacy_variants ) ) {
			foreach ( $legacy_variants as $legacy_variant ) {
				$style  = kalium_get_array_key( $legacy_variant, 'style' );
				$weight = kalium_get_array_key( $legacy_variant, 'weight' );
				$files  = kalium_get_array_key( $legacy_variant, 'files' );

				$variants[] = [
					'style'  => $style,
					'weight' => $weight,
					'src'    => array_filter( $files ),
				];
			}
		}

		// Replace variants with new format
		$font_data['variants'] = $variants;

		return $font_data;
	}

	/**
	 * Legacy custom font.
	 *
	 * @param array $font_data
	 *
	 * @return array
	 */
	public static function legacy_custom_font( $font_data ) {
		$variants = [];

		// Font source fix
		$font_data['source'] = 'external';

		// Stylesheet URL
		if ( isset( $font_data['options']['font_url'] ) ) {
			$font_data['stylesheet_url'] = $font_data['options']['font_url'];
		}

		// Defined legacy variants
		$legacy_variants = kalium_get_array_key( $font_data['options'], 'font_variants' );

		// Determine font variants
		if ( is_array( $legacy_variants ) ) {
			foreach ( $legacy_variants as $legacy_variant ) {
				$legacy_variant_name = explode( ',', $legacy_variant );

				$variants[] = [
					'name'   => str_replace( "'", '', $legacy_variant_name[0] ),
					'style'  => 'normal',
					'weight' => 'normal',
				];
			}
		}

		// Replace variants with new format
		$font_data['variants'] = $variants;

		return $font_data;
	}

	/**
	 * Legacy Typekit font.
	 *
	 * @param array $font_data
	 *
	 * @return array
	 */
	public static function legacy_typekit_font( $font_data ) {
		$variants = [];
		$styles   = [ 'normal', 'italic' ];
		$weights  = [ 100, 200, 300, 'normal', 500, 600, 700, 800, 900 ];

		// Font source fix
		$font_data['source'] = 'adobe';

		// Kit ID
		$font_data['kit_id'] = kalium_get_array_key( $font_data['options'], 'font_url' );

		// Add all available variants
		foreach ( $styles as $style ) {
			foreach ( $weights as $weight ) {
				$variants[] = [
					'style'  => $style,
					'weight' => $weight,
				];
			}
		}

		// Replace variants with new format
		$font_data['variants'] = $variants;

		return $font_data;
	}

	/**
	 * Legacy Premium font.
	 *
	 * @param array $font_data
	 *
	 * @return array
	 */
	public static function legacy_premium_font( $font_data ) {
		$variants = [];

		// Font source fix
		$font_data['source'] = 'laborator';

		// Font family fix
		$font_data['family'] = 'Function Pro';

		// Legacy font data
		$legacy_font_data = kalium_get_array_key( $font_data['options'], 'data' );

		// Available font variants
		$legacy_font_variants = isset( $legacy_font_data->variants ) ? (array) $legacy_font_data->variants : null;

		// Selected legacy variants
		$legacy_variants = kalium_get_array_key( $font_data, 'variants' );

		// Determine font variants
		if ( is_array( $legacy_variants ) && is_array( $legacy_font_variants ) ) {
			foreach ( $legacy_variants as $legacy_variant ) {
				$variants[] = self::legacy_parse_premium_font_variant( $legacy_variant );
			}
		}

		// Replace variants with new format
		$font_data['variants'] = $variants;

		return $font_data;
	}

	/**
	 * Parse premium font variant.
	 *
	 * @param string $variant
	 *
	 * @return array
	 */
	public static function legacy_parse_premium_font_variant( $variant ) {
		$font_weight = 'normal';

		// Font style
		$font_style = false !== strpos( $variant, 'oblique' ) ? 'italic' : 'normal';

		// Font weight
		switch ( $variant ) {

			// 300
			case 'functionpro_light':
			case 'functionpro_lightoblique':
				$font_weight = 300;
				break;

			// 400
			case 'functionpro_book':
			case 'functionpro_bookoblique':
				$font_weight = 'normal';
				break;

			// 500
			case 'functionpro_medium':
			case 'functionpro_mediumoblique':
				$font_weight = 500;
				break;

			// 600
			case 'functionpro_demi':
			case 'functionpro_demioblique':
				$font_weight = 600;
				break;

			// 700
			case 'functionpro_bold':
			case 'functionpro_boldoblique':
				$font_weight = 700;
				break;

			// 900
			case 'functionpro_extrabold':
			case 'functionpro_extraboldoblique':
				$font_weight = 900;
				break;
		}

		return [
			'style'  => $font_style,
			'weight' => $font_weight,
		];
	}

	/**
	 * Fix legacy custom CSS selectors.
	 *
	 * @param array $font_data
	 *
	 * @return array
	 */
	public static function legacy_font_custom_selectors( $font_data ) {
		$selectors     = kalium_get_array_key( $font_data['options'], 'selectors' );
		$selectors_new = [];

		/**
		 * Parse variant by font source.
		 *
		 * @param string $variant
		 * @param string $font_source
		 *
		 * @return string
		 */
		$parse_variant = function ( $variant, $font_source ) use ( $font_data ) {

			// Font Squirrel
			if ( 'font-squirrel' === $font_source ) {
				$legacy_available_variants = kalium_get_array_key( $font_data['options'], 'data' );

				// Determine font variant
				if ( is_array( $legacy_available_variants ) ) {
					foreach ( $legacy_available_variants as $legacy_available_variant ) {
						if ( isset( $legacy_available_variant->fontface_name ) && $variant === $legacy_available_variant->fontface_name ) {
							return preg_replace( '/\.[a-z]+$/i', '', $legacy_available_variant->filename ); // Variant identifier
						}
					}
				}
			} // Laborator font
			else if ( 'laborator' === $font_source ) {
				$parsed_variant = self::legacy_parse_premium_font_variant( $variant );
				$variant_name   = 'italic' === $parsed_variant['style'] ? 'italic' : 'regular';

				if ( is_numeric( $parsed_variant['weight'] ) && 400 !== $parsed_variant['weight'] ) {
					$variant_name = $parsed_variant['weight'] . ( 'italic' === $parsed_variant['style'] ? 'italic' : '' );
				}

				return $variant_name;
			}

			return $variant;
		};

		// Transform selectors
		if ( is_array( $selectors ) ) {
			foreach ( $selectors as $i => $selector ) {
				$font_sizes     = kalium_get_array_key( $selector, 'font-sizes' );
				$font_size_unit = kalium_get_array_key( $font_sizes, 'unit' );
				$text_transform = kalium_get_array_key( $selector, 'text-transform' );
				$line_height    = kalium_get_array_key( $selector, 'lineHeight' );

				if ( is_numeric( $line_height ) ) {
					$line_height = 100 * $line_height . '%';
				}

				$selectors_new[] = [
					'type'           => 'custom-selector',
					'id'             => 'custom-selector-' . ( $i + 1 ),
					'selectors'      => array_map( 'trim', explode( ',', $selector['selector'] ) ),
					'variant'        => $parse_variant( kalium_get_array_key( $selector, 'variant' ), kalium_get_array_key( $font_data, 'source' ) ),
					'font_size'      => [
						'general' => $font_sizes['general'] ? ( $font_sizes['general'] . $font_size_unit ) : null,
						'tablet'  => $font_sizes['tablet'] ? ( $font_sizes['tablet'] . $font_size_unit ) : null,
						'mobile'  => $font_sizes['mobile'] ? ( $font_sizes['mobile'] . $font_size_unit ) : null,
					],
					'text_transform' => [
						'general' => $text_transform,
					],
					'line_height'    => [
						'general' => $line_height,
					],
				];
			}
		}

		// Set custom selectors
		$font_data['custom_selectors'] = $selectors_new;

		return $font_data;
	}

	/**
	 * Arrange conditional loading statements in new version of TypoLab.
	 *
	 * @param array $font_data
	 *
	 * @return array
	 */
	public static function legacy_font_conditional_loading( $font_data ) {
		$conditional_loading     = kalium_get_array_key( $font_data['options'], 'conditional_loading' );
		$conditional_loading_new = [];

		if ( is_array( $conditional_loading ) ) {
			foreach ( $conditional_loading as $conditional_loading_statement ) {
				$statement = $conditional_loading_statement['statement'];
				$operator  = '==' === $conditional_loading_statement['operator'] ? 'equals' : 'not-equals';
				$criteria  = $conditional_loading_statement['criteria'];

				$conditional_loading_new[] = [
					'type'     => $statement,
					'operator' => $operator,
					'value'    => $criteria,
				];
			}
		}

		// Set conditional loading statements
		$font_data['conditional_loading_statements'] = $conditional_loading_new;

		return $font_data;
	}

	/**
	 * Legacy font sizes.
	 *
	 * @param array $font_appearance
	 *
	 * @return array
	 */
	public static function legacy_font_sizes( $font_appearance ) {
		$legacy_font_sizes     = TypoLab::get_option( 'font_sizes' );
		$legacy_font_sizes_new = [];

		/**
		 * Check if given group ID is valid.
		 *
		 * @param string $group_id
		 *
		 * @return bool
		 */
		$is_valid_group = function ( $group_id ) {
			return in_array( $group_id, [
				'headings',
				'paragraphs',
				'header',
				'footer',
				'standard-menu',
				'fullscreen-menu',
				'top-menu',
				'sidebar-menu',
				'mobile-menu',
				'portfolio',
				'blog',
				'shop',
			] );
		};

		/**
		 * Get corresponding element name.
		 *
		 * @param string $group_id
		 * @param string $legacy_element_id
		 *
		 * @return string|null
		 */
		$get_corresponding_element_id = function ( $group_id, $legacy_element_id ) {
			$element_ids_mapping = [
				'headings'        => [
					'h1' => 'h1',
					'h2' => 'h2',
					'h3' => 'h3',
					'h4' => 'h4',
					'h5' => 'h5',
					'h6' => 'h6',
				],
				'paragraphs'      => [
					'p' => 'p',
				],
				'header'          => [
					'default-text'   => 'header_text',
					'top-header-bar' => 'header_top_bar',
				],
				'footer'          => [
					'widgets-title'   => 'footer_widgets_title',
					'widgets-content' => 'footer_widgets_content',
					'copyrights'      => 'footer_copyrights',
				],
				'standard-menu'   => [
					'main-menu-items' => 'standard_menu',
					'sub-menu-items'  => 'standard_menu_sub',
				],
				'fullscreen-menu' => [
					'main-menu-items' => 'fullscreen_menu',
					'sub-menu-items'  => 'fullscreen_menu_sub',
				],
				'top-menu'        => [
					'main-menu-items' => 'top_menu',
					'sub-menu-items'  => 'top_menu_sub',
					'widgets-title'   => 'top_menu_widgets_title',
					'widgets-content' => 'top_menu_widgets_content',
				],
				'sidebar-menu'    => [
					'main-menu-items' => 'sidebar_menu',
					'sub-menu-items'  => 'sidebar_menu_sub',
					'widgets-title'   => 'sidebar_menu_widgets_title',
					'widgets-content' => 'sidebar_menu_widgets_content',
				],
				'mobile-menu'     => [
					'main-menu-items' => 'mobile_menu',
					'sub-menu-items'  => 'mobile_menu_sub',
				],
				'portfolio'       => [
					'titles'            => 'portfolio_item_title',
					'single-title'      => 'portfolio_item_title_single',
					'categories'        => 'portfolio_item_categories',
					'subtitles'         => 'portfolio_item_sub_titles',
					'portfolio-content' => 'portfolio_item_content',
					'services-title'    => 'portfolio_checklist_title',
					'services-content'  => 'portfolio_checklist_content',
				],
				'shop'            => [
					'titles'          => 'shop_product_title',
					'single-title'    => 'shop_product_title_single',
					'categories'      => 'shop_product_categories',
					'product-content' => 'shop_product_content',
				],
				'blog'            => [
					'titles'       => 'blog_post_title',
					'single-title' => 'blog_post_title_single',
					'post-excerpt' => 'blog_post_excerpt',
					'post-content' => 'blog_post_content',
				],
			];

			// Matched element
			if ( isset( $element_ids_mapping[ $group_id ][ $legacy_element_id ] ) ) {
				return $element_ids_mapping[ $group_id ][ $legacy_element_id ];
			}

			return null;
		};

		/**
		 * Merge values recursively.
		 *
		 * @param array $arr1
		 * @param array $arr2
		 *
		 * @return array
		 */
		$merge_values = function ( $arr1, $arr2 ) use ( & $merge_values ) {
			foreach ( $arr2 as $arr2_key => $arr2_value ) {
				if ( isset( $arr1[ $arr2_key ] ) ) {
					$is_arr1_array = is_array( $arr1[ $arr2_key ] );
					$is_arr2_array = is_array( $arr2_value );

					// Merge two arrays
					if ( $is_arr1_array && $is_arr2_array ) {
						$arr1[ $arr2_key ] = $merge_values( $arr1[ $arr2_key ], $arr2_value );
					} else {
						if ( ! isset( $arr1[ $arr2_key ] ) ) {
							$arr1[ $arr2_key ] = $arr2_value;
						} else if ( ! empty( $arr2_value ) ) {
							$arr1[ $arr2_key ] = $arr2_value;
						}
					}

				} else {
					$arr1[ $arr2_key ] = $arr2_value;
				}
			}

			return $arr1;
		};

		// Match legacy font appearance settings
		if ( is_array( $legacy_font_sizes ) ) {
			foreach ( $legacy_font_sizes as $font_size_group ) {
				$group_id = $font_size_group['id'];

				// Check if its valid group
				if ( $is_valid_group( $group_id ) ) {
					$sizes = kalium_get_array_key( $font_size_group, 'sizes' );
					$unit  = kalium_get_array_key( $font_size_group, 'unit' );

					// Create group array container
					if ( ! isset( $legacy_font_sizes_new[ $group_id ] ) ) {
						$legacy_font_sizes_new[ $group_id ] = [];
					}

					// Add elements
					if ( is_array( $sizes ) ) {
						foreach ( $sizes as $legacy_element_id => $size ) {
							$element_id = $get_corresponding_element_id( $group_id, $legacy_element_id );

							if ( ! is_null( $element_id ) ) {
								$font_size_general = kalium_get_array_key( $size, 'general' );
								$font_size_tablet  = kalium_get_array_key( $size, 'tablet' );
								$font_size_mobile  = kalium_get_array_key( $size, 'mobile' );
								$text_transform    = kalium_get_array_key( $size, 'text-transform' );
								$line_height       = kalium_get_array_key( $size, 'line-height' );

								if ( is_numeric( $line_height ) ) {
									$line_height = 100 * $line_height . '%';
								}

								$legacy_font_sizes_new[ $group_id ][ $element_id ] = [
									'font_size'      => [
										'general' => $font_size_general ? ( $font_size_general . $unit ) : null,
										'tablet'  => $font_size_tablet ? ( $font_size_tablet . $unit ) : null,
										'mobile'  => $font_size_mobile ? ( $font_size_mobile . $unit ) : null,
									],
									'line_height'    => [
										'general' => $line_height,
									],
									'text_transform' => [
										'general' => $text_transform,
									],
								];
							}
						}
					}
				}
			}
		}

		// Merge legacy values with current ones
		return $merge_values( $legacy_font_sizes_new, $font_appearance );
	}

	/**
	 * Legacy install fonts.
	 */
	public static function legacy_install_fonts() {
		$fonts = TypoLab::get_fonts( [
			'status' => 'active',
		] );

		foreach ( $fonts as $font ) {

			// Installable legacy fonts only
			if ( ! $font instanceof TypoLab_Installable_Font || ! $font->get_option( 'legacy_font' ) ) {
				continue;
			}

			// Laborator font (previously Premium font)
			if ( $font instanceof TypoLab_Laborator_Font ) {

				// Install font only if theme is registered
				if ( kalium()->theme_license->is_theme_registered() ) {

					if ( ! $font->is_installed() ) {
						$result = $font->install();

						// Installed successfully
						if ( ! is_wp_error( $result ) && true === $result ) {
							$font->save();
						}
					}
				}
			} // Font Squirrel font
			else if ( $font instanceof TypoLab_Font_Squirrel_Font ) {

				if ( ! $font->is_installed() ) {
					$result = $font->install();

					// Installed successfully
					if ( ! is_wp_error( $result ) && true === $result ) {
						$font->save();
					}
				}
			}
		}
	}

	/**
	 * Legacy fonts set preload.
	 */
	public static function legacy_preload_fonts() {
		$fonts = TypoLab::get_fonts( [
			'status' => 'active',
		] );

		foreach ( $fonts as $font ) {

			// Only legacy fonts that support preloading
			if ( ! $font->supports_preload() || ! $font->get_option( 'legacy_font' ) ) {
				continue;
			}

			// Preload font
			if ( $font->do_preload() && $font->do_fetch_preloads() ) {
				$font->preload();
				$font->save();
			}
		}
	}
}
