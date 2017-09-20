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


   // Add default base page
   $args = array(
      'post_title'   => 'Token sale',
      'post_status'  => 'publish',
      'post_type'    => 'page'
   );
   $page_id = wp_insert_post( $args );

   // Set base page as setting
   if ( ! is_wp_error( $page_id ) ) {
      update_option( 'tokensale_plugin_base_page', $page_id );
   }

   // Flush rewrite rules to set the base token sale url
   flush_rewrite_rules();

}
register_activation_hook( __FILE__, 'tokensale_plugin_activate' );

/**
 * Registers and enqueues scripts
 */
function tokensale_plugin_enqueue_scripts() {

   $version = '1.0.0';

   // Enqueue stylesheets
   wp_enqueue_style( 'tokensale_plugin_css', plugin_dir_url( __FILE__ ) . 'css/wp-token-sale.css', array(), $version );
   wp_enqueue_style( 'bootstrap_css', 'https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css', array(), $version );


   // Enqueue scripts
   wp_enqueue_script( 'jquery' );
   wp_enqueue_script( 'tokensale_plugin_js', plugin_dir_url( __FILE__ ) . 'js/wp-token-sale.js', array(), $version );
   wp_enqueue_script( 'popper_js', 'https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js', array( 'jquery' ) );
   wp_enqueue_script( 'bootstrap_js', 'https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js', array( 'jquery' ) );

}
add_action( 'wp_enqueue_scripts', 'tokensale_plugin_enqueue_scripts', 99 );

/**
 * Adds custom body classes
 */
function tokensale_plugin_body_class( $classes ) {

   // Check if loaded page is token sale environment
   if ( $environment = tokensale_plugin_is_environment() ) {
      $classes[] = 'tokensale-plugin-environment';
      $classes[] = 'tokensale-plugin-environment-' . $environment;
   }
   return $classes;
}
add_filter( 'body_class', 'tokensale_plugin_body_class' );

/**
 * Add query vars
 */
function tokensale_plugin_query_vars_filter( $vars ) {
   $vars[] = 'environment';
   return $vars;
}
add_filter( 'query_vars', 'tokensale_plugin_query_vars_filter' );


/**
 * Hide admin bar for token holders
 */
function tokensale_plugin_hide_admin_bar() {
   if ( current_user_can( 'can_manage_tokens' ) && ! is_admin() ) {
      show_admin_bar( false );
   }
}
add_action( 'after_setup_theme', 'tokensale_plugin_hide_admin_bar' );

/**
 * Prevent token holders from visiting dashboard
 */
function tokensale_plugin_prevent_visiting_dashboard() {
    if ( is_admin() && ! current_user_can( 'administrator' ) && ! ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
        exit( wp_redirect( rtrim( tokensale_plugin_base_url(), '/' ) . '/dashboard' ) );
    }
}
add_action( 'init', 'tokensale_plugin_prevent_visiting_dashboard' );

/**
 * Redirects users who are not logged in or are not token holders
 */
function tokensale_plugin_environment_redirect() {

   if ( tokensale_plugin_is_environment( 'dashboard' ) ) {
      if ( ! is_user_logged_in() || ( is_user_logged_in() && ! current_user_can( 'can_manage_tokens' ) ) ) {
         exit( wp_redirect( rtrim( tokensale_plugin_base_url(), '/' ) . '/login' ) );
      }
   }

}
add_action( 'template_redirect', 'tokensale_plugin_environment_redirect' );

/**
 * Add rewrite rules for dashboard and login environments
 */
function tokensale_plugin_environment_rewrite_rules() {
   if ( $page = tokensale_plugin_base_page() ) {
      add_rewrite_rule( $page->post_name . '/([^/]*)/?', 'index.php?pagename=token-sale&environment=$matches[1]', 'top' );
   }
}
add_action( 'init', 'tokensale_plugin_environment_rewrite_rules' );

/**
 * Include templates based on environment query var
 */
function tokensale_plugin_environment_template_include( $template ) {

   global $wp_query;

   if ( $environment = tokensale_plugin_is_environment() ) {
      $environment_template = plugin_dir_path( __FILE__ ) . '/templates/token-sale-' . $environment . '.php';
      if ( file_exists( $environment_template ) ) {
         return $environment_template;
		}
   }

   return $template;

}
add_filter( 'template_include', 'tokensale_plugin_environment_template_include' );

/**
 * Loads plugin template part
 */
function tokensale_plugin_template_part( $template_part = '' ) {
   $path = plugin_dir_path( __FILE__ ) . 'template-parts/' . $template_part;
   if ( file_exists( $path ) ) {
      include $path;
   } else {
      return false;
   }
}

