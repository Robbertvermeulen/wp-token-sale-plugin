<?php tokensale_plugin_template_part( 'header' );

$email = '';
if ( ! empty( $_GET['user_id'] ) ) {
   $user = get_user_by( 'ID', $_GET['user_id'] );
   $email = $user->user_login;
}
?>

   <div class="container-fluid">

      <div class="row">

         <div class="col-12 p-4 col-md-6 offset-md-3 p-md-5 col-lg-4 offset-lg-4 bg-faded rounded-bottom">

            <?php if ( isset( $_GET['account_activated'] ) && true == $_GET['account_activated'] ) { ?>

               <p class="alert alert-success" role="alert"><?php _e( 'Your account has been successfully activated!', 'wp-token-sale' ); ?></p>

            <?php } ?>

            <div class="text-center">
               <h2><?php _e( 'Login', 'wp-token-sale' ); ?></h2>
            </div><!--End .text-center-->

            <form method="post">

               <div class="form-group">
                  <label for="login_email_field"><?php _e( 'Email address', 'wp-token-sale' ); ?></label>
                  <input type="email" class="form-control" name="login_email" id="login_email_field" value="<?php echo $email; ?>">
               </div><!--End .form-group-->

               <div class="form-group">
                  <label for="login_password_field"><?php _e( 'Password', 'wp-token-sale' ); ?></label>
                  <input type="password" class="form-control" name="login_password" id="login_password_field">
               </div><!--End .form-group-->

               <?php wp_nonce_field( 'login', 'login_nonce' ); ?>

               <button type="submit" name="tokensale_dashboard_login" class="btn btn-primary btn-block"><?php _e( 'Login', 'wp-token-sale' ); ?></button>

            </form>

            <hr>

            <div class="text-center">
               <a href="<?php echo tokensale_plugin_environment_url( 'register' ); ?>"><?php _e( 'Register account', 'wp-token-sale' ); ?></a>
            </div><!--End .text-center-->

         </div><!--End .col-12-->

      </div><!--End .row-->

   <div><!--End .container-->

<?php tokensale_plugin_template_part( 'footer' ); ?>
