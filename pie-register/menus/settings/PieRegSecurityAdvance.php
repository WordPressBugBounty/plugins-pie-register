<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<?php $piereg = $this->get_pr_global_options();

	$_disable 			= true;
if( !isset($_GET['act']) || !isset($_GET['pie_id']) || !isset($_GET['option']) )
{
	?>
    <form name="frm_settings_security_advanced" action="" method="post">
    <?php if( function_exists( 'wp_nonce_field' )) wp_nonce_field( 'piereg_wp_settings_security_advanced','piereg_settings_security_advanced'); ?>
        <div id="piereg-premium-link">
            <div class="piereg-premium-link-items">
                <div class="piereg-premium-link-icon">
                    <img src="<?php echo esc_url(PIEREG_PLUGIN_URL . 'assets/images/pr-premium-icon.png');?>" alt="">
                </div>
                <span><a class="piereg-premium-link-text" href="https://pieregister.com/plan-and-pricing/" target="_blank" >This feature is available in premium version</a> </span>
            </div>
        </div>
        <div class="fields">
        	<div class="piereg_box_style_1">
             <input <?php disabled($_disable, true); ?> type="checkbox" name="reg_form_submission_time_enable" id="reg_form_submission_time_enable" value="1" <?php checked($piereg['reg_form_submission_time_enable']=="1", true); ?> /> 
             <?php esc_html_e("Time form submission, reject form if submitted within ",'pie-register') ?>
             <input <?php disabled($_disable, true); ?> type="text" name="reg_form_submission_time" 
             		style="width:auto;"
                    id="reg_form_submission_time" 
                    value="<?php echo ( (isset($piereg['reg_form_submission_time']) && !empty($piereg['reg_form_submission_time'])) ? esc_attr($piereg['reg_form_submission_time']) : 0 ); ?>" 
                    class="input_fields submissionfield"
                    />
                    <?php esc_html_e("seconds or less.",'pie-register') ?>
            <span class="quotation" style="margin-left:0px;"><?php esc_html_e("Security enhancement for forms (timed submission)",'pie-register') ?></span>
            </div>
        </div>
        <div class="fields">
        	<div class="piereg_box_style_1 limit-submission">
             <?php esc_html_e("Form Submission Limit for a Device ",'pie-register') ?>
             <input <?php disabled($_disable, true); ?> type="text" name="reg_form_submission_limit" 
             		style="width:auto;"
                    id="reg_form_submission_limit" 
                    value="<?php echo ( (isset($piereg['reg_form_submission_limit']) && !empty($piereg['reg_form_submission_limit'])) ? esc_attr($piereg['reg_form_submission_limit']) : 0 ); ?>" 
                    class="input_fields submissionfield" 
                    /><br>
            <span class="quotation" style="margin-left:0px;"><?php esc_html_e("Limits how many times a form can be submitted from a device within a day. Helpful to prevent spams. Set it to zero(0) to disable this feature.",'pie-register') ?></span>
            </div>
        </div>
        <div class="fields">
            <input name="action" value="pie_reg_settings" type="hidden" />
            <input <?php disabled($_disable, true); ?> type="submit" class="submit_btn flt_none" value="<?php esc_attr_e("Save Settings","pie-register"); ?>" />
        </div>
    </form>
<hr class="seperator" />    
<?php 
}
	?>
<h2 class="hydin_without_bg mar_none"><?php esc_html_e("Restrict Widgets",'pie-register') ?></h2>
    <div class="piereg_clear"></div>        
	<p><?php esc_html_e('Upgrade to premium version to use the Restrict Widgets feature. Want to learn about this feature ?','pie-register');?> 
    <a href="https://pieregister.com/manual/pie-register-features/7329-2/" target="_blank"><?php esc_html_e('Click Here','pie-register');?></a>
    </p>
