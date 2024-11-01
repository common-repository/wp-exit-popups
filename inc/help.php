<?php if ( ! defined( 'ABSPATH' ) ) exit; 

/* Add sidebar metabox for wpepu_exit_popup post type */
class wpepu_side {

    function  __construct() {

        add_action( 'add_meta_boxes', array( $this, 'epu_side_box' ) );
    }

    function epu_side_box() {  
 
        add_meta_box(
            'epu_side_details',
            __('WP Exit Popups Help', 'wp-epu'),  
            array( &$this, 'meta_box_content' ),
            'wpepu_exit_popup',
            'side',
            'low'
        );
    }

    function meta_box_content() { ?>
        <ul>
            <li><a href="https://wordpress.org/plugins/wp-exit-popups" target="_blank" rel="noopener"><?php _e( 'Plugin Page', 'wp-epu' ); ?></a></li>    
            <li><a href="https://wordpress.org/support/plugin/wp-exit-popups" target="_blank" rel="noopener"><?php _e( 'Get Help', 'wp-epu' ); ?></a></li>
            <li><a href="https://wordpress.org/plugins/wp-exit-popups/#reviews" target="_blank" rel="noopener"><?php _e( 'Leave a Review', 'wp-epu' ); ?></a></li>
        </ul>
        <p><strong><?php _e( 'Cookie Details', 'wp-epu' ); ?></strong></p>
        <p class="description"><?php _e( 'The name and value of the cookie that will be set when the user dismisses the popup.', 'wp-epu' ); ?></p>
        <p>
            <?php _e( 'Cookie Name:', 'wp-epu' ); ?><br /><input type="text" value="epu-<?php echo sanitize_title_with_dashes(get_the_title()); ?>" style="width:100%; font-family: Courier; border: none; background: #f1f1f1; border-radius: 0; margin: 2px 0 0 0;" />
        </p>
        <p>
            <?php _e( 'Cookie Value:', 'wp-epu' ); ?><br /><input type="text" value="dismissed" style="width:100%; font-family: Courier; border: none; background: #f1f1f1; border-radius: 0; margin: 2px 0 0 0;" />
        </p>
        <p><strong><?php _e( 'Upgrade to PRO', 'wp-epu' ); ?></strong></p>
        <ul>
            <li><span class="dashicons dashicons-yes-alt"></span> <?php _e( 'Full screen popup option', 'wp-epu' ); ?></li>
            <li><span class="dashicons dashicons-yes-alt"></span> <?php _e( 'Add supporting image &amp; position', 'wp-epu' ); ?></li>
            <li><span class="dashicons dashicons-yes-alt"></span> <?php _e( 'Customise rejection text', 'wp-epu' ); ?></li>
            <li><span class="dashicons dashicons-yes-alt"></span> <?php _e( 'Set expiry dates on popups', 'wp-epu' ); ?></li>
            <li><span class="dashicons dashicons-yes-alt"></span> <?php _e( 'More behaviour options for mobile', 'wp-epu' ); ?></li>
            <li><span class="dashicons dashicons-yes-alt"></span> <?php _e( 'Test popups before going live', 'wp-epu' ); ?></li>
            <li><span class="dashicons dashicons-yes-alt"></span> <?php _e( 'Helpful admin column info', 'wp-epu' ); ?></li>
            <li><span class="dashicons dashicons-yes-alt"></span> <?php _e( 'Admin notes', 'wp-epu' ); ?></li>
        </ul>
        <p><a href="https://rocketapps.com.au/product/wp-exit-popups-pro/" target="_blank" rel="noopener" class="utp"><?php _e( 'Upgrade to PRO Now', 'wp-epu' ); ?></a></p>
    <?php }
}
$wpepu_side = new wpepu_side;