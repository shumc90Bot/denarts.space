<?php
/**
 * TypoLab - ultimate font management library.
 *
 * Responsive value class.
 *
 * @author Laborator
 * @link   https://laborator.co
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class TypoLab_Responsive_Value {

    /**
     * Responsive value.
     *
     * @var array
     */
    public $value = [];

    /**
     * Constructor.
     *
     * @param {array} $value
     */
    public function __construct( $value = [] ) {
        foreach ( TypoLab_Data::get_responsive_breakpoints() as $breakpoint_id => $breakpoint ) {
            $current_value = kalium_get_array_key( $value, $breakpoint_id );

            // Leave empty for values that equal with inherit value
            if ( $current_value === $this->get_inherit_value( $breakpoint ) ) {
                $current_value = null;
            }

            // Exclude unit only values
            if ( in_array( $current_value, [ 'px', 'rem', 'em', 'pt', 'vw', '%' ] ) ) {
                $current_value = null;
            }

            // Set breakpoint value
            $this->set_value( $breakpoint_id, $current_value );
        }
    }

    /**
     * Property value getter.
     *
     * @param string $breakpoint_id
     *
     * @return mixed
     */
    public function get_value( $breakpoint_id ) {
        return kalium_get_array_key( $this->value, $breakpoint_id );
    }

    /**
     * Get inherit value for breakpoint.
     *
     * @param array $breakpoint
     *
     * @return mixed
     */
    public function get_inherit_value( $breakpoint ) {
        $inherit_from = kalium_get_array_key( $breakpoint, 'inherit' );

        if ( $inherit_from ) {
            $inherit_value = $this->get_value( $inherit_from );

            if ( $inherit_value ) {
                return $inherit_value;
            }

            return $this->get_inherit_value( kalium_get_array_key( TypoLab_Data::get_responsive_breakpoints(), $inherit_from ) );
        }

        return null;
    }

    /**
     * Property value setter.
     *
     * @param string $breakpoint_id
     * @param mixed  $value
     */
    public function set_value( $breakpoint_id, $value ) {
        $this->value[ $breakpoint_id ] = $value;
    }

    /**
     * To array.
     *
     * @return array
     */
    public function to_array() {
        return $this->value;
    }
}
