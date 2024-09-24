<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<?php $piereg = $this->get_pr_global_options(); ?>
<div class="forms_max_label">
<form action="" method="post" id="frm_settings_overrides">
  <?php if( function_exists( 'wp_nonce_field' )) wp_nonce_field( 'piereg_wp_settings_overrides','piereg_settings_overrides'); ?>
  <div class="fields">
    <div class="radio_fields">
      <input type="checkbox" name="redirect_user" id="redirect_user" value="1" <?php checked($piereg['redirect_user']=="1", true)?> />	
    </div>
    <label for="redirect_user" class="label_mar_top">
      <?php esc_html_e("When a user is logged in, do not show login, registration and forgot password links.",'pie-register') ?>
    </label>
  </div>
  <div class="fields">
    <div class="radio_fields">
      <input type="checkbox" name="show_admin_bar" id="show_admin_bar" value="1" <?php checked($piereg['show_admin_bar']=="1", true)?> />	
    </div>
    <label for="show_admin_bar" class="label_mar_top">
      <?php esc_html_e("Do not show admin bar.",'pie-register') ?>
    </label>
  </div>
  <div class="fields">
    <div class="radio_fields">
      <input type="checkbox" name="block_WP_profile" id="block_WP_profile" value="1" <?php checked($piereg['block_WP_profile']=="1", true)?> />	
    </div>
    <label for="block_WP_profile" class="label_mar_top">
      <?php esc_html_e("Redirect users to custom profile page, if one exists.",'pie-register') ?>
    </label>
  </div>
  <div class="fields">
    <div class="radio_fields">
      <input type="checkbox" name="block_wp_login" id="block_wp_login" value="1" <?php checked($piereg['block_wp_login']=="1", true)?> />	
    </div>
    <label for="block_wp_login" class="label_mar_top">
      <?php esc_html_e("Do not allow users to login from the WordPress login page. Note: You must select an alternate login page.",'pie-register') ?>
    </label>
  </div>
  <div class="fields">
    <div class="radio_fields">
      <input type="checkbox" name="allow_pr_edit_wplogin" id="allow_pr_edit_wplogin" value="1" <?php checked($piereg['allow_pr_edit_wplogin']=="1", true)?> />	
    </div>
    <label for="allow_pr_edit_wplogin" class="label_mar_top">
      <?php esc_html_e("Allow Pie Register to add header Footer in wp-login.php.",'pie-register') ?>
    </label>
  </div>
  <div class="pie-save-settings-bar">
    <div class="fields">
      <input name="action" value="pie_reg_settings" type="hidden" />
      <input type="submit" class="submit_btn submit_btn_mar_ryt" value="<?php esc_attr_e("Save Settings","pie-register"); ?>" />
    </div>
    <div id="piereg-guide-link">
      <div id="piereg-guide-link-items">
        <div class="piereg-guide-link-icon" data-title="<?php esc_attr_e('Click here for a detailed guide.','pie-register');?>">
            <a href="https://pieregister.com/documentation/settings/" target="_blank" rel="noopener noreferrer">
            <img src="<?php echo esc_url(PIEREG_PLUGIN_URL . 'assets/images/pr-guide-link-icon.png');?>" alt="">
            </a>
        </div>
      </div>
    </div>  
  </div>
</form>
</div>