<?php tokensale_plugin_template_part( 'head.php' ); ?>

   <div class="container-fluid">

      <div class="row">

         <div class="col-12 col-md-4 offset-md-4 p-4 p-md-5 bg-faded rounded-bottom">

            <div class="text-center">
               <h2>Login</h2>
            </div><!--End .text-center-->

            <form method="post">

               <div class="form-group">
                  <label for="login_email_field"><?php _e( 'Email address', 'wp-token-sale' ); ?></label>
                  <input type="email" class="form-control" name="login_email" id="login_email_field" placeholder="<?php _e( 'Enter email', 'wp-token-sale' ); ?>">
               </div>
               <div class="form-group">
                  <label for="login_password_field"><?php _e( 'Password', 'wp-token-sale' ); ?></label>
                  <input type="password" class="form-control" name="login_password" id="login_password_field" placeholder="<?php _e( 'Password', 'wp-token-sale' ); ?>">
               </div>
               <button type="submit" name="tokensale_dashboard_login" class="btn btn-primary btn-block"><?php _e( 'Login', 'wp-token-sale' ); ?></button>

            </form>

            <hr>

            <div class="text-center">
               <a href="#"><?php _e( 'Register account', 'wp-token-sale' ); ?></a>
            </div><!--End .text-center-->

         </div><!--End .col-12-->

      </div><!--End .row-->

   <div><!--End .container-->

<?php tokensale_plugin_template_part( 'foot.php' ); ?>
