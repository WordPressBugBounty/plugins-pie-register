<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<?php 
	$coming_soon_aweber = false;
	?>
<div class="pieregister-admin">
<div id="exportusers_tabs" class="hideBorder">
	<div class="settings">
    	<h2 class="headingwidth"><?php esc_html_e("Marketing",'pie-register') ?></h2>
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
				<?php do_action("piereg_email_services"); ?>
                <?php 
				if( $coming_soon_aweber ) { ?>
                    <div id="piereg_aweber"><div class="right_section image_wrapper"><img class="comming_soon_img" alt="Aweber Coming Soon" src="<?php echo esc_url(PIEREG_PLUGIN_URL.'assets/images/Coming-soon_aweber.png'); ?>" /></div></div>    
                <?php } ?>
	</div>
</div>
</div>