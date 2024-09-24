<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<?php $piereg = $this->get_pr_global_options(); 
      $post_status = 'publish,private,pending,future';

	$_disable 			= true;
	$_available_in_pro 	= ' - <span style="color:red;">'. esc_html__("Available in premium version","pie-register") . '</span>';
?>
<div class="roles_container">
<form action="" method="post" id="frm_settings_allusers" onsubmit="return validateSettings();">
  <?php 
  if( function_exists( 'wp_nonce_field' )) wp_nonce_field( 'piereg_wp_settings_allusers','piereg_settings_allusers'); ?>
       <div class="fields" style="float:none;">
            <div class="radio_fields">
                 <input <?php disabled($_disable, true); ?> type="checkbox" 
                    name="login_after_register" id="login_after_register_yes" value="1" <?php checked($piereg['login_after_register']=="1", true) ?> />
            </div>
            <label class="label_mar_top" for="login_after_register_yes"><?php esc_html_e("Auto login users after registration.",'pie-register'); ?></label> <br /><br />
            <span class="quotation_ux_adv"><?php esc_html_e("Verify that email/admin verifications and payment methods are off. Also, Make sure the registration form has a password field.","pie-register"); ?></span>
            <div id="piereg-premium-link">
              <div class="piereg-premium-link-items">
                <div class="piereg-premium-link-icon">
                    <img src="<?php echo esc_url(PIEREG_PLUGIN_URL . 'assets/images/pr-premium-icon.png');?>" alt="">
                </div>
                <span><a class="piereg-premium-link-text" href="https://pieregister.com/plan-and-pricing/" target="_blank" >This feature is available in premium version</a> </span>
              </div>
            </div> 
        </div>
    <hr style="clear:both;margin:35px 0px 0px;"  />
    <div class="fields">
      <label for="alternate_login">
        <?php esc_html_e("Login Page",'pie-register') ?>
      </label>
      <?php  $args =  array("show_option_no_change"=>"-- Please select one --","id"=>"alternate_login","name"=>"alternate_login","selected"=>$piereg['alternate_login'],"post_status"=>$post_status);         
                wp_dropdown_pages( $args ); ?>
      <span class="quotation">
      <?php esc_html_e("This page must contain the Pie Register Login form short code.",'pie-register') ?>
      </span> </div>
    <div class="fields">
      <label for="alternate_register">
        <?php esc_html_e("Registration Page",'pie-register') ?>
      </label>
      <?php  $args =  array("show_option_no_change"=>"-- Please select one --","id"=>"alternate_register","name"=>"alternate_register","selected"=>$piereg['alternate_register'],"post_status"=>$post_status);         
                wp_dropdown_pages( $args ); ?>
      <span class="quotation">
      <?php esc_html_e("This page must contain the Pie Register Registration form short code.",'pie-register') ?>
      </span> </div>
    <div class="fields">
      <label for="alternate_forgotpass">
        <?php esc_html_e("Forgot Password Page",'pie-register') ?>
      </label>
      <?php  $args =  array("show_option_no_change"=>"-- Please select one --","id"=>"alternate_forgotpass","name"=>"alternate_forgotpass","selected"=>$piereg['alternate_forgotpass'],"post_status"=>$post_status);         
                wp_dropdown_pages( $args ); ?>
      <span class="quotation">
      <?php esc_html_e("This page must contain the Pie Register Forgot Password form short code.",'pie-register') ?>
      </span> </div>
    <div class="fields">
      <label for="alternate_profilepage">
        <?php esc_html_e("Profile Page",'pie-register') ?>
      </label>
      <?php  $args =  array("show_option_no_change"=>"-- Please select one --","id"=>"alternate_profilepage","name"=>"alternate_profilepage","selected"=>$piereg['alternate_profilepage'],"post_status"=>$post_status);         
                wp_dropdown_pages( $args ); ?>
      <span class="quotation">
      <?php esc_html_e("This page must contain the Pie Register Profile section short code.",'pie-register') ?>
      </span> </div>
    <div class="fields">
      <label for="after_login"> 
        <?php esc_html_e("After Login Page",'pie-register') ?>
      </label>

      <?php
          global $post;
          $args = array( 'numberposts' => -1, 'post_type'=>'post');
          $posts = get_posts($args);
          $pages = get_pages(array( 'numberposts' => -1));
          ?>
      <select id="after_login" name="after_login" >
        <option value="-1"> Default </option>
        <option value="-2" <?php if($piereg['after_login'] == '-2' ){ echo "selected"; } ?>> <?php esc_html_e("Last visited page") ?>  </option>
        <option value="" disabled> ---- Pages ---- </option>
        <?php foreach( $pages as $page ) : $page->post_content; ?>
          <option class="level-0" value="<?php echo esc_attr($page->ID); ?>" <?php if($page->ID == $piereg['after_login']){ echo "selected"; } ?>>
            <?php echo esc_html($page->post_title); ?>
          </option>
        <?php endforeach; ?>
        <option value="" disabled> ---- Posts ---- </option>
        <?php foreach( $posts as $post ) : setup_postdata($post); ?>
          <option class="level-0" value="<?php echo esc_attr($post->ID); ?>" <?php if($post->ID == $piereg['after_login']){ echo "selected"; } ?>>
            <?php esc_html(the_title()); ?>
          </option>
        <?php endforeach; ?>
        <option value="url" <?php if('url' == $piereg['after_login']){ echo "selected"; } ?>>&lt;URL&gt;</option>
      </select>
    </div>
    <div class="fields <?php echo ($piereg['after_login'] == "url") ? "": "hide"; ?>">
      <label for="alternate_login_url"></label>	
      <input type="text" name="alternate_login_url" id="alternate_login_url" value="<?php echo (isset($piereg['alternate_login_url']) ? esc_url($piereg['alternate_login_url']) : "" ); ?>" class="input_fields" />
    </div>
    <div class="fields">
      <label for="alternate_logout">
        <?php esc_html_e("After Logout Page",'pie-register') ?>
      </label>
      <select id="alternate_logout" name="alternate_logout" >
        <option value="-1"> None </option>
        <option value="" disabled> ---- Pages ---- </option>
        <?php foreach( $pages as $page ) : $page->post_content; ?>
          <option class="level-0" value="<?php echo esc_attr($page->ID); ?>" <?php if($page->ID == $piereg['alternate_logout']){ echo "selected"; } ?>>
            <?php echo esc_html($page->post_title); ?>
          </option>
        <?php endforeach; ?>
        <option value="" disabled> ---- Posts ---- </option>
        <?php foreach( $posts as $post ) : setup_postdata($post); ?>
          <option class="level-0" value="<?php echo esc_attr($post->ID); ?>" <?php if($post->ID == $piereg['alternate_logout']){ echo "selected"; } ?>>
            <?php esc_html(the_title()); ?>
          </option>
        <?php endforeach; ?>
        <option value="url" <?php if('url' == $piereg['alternate_logout']){ echo "selected"; } ?>>&lt;URL&gt;</option>
      </select>
    </div>   
    <div class="fields <?php echo ($piereg['alternate_logout'] == "url") ? "": "hide"; ?>">
      <label for="alternate_logout_url"></label>	
      <input type="text" name="alternate_logout_url" id="alternate_logout_url" value="<?php echo (isset($piereg['alternate_logout_url']) ? esc_url($piereg['alternate_logout_url']) : "" ); ?>" class="input_fields" />
    </div>    
  <input name="action" value="pie_reg_settings" type="hidden" />
  <div class="pie-save-settings-bar">
    <div class="fields fields_submitbtn">
      <input type="submit" class="submit_btn" value="<?php esc_attr_e("Save Settings","pie-register"); ?>" />
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