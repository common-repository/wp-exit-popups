jQuery('.colour-picker').wpColorPicker();

jQuery('.remove-bg-image').click(function() {
    jQuery('.bg-image-thumb-container').remove();
    jQuery('#bg_image').val('');
});
jQuery('.remove-promo-image').click(function() {
    jQuery('.promo-image-thumb-container').remove();
    jQuery('#promo_image').val('');
});


jQuery('.interaction .radio').click(function() {
    jQuery('.interaction .form-options, .interaction .shortcode, .interaction .url, .interaction .yes, .interaction .no, .interaction .form-titles, .interaction .offer-code').removeClass('show');
});

jQuery('.interaction .radio.form').click(function() {
    jQuery('.interaction .form-options, .interaction .form-titles').addClass('show');
});
jQuery('.interaction .radio.url').click(function() {
    jQuery('.interaction .url').addClass('show');
});
jQuery('.interaction .radio.shortcode').click(function() {
    jQuery('.interaction .shortcode').addClass('show');
});
jQuery('.interaction .radio.yes_or_no').click(function() {
    jQuery('.interaction .yes, .interaction .no').addClass('show');
});
jQuery('.interaction .radio.offer_code').click(function() {
    jQuery('.offer-code').addClass('show');
});

jQuery('.layouts label').click(function() {
    jQuery('.layouts label').removeClass('layout-selected');
    jQuery(this).addClass('layout-selected');
});

jQuery('.bg-positions label').click(function() {
    jQuery('.bg-positions label').removeClass('selected');
    jQuery(this).addClass('selected');
});

jQuery('.radio-behaviour').click(function() {
    if (!jQuery('.specified:checked').val()) {
        jQuery('.specified-seconds').css('display', 'none');
        jQuery('#specified_seconds').removeAttr('required');
    }
    else {
        jQuery('.specified-seconds').css('display', 'block');
        jQuery('#specified_seconds').attr('required', 'required');
    }
});