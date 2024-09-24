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
        <?php if( sanitize_key($_GET['page']) == 'pie-settings' ) { ?> 
            <div id="tab2" class="tab-content">
            <div class="addons-container-section">
                <div class="content-row">
                    <div class="pr-addons-content">
                        <h1>Geolocation Addon</h1>
                        <h3 class="welcome-to-pr">The Geolocation add-on allows you to track user locations with WordPress form submissions. Admin can collect and store website visitor’s data along with their form submission data.</h3>

                        <h1 class="pr-addons-key-features-heading">KeyFeatures</h1>

                        <ul class="pr-addon-key-features-list">
                            <li>- <?php esc_html_e("Collects Geolocation data of your users who registered on your site.","pie-register"); ?></li>
                            <li>- <?php esc_html_e("Information like Country, City, Zip Code, Latitude/Longitude.","pie-register"); ?></li>
                            <li>- <?php esc_html_e("Location map for the user will be accessible through Google Map API.","pie-register"); ?></li>
                        </ul>

                        <ul class="resourceful-links">
                            <li><a href="https://store.genetech.co/cart/?add-to-cart=1190" target="_blank">Purchase this Addon</a></li>
                            <li><a href="https://pieregister.com/documentation/how-to-install-and-use-the-geolocation-add-on-with-pie-register/?utm_source=plugindashboard&utm_medium=abouttab&utm_campaign=documentlink" target="_blank">View Documentation</a></li>
                        </ul>

                    </div>
                    <div class="addon-image-link">
                        <div class="addon-pr-image">
                            <a href="https://pieregister.com/documentation/how-to-create-your-first-registration-form/?utm_source=plugindashboard&utm_medium=abouttab&utm_campaign=documentlink" target="_blank">
                                <img src="<?php echo esc_url($images_url . '7.jpg' ); ?>" alt="Create a Form">    
                            </a>
                        </div>

                    </div>
                </div>
            </div>
        </div>			
                    
        <?php } ?>
    </div>
</div>