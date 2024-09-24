<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<?php 
	# Define variable's default values
	$action = $subaction = "";
	$active	= "active";
	
	if(isset($_GET['tab']))
		$action	= sanitize_key($_GET['tab']);
	if(isset($_GET['subtab']))
		$subaction	= sanitize_key($_GET['subtab']);	
		
	global $errors;
	$license_key_errors = "";
	
	if(isset($errors->errors['piereg_license_error']) && !empty($errors->errors['piereg_license_error']))
	{
		foreach($errors->errors['piereg_license_error'] as $error_val){
			$license_key_errors .= "<p><strong>".wp_kses_post($error_val)."</strong></p>";
		}
	}
	?>
<div id="container"  class="pieregister-admin piereg-settings-help">
  <div class="right_section">
    <div class="settings">
      <h2 class="headingwidth"><?php esc_html_e("Help",'pie-register') ?></h2>   
      <?php 
	  	if( empty($license_key_errors) )
		{
			if( isset($this->pie_post_array['notice']) && !empty($this->pie_post_array['notice']) ){
				echo '<div id="message" class="updated fade msg_belowheading"><p><strong>' . wp_kses_post($this->pie_post_array['notice']) . '</strong></p></div>';
			}
			else if( isset($this->pie_post_array['error']) && !empty($this->pie_post_array['error']) ){
				echo '<div id="error" class="error fade msg_belowheading"><p><strong>' . wp_kses_post($this->pie_post_array['error']) . '</strong></p></div>';
			}
			if(  isset($this->pie_post_array['success']) && !empty($this->pie_post_array['success']) ){
				echo '<div id="message" class="updated fade msg_belowheading"><p><strong>' . wp_kses_post($this->pie_post_array['license_success']) . '.</strong></p></div>';
			}
		}
		?>
        <div class="pie-help"> <!-- pie-help-left -->
            <div id="tabsSetting" class="tabsSetting">
                <ul class="tabLayer1">
                    <li class="<?php echo ($action == "documentation" || $action == "") ? esc_attr($active) :""; ?>" >
                        <a href="admin.php?page=pie-help&tab=documentation"><?php esc_html_e("Documentation",'pie-register') ?></a></li>
                    <li class="<?php echo ($action == "shortcodes") ? esc_attr($active) :""; ?>">
                        <a href="admin.php?page=pie-help&tab=shortcodes"><?php esc_html_e("Shortcodes",'pie-register') ?></a></li>
                    <li class="<?php echo ($action == "license") ? esc_attr($active) :""; ?>">
                        <a href="admin.php?page=pie-help&tab=license"><?php esc_html_e("License",'pie-register') ?></a></li>
                    <li class="<?php echo ($action == "version") ? esc_attr($active) :""; ?> pr-version-tab" >
                        <a href="admin.php?page=pie-help&tab=version"><?php esc_html_e("Version",'pie-register') ?></a>
                        <ul class="tabLayer2">
                            <li class="<?php echo ($subaction == "environment" || $action == "version" && $subaction == "" ) ? esc_attr($active) :""; ?>">
                                <a href="admin.php?page=pie-help&tab=version&subtab=environment"><?php esc_html_e("Environment",'pie-register') ?></a></li>    
                            <li class="<?php echo ($subaction == "plugins-themes") ? esc_attr($active) :""; ?>">
                                <a href="admin.php?page=pie-help&tab=version&subtab=plugins-themes"><?php esc_html_e("Plugins and Themes",'pie-register') ?></a></li>    
                            <li class="<?php echo ($subaction == "error-log") ? esc_attr($active) :""; ?>" >
                                <a href="admin.php?page=pie-help&tab=version&subtab=error-log"><?php esc_html_e("Error Log",'pie-register') ?></a></li>
                        </ul>
                    </li>
                </ul>
            </div>
            <div class="wrapper-forms">
                <?php if($action == "documentation" || $action == ""){ ?>
                    <p class="pieHelpPara">
                        <div style="clear:both;">
                            <p>
                                <?php esc_html_e("Welcome to Pie Register's Support portal. Did you know that many support related questions are answered in our FAQ, Documentation and Forums sections below.  You can save time by doing quick search of the knowledgebase before submitting a support ticket. ","pie-register"); ?>
                            </p>    
                        </div>
                        <div style="clear:both;">
                            <p>
                                <?php esc_html_e("If weren't able to find what you were looking for, feel free to contact us by submitting a support ticket.","pie-register"); ?>
                            </p>
                        </div>
                    </p>
                    <div class="pieHelpMenuButtonContaner">
                        <ul class="pieHelpMenuButton">
                            <li><a href="https://pieregister.com/faqs/" target="_blank_pieHelp_1"><?php esc_html_e("Browse Frequently Asked Questions","pie-register"); ?></a></li>
                            <li><a href="https://wordpress.org/support/plugin/pie-register/" target="_blank_pieHelp_3"><?php esc_html_e("Go To Forums","pie-register"); ?></a></li>
                            <li><a href="https://pieregister.com/documentation/" target="_blank_pieHelp_4"><?php esc_html_e("Pie Register Manual","pie-register"); ?></a></li>
                            <li><a href="https://pieregister.com/docs-category/getting-started/" target="_blank_pieHelp_5"><?php esc_html_e("Getting Started","pie-register"); ?></a></li>
                            <li><a href="https://pieregister.com/documentation/community-vs-pro-version/" target="_blank_pieHelp_6"><?php esc_html_e("Free v/s Premium version","pie-register"); ?></a>
                            </li>
                            <li><a href="https://pieregister.com/features/" target="_blank_pieHelp_8"><?php esc_html_e("Pie Register Features","pie-register"); ?></a></li>
                            <li><a href="https://wordpress.org/support/plugin/pie-register/reviews/?filter=5" target="_blank_pieHelp_9"><?php esc_html_e("Like Pie Register? Give us a review.","pie-register"); ?>!</a></li>
                        </ul>
                    </div>
                <?php }elseif($action == "shortcodes"){ ?>
                    <p class="pieHelpPara">
                <?php esc_html_e("Pie Register allows you to easily embed Login, Registration, Forgot Password and Profile pages anywhere using Shortcodes. You can embed these pages inside a post, page, custom post type or even in a widget by using the following Shortcodes","pie-register"); ?></p>
                    <table id="PR_table_Short_Code" cellspacing="0" cellpadding="10" >
                        <tr>
                            <td><strong><?php esc_html_e("Forms","pie-register"); ?></strong></td>
                            <td><strong><?php esc_html_e("Short Code","pie-register"); ?></strong></td>
                        </tr>
                        <?php
                        $fields_id = get_option("piereg_form_fields_id");
                        $form_on_free	= get_option("piereg_form_free_id");
                        $count = 0;
                        for($a=1;$a<=intval($fields_id);$a++)
                        {
                            $option = get_option("piereg_form_field_option_".$a);
                            if( !empty($option) && is_array($option) && isset($option['Id']) && (!isset($option['IsDeleted']) || trim($option['IsDeleted']) != 1) )
                            {
                                echo '
                                <tr>
                                    <td><label for="F_R_F_U_'.esc_attr($a).'">'.(esc_html($option['Title'])).' : </label></td>
                                    <td>
                                        <textarea readonly="readonly" class="PR_short_code_input piereg-select-all-text-onclick" id="F_R_F_U_'.esc_attr($a).'">[pie_register_form id="'.esc_attr($option['Id']).'" title="true" description="true"]</textarea>
                                    </td>
                                </tr>';
                                //echo '';
                                $count++;
                                
                                if( $count == 1 )
                                {
                                    if( !$form_on_free )
                                    {
                                        update_option('piereg_form_free_id', $option['Id']);
                                        $form_on_free .= $option['Id'];
                                    }
                                    break;
                                }
                            }
                        }
                        ?>
                        <tr>
                            <td><label for="F_L_F_U"><?php esc_html_e("Login Form","pie-register"); ?> : </label></td>
                            <td>
                                <input type="text" id="F_L_F_U" value="[pie_register_login]" readonly="readonly" class="PR_short_code_input piereg-select-all-text-onclick" />
                            </td>
                        </tr>
                        <tr>
                            <td><label for="F_F_P_F_U"><?php esc_html_e("Forgot Password Form","pie-register"); ?> : </label></td>
                            <td>
                                <input type="text" id="F_F_P_F_U" value="[pie_register_forgot_password]" readonly="readonly" class="PR_short_code_input piereg-select-all-text-onclick" />
                            </td>
                        </tr>
                        <tr>
                            <td><label for="F_P_P_U"><?php esc_html_e("Profile Page","pie-register"); ?> : </label></td>
                            <td>
                                <input type="text" id="F_P_P_U" value="[pie_register_profile]" readonly="readonly" class="PR_short_code_input piereg-select-all-text-onclick" />
                            </td>
                        </tr>
                        <tr>
                            <td><label for="F_U_P_P_U"><?php esc_html_e("User's Profile Picture","pie-register"); ?> : </label></td>
                            <td>
                                <input type="text" id="F_U_P_P_U" value="[pie_user_profile_pic]" readonly="readonly" class="PR_short_code_input piereg-select-all-text-onclick" />
                            </td>
                        </tr>
                        <?php #PIEREG_IS_ACTIVE
                            if(false){ ?>
                            <tr>
                                <td><label for="F_R_A"><?php esc_html_e("Renew Account Page","pie-register"); ?> : </label></td>
                                <td>
                                    <input type="text" id="F_R_A" value="[pie_register_renew_account]" readonly="readonly" class="PR_short_code_input piereg-select-all-text-onclick" />
                                </td>
                            </tr>
                        <?php } ?>
                        <?php do_action("pieregister_print_shortcode"); ?>
                        <tr>
                            <td></td>
                            <td></td>
                        </tr>
                    </table>
                <?php
                    }elseif($action == "license"){
                    ?>
                    <div class="how-to-premium">
                        <h3><?php esc_html_e("Bought the Pie Register Premium plugin?",'pie-register') ?></h3>
                        <h4 style="clear:both"><?php echo (wp_kses_post('Please follow the <a href="https://pieregister.com/documentation/install-and-activate/" target="_blank" rel="noopener noreferrer">Install and Activate</a> Documentation','pie-register')) ?>.</h4>
                    </div>
                    <div class="pie_addons" style="clear:both">
                        <h3><?php esc_html_e("Pie Register Add-ons",'pie-register') ?>:</h3>
                    <?php
                        if( isset($this->pie_post_array['error']) && !empty($this->pie_post_array['error']) ){
                            echo '<div id="error" class="error fade msg_belowheading"><p><strong>' . esc_html($this->pie_post_array['error']) . '</strong></p></div>';
                        }
                        elseif( isset($license_key_errors) && !empty($license_key_errors) ){
                            echo '<div id="error" class="error fade msg_belowheading">' . wp_kses_post($license_key_errors) . '</div>';
                        }
                        elseif( isset($this->pie_post_array['success']) && !empty($this->pie_post_array['success']) ){
                            echo '<div id="message" class="updated fade msg_belowheading"><p><strong>' . esc_html($this->pie_post_array['success']) . '</strong></p></div>';
                        }
                        
                        do_action("pieregister_addons_listing");
                    ?>
                    </div>
                <?php }elseif($action == "version" && $subaction == "" || $action == "version" && $subaction == "environment"){ ?>
                        <?php
                            $pr_ver = get_plugins();
                            if($pr_ver['pie-register/pie-register.php'] != ''){
                            ?>
                        <div class="fields">
                        <label><?php esc_html_e("Pie Register Version",'pie-register') ?></label>
                        <?php
                            echo '<span class="installation_status">'.esc_html($pr_ver['pie-register/pie-register.php']['Name']).' '.esc_html($pr_ver['pie-register/pie-register.php']['Version']).'</span>';
                        ?>
                        </div>
                        <?php
                            }
                        ?>
                        <div class="fields">
                        <label><?php esc_html_e("PHP Version",'pie-register') ?></label>
                        <?php if(version_compare(phpversion(),  "5.0") == 1)
                        {
                            echo '<span class="installation_status">'.phpversion().'</span>';
                        }
                        else
                        {
                            echo '<span class="installation_status_faild">'.phpversion().'</span>';
                            echo '<span class="quotation">'.esc_html__("Pie Register requires PHP version 5.0 or newer. ","pie-register").'</span>';
                        }
                        ?>
                        </div>
                        <div class="fields">
                        <label><?php esc_html_e("MySQL Version",'pie-register') ?></label>
                        <?php
                            global $wpdb;
                            $piereg_mytsql_version_info = $wpdb->db_version();
                            if(version_compare($piereg_mytsql_version_info,  "5.0") == 1)
                            {
                                echo '<span class="installation_status">'.esc_html($piereg_mytsql_version_info).'</span>';
                            }
                            else
                            {
                                echo '<span class="installation_status_faild">'.esc_html($piereg_mytsql_version_info).'</span>';
                                echo '<span class="quotation">'.esc_html__("Pie Register requires MySQL version 5.0 or newer.","pie-register").'</span>';
                            }
                            ?>
                        
                        </div>
                        <div class="fields">
                        <label><?php esc_html_e("Wordpress Version",'pie-register') ?></label>
                        <?php if(version_compare(get_bloginfo('version'),  "3.5") == 1)
                        {
                            echo '<span class="installation_status">'.esc_html(get_bloginfo('version')).'</span>';
                        }
                        else
                        {
                            echo '<span class="installation_status_faild">'.esc_html(get_bloginfo('version')).'</span>';
                            echo '<span class="quotation">'.esc_html__("Pie Register requires Wordpress version 3.5 or newer. ","pie-register").'</span>';
                        }
                        ?>
                        </div>
                        <div class="fields">
                        <label><?php esc_html_e("Curl",'pie-register') ?></label>
                        <?php if(function_exists('curl_version'))
                        {
                            echo '<span class="installation_status">'.esc_html__("Enable","pie-register").'</span>';
                        }
                        else
                        {
                            echo '<span class="installation_status_faild">'.esc_html__("Disable","pie-register").'</span>';
                            echo '<span class="quotation">'.esc_html__("Please install CURL on the server.","pie-register").'</span>';
                        }
                        ?>
                        </div>
                        <div class="fields">
                        <label><?php esc_html_e("zip Extention",'pie-register') ?></label>
                        <?php if(extension_loaded('zip'))
                        {
                            echo '<span class="installation_status">'.esc_html__("Enable","pie-register").'</span>';
                        }
                        else
                        {
                            echo '<span class="installation_status_faild">'.esc_html__("Disable","pie-register").'</span>';
                            echo '<span class="quotation">'.esc_html__("Please install the zip extension on the server.","pie-register").'</span>';
                        }
                        ?>
                        </div>
                        <div class="fields">
                        <label><?php esc_html_e("File Get Contents",'pie-register') ?></label>
                        <?php if(function_exists('file_get_contents'))
                        {
                            echo '<span class="installation_status">'.esc_html__("Enable","pie-register").'</span>';
                        }
                        else
                        {
                            echo '<span class="installation_status_faild">'.esc_html__("Disable","pie-register").'</span>';
                            echo '<span class="quotation">'.esc_html__("Please install File Get Contents on the server.","pie-register").'</span>';
                        }
                        ?>
                        </div>
                        <div class="fields">
                        <label><?php esc_html_e("MB String",'pie-register') ?></label>
                        <?php if (extension_loaded('mbstring'))
                        {
                            echo '<span class="installation_status">'.esc_html__("Enable","pie-register").'</span>';
                        }
                        else
                        {
                            echo '<span class="installation_status_faild">'.esc_html__("Disable","pie-register").'</span>';
                            echo '<span class="quotation">'.esc_html__("Please install File Get Contents on the server.","pie-register").'</span>';
                        }
                        ?>
                        </div>
                        <?php if ( function_exists( 'ini_get' ) ){ ?>
                        
                                <div class="fields">
                                    <label><?php esc_html_e("PHP Post Max Size",'pie-register') ?></label>
                                    <?php
                                    echo '<span class="installation_status installation_status_no_bg">'.(ini_get('post_max_size')).'</span>';
                                ?>
                                </div>
                                <div class="fields">
                                    <label><?php esc_html_e("PHP Time Limit",'pie-register') ?></label>
                                    <?php
                                    echo '<span class="installation_status installation_status_no_bg">'.(ini_get('max_execution_time')).'</span>';
                                ?>
                                </div>
                                
                        <?php } else {?>
                                <div class="fields">
                                    <label><?php esc_html_e("ini_get",'pie-register') ?></label><?php
                                    echo '<span class="installation_status_faild">'.esc_html__("Disable","pie-register").'</span>';
                                    echo '<span class="quotation">'.esc_html__("Please install ini_get on the server.","pie-register").'</span>';
                                ?>
                                </div>
                        <?php } ?>
                        <div class="fields">
                        <label><?php esc_html_e("WP Memory Limit",'pie-register') ?></label>
                        <?php
                        echo '<span class="installation_status installation_status_no_bg">'.WP_MEMORY_LIMIT.'</span>';
                        ?>
                        </div>
                        <div class="fields">
                        <label><?php esc_html_e("WP Debug Mode",'pie-register') ?></label>
                        <?php
                        if ( defined('WP_DEBUG') && WP_DEBUG ) echo '<span class="installation_status installation_status_no_bg">' . esc_html__( 'Yes', 'pie-register' ) . '</span>'; else echo '<span class="installation_status installation_status_no_bg">' . esc_html__( 'No', 'pie-register' ) . '</span>';
                        ?>
                        </div>
                        <div class="fields">
                        <label><?php esc_html_e("WP Language",'pie-register') ?></label>
                        <?php
                        echo '<span class="installation_status installation_status_no_bg">' . get_locale() . '</span>';
                        ?>
                        </div>
                        <div class="fields">
                        <label><?php esc_html_e("WP Max Upload Size",'pie-register') ?></label>
                        <?php
                        echo '<span class="installation_status installation_status_no_bg">' . size_format( wp_max_upload_size() ) . '</span>';
                        ?>
                        </div>
                <?php }elseif($action == "version" && $subaction == "plugins-themes"){ ?>
                            <textarea id="piereg_log2_view_area" name="piereg_log2_view_area" style="max-width:100%;min-width:50%;width:100%;height:300px;" readonly="readonly"><?php 
                    
                                    $themes = wp_get_themes();
                                    #$current_theme = get_current_theme(); get_current_theme() is deprecated since version 3.4!
                                    $current_theme = wp_get_theme();
                                    echo "================= Themes =================\r\n\r\n";
                                    foreach($themes as $theme){
                                        if( $current_theme == $theme['Name'] )
                                            echo esc_html($theme['Name'])." [ACTIVATED]\r\n";
                                        else
                                            echo esc_html($theme['Name'])." [DEACTIVATED]\r\n";
                                    }
                                    
                                    $activate_plugins 	= get_option('active_plugins');
                                    $all_plugins 		= get_plugins();
                                    echo "\r\n\r\n================= Plugins (".count($activate_plugins)."/".count($all_plugins).") =================\r\n\r\n";
                                    foreach($all_plugins as $key=>$plugin){
                                        if( in_array($key,$activate_plugins) )
                                            echo esc_html($plugin['Name'])." [ACTIVATED]\r\n";
                                        else
                                            echo esc_html($plugin['Name'])." [DEACTIVATED]\r\n";
                                    }
                ?></textarea>
                
                <?php }elseif($action == "version" && $subaction == "error-log"){ ?>
                    <div class="piereg_log_file_download">            
                        <div class="piereg_log_file_view">
                            <textarea id="piereg_log_file_view_textarea" name="piereg_log_file_view_textarea" readonly="readonly" style="max-width:100%;min-width:50%;width:100%;height:300px;"><?php echo esc_textarea($this->piereg_get_log_file());  ?></textarea>
                        </div>
                    </div>
                <?php } ?>            
            </div>
        </div>    
    </div>
  </div>
</div>