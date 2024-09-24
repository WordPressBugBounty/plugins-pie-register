<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<style>
.captcha_in_login_attempts {
	width:25% !important;
}
</style>
<?php $piereg = $this->get_pr_global_options(); 

	$_disable 			= true;
?>
<div class="forms_max_label">
<form method="post" action="" id="piereg_form_general_settings_page" onsubmit="return validateSettingsSecurity();">
  <?php if( function_exists( 'wp_nonce_field' )) wp_nonce_field( 'piereg_wp_settings_security_b','piereg_settings_security_b'); ?>
  <h3>
    <?php esc_html_e("Login Form",'pie-register'); ?>
  </h3>
  <div id="piereg-premium-link">
      <div class="piereg-premium-link-items">
          <div class="piereg-premium-link-icon">
              <img src="<?php echo esc_url(PIEREG_PLUGIN_URL . 'assets/images/pr-premium-icon.png');?>" alt="">
          </div>
          <span><a class="piereg-premium-link-text" href="https://pieregister.com/plan-and-pricing/" target="_blank" >This feature is available in premium version</a> </span>
      </div>
  </div>
  <div class="fields">
    <label for="capthca_in_login_form">
      <?php esc_html_e("Show Captcha on login form?",'pie-register') ?>
    </label>
    <div class="radio_fields">
      <input type="radio" name="captcha_in_login_value" id="captcha_in_login_value_0" class="captcha_in_login_value" value="0" checked="checked" <?php checked(isset($piereg['captcha_in_login_value']) && $piereg['captcha_in_login_value'] == '0', true); ?> />
      <label for="captcha_in_login_value_0">No</label>
      <input type="radio" name="captcha_in_login_value" id="captcha_in_login_value_1" class="captcha_in_login_value" value="1" <?php checked(isset($piereg['captcha_in_login_value']) && $piereg['captcha_in_login_value'] == '1', true); ?> />
      <label for="captcha_in_login_value_1">Yes</label>
    </div>
  </div>
  <div class="fields piereg_captcha_label_show" <?php echo ((!isset($piereg['captcha_in_login_value']) || isset($piereg['captcha_in_login_value']) && $piereg['captcha_in_login_value'] == 0 )?'style="display:none;"':'') ?>>
    <label for="capthca_in_login_label">
      <?php esc_html_e("Captcha Label",'pie-register') ?>
    </label>
    <input type="text" name="capthca_in_login_label" id="capthca_in_login_label" value="<?php echo esc_attr($piereg['capthca_in_login_label']); ?>" class="input_fields" />
  </div>
  <div class="fields piereg_captcha_type_show" <?php echo ((!isset($piereg['captcha_in_login_value']) || isset($piereg['captcha_in_login_value']) && $piereg['captcha_in_login_value'] == 0 )?'style="display:none;"':'') ?>>
  	<div class="flt_lft width_full">
    <label for="piereg_capthca_in_login">
      <?php esc_html_e("Captcha Type",'pie-register') ?>
    </label>
    <select name="capthca_in_login" id="piereg_capthca_in_login">
      <option value="3" <?php selected((isset($piereg['capthca_in_login']) && $piereg['capthca_in_login'] == 3 ), true); ?>>
      <?php esc_html_e("No Captcha ReCaptcha",'pie-register') ?>
      </option>
      
      <option value="2" <?php selected((isset($piereg['capthca_in_login']) && $piereg['capthca_in_login'] == 2 ), true); ?>>
      <?php esc_html_e("Math Captcha",'pie-register') ?>
      </option>
    </select>
    </div>
    <span class="quotation">
    <?php esc_html_e("Select Captcha type to show on the login form.",'pie-register') ?>
    </span>
    </div>
  
      <div class="fields">
        <div class="container_attemps">
        <input <?php disabled($_disable, true); ?> type="checkbox" name="piereg_security_attempts_login_value" value="1" <?php checked(isset($piereg['piereg_security_attempts_login_value']) && $piereg['piereg_security_attempts_login_value'] == '1', true); ?> />
         <?php esc_html_e("Lockout user for",'pie-register') ?>
        <select <?php disabled($_disable, true); ?> class="security_attempts_drop" name="security_attempts_login_time" id="piereg_security_attempts_login_time">
          <option value="1" <?php selected((isset($piereg['security_attempts_login_time']) && $piereg['security_attempts_login_time'] == 1 ), true); ?>>
          <?php esc_html_e("1","pie-register"); ?>
          </option>
          <option value="2" <?php selected((isset($piereg['security_attempts_login_time']) && $piereg['security_attempts_login_time'] == 2 ), true); ?>>
          <?php esc_html_e("2","pie-register"); ?>
          </option>
          <option value="3" <?php selected((isset($piereg['security_attempts_login_time']) && $piereg['security_attempts_login_time'] == 3 ), true); ?>>
          <?php esc_html_e("3","pie-register"); ?>
          </option>
          <option value="4" <?php selected((isset($piereg['security_attempts_login_time']) && $piereg['security_attempts_login_time'] == 4 ), true); ?>>
          <?php esc_html_e("4","pie-register"); ?>
          </option>
          <option value="5" <?php selected((isset($piereg['security_attempts_login_time']) && $piereg['security_attempts_login_time'] == 5 ), true); ?>>
          <?php esc_html_e("5","pie-register"); ?>
          </option>
          <option value="7" <?php selected((isset($piereg['security_attempts_login_time']) && $piereg['security_attempts_login_time'] == 7 ), true); ?>>
          <?php esc_html_e("7","pie-register"); ?>
          </option>
          <option value="10" <?php selected((isset($piereg['security_attempts_login_time']) && $piereg['security_attempts_login_time'] == 10 ), true); ?>>
          <?php esc_html_e("10","pie-register"); ?>
          </option>
          <option value="30" <?php selected((isset($piereg['security_attempts_login_time']) && $piereg['security_attempts_login_time'] == 30 ), true); ?>>
          <?php esc_html_e("30","pie-register"); ?>
          </option>
          <option value="60" <?php selected((isset($piereg['security_attempts_login_time']) && $piereg['security_attempts_login_time'] == 60 ), true); ?>>
          <?php esc_html_e("60","pie-register"); ?>
          </option>
          <option value="90" <?php selected((isset($piereg['security_attempts_login_time']) && $piereg['security_attempts_login_time'] == 90 ), true); ?>>
          <?php esc_html_e("90","pie-register"); ?>
          </option>
          <option value="120" <?php selected((isset($piereg['security_attempts_login_time']) && $piereg['security_attempts_login_time'] == 120 ), true); ?>>
          <?php esc_html_e("120","pie-register"); ?>
          </option>
        </select>
        <?php esc_html_e("minutes after",'pie-register') ?>
        <select <?php disabled($_disable, true); ?> class="security_attempts_drop" name="security_attempts_login" id="piereg_security_attempts_login">
          <option value="2" <?php selected((isset($piereg['security_attempts_login']) && $piereg['security_attempts_login'] == 2 ), true); ?>>
          <?php esc_html_e("2","pie-register"); ?>
          </option>
          <option value="3" <?php selected((isset($piereg['security_attempts_login']) && $piereg['security_attempts_login'] == 3 ), true); ?>>
          <?php esc_html_e("3","pie-register"); ?>
          </option>
          <option value="4" <?php selected((isset($piereg['security_attempts_login']) && $piereg['security_attempts_login'] == 4 ), true); ?>>
          <?php esc_html_e("4","pie-register"); ?>
          </option>
          <option value="5" <?php selected((isset($piereg['security_attempts_login']) && $piereg['security_attempts_login'] == 5 ), true); ?>>
          <?php esc_html_e("5","pie-register"); ?>
          </option>
          <option value="7" <?php selected((isset($piereg['security_attempts_login']) && $piereg['security_attempts_login'] == 7 ), true); ?>>
          <?php esc_html_e("7","pie-register"); ?>
          </option>
          <option value="10" <?php selected((isset($piereg['security_attempts_login']) && $piereg['security_attempts_login'] == 10 ), true); ?>>
          <?php esc_html_e("10","pie-register"); ?>
          </option>
          <option value="15" <?php selected((isset($piereg['security_attempts_login']) && $piereg['security_attempts_login'] == 15 ), true); ?>>
          <?php esc_html_e("15","pie-register"); ?>
          </option>
        </select>
        <?php esc_html_e("invalid login attempts",'pie-register') ?>.</div> </div>
   
    <hr class="seperator" />
  <h3>
    <?php esc_html_e("Forgot Password Form",'pie-register'); ?>
  </h3>
  <div id="piereg-premium-link">
      <div class="piereg-premium-link-items">
          <div class="piereg-premium-link-icon">
              <img src="<?php echo esc_url(PIEREG_PLUGIN_URL . 'assets/images/pr-premium-icon.png');?>" alt="">
          </div>
          <span><a class="piereg-premium-link-text" href="https://pieregister.com/plan-and-pricing/" target="_blank" >This feature is available in premium version</a> </span>
      </div>
  </div>
  <div class="fields">
    <label for="capthca_in_forgot_form" class="limit_width">
      <?php esc_html_e("Show Captcha on forgot password form?",'pie-register') ?>
    </label>
    <div class="radio_fields">
      <input type="radio" name="captcha_in_forgot_value" id="captcha_in_forgot_value_0" class="captcha_in_forgot_value" value="0" checked="checked" <?php checked(isset($piereg['captcha_in_forgot_value']) && $piereg['captcha_in_forgot_value'] == '0', true); ?> />
      <label for="captcha_in_forgot_value_0">No</label>
      <input type="radio" name="captcha_in_forgot_value" id="captcha_in_forgot_value_1" class="captcha_in_forgot_value" value="1" <?php checked(isset($piereg['captcha_in_forgot_value']) && $piereg['captcha_in_forgot_value'] == '1', true); ?> />
      <label for="captcha_in_forgot_value_1">Yes</label>
    </div>
  </div>
  <div class="fields piereg_capthca_forgot_pass_label_show" <?php echo (!isset($piereg['captcha_in_forgot_value']) || (isset($piereg['captcha_in_forgot_value']) && $piereg['captcha_in_forgot_value'] == 0 )?'style="display:none;"':'') ?>>
    <label for="capthca_in_forgot_pass_label">
      <?php esc_html_e("Captcha Label",'pie-register') ?>
    </label>
    <input type="text" name="capthca_in_forgot_pass_label" id="capthca_in_forgot_pass_label" value="<?php echo esc_attr($piereg['capthca_in_forgot_pass_label']); ?>" class="input_fields" />
  </div>
  <div class="fields piereg_captcha_forgot_pass_type_show" <?php echo (!isset($piereg['captcha_in_forgot_value']) || (isset($piereg['captcha_in_forgot_value']) && $piereg['captcha_in_forgot_value'] == 0 )?'style="display:none;"':'') ?>>
  	<div class="flt_lft width_full">
    <label for="piereg_capthca_in_forgot_pass">
      <?php esc_html_e("Captcha Type",'pie-register') ?>
    </label>
    <select name="capthca_in_forgot_pass" id="piereg_capthca_in_forgot_pass">
      <option value="3" <?php selected((isset($piereg['capthca_in_forgot_pass']) && $piereg['capthca_in_forgot_pass'] == 3), true ) ?>>
      <?php esc_html_e("No Catpcha ReCaptcha",'pie-register') ?>
      </option>
      <!--
      <option value="1" <?php //echo ((isset($piereg['capthca_in_forgot_pass']) && $piereg['capthca_in_forgot_pass'] == 1 )?'selected="selected"':'') ?>>
      <?php //esc_html_e("Classic ReCaptcha",'pie-register') ?>
      </option>-->
      <option value="2" <?php selected((isset($piereg['capthca_in_forgot_pass']) && $piereg['capthca_in_forgot_pass'] == 2 ), true) ?>>
      <?php esc_html_e("Math Captcha",'pie-register') ?>
      </option>
    </select>
    </div>
    <span class="quotation">
    <?php esc_html_e("Select Captcha type to show on the Password Reset form.",'pie-register') ?>
    </span>
    </div>
  
      <div class="fields">
        <div class="container_attemps">
        <input <?php disabled($_disable, true); ?> type="checkbox" name="piereg_security_attempts_forgot_value" value="1" <?php checked(isset($piereg['piereg_security_attempts_forgot_value']) && $piereg['piereg_security_attempts_forgot_value'] == '1', true); ?> />
        <?php esc_html_e("Lockout user for",'pie-register') ?>
        <select <?php disabled($_disable, true); ?> class="security_attempts_drop" name="security_attempts_forgot_time" id="piereg_security_attempts_forgot_time">
          <option value="1" <?php selected((isset($piereg['security_attempts_forgot_time']) && $piereg['security_attempts_forgot_time'] == 1 ), true) ?>>
          <?php esc_html_e("1","pie-register"); ?>
          </option>
          <option value="2" <?php selected((isset($piereg['security_attempts_forgot_time']) && $piereg['security_attempts_forgot_time'] == 2 ), true) ?>>
          <?php esc_html_e("2","pie-register"); ?>
          </option>
          <option value="3" <?php selected((isset($piereg['security_attempts_forgot_time']) && $piereg['security_attempts_forgot_time'] == 3 ), true) ?>>
          <?php esc_html_e("3","pie-register"); ?>
          </option>
          <option value="4" <?php selected((isset($piereg['security_attempts_forgot_time']) && $piereg['security_attempts_forgot_time'] == 4 ), true) ?>>
          <?php esc_html_e("4","pie-register"); ?>
          </option>
          <option value="5" <?php selected((isset($piereg['security_attempts_forgot_time']) && $piereg['security_attempts_forgot_time'] == 5 ), true) ?>>
          <?php esc_html_e("5","pie-register"); ?>
          </option>
          <option value="7" <?php selected((isset($piereg['security_attempts_forgot_time']) && $piereg['security_attempts_forgot_time'] == 7 ), true) ?>>
          <?php esc_html_e("7","pie-register"); ?>
          </option>
          <option value="10" <?php selected((isset($piereg['security_attempts_forgot_time']) && $piereg['security_attempts_forgot_time'] == 10 ), true) ?>>
          <?php esc_html_e("10","pie-register"); ?>
          </option>
          <option value="30" <?php selected((isset($piereg['security_attempts_forgot_time']) && $piereg['security_attempts_forgot_time'] == 30 ), true) ?>>
          <?php esc_html_e("30","pie-register"); ?>
          </option>
          <option value="60" <?php selected((isset($piereg['security_attempts_forgot_time']) && $piereg['security_attempts_forgot_time'] == 60 ), true) ?>>
          <?php esc_html_e("60","pie-register"); ?>
          </option>
          <option value="90" <?php selected((isset($piereg['security_attempts_forgot_time']) && $piereg['security_attempts_forgot_time'] == 90 ), true) ?>>
          <?php esc_html_e("90","pie-register"); ?>
          </option>
          <option value="120" <?php selected((isset($piereg['security_attempts_forgot_time']) && $piereg['security_attempts_forgot_time'] == 120 ), true) ?>>
          <?php esc_html_e("120","pie-register"); ?>
          </option>
        </select>
        <?php esc_html_e("minutes after",'pie-register') ?>
        <select <?php disabled($_disable, true); ?> class="security_attempts_drop" name="security_attempts_forgot" id="piereg_security_attempts_forgot">
          <option value="2" <?php selected((isset($piereg['security_attempts_forgot']) && $piereg['security_attempts_forgot'] == 2 ), true) ?>>
          <?php esc_html_e("2","pie-register"); ?>
          </option>
          <option value="3" <?php selected((isset($piereg['security_attempts_forgot']) && $piereg['security_attempts_forgot'] == 3 ), true) ?>>
          <?php esc_html_e("3","pie-register"); ?>
          </option>
          <option value="4" <?php selected((isset($piereg['security_attempts_forgot']) && $piereg['security_attempts_forgot'] == 4 ), true) ?>>
          <?php esc_html_e("4","pie-register"); ?>
          </option>
          <option value="5" <?php selected((isset($piereg['security_attempts_forgot']) && $piereg['security_attempts_forgot'] == 5 ), true) ?>>
          <?php esc_html_e("5","pie-register"); ?>
          </option>
          <option value="7" <?php selected((isset($piereg['security_attempts_forgot']) && $piereg['security_attempts_forgot'] == 7 ), true) ?>>
          <?php esc_html_e("7","pie-register"); ?>
          </option>
          <option value="10" <?php selected((isset($piereg['security_attempts_forgot']) && $piereg['security_attempts_forgot'] == 10 ), true) ?>>
          <?php esc_html_e("10","pie-register"); ?>
          </option>
          <option value="15" <?php selected((isset($piereg['security_attempts_forgot']) && $piereg['security_attempts_forgot'] == 15 ), true) ?>>
          <?php esc_html_e("15","pie-register"); ?>
          </option>
        </select>
        <?php esc_html_e("invalid login attempts",'pie-register') ?> .</div>
        </div>
   
    <hr class="seperator" />
      <?php
        $this->require_once_file(PIEREG_DIR_NAME.'/menus/settings/PieRegSecurityBasicCaptcha.php');
      ?>
    <hr class="seperator" />
  <h3>
    <?php esc_html_e("User Verification",'pie-register'); ?>
  </h3>
  <div class="fields">
   <p>
      <?php esc_html_e("Note: Admin and Email verifications will not work when the payment gateway is enabled.",'pie-register') ?>
    </p>
  </div>
  <div class="fields">
    <label>
      <?php esc_html_e("New User Verification",'pie-register') ?>
    </label>
    <div>
      <select name="verification" id="verification_2" >
        <option value="0" <?php selected(($piereg['verification']=="0"), true);?> >
        <?php esc_html_e("Disable","pie-register"); ?>
        </option>
        <option value="1" <?php selected(($piereg['verification']=="1"), true);?> >
        <?php esc_html_e("Admin Approval","pie-register"); ?>
        </option>
        <option value="2" <?php selected(($piereg['verification']=="2"), true);?> >
        <?php esc_html_e("Verify Email Address","pie-register"); ?>
        </option>
        <option <?php disabled($_disable, true); ?> value="3" <?php selected(($piereg['verification']=="3"), true);?> >
        <?php esc_html_e("Admin Approval AND Verify Email Address - [PRO]","pie-register"); ?>
        </option>
      </select>
    </div>
    <div class="verification_data"> <span><strong>Admin Approval</strong>
      <?php esc_html_e("- Site admin has to approve each new user.",'pie-register') ?>
      </span> <br />
      <span>
      <strong>Verify Email Address</strong>
      <?php esc_html_e("- Require new users to click on a link sent to their email to activate their account.",'pie-register') ?>
      </span>
      <p><strong>
        <?php esc_html_e("Grace Period (days)",'pie-register') ?>
        :
        <input type="text" name="grace_period" class="input_fields2" value="<?php echo esc_attr($piereg['grace_period'])?>" />
        </strong></p>
      <p>
        <?php esc_html_e("User Ids that are not verified within the grace period are deleted at the next logon attempt. 0 (Zero) means unlimited, no grace period.",'pie-register') ?>
      </p>
    </div>
  </div>
  <div class="fields">
    <label>
      <?php esc_html_e("Verify Email Address Change",'pie-register') ?>
    </label>
    <div class="radio_fields max_label_300">
      <input type="radio" value="1" name="email_edit_verification_step" id="email_edit_verification_1" <?php checked($piereg['email_edit_verification_step']=="1", true) ?> class="step_email_edit_verif" />
      <label for="email_edit_verification_1"><strong>1-Step:</strong>
        <?php esc_html_e("Verify new email address.",'pie-register') ?>
      </label>
      <input type="radio" value="2" name="email_edit_verification_step" id="email_edit_verification_2" <?php checked($piereg['email_edit_verification_step']=="2", true) ?> class="step_email_edit_verif" />
      <label for="email_edit_verification_2"><strong>2-Step:</strong>
        <?php esc_html_e("Authenticate the change request by sending an email to the old email address + verify the new email address.",'pie-register') ?>
      </label>
      <input type="radio" value="0" name="email_edit_verification_step" id="email_edit_verification_0" <?php checked($piereg['email_edit_verification_step']=="0", true) ?> class="step_email_edit_verif" />
      <label for="email_edit_verification_0">
        <?php esc_html_e("Off",'pie-register') ?>
      </label>
    </div>
    
  <hr class="seperator" />
  <h3>
    <?php esc_html_e("Restrict Website Content From Search Engine(s) / Bot",'pie-register'); ?>
  </h3>
  <div id="piereg-premium-link">
    <div class="piereg-premium-link-items">
        <div class="piereg-premium-link-icon">
            <img src="<?php echo esc_url(PIEREG_PLUGIN_URL . 'assets/images/pr-premium-icon.png');?>" alt="">
        </div>
        <span><a class="piereg-premium-link-text" href="https://pieregister.com/plan-and-pricing/" target="_blank" >This feature is available in premium version</a> </span>
    </div>
  </div>
  <div class="fields">
    <div class="radio_fields">
      <input <?php disabled($_disable, true); ?> type="checkbox" value="1" name="restrict_bot_enabel" id="captcha_publc" <?php checked(isset($piereg['restrict_bot_enabel']) && $piereg['restrict_bot_enabel']=="1", true) ?> />
    </div>
    <label for="captcha_publc" class="label_mar_top">
      <?php esc_html_e("Restrict search engines and bots from crawling page content.",'pie-register') ?>
    </label>
  </div>
  <div class="fields">
    <label for="restrict_bot_content" class="label_textarea">
      <?php esc_html_e("User Agents to Reject",'pie-register') ?>
    </label>
    <?php global $PR_Bot_List; ?>
    <textarea <?php disabled($_disable, true); ?> name="restrict_bot_content"><?php echo esc_textarea(($piereg['restrict_bot_content']!="")?$piereg['restrict_bot_content']:$PR_Bot_List);?></textarea>
  </div>
  <div class="fields">
    <label for="restrict_bot_content_message" class="label_textarea">
      <?php esc_html_e("Text to send bots when blocking access",'pie-register') ?>
    </label>
    <textarea <?php disabled($_disable, true); ?> name="restrict_bot_content_message"><?php echo ($piereg['restrict_bot_content_message']!="")?esc_textarea($piereg['restrict_bot_content_message']):"Restricted Post: You are not allowed to view the content of this post";?></textarea>
  </div>
  <input name="action" value="pie_reg_settings" type="hidden" />
  <div class="pie-save-settings-bar">
    <div class="fields fields_submitbtn">
      <input type="submit" class="submit_btn" value=" <?php esc_attr_e("Save Changes","pie-register");?> " />
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