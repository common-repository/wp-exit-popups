<?php if ( ! defined( 'ABSPATH' ) ) exit; 

    $active_popup_id = get_option( 'epu_active_popup' );

    /* Start Popup on front-end */
    $layout                         = get_post_meta($active_popup_id, 'layout', true);
    $bg_image                       = get_post_meta($active_popup_id, 'bg_image', true);
    $bg_position                    = get_post_meta($active_popup_id, 'bg_position', true);
    $promo_image                    = get_post_meta($active_popup_id, 'promo_image', true);
    $pi_position                    = get_post_meta($active_popup_id, 'pi_position', true);
    $close_button_colour            = get_post_meta($active_popup_id, 'close_button_colour', true);
    $close_style                    = get_post_meta($active_popup_id, 'close_style', true);
    $close_position                 = get_post_meta($active_popup_id, 'close_position', true);
    $coax_text                      = get_post_meta($active_popup_id, 'coax_text', true);
    $coax_text_colour               = get_post_meta($active_popup_id, 'coax_text_colour', true);
    $coax_text_size                 = get_post_meta($active_popup_id, 'coax_text_size', true);
    $heading_text_colour            = get_post_meta($active_popup_id, 'heading_text_colour', true);
    $heading_text_size              = get_post_meta($active_popup_id, 'heading_text_size', true);
    $popup_bg_colour                = get_post_meta($active_popup_id, 'popup_bg_colour', true);
    $popup_bs_colour                = get_post_meta($active_popup_id, 'popup_bs_colour', true);
    $popup_width                    = get_post_meta($active_popup_id, 'popup_width', true);
    $popup_unit                     = get_post_meta($active_popup_id, 'popup_unit', true);
    $popup_padding                  = get_post_meta($active_popup_id, 'popup_padding', true);
    $corner_radius                  = get_post_meta($active_popup_id, 'corner_radius', true);
    $reject_offer                   = get_post_meta($active_popup_id, 'reject_offer', true);
    $interaction                    = get_post_meta($active_popup_id, 'interaction', true);
    
    /* Gravity Forms */
    $gr_form_id                     = get_post_meta($active_popup_id, 'gr_form_id', true);
    $gr_form_title                  = get_post_meta($active_popup_id, 'gr_form_title', true);
    $gr_form_description            = get_post_meta($active_popup_id, 'gr_form_description', true);
    $gr_form_ajax                   = get_post_meta($active_popup_id, 'gr_form_ajax', true);

    /* Contact Form 7 */
    $cf_form_id                     = get_post_meta($active_popup_id, 'cf_form_id', true);
    $cf_form_title                  = get_post_meta($active_popup_id, 'cf_form_title', true);

    /* WP Forms */
    $wpf_form_id                    = get_post_meta($active_popup_id, 'wpf_form_id', true);
    $wpf_form_title                 = get_post_meta($active_popup_id, 'wpf_form_title', true);
    $wpf_form_description           = get_post_meta($active_popup_id, 'wpf_form_description', true);

    /* Formidable Forms */
    $formidable_form_id             = get_post_meta($active_popup_id, 'formidable_form_id', true);
    $formidable_form_title          = get_post_meta($active_popup_id, 'formidable_form_title', true);
    $formidable_form_description    = get_post_meta($active_popup_id, 'formidable_form_description', true);

    /* Ninja Forms */
    $nf_form_id                     = get_post_meta($active_popup_id, 'nf_form_id', true);

    $full_url                       = get_post_meta($active_popup_id, 'full_url', true);
    $full_url_text                  = get_post_meta($active_popup_id, 'full_url_text', true);

    $yes_url                        = get_post_meta($active_popup_id, 'yes_url', true);
    $no_url                         = get_post_meta($active_popup_id, 'no_url', true);
    $offer_code                     = get_post_meta($active_popup_id, 'offer_code', true);

    $url_button_colour              = get_post_meta($active_popup_id, 'url_button_colour', true);
    $url_text_colour                = get_post_meta($active_popup_id, 'url_text_colour', true);

    $shortcode                      = get_post_meta($active_popup_id, 'shortcode', true);
    $modal_mask_colour              = get_post_meta($active_popup_id, 'modal_mask_colour', true);
    $modal_opacity                  = get_post_meta($active_popup_id, 'modal_opacity', true);     
    $bg_image_opacity               = get_post_meta($active_popup_id, 'bg_image_opacity', true);        
    $dismissible                    = get_post_meta($active_popup_id, 'dismissible', true);
    $cookie_expiry                  = get_post_meta($active_popup_id, 'cookie_expiry', true);
    $active_popup                   = get_post_meta($active_popup_id, 'active_popup', true);
    $show_immediately               = get_post_meta($active_popup_id, 'show_immediately', true);
    $mobile_behaviour               = get_post_meta($active_popup_id, 'mobile_behaviour', true);
    $specified_seconds              = get_post_meta($active_popup_id, 'specified_seconds', true);

    $current_time                   = time();

    if($close_button_colour) {
        $close_button_colour = $close_button_colour;
    } else {
        $close_button_colour = '#ffffff';
    }

    if($close_style == 'cross') {
        $path = '<path d="M54.6 50L99.1 5.5c1.2-1.2 1.2-3.1 0-4.2l-.4-.4c-1.2-1.2-3.1-1.2-4.2 0L50 45.4 5.5.9C4.3-.3 2.4-.3 1.3.9l-.4.3C-.3 2.4-.3 4.3.9 5.4L45.4 50 .9 94.5c-1.2 1.2-1.2 3.1 0 4.2l.4.4c1.2 1.2 3.1 1.2 4.2 0L50 54.6l44.5 44.5c1.2 1.2 3.1 1.2 4.2 0l.4-.4c1.2-1.2 1.2-3.1 0-4.2L54.6 50z"/>';
    } else if($close_style == 'cross-circle') {
        $path = '<path d="M50 100C22.4 100 0 77.6 0 50S22.4 0 50 0s50 22.4 50 50-22.4 50-50 50zm0-93.4C26 6.6 6.6 26 6.6 50S26 93.4 50 93.4 93.4 74 93.4 50 74 6.6 50 6.6z"/><path d="M54.4 50l14.1-14.1c1.2-1.2 1.2-3.1 0-4.2l-.4-.4c-1.2-1.2-3.1-1.2-4.2 0L49.8 45.4 35.7 31.3c-1.2-1.2-3.1-1.2-4.2 0l-.4.4c-1.2 1.2-1.2 3.1 0 4.2L45.2 50 31.1 64.1c-1.2 1.2-1.2 3.1 0 4.2l.4.4c1.2 1.2 3.1 1.2 4.2 0l14.1-14.1 14.1 14.1c1.2 1.2 3.1 1.2 4.2 0l.4-.4c1.2-1.2 1.2-3.1 0-4.2L54.4 50z"/>';
    } else if($close_style == 'cross-square') {
        $path = '<path d="M45.2 50L31.1 64.1c-1.2 1.2-1.2 3.1 0 4.2l.4.4c1.2 1.2 3.1 1.2 4.2 0l14.1-14.1 14.1 14.1c1.2 1.2 3.1 1.2 4.2 0l.4-.4c1.2-1.2 1.2-3.1 0-4.2L54.4 50l14.1-14.1c1.2-1.2 1.2-3.1 0-4.2l-.4-.4c-1.2-1.2-3.1-1.2-4.2 0L49.8 45.4 35.7 31.3c-1.2-1.2-3.1-1.2-4.2 0l-.4.4c-1.2 1.2-1.2 3.1 0 4.2L45.2 50z"/><path d="M93.5 0H0v100h100V0h-6.5zm0 93.5h-87v-87h87v87z"/>';
    } else {
        $path = '<path d="M54.6 50L99.1 5.5c1.2-1.2 1.2-3.1 0-4.2l-.4-.4c-1.2-1.2-3.1-1.2-4.2 0L50 45.4 5.5.9C4.3-.3 2.4-.3 1.3.9l-.4.3C-.3 2.4-.3 4.3.9 5.4L45.4 50 .9 94.5c-1.2 1.2-1.2 3.1 0 4.2l.4.4c1.2 1.2 3.1 1.2 4.2 0L50 54.6l44.5 44.5c1.2 1.2 3.1 1.2 4.2 0l.4-.4c1.2-1.2 1.2-3.1 0-4.2L54.6 50z"/>';
    }

    if($close_position == 'left' || $close_position == 'right') {

        $close_button = '<svg viewBox="0 0 100 100" class="epu-click epu-close ' . $close_position . '" style="fill:' . $close_button_colour . '">' . $path . '</svg>';

    } else if(!$close_position) {

        $close_button = '<svg viewBox="0 0 100 100" class="epu-click epu-close right" style="fill:' . $close_button_colour . '">' . $path . '</svg>';

    } else {
        $close_button = '';
    }

    if($bg_position) {
        $bg_position = $bg_position;
    } else {
        $bg_position = 'top left';
    }

    if($coax_text_colour) {
        $coax_text_colour = $coax_text_colour;
    } else {
        $coax_text_colour = '#ccc';
    }

    if($coax_text_size) {
        $coax_text_size = $coax_text_size;
    } else {
        $coax_text_size = '1.5';
    }

    if($heading_text_colour) {
        $heading_text_colour = $heading_text_colour;
    } else {
        $heading_text_colour = '#ccc';
    }

    if($heading_text_size) {
        $heading_text_size = $heading_text_size;
    } else {
        $heading_text_size = '3';
    }

    if($reject_offer) {
        $reject_offer = $reject_offer;
    } else {
        $reject_offer = __('No Thanks', 'wp-epu');
    }

    if($specified_seconds) {
        $specified_seconds = $specified_seconds * 1000;
    }

    if($popup_bg_colour) {
        $popup_bg_colour = $popup_bg_colour;
    } else {
        $popup_bg_colour = '#ffffff';
    }

    if($modal_mask_colour) {
        $modal_mask_colour = $modal_mask_colour;
    } else {
        $modal_mask_colour = '#000000';
    }

    if($modal_opacity) {
        $modal_opacity = $modal_opacity;
    } else {
        $modal_opacity = 1;
    }

    if($popup_padding) {
        $popup_padding = $popup_padding;
    } else {
        $popup_padding = '100';
    }

    if($corner_radius) {
        $corner_radius = $corner_radius;
    } else {
        $corner_radius = '0';
    }

    if($popup_width) {
        $popup_width = $popup_width;
    } else {
        $popup_width = '960';
    }

    if($cookie_expiry) {
        $cookie_expiry = $cookie_expiry;
    } else {
        $cookie_expiry = '5';
    }

    if($gr_form_title == '1') {
        $form_title = true;
    } else {
        $form_title = false;
    }
    if($gr_form_description == '1') {
        $form_description = true;
    } else {
        $form_description = false;
    }
    if($gr_form_ajax == '1') {
        $form_ajax = true;
    } else {
        $form_ajax = false;
    }

    if($cf_form_id == '1') {
        $form_title = true;
    } else {
        $form_title = false;
    }
    
    if($wpf_form_id == '1') {
        $form_title = true;
    } else {
        $form_title = false;
    }
    if($wpf_form_description == '1') {
        $form_description = true;
    } else {
        $form_description = false;
    }

    if($formidable_form_id == '1') {
        $form_description = true;
    } else {
        $formidable_form_description = false;
    }

    if($bg_image_opacity) {
        $bg_image_opacity = $bg_image_opacity;
    } else {
        $bg_image_opacity = '1';
    }

    if($popup_unit) {
        $popup_unit = $popup_unit;
    } else {
        $popup_unit = 'px';
    }

    /*
        Initial Layout.
        Some variables below may overwrite preceding variables.
    */
    if($layout == 'left') {
        $text_align = 'left';
        $container_width = '50%';
    } else if($layout == 'right') {
        $text_align = 'right';
        $container_width = '50%';
    } else if($layout == 'center') {
        $text_align = 'center';
        $container_width = '100%';
    } else if($layout == 'full-screen') {
        $text_align = 'center';
        $popup_width = '100';
        $popup_unit = '%';
        $container_width = '100%';
    } else {
        $text_align = 'left';
        $container_width = '50%';
    }
    
    $left_pos = $popup_width / 2;
        
    $image_id = attachment_url_to_postid($bg_image);
    $promo_image_id = attachment_url_to_postid($promo_image);

    list($r, $g, $b) = sscanf($popup_bs_colour, "#%02x%02x%02x");

    $epu_title = strtolower(get_the_title($active_popup_id));
    $epu_slug = sanitize_title_with_dashes($epu_title);