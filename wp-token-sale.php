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

// Token sale base page option name
define( TOKENSALE_PLUGIN_BASE_PAGE_OPTION, 'tokensale_plugin_base_page' );

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


   // Add default base page if not already set
   if ( ! tokensale_plugin_base_page() || null == get_page_by_title( 'Token sale' ) ) {

      // Add page
      $args = array(
         'post_title'   => 'Token sale',
         'post_status'  => 'publish',
         'post_type'    => 'page'
      );
      $page_id = wp_insert_post( $args );

      if ( ! is_wp_error( $page_id ) ) {

         // Set 'tokensale_plugin_base_page' option
         tokensale_plugin_set_base_page( $page_id );

         // Base page exists in options table now, so add and flush rewrite rules
         tokensale_plugin_add_environment_rewrite_rules();
         flush_rewrite_rules();
      }

   }

}
register_activation_hook( __FILE__, 'tokensale_plugin_activate' );

/**
 * Registers and enqueues scripts
 */
function tokensale_plugin_enqueue_scripts() {

   $version = '1.0.0';

   // Enqueue stylesheets
   wp_enqueue_style( 'tokensale_plugin_css', plugin_dir_url( __FILE__ ) . 'css/wp-token-sale.css', array(), $version );
   wp_enqueue_style( 'bootstrap_css', 'https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css', array() );

   // Enqueue scripts
   wp_enqueue_script( 'jquery' );
   wp_enqueue_script( 'tokensale_plugin_js', plugin_dir_url( __FILE__ ) . 'js/wp-token-sale.js', array(), $version );
   wp_enqueue_script( 'bootstrap_js', 'https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js', array( 'jquery' ) );

}
add_action( 'wp_enqueue_scripts', 'tokensale_plugin_enqueue_scripts', 99 );

/**
 * Register navigations menus
 */
function tokensale_plugin_register_menus() {
   register_nav_menus(
      array(
         'tokensale-plugin-dashboard-menu' => __( 'Token Sale dashboard menu', 'wp-token-sale' ),
      )
   );
}
add_action( 'init', 'tokensale_plugin_register_menus' );

/**
 * Changes email from
 */
// function tokensale_plugin_email_from( $email ) {
//    return get_bloginfo(  );
// }
// add_filter( 'wp_mail_from', 'tokensale_plugin_email_from' );

/**
 * Changes email from name
 */
// function tokensale_plugin_email_from_name( $name ) {
//    return 'Teddy Westside';
// }
// add_filter( 'wp_mail_from_name', 'tokensale_plugin_email_from_name' );

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
   if ( ! current_user_can( 'administrator' ) && current_user_can( 'can_manage_tokens' ) ) {
      show_admin_bar( false );
   }
}
add_action( 'after_setup_theme', 'tokensale_plugin_hide_admin_bar' );

/**
 * Prevent token holders from visiting WP dashboard
 */
function tokensale_plugin_prevent_visiting_dashboard() {
   if ( is_admin() && ! current_user_can( 'administrator' ) && ! ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
      wp_redirect( tokensale_plugin_environment_url( 'dashboard' ) );
      exit;
   }
}
add_action( 'init', 'tokensale_plugin_prevent_visiting_dashboard' );

/**
 * Redirects in token sale environment
 */
function tokensale_plugin_environment_redirect() {

   // Redirect to login if user is not logged in or is not token holder
   if ( tokensale_plugin_is_environment( 'dashboard' ) ) {
      if ( ! is_user_logged_in() || ( is_user_logged_in() && ! current_user_can( 'can_manage_tokens' ) ) ) {
         wp_redirect( tokensale_plugin_environment_url( 'login' ) );
         exit;
      }
   }

   // Is login page
   if ( tokensale_plugin_is_environment( 'login' ) ) {

      // User is logged in and is token holder
      if ( is_user_logged_in() && current_user_can( 'can_manage_tokens' ) ) {

         // Log user out when ?logout is true
         if ( isset( $_GET['logout'] ) ) {
            wp_logout();

         // Else, redirect user to dashboard
         } else {
            wp_redirect( tokensale_plugin_environment_url( 'dashboard' ) );
            exit;
         }
      }
   }
}
add_action( 'template_redirect', 'tokensale_plugin_environment_redirect' );

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
 * Adds rewrite rules for dashboard and login environments
 */
function tokensale_plugin_add_environment_rewrite_rules() {
   if ( $page = tokensale_plugin_base_page() ) {
      add_rewrite_rule( $page->post_name . '/([^/]*)/?', 'index.php?pagename=' . $page->post_name . '&environment=$matches[1]', 'top' );
   }
}

/**
 * Loads plugin template part
 */
