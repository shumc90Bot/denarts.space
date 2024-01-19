<?php
/**
 * TypoLab - ultimate font management library.
 *
 * CSS generator tool.
 *
 * @author Laborator
 * @link   https://laborator.co
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class TypoLab_CSS_Generator {

	/**
	 * CSS Rules (or selector).
	 *
	 * @var array
	 */
	public $rules = [];

	/**
	 * Props.
	 *
	 * @var array
	 */
	public $props = [];

	/**
	 * Parse various objects to CSS.
	 *
	 * @param array        $arr
	 * @param TypoLab_Font $font
	 * @param bool         $to_string
	 *
	 * @return self[]|string
	 */
	public static function parse( $arr, $font = null, $to_string = false ) {
		$items = [];

		// Convert to array
		if ( ! is_array( $arr ) ) {
			$arr = [ $arr ];
		}

		// Parse items by object type
		foreach ( $arr as $item ) {

			/**
			 * Parse TypoLab_Font_Appearance_Element.
			 */
			if ( $item instanceof TypoLab_Font_Appearance_Element ) {
				$items[] = new self( $item->get_selectors(), $item->get_css_props() );
				continue;
			}

			/**
			 * Parse TypoLab_Font_Selector.
			 */
			if ( $item instanceof TypoLab_Font_Selector ) {

				// Font is required
				if ( ! $font instanceof TypoLab_Font ) {
					continue;
				}

				/**
				 * Base selector (TypoLab_Font_Selector).
				 */
				if ( $item->is_base_selector() && $item->do_include() ) {
					if ( $variant = $font->get_variant( $item->get_variant() ) ) {
						$items[] = new self( $item->get_selectors(), $variant->get_css_props() );
					}
				}

				/**
				 * Custom selector (TypoLab_Font_Custom_Selector).
				 */
				if ( $item instanceof TypoLab_Font_Custom_Selector ) {
					$items[] = new self( $item->get_selectors(), $item->get_css_props( $font ) );
				}
			}
		}

		// To string value
		if ( $to_string ) {
			return trim( implode( PHP_EOL, $items ) );
		}

		return $items;
	}

	/**
	 * Constructor.
	 *
	 * @param array|string $rules
	 * @param array        $props
	 */
	public function __construct( $rules = [], $props = [] ) {

		// Convert rules to array
		if ( is_string( $rules ) ) {
			$rules = explode( ',', $rules );
		}

		// Add CSS rules/selectors
		if ( is_array( $rules ) && ! empty( $rules ) ) {
			$this->rules = $rules;
		}

		// Props
		if ( is_array( $props ) ) {
			$this->add_props( $props );
		}
	}

	/**
	 * Add single prop.
	 *
	 * @param string $prop
	 * @param mixed  $value
	 */
	public function add_prop( $prop, $value = null ) {
		if ( ! is_numeric( $prop ) ) {
			$this->props[ $prop ] = $value;
		}
	}

	/**
	 * Add array of properties.
	 *
	 * @param array $props
	 */
	public function add_props( $props ) {
		if ( is_array( $props ) ) {
			foreach ( $props as $prop => $value ) {
				$this->add_prop( $prop, $value );
			}
		}
	}

	/**
	 * Remove prop or array of props.
	 *
	 * @param string|array $prop
	 */
	public function remove_prop( $prop ) {
		if ( is_array( $prop ) ) {
			foreach ( $prop as $prop_name ) {
				$this->remove_prop( $prop_name );
			}
		} else {
			if ( isset( $this->props[ $prop ] ) ) {
				unset( $this->props[ $prop ] );
			}
		}
	}

	/**
	 * Generate CSS media string.
	 *
	 * @param string $breakpoint_id
	 *
	 * @return string
	 */
	public function generate_media( $breakpoint_id ) {
		$breakpoint = TypoLab_Data::get_responsive_breakpoint( $breakpoint_id );
		$media      = [];

		if ( ! is_null( $breakpoint ) ) {
			$min_size = kalium_get_array_key( $breakpoint, 'min_size' );
			$max_size = kalium_get_array_key( $breakpoint, 'max_size' );

			if ( $min_size || $max_size ) {
				$media[] = '@media screen and';
			}

			if ( $min_size ) {
				$media[] = sprintf( '(min-width: %spx)', $min_size );
			}

			if ( $max_size ) {
				if ( $min_size ) {
					$media[] = 'and';
				}

				$media[] = sprintf( '(max-width: %spx)', $max_size );
			}
		}

		return implode( ' ', $media );
	}

	/**
	 * Validate and return CSS value.
	 *
	 * @param string $value
	 * @param string $prop
	 *
	 * @return string
	 */
	public function css_value( $value, $prop = '' ) {
		return $value;
	}

	/**
	 * Generate CSS.
	 *
	 * @return string
	 */
	public function __toString() {
		$css           = $str = [];
		$bracket_open  = ' {';
		$bracket_close = '}';

		foreach ( $this->props as $prop => $value ) {
			$media = '';

			// Skip empty values
			if ( ! $value ) {
				continue;
			}

			// Responsive value
			if ( $value instanceof TypoLab_Responsive_Value ) {
				foreach ( $value->to_array() as $breakpoint_id => $breakpoint_value ) {

					// Only non-empty values
					if ( ! empty( $breakpoint_value ) ) {
						$media = $this->generate_media( $breakpoint_id );

						$css[ $media ][ $prop ] = $this->css_value( $breakpoint_value, $prop );
					}
				}
			} // Plain string
			else if ( $value ) {
				$css[ $media ][ $prop ] = $this->css_value( $value, $prop );
			}
		}

		// Build CSS selectors
		foreach ( $css as $media => $props ) {
			if ( empty( $this->rules ) || empty( $props ) ) {
				continue;
			}

			// @media
			if ( $media ) {
				$str[] = $media . $bracket_open;
			}


			// @rule
			$str[] = implode( ', ', $this->rules ) . $bracket_open;

			// @properties
			foreach ( $props as $prop => $value ) {
				$str[] = sprintf( "\t%s: %s;", $prop, $value );
			}

			// End of @rule
			$str[] = $bracket_close;

			// End of @media
			if ( $media ) {
				$str[] = $bracket_close;
			}
		}

		return implode( PHP_EOL, $str );
	}
}
