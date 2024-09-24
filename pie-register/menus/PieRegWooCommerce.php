<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<?php 
$_disable = false;
$all_plugins = get_plugins();

if (!$this->piereg_pro_is_activate())
{
    $_disable = true;
} 
?>

<fieldset class="piereg_fieldset_area-nobg" <?php disabled($_disable, true, true); ?>>
    <div id="woocommerce_menu" class="pieregister-admin">
        <div class="settings"> 
            <div id="container">
                <h2 style="width: 100%"><?php esc_html_e("WooCommerce",'pie-register') ?></h2>
                <div id="piereg-detailed-guide-link">
                    <a target="_blank" href="https://pieregister.com/documentation/how-to-use-woocommerce-addon/"><?php esc_html_e("Learn how to use WooCommerce Addon","pie-register"); ?></a>
                </div>
                <!-- PR WooCommerce AddOn Tabs -->
                <div class="invite-tabs pr-woocommerce-addon-tabs clearfix">
                    <ul>
                        <li <?php if(!isset($_GET['give_products'])){ echo 'class="invite-active"'; } ?>><a href="admin.php?page=pie-woocommerce&replace_forms"><?php esc_html_e("Replace Login and Registration Forms","pie-register") ?></a></li>
                        <li <?php if(isset($_GET['give_products'])){ echo 'class="invite-active"'; } ?>><a href="admin.php?page=pie-woocommerce&give_products"><?php esc_html_e("Assign Products to Registration Form","pie-register") ?></a></li>
                    </ul>
                </div>
            
                <?php if( (!array_key_exists('pie-register-woocommerce/pie-register-woocommerce.php', $all_plugins)) || (!is_plugin_active('pie-register-woocommerce/pie-register-woocommerce.php') ) || !$this->woocommerce_and_piereg_wc_addon_active) { ?>
                    
                    <h4 class="woocommerce-addon-message"><?php esc_html_e("If you already have the PieRegister Woocommerce Addon, head to Help>License Page and activate it.", 'pie-register');?></h4>
                    
                    <h4 class="woocommerce-addon-message"><?php esc_html_e("If you haven't downloaded the Addon, get it from ", 'pie-register');?><a target='_blank' href='https://pieregister.com/addons/woocommerce-addon/'><?php esc_html_e("here.");?></a></h4>

                    <div id="piereg-premium-link">
                        <div class="piereg-premium-link-items">
                        <div class="piereg-premium-link-icon">
                            <img src="<?php echo esc_url(PIEREG_PLUGIN_URL . 'assets/images/pr-premium-icon.png');?>" alt="">
                        </div>
                        <span><a class="piereg-premium-link-text" href="https://pieregister.com/plan-and-pricing/" target="_blank" >This feature is available in premium version</a> </span>
                        </div>
                    </div> 
                    
                <?php } else if(!$this->piereg_pro_is_activate()) { ?>
                    
                    <div id="piereg-premium-link">
                        <div class="piereg-premium-link-items">
                            <div class="piereg-premium-link-icon">
                                <img src="<?php echo esc_url(PIEREG_PLUGIN_URL . 'assets/images/pr-premium-icon.png');?>" alt="">
                            </div>
                            <span><a class="piereg-premium-link-text" href="https://pieregister.com/plan-and-pricing/" target="_blank" >This feature is available in premium version</a> </span>
                        </div>
                    </div> 
                    <?php do_action("piereg_add_addon_menu_woocommerce"); ?>

                <?php } else { 
                    // Display PR WooCommerce AddOn Settings
                    do_action("piereg_add_addon_menu_woocommerce"); 
                }
                ?>
            </div>
        </div>
    </div>
</fieldset>