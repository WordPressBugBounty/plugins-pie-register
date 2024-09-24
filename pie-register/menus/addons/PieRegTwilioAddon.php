<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<?php 
	$action =	"";
    $active	= 'class="selected"';
	if(isset($_GET['tab']))
        $action	= sanitize_key($_GET['tab']);

    $images_url = PIEREG_PLUGIN_URL . 'assets/images/pro/';
	?>

<div id="container"  class="pieregister-admin addons-page-admin">
    <div class="pane">
        <?php if( sanitize_key($_GET['page']) == 'pie-twilio' ) { ?> 
            <div id="tab2" class="tab-content">
            <div class="addons-container-section">
                <div class="content-row">
                    <div class="pr-addons-content">
                        <h1>Twilio Login Addon</h1>
                        <h3 class="welcome-to-pr">With the help of Twilio, two-step authentication can add an extra layer of security to your website. Using this add-on, you can connect your Twilio account with the Pie Register plugin to add two-factor authentication to your website and ensure a secure login process. It helps you authenticate the user by sending a PIN to their number, which is required at the login time. Also, admins can be notified when a new user registers.</h3>

                        <h1 class="pr-addons-key-features-heading">Key Features</h1>

                        <ul class="pr-addon-key-features-list">
                            <li>- <?php esc_html_e("Two-step authentication field on the registration form for a user to enter their cell phone number to get a pin number.","pie-register"); ?></li>
                            <li>- <?php esc_html_e("Enter Pin Expire Time in minutes.","pie-register"); ?></li>
                            <li>- <?php esc_html_e("Receive a notification when a user registers.","pie-register"); ?></li>
                            <li>- <?php esc_html_e("Enter Admin Mobile Number to receive registration notifications.","pie-register"); ?></li>
                        </ul>

                        <ul class="resourceful-links">
                            <li><a href="https://store.genetech.co/cart/?add-to-cart=200" target="_blank">Purchase this Addon</a></li>
                            <li><a href="https://pieregister.com/documentation/two-step-login-via-sms-twilio/?utm_source=plugindashboard&utm_medium=abouttab&utm_campaign=documentlink" target="_blank">View Documentation</a></li>
                        </ul>

                    </div>
                    <div class="addon-image-link">
                        <div class="addon-pr-image">
                            <a href="https://pieregister.com/documentation/how-to-create-your-first-registration-form/?utm_source=plugindashboard&utm_medium=abouttab&utm_campaign=documentlink" target="_blank">
                                <img src="<?php echo esc_url($images_url . '3.jpg' ); ?>" alt="Create a Form">    
                            </a>
                        </div>

                    </div>
                </div>
            </div>
        </div>			
                    
        <?php } ?>
    </div>
</div>