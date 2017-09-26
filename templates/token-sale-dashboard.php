<?php tokensale_plugin_template_part( 'header' );

   if ( is_user_logged_in() ) {
      $user = wp_get_current_user();
      ?>

      <div class="navbar navbar-toggleable-md navbar-header">

         <div class="navbar-brand">Foodimus Token Sale</div>

         <ul class="navbar-nav">
            <li class="nav-link logged-in-user"><?php echo $user->user_login; ?></li>
            <li class="nav-link logout"><a href="<?php echo tokensale_plugin_environment_url( 'login?logout=1' ); ?>"><?php _e( 'Logout', 'wp-token-sale' ); ?></a></li>
         </ul><!--End .navbar-nav-->

      </div><!--End .navbar-->

      <div class="container-fluid">

         <div class="row">

            <div class="col-12 col-lg-2 p-lg-0 sidebar">

               <button class="menu-toggler" data-toggle="collapse" data-target="#menu">
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
               </button><!--End .menu-toggler-->

               <?php
               if ( $menu_items = tokensale_plugin_nav_menu_items_by_location( 'tokensale-plugin-dashboard-menu' ) ) {
                  echo '<ul class="menu collapse" id="menu">';
                  foreach ( $menu_items as $item ) {
                     echo '<li><a href="">' . $item->title . '</a></li>';
                  }
                  echo '</ul>';
               }
               ?>

            </div><!--End .sidebar-->

            <div class="pt-4 pb-1 col-12 p-sm-4 col-lg-10 offset-lg-2 p-md-4 mainbar">

               <div class="row">

                  <div class="col-md-12">
                     <h2><?php _e( 'Dashboard', 'wp-token-sale' ); ?></h2>
                  </div><!--End .col-->

               </div><!--End .row-->

               <hr>

               <div class="cards">

                  <div class="row">

                     <div class="col-md-12">

                        <div class="card-deck">

                           <div class="card token-balance-card">

                              <div class="card-header"><?php _e( 'Your balance', 'wp-token-sale' ); ?></div>

                              <div class="card-block token-balance-value d-flex">
                                 <div class="token-balance-value align-self-center">
                                    <h3 class="token-balance-value">0.00000000000 FDM</h3>
                                 </div><!--End .token-balance-value-->
                              </div><!--End .card-block-->

                           </div><!--End .balance-card-->

                           <div class="card token-stats-card">
                              <div class="card-header"><?php _e( 'Total Tokens', 'wp-token-sale' ); ?></div>
                              <div class="card-block">

                                 <div class="stats">
                                    <h4 class="tokens-sold-value">3,481,210 / 8,000,000</h4>
                                    <p><?php _e( 'Tokens sold', 'wp-token-sale' ); ?></p>
                                 </div><!--End .block-->

                                 <div class="progress">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: 33%" aria-valuenow="33" aria-valuemin="0" aria-valuemax="100"></div>
                                 </div><!--End .progress-->

                              </div><!--End .card-footer-->

                           </div><!--End .balance-card-->

                        </div><!--End .card-deck-->

                     </div><!--End .col-->

                  </div><!--End .row-->

                  <div class="row">

                     <div class="col-md-12">

                        <div class="card token-purchase-card">
                           <div class="card-header"><?php _e( 'Purchase tokens', 'wp-token-sale' ); ?></div>

                           <div class="card-block">

                              <form>

                                 <div class="form-group">
                                    <label><?php _e( 'Enter your Ethereum ERC20 compatible wallet address. FDM tokens will be allocated to this address', 'wp-token-sale'); ?></label>
                                    <input class="form-control" type="text" placeholder="Your wallet address">
                                 </div><!--End .form-group-->

                                 <div class="row">
                                    <div class="col-md-6">
                                       <div class="form-group">
                                          <label><?php _e( 'Enter the number of tokens to purchase', 'wp-token-sale' ); ?></label>
                                          <input class="form-control" type="text" placeholder="0">
                                       </div>
                                    </div><!--End .col-->

                                    <div class="col-md-6 token-amount-buttons">

                                       <div class="form-group">
                                          <label><?php _e( 'Alternatively click on the number of tokens to purchase', 'wp-token-sale' ); ?></label>
                                          <div class="amount-buttons">
                                             <button type="button" class="btn btn-outline-primary">200</button>
                                             <button type="button" class="btn btn-outline-primary">500</button>
                                             <button type="button" class="btn btn-outline-primary">1000</button>
                                             <button type="button" class="btn btn-outline-primary">5000</button>
                                             <button type="button" class="btn btn-outline-primary">10.000</button>
                                             <button type="button" class="btn btn-outline-primary">25.000</button>
                                             <button type="button" class="btn btn-outline-primary">5.0000</button>
                                             <button type="button" class="btn btn-outline-primary">100.0000</button>
                                             <button type="button" class="btn btn-outline-primary">150.0000</button>
                                             <button type="button" class="btn btn-outline-primary">200.0000</button>
                                             <button type="button" class="btn btn-outline-primary">250.0000</button>
                                          </div><!--End .amount-buttons-->
                                       </div><!--End .form-group-->

                                    </div><!--End .col-->

                                 </div><!--End .row-->

                                 <div class="row">
                                    <div class="col-md-12 text-center token-purchasing">
                                       <button type="submit" class="btn btn-success btn-lg"><?php _e( 'Pay using Ethereum', 'wp-token-sale' ); ?></button>
                                       <p>By submitting this form, you agree to <a href="">Terms and Conditions</a> and <a href="">Privacy Policy</a></p>
                                    </div><!--End .col-->
                                 </div><!--End .row-->

                              </form>

                           </div><!--End .card-block-->

                        </div><!--End .token-purchase-card-->

                     </div><!--End .col-md-12-->

                  </div><!--End .row-->

                  <div class="row">

                     <div class="col-md-12">

                        <div class="card faq-card">
                           <div class="card-header">Frequently asked questions</div>
                           <ul class="list-group list-group-flush">
                              <li class="list-group-item"><a href="">How will I know if my purchase was successful?</a></li>
                              <li class="list-group-item"><a href="">Will there be a lock-up on tokens sold in this token sale?</a></li>
                              <li class="list-group-item"><a href="">What are the token sale transparency and security arrangements?</a></li>
                          </ul>
                        </div><!--End .balance-card-->

                     </div><!--End .col-md-12-->

                  </div><!--End .row-->

               </div><!--End .cards-->

            </div><!--End .mainbar-->

         </div>

      </div><!--End .container-fluid-->

      <?php
   }

tokensale_plugin_template_part( 'footer' ); ?>
