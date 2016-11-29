<?php
/*
Plugin Name: Diy Configurator
Plugin URI: http://URI_Of_Page_Describing_Plugin_and_Updates
Description: Development for integration of Diy Plattform
Version: 1.0
Author: xulin
Author URI: http://www.xulin-tan.de
*/

//load custom js and css for diy configurator plugins
function all_scripts_and_styles() {
//Load JS and CSS files in here
    wp_register_script ('diy_configurator_scripts', plugins_url() . '/Diy_Configurator/js/diy_configurator_scripts.js', array( 'jquery' ),'1.0.0',true);
        wp_enqueue_script('diy_configurator_scripts');
}
// script!s! !!!
add_action( 'wp_enqueue_scripts', 'all_scripts_and_styles' );

// fix zoom func for woocommerce
function xulin_custom_pretty_foto_js() {
//Load JS and CSS files in here
    wp_register_script ('xulin_custom_pretty_foto_js', plugins_url() . '/Diy_Configurator/js/pretty_foto.js', array( 'jquery' ),'1.0.0',true);
    wp_enqueue_script('xulin_custom_pretty_foto_js');
}
add_action( 'wp_enqueue_scripts', 'xulin_custom_pretty_foto_js' );

//create folder structure to generate json config
function diy_configurator_install(){
    $target_path = $_SERVER['DOCUMENT_ROOT']."/tshirtecommerce/assets/custom_img";
    if(!is_dir($target_path)){
        mkdir($target_path, 0777);
    }
}
register_activation_hook(__FILE__,'diy_configurator_install');

function init_standard_main_img_size(){
    add_option('default_width',111, '','yes');
    add_option('default_height',222, '','yes');
    add_option('default_top',333, '','yes');
    add_option('default_left',444, '','yes');
}
register_activation_hook(__FILE__,'init_standard_main_img_size');

//function remove_header_from_iframe($content){
//
//    $script = "
//        <script type='text/javascript'>
//            jQuery(document).ready(function() {
//                jQuery('body > div.wrapper > div.top-menu.main-menu-top').hide();
//            });
//        </script>
//    ";
//    if(is_page("design your own")){
//        echo $_SERVER['DOCUMENT_ROOT'];
//    }
////    return $content;
//}
//add_filter('the_content','remove_header_from_iframe');

?>