
jQuery(document).ready(function () {

    jQuery("#publish").click(function () {
        jQuery('#tshirtecommerce-designer')[0].contentWindow.jQuery('body').trigger('generate_json_config');
    });
});


function iframe_loaded(e) {
    return getParamValue('post');
}

function getParamValue(paramName) {
    var url = window.location.search.substring(1);
    var qArray = url.split('&');
    for (var i = 0; i < qArray.length; i++)
    {
        var pArr = qArray[i].split('=');
        if (pArr[0] == paramName)
            return pArr[1];
    }
}