<?php 
	if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
	# Define variable's default values
	$action = $subaction = "";
	$active	= "active";
	
	if(isset($_GET['tab']))
		$action	= sanitize_key($_GET['tab']);
	if(isset($_GET['subtab']))
		$subaction	= sanitize_key($_GET['subtab']);
	?>  
<div id="container"  class="pieregister-admin">
  <div class="right_section">
    <div class="settings">
        <h2 class="headingwidth"><?php esc_html_e("Settings",'pie-register') ?></h2>  
        <div id="piereg-detailed-guide-link">
            <a target="_blank" href="https://pieregister.com/documentation/settings/"><?php esc_html_e("Learn how to Configure Settings","pie-register"); ?></a>
        </div>    
      <div class="rest_btn_wrap">
        <form id="frm_default" method="post" onsubmit="return window.confirm('Are you sure? It will restore all the plugin settings to default.');">
          <input type="button" onclick="jQuery('#frm_default').submit();" class="submit_btn flt_none" value="<?php esc_attr_e("Reset to Default","pie-register");?>" />
          <input type="hidden" value="1" name="piereg_default_settings" />
        </form>
      </div>
      <?php 
	  	if( isset($this->pie_post_array['notice']) && !empty($this->pie_post_array['notice']) ){
			echo '<div id="message" class="updated fade msg_belowheading"><p><strong>' . wp_kses_post($this->pie_post_array['notice']) . '</strong></p></div>';
			
			# Role Based Pages On Edit Section
			if(	!isset($_GET['action']) && !isset($_GET['pie_id']) ) {
				$_POST['piereg_user_role'] = $_POST['logged_in_url'] = $_POST['log_in_page'] = $_POST['log_out_url'] = $_POST['log_out_page'] = "";
			}
		}
		else if( isset($this->pie_post_array['error']) && !empty($this->pie_post_array['error']) ){
			echo '<div id="error" class="error fade msg_belowheading"><p><strong>' . wp_kses_post($this->pie_post_array['error']) . '</strong></p></div>';
		}		
		
		if(  isset($this->pie_post_array['success']) && !empty($this->pie_post_array['success']) ){
			echo '<div id="message" class="updated fade msg_belowheading"><p><strong>' . wp_kses_post($this->pie_post_array['license_success']) . '.</strong></p></div>';
		}
		
		?>
        <div id="tabsSetting" class="tabsSetting">
            <ul class="tabLayer1">
                <li class="pr-pages-tab <?php echo ($action == "pages" || $action == "") ? esc_attr($active) :""; ?>">
                	<a href="admin.php?page=pie-settings&tab=pages"><?php esc_html_e("Pages",'pie-register') ?></a>
                    <ul class="tabLayer2">
                        <li class="<?php echo ( ($action == "pages" && $subaction == "") || ($action == "" && $subaction == "") || $subaction == "all-users" ) ? esc_attr($active) :""; ?>">
                        	<a href="admin.php?page=pie-settings&tab=pages&subtab=all-users"><?php esc_html_e("All Users",'pie-register') ?></a></li>                        
                        <li class="<?php echo ($subaction == "role-based") ? esc_attr($active) :""; ?>">
                            <a href="admin.php?page=pie-settings&tab=pages&subtab=role-based"><?php esc_html_e("Role Based Redirect",'pie-register') ?></a></li>
                    </ul>
                </li>
            	<li class="pr-ux-tab <?php echo ($action == "ux") ? esc_attr($active) :""; ?>" >
                	<a href="admin.php?page=pie-settings&tab=ux"><?php esc_html_e("UX",'pie-register') ?></a>
                	<ul class="tabLayer2">
                        <li class="<?php echo ( ($action == "ux" && $subaction == "") || $subaction == "basic" ) ? esc_attr($active) :""; ?>">
                        	<a href="admin.php?page=pie-settings&tab=ux&subtab=basic"><?php esc_html_e("Basic",'pie-register') ?></a></li>  
                        <li class="<?php echo ($subaction == "advanced") ? esc_attr($active) :""; ?>">
                        	<a href="admin.php?page=pie-settings&tab=ux&subtab=advanced"><?php esc_html_e("Advanced",'pie-register') ?></a></li>
                    </ul>
                </li>
            	<li class="pr-overrides-tab <?php echo ($action == "overrides") ? esc_attr($active) :""; ?>">
                	<a href="admin.php?page=pie-settings&tab=overrides"><?php esc_html_e("Overrides",'pie-register') ?></a></li>
                <li class="<?php echo ($action == "geo_location") ? esc_attr($active) : ""; ?>">
                    <a href="admin.php?page=pie-settings&tab=geo_location"><?php esc_html_e("Geolocation", 'piereg') ?></a></li>
                <li class="pr-security-tab <?php echo ($action == "security") ? esc_attr($active) :""; ?>">
                	<a href="admin.php?page=pie-settings&tab=security"><?php esc_html_e("Security",'pie-register') ?></a>
                    <ul class="tabLayer2">
                        <li class="<?php echo ( ($action == "security" && $subaction == "") || $subaction == "sbasic" ) ? esc_attr($active) :""; ?>">
                        	<a href="admin.php?page=pie-settings&tab=security&subtab=sbasic"><?php esc_html_e("Basic",'pie-register') ?></a></li>
                        <li class="<?php echo ($subaction == "sadvanced") ? esc_attr($active) :""; ?>">
                        	<a href="admin.php?page=pie-settings&tab=security&subtab=sadvanced"><?php esc_html_e("Advanced",'pie-register') ?></a></li>
                    </ul>
                </li>
            </ul>
        </div>
                
        <div class="wrapper-forms">
        	<div class="right_section">
        		<div class="settings">
                <?php 
                    if( ($action == "pages") || $action == "") { 
                        
                        if($action == "pages" && $subaction == "role-based") { 
                            $this->require_once_file(PIEREG_DIR_NAME.'/menus/settings/PieRegPagesRoleBased.php');
                        
                        } else {	
                        
                            $this->require_once_file(PIEREG_DIR_NAME.'/menus/settings/PieRegPagesAllUsers.php');			
                        } 
                    
                    } elseif ($action == "geo_location") {
                        
                        if ( is_plugin_active( 'pie-register-geolocation/pie-register-geolocation.php' ) ) do_action( 'piereg_getting_geo_location_menu' );
                        
                        else do_action("pie_register_geolocation_addon_page");
                        
                    } elseif($action == "ux") { 
                        
                        $this->require_once_file(PIEREG_DIR_NAME.'/menus/settings/PieRegUX.php');
                        
                    } elseif($action == "overrides") { 
                    
                        $this->require_once_file(PIEREG_DIR_NAME.'/menus/settings/PieRegOverrides.php');
                    
                    } elseif($action == "security") { 
                    
                        if($action == "security" && $subaction == "" || $subaction == "sbasic") { 
                            
                            $this->require_once_file(PIEREG_DIR_NAME.'/menus/settings/PieRegSecurityBasic.php');
                        
                        } else {	
                        
                            $this->require_once_file(PIEREG_DIR_NAME.'/menus/settings/PieRegSecurityAdvance.php');		
                        }
                    
                    }  
                    ?>
        		</div>
        	</div>    
        </div>
    </div>
  </div>
</div>