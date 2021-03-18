<?php
/**
 * Plugin Name: Google FAQs schema
 * Plugin URI:
 * Description: Fusion FAQs element with Google Schema tags
 * Version: 1.0
 * Author: Galileo Tech Media
 * Author URI: https://galileotechmedia.com/
 * @package Fusion-Core
 * @subpackage Core
 */

if ( ! defined( 'FFWS_PLUGIN_DIR' ) ) {
    define( 'FFWS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
}

include FFWS_PLUGIN_DIR."/functions.php";
include FFWS_PLUGIN_DIR."/options.php";
/*Prechecks*/

add_action('admin_notices',function(){
    if ( ! class_exists( 'FusionBuilder' ) ) {
        echo '<div class="error"><p>Fusion Builder is required for FAQs with Schema element.</p></div>';
    }
});

register_activation_hook(__FILE__, function(){
    if ( class_exists( 'FusionBuilder' ) ) {
        set_transient('ffws_activation_success', true, 5);
    };
});

if(get_transient('ffws_activation_success')){
    add_action('admin_notices', 'ffws_activation_success');
    delete_transient('ffws_activation_success');
}