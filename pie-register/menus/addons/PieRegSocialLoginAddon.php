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
        <?php if( sanitize_key($_GET['page']) == 'pie-social-login' ) { ?> 
            <div id="tab2" class="tab-content">
            <div class="addons-container-section">
                <div class="content-row">
                    <div class="pr-addons-content">
                        <h1>Social Login Addon</h1>
                        <h3 class="welcome-to-pr">Giving your users the ability to sign up/sign in with their existing social media accounts can increase membership on your website. The Social Login Add-on does precisely that. The users can sign up/sign in directly with their accounts on Facebook, Twitter, Google, LinkedIn, etc. and be a part of your website without going through the whole registration process.</h3>

                        <h1 class="pr-addons-key-features-heading">Key Features</h1>

                        <ul class="pr-addon-key-features-list">
                            <li>- <?php esc_html_e("Setup social login buttons of different sizes, or you can use custom images.","pie-register"); ?></li>
                            <li>- <?php esc_html_e("Social login buttons can be placed on any page/post using Shortcodes.","pie-register"); ?></li>
                            <li>- <?php esc_html_e("Instant registration when logging in via social profiles.","pie-register"); ?></li>
                            <li>- <?php esc_html_e("The user details will be automatically pulled from their social profiles.","pie-register"); ?></li>
                        </ul>

                        <ul class="resourceful-links">
                            <li><a href="https://store.genetech.co/cart/?add-to-cart=199" target="_blank">Purchase this Addon</a></li>
                            <li><a href="https://pieregister.com/documentation/social-login/?utm_source=plugindashboard&utm_medium=abouttab&utm_campaign=documentlink" target="_blank">View Documentation</a></li>
                        </ul>

                    </div>
                    <div class="addon-image-link">
                        <div class="addon-pr-image">
                            <a href="https://pieregister.com/documentation/how-to-create-your-first-registration-form/?utm_source=plugindashboard&utm_medium=abouttab&utm_campaign=documentlink" target="_blank">
                                <img src="<?php echo esc_url($images_url . '1.jpg' ); ?>" alt="Create a Form">    
                            </a>
                        </div>

                    </div>
                </div>
            </div>
        </div>			
                    
        <?php } ?>
    </div>
</div>