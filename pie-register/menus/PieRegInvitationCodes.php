<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<?php
global $piereg_dir_path;
if( file_exists(PIEREG_DIR_NAME."/classes/invitation_code_pagination.php") )
  include_once( PIEREG_DIR_NAME."/classes/invitation_code_pagination.php");
$piereg = get_option(OPTION_PIE_REGISTER);
?>
<form method="post" action="" id="del_form">
<?php if( function_exists( 'wp_nonce_field' )) wp_nonce_field( 'piereg_wp_invitation_code_del_form_nonce','piereg_invitation_code_del_form_nonce'); ?>
  <input type="hidden" id="invi_del_id" name="invi_del_id" value="0" />
</form>
<form method="post" action="" id="status_form">
<?php if( function_exists( 'wp_nonce_field' )) wp_nonce_field( 'piereg_wp_invitation_code_status_form_nonce','piereg_invitation_code_status_form_nonce'); ?>
  <input type="hidden" id="status_id" name="status_id" value="0" />
</form>
<div id="container" class="pieregister-admin">
  <div class="right_section">
    <div class="invitation settings">
      <h2 class="fullheading">
        <?php esc_html_e("Invitation Codes",'pie-register'); ?>
      </h2>
      <?php
       // invite_through_email  
       if(isset($this->pie_post_array['notice']) && !empty($this->pie_post_array['notice']) ){

          if(is_array($this->pie_post_array['notice'])) {
            foreach( $this->pie_post_array['notice'] as $msg ) {
              echo '<div id="message" class="updated fade msg_belowheading ext-space"><p><strong>' . wp_kses_post($msg) . '.</strong></p></div>';
            }
          } else {
            echo '<div id="message" class="updated fade msg_belowheading ext-space"><p><strong>' . wp_kses_post($this->pie_post_array['notice']) . '.</strong></p></div>';
          }
       }  
       
       if( isset($this->pie_post_array['error_message']) && !empty( $this->pie_post_array['error_message'] ) )
          echo '<div style="clear: both;float: none;"><p class="error">' . wp_kses_post($this->pie_post_array['error_message'])  . "</p></div>";

       if( isset($this->pie_post_array['error']) && !empty( $this->pie_post_array['error'] ) ){
        if(is_array($this->pie_post_array['error'])) {
            foreach( $this->pie_post_array['error'] as $msg ) {
              echo '<div style="clear: both;float: none;"><p class="error">' . wp_kses_post($msg) . "</p></div>";
            }
          } else {
            echo '<div style="clear: both;float: none;"><p class="error">' . wp_kses_post($this->pie_post_array['error'])  . "</p></div>";
          }
       }
          
       if(isset( $this->pie_post_array['success_message'] ) && !empty( $this->pie_post_array['success_message'] ))
          echo '<div style="clear: both;float: none;"><p class="success">' . wp_kses_post($this->pie_post_array['success_message'])  . "</p></div>";
      
      ?>
      
    <div class="invite-tab-content">
       <form method="post" action="">
          <?php if( function_exists( 'wp_nonce_field' )) wp_nonce_field( 'piereg_wp_invitation_code_enable_nonce','piereg_invitation_code_enable_nonce'); ?>
          <ul class="clearfix">
            <li>
              <div class="fields">
                <div class="radio_fields">
                  <input type="checkbox" name="enable_invitation_codes" id="enable_invitation_codes" value="1" <?php checked($piereg['enable_invitation_codes']=="1", true); ?> />
                  <input type="hidden" name="hdn_enable_invitation_codes" value="1" />
                </div>
                <label for="enable_invitation_codes" class="labelaligned enable-invite">
                  <?php esc_html_e("Enable Invitation Codes","pie-register");?>
                </label>
              </div>
            </li>
            <li class="no-margin clearfix">
              <div class="fields">
                <p>
                  <i> <?php esc_html_e("Protect your privacy. If you want your blog to be exclusive, enable Invitation Codes to allow users to register by invitation only.",'pie-register'); ?>
                  </i>
                  <br />
                  <strong><?php esc_html_e("Note",'pie-register') ?> :</strong> <?php esc_html_e("You must add the invitation code field to your registration form.",'pie-register') ?></p>
              </div>
            </li>
          </ul>
          <div id="piereg-detailed-guide-link">
            <a target="_blank" href="https://pieregister.com/documentation/how-to-use-invitation-codes-with-pie-register/"><?php esc_html_e("Learn how to use Invitation Codes","pie-register"); ?></a>
          </div>
          </form>

        <div class="invite-tabs clearfix">
          <ul>
            <li <?php if(!isset($_GET['autogenerate']) && !isset($_GET['inviteemail']) && !isset($_GET['allowuserinvitation']) && !isset($_GET['usertracker'])){ echo 'class="invite-active"'; } ?>><a href="admin.php?page=pie-invitation-codes&rawcode">Raw Codes</a></li>
            <li <?php if(isset($_GET['autogenerate'])){ echo 'class="invite-active"'; } ?>><a href="admin.php?page=pie-invitation-codes&autogenerate">Auto Generate Codes</a></li>
            <li <?php if(isset($_GET['inviteemail'])){ echo 'class="invite-active"'; } ?>><a href="admin.php?page=pie-invitation-codes&inviteemail">Invite Through Email</a></li>
            <li <?php if(isset($_GET['allowuserinvitation'])){ echo 'class="invite-active"'; } ?>><a href="admin.php?page=pie-invitation-codes&allowuserinvitation">Allow Users to Invite</a></li>
            <li <?php if(isset($_GET['usertracker'])){ echo 'class="invite-active"'; } ?>><a href="admin.php?page=pie-invitation-codes&usertracker">User Tracker</a></li>
          </ul>
        </div>
        <?php 
          if(!isset($_GET['autogenerate']) && !isset($_GET['inviteemail']) && !isset($_GET['allowuserinvitation']) && !isset($_GET['usertracker'])){
             $this->require_once_file(PIEREG_DIR_NAME.'/menus/invitations/custom-code.php');
          } else if(isset($_GET['autogenerate'])){
             $this->require_once_file(PIEREG_DIR_NAME.'/menus/invitations/autogenerate.php');
          } else if(isset($_GET['inviteemail'])){
             $this->require_once_file(PIEREG_DIR_NAME.'/menus/invitations/inviteemail.php');
          } else if(isset($_GET['allowuserinvitation'])){
            $this->require_once_file(PIEREG_DIR_NAME.'/menus/invitations/allowuserinvitation.php');
          } else if(isset($_GET['usertracker'])){
          $this->require_once_file(PIEREG_DIR_NAME.'/menus/invitations/usertracker.php');
       }
          
        ?> 
       
    </div><!-- invite-tab-content -->
  <?php if(!isset($_GET['inviteemail']) && !isset($_GET['allowuserinvitation']) && !isset($_GET['usertracker'])){ ?>
      <div style="float:left;" class="invitaion_code_bulk_action_form_div">
        <form method="post" onsubmit="return get_selected_box_ids();" >
          <?php if( function_exists( 'wp_nonce_field' )) wp_nonce_field( 'piereg_wp_invitaion_code_bulk_option_nonce','piereg_invitaion_code_bulk_option_nonce'); ?>
          <input type="hidden" value="" name="select_invitaion_code_bulk_option" id="select_invitaion_code_bulk_option">
            <select name="invitaion_code_bulk_option" id="invitaion_code_bulk_option">
            <option selected="selected" value="0">
            <?php esc_html_e("Bulk Actions","pie-register"); ?>
            </option>
            <option value="delete">
            <?php esc_html_e("Delete","pie-register"); ?>
            </option>
            <option value="active">
            <?php esc_html_e("Activate","pie-register"); ?>
            </option>
            <option value="unactive">
            <?php esc_html_e("Deactivate","pie-register"); ?>
            </option>
          </select>
          <input type="submit" value="<?php esc_attr_e("Apply","pie-register"); ?>" class="button action" id="doaction" name="btn_submit_invitaion_code_bulk_option">
        </form>
        <span style="color:#F00;display:none;" id="invitaion_code_error"><?php esc_html_e("Select invitation codes to perform bulk operation.","pie-register");?></span>
      </div>

      <?php 
      $Pie_Invitation_Table = new Pie_Invitation_Table();
      $Pie_Invitation_Table->set_order();
          $Pie_Invitation_Table->set_orderby();
          $Pie_Invitation_Table->prepare_items();
          $Pie_Invitation_Table->search_box("Search", "search_invitaion_code");
      $Pie_Invitation_Table->display();
        ?>
      <div style="float:right;padding-right:5px;margin-top:11px;">
        <form method="post" id="form_invitation_code_per_page_items">
          <?php if( function_exists( 'wp_nonce_field' )) wp_nonce_field( 'piereg_wp_invitation_code_per_page_nonce','piereg_invitation_code_per_page_nonce'); ?>
          <?php esc_html_e("Items Per Page","pie-register"); ?>
          <select name="invitation_code_per_page_items" id="invitation_code_per_page_items" title="<?php esc_attr_e("Invitation codes to show on a page.","pie-register"); ?>">
            <?php
              //$opt = get_option("pie_register");
              $opt = get_option(OPTION_PIE_REGISTER);
              $per_page = ( isset($opt['invitaion_codes_pagination_number']) && ((int)$opt['invitaion_codes_pagination_number']) != 0) ? (int)$opt['invitaion_codes_pagination_number'] : 10;
              
              for($per_page_item = 10; $per_page_item <= 50; $per_page_item +=10)
              {
                $checked = selected($per_page == $per_page_item, true);
                echo '<option value="'.esc_attr($per_page_item).'" '.$checked.'>'.esc_html($per_page_item).'</option>';
              }
              echo '<option value="75" '.selected($per_page == "75", true, true).' >75</option>';
              echo '<option value="100" '.selected($per_page == "100", true, true).' >100</option>';
            ?>
          </select>
        </form>
      </div>
    <?php } ?>
    </div>
  </div>
</div>