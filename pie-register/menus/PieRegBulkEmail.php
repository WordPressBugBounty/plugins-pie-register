<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>

<div class="pieregister-admin">
	<div class="hideBorder bulkemail_tabs">
		<div class="settings">

    		<h2 class="headingwidth"><?php esc_html_e("Bulk Email",'pie-register') ?></h2>
			<div id="piereg-detailed-guide-link">
				<a target="_blank" href="https://pieregister.com/documentation/how-to-send-bulk-e-mails-using-pie-register/"><?php esc_html_e("Learn how to use Bulk Email Addon","pie-register"); ?></a>
			</div>
			
			<?php
				// phpcs:disable WordPress.Security.NonceVerification.Missing -- display only: $_POST notice/error/warning are plain-text messages set by add-on save handlers (after their own nonce check); printed as text with esc_html, never stored.
				if( ! empty( $_POST['notice'] ) ){
					echo '<div id="message" class="updated fade msg_belowheading"><p>' . esc_html( sanitize_text_field( wp_unslash( $_POST['notice'] ) ) ) . '</p></div>';
				}elseif( ! empty( $_POST['error'] ) ){
					echo '<div id="error" class="error fade msg_belowheading"><p>' . esc_html( sanitize_text_field( wp_unslash( $_POST['error'] ) ) ) . '</p></div>';
				}
				if( ! empty( $_POST['warning'] ) ){
					echo '<div id="warning" class="warning fade msg_belowheading"><p>' . esc_html( sanitize_text_field( wp_unslash( $_POST['warning'] ) ) ) . '</p></div>';
				}
				// phpcs:enable WordPress.Security.NonceVerification.Missing
			?>
			
        	
			<?php if ( is_plugin_active('pie-register-bulkemail/pie-register-bulkemail.php') ) { ?>
				<div class="invite-tabs clearfix">
				<ul>
					<?php // phpcs:disable WordPress.Security.NonceVerification.Recommended -- read-only tab flags used only to highlight the active tab ?>
					<li <?php if(!isset($_GET['piereg_bulkemail_drafts']) && !isset($_GET['piereg_bulkemail_sent_items'])){ echo 'class="invite-active"'; } ?>><a href="admin.php?page=pie-bulkemail&piereg_bulkemail"><?php esc_html_e("Create Email","pie-register")?></a></li>
					<li <?php if(isset($_GET['piereg_bulkemail_drafts'])){ echo 'class="invite-active"'; } ?>><a href="admin.php?page=pie-bulkemail&piereg_bulkemail_drafts"><?php esc_html_e("Drafts","pie-register")?></a></li>
					<li <?php if(isset($_GET['piereg_bulkemail_sent_items'])){ echo 'class="invite-active"'; } ?>><a href="admin.php?page=pie-bulkemail&piereg_bulkemail_sent_items"><?php esc_html_e("Sent Items","pie-register")?></a></li>
					<?php // phpcs:enable WordPress.Security.NonceVerification.Recommended ?>
				</ul>
				</div>
			<?php } ?>
				
			<?php do_action("piereg_show_bulk_email_settings"); ?>
			
		</div>
	</div>
</div>