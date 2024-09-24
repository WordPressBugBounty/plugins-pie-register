<form method="post" action="">
  <?php if( function_exists( 'wp_nonce_field' )) wp_nonce_field( 'piereg_wp_invitation_code_nonce','piereg_invitation_code_nonce'); ?>
  <ul class="bg-white clearfix invite-form">
      <li class="clearfix">
        <div class="fields">
          <div  class="cols-2">
            <h3>
              <?php esc_html_e("Insert Codes","pie-register");?>
            </h3>
          </div><!-- cols-3 -->
          <div class="cols-3">
            <textarea id="piereg_codepass" name="piereg_codepass"><?php echo (isset($this->pie_post_array['piereg_codepass'])?esc_textarea($this->pie_post_array['piereg_codepass']):''); ?></textarea>
             <span style="text-align:left;" class="note pie_usage_note">
            <?php esc_html_e("Enter one invitation code per line. Special characters are not allowed.","pie-register");?>
            </span>
          </div><!-- cols-3 -->
        </div>
      </li>
      <li class="clearfix code_desc">
        <div class="fields">
          <div class="cols-2">
            <h3>
              <?php esc_html_e("Code Description","pie-register");?>
            </h3>
          </div><!-- cols-3 -->
          <div class="cols-3">
            <textarea style="float:left;" type="text" id="invitation_code_description" name="invitation_code_description"><?php echo (isset($this->pie_post_array['invitation_code_description'])?esc_textarea($this->pie_post_array['invitation_code_description']):''); ?></textarea>
            <span style="text-align:left;" class="note pie_invitation_code_description_note">
            <?php esc_html_e("Enter a short description.","pie-register");?>
            </span>
          </div><!-- cols-3 -->
        </div>
      </li>
      <li style="margin-bottom:6%;" class="clearfix invitation_code_assign_user_role">
        <div class="fields">
          <div class="cols-2">
            <h3>
              <?php esc_html_e("Assign User Role","pie-register");?>
            </h3>
          </div><!-- cols-3 -->
          <div class="cols-3 fields invitation_code_user_role">
            <select disabled id="invitation_code_user_role" name="invitation_code_user_role">
              <?php
                  $wp_roles = new WP_Roles;
                  $roles = array();
                  foreach ( $wp_roles->roles as $role)
                  {
                    $roles[] = $role['name'];
                  }
                  echo $this->createDropdown($roles,((isset($_POST['invitation_code_user_role']))?$_POST['invitation_code_user_role']:""));
              ?>
            </select>
            <span data-available="(Available in premium version.)" style="text-align:left;" class="note pie_usage_note pro-ver pie_invitation_code_user_role_note">
              <?php esc_html_e("Please select user role.","pie-register"); ?> 
              <br>
              <?php esc_html_e("Note: Make sure you don't have Custom User Role Field added to your Registration Form.","pie-register");?>
            </span>
          </div><!-- cols-3 -->
        </div>
      </li>
      <li class="clearfix code_usageItem">
        <div class="fields">
          <div class="cols-2">
            <h3>
              <?php esc_html_e("Usage","pie-register");?>
            </h3>
          </div><!-- cols-3 -->
          <div class="cols-3">
            <input style="float:left;" value="<?php echo (isset($this->pie_post_array['invitation_code_usage'])?esc_attr($this->pie_post_array['invitation_code_usage']):''); ?>" type="text" id="invitation_code_usage" name="invitation_code_usage" class="input_fields2" />
            <span style="text-align:left;" class="note pie_usage_note">
            <?php esc_html_e("Number of times a single code can be used to register.","pie-register"); ?> 
            <br>
            <?php esc_html_e("Note: For unlimited usage of the code, set usage to 0","pie-register");?>
            </span>
          </div><!-- cols-3 -->
           </div>
      </li>
      <li class="clearfix code_expiryDate">
        <div class="fields">
          <div class="cols-2">
            <h3>
              <?php esc_html_e("Expiry Date","pie-register");?>
            </h3>
          </div><!-- cols-3 -->
          <div class="cols-3">
                <input style="float:left;" autocomplete="off" value="YYYY-MM-DD" type="text" id="invitation_expiry_date" class="input_fields2" disabled>
                <span data-available="(Available in premium version.)" style="text-align:left;" class="note pie_usage_note pro-ver">
                    <?php esc_html_e("Define invitation code expiry date here. Leaving it empty means that the invitation code will never expire.","pie-register");?>
                </span>
          </div><!-- cols-3 -->
           </div>
      </li>
    
    <li class="clearfix">
      <div class="fields fields_submitbtn">
        <div class="cols-2">&nbsp;</div><!-- cols-3 -->
        <div class="cols-3 text-right">
          <input name="add_code" class="submit_btn" value="<?php esc_attr_e('Add Code','pie-register');?>" type="submit" />
        </div><!-- cols-3 -->
      </div>
      <div id="piereg-guide-link">
        <div id="piereg-guide-link-items">
          <div class="piereg-guide-link-icon" data-title="<?php esc_attr_e('Click here for a detailed guide.','pie-register');?>">
            <a href="https://pieregister.com/documentation/how-to-use-invitation-codes-with-pie-register/" target="_blank" rel="noopener noreferrer">
              <img src="<?php echo esc_url(PIEREG_PLUGIN_URL . 'assets/images/pr-guide-link-icon.png');?>" alt="">
            </a>
          </div>
        </div>
      </div>
    </li>
  </ul>
</form>