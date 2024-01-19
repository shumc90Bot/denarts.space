<?php
/**
 * TypoLab - ultimate font management library.
 *
 * Font preload trait.
 *
 * @author Laborator
 * @link   https://laborator.co
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

trait TypoLab_Font_Preload {

	/**
	 * Font preload options array key name.
	 *
	 * @var string
	 */
	private $preload_variants_key_name = 'preload_variants';

	/**
	 * Get font preload variants (raw array).
	 *
	 * @param bool $to_array
	 *
	 * @return TypoLab_Font_Variant[]|array
	 */
	public function get_preload_variants( $to_array = false ) {
		$preload_variants = $this->get_option( $this->preload_variants_key_name, [] );

		if ( $to_array ) {
			return $preload_variants;
		}

		return array_map( [ 'TypoLab_Font_Variant', 'create_instance' ], $preload_variants );
	}

	/**
	 * Set preload variants.
	 *
	 * @param TypoLab_Font_Variant[] $variants
	 */
	public function set_preload_variants( $variants ) {
		$preload_variants = [];

		if ( is_array( $variants ) ) {
			foreach ( $variants as $variant ) {
				$preload_variants[] = $variant->to_array();
			}
		}

		// Set option
		$this->set_option( $this->preload_variants_key_name, $preload_variants );
	}

	/**
	 * Get font preload URLs.
	 *
	 * @return array
	 */
	public function get_preload_urls() {
		$urls = [];

		foreach ( $this->get_preload_variants() as $variant ) {
			foreach ( $variant->src as $url ) {
				$urls[] = $url;
			}
		}

		return $urls;
	}

	/**
	 * Checks if font needs new preload fetch.
	 *
	 * @return bool
	 */
	public function do_fetch_preloads() {
		$font_variants    = $this->get_variants();
		$preload_variants = $this->get_preload_variants();

		// Check missing preload variants
		foreach ( $font_variants as $variant ) {
			$preloaded = false;

			foreach ( $preload_variants as $preload_variant ) {

				// Match variants on style and weight
				if ( $preload_variant->style === $variant->style && $preload_variant->weight === $variant->weight ) {
					$preloaded = true;
				}
			}

			// Variant is not preloaded
			if ( ! $preloaded ) {
				return true;
			}
		}

		// Check for extra preload variants
		$unique_preload_variants = array_reduce( $preload_variants, function ( $carry, $variant ) {

			// First element
			if ( ! empty( $carry ) ) {
				foreach ( $carry as $carry_variant ) {

					// Exists
					if ( $carry_variant->style === $variant->style && $carry_variant->weight === $variant->weight ) {
						return $carry;
					}
				}
			}

			// Add variant to array
			$carry[] = $variant;

			return $carry;
		}, [] );

		return 0 === count( $unique_preload_variants );
	}

	/**
	 * Convert font faces to font variant instances.
	 *
	 * @param array $font_faces
	 * @param array $args
	 *
	 * @return TypoLab_Font_Variant[]
	 */
	public function font_faces_to_variants( $font_faces, $args = [] ) {
		$args = wp_parse_args( $args, [
			'match_preload_variants' => true,
		] );

		// Variants
		$preload_variants = [];
		$font_formats     = [
			'embedded-opentype' => 'eot',
			'truetype'          => 'ttf',
		];

		// Parse font faces to variants
		if ( is_array( $font_faces ) ) {
			foreach ( $font_faces as $font_face ) {
				$subset    = $font_face['subset'];
				$files     = $font_face['files'];
				$css_props = $font_face['props'];

				// Variant
				$variant = new TypoLab_Font_Variant( [
					'name'          => kalium_get_array_key( $css_props, 'font-family' ),
					'weight'        => kalium_get_array_key( $css_props, 'font-weight' ),
					'style'         => kalium_get_array_key( $css_props, 'font-style' ),
					'display'       => kalium_get_array_key( $css_props, 'font-display' ),
					'unicode_range' => kalium_get_array_key( $css_props, 'unicode-range' ),
				], $this );

				// Add source files
				foreach ( $files as $file ) {
					$format = $file['format'];
					$source = $file['source'];

					if ( ! empty( $font_formats[ $format ] ) ) {
						$format = $font_formats[ $format ];
					}

					// Add source
					$variant->src[ $format ] = $source;
				}

				// Subset information
				$variant->data['subset'] = $subset;

				// Add to variants
				$preload_variants[] = $variant;
			}
		}

		// Match preload variants from font
		if ( $args['match_preload_variants'] ) {
			$preload_variants = $this->match_preload_variants_from_font( $preload_variants );
		}

		// Filter preload variants (optimal)
		$preload_variants = $this->filter_preload_variants( $preload_variants, [
			'load_type' => 'optimal',
		] );

		return $preload_variants;
	}

	/**
	 * Get matching preload variants with current font variants.
	 *
	 * @param TypoLab_Font_Variant[] $preload_variants
	 *
	 * @return TypoLab_Font_Variant[]
	 */
	public function match_preload_variants_from_font( $preload_variants ) {
		$matched_variants = [];

		// Defined font variants
		foreach ( $this->get_variants() as $variant ) {

			// Font face preload variants
			foreach ( $preload_variants as $preload_variant ) {

				// Match variants on style and weight
				if ( $preload_variant->style === $variant->style && $preload_variant->weight === $variant->weight ) {

					// Match by name (if not empty)
					if ( empty( $variant->name ) || strtolower( $preload_variant->name ) === strtolower( $variant->name ) ) {
						$matched_variants[] = $preload_variant;
					}
				} // Match variants by name
				else if ( ! empty( $variant->name ) && strtolower( $preload_variant->name ) === strtolower( $variant->name ) ) {
					$matched_variants[] = $preload_variant;
				}
			}
		}

		return $matched_variants;
	}

	/**
	 * Filter preload variants based on specified load type.
	 *
	 * @param TypoLab_Font_Variant[] $preload_variants
	 * @param array                  $args
	 *
	 * @return TypoLab_Font_Variant[]
	 */
	public function filter_preload_variants( $preload_variants, $args = [] ) {
		$args = wp_parse_args( $args, [
			'load_type' => 'all', // all,optimal,selected
		] );

		// Optimal load type
		if ( 'optimal' === $args['load_type'] ) {
			return $this->get_optimal_font_preload_variants( $preload_variants );
		}

		return $preload_variants;
	}

	/**
	 * Get optimal preload variants.
	 *
	 * @param TypoLab_Font_Variant[] $preload_variants
	 *
	 * @return TypoLab_Font_Variant[]
	 */
	public function get_optimal_font_preload_variants( $preload_variants ) {
		$matched_variants = [];

		// Subsets that will be preloaded
		$preload_subsets = [
			'latin',
		];

		// Indexed subsets that will be preloaded
		$preload_indexed_subsets = [ 0 ];

		// Unicode ranges that will be preloaded
		$preload_unicode_ranges = [
			'U+0000-00FF',
			'U+0131',
			'U+0152-0153',
			'U+02BB-02BC',
			'U+02C6',
			'U+02DA',
			'U+02DC',
			'U+2000-206F',
			'U+2074',
			'U+20AC',
			'U+2122',
			'U+2191',
			'U+2193',
			'U+2212',
			'U+2215',
			'U+FEFF',
			'U+FFFD',
		];

		foreach ( $preload_variants as $preload_variant ) {
			$subset = kalium_get_array_key( $preload_variant->data, 'subset' );

			// If subset is available
			if ( $subset ) {

				// Include only if subset is allowed
				if ( in_array( $subset, $preload_subsets ) ) {
					$matched_variants[] = $preload_variant;
				} // If indexed subset is available
				else if ( preg_match( '/\[(?<index>\d+)\]/', $subset, $matches ) && in_array( intval( $matches['index'] ), $preload_indexed_subsets ) ) {
					$matched_variants[] = $preload_variant;
				}
			} // If unicode range is available
			else if ( ! empty( $preload_variant->unicode_range ) ) {

				// Include only if unicode range is allowed
				if ( $this->is_in_unicode_range( $preload_variant->unicode_range, $preload_unicode_ranges ) ) {
					$matched_variants[] = $preload_variant;
				}
			} // Otherwise, include all variants because no subset or unicode range is specified
			else {
				$matched_variants[] = $preload_variant;
			}
		}

		return $matched_variants;
	}

	/**
	 * Check if given unicode is in the allowed range.
	 *
	 * @param string|array $unicode_ranges
	 * @param array        $allowed_unicode_ranges
	 *
	 * @return bool
	 */
	public function is_in_unicode_range( $unicode_ranges, $allowed_unicode_ranges = [] ) {
		if ( is_string( $unicode_ranges ) ) {
			$unicode_ranges = array_map( 'trim', explode( ',', $unicode_ranges ) );
		}

		/**
		 * Convert unicode range values to from-to range array.
		 *
		 * @param string $value
		 *
		 * @return array
		 */
		$range_mapper = function ( $value ) {
			if ( preg_match( '/^U\+(?<from>[a-f0-9]{1,4})(-(?<to>[a-f0-9]{1,4}))?$/i', $value, $matches ) ) {
				$from = $matches['from'];
				$to   = empty( $matches['to'] ) ? $matches['from'] : $matches['to'];

				return [
					'from' => strtoupper( $from ),
					'to'   => strtoupper( $to ),
				];
			}

			return null;
		};

		// Convert to range arrays
		$unicode_ranges         = array_map( $range_mapper, $unicode_ranges );
		$allowed_unicode_ranges = array_map( $range_mapper, $allowed_unicode_ranges );

		// Loop through unicode ranges
		foreach ( $unicode_ranges as $unicode_range ) {
			$from = hexdec( $unicode_range['from'] );
			$to   = hexdec( $unicode_range['to'] );

			// If only one unicode range is in allowed unicode range this function will return true
			foreach ( $allowed_unicode_ranges as $allowed_unicode_range ) {
				$allowed_from = hexdec( $allowed_unicode_range['from'] );
				$allowed_to   = hexdec( $allowed_unicode_range['to'] );

				if ( $from >= $allowed_from && $to <= $allowed_to ) {
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * Preload font logic.
	 */
	public function preload() {

		// Installable font
		if ( $this instanceof TypoLab_Installable_Font && ! $this->is_installed() ) {
			return;
		}

		// Fetch font face variants
		$stylesheet_url   = $this->get_stylesheet_url();
		$font_faces       = TypoLab_Font_Installer::fetch_font_faces( $stylesheet_url );
		$preload_variants = $this->font_faces_to_variants( $font_faces );

		// Set font preload variants
		$this->set_preload_variants( $preload_variants );
	}
}
