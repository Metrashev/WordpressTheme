<?php
/*
 *  ACF Google Map Field Class
 *
 *  All the logic for this field type
 *
 *  @class 		acf_field_google_map
 *  @extends		acf_field
 *  @package		ACF
 *  @subpackage	Fields
 */

if ( !class_exists( 'acf_field_google_map' ) ) :

    class acf_field_google_map extends acf_field {
        /*
         *  __construct
         *
         *  This function will setup the field type data
         *
         *  @type	function
         *  @date	5/03/2014
         *  @since	5.0.0
         *
         *  @param	n/a
         *  @return	n/a
         */

        function __construct() {

            // vars
            $this->name = 'google_map';
            $this->label = __( "Google Map", 'acf' );
            $this->category = 'jquery';
            $this->defaults = array(
                'height' => '',
                'center_lat' => '',
                'center_lng' => '',
                'zoom' => ''
            );
            $this->default_values = array(
                'height' => '400',
                'center_lat' => '-37.81411',
                'center_lng' => '144.96328',
                'zoom' => '14'
            );
            $this->l10n = array(
                'locating' => __( "Locating", 'acf' ),
                'browser_support' => __( "Sorry, this browser does not support geolocation", 'acf' ),
            );


            // do not delete!
            parent::__construct();
        }

        /*
         *  render_field()
         *
         *  Create the HTML interface for your field
         *
         *  @param	$field - an array holding all the field's data
         *
         *  @type	action
         *  @since	3.6
         *  @date	23/01/13
         */

        function render_field( $field ) {

            // validate value
            if ( empty( $field['value'] ) ) {

                $field['value'] = array();
            }


            // value
            $field['value'] = acf_parse_args( $field['value'], array(
                'address' => '',
                'lat' => '',
                'lng' => ''
                    ) );


            // default options
            foreach ( $this->default_values as $k => $v ) {

                if ( empty( $field[$k] ) ) {

                    $field[$k] = $v;
                }
            }


            // vars
            $atts = array(
                'id' => $field['id'],
                'class' => $field['class'],
                'data-id' => $field['id'] . '-' . uniqid(),
                'data-lat' => $field['center_lat'],
                'data-lng' => $field['center_lng'],
                'data-zoom' => $field['zoom']
            );


            // modify atts
            $atts['class'] .= ' acf-google-map';

            if ( $field['value']['address'] ) {

                $atts['class'] .= ' active';
            }
            ?>
            <div <?php acf_esc_attr_e( $atts ); ?>>

                <div class="acf-hidden">
                    <?php foreach ( $field['value'] as $k => $v ): ?>
                        <input type="hidden" class="input-<?php echo $k; ?>" name="<?php echo esc_attr( $field['name'] ); ?>[<?php echo $k; ?>]" value="<?php echo esc_attr( $v ); ?>" />
                    <?php endforeach; ?>
                </div>

                <div class="title acf-soh">

                    <div class="has-value">
                        <a href="#" data-name="clear-location" class="acf-icon light acf-soh-target" title="<?php _e( "Clear location", 'acf' ); ?>">
                            <i class="acf-sprite-delete"></i>
                        </a>
                        <h4><?php echo $field['value']['address']; ?></h4>
                    </div>

                    <div class="no-value">
                        <a href="#" data-name="find-location" class="acf-icon light acf-soh-target" title="<?php _e( "Find current location", 'acf' ); ?>">
                            <i class="acf-sprite-locate"></i>
                        </a>
                        <input type="text" placeholder="<?php _e( "Search for address...", 'acf' ); ?>" class="search" />
                    </div>

                </div>

                <div class="canvas" style="height: <?php echo $field['height']; ?>px">

                </div>

            </div>
            <?php
        }

        /*
         *  render_field_settings()
         *
         *  Create extra options for your field. This is rendered when editing a field.
         *  The value of $field['name'] can be used (like bellow) to save extra data to the $field
         *
         *  @type	action
         *  @since	3.6
         *  @date	23/01/13
         *
         *  @param	$field	- an array holding all the field's data
         */

        function render_field_settings( $field ) {

            // center_lat
            acf_render_field_setting( $field, array(
                'label' => __( 'Center', 'acf' ),
                'instructions' => __( 'Center the initial map', 'acf' ),
                'type' => 'text',
                'name' => 'center_lat',
                'prepend' => 'lat',
                'placeholder' => $this->default_values['center_lat']
            ) );


            // center_lng
            acf_render_field_setting( $field, array(
                'label' => __( 'Center', 'acf' ),
                'instructions' => __( 'Center the initial map', 'acf' ),
                'type' => 'text',
                'name' => 'center_lng',
                'prepend' => 'lng',
                'placeholder' => $this->default_values['center_lng']
            ) );


            // zoom
            acf_render_field_setting( $field, array(
                'label' => __( 'Zoom', 'acf' ),
                'instructions' => __( 'Set the initial zoom level', 'acf' ),
                'type' => 'text',
                'name' => 'zoom',
                'placeholder' => $this->default_values['zoom']
            ) );


            // allow_null
            acf_render_field_setting( $field, array(
                'label' => __( 'Height', 'acf' ),
                'instructions' => __( 'Customise the map height', 'acf' ),
                'type' => 'text',
                'name' => 'height',
                'append' => 'px',
                'placeholder' => $this->default_values['height']
            ) );
        }

        /*
         *  validate_value
         *
         *  description
         *
         *  @type	function
         *  @date	11/02/2014
         *  @since	5.0.0
         *
         *  @param	$post_id (int)
         *  @return	$post_id (int)
         */

        function validate_value( $valid, $value, $field, $input ) {

            // bail early if not required
            if ( !$field['required'] ) {

                return $valid;
            }


            if ( empty( $value ) || empty( $value['lat'] ) || empty( $value['lng'] ) ) {

                return false;
            }


            // return
            return $valid;
        }

        /*
         *  update_value()
         *
         *  This filter is appied to the $value before it is updated in the db
         *
         *  @type	filter
         *  @since	3.6
         *  @date	23/01/13
         *
         *  @param	$value - the value which will be saved in the database
         *  @param	$post_id - the $post_id of which the value will be saved
         *  @param	$field - the field array holding all the field options
         *
         *  @return	$value - the modified value
         */

        function update_value( $value, $post_id, $field ) {

            if ( empty( $value ) || empty( $value['lat'] ) || empty( $value['lng'] ) ) {

                return false;
            }


            // return
            return $value;
        }

    }

    new acf_field_google_map();

endif;
?>