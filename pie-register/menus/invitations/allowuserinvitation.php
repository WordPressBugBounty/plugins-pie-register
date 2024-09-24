
<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<?php 
$piereg = get_option(OPTION_PIE_REGISTER);
$pie_user_invitation_access_invitation_codes = get_option('pie_user_invitation_access_invitation_codes');
$all_users = get_users();
$_disable = false;
if(!$this->piereg_pro_is_activate)  {
  $_disable       = true;
}
if ( !current_user_can('piereg_manage_cap') )
{
  wp_redirect(esc_url_raw("admin.php?page=pie-invitation-codes"));
}
?>
<fieldset class="piereg_fieldset_area-nobg pr-allow-user-invitation-menu" <?php disabled($_disable, true, true); ?>>
<form method="post" action="" name="pie_invite_sent" id="pie_invite_sent" enctype="multipart/form-data">
<?php if( function_exists( 'wp_nonce_field' )) wp_nonce_field( 'piereg_wp_invitation_code_nonce','piereg_invitation_code_nonce'); ?>
<h3><?php echo esc_html_e('Invite Users','pie-register'); ?></h3>
<ul class="bg-white clearfix invite-form pr-allow-user-invitation-menu">
<?php if(!$this->piereg_pro_is_activate ){ ?>
  <div id="piereg-premium-link">
    <div class="piereg-premium-link-items">
      <div class="piereg-premium-link-icon">
        <img src="<?php echo esc_url(PIEREG_PLUGIN_URL . 'assets/images/pr-premium-icon.png');?>" alt="">
      </div>
      <span><a class="piereg-premium-link-text" href="https://pieregister.com/plan-and-pricing/" target="_blank" >This feature is available in premium version</a> </span>
    </div>
  </div> 
<?php }?>
<li class="clearfix">
  <strong><?php esc_html_e("Note",'pie-register') ?> :</strong> <?php esc_html_e("The admin can access and track user-generated codes through User Tracker",'pie-register') ?>
</li>
<li class="clearfix">
  <div class="fields pr-enable-user-invitation">
      <div class="radio_fields">
        <input $_disable type="checkbox" name="enable_user_invitation" id="enable_user_invitation" value="1" <?php checked($piereg['enable_user_invitation']=="1", true); ?> />
      </div>
      <label for="enable_user_invitation" class="labelaligned enable-invite">
        <h3><?php esc_html_e(" Let users on your website invite other users ","pie-register");?></h3>
      </label>
  </div>
