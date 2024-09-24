<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<?php $piereg = get_option(OPTION_PIE_REGISTER); ?>
<div class="pieregister-admin" style="width:99%;overflow:hidden;">
  <div id="container">
    <div class="right_section">
      <div class="settings pie_wrap" style="padding-bottom:0px;">
        <?php
		if( isset($this->pie_post_array['error_message']) && !empty( $this->pie_post_array['error_message'] ) )
			echo '<div style="clear: both;float: none;"><p class="error">' . wp_kses_post($this->pie_post_array['error_message']) . "</p></div>";
		if(isset( $this->pie_post_array['success_message'] ) && !empty( $this->pie_post_array['success_message'] ))
			echo '<div style="clear: both;float: none;"><p class="success">' . wp_kses_post($this->pie_post_array['success_message']) . "</p></div>";
			?>
      </div>
      <div class="settings" style="padding-bottom:0px;">
        <div class="pieHelpTicket">
            	<a 
              	href="javascript:;" 
                title="With Pie Register Pro you can have an unlimited number of registration forms." 
                class="button button-large button-disaled">
                &nbsp; <?php esc_html_e("Add New","pie-register"); ?> &nbsp;
              </a>
              <div id="piereg-premium-link">
                <div class="piereg-premium-link-items">
                    <div class="piereg-premium-link-icon">
                      <img src="<?php echo esc_url(PIEREG_PLUGIN_URL . 'assets/images/pr-premium-icon.png');?>" alt="">
                    </div>
                    <span><a class="piereg-premium-link-text" href="https://pieregister.com/plan-and-pricing/" target="_blank" >This feature is available in premium version</a> </span>
                </div>
              </div> 
          </div>
      </div>
      <div class="pieHelpTicket">
        <div class="settings" style="padding-bottom:0px;">
          <table cellspacing="0" class="piereg_form_table">
            <thead>
              <tr>
                <th></th>
                <th><?php esc_html_e("ID","pie-register"); ?></th>
                <th><?php esc_html_e("Form Title","pie-register"); ?></th>
                <th><?php esc_html_e("Views","pie-register"); ?></th>
                <th><?php esc_html_e("Submissions","pie-register"); ?></th>
                <th><?php esc_html_e("Shortcode","pie-register"); ?></th>
              </tr>
            </thead>
            <tfoot>
              <tr>
                <th></th>
                <th><?php esc_html_e("ID","pie-register"); ?></th>
                <th><?php esc_html_e("Form Title","pie-register"); ?></th>
                <th><?php esc_html_e("Views","pie-register"); ?></th>
                <th><?php esc_html_e("Submissions","pie-register"); ?></th>
                <th><?php esc_html_e("Shortcode","pie-register"); ?></th>
              </tr>
            </tfoot>
            <tbody class="">
              <?php

$form_on_free	= get_option("piereg_form_free_id");

	$option = get_option("piereg_form_field_option_".$form_on_free);
	if( !empty($option) && is_array($option) && isset($option['Id']) && (!isset($option['IsDeleted']) || trim($option['IsDeleted']) != 1) )
	{			
		?>
      <tr>
        <td><?php if(trim($option['Status']) != "" and $option['Status'] == "enable"): ?>
          <a href="<?php echo esc_url('admin.php?page=pie-register&prfrmid='. esc_attr($option['Id']).'&status=disenable') ?>"> <img title="Deactivate" alt="Deactivate" src="<?php echo esc_url(plugins_url("../assets/images/active1.png",__FILE__)); ?>"> </a>
          <?php else:  ?>
          <a href="<?php echo esc_url('admin.php?page=pie-register&prfrmid='. esc_attr($option['Id']) .'&status=enable') ?>"> <img title="Activate" alt="Activate" src="<?php echo esc_url(plugins_url("../assets/images/active0.png",__FILE__)); ?>"> </a>
          <?php endif; ?></td>
        <td class="column-id"><?php echo esc_html($option['Id']); ?></td>
        <td class="column-title"><strong><?php echo esc_html($option['Title']); ?></strong>
          <div class="piereg-actions">
            <span class="edit"><a class="underlinenone" href=" <?php echo esc_url('admin.php?page=pr_new_registration_form&form_id='. esc_attr($option['Id']) ) ?>" title="Edit this form">Edit</a> | </span>
            <?php 
            $preview_url = add_query_arg(array('pr_preview'=>1,'form_id'=>$option['Id'],'prFormId'=>$option['Id']), get_permalink($piereg['alternate_register']));
            ?>
            <span class="edit"><a onclick="javascript:('Are you sure you want to delete this form?','admin.php?page=pie-register&prfrmid=<?php echo esc_attr($option['Id']); ?>&action=delete');" title="Delete this form">Delete</a> | </span>
            <span class="edit"><a class="underlinenone" href="<?php echo esc_url($preview_url); ?>" target="_blank" title="Preview this form">Preview</a> </span>
          </div>
        </td>
        <td class="column-date"><strong><?php echo esc_html($option['Views']) ?></strong></td>
        <td class="column-date"><strong><?php echo esc_html($option['Entries']); ?></strong></td>
        <td class="column-date" ><div class="style_textarea" onclick="selectText('piereg-select-all-text-onclick_<?php echo esc_js($option['Id']); ?>')" id="piereg-select-all-text-onclick_<?php echo esc_attr($option['Id']); ?>" readonly="readonly"><?php echo '[pie_register_form id="'.esc_html($option['Id']).'" title="true" description="true" ]' ?></div></td>
      </tr>
              <?php 
		
			if( !$form_on_free )
			{
				update_option('piereg_form_free_id', $option['Id']);
				$form_on_free .= $option['Id'];
			}
	}
			if(!$option){ ?>
              <tr>
                <td colspan="6"><h3>
                    <?php esc_html_e("No Registration Form Found","pie-register"); ?>
                  </h3></td>
              </tr>
            <?php }?>
            </tbody>
          </table>
        </div>
      </div>
      <?php 
      if( current_user_can( 'piereg_manage_cap' ) ){
      do_action( 'admin_notices_specific_pages');	
      }
    ?>
    </div>
  </div>
</div>