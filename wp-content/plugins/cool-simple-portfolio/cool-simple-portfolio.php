<?php

/**
  Plugin Name: Cool Simple Portfolio
  Plugin URI: http://
  Description: Cool Simple Portfolio with a lot of options
  Version: 1.0
  Author:
  Author URI: http://
  License: GPLv2 or later
 * https://codex.wordpress.org/Function_Reference/get_the_terms
 */
  class CoolSimpleItems {

    public $isHQTheme = false;
    protected $_name;
    protected $_slug;
    public $version = '1.0.0';

    public function __construct( $name, $slug ) {
        $this->_name = $name;
        $this->_slug = $slug;
        global $isHQTheme;
        if ( isset( $isHQTheme ) ) {
            $this->isHQTheme = $isHQTheme;
        } else {
            $this->isHQTheme = false;
        }


        /*$this->type = 'standart';
        $this->columns = 2;
        $this->hover = 'effect-apollo'; // variable for hover effects  .effect-lilly, .effect-sadie, .effect-slider, .effect-honey, .effect-oscar, .effect-bubba, effect-apollo, effect-moses
            */

        load_plugin_textdomain( $this->_slug, false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

        add_action( 'init', array( $this, 'register' ) );

        /* SETTINGS page  */
        add_action( 'admin_menu', array( $this, 'settings_page' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'scripts_styles' ), 500 );

        /* Define images */
        add_image_size( $this->_slug . '-medium', get_option( $this->_slug . '_thumb_size_w', '300' ), get_option( $this->_slug . '_thumb_size_h', '250' ), true );
        add_image_size( $this->_slug . '-large', get_option( $this->_slug . '_img_size_w', '700' ), get_option( $this->_slug . '_img_size_h', '400' ), true );

        /* SHORTCODE */
        add_shortcode( $this->_slug, array( $this, 'shortcode_listing' ) );
        add_shortcode( $this->_slug . '_categories', array( $this, 'shortcode_categories' ) );
        add_shortcode( $this->_slug . '_tags', array( $this, 'shortcode_tags' ) );

        /* Frontend STYLES and SCRIPTS */
        add_action( 'wp_enqueue_scripts', array( $this, 'main_style' ) );
        add_filter( 'template_include', array( $this, 'template_loader' ) );
        add_filter( 'hq_add_sections_map', array( $this, 'hq_add_sections_map' ) );
        add_filter( 'hq_add_setting_controls_map', array( $this, 'hq_add_setting_controls_map' ) );

        require_once $this->plugin_path() . '/inc/acf-pro/acf.php';
        require_once $this->plugin_path() . '/inc/acf-pro/customfields.php';
    }

    function register() {
        $labels = array(
            'name' => __( 'Cool Simple ' . $this->_name, $this->_slug ),
            'singular_name' => __( $this->_name . ' Item', $this->_slug ),
            'add_new' => __( 'Add New', $this->_slug ),
            'add_new_item' => __( 'Add New Item', $this->_slug ),
            'edit_item' => __( 'Edit Item', $this->_slug ),
            'new_item' => __( 'New Item', $this->_slug ),
            'view_item' => __( 'View Item', $this->_slug ),
            'search_items' => __( 'Search Items', $this->_slug ),
            'not_found' => __( 'No items found', $this->_slug ),
            'not_found_in_trash' => __( 'No items found in Trash', $this->_slug ),
            'parent_item_colon' => __( 'Parent Item:', $this->_slug ),
            'menu_name' => __( 'Cool Simple ' . $this->_name, $this->_slug )
            );

        $args = array(
            'labels' => $labels,
            'hierarchical' => true,
            'description' => __( 'Custom Post Type - ' . $this->_name . ' Pages', $this->_slug ),
            'supports' => array( 'title', 'editor', 'excerpt', 'thumbnail', 'tags' ),
            'taxonomies' => array( $this->_slug . '-category', $this->_slug . '-tag' ),
            'public' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'menu_position' => 20,
            //'menu_icon' => plugins_url( 'images/portfolio.png', __FILE__ ),
            'show_in_nav_menus' => true,
            'publicly_queryable' => true,
            'exclude_from_search' => false,
            'has_archive' => true,
            'query_var' => true,
            'can_export' => true,
            'rewrite' => array(
                'slug' => get_option( $this->_slug . '_item_slug', 'item' ),
                'with_front' => false
                ),
            'capability_type' => 'post'
            );

register_post_type( $this->_slug, $args );

        // "Categories" Custom Taxonomy
$labels = array(
    'name' => __( $this->_name . ' Categories', $this->_slug ),
    'singular_name' => __( $this->_name . ' Category', $this->_slug ),
    'search_items' => __( 'Search ' . $this->_name . ' Categories', $this->_slug ),
    'all_items' => __( 'All ' . $this->_name . ' Categories', $this->_slug ),
    'parent_item' => __( 'Parent ' . $this->_name . ' Category', $this->_slug ),
    'parent_item_colon' => __( 'Parent ' . $this->_name . ' Category:', $this->_slug ),
    'edit_item' => __( 'Edit ' . $this->_name . ' Category', $this->_slug ),
    'update_item' => __( 'Update ' . $this->_name . ' Category', $this->_slug ),
    'add_new_item' => __( 'Add New ' . $this->_name . ' Category', $this->_slug ),
    'new_item_name' => __( 'New ' . $this->_name . ' Category Name', $this->_slug ),
    'menu_name' => __( $this->_name . ' Categories', $this->_slug )
    );

$args = array(
    'hierarchical' => true,
    'labels' => $labels,
    'show_ui' => true,
    'query_var' => true,
    'rewrite' => array(
        'slug' => get_option( $this->_slug . '_item_category_slug', 'item-category' ),
        'with_front' => false
        )
    );

register_taxonomy( $this->_slug . '-category', array( $this->_slug ), $args );

        // "Tags" Custom Taxonomy
$labels = array(
    'name' => __( $this->_name . ' Tags', $this->_slug ),
    'singular_name' => __( $this->_name . ' Tag', $this->_slug ),
    'search_items' => __( 'Search ' . $this->_name . ' Tags', $this->_slug ),
    'all_items' => __( 'All ' . $this->_name . ' Tags', $this->_slug ),
    'parent_item' => __( 'Parent ' . $this->_name . ' Tag', $this->_slug ),
    'parent_item_colon' => __( 'Parent ' . $this->_name . ' Tags:', $this->_slug ),
    'edit_item' => __( 'Edit ' . $this->_name . ' Tag', $this->_slug ),
    'update_item' => __( 'Update ' . $this->_name . ' Tag', $this->_slug ),
    'add_new_item' => __( 'Add New ' . $this->_name . ' Tag', $this->_slug ),
    'new_item_name' => __( 'New ' . $this->_name . ' Tag Name', $this->_slug ),
    'menu_name' => __( $this->_name . ' Tags', $this->_slug )
    );

$args = array(
    'hierarchical' => true,
    'labels' => $labels,
    'show_ui' => true,
    'query_var' => true,
    'rewrite' => array(
        'slug' => get_option( $this->_slug . '_item_tag_slug', 'item-tag' ),
        'with_front' => false
        )
    );

register_taxonomy( $this->_slug . '-tag', array( $this->_slug ), $args );
}

function settings_page() {
    if ( !$this->isHQTheme ) {
        add_submenu_page( 'edit.php?post_type=' . $this->_slug, __( 'Settings', $this->_slug ), __( $this->_name . ' Settings', $this->_slug ), 'edit_posts', basename( __FILE__ ), array( $this, 'settings' ) );
        add_action( 'admin_init', array( $this, 'service_settings_store' ) );
    }
}

function service_settings_store() {
    register_setting( 'service_settings', $this->_slug . '_thumb_size_w' );
    register_setting( 'service_settings', $this->_slug . '_thumb_size_h' );
    register_setting( 'service_settings', $this->_slug . '_img_size_w' );
    register_setting( 'service_settings', $this->_slug . '_img_size_h' );
    register_setting( 'service_settings', $this->_slug . '_item_slug' );
    register_setting( 'service_settings', $this->_slug . '_item_category_slug' );
    register_setting( 'service_settings', $this->_slug . '_item_tag_slug' );
    register_setting( 'service_settings', $this->_slug . '_listing' );
    register_setting( 'service_settings', $this->_slug . '_hover' );
    register_setting( 'service_settings', $this->_slug . '_columns' );
    register_setting( 'service_settings', $this->_slug . '_listing_recent');
    register_setting( 'service_settings', $this->_slug . '_columns_recent');
    register_setting( 'service_settings', $this->_slug . '_hover_recent');

}

function settings() {
    ?>
    <div class="wrap">
        <h2><?php _e( $this->_name . ' Settings', $this->_slug ); ?></h2>
        <form method="post" action="options.php">
            <?php settings_fields( 'service_settings' ); ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><?php _e( $this->_name . ' Thumnbail Size (Archive)', $this->_slug ); ?> </th>
                    <td>
                        <label for="<?php echo $this->_slug ?>_thumb_size_w"><?php _e( 'Width (in px)', $this->_slug ); ?></label> <input type="text" name="<?php echo $this->_slug ?>_thumb_size_w" value="<?php echo get_option( $this->_slug . '_thumb_size_w', '300' ); ?>" /> <?php _e( '(default is 303)', $this->_slug ); ?><br />
                        <label for="<?php echo $this->_slug ?>_thumb_size_h"><?php _e( 'Height (in px)', $this->_slug ); ?></label> <input type="text" name="<?php echo $this->_slug ?>_thumb_size_h" value="<?php echo get_option( $this->_slug . '_thumb_size_h', '250' ); ?>" /> <?php _e( '(default is 210)', $this->_slug ); ?>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php _e( $this->_name . ' Image Size (Single)', $this->_slug ); ?> </th>
                    <td>
                        <label for="<?php echo $this->_slug ?>_img_size_w"><?php _e( 'Width (in px)', $this->_slug ); ?></label> <input type="text" name="<?php echo $this->_slug ?>_img_size_w" value="<?php echo get_option( $this->_slug . '_img_size_w', '700' ); ?>" /> <?php _e( '(default is 700)', $this->_slug ); ?><br />
                        <label for="<?php echo $this->_slug ?>_img_size_h"><?php _e( 'Height (in px)', $this->_slug ); ?></label> <input type="text" name="<?php echo $this->_slug ?>_img_size_h" value="<?php echo get_option( $this->_slug . '_img_size_h', '400' ); ?>" /> <?php _e( '(default is 400)', $this->_slug ); ?>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php _e( $this->_name . ' Slugs', $this->_slug ); ?> </th>
                    <td>
                        <label for="<?php echo $this->_slug ?>_item_slug"><?php _e( 'Item Slug', $this->_slug ); ?></label> <input type="text" name="<?php echo $this->_slug ?>_item_slug" value="<?php echo get_option( $this->_slug . '_item_slug', 'item' ); ?>" /> <?php _e( '(default is "item")', $this->_slug ); ?><br />
                        <label for="<?php echo $this->_slug ?>_item_category_slug"><?php _e( 'Category Slug', $this->_slug ); ?></label> <input type="text" name="<?php echo $this->_slug ?>_item_category_slug" value="<?php echo get_option( $this->_slug . '_item_category_slug', 'item-category' ); ?>" /> <?php _e( '(default is "item-category")', $this->_slug ); ?><br />
                        <label for="<?php echo $this->_slug ?>_item_tag_slug"><?php _e( 'Tag Slug', $this->_slug ); ?></label> <input type="text" name="<?php echo $this->_slug ?>_item_tag_slug" value="<?php echo get_option( $this->_slug . '_item_tag_slug', 'item-tag' ); ?>" /> <?php _e( '(default is "item-tag")', $this->_slug ); ?>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php _e( $this->_name . ' Layouts', $this->_slug ); ?> </th>
                    <td>
                        <label for="<?php echo $this->_slug ?>_listing"><?php _e( 'Listing', $this->_slug ); ?>: </label> 
                        <select name="<?php echo $this->_slug ?>_listing">
                            <option value="standart"<?php if ( get_option( $this->_slug . '_listing', 'standart' ) == 'standart' ) echo 'selected' ?>>Standart</option>
                            <option value="masonry"<?php if ( get_option( $this->_slug . '_listing', 'masonry' ) == 'masonry' ) echo 'selected' ?>>Masonry</option>
                            <option value="standart-with-space"<?php if ( get_option( $this->_slug . '_listing', 'standart-with-space' ) == 'standart-with-space' ) echo 'selected' ?>>Standart with space</option>
                            <option value="masonry-with-space"<?php if ( get_option( $this->_slug . '_listing', 'masonry-with-space' ) == 'masonry-with-space' ) echo 'selected' ?>>Masonry with space</option>
                        </select><br />
                        <?php _e( 'Single: Single layout can be changed by project.', $this->_slug ); ?>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php _e( $this->_name . ' Hover', $this->_slug ); ?> </th>
                    <td>
                        <label for="<?php echo $this->_slug ?>_hover"><?php _e( 'Hover Effect', $this->_slug ); ?>: </label> 
                        <select name="<?php echo $this->_slug ?>_hover">
                            <option value="effect-lilly"<?php if ( get_option( $this->_slug . '_hover', 'effect-lilly' ) == 'effect-lilly' ) echo 'selected' ?>>Effect Lilly</option>
                            <option value="effect-sadie"<?php if ( get_option( $this->_slug . '_hover', 'effect-sadie' ) == 'effect-sadie' ) echo 'selected' ?>>Effect Sadie</option>
                            <option value="effect-slider"<?php if ( get_option( $this->_slug . '_hover', 'effect-slider' ) == 'effect-slider' ) echo 'selected' ?>>Effect Slider</option>
                            <option value="effect-honey"<?php if ( get_option( $this->_slug . '_hover', 'effect-honey' ) == 'effect-honey' ) echo 'selected' ?>>Effect Honey</option>
                            <option value="effect-oscar"<?php if ( get_option( $this->_slug . '_hover', 'effect-oscar' ) == 'effect-oscar' ) echo 'selected' ?>>Effect Oscar</option>
                            <option value="effect-bubba"<?php if ( get_option( $this->_slug . '_hover', 'effect-bubba' ) == 'effect-bubba' ) echo 'selected' ?>>Effect Bubba</option>
                            <option value="effect-apollo"<?php if ( get_option( $this->_slug . '_hover', 'effect-apollo' ) == 'effect-apollo' ) echo 'selected' ?>>Effect Apollo</option>
                            <option value="effect-moses"<?php if ( get_option( $this->_slug . '_hover', 'effect-moses' ) == 'effect-moses' ) echo 'selected' ?>>Effect Moses</option>
                        </select><br />
                        <?php _e( '', $this->_slug ); ?>
                    </td>
                </tr>

                <tr valign="top">
                    <th scope="row"><?php _e( $this->_name . ' Columns', $this->_slug ); ?> </th>
                    <td>
                        <label for="<?php echo $this->_slug ?>_columns"><?php _e( 'Portfolio Columns', $this->_slug ); ?>: </label> 
                        <select name="<?php echo $this->_slug ?>_columns">
                            <option value="2"<?php if ( get_option( $this->_slug . '_columns', '1' ) == '1' ) echo 'selected' ?>>1</option>
                            <option value="2"<?php if ( get_option( $this->_slug . '_columns', '2' ) == '2' ) echo 'selected' ?>>2</option>
                            <option value="3"<?php if ( get_option( $this->_slug . '_columns', '3' ) == '3' ) echo 'selected' ?>>3</option>
                            <option value="4"<?php if ( get_option( $this->_slug . '_columns', '4' ) == '4' ) echo 'selected' ?>>4</option>
                            <option value="5"<?php if ( get_option( $this->_slug . '_columns', '5' ) == '5' ) echo 'selected' ?>>5</option>
                        </select><br />
                        <?php _e( '', $this->_slug ); ?>
                    </td>
                </tr>

                <tr valign="top">
                    <th scope="row"><?php _e( $this->_name . ' Layouts Recent', $this->_slug ); ?> </th>
                    <td>
                        <label for="<?php echo $this->_slug ?>_listing_recent"><?php _e( 'Listing Recent', $this->_slug ); ?>: </label> 
                        <select name="<?php echo $this->_slug ?>_listing_recent">
                            <option value="standart"<?php if ( get_option( $this->_slug . '_listing_recent', 'standart' ) == 'standart' ) echo 'selected' ?>>Standart</option>
                            <option value="masonry"<?php if ( get_option( $this->_slug . '_listing_recent', 'masonry' ) == 'masonry' ) echo 'selected' ?>>Masonry</option>
                            <option value="standart-with-space"<?php if ( get_option( $this->_slug . '_listing_recent', 'standart-with-space' ) == 'standart-with-space' ) echo 'selected' ?>>Standart with space</option>
                            <option value="masonry-with-space"<?php if ( get_option( $this->_slug . '_listing_recent', 'masonry-with-space' ) == 'masonry-with-space' ) echo 'selected' ?>>Masonry with space</option>
                        </select><br />
                        <?php _e( 'Single: Single layout can be changed by project.', $this->_slug ); ?>
                    </td>
                </tr>

                <tr valign="top">
                    <th scope="row"><?php _e( $this->_name . ' Hover Recent', $this->_slug ); ?> </th>
                    <td>
                        <label for="<?php echo $this->_slug ?>_hover_recent"><?php _e( 'Hover Effect Recent', $this->_slug ); ?>: </label> 
                        <select name="<?php echo $this->_slug ?>_hover_recent">
                            <option value="effect-lilly"<?php if ( get_option( $this->_slug . '_hover_recent', 'effect-lilly' ) == 'effect-lilly' ) echo 'selected' ?>>Effect Lilly</option>
                            <option value="effect-sadie"<?php if ( get_option( $this->_slug . '_hover_recent', 'effect-sadie' ) == 'effect-sadie' ) echo 'selected' ?>>Effect Sadie</option>
                            <option value="effect-slider"<?php if ( get_option( $this->_slug . '_hover_recent', 'effect-slider' ) == 'effect-slider' ) echo 'selected' ?>>Effect Slider</option>
                            <option value="effect-honey"<?php if ( get_option( $this->_slug . '_hover_recent', 'effect-honey' ) == 'effect-honey' ) echo 'selected' ?>>Effect Honey</option>
                            <option value="effect-oscar"<?php if ( get_option( $this->_slug . '_hover_recent', 'effect-oscar' ) == 'effect-oscar' ) echo 'selected' ?>>Effect Oscar</option>
                            <option value="effect-bubba"<?php if ( get_option( $this->_slug . '_hover_recent', 'effect-bubba' ) == 'effect-bubba' ) echo 'selected' ?>>Effect Bubba</option>
                            <option value="effect-apollo"<?php if ( get_option( $this->_slug . '_hover_recent', 'effect-apollo' ) == 'effect-apollo' ) echo 'selected' ?>>Effect Apollo</option>
                            <option value="effect-moses"<?php if ( get_option( $this->_slug . '_hover_recent', 'effect-moses' ) == 'effect-moses' ) echo 'selected' ?>>Effect Moses</option>
                        </select><br />
                        <?php _e( '', $this->_slug ); ?>
                    </td>
                </tr>

                <tr valign="top">
                    <th scope="row"><?php _e( $this->_name . ' Columns Recent', $this->_slug ); ?> </th>
                    <td>
                        <label for="<?php echo $this->_slug ?>_columns_recent"><?php _e( 'Portfolio Columns Recent', $this->_slug ); ?>: </label> 
                        <select name="<?php echo $this->_slug ?>_columns_recent">
                            <option value="2"<?php if ( get_option( $this->_slug . '_columns_recent', '1' ) == '1' ) echo 'selected' ?>>1</option>
                            <option value="2"<?php if ( get_option( $this->_slug . '_columns_recent', '2' ) == '2' ) echo 'selected' ?>>2</option>
                            <option value="3"<?php if ( get_option( $this->_slug . '_columns_recent', '3' ) == '3' ) echo 'selected' ?>>3</option>
                            <option value="4"<?php if ( get_option( $this->_slug . '_columns_recent', '4' ) == '4' ) echo 'selected' ?>>4</option>
                            <option value="5"<?php if ( get_option( $this->_slug . '_columns_recent', '5' ) == '5' ) echo 'selected' ?>>5</option>
                        </select><br />
                        <?php _e( '', $this->_slug ); ?>
                    </td>
                </tr>
            </table>
            <p class="submit"><input type="submit" class="button-primary" value="<?php _e( 'Save Changes', $this->_slug ); ?>" /></p>
        </form>
    </div>
    <?php
}

function hq_add_sections_map( $add_sections ) {
    $add_sections['hq_' . $this->_slug . '_settings'] = array(
        'title' => __( $this->_name, $this->_slug ),
        );
    return $add_sections;
}

function hq_add_setting_controls_map( $add_setting_controls ) {
    $section = 'hq_' . $this->_slug . '_settings';
    $setting_controls = array(


        'hq_' . $this->_slug . '_thumb_size_w' => array(
            'default' => '300',
            'label' => __( $this->_name . ' Thumnbail size (Width)', HQTheme::THEME_SLUG ),
            'section' => $section,
            'type' => 'number',
            ),
        'hq_' . $this->_slug . '_thumb_size_h' => array(
            'default' => '200',
            'label' => __( $this->_name . ' Thumnbail size (Height)', HQTheme::THEME_SLUG ),
            'section' => $section,
            'type' => 'number',
            ),
        'hq_' . $this->_slug . '_img_size_w' => array(
            'default' => '700',
            'label' => __( $this->_name . ' Image size (Width)', HQTheme::THEME_SLUG ),
            'section' => $section,
            'type' => 'number',
            ),
        'hq_' . $this->_slug . '_img_size_h' => array(
            'default' => '400',
            'label' => __( $this->_name . ' Image size (Height)', HQTheme::THEME_SLUG ),
            'section' => $section,
            'type' => 'number',
            ),
        'hq_' . $this->_slug . '_item_slug' => array(
            'default' => 'item',
            'label' => __( $this->_name . ' Item Slug', HQTheme::THEME_SLUG ),
            'section' => $section,
            'type' => 'text',
            ),
        'hq_' . $this->_slug . '_item_category_slug' => array(
            'default' => 'item-category',
            'label' => __( $this->_name . ' Category Slug', HQTheme::THEME_SLUG ),
            'section' => $section,
            'type' => 'text',
            ),
        'hq_' . $this->_slug . '_item_tag_slug' => array(
            'default' => 'item-tag',
            'label' => __( $this->_name . ' Tag Slug', HQTheme::THEME_SLUG ),
            'section' => $section,
            'type' => 'text',
            ),
        /*'hq_' . $this->_slug . '_listing' => array(
            'default' => 'Standart',
            'label' => __( $this->_name . ' Layout', HQTheme::THEME_SLUG ),
            'section' => $section,
            'type' => 'select',
            'choices' => array(
                'standart' => 'Standart',
                'standart-with-space' => 'Standart with space',
                'masonry' => 'Masonry',
                'masonry-with-space' => 'Masonry with space',
                )
            ),
        'hq_' . $this->_slug . '_columns' => array(
            'default' => '1',
            'label' => __( $this->_name . ' Columns', HQTheme::THEME_SLUG ),
            'section' => $section,
            'type' => 'select',
            'choices' => array(
                '1' => '1',
                '2' => '2',
                '3' => '3',
                '4' => '4',
                '5' => '5',
                )
            ),
        'hq_' . $this->_slug . '_hover' => array(
            'default' => 'standart',
            'label' => __( $this->_name . ' Hover', HQTheme::THEME_SLUG ),
            'section' => $section,
            'type' => 'select',
            'choices' => array(
                'effect-lilly' => 'Effect Lilly',
                'effect-sadie' => 'Effect Sadie',
                'effect-slider' => 'Effect Slider',
                'effect-honey' => 'Effect Honey',
                'effect-oscar' => 'Effect Oscar',
                'effect-bubba' => 'Effect Bubba',
                'effect-apollo' => 'Effect Apollo',
                'effect-moses' => 'Effect Moses',
                )
            ),*/
/*
    До тук са контролите, които са в customizer-а. Горните 3 са закоментирани, защото се получават 2 класа в DOM дървото. Трябва да се направи проверка, кое да се избира първо. 
    А долните закоментарени контроли са старите, които ми каза да закоментирам*/ 
/*=====================================================================================*/

       /* 'hq_' . $this->_slug . '_listing' => array(
            'default' => 'Standart',
            'label' => __( $this->_name . ' Grid', HQTheme::THEME_SLUG ),
            'section' => $section,
            'type' => 'select',
            'choices' => array(
                'standart' => 'Standart',
                'standart-with-space' => 'Standart with space',
                'masonry' => 'Masonry',
                'masonry-with-space' => 'Masonry with space',
                )
            ),
        'hq_' . $this->_slug . '_sidebar_left' => array(
            'default' => 0,
            'label' => __( $this->_name . ' Sidebar Left', HQTheme::THEME_SLUG ),
            'section' => $section,
            'type' => 'select',
            'choices' => HQTheme_Customize::getInstance()->getAvailableSidebars()
            ),
        'hq_' . $this->_slug . '_sidebar_right' => array(
            'default' => 0,
            'label' => __( $this->_name . ' Sidebar Right', HQTheme::THEME_SLUG ),
            'section' => $section,
            'type' => 'select',
            'choices' => HQTheme_Customize::getInstance()->getAvailableSidebars()
            ),
        'hq_' . $this->_slug . '_pagination' => array(
            'default' => 0,
            'label' => __( $this->_name . ' Infinite scroll', HQTheme::THEME_SLUG ),
            'section' => $section,
            'type' => 'checkbox',
            ),
        'woocommerce_single_page' => array(
            'setting_type' => null,
            'control' => 'HQTheme_Controls',
            'section' => $section,
            'type' => 'sub-title',
            'label' => __( 'Product Page Options', HQTheme::THEME_SLUG ),
            ),
        'hq_' . $this->_slug . '_single_title' => array(
            'default' => 1,
            'label' => __( 'Product Title', HQTheme::THEME_SLUG ),
            'section' => $section,
            'type' => 'checkbox',
            ),
        'hq_' . $this->_slug . '_single_ratings' => array(
            'default' => 1,
            'label' => __( 'Product Ratings', HQTheme::THEME_SLUG ),
            'section' => $section,
            'type' => 'checkbox',
            ),
        'hq_' . $this->_slug . '_single_social' => array(
            'default' => 1,
            'label' => __( 'Product Social Shares', HQTheme::THEME_SLUG ),
            'section' => $section,
            'type' => 'checkbox',
            ),
        'hq_' . $this->_slug . '_single_prev_next' => array(
            'default' => 1,
            'label' => __( 'Previous / Next Links', HQTheme::THEME_SLUG ),
            'section' => $section,
            'type' => 'checkbox',
            ),*/
        ); //end of layout_options
return array_merge(
    $add_setting_controls, $setting_controls
    );
}

function shortcode_listing( $atts ) {
    global $wpdb;

    $args = array(
        'post_type' => $this->_slug,
        'post_status' => 'publish',
        'posts_per_page' => -1,
        );

    $my_query = null;
    $my_query = new WP_Query( $args );
    if ( $my_query->have_posts() ) {
        ?>
        <ul id="filters">
            <li><a href="#" data-filter="*" class="selected"><?php _e( 'All', $this->_slug ); ?></a></li>
            <?php
                $terms = get_terms( $this->_slug . '-category' ); // get all categories, but you can use any taxonomy
                $count = count( $terms ); //How many are they?
                if ( $count > 0 ) {  //If there are more than 0 terms
                    foreach ( $terms as $term ) {  //for each term:
                        echo "<li><a href='#' data-filter='." . $term->slug . "'>" . $term->name . "</a></li>\n";
                    }
                }
                ?>
            </ul>
            <?php
            echo '<div id="projects">';
            
            /**
             * Някой ефекти чупят линка. 
             * Линка трябва да е на цялата картинка.
             * Ефектите в листига и в Related projects трябва да са еднакви.
             * Оциите, които сега са в променливи трябва се изкават някъде, където и портебителите могат да ги контролират.
             * Ще ги сложим на 2 места. 
             * 1 в страницата с настройки на плъгина (Portfolio Settings). Тук се слагат като се добавят във функциите: service_settings_store settings. Виж как са добавени дрите опции и добав и новите.
             * 2 в къстъмайзера, но това за сега ще го оставим.
             * Опциите са: $hover, $type, $columns, $layoutType
             * Стойностите се вземат с функцията get_option( $option_name, $default_value )
             * Трябва да се направи widget - това ще ти обесня как ще стане
             * Добавяне на този ефекти при зареждане - https://graphicfusiondesign.com/design/creating-fancy-css3-fade-in-animations-on-page-load/
             * 
             * Всички тези ефекти трябва да са оптимало направени защото ще ги сложим и в темата на постове и продукти
             * 
             * Не се занимавай с нещата надолу
             * Още малко ефекти - http://james-star.com/answers/en/css3-hover-effect-transitions-transformations-and-animations/
             * И нещо за генериране на ефекти (няма да го ползваме за сега) http://www.css3maker.com/css3-transition.html
             */
            
            while ( $my_query->have_posts() ) : $my_query->the_post();
            $post = get_post( get_the_ID() );
            $terms = get_the_terms( $post->ID, $this->_slug . '-category' );
            $termsString = '';
            if ( is_array( $terms ) ) {
                foreach ( $terms as $term ) {
                    $termsString .= $term->slug . ' ';
                }
            }
            /*$portfolioLayout = get_theme_mod( 'hq_' . $this->_slug . '_listing' );
            $portfolioColumn = get_theme_mod( 'hq_' . $this->_slug . '_columns' );
            $portfolioHover = get_theme_mod( 'hq_' . $this->_slug . '_hover' );*/
            ?>
            <div class="<?php echo $termsString; ?> project <?php echo get_option( $this->_slug . '_listing', '') ?>  <?php echo $portfolioLayout ?> portfolio-column-<?php echo get_option( $this->_slug . '_columns', '') ?> portfolio-column-<?php echo $portfolioColumn ?>">
                <div class="wrapper-image-grid-portfolio <?php echo get_option( $this->_slug . '_hover', '') ?> <?php echo $portfolioHover ?>"> 
                    <?php the_post_thumbnail( 'csportfolio-medium' ); ?> 
                    <div class="overlay-hover">
                        <a class="tagline overtext" href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a>
                    </div>
                </div>
                <p class="portfolio-title">
                    <a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a>
                </p>

                <div class="portfolio-grid-content">
                    <?php the_content(); ?>
                </div>
            </div>
            <?php
            endwhile;
            echo '</div>';
            ?>
            <script type="text/javascript">
            (function ($) {
                $container = $('#projects');
                $container.isotope({itemSelector: '.project'});

                $('#filters a').click(function () {
                    var filterValue = $(this).attr('data-filter');
                    $container.isotope({filter: filterValue});
                });

            })(jQuery);
            </script>
            <?php
        }
        wp_reset_query();  // Restore global post data stomped by the_post().
    }

    function shortcode_categories( $atts ) {
        global $wpdb;

        $terms = get_terms( $this->_slug . '-category' );
        if ( !empty( $terms ) && !is_wp_error( $terms ) ) {
            echo '<ul>';
            foreach ( $terms as $term ) {
                echo '<li><a href="' . get_term_link( $term, $this->_slug . '-category' ) . '">' . $term->name . '</li>';
            }
            echo '</ul>';
        }
        wp_reset_query();  // Restore global post data stomped by the_post().
    }

    function shortcode_tags( $atts ) {
        global $wpdb;

        $terms = get_terms( $this->_slug . '-tag' );
        if ( !empty( $terms ) && !is_wp_error( $terms ) ) {
            echo '<ul>';
            foreach ( $terms as $term ) {
                echo '<li><a href="' . get_term_link( $term, $this->_slug . '-tag' ) . '">' . $term->name . '</li>';
            }
            echo '</ul>';
        }
        wp_reset_query();  // Restore global post data stomped by the_post().
    }

    function main_style() { // FIX + VERSION
        wp_register_style( $this->_slug, plugins_url( '/cool-simple-portfolio/css/main.css' ), array(), $this->version, 'all' );
        wp_enqueue_style( $this->_slug );
    }

    function scripts_styles() {

        $suffix = (defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG) ? '' : '.min';

        wp_register_script( 'isotope', plugins_url( "/js/isotope.pkgd.min.js", __FILE__ ), array( 'jquery' ), $this->version );
        wp_enqueue_script( 'isotope' );
    }

    /* ----------------------------------------------------------------------------------- */
    /* Pagination */
    /* ----------------------------------------------------------------------------------- */

    function pagination() { // used from templates
        wp_link_pages( array( 'before' => '<div class="page-links">' . __( 'Pages:', $this->_slug ), 'after' => '</div>' ) );
    }

    function getSlug() { // used from templates
        return $this->_slug;
    }

    /**
     * Get the plugin url.
     *
     * @return string
     */
    public function plugin_url() {
        return untrailingslashit( plugins_url( '/', __FILE__ ) );
    }

    /**
     * Get the plugin path.
     *
     * @return string
     */
    public function plugin_path() {
        return untrailingslashit( plugin_dir_path( __FILE__ ) );
    }

    /**
     * Get the template path.
     *
     * @return string
     */
    public function template_path() {
        return $this->_slug . '/';
    }

    public function template_loader( $template ) {
        $template_path = $this->template_path();

        $find = array( 'cool-simple-portfolio.php' );
        $file = '';
        //var_dump( get_post_type(), is_single(), is_tax());

        if ( is_single() && get_post_type() == $this->_slug ) {
            $layoutType = 1;
            $file = 'single' . $layoutType . '.php';
            $find[] = $file;
            $find[] = $template_path . $file;
        } elseif ( is_tax() ) {
            $term = get_queried_object();
            if ( is_tax( $this->_slug . '_cat' ) || is_tax( $this->_slug . '_tag' ) ) {
                $file = 'taxonomy-' . $term->taxonomy . '.php';
            } else {
                $file = 'archive.php';
            }

            $find[] = 'taxonomy-' . $term->taxonomy . '-' . $term->slug . '.php';
            $find[] = $template_path . 'taxonomy-' . $term->taxonomy . '-' . $term->slug . '.php';
            $find[] = 'taxonomy-' . $term->taxonomy . '.php';
            $find[] = $template_path . 'taxonomy-' . $term->taxonomy . '.php';
            $find[] = $file;
            $find[] = $template_path . $file;
        } elseif ( is_post_type_archive( $this->_slug ) ) {
            $file = 'archive.php';
            $find[] = $file;
            $find[] = $template_path . $file;
        }
        //var_dump($file, $find);

        if ( $file ) {
            if ( file_exists( $this->plugin_path() . "/templates/{$file}" ) ) {
                $template = $this->plugin_path() . "/templates/{$file}";
            } else {
                $template = locate_template( array_unique( $find ) );
            }
        }

        return $template;
    }

    public function get_option( $option_name, $default_value ) {
        if ( $this->isHQTheme ) {
            get_theme_mod( $this->_slug . '_' . $option_name, $default_value );
        } else {
            get_option( $this->_slug . '_' . $option_name, $default_value );
        }
    }
}

$CoolSimplePortfolio = new CoolSimpleItems( 'Portfolio', 'csportfolio' );
?>