<?php
/**
 * Plugin Name: WP Token Sale
 * Plugin URI: https://www.wptokensaleplugin.com
 * Description: Token sale plugin.
 * Version: 1.0.0
 * Author: Robbert Vermeulen
 * Author URI: https://dev.robbertvermeulen.com
 * Text Domain: wp-token-sale
 * License: GPL2
 */

/**
 * When plugin gets activated
 */
function tokensale_plugin_activate() {

   // Capabilities for token holder role
   $capabilities = array(
      'read'               => true,
      'edit_posts'         => true,
      'upload_files'       => true,
      'can_manage_tokens'  => true
   );

   // Create token holder role
   $role = add_role( 'token-holder', __( 'Token holder', 'wp-token-sale' ), $capabilities );

   // Get administrator role object
   $admin = get_role( 'administrator' );

   // Add 'can_manage_tokens' capability to administrator role
   $admin->add_cap( 'can_manage_tokens' );

}
register_activation_hook( __FILE__, 'tokensale_plugin_activate' );

/**
 * Registers and enqueues scripts
 */
function tokensale_plugin_enqueue_scripts() {

   $version = '1.0.0';

   // Enqueue stylesheets
   wp_enqueue_style( 'tokensale_plugin_css', plugin_dir_url( __FILE__ ) . 'css/wp-token-sale.css', array(), $version );
   wp_enqueue_style( 'bootstrap_css', 'https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css', array(), $version );


   // Enqueue scripts
   wp_enqueue_script( 'jquery' );
   wp_enqueue_script( 'tokensale_plugin_js', plugin_dir_url( __FILE__ ) . '/js/wp-token-sale.js', array(), $version );
   wp_enqueue_script( 'popper_js', 'https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js', array( 'jquery' ) );
   wp_enqueue_script( 'bootstrap_js', 'https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/js/bootstrap.min.js', array( 'jquery' ) );

}
add_action( 'wp_enqueue_scripts', 'tokensale_plugin_enqueue_scripts' );

/**
 * Add query vars
 */
function tokensale_plugin_query_vars_filter( $vars ) {
   $vars[] = 'environment';
   return $vars;
}
add_filter( 'query_vars', 'tokensale_plugin_query_vars_filter' );

/**
 * Add rewrite rules for dashboard and login environments
 */
function tokensale_plugin_environment_rewrite_rules() {
   add_rewrite_rule('token-sale/([^/]*)/?', 'index.php?pagename=token-sale&environment=$matches[1]', 'top');
}
add_action( 'init', 'tokensale_plugin_environment_rewrite_rules' );

/**
 * Include templates based on environment query var
 */
function tokensale_plugin_environment_template_include( $template ) {

   global $wp_query;

   if ( is_page( 'token-sale' ) ) {
      if ( $environment = get_query_var( 'environment' ) ) {
         $environment_template = plugin_dir_path( __FILE__ ) . '/templates/token-sale-' . $environment . '.php';
         if ( file_exists( $environment_template ) ) {
            return $environment_template;
   		}
      }
   }

   return $template;

}
add_filter( 'template_include', 'tokensale_plugin_environment_template_include' );

/**
 * Admin menus
 */
function tokensale_plugin_admin_menus() {

   add_menu_page( __( 'Token sale', 'wp-token-sale' ), __( 'Token sale', 'wp-token-sale' ), 'manage_options', 'token-sale-settings', 'tokensale_plugin_settings_screen', 'dashicons-image-filter' );

}
add_action( 'admin_menu', 'tokensale_plugin_admin_menus' );

/**
 * Admin menus settings screen
 */
function tokensale_plugin_settings_screen() {
   echo 'Test';
}

/**
 * Loads plugin template part
 */
function tokensale_plugin_template_part( $template = '' ) {
   $template_path = plugin_dir_path( __FILE__ ) . $template;
   if ( file_exists( $template_path ) ) {
      include $template_path;
   } else {
      return false;
   }
}
