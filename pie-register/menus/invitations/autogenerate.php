<form method="post" action="">
  <?php if( function_exists( 'wp_nonce_field' )) wp_nonce_field( 'piereg_wp_invitation_code_nonce','piereg_invitation_code_nonce'); ?>
  <ul class="bg-white clearfix invite-form">
        <div id="piereg-premium-link">
          <div class="piereg-premium-link-items">
            <div class="piereg-premium-link-icon">
              <img src="<?php echo esc_url(PIEREG_PLUGIN_URL . 'assets/images/pr-premium-icon.png');?>" alt="">
            </div>
            <span><a class="piereg-premium-link-text" href="https://pieregister.com/plan-and-pricing/" target="_blank" >This feature is available in premium version</a> </span>
          </div>
        </div> 
        <li class="clearfix">
        <div class="fields">
          <div  class="cols-2">
            <h3>
              <?php esc_html_e("Code Prefix","pie-register");?>
            </h3>
          </div><!-- cols-3 -->
         <div class="cols-3">
            <input style="float:left;" type="text" name="invitation_code_prefix" id="invitation_code_prefix" class="input_fields2" disabled />
            <span style="text-align:left;" class="note pie_usage_note">
            <?php esc_html_e("Prefix should contain max 3 characters (alphabets/numbers).","pie-register");?>
            </span>
          </div><!-- cols-3 -->
        </div>
      </li>
      <li class="clearfix">
        <div class="fields">
          <div  class="cols-2">
            <h3>
              <?php esc_html_e("Code Numbers","pie-register");?>
            </h3>
          </div><!-- cols-3 -->
          <div class="cols-3">
            <input style="float:left;" type="number" name="invitation_code_numbers" class="input_fields2" disabled />
            <span style="text-align:left;" class="note pie_usage_note">
            <?php esc_html_e("Enter the number of codes to generate. Max 10","pie-register");?>
            </span>
          </div><!-- cols-3 -->
        </div>
      </li>
      <li class="clearfix">
        <div class="fields">
          <div  class="cols-2">
            <h3>
              <?php esc_html_e("Code Length","pie-register");?>
            </h3>
          </div><!-- cols-3 -->
         <div class="cols-3">
            <input disabled style="float:left;" value="<?php echo (isset($this->pie_post_array['invitation_code_length'])?esc_attr($this->pie_post_array['invitation_code_length']):''); ?>" type="number" min="5" max="10" name="invitation_code_length" class="input_fields2"/>
            <span style="text-align:left;" class="note pie_usage_note">
            <?php esc_html_e("Enter the length of code. Code can be minimum 5 characters and maximum 10 characters long.","pie-register");?>
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
            <textarea disabled style="float:left;" type="text" id="invitation_code_description" name="invitation_code_description"><?php echo (isset($this->pie_post_array['invitation_code_description'])?esc_textarea($this->pie_post_array['invitation_code_description']):''); ?></textarea>
            <span style="text-align:left;" class="note pie_invitation_code_description_note">
            <?php esc_html_e("Enter a short description.","pie-register");?>
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
            <input type="text" id="invitation_code_usage" name="invitation_code_usage" class="input_fields2" disabled />  
            <span style="text-align:left;" class="note pie_usage_note">
            <?php esc_html_e("Number of times a single code can be used to register.<br> Note: For unlimited usage of the code, set usage to 0.","pie-register");?>
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
            <span style="text-align:left;" class="note pie_usage_note" style="color:red;">
                <?php esc_html_e("Define invitation code expiry date here. Leaving it empty means that the invitation code will never expire.","pie-register");?>
            </span>
          </div><!-- cols-3 -->
           </div>
      </li>
    
    <li class="clearfix">
      <div class="fields fields_submitbtn">
        <div class="cols-2">&nbsp;</div><!-- cols-3 -->
        <div class="cols-3 text-right">
          <input disabled name="add_code" class="submit_btn" value="<?php esc_attr_e('Add Code','pie-register');?>" type="submit" />
        </div><!-- cols-3 -->
      </div>
    </li>

  </ul>
</form>
