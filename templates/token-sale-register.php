<?php tokensale_plugin_template_part( 'header' ); ?>

   <div class="container-fluid">

      <div class="row">

         <div class="col-12 p-4 col-md-6 offset-md-3 p-md-5 col-lg-4 offset-lg-4 bg-faded rounded-bottom">

            <?php if ( isset( $_GET['waiting_for_activation'] ) ) { ?>

               <p class="alert alert-info" role="alert"><?php _e( 'Thanks for registering! An <strong>activation e-mail</strong> is sent to your e-mail address.', 'wp-token-sale' ); ?></p>

            <?php } else { ?>

               <div class="text-center">
                  <h2><?php _e( 'Register account', 'wp-token-sale' ); ?></h2>
               </div><!--End .text-center-->

               <form method="post">

                  <div class="form-group">
                     <label for="account_first_name_field"><?php _e( 'First name', 'wp-token-sale' ); ?></label>
                     <input type="text" class="form-control" name="account[first_name]" id="account_first_name_field">
                  </div><!--End .form-group-->

                  <div class="form-group">
                     <label for="account_last_name_field"><?php _e( 'Last name', 'wp-token-sale' ); ?></label>
                     <input type="text" class="form-control" name="account[last_name]" id="account_last_name_field">
                  </div><!--End .form-group-->

                  <div class="form-group">
                     <label for="account_email_field"><?php _e( 'Email address', 'wp-token-sale' ); ?></label>
                     <input type="email" class="form-control" name="account[email]" id="account_email_field">
                  </div><!--End .form-group-->

                  <div class="form-group">
                     <label for="account_password_field"><?php _e( 'Password', 'wp-token-sale' ); ?></label>
                     <input type="password" class="form-control" name="account[password]" id="account_password_field">
                  </div><!--End .form-group-->

                  <div class="form-group">
                     <label for="account_second_password_field"><?php _e( 'Retype password', 'wp-token-sale' ); ?></label>
                     <input type="password" class="form-control" name="account[second_password]" id="account_second_password_field">
                  </div><!--End .form-group-->

                  <?php wp_nonce_field( 'register_account', 'register_account_nonce' ); ?>

                  <button type="submit" name="tokensale_register_account" class="btn btn-primary btn-block"><?php _e( 'Register', 'wp-token-sale' ); ?></button>

               </form>

               <hr>

               <div class="text-center">
                  <a href="<?php echo tokensale_plugin_environment_url( 'login' ); ?>"><?php _e( 'I already have an account', 'wp-token-sale' ); ?></a>
               </div><!--End .text-center-->

            <?php } ?>

         </div><!--End .col-12-->

      </div><!--End .row-->

   <div><!--End .container-->

<?php tokensale_plugin_template_part( 'footer' ); ?>
