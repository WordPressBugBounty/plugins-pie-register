<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<?php $piereg = $this->get_pr_global_options(); ?>
<div class="pieregister-admin">
<div class="pie_notification_area">
    <div class="settings pad_bot_none">
        <h2 class="headingwidth"><?php esc_html_e("Email Notifications",'pie-register') ?></h2>
		<div id="piereg-detailed-guide-link">
			<a target="_blank" href="https://pieregister.com/documentation/how-to-send-custom-email-notifications-using-pie-register/"><?php esc_html_e("Learn how to use Email Notifications","pie-register"); ?></a>
		</div>  
        <?php
			if(isset($this->pie_post_array['notice']) && $this->pie_post_array['notice'] ){
				echo '<div id="message" class="updated fade msg_belowheading"><p><strong>' . wp_kses_post($this->pie_post_array['notice']) . '.</strong></p></div>';
			}
			if( isset($this->pie_post_array['error_message']) && !empty( $this->pie_post_array['error_message'] ) )
				echo '<div style="clear: both;float: none;"><p class="error">' . wp_kses_post($this->pie_post_array['error_message'])  . "</p></div>";
			if( isset($this->pie_post_array['error']) && !empty( $this->pie_post_array['error'] ) )
				echo '<div style="clear: both;float: none;"><p class="error">' . wp_kses_post($this->pie_post_array['error'])  . "</p></div>";
			if(isset( $this->pie_post_array['success_message'] ) && !empty( $this->pie_post_array['success_message'] ))
				echo '<div style="clear: both;float: none;"><p class="success">' . wp_kses_post($this->pie_post_array['success_message'])  . "</p></div>";
			?>
        <div class="invite-tabs notification-tabs clearfix">
          <ul>
            <li <?php if(!isset($_GET['user_notification'])){ echo 'class="invite-active"'; } ?>><a href="admin.php?page=pie-notifications&admin_notification"><?php esc_html_e("Admin Notification","pie-register") ?></a></li>
            <li <?php if(isset($_GET['user_notification'])){ echo 'class="invite-active"'; } ?>><a href="admin.php?page=pie-notifications&user_notification"><?php esc_html_e("User Notification","pie-register") ?></a></li>
          </ul>
        </div>
	</div>
    <div id="container" class="pieregister-admin pieregister-admin-notification">
        <?php 
        	if(!isset($_GET['user_notification'])){
        		$this->require_once_file(PIEREG_DIR_NAME.'/menus/notification/PieRegAdminNotification.php');
        	} else {
        		$this->require_once_file(PIEREG_DIR_NAME.'/menus/notification/PieRegUserNotification.php');
        	}

         ?>
        
    </div> 
</div>
</div>