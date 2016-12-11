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

//add js to admin woocommerce product edit
function add_js_admin_product_edit( $hook ) {
    if ('post.php' != $hook) {
        return;
    }
    wp_enqueue_script( 'admin_product_edit_js', plugin_dir_url( __FILE__ ) . '/js/admin_product_edit.js' );
}
add_action('admin_enqueue_scripts', 'add_js_admin_product_edit');

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

function diy_config_admin_page(){
    add_menu_page( 'Diy Configurator Admin', 'Diy Configurator', 'manage_options', 'diy_config_admin', 'diy_config_admin_init');
}
function diy_config_admin_init(){

    $categories_with_products = serialize(attach_products_to_categories(get_target_categories()));
//    echo $categories_with_products;
    $target_path = $_SERVER['DOCUMENT_ROOT']."/tshirtecommerce/assets/custom_img/design_config_basis.txt";
    file_put_contents($target_path,$categories_with_products);
//    print_r(get_target_categories());
}
add_action('admin_menu', 'diy_config_admin_page');

function get_target_categories(){

    $target_categories = array();
    $args = array(
        'number' => '',
        'orderby' => '',
        'order' => '',
        'hide_empty' => '',
        'include' => ''
    );
    $product_categories = get_terms('product_cat', $args);
    //get Category id by Category Name
    $main_cate_id = get_term_by('slug', 'diy_product_options', 'product_cat', 'ARRAY_A')['term_id'];

    foreach ($product_categories as $cate) {
        $cate_parent_id = $cate->parent;
        if ($cate_parent_id == $main_cate_id) {
            $cate_name = $cate->name;
//            if(strtolower($cate_name) == 'color'){}
            $cate_slug = $cate->slug;
            $cate_id = $cate->term_taxonomy_id;
//            $target_categories[$cate_id] = $cate_name;
            $target_categories[$cate_id]= array(
                'cate_name' => $cate_name,
                'products' => array()
            );
        }
    }
    return $target_categories;
}

function attach_products_to_categories($categories){

    foreach ($categories as $key => $value){
        $args = array(
            'post_type'             => 'product',
            'post_status'           => 'publish',
            'ignore_sticky_posts'   => 1,
            'posts_per_page'        => '12',
            'meta_query'            => array(
                array(
                    'key'           => '_visibility',
                    'value'         => array('catalog', 'visible'),
                    'compare'       => 'IN'
                )
            ),
            'tax_query'             => array(
                array(
                    'taxonomy'      => 'product_cat',
                    'field' => 'term_id',
                    'terms'         => $key,
                    'operator'      => 'IN' // Possible values are 'IN', 'NOT IN', 'AND'.
                )
            )
        );
        $products = new WP_Query($args);
        foreach ($products->posts as $product){
            $product_name = $product->post_title;
            $product_id = $product->ID;
            $pArray = array(
                'product_id' => $product_id,
                'product_name' => $product_name
            );
            $categories[$key]['products'][] = $pArray;
        }
    }
    return $categories;
}
?>