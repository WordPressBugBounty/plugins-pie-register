<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<?php
$piereg = $this->get_pr_global_options();

$paypal_live = $paypal_sandbox = false;
if($piereg['paypal_sandbox'] == "no"){
	$paypal_live 	= true;
} elseif ($piereg['paypal_sandbox'] == "yes" || !$piereg['paypal_sandbox']) {
	$paypal_sandbox = true;   
} 
				  
?>
<div class="pieregister-admin pieregister-admin-payment-gateways" >
<div id="payment_gateway_tabs" class="hideBorder">
    <div class="settings pad_bot_none">
        <h2 class="headingwidth"><?php esc_html_e("Payment Gateways",'pie-register') ?></h2>
        <div id="piereg-detailed-guide-link">
			<a target="_blank" href="https://pieregister.com/documentation/payment-integration/"><?php esc_html_e("Learn how to use Payment Gateways","pie-register"); ?></a>
		</div>  
        <?php 
		if(isset($this->pie_post_array['notice']) && $this->pie_post_array['notice'] ){
			echo '<div id="message" class="updated fade msg_belowheading"><p><strong>' . wp_kses_post($this->pie_post_array['notice']) . '.</strong></p></div>';
        }else if( (isset($_POST['notice']) && $_POST['notice']) ){
			echo '<div id="message" class="updated fade msg_belowheading"><p><strong>' . wp_kses_post($_POST['notice']) . '</strong></p></div>';
		}else if( isset($this->pie_post_array['error']) && !empty($this->pie_post_array['error']) ){
			echo '<div id="error" class="error fade msg_belowheading"><p><strong>' . wp_kses_post($this->pie_post_array['error']) . '</strong></p></div>';
		}
		?>
        <div class="tabOverwrite">
            <div id="tabsSetting" class="tabsSetting">            
                <ul class="tabLayer1">
                    <li><a href="#piereg_general_settings_payment_gateway"><?php esc_html_e("General Settings","pie-register") ?></a></li><!--Add General Settings Menu-->
                    <?php $paypal_tab = esc_html__("PayPal Standard","pie-register"); ?>
                    <li><a href="#piereg_paypal_payment_gateway"><?php echo apply_filters('pie_register_paypal_tab_label',$paypal_tab); ?></a></li><!--Add Paypal-->
                    <?php //pie_register_Authorize_Net_paymentgateways_menus
                        do_action('pie_register_payment_setting_menus'); //<!--for Authorize.Net-->
                    ?>
                    <li><a href="#piereg_payment_log"><?php esc_html_e("Payment Log","pie-register") ?></a></li><!--Add Payment Log-->
                </ul>
            </div>
        </div>
    </div>
    <!-- start Paypal pament gateway -->
    <div id="piereg_paypal_payment_gateway">
        <div id="container">
          <div class="right_section">
            <div class="settings">
              <?php echo '<a href="http://www.paypal.com/payments-standard" target="_blank"><img class="logo-payment-align" src="'.esc_url($this->plugin_url."/assets/images/paypal-standard-logo.png").'" /></a>'; ?>
              <div id="pie-register">
              	<form method="post" action="#piereg_paypal_payment_gateway" enctype="multipart/form-data">
                <?php if( function_exists( 'wp_nonce_field' )) wp_nonce_field( 'piereg_wp_payment_gateway_page_nonce','piereg_payment_gateway_page_nonce'); ?>
                <div class="fields">
                  <div class="radio_fields">
                  	<input type="checkbox" name="enable_paypal" id="enable_paypal" value="1" <?php checked($piereg['enable_paypal']=="1", true, true); ?> />
                  </div>
                  <label for="enable_paypal" class="label_mar_top"><?php esc_html_e("Enable PayPal Standard","pie-register"); ?></label>
                </div>
                <div class="fields">
                    <label for="paypal_butt_id"><?php esc_html_e('PayPal Hosted Button ID', 'pie-register');?></label>
                    <input type="text" name="piereg_paypal_butt_id" class="input_fields" id="paypal_butt_id" value="<?php echo esc_attr($piereg['paypal_butt_id']);?>" />
                </div>
                <div class="fields">
                  <label for="paypal_sandbox">
                    <?php esc_html_e('Paypal Mode', 'pie-register');?>
                  </label>
                  <select name="piereg_paypal_sandbox" id="paypal_sandbox">
                    <option <?php selected($paypal_live, true); ?> value="no"><?php esc_html_e('Live', 'pie-register');?></option>
                    <option <?php selected($paypal_sandbox, true); ?> value="yes"><?php esc_html_e('Sandbox', 'pie-register');?></option>
                  </select>
                </div>

                <?php 
                    if ( $this->checkAddonActivated('PayPal_Recurring') == true )
                    {
                        do_action('piereg_paypal_recurring_settings_end');
                    }                
                ?>

                <div class="pie-save-settings-bar">
                    <div class="fields fields_submitbtn">
                        <input name="action" value="pie_reg_update" type="hidden" />
                        <input type="hidden" name="payment_gateway_page" value="1" />
                        <input name="Submit" class="submit_btn" value="<?php esc_attr_e('Save Changes','pie-register');?>" type="submit" />
                    </div>
                    <div id="piereg-guide-link">
                        <div id="piereg-guide-link-items">
                            <div class="piereg-guide-link-icon" data-title="<?php esc_attr_e('Click here for a detailed guide.','pie-register');?>">
                            <a href="https://pieregister.com/documentation/how-to-add-membership-fees-field-using-pie-register/" target="_blank" rel="noopener noreferrer">
                                <img src="<?php echo esc_url(PIEREG_PLUGIN_URL . 'assets/images/pr-guide-link-icon.png');?>" alt="">
                            </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class='pie-instructions-div'>
                    <h3><?php esc_html_e("Instructions","pie-register"); ?></h3>
                    <div style="width:1px;height:20px;"></div>
                    <div class="fields">
                
                    <?php 
                    if ( $this->checkAddonActivated( 'PayPal_Recurring' ) == true )
                    {
                    ?>
                        <p><strong>
                        <?php esc_html_e('Please follow the steps below to generate PayPal Access Token for Recurring Payments.', 'pie-register');?>
                        </strong></p>
                        <ol>
                        <li><?php esc_html_e("Login to your","pie-register"); ?> <a target="_blank" href="https://developer.paypal.com/dashboard/"><?php esc_html_e("Paypal Developer Dashboard","pie-register"); ?></a>.</li>
                        <li><?php esc_html_e("Navigate to Apps and Credentials. Create a new App","pie-register"); ?>.</li>
                        <li><?php esc_html_e("Copy your Client ID and Secret Key for the App and paste them into the Pie Register Recurring Payment settings","pie-register"); ?>.</li>
                        <li><?php esc_html_e("Save Changes, Access Token wiill be generated","pie-register"); ?>.</li>
                        </ol>
                    <?php } ?>
                    <p><strong>
                    <?php esc_html_e('Please follow the steps below to create a PayPal payment button.', 'pie-register');?>
                    </strong></p>
                    <ol>
                    <li><?php esc_html_e("Login to your","pie-register"); ?> <a target="_blank" href="https://www.paypal.com/"><?php esc_html_e("Paypal account","pie-register"); ?></a>.</li>
                    <li><?php esc_html_e("Go to PayPal Buttons and Click on","pie-register"); ?> <?php esc_html_e("Buy Now","pie-register"); ?> <?php echo apply_filters('pie_register_paypal_recurring_instructions',esc_html__("button","pie-register")); ?>.</li>
                    <li><?php esc_html_e("Give your Button a name. i.e: Website Access fee and set the price.","pie-register"); ?></li>
                    <li><?php esc_html_e('Click on Step3: Customize advance features (optional) Tab, select "Add advanced variables" checkbox and add the following snippet',"pie-register"); ?>:

                    <textarea readonly="readonly" onfocus="this.select();" onclick="this.select();" onkeypress="this.select();" style="height:100px;min-height:auto;" >rm=2<?php echo "\r\n"; ?>
                    notify_url=<?php echo ''.esc_url(trailingslashit(get_bloginfo("url"))).'';?>?action=ipn_success<?php echo "\r\n"; ?>
                    cancel_return=<?php echo ''.esc_url(trailingslashit(get_bloginfo("url"))).'';?>?action=payment_cancel<?php echo "\r\n"; ?>
                    return=<?php echo ''.esc_url(trailingslashit(get_bloginfo("url"))).'' ;?>?action=payment_success</textarea>

                    
                    </li>
                    <li><?php esc_html_e("Click Create button, On the next page, you will see the generated button code snippet like the following","pie-register"); ?>:
                        <xmp style="cursor:text;width:100%;white-space:pre-line; margin:0;">
                            <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
                            <input type="hidden" name="cmd" value="_s-xclick">
                            <input type="hidden" name="hosted_button_id" value="XXXXXXXXXX">
                            <input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_buynowCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
                            <img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
                            </form>
                        </xmp>
                    </li>
                    <li><?php esc_html_e("Copy the snippet into any text editor, extract and put the hosted_button_id value (XXXXXXXXXX) into the Above Field.","pie-register"); ?></li>
                    <li><?php esc_html_e("Save Changes, You're done!","pie-register"); ?></li>
                    </ol>
                </div>
              </div>
              </form>
            </div>
          </div>
        </div>
        </div>
    </div>
    <!--End Paypal-->
    
    <!-- start pament gateway General Settings page-->
    <div id="piereg_general_settings_payment_gateway" style="display:inline-block;">
        <div id="container">
            <div class="right_section">
                <div class="settings">
                    <div id="pie-register">
                       <form method="post" action="#piereg_general_settings_payment_gateway">
                       		<?php if( function_exists( 'wp_nonce_field' )) wp_nonce_field( 'piereg_wp_payment_gateway_settings_nonce','piereg_payment_gateway_settings_nonce'); ?>
                            <h3><?php esc_html_e("Messages",'pie-register'); ?></h3>
                            <!--	Payment success Message	-->
                            <div class="fields">
                                <label for="payment_success_msg"><?php esc_html_e('Payment Success', 'pie-register');?></label>
                                <input type="text" class="input_fields" name="payment_success_msg" id="payment_success_msg" value="<?php echo ((isset($piereg['payment_success_msg']) && !empty($piereg['payment_success_msg']))?esc_attr($piereg['payment_success_msg']):__("Payment was successful.","pie-register"));?>" />
                            </div>
                            <!--	Payment Failed Message	-->
                            <div class="fields">
                                <label for="payment_faild_msg"><?php esc_html_e('Payment Failed', 'pie-register');?></label>
                                <input type="text" class="input_fields" name="payment_faild_msg" id="payment_faild_msg" value="<?php echo ((isset($piereg['payment_faild_msg']) && !empty($piereg['payment_faild_msg']))?esc_attr($piereg['payment_faild_msg']):__("Payment failed.","pie-register"));?>" />
                            </div>
                            <!--	Renew Account Message	-->
                            <div class="fields">
                                <label for="payment_renew_msg"><?php esc_html_e('Reactivate Account', 'pie-register');?></label>
                                <input type="text" class="input_fields" name="payment_renew_msg" id="payment_renew_msg" value="<?php echo ((isset($piereg['payment_renew_msg']) && !empty($piereg['payment_renew_msg']))?esc_attr($piereg['payment_renew_msg']):__("Account needs to be activated.","pie-register"));?>" />
                            </div>
                            <!--	Alreact Activate Message	-->
                            <div class="fields">
                                <label for="payment_already_activate_msg"><?php esc_html_e('Already Active', 'pie-register');?></label>
                                <input type="text" class="input_fields" name="payment_already_activate_msg" id="payment_already_activate_msg" value="<?php echo ((isset($piereg['payment_already_activate_msg']) && !empty($piereg['payment_already_activate_msg']))?esc_attr($piereg['payment_already_activate_msg']):__("Account is already active.","pie-register"));?>" />
                            </div>
                            
			                <input name="action" value="pie_reg_update" type="hidden" />
                            <input type="hidden" name="payment_gateway_general_settings" value="1" />
                            <!-- style="background:#0C6;"-->
                            <div class="pie-save-settings-bar">
                                <div class="fields fields_submitbtn">
                                    <input name="Submit" class="submit_btn" value="<?php esc_attr_e('Save Changes','pie-register');?>" type="submit" />
                                </div>
                                <div id="piereg-guide-link">
                                    <div id="piereg-guide-link-items">
                                        <div class="piereg-guide-link-icon" data-title="<?php esc_attr_e('Click here for a detailed guide.','pie-register');?>">
                                            <a href="https://pieregister.com/documentation/payment-integration/" target="_blank" rel="noopener noreferrer">
                                                <img src="<?php echo esc_url(PIEREG_PLUGIN_URL . 'assets/images/pr-guide-link-icon.png');?>" alt="">
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="addons-container-section">
            <div class="addon-row">
                <div class="addon-column margin-right">
                    <div class="addon-container">
                        <img class="addon-img" src="<?php echo esc_url(plugins_url("assets/images/pro/6.jpg", dirname(__FILE__) )); ?>" alt="Authorize.net Payment Addon">
                        <div class="">
                            <div class="addon-content-container">
                                <h3>Authorize.net Payment Addon</h3>
                                <p>Use Authorize.net addon to process membership payments using the Authorize.net Payment Add-on.</p>
                                <?php if ( !is_plugin_active('pie-register-authorize-net/pie-register-authorize-net.php') ) { ?>
                                    <a class="get-addon" target="_blank" href="https://store.genetech.co/cart/?add-to-cart=878">Get this addon</a>
                                <?php } ?>
                                <a class="read-more get-addon view-documentation" target="_blank" href="https://pieregister.com/documentation/authorize-net-payment-gateway/?utm_source=plugindashboard&utm_medium=abouttab&utm_campaign=documentlink"> View Documentation</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="addon-column margin-right">
                    <div class="addon-container">
                        <img class="addon-img" src="<?php echo esc_url(plugins_url("assets/images/pro/13.jpg", dirname(__FILE__) )); ?>" alt="Stripe Payment Addon">
                        <div class="">
                            <div class="addon-content-container">
                                <h3>PayPal Subscriptions Addon</h3>
                                <p>Charge potential users with recurring payments using the PayPal Subscriptions Add-on.</p>
                                <?php if ( !is_plugin_active('pie-register-paypal-recurring/pie-register-paypal-recurring.php') ) { ?>
                                    <a class="get-addon" target="_blank" href="https://store.genetech.co/cart/?add-to-cart=11285">Get this addon</a>
                                <?php } ?>
                                <a class="read-more get-addon view-documentation" target="_blank" href="https://pieregister.com/documentation/how-to-use-paypal-recurring-payments-add-on/?utm_source=plugindashboard&utm_medium=abouttab&utm_campaign=documentlink"> View Documentation</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="addon-column margin-right">
                    <div class="addon-container">
                        <img class="addon-img" src="<?php echo esc_url(plugins_url("assets/images/pro/5.jpg", dirname(__FILE__) )); ?>" alt="Stripe Payment Addon">
                        <div class="">
                            <div class="addon-content-container">
                                <h3>Stripe One-Time Payment Addon</h3>
                                <p>Stripe Payment add-on allows you to charge one-time membership fees to your site.</p>
                                <?php if ( !is_plugin_active('pie-register-stripe/pie-register-stripe.php') ) { ?>
                                    <a class="get-addon" target="_blank" href="https://store.genetech.co/cart/?add-to-cart=835">Get this addon</a>
                                <?php } ?>
                                <a class="read-more get-addon view-documentation" target="_blank" href="https://pieregister.com/documentation/stripe-payment-gateway/?utm_source=plugindashboard&utm_medium=abouttab&utm_campaign=documentlink"> View Documentation</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="addon-column margin-right">
                    <div class="addon-container">
                        <img class="addon-img" src="<?php echo esc_url(plugins_url("assets/images/pro/12.jpg", dirname(__FILE__) )); ?>" alt="Stripe Payment Addon">
                        <div class="">
                            <div class="addon-content-container">
                                <h3>Stripe Recurring Payments Addon</h3>
                                <p>Charge potential users with recurring payments using the Stripe Recurring Payments Add-on.</p>
                                <?php if ( !is_plugin_active('pie-register-stripe-recurring/pie-register-stripe-recurring.php') ) { ?>
                                    <a class="get-addon" target="_blank" href="https://store.genetech.co/cart/?add-to-cart=11099">Get this addon</a>
                                <?php } ?>
                                <a class="read-more get-addon view-documentation" target="_blank" href="https://pieregister.com/documentation/how-to-use-stripe-recurring-addon/?utm_source=plugindashboard&utm_medium=abouttab&utm_campaign=documentlink"> View Documentation</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!--End General Settings-->
	
	<?php
		do_action("pie_register_Authorize_Net_paymentgateways");//Depricate
		do_action("pie_register_PaymentGateways");//Get Payment Gateways Page
    ?>

	 <!-- start pament log page-->
     <div class="settings">
        <div class="fields">
            <label>
                <strong>Note: </strong>
                <?php echo esc_html("It will take a few minutes to update new records.","pie-register"); ?>
            </label>
        </div>
    </div>
    <div id="piereg_payment_log" style="display:inline-block;">
    	<div id="pie-register-payment-log">
            <div class="settings" style="margin: 0px;width: 99%;">
               <div class="piereg-payment-log-area">
                	<table class="wp-list-table widefat fixed tableexamples piereg-payment-log-table">
                    	<thead>
                            <tr>
                                <th><?php esc_html_e("User Email","pie-register"); ?></th>
                                <th><?php esc_html_e("Method","pie-register"); ?></th>
                                <th><?php esc_html_e("Type","pie-register"); ?></th>
                                <th><?php esc_html_e("Date","pie-register"); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
						$data = get_option("piereg_payment_log_option");
						$x = 0;
						if(!empty($data) && is_array($data)){
							
							usort($data, function( $a, $b ) {
								return strtotime($b["date"]) - strtotime($a["date"]);
							});
							
							foreach( $data as $k_data=>$v_data){?>
								<tr <?php echo ( ($x % 2)?'class="alternate"':'' ); ?> data-piereg-id="piereg-id-<?php echo esc_attr(md5( $v_data['date']." | " . $v_data['email'] )); ?>" >
									<td><?php echo esc_html($v_data['email']); ?></td>
									<td><?php echo esc_html($v_data['method']); ?></td>
									<td><?php echo esc_html($v_data['type']); ?></td>
									<td><?php echo esc_html($v_data['date']); ?></td>
								</tr>
								<tr style="display:none;" class="piereg-payment-log-desc piereg-id-<?php echo esc_attr(md5( $v_data['date']." | " . $v_data['email'] )); ?>" >
									<td colspan="4"><pre><?php print_r( $v_data['responce'] ); ?></pre></td>
								</tr>
							<?php 
							$x++;
							}
						}else{?>
							<tr class="piereg-payment-log-desc" >
                                <td colspan="4" align="center" ><?php esc_html_e("No Record Found","pie-register"); ?></td>
                            </tr>
						<?php } ?>
                        </tbody>
                        <tfoot>
                        	<tr>
                                <th><?php esc_html_e("User Email","pie-register"); ?></th>
                                <th><?php esc_html_e("Method","pie-register"); ?></th>
                                <th><?php esc_html_e("Type","pie-register"); ?></th>
                                <th><?php esc_html_e("Date","pie-register"); ?></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="pie-save-settings-bar pie-payment-logs-cta-bar">
                    <div class="fields" style="width:99.9%;">
                
                        <form action="#piereg_payment_log" method="post" onsubmit="return confirm('<?php _e("Are you sure you want to clear the payment log?","pie-register"); ?>');" >
                            <?php if( function_exists( 'wp_nonce_field' )) wp_nonce_field( 'piereg_wp_payment_log','piereg_payment_log'); ?>
                            <input name="piereg_delete_payment_log_file" style="margin:0;" class="submit_btn" value="<?php esc_attr_e('Clear All','pie-register');?>" type="submit" />
                        </form>
                        
                        <!-- <form action="#piereg_payment_log" method="post"> -->
                            <?php 
                            // if( function_exists( 'wp_nonce_field' )) wp_nonce_field( 'piereg_wp_payment_log','piereg_payment_log'); 
                            ?>
                            <!-- <input name="piereg_download_payment_log_file" style="margin:0;margin-right:10px;" class="submit_btn" value="<?php esc_attr_e('Download','pie-register');?>" type="submit" /> -->
                        <!-- </form> -->
                    </div>
                    <div id="piereg-guide-link">
                        <div id="piereg-guide-link-items">
                            <div class="piereg-guide-link-icon" data-title="<?php esc_attr_e('Click here for a detailed guide.','pie-register');?>">
                                <a href="https://pieregister.com/documentation/how-to-add-membership-fees-field-using-pie-register/" target="_blank" rel="noopener noreferrer">
                                    <img src="<?php echo esc_url(PIEREG_PLUGIN_URL . 'assets/images/pr-guide-link-icon.png');?>" alt="">
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
             </div>
        </div>
    </div>
    <!--End General Settings-->

</div>
</div>