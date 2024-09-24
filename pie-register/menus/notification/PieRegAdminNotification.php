<?php 
$piereg_base   = new PieReg_Base();
$piereg        = $piereg_base->get_pr_global_options();
$piereg_forms  = $piereg_base->get_pr_forms_info();
?>
<div class="right_section">
  <div class="notifications">
  <form method="post" action="#piereg_admin_notification">
    <?php if( function_exists( 'wp_nonce_field' )) wp_nonce_field( 'piereg_wp_admin_email_notification','piereg_admin_email_notification'); ?>
    <div class="notification-accordion">
      <div class="notification-item">
        <div class="notification-item-toggler">
          <p><?php esc_html_e('Default - Admin Notification' ,"pie-register"); ?></p>
          <div class="pie-email-notif-accordian-icon"></div>
        </div>
        <div class="content clearfix">
        <ul>
              <li class="hide-div clearfix" >
                <div class="fields pie-email-notif-chkbox">
                  <input name="enable_admin_notifications" <?php checked($piereg['enable_admin_notifications'] == "1", true); ?> type="checkbox" class="checkbox" value="1" />
                  <label class="pie-enable-notif-chkbox-label" style="width:auto;margin-right:20px;"><?php esc_html_e("Enable email notifications to administrator",'pie-register');?></label>
                  <label style="font-size:12px;margin-bottom:10px;margin-top:10px;font-size:14px;"><i><?php esc_html_e("Note: If the default WP Login page is used, Email verification links will not work.",'pie-register');?></i></label>           
                </div>
              </li>
              <li  class="hide-div clearfix">
                <div class="fields pie-email-notif-textarea">
                  <label><?php esc_html_e("Send To Email(s)*",'pie-register');?></label>
                  <textarea name="admin_sendto_email" class="textarea_fields" rows="6"><?php echo esc_textarea($piereg['admin_sendto_email'])?></textarea>
                  <p style="font-size:13px;margin-top:0;"><?php esc_html_e("comma seperated for multiple emails. e.g. someone@example.com,someoneelse@example.com",'pie-register');?></p>
                </div>
              </li>
              <li  class="hide-div clearfix">
                <div class="fields pie-email-notif-input">
                  <label><?php esc_html_e("From Name",'pie-register');?></label>
                  <input name="admin_from_name" value="<?php echo esc_attr($piereg['admin_from_name'])?>" type="text" class="input_fields2" />
                </div>
              </li>
              <li  class="hide-div clearfix">
                <div class="fields pie-email-notif-input">
                  <label><?php esc_html_e("From Email",'pie-register');?></label>
                  <input name="admin_from_email" value="<?php echo esc_attr($piereg['admin_from_email'])?>" type="text" class="input_fields2" />
                </div>
              </li>
              <li  class="hide-div clearfix">
                <div class="fields pie-email-notif-input">
                  <label><?php esc_html_e("Reply To",'pie-register');?></label>
                  <input name="admin_to_email" value="<?php echo esc_attr($piereg['admin_to_email'])?>" type="text" class="input_fields2" />
                </div>
              </li>
              <li  class="hide-div clearfix">
                <div class="fields pie-email-notif-input">
                  <label><?php esc_html_e("BCC",'pie-register');?></label>
                  <input  name="admin_bcc_email" value="<?php echo esc_attr($piereg['admin_bcc_email'])?>" type="text" class="input_fields" />
                </div>
              </li>
              <li  class="hide-div clearfix">
                <div class="fields pie-email-notif-input">
                    <label><?php esc_html_e("Subject",'pie-register');?></label>
                    <input name="admin_subject_email" id="admin_subject_email" value="<?php echo esc_attr($piereg['admin_subject_email'])?>" type="text" class="input_fields" />
                  <div class="pie_wrap_keys">                                
                        <strong><?php esc_html_e("Use tags in subject field","pie-register"); ?>:</strong>
                        <span class="style_textarea" onclick="selectText('piereg-select-all-text-onclick_1')" id="piereg-select-all-text-onclick_1" readonly="readonly">%user_login%</span>
                        <span class="style_textarea" onclick="selectText('piereg-select-all-text-onclick_2')" id="piereg-select-all-text-onclick_2" readonly="readonly">%user_email%</span>
                        <span class="style_textarea" onclick="selectText('piereg-select-all-text-onclick_3')" id="piereg-select-all-text-onclick_3" readonly="readonly">%blogname%</span>
                    </div>
                </div>
              </li>
              <li  class="hide-div clearfix">
                <div class="fields flex-format">
                  <div class="radio_fields">
                      <input type="checkbox" name="admin_message_email_formate" id="admin_message_email_formate" value="1" <?php checked($piereg['admin_message_email_formate'] == "1", true); ?> />	
                  </div>
                  <label class="labelaligned"><?php esc_html_e("Email HTML Format",'pie-register');?></label>
                </div>
              </li>
              <li  class="hide-div clearfix"> 
                <div class="fields pie-email-notif-html-and-keys">
                <label style="font-size:12px;margin-bottom:10px;font-size:14px;"><i><?php esc_html_e("Message: Enter a message below to receive notification email when new users register.",'pie-register');?></i></label>
                <label class="piereg-notifications-note" style="font-size:12px;margin-bottom:10px;font-size:14px;"><i><?php esc_html_e("Note: If the default WP Login page is used, Admin and Email verifications links will not work.",'pie-register');?></i></label>
                <p>
                <label><?php esc_html_e("Replacement Keys","pie-register"); ?>:</label>
                <?php
                    $fields = maybe_unserialize(get_option("pie_fields"));
                    $replacement_fields = '';
                    $woocommerce_fields = '';	   	
                    if( (is_array($fields) || is_object($fields)) && sizeof($fields) > 0 )
                    {
                        foreach($fields as $pie_fields)	
                        {
                            switch($pie_fields['type']) :
                            case 'default' :
                            case 'form' :					
                            case 'submit' :
                            case 'username' :
                            case 'email' :
                            case 'password' :
                            case 'name' :
                            case 'pagebreak' :
                            case 'sectionbreak' :
                            case 'hidden' :
                            case 'html' :
                            case 'captcha' :
                            case 'math_captcha' :
                                continue 2;
                            break;
                            endswitch;						
                            if($pie_fields['type'] == "invitation")
                            {
                                $meta_key = "invitation_code";
                            }
                            elseif($pie_fields['type'] == "custom_role")
                            {
                                $meta_key = "custom_role";
                            }
                            elseif($pie_fields['type'] == "wc_billing_address")
                            {
                                $meta_key = "wc_billing_address";
                                if( empty($pie_fields['label']) ) 
                                {
                                  $pie_fields['label'] = "Billing Address";
                                }
                            }
                            elseif($pie_fields['type'] == "wc_shipping_address")
                            {
                                $meta_key = "wc_shipping_address";
                                if( empty($pie_fields['label']) ) 
                                {
                                  $pie_fields['label'] = "Shipping Address";
                                }
                            }
                            else
                            {
                                $meta_key	= "pie_".$pie_fields['type']."_".$pie_fields['id'];
                            }
                            
                            if ($pie_fields['type'] == "wc_billing_address" || $pie_fields['type'] == "wc_shipping_address")
                            {
                              $woocommerce_fields .= '<option value="%'.esc_attr($meta_key).'%">'.esc_html($pie_fields['label']).'</option>';
                            }
                            else
                            {
                              $replacement_fields .= '<option value="%'.esc_attr($meta_key).'%">'.ucwords(esc_html($pie_fields['label'])).'</option>';
                            }
                        }
                    }
                    ?>
                    <select class="piereg_replacement_keys" name="replacement_keys" id="replacement_keys">
                        <option value="select"><?php esc_html_e('Select','pie-register');?></option>
                        <optgroup label="<?php esc_attr_e("Standard Fields",'pie-register') ?>">
                            <option value="%user_login%"><?php esc_html_e("User Name",'pie-register') ?></option>
                            <option value="%user_email%"><?php esc_html_e("User E-mail",'pie-register') ?></option>
                            <option value="%firstname%"><?php esc_html_e("User First Name",'pie-register') ?></option>
                            <option value="%lastname%"><?php esc_html_e("User Last Name",'pie-register') ?></option>
                            <option value="%user_url%"><?php esc_html_e("User URL",'pie-register') ?></option>
                            <option value="%user_biographical_nfo%"><?php esc_html_e("User Biographical Info",'pie-register') ?></option>
                            <option value="%user_registration_date%"><?php esc_html_e("User Registration Date",'pie-register') ?></option>
                        </optgroup>
                        <optgroup label="<?php esc_attr_e("Legacy Fields",'pie-register') ?>">
                            <option value="%user_aim%"><?php esc_html_e("User AIM",'pie-register') ?></option>
                            <option value="%user_yim%"><?php esc_html_e("User YIM",'pie-register') ?></option>
                            <option value="%user_jabber%"><?php esc_html_e("User Jabber",'pie-register') ?></option>
                        </optgroup>
                        <optgroup label="<?php esc_attr_e("Custom Fields",'pie-register') ?>">
                            <?php echo wp_kses($replacement_fields,$this->piereg_forms_get_allowed_tags()); ?>
                        </optgroup>
                        <optgroup label="<?php esc_attr_e("WooCommerce Fields",'pie-register') ?>">
                            <?php echo wp_kses($woocommerce_fields,$this->piereg_forms_get_allowed_tags()); ?>
                        </optgroup>
                        <optgroup label="<?php esc_attr_e("Other",'pie-register') ?>">
                            <option value="%blogname%"><?php esc_html_e("Blog Name",'pie-register') ?></option>
                            <option value="%siteurl%"><?php esc_html_e("Site URL",'pie-register') ?></option>
                            <option value="%verificationurl%"><?php esc_html_e("Verification URL",'pie-register') ?></option> <!-- task duplicate form -->
                            <option value="%blogname_url%"><?php esc_html_e("Blog Name With Site URL",'pie-register') ?></option>
                            <option value="%user_ip%"><?php esc_html_e("User IP",'pie-register') ?></option>
                        </optgroup>
                    </select>
                    </p>
                  <?php  
                      $settings = array( 'textarea_name' => "admin_message_email");
                      $textarea_text = $piereg['admin_message_email'];
                      wp_editor($textarea_text, 'piereg_text_editor', $settings );
                  ?>  
                  <div class="piereg_clear"></div>
                </div>
              </li>
            </ul>
        </div>
      </div>
      <div class="notification-item">
        <div class="notification-item-toggler">
          <p><?php esc_html_e('Admin Notification On Profile Update' ,"pie-register"); ?></p>
          <div class="pie-email-notif-accordian-icon"></div>
        </div>
        <div class="content clearfix">
          <ul class="clearfix">
            <li class="hide-div clearfix">
              <div class="fields pie-email-notif-chkbox">
                <input name="enable_admin_notifications_profile_update" <?php checked($piereg['enable_admin_notifications_profile_update'] == "1", true); ?> type="checkbox" class="checkbox" value="1" />
                <?php esc_html_e("Enable email notifications to administrator On Profile Update",'pie-register');?>  
                <label class="piereg-notifications-note" style="font-size:12px;margin-bottom:10px;margin-top:10px;font-size:14px;"><i><?php esc_html_e("Note: If the default WP Login page is used, Email verification links will not work.",'pie-register');?></i></label>            
              </div>
            </li>
            <li class="hide-div clearfix">
              <div class="fields pie-email-notif-textarea">
                <label><?php esc_html_e("Send To Email(s)*",'pie-register');?></label>
                <textarea name="admin_sendto_email_profile_update" class="textarea_fields" rows="6"><?php echo esc_textarea($piereg['admin_sendto_email_profile_update'])?></textarea>
                <p style="font-size:13px;margin-top:0;"><?php esc_html_e("comma seperated for multiple emails. e.g. someone@example.com,someoneelse@example.com",'pie-register');?></p>
              </div>
            </li>
            <li class="hide-div clearfix">
              <div class="fields pie-email-notif-input">
                <label><?php esc_html_e("From Name",'pie-register');?></label>
                <input name="admin_from_name_profile_update" value="<?php echo esc_attr($piereg['admin_from_name_profile_update'])?>" type="text" class="input_fields2" />
              </div>
            </li>
            <li class="hide-div clearfix">
              <div class="fields pie-email-notif-input">
                <label><?php esc_html_e("From Email",'pie-register');?></label>
                <input name="admin_from_email_profile_update" value="<?php echo esc_attr($piereg['admin_from_email_profile_update'])?>" type="text" class="input_fields2" />
              </div>
            </li>
            <li class="hide-div clearfix">
              <div class="fields pie-email-notif-input">
                <label><?php esc_html_e("Reply To",'pie-register');?></label>
                <input name="admin_to_email_profile_update" value="<?php echo esc_attr($piereg['admin_to_email_profile_update'])?>" type="text" class="input_fields2" />
              </div>
            </li>
            <li class="hide-div clearfix">
              <div class="fields pie-email-notif-input">
                <label><?php esc_html_e("BCC",'pie-register');?></label>
                <input  name="admin_bcc_email_profile_update" value="<?php echo esc_attr($piereg['admin_bcc_email_profile_update'])?>" type="text" class="input_fields" />
              </div>
            </li>
            <li class="hide-div clearfix">
              <div class="fields pie-email-notif-input">
                  <label><?php esc_html_e("Subject",'pie-register');?></label>
                  <input name="admin_subject_email_profile_update" id="admin_subject_email_profile_update" value="<?php echo esc_attr($piereg['admin_subject_email_profile_update'])?>" type="text" class="input_fields" />
                <div class="pie_wrap_keys">                                
                      <strong><?php esc_html_e("Use tags in subject field","pie-register"); ?>:</strong>
                      <span class="style_textarea" onclick="selectText('piereg-select-all-text-onclick_1')" id="piereg-select-all-text-onclick_1" readonly="readonly">%user_login%</span>
                      <span class="style_textarea" onclick="selectText('piereg-select-all-text-onclick_2')" id="piereg-select-all-text-onclick_2" readonly="readonly">%user_email%</span>
                      <span class="style_textarea" onclick="selectText('piereg-select-all-text-onclick_3')" id="piereg-select-all-text-onclick_3" readonly="readonly">%blogname%</span>
                  </div>
              </div>
            </li>
            <li class="hide-div clearfix">
              <div class="fields flex-format">
                <div class="radio_fields">
                    <input type="checkbox" name="admin_message_email_formate_profile_update" id="admin_message_email_formate_profile_update" value="1" <?php checked($piereg['admin_message_email_formate_profile_update'] == "1", true); ?> />	
                </div>
                <label class="labelaligned"><?php esc_html_e("Email HTML Format",'pie-register');?></label>
              </div>
            </li>
            <li class="hide-div clearfix">
              <div class="fields pie-email-notif-html-and-keys">
              <label style="font-size:12px;margin-bottom:10px;font-size:14px;"><i><?php esc_html_e("Message: Enter a message below to receive notification email when new users register.",'pie-register');?></i></label>
              <p>
              <label><?php esc_html_e("Replacement Keys","pie-register"); ?>:</label>
              <?php
                  $fields = maybe_unserialize(get_option("pie_fields"));
                  $woocommerce_fields = '';
                  $replacement_keys_per_form = '';
                  $replacement_fields = '';

                  if( (is_array($fields) || is_object($fields)) && sizeof($fields) > 0 )
                  {
                    foreach($fields as $pie_fields)	
                    {
                        switch($pie_fields['type']) :
                        case 'default' :
                        case 'form' :
                        case 'submit' :
                        case 'username' :
                        case 'email' :
                        case 'password' :
                        case 'name' :
                        case 'pagebreak' :
                        case 'sectionbreak' :
                        case 'hidden' :
                        case 'html' :
                        case 'captcha' :
                        case 'math_captcha' :
                            continue 2;
                        break;
                        endswitch;						
                        if($pie_fields['type'] == "invitation")
                        {
                            $meta_key = "invitation_code";
                        }
                        elseif($pie_fields['type'] == "custom_role")
                        {
                            $meta_key = "custom_role";
                        }
                        elseif($pie_fields['type'] == "wc_billing_address")
                        {
                            $meta_key = "wc_billing_address";
                            if( empty($pie_fields['label']) ) 
                            {
                              $pie_fields['label'] = "Billing Address";
                            }
                        }
                        elseif($pie_fields['type'] == "wc_shipping_address")
                        {
                            $meta_key = "wc_shipping_address";
                            if( empty($pie_fields['label']) ) 
                            {
                              $pie_fields['label'] = "Shipping Address";
                            }
                        }
                        else
                        {
                            $meta_key	= "pie_".$pie_fields['type']."_".$pie_fields['id'];
                        }
                        
                        if ($pie_fields['type'] == "wc_billing_address" || $pie_fields['type'] == "wc_shipping_address")
                        {
                          $woocommerce_fields .= '<option value="%'.esc_attr($meta_key).'%">'.esc_html($pie_fields['label']).'</option>';
                        }
                        else
                        {
                          $replacement_fields .= '<option value="%'.esc_attr($meta_key).'%">'.ucwords(esc_html($pie_fields['label'])).'</option>';
                        }
                    }
                  }
                  ?>
                  <select class="piereg_replacement_keys" name="replacement_keys" id="replacement_keys">
                      <option value="select"><?php esc_html_e('Select','pie-register');?></option>
                      <optgroup label="<?php esc_attr_e("Standard Fields",'pie-register') ?>">
                          <option value="%user_login%"><?php esc_html_e("User Name",'pie-register') ?></option>
                          <option value="%user_email%"><?php esc_html_e("User E-mail",'pie-register') ?></option>
                          <option value="%firstname%"><?php esc_html_e("User First Name",'pie-register') ?></option>
                          <option value="%lastname%"><?php esc_html_e("User Last Name",'pie-register') ?></option>
                          <option value="%user_url%"><?php esc_html_e("User URL",'pie-register') ?></option>
                          <option value="%user_biographical_nfo%"><?php esc_html_e("User Biographical Info",'pie-register') ?></option>
                          <option value="%user_registration_date%"><?php esc_html_e("User Registration Date",'pie-register') ?></option>
                      </optgroup>
                      <optgroup label="<?php esc_attr_e("Legacy Fields",'pie-register') ?>">
                          <option value="%user_aim%"><?php esc_html_e("User AIM",'pie-register') ?></option>
                          <option value="%user_yim%"><?php esc_html_e("User YIM",'pie-register') ?></option>
                          <option value="%user_jabber%"><?php esc_html_e("User Jabber",'pie-register') ?></option>
                      </optgroup>
                      <optgroup label="<?php esc_attr_e("Custom Fields",'pie-register') ?>">
                          <?php echo wp_kses($replacement_fields,$this->piereg_forms_get_allowed_tags()); ?>
                      </optgroup>
                      <optgroup label="<?php esc_attr_e("WooCommerce Fields",'pie-register') ?>">
                          <?php echo wp_kses($woocommerce_fields,$this->piereg_forms_get_allowed_tags()); ?>
                      </optgroup>
                      <optgroup label="<?php esc_attr_e("Other",'pie-register') ?>">
                          <option value="%blogname%"><?php esc_html_e("Blog Name",'pie-register') ?></option>
                          <option value="%siteurl%"><?php esc_html_e("Site URL",'pie-register') ?></option>
                          <option value="%verificationurl%"><?php esc_html_e("Verification URL",'pie-register') ?></option> <!-- task duplicate form -->
                          <option value="%blogname_url%"><?php esc_html_e("Blog Name With Site URL",'pie-register') ?></option>
                          <option value="%user_ip%"><?php esc_html_e("User IP",'pie-register') ?></option>
                      </optgroup>
                  </select>
                </p>
                <?php  
                    $settings = array( 'textarea_name' => "admin_message_email_profile_update");
                    $textarea_text = $piereg['admin_message_email_profile_update'];
                    wp_editor($textarea_text, 'piereg_text_editor_profile_update', $settings );
                ?>  
                <div class="piereg_clear"></div>
              </div>
            </li>
          </ul>
        </div>
      </div>
      <div class="notification-item">
        <div class="notification-item-toggler">
          <p><?php esc_html_e('User Cancelled Subscription - Admin Notification' ,"pie-register"); ?></p>
          <div class="pie-email-notif-accordian-icon"></div>
        </div>
        <div class="content clearfix">
          <ul class="clearfix">
            <li class="hide-div clearfix">
              <div class="fields pie-email-notif-chkbox">
                <input name="enable_admin_notifications_cancel_subscription" <?php checked(isset($piereg['enable_admin_notifications_cancel_subscription']) && $piereg['enable_admin_notifications_cancel_subscription'] == "1", true); ?> type="checkbox" class="checkbox enable_admin_notif_chkbox" value="1" />
                <?php esc_html_e("Enable cancel subscription notifications to administrator",'pie-register');?>  
                <label class="piereg-notifications-note" style="font-size:12px;margin-bottom:10px;margin-top:10px;font-size:14px;"><i><?php esc_html_e("Note: If the default WP Login page is used, Email verification links will not work.",'pie-register');?></i></label>            
              </div>
            </li>
            <li class="hide-div clearfix">
              <div class="fields pie-email-notif-textarea">
                <label><?php esc_html_e("Send To Email(s)*",'pie-register');?></label>
                <textarea name="admin_sendto_email_cancel_subscription" class="textarea_fields" rows="6"><?php echo isset($piereg['admin_sendto_email_cancel_subscription']) ? esc_textarea($piereg['admin_sendto_email_cancel_subscription']) : '';?></textarea>
                <p style="font-size:13px;margin-top:0;"><?php esc_html_e("comma seperated for multiple emails. e.g. someone@example.com,someoneelse@example.com",'pie-register');?></p>
              </div>
            </li>
            <li class="hide-div clearfix">
              <div class="fields pie-email-notif-input">
                <label><?php esc_html_e("From Name",'pie-register');?></label>
                <input name="admin_from_name_cancel_subscription" value="<?php echo isset($piereg['admin_from_name_cancel_subscription']) ? esc_attr($piereg['admin_from_name_cancel_subscription']): '';?>" type="text" class="input_fields2" />
              </div>
            </li>
            <li class="hide-div clearfix">
              <div class="fields pie-email-notif-input">
                <label><?php esc_html_e("From Email",'pie-register');?></label>
                <input name="admin_from_email_cancel_subscription" value="<?php echo isset($piereg['admin_from_email_cancel_subscription']) ? esc_attr($piereg['admin_from_email_cancel_subscription']) : '';?>" type="text" class="input_fields2" />
              </div>
            </li>
            <li class="hide-div clearfix">
              <div class="fields pie-email-notif-input">
                <label><?php esc_html_e("Reply To",'pie-register');?></label>
                <input name="admin_to_email_cancel_subscription" value="<?php echo isset($piereg['admin_to_email_cancel_subscription']) ? esc_attr($piereg['admin_to_email_cancel_subscription']) : '';?>" type="text" class="input_fields2" />
              </div>
            </li>
            <li class="hide-div clearfix">
              <div class="fields pie-email-notif-input">
                <label><?php esc_html_e("BCC",'pie-register');?></label>
                <input  name="admin_bcc_email_cancel_subscription" value="<?php echo isset($piereg['admin_bcc_email_cancel_subscription']) ? esc_attr($piereg['admin_bcc_email_cancel_subscription']) : '';?>" type="text" class="input_fields" />
              </div>
            </li>
            <li class="hide-div clearfix">
              <div class="fields pie-email-notif-input">
                  <label><?php esc_html_e("Subject",'pie-register');?></label>
                  <input name="admin_subject_email_cancel_subscription" id="admin_subject_email_cancel_subscription" value="<?php echo isset($piereg['admin_subject_email_cancel_subscription']) ? esc_attr($piereg['admin_subject_email_cancel_subscription']) : '';?>" type="text" class="input_fields" />
                <div class="pie_wrap_keys">                                
                      <strong><?php esc_html_e("Use tags in subject field","pie-register"); ?>:</strong>
                      <span class="style_textarea" onclick="selectText('piereg-select-all-text-onclick_1')" id="piereg-select-all-text-onclick_1" readonly="readonly">%user_login%</span>
                      <span class="style_textarea" onclick="selectText('piereg-select-all-text-onclick_2')" id="piereg-select-all-text-onclick_2" readonly="readonly">%user_email%</span>
                      <span class="style_textarea" onclick="selectText('piereg-select-all-text-onclick_3')" id="piereg-select-all-text-onclick_3" readonly="readonly">%blogname%</span>
                  </div>
              </div>
            </li>
            <li class="hide-div clearfix">
              <div class="fields flex-format">
                <div class="radio_fields">
                    <input type="checkbox" name="admin_message_email_formate_cancel_subscription" id="admin_message_email_formate_cancel_subscription" value="1" <?php checked(isset($piereg['admin_message_email_formate_cancel_subscription']) && $piereg['admin_message_email_formate_cancel_subscription'] == "1", true); ?> />	
                </div>
                <label class="labelaligned"><?php esc_html_e("Email HTML Format",'pie-register');?></label>
              </div>
            </li>
            <li class="hide-div clearfix">
              <div class="fields pie-email-notif-html-and-keys">
              <label style="font-size:12px;margin-bottom:10px;font-size:14px;"><i><?php esc_html_e("Message: Enter a message below to receive notification email when new users register.",'pie-register');?></i></label>
              <p>
              <label><?php esc_html_e("Replacement Keys","pie-register"); ?>:</label>
              <?php
                  $fields = maybe_unserialize(get_option("pie_fields"));
                  $woocommerce_fields = '';
                  $replacement_keys_per_form = '';
                  
                  foreach ($piereg_forms as $piereg_form){
                    $replacement_fields = '';
                    $piereg_form_data = $piereg_base->getCurrentFields($piereg_form['Id']);
                
                    if( (is_array($piereg_form_data) || is_object($piereg_form_data)) && sizeof($piereg_form_data) > 0 )
                    {
                        foreach($piereg_form_data as $pie_fields)	
                        {
                            switch($pie_fields['type']) :
                            case 'default' :
                            case 'form' :					
                            case 'submit' :
                            case 'username' :
                            case 'email' :
                            case 'password' :
                            case 'name' :
                            case 'pagebreak' :
                            case 'sectionbreak' :
                            case 'hidden' :
                            case 'html' :
                            case 'captcha' :
                            case 'math_captcha' :
                                continue 2;
                            break;
                            endswitch;						
                            if($pie_fields['type'] == "invitation")
                            {
                                $meta_key = "invitation_code";
                            }
                            elseif($pie_fields['type'] == "custom_role")
                            {
                                $meta_key = "custom_role";
                            }
                            elseif($pie_fields['type'] == "wc_billing_address")
                            {
                                $meta_key = "wc_billing_address";
                                if( empty($pie_fields['label']) ) 
                                {
                                  $pie_fields['label'] = "Billing Address";
                                }
                            }
                            elseif($pie_fields['type'] == "wc_shipping_address")
                            {
                                $meta_key = "wc_shipping_address";
                                if( empty($pie_fields['label']) ) 
                                {
                                  $pie_fields['label'] = "Shipping Address";
                                }
                            }
                            else
                            {
                                $meta_key	= "pie_".$pie_fields['type']."_".$pie_fields['id'];
                            }
                            
                            if ($pie_fields['type'] == "wc_billing_address" || $pie_fields['type'] == "wc_shipping_address")
                            {
                              $woocommerce_fields .= '<option value="%'.esc_attr($meta_key).'%">'.esc_html($pie_fields['label']).'</option>';
                            }
                            else
                            {
                              $replacement_fields .= '<option value="%'.esc_attr($meta_key).'%">'.ucwords(esc_html($pie_fields['label'])).'</option>';
                            }
                        }
                    }
                    
                    $replacement_keys_per_form .= '<optgroup label="'.esc_attr($piereg_form_data["form"]["label"]).'">';
                      $replacement_keys_per_form .= wp_kses($replacement_fields,$this->piereg_forms_get_allowed_tags());
                    $replacement_keys_per_form .= '</optgroup>';

                  }
                  ?>
                  <select class="piereg_replacement_keys" name="replacement_keys" id="replacement_keys">
                      <option value="select"><?php esc_html_e('Select','pie-register');?></option>
                      <optgroup label="<?php esc_attr_e("Standard Fields",'pie-register') ?>">
                          <option value="%user_login%"><?php esc_html_e("User Name",'pie-register') ?></option>
                          <option value="%user_email%"><?php esc_html_e("User E-mail",'pie-register') ?></option>
                          <option value="%firstname%"><?php esc_html_e("User First Name",'pie-register') ?></option>
                          <option value="%lastname%"><?php esc_html_e("User Last Name",'pie-register') ?></option>
                          <option value="%user_url%"><?php esc_html_e("User URL",'pie-register') ?></option>
                          <option value="%user_biographical_nfo%"><?php esc_html_e("User Biographical Info",'pie-register') ?></option>
                          <option value="%user_registration_date%"><?php esc_html_e("User Registration Date",'pie-register') ?></option>
                      </optgroup>
                      <optgroup label="<?php esc_attr_e("Legacy Fields",'pie-register') ?>">
                          <option value="%user_aim%"><?php esc_html_e("User AIM",'pie-register') ?></option>
                          <option value="%user_yim%"><?php esc_html_e("User YIM",'pie-register') ?></option>
                          <option value="%user_jabber%"><?php esc_html_e("User Jabber",'pie-register') ?></option>
                      </optgroup>
                      <!-- <optgroup label="<?php esc_attr_e("Custom Fields",'pie-register') ?>"> -->
                          <?php echo wp_kses($replacement_keys_per_form,$this->piereg_forms_get_allowed_tags()); ?>
                      <!-- </optgroup> -->
                      <optgroup label="<?php esc_attr_e("WooCommerce Fields",'pie-register') ?>">
                          <?php echo wp_kses($woocommerce_fields,$this->piereg_forms_get_allowed_tags()); ?>
                      </optgroup>
                      <optgroup label="<?php esc_attr_e("Other",'pie-register') ?>">
                          <option value="%blogname%"><?php esc_html_e("Blog Name",'pie-register') ?></option>
                          <option value="%siteurl%"><?php esc_html_e("Site URL",'pie-register') ?></option>
                          <option value="%verificationurl%"><?php esc_html_e("Verification URL",'pie-register') ?></option> <!-- task duplicate form -->
                          <option value="%blogname_url%"><?php esc_html_e("Blog Name With Site URL",'pie-register') ?></option>
                          <option value="%user_ip%"><?php esc_html_e("User IP",'pie-register') ?></option>
                      </optgroup>
                  </select>
                </p>
                <?php  
                    $settings = array( 'textarea_name' => "admin_message_email_cancel_subscription");
                    $textarea_text = isset($piereg['admin_message_email_cancel_subscription']) ? $piereg['admin_message_email_cancel_subscription'] : '';
                    wp_editor($textarea_text, 'piereg_text_editor_cancel_subscription', $settings );
                ?>  
                <div class="piereg_clear"></div>
              </div>
            </li>
          </ul>
        </div>
      </div>
    </div>
    <input name="action" value="pie_reg_update" type="hidden" />
    <input type="hidden" name="admin_email_notification_page" value="1" />
    <div class="pie-save-settings-bar">
      <div class="pie-save-settings-btn">
        <p class="submit"><input class="submit_btn notify-submit-btn" name="Submit" value="<?php esc_attr_e('Save Changes','pie-register');?>" type="submit" /></p>
      </div>
      <div id="piereg-guide-link">
        <div id="piereg-guide-link-items">
          <div class="piereg-guide-link-icon" data-title="<?php esc_attr_e('Click here for a detailed guide.','pie-register');?>">
            <a href="https://pieregister.com/documentation/how-to-send-custom-email-notifications-using-pie-register/" target="_blank" rel="noopener noreferrer">
              <img src="<?php echo esc_url(PIEREG_PLUGIN_URL . 'assets/images/pr-guide-link-icon.png');?>" alt="">
            </a>
          </div>
        </div>
      </div>
    </div>
    </form>
  </div>
</div>