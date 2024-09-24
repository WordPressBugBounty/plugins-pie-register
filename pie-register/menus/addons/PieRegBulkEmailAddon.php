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
        <?php if( sanitize_key($_GET['page']) == 'pie-bulkemail' ) { ?> 
            <div id="tab2" class="tab-content">
            <div class="addons-container-section">
                <div class="content-row">
                    <div class="pr-addons-content">
                        <h1>Bulk Email Addon</h1>
                        <h3 class="welcome-to-pr">Bulk Email Add-on gives Admin the ability to send emails in bulk to all the registered users or Based on User Roles. The Email can be sent right away, or you can schedule to send it later. It can also be saved as a draft for later use and can be edited before sending. Once sent, the message goes into the Sent Items tab, with the columns displaying Form Name, Subject, and Sent Date.</h3>

                        <h1 class="pr-addons-key-features-heading">Key Features</h1>

                        <ul class="pr-addon-key-features-list">
                            <li>- <?php esc_html_e("Allows Admin to broadcast an Email message to all the registered users or based on User Roles of a specific form.","pie-register"); ?></li>
                            <li>- <?php esc_html_e("Admin can add replacement keys for Name, Email.","pie-register"); ?></li>
                            <li>- <?php esc_html_e("You can schedule the emails or send them right away.","pie-register"); ?></li>
                            <li>- <?php esc_html_e("Messages in the Draft tab are saved with the details of Subject, Form Name, Role, Date Created and Edit/Delete.","pie-register"); ?></li>
                            <li>- <?php esc_html_e("Messages in the Sent Items tab are saved with the Subject, Form Name, Role, Sent Date, and Duplicate/Delete.","pie-register"); ?></li>
                            <li>- <?php esc_html_e("Admin can view the list of all sent emails.","pie-register"); ?></li>
                            <li>- <?php esc_html_e("Each email sent will have the respective User name and Email for a personalized touch to the message.","pie-register"); ?></li>
                        </ul>

                        <ul class="resourceful-links">
                            <li><a href="https://store.genetech.co/cart/?add-to-cart=1190" target="_blank">Purchase this Addon</a></li>
                            <li><a href="https://pieregister.com/documentation/how-to-send-bulk-e-mails-using-pie-register/?utm_source=plugindashboard&utm_medium=abouttab&utm_campaign=documentlink" target="_blank">View Documentation</a></li>
                        </ul>

                    </div>
                    <div class="addon-image-link">
                        <div class="addon-pr-image">
                            <a href="https://pieregister.com/documentation/how-to-create-your-first-registration-form/?utm_source=plugindashboard&utm_medium=abouttab&utm_campaign=documentlink" target="_blank">
                                <img src="<?php echo esc_url($images_url . '8.jpg' ); ?>" alt="Create a Form">    
                            </a>
                        </div>

                    </div>
                </div>
            </div>
        </div>			   
        <?php } ?>
    </div>
</div>