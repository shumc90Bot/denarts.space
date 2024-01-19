<?php
/**
 * TypoLab - ultimate font management library.
 *
 * TypoLab font object.
 *
 * @author Laborator
 * @link   https://laborator.co
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class TypoLab_Font {

	/**
	 * Font ID.
	 *
	 * @var string
	 */
	public $id;

	/**
	 * Font source.
	 *
	 * @var string
	 */
	public $source;

	/**
	 * Font family name.
	 *
	 * @var string
	 */
	public $family_name;

	/**
	 * Font variants.
	 *
	 * @var TypoLab_Font_Variant[]
	 */
	public $variants = [];

	/**
	 * Selectors.
	 *
	 * @var TypoLab_Font_Selector[]
	 */
	public $base_selectors = [];

	/**
	 * Custom selectors.
	 *
	 * @var TypoLab_Font_Custom_Selector[]
	 */
	public $custom_selectors = [];

	/**
	 * Conditional loading.
	 *
	 * @var TypoLab_Font_Load_Condition[]
	 */
	public $conditional_loading_statements = [];

	/**
	 * In footer.
	 *
	 * @var bool
	 */
	public $placement = 'inherit';

	/**
	 * Font preload.
	 *
	 * @var string
	 */
	public $preload = 'inherit';

	/**
	 * Font status.
	 *
	 * @var bool
	 */
	public $active = true;

	/**
	 * Timestamp of creation.
	 *
	 * @var int
	 */
	public $created_time;

	/**
	 * Font options.
	 *
	 * @var array
	 */
	public $options = [];

	/**
	 * Font array data when saved in database.
	 *
	 * @var array
	 */
	protected $json = [];

	/**
	 * Create font instance from font $export_json array.
	 *
	 * @param array $args
	 *
	 * @return TypoLab_Font|null
	 */
	public static function create_instance( $args ) {

		// Legacy font
		$args = TypoLab_Legacy_Migration::legacy_font( $args );

		// Font details
		$font_class  = self::class;
		$font_source = kalium_get_array_key( $args, 'source' );

		// If no arguments are passed
		if ( empty( $args ) ) {
			return null;
		}

		if ( 'google' === $font_source ) {
			$font_class = TypoLab::$pull_google_fonts ? TypoLab_Hosted_Google_Font::class : TypoLab_Google_Font::class;
		} elseif ( 'font-squirrel' === $font_source ) {
			$font_class = TypoLab_Font_Squirrel_Font::class;
		} elseif ( 'laborator' === $font_source ) {
			$font_class = TypoLab_Laborator_Font::class;
		} elseif ( 'hosted' === $font_source ) {
			$font_class = TypoLab_Hosted_Font::class;
		} elseif ( 'adobe' === $font_source ) {
			$font_class = TypoLab_Adobe_Font::class;
		} elseif ( 'external' === $font_source ) {
			$font_class = TypoLab_External_Font::class;
		} elseif ( 'system' === $font_source ) {
			$font_class = TypoLab_System_Font::class;
		}

		return new $font_class( $args );
	}

	/**
	 * Parse variant.
	 *
	 * @param string       $variant_id
	 * @param TypoLab_Font $font
	 *
	 * @return TypoLab_Font_Variant
	 */
	public static function parse_variant( $variant_id, $font = null ) {
		$style = $weight = 'normal';

		if ( preg_match( '/(?<weight>[0-9]{2,3})(?<italic>italic)?/', $variant_id, $matches ) ) {
			$weight = $matches['weight'];

			if ( ! empty( $matches['italic'] ) ) {
				$style = 'italic';
			}
		} else if ( 'italic' === $variant_id ) {
			$style = 'italic';
		}

		return new TypoLab_Font_Variant( [
			'style'  => $style,
			'weight' => $weight,
		], $font );
	}

	/**
	 * Constructor.
	 *
	 * @param array $args
	 */
	public function __construct( $args = [] ) {

		// Font Id
		$this->id = kalium_get_array_key( $args, 'id' );

		// Generate Font Id
		if ( ! $this->id ) {
			$this->id = TypoLab::new_font_id();
		}

		// Font source
		$this->source = kalium_get_array_key( $args, 'source' );

		// Font family
		$this->family_name = kalium_get_array_key( $args, 'family' );

		// Load font in footer
		$this->placement = kalium_get_array_key( $args, 'placement', $this->placement );

		// Preload
		$this->preload = kalium_get_array_key( $args, 'preload', $this->preload );

		// Published
		$this->active = kalium_get_array_key( $args, 'active', $this->active );

		// Created time
		$this->created_time = kalium_get_array_key( $args, 'created_time', time() );

		// Variants
		$variants = kalium_get_array_key( $args, 'variants', [] );

		if ( is_array( $variants ) ) {
			foreach ( $variants as $variant ) {
				$this->variants[] = new TypoLab_Font_Variant( $variant, $this );
			}
		}

		// Base selectors
		$base_selectors = kalium_get_array_key( $args, 'base_selectors', [] );

		if ( is_array( $base_selectors ) ) {
			foreach ( $base_selectors as $selector ) {
				$this->base_selectors[] = new TypoLab_Font_Selector( $selector );
			}
		}

		// Custom selectors
		$custom_selectors = kalium_get_array_key( $args, 'custom_selectors', [] );

		if ( is_array( $custom_selectors ) ) {
			foreach ( $custom_selectors as $selector ) {
				$this->custom_selectors[] = new TypoLab_Font_Custom_Selector( $selector );
			}
		}

		// Conditional statements
		$conditional_loading_statements = kalium_get_array_key( $args, 'conditional_loading_statements', [] );

		if ( is_array( $conditional_loading_statements ) ) {
			foreach ( $conditional_loading_statements as $statement ) {
				$this->conditional_loading_statements[] = new TypoLab_Font_Load_Condition( $statement );
			}
		}

		// Options
		$options = kalium_get_array_key( $args, 'options', $this->options );

		if ( is_array( $options ) ) {
			$this->options = $options;
		}
	}

	/**
	 * Get font ID.
	 *
	 * @return string
	 */
	public function get_id() {
		return $this->id;
	}

	/**
	 * Get font source.
	 *
	 * @return string
	 */
	public function get_source() {
		return $this->source;
	}

	/**
	 * Get source title.
	 *
	 * @return string|null
	 */
	public function get_source_title() {
		$font_source = TypoLab_Data::get_font_source( $this->get_source() );

		return kalium_get_array_key( $font_source, 'name' );
	}

	/**
	 * Get font family name.
	 *
	 * @return string
	 */
	public function get_family_name() {
		return $this->family_name;
	}

	/**
	 * Get font family stack.
	 *
	 * @return string[]
	 */
	public function get_font_family_stack() {
		return [
			$this->quote( $this->get_family_name() ),
		];
	}

	/**
	 * Get title.
	 *
	 * @return string
	 */
	public function get_title() {
		return $this->get_family_name();
	}

	/**
	 * Get font display value.
	 *
	 * @return string
	 */
	public function get_font_display() {
		return TypoLab::$font_display;
	}

	/**
	 * Get stylesheet URL.
	 *
	 * @return string
	 */
	public function get_stylesheet_url() {
		return null;
	}

	/**
	 * Get safe stylesheet URL.
	 */
	public function get_safe_stylesheet_url() {
		return admin_url( 'admin-ajax.php?action=typolab-safe-stylesheet&font-id=' . $this->get_id() );
	}

	/**
	 * Print styles.
	 */
	public function print_styles() {
	}

	/**
	 * Get font variants.
	 *
	 * @param bool $export
	 *
	 * @return TypoLab_Font_Variant[]|array
	 */
	public function get_variants( $export = false ) {
		if ( $export ) {
			$variants = [];
			foreach ( $this->get_variants() as $variant ) {
				$variants[] = $variant->to_array();
			}

			return $variants;
		}

		return $this->variants;
	}

	/**
	 * Get variant by value.
	 *
	 * @param string $variant_id
	 *
	 * @return TypoLab_Font_Variant|null
	 */
	public function get_variant( $variant_id ) {
		foreach ( $this->get_variants() as $variant ) {
			if ( $variant->to_string() === $variant_id ) {
				return $variant;
			}
		}

		return null;
	}

	/**
	 * Get variant as string representation.
	 *
	 * @param TypoLab_Font_Variant $variant
	 *
	 * @return string
	 */
	public function get_variant_value( $variant ) {
		$variant_name = $variant->is_italic() ? 'italic' : 'regular';

		if ( is_numeric( $variant->weight ) && 400 !== $variant->weight ) {
			$variant_name = $variant->weight . ( $variant->is_italic() ? 'italic' : '' );
		}

		return $variant_name;
	}

	/**
	 * Get font variants string values in array.
	 *
	 * @return array
	 */
	public function get_variants_values() {
		$variants = [];

		foreach ( $this->get_variants() as $font_variant ) {
			$variants[] = $font_variant->to_string();
		}

		return $variants;
	}

	/**
	 * Get default variant.
	 *
	 * @return TypoLab_Font_Variant|null
	 */
	public function get_default_variant() {
		$variants = $this->get_variants();

		foreach ( $variants as $variant ) {
			if ( $variant->is_regular() ) {
				return $variant;
			}
		}

		return reset( $variants );
	}

	/**
	 * Get base selectors.
	 *
	 * @param bool $export
	 *
	 * @return TypoLab_Font_Selector[]
	 */
	public function get_base_selectors( $export = false ) {
		if ( $export ) {
			$selectors = [];
			foreach ( $this->base_selectors as $selector ) {
				$selectors[] = $selector->to_array();
			}

			return $selectors;
		}

		return $this->base_selectors;
	}

	/**
	 * Get custom selectors.
	 *
	 * @param bool $export
	 *
	 * @return TypoLab_Font_Custom_Selector[]
	 */
	public function get_custom_selectors( $export = false ) {
		if ( $export ) {
			$selectors = [];
			foreach ( $this->custom_selectors as $selector ) {
				$selectors[] = $selector->to_array();
			}

			return $selectors;
		}

		return $this->custom_selectors;
	}

	/**
	 * Get conditional loading statements.
	 *
	 * @param bool $to_array
	 *
	 * @return TypoLab_Font_Load_Condition[]|array
	 */
	public function get_conditional_loading_statements( $to_array = false ) {
		if ( $to_array ) {
			$statements = [];
			foreach ( $this->get_conditional_loading_statements() as $statement ) {
				$statements[] = $statement->to_array();
			}

			return $statements;
		}

		return $this->conditional_loading_statements;
	}

	/**
	 * Get font placement.
	 *
	 * @return string
	 */
	public function get_placement() {
		return $this->placement;
	}

	/**
	 * Whether to load font in footer.
	 *
	 * @return bool
	 */
	public function in_footer() {
		if ( 'inherit' === $this->placement ) {
			return 'body' === TypoLab::$font_placement;
		}

		return 'body' === $this->placement;
	}

	/**
	 * Get font preload status.
	 *
	 * @return string
	 */
	public function get_preload() {
		return $this->preload;
	}

	/**
	 * Preload font.
	 *
	 * @return bool
	 */
	public function do_preload() {
		if ( 'inherit' === $this->get_preload() ) {
			return TypoLab::$font_preload;
		}

		return 'yes' === $this->get_preload();
	}

	/**
	 * Check if font can be loaded.
	 *
	 * @return bool
	 */
	public function do_load() {
		$statements = $this->get_conditional_loading_statements();

		// If any of statements matches
		foreach ( $statements as $statement ) {
			if ( $statement->match() ) {
				return true;
			}
		}

		// Base selectors
		$has_base_selectors = ! empty( array_filter( $this->get_base_selectors(), function( $base_selector ) {
			return $base_selector->do_include();
		} ));

		// Custom selectors
		$has_custom_selectors = ! empty( $this->get_custom_selectors() );

		return $has_base_selectors || $has_custom_selectors;
	}

	/**
	 * Set active status of font.
	 *
	 * @param bool $active
	 */
	public function set_active( $active ) {
		$this->active = ! ! $active;
	}

	/**
	 * Check if this font is active.
	 *
	 * @return bool
	 */
	public function is_active() {
		return $this->active;
	}

	/**
	 * Get created time.
	 *
	 * @return int
	 */
	public function get_created_time() {
		return $this->created_time;
	}

	/**
	 * Get options.
	 *
	 * @return array
	 */
	public function get_options() {
		return $this->options;
	}

	/**
	 * Set font option.
	 *
	 * @param string $option_name
	 * @param mixed  $option_value
	 */
	public function set_option( $option_name, $option_value ) {
		$this->options[ $option_name ] = $option_value;
	}

	/**
	 * Get font option.
	 *
	 * @param string $option_name
	 * @param mixed  $default
	 *
	 * @return mixed
	 */
	public function get_option( $option_name, $default = null ) {
		return kalium_get_array_key( $this->options, $option_name, $default );
	}

	/**
	 * Remove option.
	 *
	 * @param string $option_name
	 */
	public function remove_option( $option_name ) {
		unset( $this->options[ $option_name ] );
	}

	/**
	 * Get stylesheet file.
	 *
	 * @return string
	 */
	public function get_stylesheet() {
		return $this->get_option( 'stylesheet' );
	}

	/**
	 * Check if font supports preloading.
	 *
	 * @return bool
	 */
	public function supports_preload() {
		return method_exists( $this, 'preload' );
	}

	/**
	 * Absolute font URL.
	 *
	 * @param string $relative_url
	 *
	 * @return string
	 */
	public function get_font_url( $relative_url ) {
		return trailingslashit( TypoLab::$fonts_url ) . $relative_url;
	}

	/**
	 * Get font as array.
	 *
	 * @return array
	 */
	public function to_array() {
		$this->to_json();

		return $this->json;
	}

	/**
	 * Font export.
	 *
	 * @return array
	 */
	public function export() {
		return $this->to_array();
	}

	/**
	 * Font import.
	 */
	public function import() {
	}

	/**
	 * Save font in database.
	 */
	public function save() {
		$fonts   = TypoLab::get_option( 'registered_fonts', [] );
		$updated = false;

		// Update existing
		foreach ( $fonts as $i => $font ) {
			if ( $this->get_id() === $font['id'] ) {
				$fonts[ $i ] = $this->to_array();

				// Mark as updated font
				$updated = true;
			}
		}

		// Add as new font
		if ( ! $updated ) {
			$fonts[] = $this->to_array();
		}

		// Save font entries in database
		TypoLab::set_option( 'registered_fonts', $fonts );
	}

	/**
	 * Delete font from database.
	 *
	 * @return bool|WP_Error
	 */
	public function delete() {
		$fonts   = TypoLab::get_option( 'registered_fonts', [] );
		$deleted = false;

		// Delete from list
		foreach ( $fonts as $i => $font ) {
			if ( $this->get_id() === $font['id'] ) {
				unset( $fonts[ $i ] );
				$deleted = true;
			}
		}

		// Delete stylesheet
		if ( $stylesheet = $this->get_option( 'stylesheet' ) ) {
			$result = TypoLab_Font_Installer::delete_file( $stylesheet );

			if ( is_wp_error( $result ) ) {
				return $result;
			}
		}

		// Delete font assets
		if ( method_exists( $this, 'delete_font_assets' ) ) {
			$this->delete_font_assets();
		}

		// Save current state
		if ( $deleted ) {
			TypoLab::set_option( 'registered_fonts', $fonts );
		}

		return $deleted;
	}

	/**
	 * Switch font source.
	 *
	 * @param string $new_source
	 *
	 * @return self
	 */
	public function switch_source( $new_source ) {
		$source = TypoLab_Data::get_font_source( $new_source );

		// Switch to different source only
		if ( $source && $new_source !== $this->get_source() ) {
			$this->source = $new_source;

			// Reset font family and variants
			$this->family_name = null;
			$this->variants    = [];

			return self::create_instance( $this->to_array() );
		}

		return $this;
	}

	/**
	 * Quote string. Usable for wrapping font family name.
	 *
	 * @param string $str
	 *
	 * @return string
	 */
	public function quote( $str ) {
		return TypoLab_Helper::quote( $str );
	}

	/**
	 * Assign font data to array.
	 */
	protected function to_json() {
		$this->json['id']           = $this->get_id();
		$this->json['source']       = $this->get_source();
		$this->json['family']       = $this->get_family_name();
		$this->json['placement']    = $this->get_placement();
		$this->json['preload']      = $this->get_preload();
		$this->json['active']       = $this->is_active();
		$this->json['created_time'] = $this->get_created_time();
		$this->json['options']      = $this->get_options();

		// Font variants
		$this->json['variants'] = $this->get_variants( true );

		// Font selectors
		$this->json['base_selectors']   = $this->get_base_selectors( true );
		$this->json['custom_selectors'] = $this->get_custom_selectors( true );

		// Conditional loading statements
		$this->json['conditional_loading_statements'] = $this->get_conditional_loading_statements( true );
	}
}