/**
 * Check whether page is token sale environment
 */
function tokensale_plugin_is_environment( $specific = '' ) {

   if ( ! is_page( 'token-sale' ) )
      return false;

   // If is token sale environment
   if ( $environment = get_query_var( 'environment' ) ) {

      // If looking for specific environment
      if ( ! empty( $specific ) ) {
         if ( $environment != $specific ) {
            return false;
         }
      }
      return $environment;
   }
}

/**
 * Returns token sale base page object
 */
function tokensale_plugin_base_page() {
   if ( $page_id = get_option( 'tokensale_plugin_base_page' ) ) {
      return get_post( $page_id );
   }
   return false;
}

/**
 * Returns token sale base url
 */
function tokensale_plugin_base_url() {
   if ( $page = tokensale_plugin_base_page() ) {
      return get_permalink( $page->ID );
   } else {
      return site_url();
   }
}

/**
 * Handles dashboard login
 */
function tokensale_plugin_login_dashboard() {

   $error = false;

   if ( isset( $_POST['tokensale_dashboard_login'] ) ) {

      $credentials = [];

      // Email
      if ( ! empty( $_POST['login_email'] ) ) {
         $credentials['user_login'] = $_POST['login_email'];
      } else {
         $error = true;
      }

      // Password
      if ( ! empty( $_POST['login_password'] ) ) {
         $credentials['user_password'] = $_POST['login_password'];
      } else {
         $error = true;
      }

      if ( ! $error ) {

         // Log in user with credentials
         $user = wp_signon( $credentials );
         if ( ! is_wp_error( $user ) ) {

            // If user is can manage tokens
            if ( user_can( $user, 'can_manage_tokens' ) ) {
               exit( wp_redirect( rtrim( tokensale_plugin_base_url(), '/' ) . '/dashboard' ) );
            }
            // Else, log user out and redirect to login page
            else {
               wp_logout();
               exit( wp_redirect( rtrim( tokensale_plugin_base_url(), '/' ) . '/login' ) );
            }
         }
      }

   }

}
add_action( 'init', 'tokensale_plugin_login_dashboard' );

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
function tokensale_plugin_settings_screen() { ?>

   <div class="wrap">

      <h1><?php _e( 'Token sale settings', 'wp-token-sale' ); ?></h1>

      <form method="post" action="options.php">

         <?php
         // Output nonce, action, and option_page fields.
         settings_fields( 'token-sale-settings' );

         // Prints out all settings sections added to a particular settings page.
         do_settings_sections( 'token-sale-settings' );

         // Echos a submit button
         submit_button();
         ?>

      </form>

   </div><!--End .wrapper-->

   <?php
}

/**
 * Adds all sections, fields and settings
 */
function tokensale_plugin_settings_init() {

   $option_group = 'token-sale-settings';
   $page = 'token-sale-settings';

   $section = 'tokensale_plugin_general_section';
   add_settings_section( $section, __( 'General', 'wp-token-sale' ), 'tokensale_plugin_general_section_callback', $page );

   // Base page setting and field
   $id = 'tokensale_plugin_base_page';
   register_setting( $option_group, $id );
   add_settings_field( $id, __( 'Base page', 'wp-token-sale' ), 'tokensale_plugin_page_dropdown', $page, $section, array(
      'id' => $id,
      'description' => __( 'This is the basic page for token sale related pages like /dashboard and /login.', 'wp-token-sale' ),
   ));

}
add_action( 'admin_init', 'tokensale_plugin_settings_init' );

function tokensale_plugin_general_section_callback() {
   echo '<p>' . __( 'General settings', 'wp-token-sale' ) . '<p>';
}

function tokensale_plugin_page_dropdown( $args ) {

   $pages = get_posts( array(
      'post_type' => 'page',
      'post_status' => 'publish',
      'posts_per_page' => -1,
   ));

   $html  = '<select name="' . $args['id'] . '" id="' . $args['id'] . '">';
      $html .= '<option>-- ' . __( 'Select a page', 'wp-token-sale' ) . ' --</option>';
      if ( ! empty( $pages ) ) {
         foreach ( $pages as $page ) {
            $html .= '<option value="' . $page->ID . '" ' . selected( $page->ID, get_option( $args['id'] ), false ) . '>' . $page->post_title . '</option>';
         }
      }
   $html .= '</select>';

   if ( isset( $args['description'] ) ) {
      $html .= '<p class="description">' . $args['description'] . '</p>';
   }

   echo $html;

}
