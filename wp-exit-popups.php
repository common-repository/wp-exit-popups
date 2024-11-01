<?php
/*
Plugin Name:    WP Exit Popups
Plugin URI:     https://wordpress.org/plugins/wp-exit-popups/
Description:    Create and configure exit popups that appear when the user intends to leave your website.
Version:        1.1.4
Author: 		Rocket Apps
Author URI: 	https://rocketapps.com.au
Text Domain: 	wp-epu
Author Email:   support@rocketapps.com.au
Domain Path:    /languages/
*/

/* Look for translation file. */
function wpepu_textdomain() {
    load_plugin_textdomain( 'wp-epu', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}
add_action( 'plugins_loaded', 'wpepu_textdomain' );

/* Add custom CSS and Thickbox to admin. */
function wpepu_admin_styles() {
	$plugin_directory = plugins_url('css/', __FILE__ );
	$wp_epu_plugin_data = get_plugin_data( __FILE__ );
	$wp_epu_plugin_version = $wp_epu_plugin_data['Version'];
	wp_enqueue_style('wp-epu-admin-styles', $plugin_directory . 'wp-exit-popup-admin.css', '', $wp_epu_plugin_version);
	wp_enqueue_script('jquery');
	wp_enqueue_media();
}
add_action('admin_enqueue_scripts', 'wpepu_admin_styles');


/* Only enqueue colour picker to  'wpepu_exit_popup' post type */
add_action( 'current_screen', 'wpepu_this_screen' );
function wpepu_this_screen() {
    $current_screen = get_current_screen();
    if( $current_screen->post_type === 'wpepu_exit_popup' ) {
        wp_enqueue_style( 'wp-color-picker' );
        wp_enqueue_script( 'epu-handle', plugins_url('js/epu.js', __FILE__ ), array( 'wp-color-picker' ), false, true );
    }
}

/* Enqueue front-end scripts */
function wpepu_front_end_scripts() {
    //wp_enqueue_style( 'style-name', get_stylesheet_uri() );
    wp_enqueue_script( 'cookie', plugin_dir_url( __FILE__ ) . 'js/js.cookie.min.js');
    wp_enqueue_script( 'jquery' );
}
add_action( 'wp_enqueue_scripts', 'wpepu_front_end_scripts' );


/* Set up custom post type for popups */
function create_wpepu_post_type() {

    register_post_type( 'wpepu_exit_popup',

        array(
            'labels' => array(
                'singular_name'     => __( 'Exit Popups', 'wp-epu'),
                'name' 				=> __( 'Exit Popups', 'wp-epu'),
                'add_new'           => __( 'Add Popup', 'wp-epu'),
                'add_new_item'      => __( 'Add Popup', 'wp-epu'),
                'edit_item'         => __( 'Edit Popup', 'wp-epu'),
                'new_item'          => __( 'New Popup', 'wp-epu'),
                'search_items'      => __( 'Search Popups', 'wp-epu'),
                'not_found'  		=> __( 'No Popups Found', 'wp-epu'),
                'not_found_in_trash'=> __( 'No Popups Found in Trash', 'wp-epu'),
                'all_items'     	=> __( 'All Popups','wp-epu')
            ),
        'public'			 	=> false,
        'has_archive' 			=> false,
        'rewrite'				=> array('slug' => 'exit-popups'),
        'publicly_queryable'  	=> false,
        'hierarchical'        	=> true,
        'show_ui' 				=> true,
        'show_in_menu'          => true,
        'exclude_from_search'	=> false,
        'query_var'				=> true,
        'menu_position'			=> 80,
        'can_export'          	=> true,
        'menu_icon'         	=> plugins_url('images/', __FILE__ ) . 'admin-icon.svg',
        'supports'  			=> array('title', 'revisions', 'author'),
        'capability_type'       => 'post',
        'taxonomies'            => array('wpepu_exit_popup'),
        'map_meta_cap'          => true,
        )
    );
}
add_action( 'init', 'create_wpepu_post_type' );

require_once('inc/help.php');

/* Add metabox for wpepu_exit_popup post type */
class wpepu_meta {

    var $plugin_dir;
    var $plugin_url;

    function  __construct() {

        add_action( 'add_meta_boxes', array( $this, 'epu_meta_box' ) );
        add_action( 'save_post', array($this, 'save_data') );
    }

    function epu_meta_box() {
        
        add_meta_box(
            'popup_details',
            __('Popup Details', 'wp-epu'),  
            array( &$this, 'meta_box_content' ),
            'wpepu_exit_popup',
            'normal',
            'high'
        );
    }

    function meta_box_content() {

        global $post;
        $layout                         = get_post_meta($post->ID, 'layout', true);
        $bg_image                       = get_post_meta($post->ID, 'bg_image', true);
        $bg_position                    = get_post_meta($post->ID, 'bg_position', true);
        $coax_text                      = get_post_meta($post->ID, 'coax_text', true);
        $coax_text_colour               = get_post_meta($post->ID, 'coax_text_colour', true);
        $coax_text_size                 = get_post_meta($post->ID, 'coax_text_size', true);
        $heading_text_colour            = get_post_meta($post->ID, 'heading_text_colour', true);
        $heading_text_size              = get_post_meta($post->ID, 'heading_text_size', true);
        $popup_bg_colour                = get_post_meta($post->ID, 'popup_bg_colour', true);
        $popup_bs_colour                = get_post_meta($post->ID, 'popup_bs_colour', true);
        $close_button_colour            = get_post_meta($post->ID, 'close_button_colour', true);
        $close_position                 = get_post_meta($post->ID, 'close_position', true);
        $close_style                    = get_post_meta($post->ID, 'close_style', true);
        $popup_width                    = get_post_meta($post->ID, 'popup_width', true);
        $popup_unit                     = get_post_meta($post->ID, 'popup_unit', true);
        $popup_padding                  = get_post_meta($post->ID, 'popup_padding', true);    
        $corner_radius                  = get_post_meta($post->ID, 'corner_radius', true);       
        
        $interaction                    = get_post_meta($post->ID, 'interaction', true);

        /* Gravity Forms */
        $gr_form_id                     = get_post_meta($post->ID, 'gr_form_id', true);
        $gr_form_title                  = get_post_meta($post->ID, 'gr_form_title', true);
        $gr_form_description            = get_post_meta($post->ID, 'gr_form_description', true);
        $gr_form_ajax                   = get_post_meta($post->ID, 'gr_form_ajax', true);

        /* Contact Form 7 */
        $cf_form_id                     = get_post_meta($post->ID, 'cf_form_id', true);
        $cf_form_title                  = get_post_meta($post->ID, 'cf_form_title', true);

        /* WP Forms */
        $wpf_form_id                    = get_post_meta($post->ID, 'wpf_form_id', true);
        $wpf_form_title                 = get_post_meta($post->ID, 'wpf_form_title', true);
        $wpf_form_description           = get_post_meta($post->ID, 'wpf_form_description', true);

        /* Formidable Forms */
        $formidable_form_id             = get_post_meta($post->ID, 'formidable_form_id', true);
        $formidable_form_title          = get_post_meta($post->ID, 'formidable_form_title', true);
        $formidable_form_description    = get_post_meta($post->ID, 'formidable_form_description', true);

        /* Ninja Forms */
        $nf_form_id                     = get_post_meta($post->ID, 'nf_form_id', true);

        $full_url                       = get_post_meta($post->ID, 'full_url', true);
        $full_url_text                  = get_post_meta($post->ID, 'full_url_text', true);

        $yes_url                        = get_post_meta($post->ID, 'yes_url', true);
        $no_url                         = get_post_meta($post->ID, 'no_url', true);

        $url_button_colour              = get_post_meta($post->ID, 'url_button_colour', true);
        $url_text_colour                = get_post_meta($post->ID, 'url_text_colour', true);
        
        $yes_or_no                      = get_post_meta($post->ID, 'yes_or_no', true);
        $offer_code                     = get_post_meta($post->ID, 'offer_code', true);
        $yes_url                        = get_post_meta($post->ID, 'yes_url', true);
        $no_url                         = get_post_meta($post->ID, 'no_url', true);
        $shortcode                      = get_post_meta($post->ID, 'shortcode', true);
        $modal_mask_colour              = get_post_meta($post->ID, 'modal_mask_colour', true);
        $modal_opacity                  = get_post_meta($post->ID, 'modal_opacity', true);    
        $bg_image_opacity               = get_post_meta($post->ID, 'bg_image_opacity', true);        
        $dismissible                    = get_post_meta($post->ID, 'dismissible', true);
        $cookie_expiry                  = get_post_meta($post->ID, 'cookie_expiry', true);;
        $show_immediately               = get_post_meta($post->ID, 'show_immediately', true);
        $specified_seconds              = get_post_meta($post->ID, 'specified_seconds', true);

        $active_popup                   = get_option( 'epu_active_popup' );
        
        wp_nonce_field( plugin_basename( __FILE__ ), 'exit_popup_nounce' );

    ?>

        <div class="epu-padder two-third-width">

            <h2><?php _e( 'Initial layout', 'wp-epu' ); ?></h2>
            <p><strong><?php _e( 'Select a Layout', 'wp-epu' ); ?></strong></p>
            <p class="description"><?php _e( 'Choose your preferred starting layout for this popup. If no choice is made, the starting layout will be left aligned.', 'wp-epu' ); ?></p>

            <div class="layouts">

                <label <?php if($layout == 'left') { echo 'class="layout-selected"'; } ?>>
                    <img src="<?php echo plugin_dir_url( __FILE__ ); ?>images/left.svg" />
                    <input type="radio" name="layout" value="left" <?php if($layout == 'left') { echo 'checked'; } ?> class="radio layout-left" />
                    <?php _e( 'Left align', 'wp-epu' ); ?>
                </label>

                <label <?php if($layout == 'center') { echo 'class="layout-selected"'; } ?>>
                    <img src="<?php echo plugin_dir_url( __FILE__ ); ?>images/center.svg" />
                    <input type="radio" name="layout" value="center" <?php if($layout == 'center') { echo 'checked'; } ?> class="radio layout-center" />
                    <?php _e( 'Centre', 'wp-epu' ); ?>
                </label>

                <label <?php if($layout == 'right') { echo 'class="layout-selected"'; } ?>>
                    <img src="<?php echo plugin_dir_url( __FILE__ ); ?>images/right.svg" />
                    <input type="radio" name="layout" value="right" <?php if($layout == 'right') { echo 'checked'; } ?> class="radio layout-right" />
                    <?php _e( 'Right align', 'wp-epu' ); ?>
                </label>

                <label disabled  style="pointer-events: none;">
                    <img src="<?php echo plugin_dir_url( __FILE__ ); ?>images/full-screen.svg" style="opacity: .3;" />
                    <?php _e( 'Full screen, centre', 'wp-epu' ); ?> <span class="pro"><?php _e( 'PRO', 'wp-epu' ); ?></span>
                </label>

                <label disabled  style="pointer-events: none;">
                    <img src="<?php echo plugin_dir_url( __FILE__ ); ?>images/bottom-center.svg" style="opacity: .3;" />
                    <?php _e( 'Bottom, centre', 'wp-epu' ); ?> <span class="pro"><?php _e( 'PRO', 'wp-epu' ); ?></span>
                </label>

            </div>

        </div>

        <div class="epu-padder third-width">

            <h2><?php _e( 'Heading Text', 'wp-epu' ); ?></h2>
            <p><strong><?php _e( 'Heading Text Presentation', 'wp-epu' ); ?></strong></p>
            <p class="description"><?php _e( 'The heading text is the title of this post. Place emphasis on the heading text with a suitable colour and size.', 'wp-epu' ); ?></p>

            <div class="flex">
                <div class="half-width">
                    <p><strong><?php _e( 'Colour', 'wp-epu' ); ?></strong></p>
                    <input type="text" name="heading_text_colour" value="<?php if($heading_text_colour) { echo $heading_text_colour; } ?>" maxlength="7" class="colour-picker" />
                </div>
                <div class="half-width">
                    <p><strong><?php _e( 'Size', 'wp-epu' ); ?></strong></p>
                    <input type="number" name="heading_text_size" value="<?php if($heading_text_size) { echo $heading_text_size; } ?>" min="1" step=".1" class="half-width" /> <?php _e( 'em', 'wp-epu' ); ?>
                </div>
            </div>

        </div>

        <div class="epu-padder third-width">

            <h2><?php _e( 'Coax Text', 'wp-epu' ); ?></h2>
            <p><strong><?php _e( 'Content', 'wp-epu' ); ?></strong></p>
            <p class="description"><?php _e( 'Include a suitable message to coax users to engage with your popup. Ideally keep this message concise.', 'wp-epu' ); ?></p>
            <textarea name="coax_text" class="regular"><?php if ($coax_text) { echo $coax_text; } ?></textarea>

            <div class="flex">
                <div class="half-width">
                    <p><strong><?php _e( 'Colour', 'wp-epu' ); ?></strong></p>
                    <input type="text" name="coax_text_colour" value="<?php if($coax_text_colour) { echo $coax_text_colour; } ?>" maxlength="7" class="colour-picker" />
                </div>

                <div class="half-width">
                    <p><strong><?php _e( 'Size', 'wp-epu' ); ?></strong></p>
                    <input type="number" name="coax_text_size" value="<?php if($coax_text_size) { echo $coax_text_size; } ?>" min=".1" step=".1" class="half-width" /> <?php _e( 'em', 'wp-epu' ); ?>
                </div>
            </div>

            <p><strong><?php _e( 'Rejection Text', 'wp-epu' ); ?></strong> <span class="pro"><?php _e( 'PRO', 'wp-epu' ); ?></span></p>
            <input type="text" placeholder="<?php _e( 'No thanks', 'wp-epu' ); ?>" class="full-width" maxlength="100" disabled />

        </div>

        <div class="epu-padder third-width">
            <h2><?php _e( 'Background Image', 'wp-epu' ); ?></h2>
            <p><strong><?php _e( 'Use a Background Image', 'wp-epu' ); ?></strong></p>
            <p class="description"><?php _e( 'Optionally include a background image for the popup.', 'wp-epu' ); ?></p>
            <input type="text" name="bg_image" id="bg_image" value="<?php if($bg_image) { echo $bg_image; } ?>" class="browse" />
            <input type="button" name="bg-image-upload-button" class="media-button button button-secondary bg-image-upload-button" value="<?php _e( 'Browse', 'wp-epu' ); ?>" />

            <div class="flex">

                <div class="half-width">
                    <span class="thumb-container bg-image-thumb-container">
                        <?php if($bg_image) {
                            $image_id = attachment_url_to_postid($bg_image);
                        ?>    
                            <span class="dashicons dashicons-dismiss remove-bg-image" title="<?php _e( 'Remove image', 'wp-epu' ); ?>"></span>
                            <img src="<?php echo wp_get_attachment_image_src($image_id, 'medium')[0]; ?>" class="bg-image-thumb" />
                        
                        <?php } else { ?>
                            <img src="<?php echo plugin_dir_url( __FILE__ ); ?>images/clear.svg" class="bg-image-thumb" />
                        <?php } ?>
                    </span>
                </div>

                <script>
                    jQuery(function($) {
                        jQuery('.bg-image-upload-button').click(function(e) {
                            e.preventDefault();
                            var image = wp.media({ 
                                title: 'Browse',
                                multiple: false
                            }).open()
                            .on('select', function(e) {
                                var uploaded_image = image.state().get('selection').first();
                                console.log(uploaded_image);
                                var bg_image = uploaded_image.toJSON().url;
                                var image_id = uploaded_image.toJSON().id;
                                jQuery('#bg_image').val(bg_image);
                                jQuery('.bg-image-thumb').attr('src', bg_image);
                                jQuery('.epu-image-container img').attr('srcset',bg_image);
                                jQuery('.dashicons-admin-customizer').attr('href','<?php echo admin_url();?>post.php?post=' + image_id + '&action=edit&image-editor');
                            });
                        });
                    }); 
                    </script>

                <div class="half-width">
                    <p class="bg-positions">
                        <label <?php if($bg_position == 'top left') { echo 'class="selected"'; } ?> title="<?php _e( 'Top Left', 'wp-epu' ); ?>"><input type="radio" name="bg_position" value="top left" <?php if($bg_position == 'top left') { echo 'checked'; } ?> class="radio" /><img src="<?php echo plugin_dir_url( __FILE__ ); ?>images/arrow.svg" /></label>
                        <label <?php if($bg_position == 'top center') { echo 'class="selected"'; } ?> title="<?php _e( 'Top Centre', 'wp-epu' ); ?>"><input type="radio" name="bg_position" value="top center" <?php if($bg_position == 'top center') { echo 'checked'; } ?> class="radio" /><img src="<?php echo plugin_dir_url( __FILE__ ); ?>images/arrow.svg" /></label>
                        <label <?php if($bg_position == 'top right') { echo 'class="selected"'; } ?> title="<?php _e( 'Top Right', 'wp-epu' ); ?>"><input type="radio" name="bg_position" value="top right" <?php if($bg_position == 'top right') { echo 'checked'; } ?> class="radio" /><img src="<?php echo plugin_dir_url( __FILE__ ); ?>images/arrow.svg" /></label>
                        <label <?php if($bg_position == 'center left') { echo 'class="selected"'; } ?> title="<?php _e( 'Centre Left', 'wp-epu' ); ?>"><input type="radio" name="bg_position" value="center left" <?php if($bg_position == 'middle left') { echo 'checked'; } ?> class="radio" /><img src="<?php echo plugin_dir_url( __FILE__ ); ?>images/arrow.svg" /></label>
                        <label <?php if($bg_position == 'center') { echo 'class="selected"'; } ?> title="<?php _e( 'Centre', 'wp-epu' ); ?>"><input type="radio" name="bg_position" value="center" <?php if($bg_position == 'center') { echo 'checked'; } ?> class="radio" /><img src="<?php echo plugin_dir_url( __FILE__ ); ?>images/circle.svg" /></label>
                        <label <?php if($bg_position == 'center right') { echo 'class="selected"'; } ?> title="<?php _e( 'Centre Right', 'wp-epu' ); ?>"><input type="radio" name="bg_position" value="center right" <?php if($bg_position == 'middle right') { echo 'checked'; } ?> class="radio" /><img src="<?php echo plugin_dir_url( __FILE__ ); ?>images/arrow.svg" /></label>
                        <label <?php if($bg_position == 'bottom left') { echo 'class="selected"'; } ?> title="<?php _e( 'Bottom Left', 'wp-epu' ); ?>"><input type="radio" name="bg_position" value="bottom left" <?php if($bg_position == 'bottom left') { echo 'checked'; } ?> class="radio" /><img src="<?php echo plugin_dir_url( __FILE__ ); ?>images/arrow.svg" /></label>
                        <label <?php if($bg_position == 'bottom center') { echo 'class="selected"'; } ?> title="<?php _e( 'Bottom Centre', 'wp-epu' ); ?>"><input type="radio" name="bg_position" value="bottom center" <?php if($bg_position == 'bottom center') { echo 'checked'; } ?> class="radio" /><img src="<?php echo plugin_dir_url( __FILE__ ); ?>images/arrow.svg" /></label>
                        <label <?php if($bg_position == 'bottom right') { echo 'class="selected"'; } ?> title="<?php _e( 'Bottom Right', 'wp-epu' ); ?>"><input type="radio" name="bg_position" value="bottom right" <?php if($bg_position == 'bottom right') { echo 'checked'; } ?> class="radio" /><img src="<?php echo plugin_dir_url( __FILE__ ); ?>images/arrow.svg" /></label>
                    </p>
                </div>
            </div>

            <p><strong><?php _e( 'Opacity', 'wp-epu' ); ?></strong></p>
            <select name="bg_image_opacity">
                <option value="1"<?php if($bg_image_opacity =='1') { echo ' selected'; } ?>>none</option>
                <option value=".1"<?php if($bg_image_opacity =='.1') { echo ' selected'; } ?>>.1</option>
                <option value=".2"<?php if($bg_image_opacity =='.2') { echo ' selected'; } ?>>.2</option>
                <option value=".3"<?php if($bg_image_opacity =='.3') { echo ' selected'; } ?>>.3</option>
                <option value=".4"<?php if($bg_image_opacity =='.4') { echo ' selected'; } ?>>.4</option>
                <option value=".5"<?php if($bg_image_opacity =='.5') { echo ' selected'; } ?>>.5</option>
                <option value=".6"<?php if($bg_image_opacity =='.6') { echo ' selected'; } ?>>.6</option>
                <option value=".7"<?php if($bg_image_opacity =='.7') { echo ' selected'; } ?>>.7</option>
                <option value=".8"<?php if($bg_image_opacity =='.8') { echo ' selected'; } ?>>.8</option>
                <option value=".9"<?php if($bg_image_opacity =='.9') { echo ' selected'; } ?>>.9</option>
            </select>

        </div>

        <div class="epu-padder third-width">
            <h2><?php _e( 'Supporting image', 'wp-epu' ); ?></h2>
            <p><strong><?php _e( 'Include a Supporting Image', 'wp-epu' ); ?></strong> <span class="pro"><?php _e( 'PRO', 'wp-epu' ); ?></span></p>
            <p class="description"><?php _e( 'Optionally include a supporting image for the popup.', 'wp-epu' ); ?></p>
            <input type="text" class="browse" disabled />
            <input type="button" name="promo-image-upload-button" id="promo-image-upload-button" class="media-button button button-secondary" value="<?php _e( 'Browse', 'wp-epu' ); ?>">

            <p><strong><?php _e( 'Position', 'wp-epu' ); ?></strong> <span class="pro"><?php _e( 'PRO', 'wp-epu' ); ?></span></p>
            <p>
                <label><input type="radio" name="pi_position" class="radio" disabled /><?php _e( 'Before heading (default)', 'wp-epu' ); ?> </label>
                <label><input type="radio" name="pi_position" class="radio" disabled /><?php _e( 'After heading', 'wp-epu' ); ?> </label>
                <label><input type="radio" name="pi_position" class="radio" disabled /><?php _e( 'After coax text', 'wp-epu' ); ?> </label>
            </p>
            
            <p><strong><?php _e( 'Width', 'wp-epu' ); ?></strong> <span class="pro"><?php _e( 'PRO', 'wp-epu' ); ?></span></p>
            <p>
                <input type="number" min="0" max="100" step="1" class="quarter-width" disabled /> <?php _e( '%', 'wp-epu' ); ?>
            </p>
        </div>

        <div class="epu-padder third-width">

            <h2><?php _e( 'Popup Box', 'wp-epu' ); ?></h2>            

            <div class="flex">
                <div class="half-width">
                    <p><strong><?php _e( 'Background Colour', 'wp-epu' ); ?></strong></p>
                    <input type="text" name="popup_bg_colour" value="<?php if($popup_bg_colour) { echo $popup_bg_colour; } ?>" maxlength="7" class="colour-picker" />
                </div>

                <div class="half-width">
                    <p><strong><?php _e( 'Box Shadow Colour', 'wp-epu' ); ?></strong></p>
                    <input type="text" name="popup_bs_colour" value="<?php if($popup_bs_colour) { echo $popup_bs_colour; } ?>" maxlength="7" class="colour-picker" />
                </div>
            </div>

            <p><strong><?php _e( 'Width', 'wp-epu' ); ?></strong></p>
            <input type="number" name="popup_width" value="<?php if($popup_width) { echo $popup_width; } ?>" placeholder="960" class="quarter-width popup-width" />
            <select name="popup_unit" class="popup-unit">
                <option value=""></option>
                <option value="px"<?php if($popup_unit =='px') { echo ' selected'; } ?>>px</option>
                <option value="%"<?php if($popup_unit =='%') { echo ' selected'; } ?>>%</option>
                <option value="vw"<?php if($popup_unit =='vw') { echo ' selected'; } ?>>vw</option>
            </select> <?php _e( '(width is ignored on fullscreen popus)', 'wp-epu' ); ?>

            <div class="flex">

                <div class="half-width">
                    <p><strong><?php _e( 'Padding', 'wp-epu' ); ?></strong></p>
                    <input type="number" name="popup_padding" value="<?php if($popup_padding) { echo $popup_padding; } ?>" placeholder="100" class="half-width popup-padding" min="1" /> <?php _e( 'px', 'wp-epu' ); ?>
                </div>

                <div class="half-width">
                    <p><strong><?php _e( 'Corner Radius', 'wp-epu' ); ?></strong></p>
                    <input type="number" name="corner_radius" value="<?php if($corner_radius) { echo $corner_radius; } ?>" placeholder="0" class="half-width popup-padding" min="0" /> <?php _e( 'px', 'wp-epu' ); ?>
                </div>

            </div>
  
        </div>

        <div class="epu-padder third-width">

            <h2><?php _e( 'Modal Mask', 'wp-epu' ); ?></h2>
            <p><strong><?php _e( 'Colour', 'wp-epu' ); ?></strong></p>
            <input type="text" name="modal_mask_colour" id="modal_mask_colour" value="<?php if($modal_mask_colour) { echo $modal_mask_colour; } ?>" maxlength="7"  class="colour-picker" />

            <p><strong><?php _e( 'Opacity', 'wp-epu' ); ?></strong></p>
            <select name="modal_opacity">
                <option value="1"<?php if($modal_opacity =='1') { echo ' selected'; } ?>>none</option>
                <option value=".1"<?php if($modal_opacity =='.1') { echo ' selected'; } ?>>.1</option>
                <option value=".2"<?php if($modal_opacity =='.2') { echo ' selected'; } ?>>.2</option>
                <option value=".3"<?php if($modal_opacity =='.3') { echo ' selected'; } ?>>.3</option>
                <option value=".4"<?php if($modal_opacity =='.4') { echo ' selected'; } ?>>.4</option>
                <option value=".5"<?php if($modal_opacity =='.5') { echo ' selected'; } ?>>.5</option>
                <option value=".6"<?php if($modal_opacity =='.6') { echo ' selected'; } ?>>.6</option>
                <option value=".7"<?php if($modal_opacity =='.7') { echo ' selected'; } ?>>.7</option>
                <option value=".8"<?php if($modal_opacity =='.8') { echo ' selected'; } ?>>.8</option>
                <option value=".9"<?php if($modal_opacity =='.9') { echo ' selected'; } ?>>.9</option>
            </select>

            <p><input name="dismissible" type="checkbox" value="1" <?php if( $dismissible == true ) { ?>checked="checked"<?php } ?> /> <?php _e( 'Modal mask can also be clicked to dismiss the popup', 'wp-epu' ); ?></p>

        </div>

        <div class="epu-padder third-width">

            <h2><?php _e( 'Close button', 'wp-epu' ); ?></h2>
            <p><strong><?php _e( 'Colour', 'wp-epu' ); ?></strong></p>
            <input type="text" name="close_button_colour" value="<?php if($close_button_colour) { echo $close_button_colour; } ?>" maxlength="7" class="colour-picker" />

            <div class="flex">
                <div class="half-width">
                    <p><strong><?php _e( 'Style', 'wp-epu' ); ?></strong></p>
                    <p>
                        <label><input type="radio" name="close_style" value="cross" <?php if($close_style == 'cross') { echo 'checked'; } ?> class="radio" /><?php _e( 'Cross (default)', 'wp-epu' ); ?> </label>
                        <label><input type="radio" name="close_style" value="cross-circle" <?php if($close_style == 'cross-circle') { echo 'checked'; } ?> class="radio" /><?php _e( 'Cross in circle', 'wp-epu' ); ?> </label>
                        <label><input type="radio" name="close_style" value="cross-square" <?php if($close_style == 'cross-square') { echo 'checked'; } ?> class="radio" /><?php _e( 'Cross in square', 'wp-epu' ); ?> </label>
                    </p>
                </div>

                <div class="half-width">
                    <p><strong><?php _e( 'Position', 'wp-epu' ); ?></strong></p>
                    <p>
                        <label><input type="radio" name="close_position" value="right" <?php if($close_position == 'right') { echo 'checked'; } ?> class="radio" /><?php _e( 'Top right (default)', 'wp-epu' ); ?> </label>
                        <label><input type="radio" name="close_position" value="left" <?php if($close_position == 'left') { echo 'checked'; } ?> class="radio" /><?php _e( 'Top left', 'wp-epu' ); ?> </label>
                        <label><input type="radio" name="close_position" value="0" <?php if($close_position == '0') { echo 'checked'; } ?> class="radio" /><?php _e( 'Hide', 'wp-epu' ); ?> </label>
                    </p>
                </div>
            </div>

        </div>


        <div class="epu-padder third-width interaction">

            <h2><?php _e( 'Interaction', 'wp-epu' ); ?></h2>
            <p><strong><?php _e( 'Call to Action', 'wp-epu' ); ?></strong></p>

            <?php if ( is_plugin_active( 'gravityforms/gravityforms.php' ) ) {
                $form_plugin = __('Gravity Form', 'wp-epu');
            } else if ( is_plugin_active( 'contact-form-7/wp-contact-form-7.php' ) ) {
                $form_plugin = __('Contact Form', 'wp-epu');
            }  else if ( is_plugin_active( 'wpforms-lite/wpforms.php' ) ) {
                $form_plugin = __('WP Form', 'wp-epu');
            } else if ( is_plugin_active( 'formidable/formidable.php' ) ) {
                $form_plugin = __('Formidable Form', 'wp-epu');
            } else if ( is_plugin_active( 'ninja-forms/ninja-forms.php' ) ) {
                $form_plugin = __('Ninja Form', 'wp-epu');
            }
            ?>

            <p>
                <?php if(isset($form_plugin)) { /* If a form plugin exists */ ?>
                <label><input type="radio" name="interaction" value="form" <?php if($interaction == 'form') { echo 'checked'; } ?> class="radio form" /><?php echo $form_plugin; ?> </label>
                <?php } ?>
                <label><input type="radio" name="interaction" value="url" <?php if($interaction == 'url') { echo 'checked'; } ?> class="radio url" /><?php _e( 'URL', 'wp-epu' ); ?> </label>
                <label><input type="radio" name="interaction" value="yes_or_no" <?php if($interaction == 'yes_or_no') { echo 'checked'; } ?> class="radio yes_or_no" /><?php _e( 'Choose Yes or No', 'wp-epu' ); ?> </label>
                <label><input type="radio" name="interaction" value="shortcode" <?php if($interaction == 'shortcode') { echo 'checked'; } ?> class="radio shortcode" /><?php _e( 'Shortcode', 'wp-epu' ); ?> </label>
                <label><input type="radio" name="interaction" value="offer_code" <?php if($interaction == 'offer_code') { echo 'checked'; } ?> class="radio offer_code" /><?php _e( 'Offer Code', 'wp-epu' ); ?> </label>
            </p>

            <?php /* If Gravity Forms is activated */ 
            if ( is_plugin_active( 'gravityforms/gravityforms.php' ) ) { ?>
                <select name="gr_form_id" id="form_selection" class="hide form-titles full-width <?php if($interaction == 'form') { echo 'show'; } ?>">
                    <?php
                        $forms = GFAPI::get_forms();
                        foreach ( $forms as $form) { ?>
                            <option value="<?php echo $form['id']; ?>" <?php if($form['id'] == $gr_form_id ) { echo ' selected'; } ?>><?php echo $form['title']; ?></option>
                        <?php }
                    ?>
                </select>

                <?php if($interaction == 'form') { ?>
                    <ul class="form-options form-links show">
                        <li><a href="<?php echo admin_url(); ?>admin.php?page=gf_edit_forms&id=<?php echo $gr_form_id; ?>" target="_blank"><?php _e( 'Edit Form', 'wp-epu' ); ?></a></li>
                        <li><a href="<?php echo admin_url(); ?>admin.php?page=gf_edit_forms&view=settings&subview=settings&id=<?php echo $gr_form_id; ?>" target="_blank"><?php _e('Form Settings', 'wp-epu'); ?></a></li>
                        <li>
                            <?php 
                            $entry_count = RGFormsModel::get_form_counts($gr_form_id);
                            $entry_total =   __('View', 'wp-epu') . ' ' . $entry_count['total']; ?>
                                <a href="<?php echo admin_url(); ?>admin.php?page=gf_entries&id=<?php echo $gr_form_id; ?>" target="_blank">  <?php echo $entry_total; ?>
                            <?php if($entry_total > 1 || $entry_total == 0) {
                                _e('Entries', 'wp-epu');
                            } else if($entry_total == 1) {
                                _e('Entry', 'wp-epu');
                            } ?>
                            </a>
                        </li>
                    </ul>
                <?php } ?>
                
                <div class="form-options <?php if($interaction == 'form') { echo 'show'; } ?>">
                    <p><strong><?php _e( 'Form Options', 'wp-epu' ); ?></strong></p>
                    <label>
                        <input name="gr_form_title" type="checkbox" value="1" <?php if( $gr_form_title == true ) { ?>checked="checked"<?php } ?> /> <?php _e( 'Show the form title', 'wp-epu' ); ?>
                    </label>
                        
                    <label>
                        <input name="gr_form_description" type="checkbox" value="1" <?php if( $gr_form_description == true ) { ?>checked="checked"<?php } ?> /> <?php _e( 'Show the form description', 'wp-epu' ); ?>
                    </label>

                    <label>
                        <input name="gr_form_ajax" type="checkbox" value="1" <?php if( $gr_form_ajax == true ) { ?>checked="checked"<?php } ?> /> <?php _e( 'Use AJAX (recommended)', 'wp-epu' ); ?>
                    </label>
                </div>
            <?php }
             /* End if Gravity Forms is activated */ ?>

            <?php /* If Contact Form 7 is activated */ 
            if ( is_plugin_active( 'contact-form-7/wp-contact-form-7.php' ) ) { ?>
            <select name="cf_form_id" id="form_selection" class="hide form-titles full-width <?php if($interaction == 'form') { echo 'show'; } ?>">
                <?php $args = array(
                    'post_type'         => 'wpcf7_contact_form',
                    'orderby'           => 'title',
                    'order'             => 'asc',
                    'posts_per_page'    => -1
                );
                $query = new WP_Query($args);
                while ($query->have_posts()) : $query->the_post(); ?>
                    <option value="<?php echo get_the_ID(); ?>" <?php if(get_the_ID() == $cf_form_id) { echo ' selected'; } ?>><?php echo the_title(); ?></option>
                <?php endwhile;
                wp_reset_postdata();
                ?>
            </select>

            <div class="form-options <?php if($interaction == 'form') { echo 'show'; } ?>">
                <p><strong><?php _e( 'Form Options', 'wp-epu' ); ?></strong></p>
                <label>
                    <input name="cf_form_title" type="checkbox" value="1" <?php if( $cf_form_title == true ) { ?>checked="checked"<?php } ?> /> <?php _e( 'Show the form title', 'wp-epu' ); ?>
                </label>
            </div>
            <?php }
            /* End if Contact Form 7 is activated */ 

            /* If WP Form Lite is activated */ 
            else if ( is_plugin_active( 'wpforms-lite/wpforms.php' ) ) {?>
                <select name="wpf_form_id" id="form_selection" class="hide form-titles full-width <?php if($interaction == 'form') { echo 'show'; } ?>">
                    <?php $args = array(
                        'post_type'         => 'wpforms',
                        'orderby'           => 'title',
                        'order'             => 'asc',
                        'posts_per_page'    => -1
                    );
                    $query = new WP_Query($args);
                    while ($query->have_posts()) : $query->the_post(); ?>
                        <option value="<?php echo get_the_ID(); ?>" <?php if(get_the_ID() == $wpf_form_id ) { echo ' selected'; } ?>><?php echo the_title(); ?></option>
                    <?php endwhile;
                    wp_reset_postdata();
                    ?>
                </select>
                
                <div class="form-options <?php if($interaction == 'form') { echo 'show'; } ?>">
                    <p><strong><?php _e( 'Form Options', 'wp-epu' ); ?></strong></p>
                    <label>
                        <input name="wpf_form_title" type="checkbox" value="1" <?php if( $wpf_form_title == true ) { ?>checked="checked"<?php } ?> /> <?php _e( 'Show the form title', 'wp-epu' ); ?>
                    </label>
                        
                    <label>
                        <input name="wpf_form_description" type="checkbox" value="1" <?php if( $wpf_form_description == true ) { ?>checked="checked"<?php } ?> /> <?php _e( 'Show the form description', 'wp-epu' ); ?>
                    </label>
                </div>
            <?php 
            } else /* End if WP Form Lite is activated */

            /* If Formidable is activated */ 
            if ( is_plugin_active( 'formidable/formidable.php' ) ) {
                global $wpdb;
                $formidable_table  = $wpdb->prefix . 'frm_forms';
                
                $form_items = $wpdb->get_results("
                    SELECT * 
                    FROM $formidable_table
                    ORDER BY id DESC
                "); ?>
                <select name="formidable_form_id" id="form_selection" class="hide form-titles full-width <?php if($interaction == 'form') { echo 'show'; } ?>">
                    <?php foreach ( $form_items as $form_item ) {
                        $formidable_id      = $form_item->id;
                        $formidable_name    = $form_item->name;
                    ?>
                        <option value="<?php echo $formidable_id; ?>"<?php if($formidable_id == $formidable_form_id ) { echo ' selected'; } ?>><?php echo $formidable_name; ?></option>
                    <?php } ?>
                </select>

                <div class="form-options <?php if($interaction == 'form') { echo 'show'; } ?>">
                    <p><strong><?php _e( 'Form Options', 'wp-epu' ); ?></strong></p>
                    <label>
                        <input name="formidable_form_title" type="checkbox" value="1" <?php if( $formidable_form_title == true ) { ?>checked="checked"<?php } ?> /> <?php _e( 'Show the form title', 'wp-epu' ); ?>
                    </label>
                        
                    <label>
                        <input name="formidable_form_description" type="checkbox" value="1" <?php if( $formidable_form_description == true ) { ?>checked="checked"<?php } ?> /> <?php _e( 'Show the form description', 'wp-epu' ); ?>
                    </label>
                </div>
                
            <?php } 
            /* End if Formidable is activated */ 

                /* If Ninja Forms is activated */ 
            if ( is_plugin_active( 'ninja-forms/ninja-forms.php' ) ) {
                global $wpdb;
                $ninja_forms_table  = $wpdb->prefix . 'nf3_forms';
                
                $form_items = $wpdb->get_results("
                    SELECT * 
                    FROM $ninja_forms_table
                    ORDER BY id DESC
                "); ?>
                <select name="nf_form_id" id="form_selection" class="hide form-titles full-width <?php if($interaction == 'form') { echo 'show'; } ?>">
                    <?php foreach ( $form_items as $form_item ) {
                        $nf_id      = $form_item->id;
                        $nf_title   = $form_item->title;
                    ?>
                        <option value="<?php echo $nf_id; ?>"<?php if($nf_id == $nf_form_id ) { echo ' selected'; } ?>><?php echo $nf_title; ?></option>
                    <?php } ?>
                </select>
                
            <?php }
            /* End if Ninja Forms is activated */ 
            ?>

            <div class="hide shortcode <?php if($interaction == 'shortcode') { echo 'show'; } ?>">
                <p><strong><?php _e( 'Enter shortcode', 'wp-epu' ); ?></strong></p>
                <input name="shortcode" type="text" value="<?php if($shortcode) { echo esc_html(stripslashes($shortcode)); } ?>" placeholder="" class="shortcode-field full-width" />
            </div>

            <div class="hide url <?php if($interaction == 'url') { echo 'show'; } ?>">
                
                <p><strong><?php _e( 'Button text and URL', 'wp-epu' ); ?></strong></p>

                <input name="full_url_text" type="text" value="<?php if($full_url_text) { echo $full_url_text; } ?>" placeholder="<?php _e( 'Button text', 'wp-epu' ); ?>" class="url-field full-width" />

                <input name="full_url" type="text" value="<?php if($full_url) { echo $full_url; } ?>" placeholder="<?php _e( 'Button URL', 'wp-epu' ); ?>" class="url-field full-width" />

                <div class="flex">
                    <div class="half-width">
                        <p><strong><?php _e( 'Button Colour', 'wp-epu' ); ?></strong></p>
                        <input type="text" name="url_button_colour" value="<?php if($url_button_colour) { echo $url_button_colour; } ?>" maxlength="7" class="colour-picker" />
                    </div>
                    <div class="half-width">
                        <p><strong><?php _e( 'Button Text Colour', 'wp-epu' ); ?></strong></p>
                        <input type="text" name="url_text_colour" value="<?php if($url_text_colour) { echo $url_text_colour; } ?>" maxlength="7" class="colour-picker" />
                    </div>
                </div>
            </div>

            <div class="hide yes <?php if($interaction == 'yes_or_no') { echo 'show'; } ?>">
                <p><strong><?php _e( 'URL for YES', 'wp-epu' ); ?></strong></p>
                <input name="yes_url" type="text" value="<?php if($yes_url) { echo $yes_url; } ?>" placeholder="<?php echo home_url(); ?>/yes" class="url-field full-width" />
            </div>

            <div class="hide no <?php if($interaction == 'yes_or_no') { echo 'show'; } ?>">
                <p><strong><?php _e( 'URL for NO', 'wp-epu' ); ?></strong></p>
                <input name="no_url" type="text" value="<?php if($no_url) { echo $no_url; } ?>" placeholder="<?php echo home_url(); ?>/no" class="url-field full-width" />
            </div>

            <div class="hide offer-code <?php if($interaction == 'offer_code') { echo 'show'; } ?>">
                <p><strong><?php _e( 'Enter an offer code', 'wp-epu' ); ?></strong></p>
                <input name="offer_code" type="text" value="<?php if($offer_code) { echo $offer_code; } ?>" class="full-width" />
            </div>

        </div>

        <div class="epu-padder third-width">

            <h2><?php _e( 'Lifespan', 'wp-epu' ); ?></h2>
            <p><strong><?php _e( 'Reinstatement', 'wp-epu' ); ?></strong></p>
            <p class="description"><?php _e( 'This is how long it will take for the popup to be reinstated after the user dismissed it. If not specified (or 0) the popup will return every 5 days.', 'wp-epu' ); ?></p>
            <input type="number" name="cookie_expiry" id="cookie_expiry" value="<?php if($cookie_expiry) { echo $cookie_expiry; } ?>" min=0 placeholder="5" class="quarter-width" /> <?php _e( 'days (default is 5)', 'wp-epu' ); ?>
            
            <p><strong><?php _e( 'Expiry', 'wp-epu' ); ?></strong> <span class="pro"><?php _e( 'PRO', 'wp-epu' ); ?></span></p>

            <p class="description"><?php _e( 'If the popup needs to be removed at some point in the future, set an expiry date here.', 'wp-epu' ); ?></p>

            <input type="date" class="half-width" disabled />

        </div>

        <div class="epu-padder third-width mobile">
  
            <h2><?php _e( 'Mobile', 'wp-epu' ); ?></h2>          
            
            <p><strong><?php _e( 'Mobile Behaviour', 'wp-epu' ); ?></strong></p>
            <p class="description"><?php _e( 'Popups behave differently on mobile devices. You will have to decide what event will cause the popup to appear.', 'wp-epu' ); ?></p>
            <p>
                <label><input type="radio" name="mobile_behaviour" value="scrollup" checked class="radio radio-behaviour" /><?php _e( 'On scroll up (default)', 'wp-epu' ); ?> </label>
                <label><input type="radio" name="mobile_behaviour" class="radio radio-behaviour" disabled /><?php _e( 'Touch anywhere on page', 'wp-epu' ); ?> <span class="pro"><?php _e( 'PRO', 'wp-epu' ); ?></span></label>
                <label><input type="radio" name="mobile_behaviour" class="radio radio-behaviour specified" disabled /><?php _e( 'After specified time', 'wp-epu' ); ?> <span class="pro"><?php _e( 'PRO', 'wp-epu' ); ?></span></label>
            </p>

        </div>

        <div class="epu-padder third-width">

            <?php
                if (isset($_GET['post_type'])) {
                    $index = $_GET['post_type'];
                } else if (isset($_GET['post'])) {
                    $index = $_GET['post'];
                }
            ?>

            <h2><?php _e( 'Status', 'wp-epu' ); ?></h2>
            <p><strong><?php _e( 'Make this the Active Popup', 'wp-epu' ); ?></strong></p>
            <p class="description"><?php _e( 'Only one popup can be active at any given time. When you make this popup active, any current popups will become inactive.', 'wp-epu' ); ?></p>
            <p><input name="active_popup" type="checkbox" value="<?php if( $active_popup == $index ) { echo $index; } ?>" <?php if( $active_popup == $index ) { ?>checked="checked"<?php } ?> /> <?php _e( 'Make active', 'wp-epu' ); ?></p>
            
            <p><strong><?php _e( 'Force Immediate Popup', 'wp-epu' ); ?></strong></p>
            <p class="description"><?php _e( 'Force the popup to show immediately. This will happen on both desktop and mobile devices.', 'wp-epu' ); ?></p>
            <p><input name="show_immediately" type="checkbox" value="1" <?php if( $show_immediately == true ) { ?>checked="checked"<?php } ?> /> <?php _e( 'Popup immediately', 'wp-epu' ); ?></p>
            
        </div>

        <div class="epu-padder third-width">

            <h2><?php _e( 'Testing', 'wp-epu' ); ?></h2>
        
            <p><strong><?php _e( 'Testing', 'wp-epu' ); ?></strong> <span class="pro"><?php _e( 'PRO', 'wp-epu' ); ?></span></p>

            <p>
                <p class="description"><?php _e( 'Test your popup design before going live.', 'wp-epu' ); ?></p>
                <a class="button" disabled><?php _e( 'Open Test Window', 'wp-epu' ); ?></a>
            </p>

        </div>

        <div class="epu-padder full-width">

            <h2><?php _e( 'Admin notes', 'wp-epu' ); ?></h2>
            <p><?php _e( 'Admin notes will only be shown here.', 'wp-epu' ); ?> <span class="pro"><?php _e( 'PRO', 'wp-epu' ); ?></span></p>
            <textarea class="regular admin-notes" rows="5" disabled></textarea>

        </div>
       
    <?php }

    function save_data($post_id) {

        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
            return;
            $nonce = isset($_POST['exit_popup_nounce']) ? $_POST['exit_popup_nounce'] : '';

            if ( !wp_verify_nonce( $nonce, plugin_basename( __FILE__ ) ) )
            return;

        /* Check permissions */
        if ( 'page' == $_POST['exit_popup_nounce'] ){
			if ( !current_user_can( 'edit_page', $post_id ) )
				return;
		} else {
			if ( !current_user_can( 'edit_post', $post_id ) )
				return;
		}

        /* Save post meta */
        
        if(isset($_POST['layout']))
        update_post_meta( $post_id, 'layout', sanitize_text_field( $_POST['layout']));

        if(isset($_POST['bg_image']))
        update_post_meta( $post_id, 'bg_image', sanitize_text_field( $_POST['bg_image']));

        if(isset($_POST['bg_position']))
        update_post_meta( $post_id, 'bg_position', sanitize_text_field( $_POST['bg_position']));

        if(isset($_POST['close_button_colour']))
        update_post_meta( $post_id, 'close_button_colour', sanitize_hex_color( $_POST['close_button_colour']));

        if(isset($_POST['coax_text']))
        update_post_meta( $post_id, 'coax_text', sanitize_text_field( $_POST['coax_text']));

        if(isset($_POST['coax_text_colour']))
        update_post_meta( $post_id, 'coax_text_colour', sanitize_hex_color( $_POST['coax_text_colour']));

        if(isset($_POST['coax_text_size']))
        update_post_meta( $post_id, 'coax_text_size', sanitize_text_field( $_POST['coax_text_size']));

        if(isset($_POST['heading_text_colour']))
        update_post_meta( $post_id, 'heading_text_colour', sanitize_hex_color( $_POST['heading_text_colour']));

        if(isset($_POST['heading_text_size']))
        update_post_meta( $post_id, 'heading_text_size', sanitize_text_field( $_POST['heading_text_size']));

        if(isset($_POST['popup_bg_colour']))
        update_post_meta( $post_id, 'popup_bg_colour', sanitize_hex_color( $_POST['popup_bg_colour']));

        if(isset($_POST['popup_bs_colour']))
        update_post_meta( $post_id, 'popup_bs_colour', sanitize_hex_color( $_POST['popup_bs_colour']));

        if(isset($_POST['popup_width']))
        update_post_meta( $post_id, 'popup_width', sanitize_text_field( (int)$_POST['popup_width']));

        if(isset($_POST['popup_unit']))
        update_post_meta( $post_id, 'popup_unit', sanitize_text_field( $_POST['popup_unit']));

        if(isset($_POST['popup_padding']))
        update_post_meta( $post_id, 'popup_padding', sanitize_text_field( (int)$_POST['popup_padding']));

        if(isset($_POST['corner_radius']))
        update_post_meta( $post_id, 'corner_radius', sanitize_text_field( (int)$_POST['corner_radius']));

        if(isset($_POST['modal_mask_colour']))
        update_post_meta( $post_id, 'modal_mask_colour', sanitize_hex_color( $_POST['modal_mask_colour']));

        if(isset($_POST['modal_opacity']))
        update_post_meta( $post_id, 'modal_opacity', sanitize_text_field( $_POST['modal_opacity']));

        if(isset($_POST['bg_image_opacity']))
        update_post_meta( $post_id, 'bg_image_opacity', sanitize_text_field( $_POST['bg_image_opacity']));

        if(isset($_POST['gr_form_id']))
        update_post_meta( $post_id, 'gr_form_id', sanitize_text_field( (int)$_POST['gr_form_id']));

        if(isset($_POST['gr_form_title']))
        update_post_meta( $post_id, 'gr_form_title', sanitize_text_field( $_POST['gr_form_title']));

        if(isset($_POST['gr_form_description']))
        update_post_meta( $post_id, 'gr_form_description', sanitize_text_field( $_POST['gr_form_description']));

        if(isset($_POST['gr_form_ajax']))
        update_post_meta( $post_id, 'gr_form_ajax', sanitize_text_field( $_POST['gr_form_ajax']));

        if(isset($_POST['cf_form_id']))
        update_post_meta( $post_id, 'cf_form_id', sanitize_text_field( (int)$_POST['cf_form_id']));

        if(isset($_POST['cf_form_title']))
        update_post_meta( $post_id, 'cf_form_title', sanitize_text_field( $_POST['cf_form_title']));

        if(isset($_POST['wpf_form_id']))
        update_post_meta( $post_id, 'wpf_form_id', sanitize_text_field( (int)$_POST['wpf_form_id']));

        if(isset($_POST['wpf_form_title']))
        update_post_meta( $post_id, 'wpf_form_title', sanitize_text_field( $_POST['wpf_form_title']));

        if(isset($_POST['wpf_form_description']))
        update_post_meta( $post_id, 'wpf_form_description', sanitize_text_field( $_POST['wpf_form_description']));

        if(isset($_POST['formidable_form_id']))
        update_post_meta( $post_id, 'formidable_form_id', sanitize_text_field( (int)$_POST['formidable_form_id']));

        if(isset($_POST['formidable_form_title']))
        update_post_meta( $post_id, 'formidable_form_title', sanitize_text_field( $_POST['formidable_form_title']));

        if(isset($_POST['formidable_form_description']))
        update_post_meta( $post_id, 'formidable_form_description', sanitize_text_field( $_POST['formidable_form_description']));

        if(isset($_POST['nf_form_id']))
        update_post_meta( $post_id, 'nf_form_id', sanitize_text_field( (int)$_POST['nf_form_id']));

        if(isset($_POST['yes_or_no']))
        update_post_meta( $post_id, 'yes_or_no', sanitize_text_field( $_POST['yes_or_no']));

        if(isset($_POST['yes_url']))
        update_post_meta( $post_id, 'yes_url', esc_url( $_POST['yes_url']));

        if(isset($_POST['no_url']))
        update_post_meta( $post_id, 'no_url', esc_url( $_POST['no_url']));

        if(isset($_POST['offer_code']))
        update_post_meta( $post_id, 'offer_code', sanitize_text_field( $_POST['offer_code']));

        if(isset($_POST['full_url']))
        update_post_meta( $post_id, 'full_url', esc_url( $_POST['full_url']));

        if(isset($_POST['full_url_text']))
        update_post_meta( $post_id, 'full_url_text', sanitize_text_field( $_POST['full_url_text']));

        if(isset($_POST['url_button_colour']))
        update_post_meta( $post_id, 'url_button_colour', sanitize_hex_color( $_POST['url_button_colour']));

        if(isset($_POST['url_text_colour']))
        update_post_meta( $post_id, 'url_text_colour', sanitize_hex_color( $_POST['url_text_colour']));

        if(isset($_POST['shortcode']))
        update_post_meta($post_id, 'shortcode', sanitize_text_field(wp_slash($_POST['shortcode'])));

        if(isset($_POST['cookie_expiry']))
        update_post_meta( $post_id, 'cookie_expiry', sanitize_text_field( (int)$_POST['cookie_expiry']));

        if(isset($_POST['close_style']))
        update_post_meta( $post_id, 'close_style', sanitize_text_field( $_POST['close_style']));

        if(isset($_POST['close_position']))
        update_post_meta( $post_id, 'close_position', sanitize_text_field( $_POST['close_position']));

        if(isset($_POST['interaction']))
        update_post_meta( $post_id, 'interaction', sanitize_text_field( $_POST['interaction']));

        if ( isset( $_POST['show_immediately'] ) || !isset( $_POST['show_immediately'] )) {
            $data = sanitize_text_field($_POST['show_immediately']);
            update_post_meta($post_id, 'show_immediately', $data, get_post_meta($post_id, 'show_immediately', TRUE));
        }
        if ( isset( $_POST['dismissible'] ) || !isset( $_POST['dismissible'] )) {
            $data = sanitize_text_field($_POST['dismissible']);
            update_post_meta($post_id, 'dismissible', $data, get_post_meta($post_id, 'dismissible', TRUE));
        }

        return $data;
    }
}
$wpepu_meta = new wpepu_meta;

/* Admin columns */
add_filter( 'manage_wpepu_exit_popup_posts_columns', 'set_custom_epu_columns' );
function set_custom_epu_columns($columns) {
    $columns['active_epu']      = __( 'Active', 'wp-epu' );
    return $columns;
}

add_action( 'manage_wpepu_exit_popup_posts_custom_column' , 'wpepu_column', 10, 2 );
function wpepu_column( $column, $post_id ) {
    $active_popup = get_option('epu_active_popup');

    switch ( $column ) {

        case 'active_epu' :
            if($active_popup == $post_id) { 
                echo '<span class="dashicons dashicons-star-filled" title="' . __('This popup is active', 'wp-epu') . '"></span>';
            }
        break;

        case 'the_date' :
            echo get_the_date();
        break;
    }
}

/* Change the order of the wpepu_exit_popup post type columns */
function wpepu_column_order ( $columns ) {

    unset( $columns['author'] );
    unset($columns['title']);
    unset($columns['active_epu']);
    unset($columns['date']); 
    
   return array_merge ( $columns, array ( 
        'title'         => __ ( 'Popup Title', 'wp-epu'),
        'active_epu'    => __ ( 'Active', 'wp-epu'),
        'author'        => __ ( 'Author', 'wp-epu'),
        'the_date'      => __ ( 'Date', 'wp-epu')
   ) );

 }
add_filter ( 'manage_wpepu_exit_popup_posts_columns', 'wpepu_column_order' );


/* Add data into options table if wpepu_exit_popup post is set to active  */
add_action('save_post','epu_post_callback');
function epu_post_callback($post_id) {
    
    $post_type = 'wpepu_exit_popup';
    global $post; 
    if (isset($post->post_type) != $post_type) {
        /* Don't do anything */
        return;
    }
    /* But do this stuff if post 'active_popup' was checked */
    if ( isset( $_POST['active_popup'] )) {

        /* Update exiting 'epu_active_popup' option if it exists */
        if(get_option('epu_active_popup')) {
            update_option('epu_active_popup', $post_id);
        } else {
        /* Otherwise, add a new option */
            add_option('epu_active_popup', $post_id);
        }

    } else {
        return;
    }
}

/* If test mode */
$popuptest = isset($_GET['popuptest']);

if($popuptest) {

    /* Output popup  TEST into front-end footer */
    add_action('wp_footer', 'epu_test_output', 10);
    function epu_test_output() {
        require_once('test-mode.php');
    }

} else {

    add_action('wp_footer', 'wpepu_output', 10);
    function wpepu_output() {

        require('inc/vars.php');
        
        /* If epu- and epu-gone- cookies do not exist */
        if(!isset($_COOKIE['epu-' . $epu_slug]) && !isset($_COOKIE['epu-gone-' . $epu_slug]) && $active_popup_id) { ?>

            <!--/ Start EPU Container /-->
            <div class="epu epu-<?php echo $active_popup_id; ?>" id="epu-<?php echo $epu_slug; ?>">

                <?php echo $close_button; ?>
                
                <!--/ Start EPU Container /-->
                <div class="epu-container">

                    <div class="epu-heading"><?php echo get_the_title($active_popup_id); ?></div>
                    
                    <?php if ($coax_text) {
                        echo '<div class="epu-coax-text">' . html_entity_decode($coax_text) . '</div>';
                    } ?>

                    <?php 
                        if($interaction == 'form') {
                            
                            if ( ! function_exists( 'is_plugin_active' ) ) {
                                require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
                            }
                            if ( is_plugin_active( 'gravityforms/gravityforms.php' ) ) {
                                gravity_form($gr_form_id , $gr_form_title, $gr_form_description, false, '', $gr_form_ajax, 999999); 
                            } else if ( is_plugin_active( 'contact-form-7/wp-contact-form-7.php' ) ) {
                                echo do_shortcode('[contact-form-7 id=' . $cf_form_id  . ' title=' . $cf_form_title . ']');
                            } else if ( is_plugin_active( 'wpforms-lite/wpforms.php' ) ) {
                                echo do_shortcode('[wpforms id=' . $wpf_form_id  . ' title=' . $wpf_form_title . ' description=' . $wpf_form_description . ']');
                            } else if ( is_plugin_active( 'formidable/formidable.php' ) ) {
                                echo FrmFormsController::get_form_shortcode( array( 'id' => $formidable_form_id, 'title' => $formidable_form_title, 'description' => $formidable_form_description ) );
                            } else if ( is_plugin_active( 'ninja-forms/ninja-forms.php' ) ) {
                                echo do_shortcode('[ninja_form id=' . $nf_form_id . ']');
                            }

                        } else if($interaction == 'url') { ?>
                            <a href="<?php echo $full_url; ?>" class="epu-button epu-url"><?php echo $full_url_text; ?></a>
                        <?php } else if($interaction == 'shortcode') {
                            echo do_shortcode(stripslashes($shortcode));
                        } else if($interaction == 'yes_or_no') { ?>
                            <a href="<?php echo $yes_url; ?>" class="epu-yes-no-button epu-yes-button"><?php _e( 'Yes', 'wp-epu' ); ?></a>
                            <a href="<?php echo $no_url; ?>" class="epu-yes-no-button epu-no-button"><?php _e( 'No', 'wp-epu' ); ?></a>
                        <?php } else if($interaction == 'offer_code') { ?>
                            <div class="epu-offer-code">
                                <input type="text" value="<?php echo $offer_code; ?>" id="the_offer_code" spellcheck="false" />
                                <div class="epu-copied"></div>
                            </div>
                            <script>
                                jQuery('.epu-offer-code input').click(function() {
                                    jQuery('.epu-copied').html('<span><?php _e( 'Copied!', 'wp-epu' ); ?></span>');
                                    jQuery(this).addClass('copied');
                                    document.execCommand('copy');
                                });
                                
                                var textBox = document.getElementById('the_offer_code');
                                textBox.onfocus = function() {
                                    textBox.select();

                                    // Work around Chrome's little problem
                                    textBox.onmouseup = function() {
                                        // Prevent further mouseup intervention
                                        textBox.onmouseup = null;
                                        return false;
                                    };
                                };
                            </script>
                        <?php }
                    ?>

                    <?php if($interaction != 'yes_or_no') { ?>
                    <div class="epu-rejection"><?php _e( 'No Thanks', 'wp-epu' ); ?></div>
                    <?php } ?>

                </div>
                <!--/ End EPU Container /-->

                <div class="background-image"></div>

            </div>
            <!--/ End EPU Container /-->

            <div class="epu-mask epu-click"></div>

            <script>
                jQuery('.epu-close,<?php if($dismissible) { echo ' .epu-mask,'; } ?> .epu-rejection, .epu-question').click(function() {
                    jQuery('.epu, .epu-mask').remove();
                    Cookies.set('epu-<?php echo $epu_slug; ?>', 'dismissed', { expires: <?php echo $cookie_expiry; ?> });
                });
                <?php if($show_immediately) { ?>
                jQuery(document).ready(function () {
                    jQuery('.epu, .epu-mask').css('display', 'block');
                });
                <?php } else { ?>


                <?php if(!wp_is_mobile()) { ?>

                    jQuery(document).mouseleave(function () {
                        jQuery('.epu, .epu-mask').css('display', 'block');
                    });
                    
                <?php } else if(wp_is_mobile()) { ?>
                
                    <?php if($mobile_behaviour == 'scrollup' || !$mobile_behaviour) {  /* Show on Scroll Up, or when no option is selected */ ?>

                        var position = jQuery(window).scrollTop(); 
                        jQuery(window).scroll(function() {
                            var scroll = jQuery(window).scrollTop();
                            if(scroll > position) {
                                //console.log('scrollDown');
                            } else {
                                //console.log('scrollUp');
                                jQuery('.epu, .epu-mask').css('display', 'block');
                            }
                            position = scroll;
                        });
                    
                    <?php } else if($mobile_behaviour == 'specified' && $specified_seconds) {  /* Show on Idle time */ ?>

                        setTimeout( function() { 
                            jQuery('.epu, .epu-mask').css('display', 'block');
                        }, <?php echo $specified_seconds; ?> );

                    <?php } else if($mobile_behaviour == 'tapanywhere') {  /* Show on Touch Anywhere on page */ ?>

                        jQuery('body').click(function() {
                            jQuery('.epu, .epu-mask').css('display', 'block');
                        });

                    <?php } ?>

                <?php } ?>

            <?php } ?>
        </script>

    <?php
        }        
    }
    /* End Popup on front-end */
}

add_action('wp_head', 'epu_header_output', 999);
function epu_header_output() { 

    $active_popup_id = get_option( 'epu_active_popup' );
    require('inc/vars.php'); ?>

    <style>
        .epu {
            display: none;
            text-align: <?php echo $text_align; ?>;
            position: fixed;
            <?php if($layout != 'full-screen') { ?>
            top: 50%;
            transform: translateY(-50%);
            <?php } else { ?>
            top: 0;
            height: 100%;
            <?php } ?>
            padding: <?php echo $popup_padding; ?>px;
            z-index: 9999999999;
            background: #fff;
            box-sizing: border-box;
            overflow: hidden;
            width: <?php echo $popup_width; ?><?php echo $popup_unit; ?>; 
            left: calc(50% - <?php echo $left_pos; ?><?php echo $popup_unit; ?>); 
            background-color: <?php echo $popup_bg_colour; ?>;
            <?php if($popup_bs_colour) { ?>
            box-shadow: 5px 5px 25px rgba(<?php echo $r . ',' . $g . ',' . $b; ?>,0.2);
            <?php } ?>;
            border-radius: <?php echo $corner_radius; ?>px;
        }
        .epu .epu-container {
            display: block;
            width: <?php echo $container_width; ?>;
            <?php if($layout == 'right') { ?>
            float: right;
            <?php } ?>

            <?php if($layout == 'full-screen') { ?>
            position: fixed;
            top: 50%;
            transform: translateY(-50%);
            left: 0;
            padding: 0 100px;
            <?php } ?>
        }
        .epu .epu-heading {
            margin: 0 0 25px 0;
            font-weight: bold;
            font-size: <?php echo $heading_text_size; ?>em;
            line-height: 1.1em;
            color: <?php echo $heading_text_colour; ?>;
        }
        .epu .epu-coax-text {
            margin: 25px 0;
            font-size: <?php echo $coax_text_size; ?>em;
            color: <?php echo $coax_text_colour; ?>;
        }
        .epu-rejection {
            color: <?php echo $coax_text_colour; ?>;
            font-size: <?php echo $coax_text_size * .75; ?>em;
            cursor: pointer;
            margin: 25px 0 0 0;
        }
        .epu-offer-code input[type="text"] {
            padding: 5px 10px 3px 10px;
            background: none;
            border: none;
            font-size: 2em;
            line-height: 1em;
            font-family: 'Courier New', Courier, monospace;
            border: dashed 1px <?php echo $coax_text_colour; ?>;
            color: <?php echo $coax_text_colour; ?>;
            width: auto;
            display: inline-block;
            outline: 0;
        }
        .epu-copied span {
            display: inline-block;
            color: #fff;
            background: #4caf50;
            text-transform: uppercase;
            padding: 3px 5px;
            font-size: 13px;
            border-radius: 2px;
        }
        .epu-button {
            display: inline-block;
            padding: 15px 20px;
            background: <?php echo $url_button_colour; ?>;
            color:  <?php echo $url_text_colour; ?>;
            text-decoration: none;
            border-radius: 2px;
        }
        .epu-yes-no-button {
            display: inline-block;
            padding: 15px 20px;
            text-decoration: none;
            border-radius: 2px;
            color: #fff;
        }
        .epu-yes-button {
            background: #4caf50;
            margin: 0 10px 0 0;
        }
        .epu-no-button {
            background: #f44336;
        }
        <?php if($bg_image) { 
            if(!wp_is_mobile()) {
                $bg = wp_get_attachment_image_url($image_id, $size = 'large');
            } else {
                $bg = wp_get_attachment_image_url($image_id, $size = 'large');
            }
        ?>
        .epu .background-image {
            display: block;
            position: absolute;
            z-index: -1;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('<?php echo $bg; ?>');
            background-position: <?php echo $bg_position; ?>;
            background-size: cover;
            opacity: <?php echo $bg_image_opacity; ?>;
        }
        <?php }
        ?>

        .epu-close {
            position: fixed;
            top: 25px;
            width: 25px;
            height: 25px;
            cursor: pointer;
        }

        .epu-close.right {
            right: 20px;
        }

        .epu-close.left {
            left: 20px;
        }

        .epu-mask {
            background-color:<?php echo $modal_mask_colour; ?>; 
            opacity:<?php echo $modal_opacity; ?>;
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 999999999;
        }

        /* Responsive */
        @media screen and (max-width: 960px) {
            .epu {
                left: 0;
                width: 100%;
                padding: 0;
            }
            .epu .epu-container {
                padding: 25px;
                width: 100%;
            }
            .epu .epu-heading {
                font-size: <?php echo $heading_text_size * .5; ?>em;
            }
            .epu .epu-coax-text {
                margin: 15px 0;
                font-size: <?php echo $coax_text_size *.75; ?>em;
            }
            .epu .bg-image {
                opacity: <?php echo $bg_image_opacity; ?>;
            }
        }
    </style>

<?php }