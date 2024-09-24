<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<?php $piereg = $this->get_pr_global_options();
?>
<div class="pieregister-admin">
<div class="pie_restrictions_area">
    <div class="settings pad_bot_none">
        <h2 class="headingwidth"><?php _e("User Control",'pie-register') ?></h2>
        <div id="piereg-detailed-guide-link">
          <a target="_blank" href="https://pieregister.com/documentation/user-control/"><?php esc_html_e("Learn how to Control Users","pie-register"); ?></a>
        </div>  
        <div id="piereg-premium-link">
          <div class="piereg-premium-link-items">
              <div class="piereg-premium-link-icon">
                <img src="<?php echo esc_url(PIEREG_PLUGIN_URL . 'assets/images/pr-premium-icon.png');?>" alt="">
              </div>
              <span><a class="piereg-premium-link-text" href="https://pieregister.com/plan-and-pricing/" target="_blank" >This feature is available in premium version</a> </span>
          </div>
        </div> 
        <div class="invite-tabs restrictions-tabs clearfix">
          <ul>
            <li <?php if(!isset($_GET['allowed_users'])){ echo 'class="invite-active"'; } ?>><a href="admin.php?page=pie-black-listed-users&block_ussers"><?php esc_html_e("Block Users","pie-register") ?></a></li>
            <li <?php if(isset($_GET['allowed_users'])){ echo 'class="invite-active"'; } ?>><a href="admin.php?page=pie-black-listed-users&allowed_users"><?php esc_html_e("Allow Users","pie-register") ?></a></li>
          </ul>
        </div>
  </div>
    <div id="container" class="pieregister-admin pieregister-admin-restrictions">
        <?php 
          if(!isset($_GET['allowed_users'])){
            $this->require_once_file(PIEREG_DIR_NAME.'/menus/restrictions/PieRegRestrictUsers.php');
          } else {
            $this->require_once_file(PIEREG_DIR_NAME.'/menus/restrictions/PieRegAllowedUsers.php');
          }

         ?>
        
    </div> 
</div>
</div>