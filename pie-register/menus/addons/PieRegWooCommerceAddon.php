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
        <?php if( sanitize_key($_GET['page']) == 'pie-woocommerce' ) { ?> 
            <div id="tab2" class="tab-content">
            <div class="addons-container-section">
                <div class="content-row">
                    <div class="pr-addons-content">
                        <h1>WooCommerce Addon</h1>
                        <h3 class="welcome-to-pr">With the WooCommerce Add-on, you can add billing and shipping addresses fields to your Pie Register registration form and send a free product as a gift to your newest users once they fill out your registration form. You can choose to hide or display user data from certain fields on your WooCommerce checkout page. Admins can also replace the default WooCommerce login and registration forms with Pie Register login and registration forms.</h3>

                        <h1 class="pr-addons-key-features-heading">Key Features</h1>

                        <ul class="pr-addon-key-features-list">
                            <li>- <?php esc_html_e("Add fields for Billing and Shipping address to your Pie Register registration form.","pie-register"); ?></li>
                            <li>- <?php esc_html_e("View/edit the information added in the billing and shipping fields via the addon on the Pie Register ‘Profile’ page.","pie-register"); ?></li>
                            <li>- <?php esc_html_e("Hide or display user data for specific Pie Register fields on the WooCommerce checkout page.","pie-register"); ?></li>
                            <li>- <?php esc_html_e("Replace default WooCommerce login and registration forms with Pie Register login and registration forms.","pie-register"); ?></li>
                            <li>- <?php esc_html_e("Send a free product as a gift to your newly registered users when they fill the registration form.","pie-register"); ?></li>
                        </ul>

                        <ul class="resourceful-links">
                            <li><a href="https://store.genetech.co/cart/?add-to-cart=8226" target="_blank">Purchase this Addon</a></li>
                            <li><a href="https://pieregister.com/documentation/how-to-use-woocommerce-addon/?utm_source=plugindashboard&utm_medium=abouttab&utm_campaign=documentlink" target="_blank">View Documentation</a></li>
                        </ul>

                    </div>
                    <div class="addon-image-link">
                        <div class="addon-pr-image">
                            <a href="https://pieregister.com/documentation/how-to-create-your-first-registration-form/?utm_source=plugindashboard&utm_medium=abouttab&utm_campaign=documentlink" target="_blank">
                                <img src="<?php echo esc_url($images_url . '9.jpg' ); ?>" alt="Create a Form">    
                            </a>
                        </div>

                    </div>
                </div>
            </div>
        </div>			
                    
        <?php } ?>
    </div>
</div>