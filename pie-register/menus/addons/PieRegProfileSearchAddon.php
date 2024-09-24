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
        <?php if( sanitize_key($_GET['page']) == 'pie-profile-search' ) { ?> 
            <div id="tab2" class="tab-content">
            <div class="addons-container-section">
                <div class="content-row">
                    <div class="pr-addons-content">
                        <h1>Profile Search Addon</h1>
                        <h3 class="welcome-to-pr">Profile Search is necessary and can be a game-changer for community websites. It allows the users to find each other on your website, bring about more user interaction, and create user-generated content. With the Profile Search add-on, you can customize how the search results look or what information will be shown immediately. This add-on also lets the user customize their search settings.</h3>

                        <h1 class="pr-addons-key-features-heading">Key Features</h1>

                        <ul class="pr-addon-key-features-list">
                            <li>- <?php esc_html_e("Customize search settings from the Profile Search Settings section.","pie-register"); ?></li>
                            <li>- <?php esc_html_e("Enable Default member directories to show the registered users in a list on the Search Profile Form.","pie-register"); ?></li>
                            <li>- <?php esc_html_e("You can also select where to display search results.","pie-register"); ?></li>
                            <li>- <?php esc_html_e("Admin can also customize the design of the Profile Result Page.","pie-register"); ?></li>
                            <li>- <?php esc_html_e("Restrict only logged-in users to Search User Profile.","pie-register"); ?></li>
                            <li>- <?php esc_html_e("Set whether to exclude users by their username or roles from search results.","pie-register"); ?></li>
                        </ul>

                        <ul class="resourceful-links">
                            <li><a href="https://store.genetech.co/cart/?add-to-cart=198" target="_blank">Purchase this Addon</a></li>
                            <li><a href="https://pieregister.com/documentation/profile-search/?utm_source=plugindashboard&utm_medium=abouttab&utm_campaign=documentlink" target="_blank">View Documentation</a></li>
                        </ul>

                    </div>
                    <div class="addon-image-link">
                        <div class="addon-pr-image">
                            <a href="https://pieregister.com/documentation/how-to-create-your-first-registration-form/?utm_source=plugindashboard&utm_medium=abouttab&utm_campaign=documentlink" target="_blank">
                                <img src="<?php echo esc_url($images_url . '2.jpg' ); ?>" alt="Create a Form">    
                            </a>
                        </div>

                    </div>
                </div>
            </div>
        </div>			
                    
        <?php } ?>
    </div>
</div>