function tokensale_plugin_template_part( $template_part = '' ) {
   $path = plugin_dir_path( __FILE__ ) . 'template-parts/' . $template_part . '.php';
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

   if ( ! is_page( tokensale_plugin_base_page() ) )
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
 * Sets token sale base page id
 */
function tokensale_plugin_set_base_page( $page_id ) {
   if ( ! empty( $page_id ) && is_int( $page_id ) ) {
      update_option( TOKENSALE_PLUGIN_BASE_PAGE_OPTION, $page_id );
   }
}

/**
 * Returns token sale base page object
 */
function tokensale_plugin_base_page() {
   if ( $page_id = get_option( TOKENSALE_PLUGIN_BASE_PAGE_OPTION ) ) {
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
 * Returns token sale environment url
 */
function tokensale_plugin_environment_url( $environment ) {
   if ( ! empty( $environment ) ) {
      return rtrim( tokensale_plugin_base_url(), '/' ) . '/' . ltrim( $environment, '/' );
   }
}

/**
 * Get nav menu items by location
 *
 * @param $location The menu location id
 */
function tokensale_plugin_nav_menu_items_by_location( $location, $args = [] ) {

   // Get all locations
   $locations = get_nav_menu_locations();

   // Get object id by location
   $object = wp_get_nav_menu_object( $locations[$location] );

   // Get menu items by menu name
   $menu_items = wp_get_nav_menu_items( $object->name, $args );

   // Return menu post objects
   return $menu_items;
}


/**
 * Handles dashboard login
 */
function tokensale_plugin_login_dashboard() {

   $error = false;

   if ( isset( $_POST['tokensale_dashboard_login'] ) ) {

      $credentials = array();

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

      // Check if account is activated
      if ( $user = get_user_by( 'login', $credentials['user_login'] ) ) {
         if ( get_user_meta( $user->ID, 'active_account', true ) != true )
            $error = true;
      }

      // Verify nonce field
      if ( ! isset( $_POST['login_nonce'] ) || ! wp_verify_nonce( $_POST['login_nonce'], 'login' ) )
         $error = true;

      if ( ! $error ) {

         // Log in user with credentials
         $user = wp_signon( $credentials );
         if ( ! is_wp_error( $user ) ) {

            // If user is can manage tokens
            if ( user_can( $user, 'can_manage_tokens' ) ) {
               $redirect = tokensale_plugin_environment_url( 'dashboard' );
            }
            // Else, log user out and redirect to login page
            else {
               wp_logout();
               $redirect = tokensale_plugin_environment_url ( 'login' );
            }

            // Redirect to dashboard or back to login
            wp_redirect( $redirect );
            exit;
         }
      }

   }

}
add_action( 'init', 'tokensale_plugin_login_dashboard' );

/**
 * Handles account register
 */
function tokensale_plugin_register() {

   // Visiting activation url
   if ( ! empty( $_GET['activation_code'] ) && ! empty( $_GET['user_id'] ) ) {

      // If activation code corresponds to the stored one in user meta
      if ( get_user_meta( $_GET['user_id'], 'activation_code', true ) === sha1( $_GET['activation_code'] ) ) {

         // Activate account
         if ( update_user_meta( $_GET['user_id'], 'active_account', true ) ) {

            // Redirect to url with activated parameter
            $redirect = add_query_arg( array(
               'account_activated' => true,
               'user_id' => $_GET['user_id']
            ), tokensale_plugin_environment_url( 'login' ) );

            wp_redirect( $redirect );
            exit;

         }
      }

   }

   // Submitting register form
   if ( isset( $_POST['tokensale_register_account'] ) ) {

      $error = false;

      if ( ! empty( $_POST['account'] ) ) {

         $account = array();

         foreach ( $_POST['account'] as $name => $value ) {

            if ( empty( $value ) ) {
               $error = true;
            } else {
               $account[$name] = $value;
            }

         }

         // Verify nonce field
         if ( ! isset( $_POST['register_account_nonce'] ) || ! wp_verify_nonce( $_POST['register_account_nonce'], 'register_account' ) )
            $error = true;

         // No errors and password are the same
         if ( ! $error && ( $account['password'] === $account['second_password'] ) ) {

            $userdata = array(
               'user_login'   => $account['email'],
               'user_pass'    => $account['password'],
               'user_email'   => $account['email'],
               'first_name'   => $account['first_name'],
               'last_name'    => $account['last_name'],
               'role'         => 'token-holder'
            );

            // Create new user
            $user_id = wp_insert_user( $userdata );

            if ( $user_id && ! is_wp_error( $user_id ) ) {

               // Generate activation code
               $activation_code = bin2hex( openssl_random_pseudo_bytes( 16 ) );

               // Create activation link to login url with activation code and username
               $activation_link = add_query_arg( array(
                  'activation_code' => $activation_code,
                  'user_id' => $user_id,
               ), tokensale_plugin_environment_url( 'login' ) );

               // Store SHA-1 hash of activation code in user meta data
               add_user_meta( $user_id, 'activation_code', sha1( $activation_code ) );

               // Set active account to false, so it can be activated by the activation link
               add_user_meta( $user_id, 'active_account', false );

               // Set email from
               add_filter( 'wp_mail_from', function() {
                  $mail = get_option( 'tokensale_plugin_email_from' );
                  return ( empty( $mail ) ) ? get_bloginfo( 'admin_email' ) : $mail;
               });

               // Set email from name
               add_filter( 'wp_mail_from_name', function() {
                  $name = get_option( 'tokensale_plugin_email_from' );
                  return ( empty( $name ) ) ? get_bloginfo( 'name' ) : $mail;
               });

               // Send activation email
               $message  = __( 'Thanks for registering your account', 'wp-token-sale' );
               $message .= ' ' . $activation_link;
               wp_mail( $account['email'], __( 'Activate your account', 'wp-token-sale' ), $message );

               // Redirect
               wp_redirect( add_query_arg( 'waiting_for_activation', true, tokensale_plugin_environment_url( 'register' ) ) );

            }
         }
      }
   }

}
add_action( 'init', 'tokensale_plugin_register' );

/**
 * Admin menus
 */
function tokensale_plugin_admin_menus() {
   add_menu_page( __( 'Token sale', 'wp-token-sale' ), __( 'Token sale', 'wp-token-sale' ), 'manage_options', 'token-sale-settings', 'tokensale_plugin_settings_screen', 'dashicons-image-filter' );
}
add_action( 'admin_menu', 'tokensale_plugin_admin_menus' );

/**
 * Add and flush rewrite rules after updating base page option
 * so the new url can be visited
 */
function tokensale_plugin_update_base_page_rewrite_rules( $option ) {
   if ( TOKENSALE_PLUGIN_BASE_PAGE_OPTION == $option ) {
      tokensale_plugin_add_environment_rewrite_rules();
      flush_rewrite_rules();
   }
}
add_action( 'updated_option', 'tokensale_plugin_update_base_page_rewrite_rules' );

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

   // General section
   $section = 'tokensale_plugin_general_section';
   add_settings_section( $section, __( 'General', 'wp-token-sale' ), 'tokensale_plugin_general_section_callback', $page );

   // Base page setting and field
   $id = TOKENSALE_PLUGIN_BASE_PAGE_OPTION;
   register_setting( $option_group, $id );
   add_settings_field( $id, __( 'Base page', 'wp-token-sale' ), 'tokensale_plugin_page_dropdown', $page, $section, array(
      'id' => $id,
      'description' => __( 'This is the basic page for token sale related pages like /dashboard and /login.', 'wp-token-sale' ),
   ));

   // Email section
   $section = 'tokensale_plugin_email_section';
   add_settings_section( $section, __( 'E-mail', 'wp-token-sale' ), 'tokensale_plugin_email_section_callback', $page );

   // Email from setting and field
   $id = 'tokensale_plugin_email_from';
   register_setting( $option_group, $id );
   add_settings_field( $id, __( 'Email from', 'wp-token-sale' ), 'tokensale_plugin_text_field', $page, $section, array(
      'id' => $id,
      'description' => __( 'The e-mail address used as "from" in token sale related e-mails.', 'wp-token-sale' ),
      'placeholder'  => get_bloginfo( 'admin_email' )
   ));

   // Email from setting and field
   $id = 'tokensale_plugin_email_from_name';
   register_setting( $option_group, $id );
   add_settings_field( $id, __( 'Email from name', 'wp-token-sale' ), 'tokensale_plugin_text_field', $page, $section, array(
      'id' => $id,
      'description'  => __( 'The name used as "from" in token sale related e-mails.', 'wp-token-sale' ),
      'placeholder'  => get_bloginfo( 'name' )
   ));

}
add_action( 'admin_init', 'tokensale_plugin_settings_init' );

/**
 * General settings section
 */
function tokensale_plugin_general_section_callback() {
   echo '<p>' . __( 'General settings', 'wp-token-sale' ) . '<p>';
}

/**
 * Email settings section
 */
function tokensale_plugin_email_section_callback() {
   echo '<p>' . __( 'E-mail settings', 'wp-token-sale' ) . '<p>';
}

/**
 * Prints text field for a setting in add_settings_field()
 */
function tokensale_plugin_text_field( $args ) {

   $value = get_option( $args['id'] );

   $html = '<input type="text" name="' . $args['id'] . '" value="' . $value . '" placeholder="' . $args['placeholder'] . '">';
   if ( isset( $args['description'] ) ) {
      $html .= '<p class="description">' . $args['description'] . '</p>';
   }
   echo $html;

}

/**
 * Prints a page dropdown for a setting in add_settings_field()
 */
function tokensale_plugin_page_dropdown( $args ) {

   $pages = get_posts( array(
      'post_type' => 'page',
      'post_status' => 'publish',
      'posts_per_page' => -1,
   ));

   $html = '<select name="' . $args['id'] . '" id="' . $args['id'] . '">';
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
