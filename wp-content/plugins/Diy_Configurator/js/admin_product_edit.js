
jQuery(document).ready(function () {
    jQuery("#publish").click(function () {
        jQuery('#tshirtecommerce-designer')[0].contentWindow.jQuery('body').trigger('generate_json_config');
    });
});