</li>
<hr class="seperator" />
<div class="user_invitation_settings_div">
  <li class="clearfix">
    <div class="fields">
      <div class="cols-2">
          <h3><?php esc_html_e("Provide the ability to send invitations to:",'pie-register');?></h3>
      </div>
      <div class="cols-6">
        <div class="radio_fields user_invitation_add_capability">
          <input $_disable type="radio" name="user_invitation_add_capability" id="user_invitation_add_capability_0" class="user_invitation_add_capability_0" value="0" checked="checked" <?php checked(isset($piereg['user_invitation_add_capability']) && $piereg['user_invitation_add_capability'] == '0', true); ?> />
          <label for="user_invitation_add_capability_0">All Users</label>
          <input $_disable type="radio" name="user_invitation_add_capability" id="user_invitation_add_capability_1" class="user_invitation_add_capability_1" value="1" <?php checked(isset($piereg['user_invitation_add_capability']) && $piereg['user_invitation_add_capability'] == '1', true); ?> />
          <label for="user_invitation_add_capability_1">Particular User Role(s)</label>
          <input $_disable type="radio" name="user_invitation_add_capability" id="user_invitation_add_capability_2" class="user_invitation_add_capability_2" value="2" <?php checked(isset($piereg['user_invitation_add_capability']) && $piereg['user_invitation_add_capability'] == '2', true); ?> />
          <label for="user_invitation_add_capability_2">Specific User(s)</label>
        </div>
      </div>
    </div>
  </li>
  <div class="allow_specific_roles_settings_div">
    <li class="clearfix">
      <div class="fields">
        <div class="cols-2">
            <h3><?php esc_html_e("Select user role(s)","pie-register");?></h3>
        </div>
        <div class="cols-3">
        <select $_disable multiple id="user_invitation_user_roles" name="user_invitation_user_roles[]">
          <?php
          global $wp_roles;
          $role = $wp_roles->roles;
          foreach($role as $key => $value)
          { 
            if( is_array($value['capabilities']) && array_key_exists('piereg_manage_cap',$value['capabilities'])) continue;
          ?>
          <option <?php echo selected(isset($piereg['user_invitation_user_roles']) && is_array($piereg['user_invitation_user_roles']) && in_array($key,$piereg['user_invitation_user_roles']), true); ?> value="<?php echo esc_attr($key) ?>"><?php echo esc_html( trim( $value['name'] ) ); ?></option>
          <?php
          }
          ?>
        </select>
        </div>
      </div>
    </li>
  </div>
  <div class="allow_specific_users_div">
    <li class="clearfix">
      <div class="fields">
        <div class="cols-2">
            <h3><?php esc_html_e("Specify User(s)","pie-register");?></h3>
        </div>
        <div class="cols-3">
          <textarea name="specific_user_invitation" id="specific_user_invitation" rows="20"><?php echo (isset($piereg['specific_user_invitation'])?esc_attr($piereg['specific_user_invitation']):''); ?></textarea>   
          <span class="quotation import-email-invites">
            <?php esc_html_e("Add Email Addresses, comma seperated.","pie-register"); ?>
          </span>  
        </div>
      </div>
    </li>
  </div>
  <div class="remove_capability_from_specific_users_div">
    <li class="clearfix">
      <div class="fields">
        <div class="cols-2">
            <h3><?php esc_html_e("Remove capability from specific users","pie-register");?></h3>
        </div>
        <div class="cols-3">
          <select $_disable multiple class="multiselect display_search" id="remove_capability_user_invitation" name="remove_capability_user_invitation[]">
          <?php
          foreach($all_users as $user)
          {
            if ( array_key_exists('manage_options',$user->allcaps) ) continue; 
          ?>
            <option value="<?php echo esc_attr($user->user_email) ?>" <?php echo ( is_array($piereg['remove_capability_user_invitation']) && in_array($user->user_email,$piereg['remove_capability_user_invitation']) ) ?'selected="selected"':''?>><?php echo esc_html($user->user_email); ?></option>
          <?php
          }
          ?>
        </select>
        <span style="text-align:left;" class="note pie_usage_note">
        <?php esc_html_e("The dropdown list displays all of the users. You can search for specific users to remove capability from.","pie-register");?>
        </span>
        </div>
      </div>
    </li>	
  </div>
  <li class="clearfix">
    <div class="fields">
      <div class="cols-2">
          <h3><?php esc_html_e("How users will access invitation codes","pie-register");?></h3>
      </div>
      <div class="cols-3">
      <select $_disable multiple class="multiselect" id="access_invitation_codes" name="access_invitation_codes[]">
        <?php
        foreach($pie_user_invitation_access_invitation_codes as $key => $value)
        { 
        ?>
        <option value="<?php echo esc_attr($key) ?>"<?php echo ( is_array($piereg['access_invitation_codes']) && in_array($key,$piereg['access_invitation_codes']) ) ?'selected="selected"':''?>><?php echo esc_html($value); ?></option>
        <?php
        }
        ?>
      </select>
      </div>
    </div>
  </li>
  <li class="clearfix limit_user_invitation_field">
    <div class="fields">
      <div class="cols-2">
        <h3>
          <?php esc_html_e("Limit to generate codes","pie-register");?>
        </h3>
      </div><!-- cols-3 -->
      <div class="cols-3">
        <input $_disable style="float:left;" value="<?php echo (isset($piereg['limit_user_invitation'])?esc_attr($piereg['limit_user_invitation']):''); ?>" type="text" id="limit_user_invitation" name="limit_user_invitation" class="input_fields2" />
        <span style="text-align:left;" class="note pie_usage_note">
        <?php esc_html_e("Impose a limit on the number of invitation codes that a user can generate. Set limit as 0 if you want user to generated unlimited codes.","pie-register");?>
        </span>
      </div><!-- cols-3 -->
    </div>
  </li>
  <li class="clearfix limit_email_invites_field">
    <div class="fields">
      <div class="cols-2">
        <h3>
          <?php esc_html_e("Limit to send email invites","pie-register");?>
        </h3>
      </div><!-- cols-3 -->
      <div class="cols-3">
        <input $_disable style="float:left;" value="<?php echo (isset($piereg['limit_email_invites'])?esc_attr($piereg['limit_email_invites']):''); ?>" type="text" id="limit_email_invites" name="limit_email_invites" class="input_fields2" />
        <span style="text-align:left;" class="note pie_usage_note">
        <?php esc_html_e("Impose a limit on the number of email invites user can set. Set limit as 0 if you want user to send unlimited codes.","pie-register");?>
        </span>
      </div><!-- cols-3 -->
    </div>
  </li>
  <li class="clearfix registration_page_user_invitation">
    <div class="fields">
      <div class="cols-2">
        <h3>
          <?php esc_html_e("Registration Page","pie-register");?>
        </h3>
      </div><!-- cols-3 -->
      <div class="cols-3">
        <select $_disable id="registration_page_user_invitation" name="registration_page_user_invitation">
          <?php
          $registration_pages = get_pages();
          foreach($registration_pages as $key=>$page)
          { 
            if ( !has_shortcode( $page->post_content, 'pie_register_form' ) ) continue;
          ?>
            <option value="<?php echo esc_attr($page->ID); ?>" <?php echo ( isset($piereg['registration_page_user_invitation']) && ($page->ID == intval($piereg['registration_page_user_invitation'])) ) ?'selected="selected"':''?>><?php echo esc_html($page->post_title); ?></option>
          <?php
          }
          ?>
        </select>
        <span style="text-align:left;" class="note pie_usage_note">
        <?php esc_html_e("Select the Registration page that you want the user to see on the 'Invite Through Email' tab.","pie-register");?>
        </span>
      </div><!-- cols-3 -->
    </div>
  </li>
  <li class="clearfix">
    <div class="fields">
      <div class="cols-2">
        <h3>
          <?php esc_html_e("Usage","pie-register");?>
        </h3>
      </div><!-- cols-3 -->
      <div class="cols-3">
        <input $_disable style="float:left;" value="<?php echo (isset($piereg['usage_user_invitation'])?esc_attr($piereg['usage_user_invitation']):''); ?>" type="text" id="usage_user_invitation" name="usage_user_invitation" class="input_fields2" />
        <span style="text-align:left;" class="note pie_usage_note">
        <?php esc_html_e("Number of times a single code can be used to register. For unlimited usage of the code, set usage to 0","pie-register");?>
        </span>
      </div><!-- cols-3 -->
    </div>
  </li>
</div>
<li class="clearfix">
    <div class="fields fields_submitbtn">
      <div class="cols-2">&nbsp;</div><!-- cols-3 -->
      <div class="cols-3 text-right">
        <input name="submit_user_invitation" class="submit_btn" value="<?php esc_attr_e('Save','pie-register');?>" type="submit" />
      </div><!-- cols-3 -->
    </div>
  </li>
</ul>
</form>
</fieldset>