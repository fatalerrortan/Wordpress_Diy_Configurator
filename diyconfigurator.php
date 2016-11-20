<?php
/*
Plugin Name: Diy Configurator
Plugin URI: http://URI_Of_Page_Describing_Plugin_and_Updates
Description: Development for integration of Diy Plattform
Version: 1.0
Author: xulin
Author URI: http://www.xulin-tan.de
*/
// called when ativing this plugin
register_activation_hook(__FILE__,'diy_configurator_install');
// called when deactiving this plugin
register_deactivation_hook(__FILE__,'diy_configurator_remove');

function diy_configurator_install(){
//    A safe way of adding a named option/value pair to the options database table. Tab Name: wp_options
    add_option('diy_infos','diy test!', '','yes');
}

function display_copyright_remove() {
    /* remove the value inserted by add_option() */
    delete_option('diy_infos');
}

if( is_admin() ) {
    add_action('admin_menu', 'diy_configurator_admin_menu');
}

function diy_configurator_admin_menu(){
    /* add_options_page( $page_title, $menu_title, $capability, $menu_slug, $function);  */
    add_options_page('Diy Configurator', 'Diy Menu', 'administrator','diy_configurator_admin', 'display_diy_configurator_admin_html_page');
}

function display_diy_configurator_admin_html_page(){
    ?>
    <div>
        <h2>Set Copyright</h2>
        <form method="post" action="options.php">
            <?php /* set post form data into database */ ?>
            <?php wp_nonce_field('update-options'); ?>

            <p>
                <textarea
                    name="diy_infos"
                    id="diy_infos_id"
                    cols="40"
                    rows="6"><?php echo get_option('diy_infos'); ?></textarea>
            </p>

            <p>
                <input type="hidden" name="action" value="update" />
                <input type="hidden" name="page_options" value="diy_infos" />

                <input type="submit" value="Save" class="button-primary" />
            </p>
        </form>
    </div>
    <?php
}
add_filter('the_content','getDiyInfo');

function getDiyInfo($content){
    if(is_page(2)){
        $content = $content. get_option('diy_infos');
    }
    return $content;
}
?>