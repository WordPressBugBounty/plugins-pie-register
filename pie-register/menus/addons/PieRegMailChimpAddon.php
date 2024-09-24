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
        <?php if( sanitize_key($_GET['page']) == 'pie-export-users' ) { ?> 
            <div id="tab2" class="tab-content">
            <div class="addons-container-section">
                <div class="content-row">
                    <div class="pr-addons-content">
                        <h1>MailChimp Addon</h1>
                        <h3 class="welcome-to-pr">MailChimp is an excellent tool to create and manage email marketing campaigns, and this add-on can facilitate precisely that. Once a user signs up, it is added to your MailChimp list, and then all the communication going out from you can be tracked via MailChimp’s dashboard. With this add-on, you can also add your previous users to the MailChimp mailing list to keep track of all your old and new users.</h3>

                        <h1 class="pr-addons-key-features-heading">Key Features</h1>

                        <ul class="pr-addon-key-features-list">
                            <li>- <?php esc_html_e("Single/Double user opt-in with verification.","pie-register"); ?></li>
                            <li>- <?php esc_html_e("Custom opt-in message. ","pie-register"); ?></li>
                            <li>- <?php esc_html_e("Settings for each Registration Form. ","pie-register"); ?></li>
                            <li>- <?php esc_html_e("Merge fields and Map fields to Mailchimp by form. ","pie-register"); ?></li>
                            <li>- <?php esc_html_e("Import users in bulk by form. ","pie-register"); ?></li>
                            <li>- <?php esc_html_e("Admin receives an email if the user registering fails to subscribe for any reason. ","pie-register"); ?></li>
                            <li>- <?php esc_html_e("Admin can download a CSV with errors for users who weren’t bulk imported. ","pie-register"); ?></li>
                        </ul>

                        <ul class="resourceful-links">
                            <li><a href="https://store.genetech.co/cart/?add-to-cart=197" target="_blank">Purchase this Addon</a></li>
                            <li><a href="https://pieregister.com/documentation/mailchimp/?utm_source=plugindashboard&utm_medium=abouttab&utm_campaign=documentlink" target="_blank">View Documentation</a></li>
                        </ul>

                    </div>
                    <div class="addon-image-link">
                        <div class="addon-pr-image">
                            <a href="https://pieregister.com/documentation/how-to-create-your-first-registration-form/?utm_source=plugindashboard&utm_medium=abouttab&utm_campaign=documentlink" target="_blank">
                                <img src="<?php echo esc_url($images_url . '4.jpg' ); ?>" alt="Create a Form">    
                            </a>
                        </div>

                    </div>
                </div>
            </div>
        </div>			
                    
        <?php } ?>
    </div>
</div>