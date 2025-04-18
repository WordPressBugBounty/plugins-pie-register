<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<?php 
    $_disable 			= true;
?>
<div class="pieregister-admin">
  <div class="settings" style="padding-bottom:0px;">
    <h2>
      <?php  esc_html_e("Import/Export",'pie-register') ?>
    </h2>
    <div id="piereg-detailed-guide-link">
      <a target="_blank" href="https://pieregister.com/documentation/data-import-export/"><?php esc_html_e("Learn how to use Import/Export","pie-register"); ?></a>
    </div>  
  </div>
  <div style="clear: both;float: none;">
    <?php
       if( isset($this->pie_post_array['error_message']) && !empty( $this->pie_post_array['error_message'] ) )
	        echo '<p class="error">' . wp_kses_post($this->pie_post_array['error_message'])  . "</p>";
       if( isset($this->pie_post_array['error']) && !empty( $this->pie_post_array['error'] ) )
    	    echo '<p class="error">' . wp_kses_post($this->pie_post_array['error'])  . "</p>";
       if(isset( $this->pie_post_array['success_message'] ) && !empty( $this->pie_post_array['success_message'] ))
	        echo '<p class="success">' . wp_kses_post($this->pie_post_array['success_message'])  . "</p>";
        ?>
  </div>
  <div class="settings" style="padding-bottom:0px;">
    <div class="right_section importexport">
      <fieldset class="piereg_fieldset_area">
        <legend>
        <?php esc_html_e("All Settings",'pie-register') ?>
        <div id="piereg-premium-link">
          <div class="piereg-premium-link-items">
            <div class="piereg-premium-link-icon">
              <img src="<?php echo esc_url(PIEREG_PLUGIN_URL . 'assets/images/pr-premium-icon.png');?>" alt="">
            </div>
            <span><a class="piereg-premium-link-text" href="https://pieregister.com/plan-and-pricing/" target="_blank" >This feature is available in premium version</a> </span>
          </div>
        </div> 
        </legend>
        <div class="fields">
          <form method="post" action="">
            <?php if( function_exists( 'wp_nonce_field' )) wp_nonce_field( 'piereg_wp_export_general_settings','piereg_export_general_settings'); ?>
            <label>
              <?php esc_html_e("Export",'pie-register') ?>
            </label>
            <input type="hidden" name="import_export_settings" value="1" />
            <input <?php disabled($_disable, true, true) ?> type="submit" name="export_general_settings" value=" <?php esc_html_e("Export","pie-register"); ?> " class="button button-primary button-large"  />
          </form>
        </div>
        <div class="fields">
          <form method="post" action="" enctype="multipart/form-data">
            <?php if( function_exists( 'wp_nonce_field' )) wp_nonce_field( 'piereg_wp_import_general_settings','piereg_import_general_settings'); ?>
            <label>
              <?php esc_html_e("Import",'pie-register') ?>
            </label>
            <div class="file_container">
              <input <?php disabled($_disable, true, true) ?>  type="file" name="import_general_settings_file" class="import_general_settings_file" />
              <input type="hidden" name="import_export_settings" value="1" />
              <input <?php disabled($_disable, true, true) ?>  type="submit" name="import_general_settings" value=" <?php esc_attr_e("Import","pie-register"); ?> " 
                					 onclick="validImportForm(this.form, '.import_general_settings_file')" 
                                	 class="button button-primary button-large" 
                                	 />
              <span class="quotation"><strong>
              <?php esc_html_e("Warning","pie-register"); ?>
              </strong>:
              <?php esc_html_e("Only json format is supported. Importing data in other formats may break your existing settings","pie-register"); ?>
              </span> </div>
          </form>
        </div>
      </fieldset>
      <fieldset class="piereg_fieldset_area">
        <legend>
        <?php esc_html_e("E-mail Templates",'pie-register') ?>
        <div id="piereg-premium-link">
          <div class="piereg-premium-link-items">
            <div class="piereg-premium-link-icon">
              <img src="<?php echo esc_url(PIEREG_PLUGIN_URL . 'assets/images/pr-premium-icon.png');?>" alt="">
            </div>
            <span><a class="piereg-premium-link-text" href="https://pieregister.com/plan-and-pricing/" target="_blank" >This feature is available in premium version</a> </span>
          </div>
        </div> 
        </legend>
        <div class="fields">
          <form method="post" action="">
            <?php if( function_exists( 'wp_nonce_field' )) wp_nonce_field( 'piereg_wp_export_email_template','piereg_export_email_template'); ?>
            <label>
              <?php esc_html_e("Export",'pie-register') ?>
            </label>
            <input type="hidden" name="import_export_settings" value="1" />
            <input <?php disabled($_disable, true, true) ?> type="submit" name="export_email_template" value=" <?php esc_attr_e("Export","pie-register"); ?> " class="button button-primary button-large"  />
          </form>
        </div>
        <div class="fields">
          <form method="post" action="" enctype="multipart/form-data">
            <?php if( function_exists( 'wp_nonce_field' )) wp_nonce_field( 'piereg_wp_import_email_template','piereg_import_email_template'); ?>
            <label>
              <?php esc_html_e("Import",'pie-register') ?>
            </label>
            <div class="file_container">
              <input <?php disabled($_disable, true, true) ?> type="file" name="import_email_template_file" class="import_email_template_file" />
              <input type="hidden" name="import_export_settings" value="1" />
              <input <?php disabled($_disable, true, true) ?> type="submit" name="import_email_template" value=" <?php esc_attr_e("Import","pie-register"); ?> " 
                			onclick="validImportForm(this.form, '.import_email_template_file')" 
                            class="button button-primary button-large" />
              <span class="quotation"><strong>
              <?php esc_html_e("Warning","pie-register"); ?>
              </strong>:
              <?php esc_html_e("Only json format is supported. Importing data in other formats may break your existing email templates.","pie-register"); ?>
              </span> </div>
          </form>
        </div>
      </fieldset>
      <fieldset class="piereg_fieldset_area">
        <legend>
        <?php esc_html_e("Invitation Codes",'pie-register') ?>
        <div id="piereg-premium-link">
          <div class="piereg-premium-link-items">
            <div class="piereg-premium-link-icon">
              <img src="<?php echo esc_url(PIEREG_PLUGIN_URL . 'assets/images/pr-premium-icon.png');?>" alt="">
            </div>
            <span><a class="piereg-premium-link-text" href="https://pieregister.com/plan-and-pricing/" target="_blank" >This feature is available in premium version</a> </span>
          </div>
        </div> 
        </legend>
        <div class="fields">
          <form method="post" action="">
            <?php if( function_exists( 'wp_nonce_field' )) wp_nonce_field( 'piereg_wp_export_invitations_codes','piereg_export_invitations_codes'); ?>
            <label>
              <?php esc_html_e("Export",'pie-register') ?>
            </label>
            <input type="hidden" name="import_export_settings" value="1" />
            <input <?php disabled($_disable, true, true) ?> type="submit" name="export_invitations_codes" value=" <?php esc_attr_e("Export","pie-register"); ?> " class="button button-primary button-large"  />
          </form>
        </div>
        <div class="fields">
          <form method="post" action="" enctype="multipart/form-data">
            <?php if( function_exists( 'wp_nonce_field' )) wp_nonce_field( 'piereg_wp_import_invitations_codes','piereg_import_invitations_codes'); ?>
            <label>
              <?php esc_html_e("Import",'pie-register') ?>
            </label>
            <div class="file_container">
              <input <?php disabled($_disable, true, true) ?> type="file" name="import_invitations_codes_file" class="import_invitations_codes_file" />
              <input type="hidden" name="import_export_settings" value="1" />
              <input <?php disabled($_disable, true, true) ?> type="submit" name="import_invitations_codes" value=" <?php esc_attr_e("Import","pie-register"); ?> " 
                				onclick="validImportForm(this.form, '.import_invitations_codes_file')"  
                                class="button button-primary button-large"  />
              <span class="quotation"><strong>
              <?php esc_html_e("Warning","pie-register"); ?>
              </strong>:
              <?php esc_html_e("Only json and CSV formats are supported. Importing data in other formats may break your existing invitation codes.","pie-register"); ?>
              </span>             
              <span class="quotation"><?php echo sprintf( __( 'You may want to see', 'pie-register').' <a target="_blank" download href="%s"> '.__('this example of the CSV file.', 'pie-register').'</a>.' , esc_url(plugin_dir_url(__FILE__).'examples/example-invitaion-codes.csv')); ?></span>
            </div>
          </form>
        </div>
      </fieldset>
      <fieldset class="piereg_fieldset_area">
        <legend>
        <?php esc_html_e("All User Data with Custom Fields",'pie-register') ?>
        <div id="piereg-premium-link">
          <div class="piereg-premium-link-items">
            <div class="piereg-premium-link-icon">
              <img src="<?php echo esc_url(PIEREG_PLUGIN_URL . 'assets/images/pr-premium-icon.png');?>" alt="">
            </div>
            <span><a class="piereg-premium-link-text" href="https://pieregister.com/plan-and-pricing/" target="_blank" >This feature is available in premium version</a> </span>
          </div>
        </div> 
        </legend>
        <?php
			if(isset( $this->pie_post_array['successfull_import_all_users_data'] ) && !empty( $this->pie_post_array['successfull_import_all_users_data'] ))
      // translators: %d is the number of users successfully imported
				echo '<p class="success">' . sprintf( __("(%d) user(s) successfully imported","pie-register"), intval($this->pie_post_array['successfull_import_all_users_data']) ) . "</p>";
			if(isset( $this->pie_post_array['unsuccessfull_import_all_users_data'] ) && !empty( $this->pie_post_array['unsuccessfull_import_all_users_data'] ))
      // translators: %d is the number of users already exist
				echo '<p class="error">' . sprintf( __("(%d) user(s) are already exist(s)","pie-register"), intval($this->pie_post_array['unsuccessfull_import_all_users_data']) ) . "</p>";
			?>
        <div class="fields">
          <form method="post" action="">
            <?php if( function_exists( 'wp_nonce_field' )) wp_nonce_field( 'piereg_wp_export_all_user_custom_data','piereg_export_all_user_custom_data'); ?>
            <label>
              <?php esc_html_e("Export",'pie-register') ?>
            </label>
            <input type="hidden" name="import_export_settings" value="1" />
            <input <?php disabled($_disable, true, true) ?> type="submit" name="piereg_export_user_custom_data" value=" <?php esc_attr_e("Export","pie-register"); ?> " class="button button-primary button-large"  />
          </form>
        </div>
        <div class="fields">
          <form method="post" action="" enctype="multipart/form-data">
            <?php if( function_exists( 'wp_nonce_field' )) wp_nonce_field( 'piereg_wp_import_all_user_custom_data','piereg_import_all_user_custom_data'); ?>
            <label>
              <?php esc_html_e("Import",'pie-register') ?>
            </label>
            <div class="file_container">
              <input <?php disabled($_disable, true, true) ?> type="file" name="import_all_users_data_with_custom_field" class="import_all_users_data_with_custom_field" />
              <input type="hidden" name="import_export_settings" value="1" />
              <input <?php disabled($_disable, true, true) ?> type="submit" name="piereg_import_user_custom_data" value="<?php esc_attr_e("Import","pie-register"); ?>" 
            					onclick="validImportForm(this.form, '.import_all_users_data_with_custom_field')" 
                                class="button button-primary button-large"  />
              <span class="quotation width-63"><strong>
              <?php esc_html_e("Warning","pie-register"); ?>
              </strong>:
              <?php _e ( wp_kses("Only json format is supported <em>(for backup purpose)</em>", array('em' => array())),"pie-register"); ?>
              </span></div>
          </form>
        </div>
      </fieldset>
    </div>
  </div>
  <div class="notifications">
    <div class="settings importexport importexportBottomSection" style="padding-bottom:0px;">
      <h3>
        <?php  esc_html_e("User Entries",'pie-register') ?>
      </h3>
      <div class="export">
        <form method="post" action="" id="export">
          <?php if( function_exists( 'wp_nonce_field' )) wp_nonce_field( 'piereg_wp_exportusers_nonce','piereg_export_users_nonce'); ?>
          <ul>
            <li>
              <div class="fields">
                <h2>
                  <?php  esc_html_e("Export",'pie-register'); ?>
                </h2>
                <p>
                  <?php  esc_html_e("You can export users with default fields within a date range to a CSV file. Select the fields and the Date Range. Selecting the Date Range is optional. If you do not select a date range, all entries will be exported. Click on the Download CSV File to complete the download.",'pie-register'); ?>
                </p>
              </div>
            </li>
            <li>
              <div class="fields select_checkbox">
                <h2>
                  <?php esc_html_e("Select Fields",'pie-register'); ?>
                </h2>
                <div class="export_field">
                  <input id="field_selectall" type="checkbox" class="checkbox selectall" />
                  <label for="field_selectall">
                    <?php esc_html_e("Select All","pie-register"); ?>
                  </label>
                </div>
                <div class="export_field">
                  <input id="field_user_login" type="checkbox" class="checkbox meta_key" name="pie_fields_csv[user_login]" value="Username"  />
                  <label for="field_user_login">
                    <?php esc_html_e("Username","pie-register"); ?>
                  </label>
                </div>
                <div class="export_field">
                  <input id="field_first_name" type="checkbox" class="checkbox meta_key" name="pie_meta_csv[first_name]" value="First Name" />
                  <label for="field_first_name">
                    <?php esc_html_e("First Name","pie-register"); ?>
                  </label>
                </div>
                <div class="export_field">
                  <input id="field_last_name" type="checkbox" class="checkbox meta_key" name="pie_meta_csv[last_name]" value="Last Name" />
                  <label for="field_last_name">
                    <?php esc_html_e("Last Name","pie-register"); ?>
                  </label>
                </div>
                <div class="export_field">
                  <input id="field_nickname" type="checkbox" class="checkbox meta_key" name="pie_meta_csv[nickname]" value="Nickname" />
                  <label for="field_nickname">
                    <?php esc_html_e("Nickname","pie-register"); ?>
                  </label>
                </div>
                <div class="export_field">
                  <input id="field_display_name" type="checkbox" class="checkbox meta_key" name="pie_fields_csv[display_name]" value="Display name" />
                  <label for="field_display_name">
                    <?php esc_html_e("Display name","pie-register"); ?>
                  </label>
                </div>
                <div class="export_field">
                  <input id="field_user_email" type="checkbox" class="checkbox meta_key" name="pie_fields_csv[user_email]" value="E-mail" />
                  <label for="field_user_email">
                    <?php esc_html_e("E-mail","pie-register"); ?>
                  </label>
                </div>
                <div class="export_field">
                  <input id="field_user_url" type="checkbox" class="checkbox meta_key" name="pie_fields_csv[user_url]" value="Website" />
                  <label for="field_user_url">
                    <?php esc_html_e("Website","pie-register"); ?>
                  </label>
                </div>
                <div class="export_field">
                  <input id="field_description" type="checkbox" class="checkbox meta_key" name="pie_meta_csv[description]" value="Biographical Info" />
                  <label for="field_description">
                    <?php esc_html_e("Biographical Info","pie-register"); ?>
                  </label>
                </div>
                <div class="export_field">
                  <input id="field_role" type="checkbox" class="checkbox meta_key" name="pie_meta_csv[wp_capabilities]" value="Role" />
                  <label for="field_role">
                    <?php esc_html_e("Role","pie-register"); ?>
                  </label>
                </div>
                <div class="export_field">
                  <input id="field_user_registered" type="checkbox" class="checkbox meta_key" name="pie_fields_csv[user_registered]" value="User Registered" />
                  <label for="field_user_registered">
                    <?php esc_html_e("User Registered","pie-register"); ?>
                  </label>
                </div>
              </div>
            </li>
            <li>
              <div class="fields date">
                <h2>
                  <?php esc_html_e("Select User Registration Date Range","pie-register"); ?>
                </h2>
                <div class="start_date">
                  <label for="field_">
                    <?php esc_html_e("Start","pie-register"); ?>
                  </label>
                  <input readonly id="date_start" name="date_start" type="text" class="input_fields date_start" />
                  <img id="start_icon" src="<?php echo esc_url(PIEREG_PLUGIN_URL.'assets/images/calendar_img.jpg') ?>" width="22" height="22" alt="calendar" class="calendar_img" /> 
                </div>
                <div class="end_date">
                  <label for="field_">
                    <?php esc_html_e("End","pie-register"); ?>
                  </label>
                  <input readonly id="date_end" name="date_end" type="text" class="input_fields date_start" />
                  <img id="end_icon" src="<?php echo esc_url(PIEREG_PLUGIN_URL.'assets/images/calendar.png') ?>" width="22" height="22" alt="calendar" class="calendar_img" /> 
                </div>
                <?php esc_html_e("Date Range is optional, if no date range is selected all entries will be exported.","pie-register"); ?>
                <div class="piereg_clear"></div>
                <div class="pie-save-settings-bar pie-import-export-cta-bar">
                  <input type="submit" class="submit_btn" value="<?php esc_attr_e("Download CSV File","pie-register")?>" />
                  <div id="piereg-guide-link">
                    <div id="piereg-guide-link-items">
                      <div class="piereg-guide-link-icon" data-title="<?php esc_attr_e('Click here for a detailed guide.','pie-register');?>">
                          <a href="https://pieregister.com/documentation/data-import-export/" target="_blank" rel="noopener noreferrer">
                          <img src="<?php echo esc_url(PIEREG_PLUGIN_URL . 'assets/images/pr-guide-link-icon.png');?>" alt="">
                          </a>
                      </div>
                    </div>
                  </div>  
                </div>
              </div>
            </li>
          </ul>
        </form>
      </div>
      <div class="import">
        <form method="post" action="" enctype="multipart/form-data">
          <?php if( function_exists( 'wp_nonce_field' )) wp_nonce_field( 'piereg_wp_importusers_nonce','piereg_import_users_nonce'); ?>
          <ul>
            <li>
              <div class="fields">
                <h2>
                  <?php esc_html_e("Import",'pie-register'); ?>
                </h2>
                <p>
                  <?php esc_html_e("Select the CSV file you want to import. When you click Import, Pie Register will import all the users from the CSV file. Please see the example CSV file before importing.",'pie-register'); ?>
                </p>
              </div>
            </li>
            <li>
              <div class="fields">
                <h2>
                  <?php esc_html_e("Select File","pie-register"); ?>
                </h2>
                <input style="margin-left:0px;" name="csvfile" type="file" class="input_fields" />
              </div>
            </li>
            <li>
              <div class="fields">
                <input type="checkbox" id="update_existing_users" value="yes" name="update_existing_users" />
                <label for="update_existing_users" style="margin-top:0px;" >
                  <?php esc_html_e("Update Existing Users","pie-register"); ?>
                </label>
              </div>
            </li>
            <li>
              <div class="fields"> <span style="float:left"><?php echo sprintf( __( 'You may want to see', 'pie-register').' <a target="_blank" download href="%s"> '.__('this example of the CSV file.', 'pie-register').'</a>.' , esc_url(plugin_dir_url(__FILE__).'examples/example.csv')); ?></span>
                <div class="piereg_clear"></div>
              </div>
            </li>
            <li>
              <div class="pie-save-settings-bar pie-import-export-cta-bar">
                <div class="fields">
                  <input type="submit" class="submit_btn submit_btn2" value="<?php esc_attr_e("Import","pie-register")?>" />
                </div>
                <div id="piereg-guide-link">
                  <div id="piereg-guide-link-items">
                    <div class="piereg-guide-link-icon" data-title="<?php esc_attr_e('Click here for a detailed guide.','pie-register');?>">
                        <a href="https://pieregister.com/documentation/data-import-export/" target="_blank" rel="noopener noreferrer">
                        <img src="<?php echo esc_url(PIEREG_PLUGIN_URL . 'assets/images/pr-guide-link-icon.png');?>" alt="">
                        </a>
                    </div>
                  </div>
                </div>  
              </div>
            </li>
          </ul>
        </form>
      </div>
    </div>
  </div>
</div>
