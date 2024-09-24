<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 

if( file_exists( (PIEREG_DIR_NAME)."/classes/registration_form.php" ) ){
	require_once( (PIEREG_DIR_NAME)."/classes/registration_form.php" );
}

class Registration_form_template extends Registration_form
{
	var $is_pr_widget = false;	
	var $pageBreak_prev_label 	= '';
	var $pageBreak_prev_type 	= '';
	
	function addDesc()
	{
		if(!empty($this->field['desc']))
		{
			return '<p class="desc">'.html_entity_decode($this->field['desc']).'</p>';
		}
	}
	function addLabel($isblank="")
	{
		if(isset($this->field['label']) && empty($this->field['label']) ) 
		{
			return "";
		}
		
		if($this->field['type'] == "html" && $this->field['label'] == ""){
			return "";
		}
		if($this->field['type']=="name" && $this->field['name_format']=="normal")
		{
			return "";
		}
		$field_required = "";
		if( isset($this->field['required']) && $this->field['required'] != "" )
			$field_required .= '&nbsp;<span class="piereg_field_required_label">*</span>';
		
		$topclass = "";
		if($this->label_alignment=="top")
			$topclass = "label_top";
	
		$labelled = "";
		if(isset($this->field['label'])) {
			$labelled = __(html_entity_decode(stripslashes($this->field['label'])),"pie-register").$field_required;
		}
		
		if($isblank == 'empty'){
			$labelled = "&nbsp;";
		}
				
		return '<label class="'.esc_attr($topclass) .'" for="'.esc_attr($this->name).'">'.wp_kses_post($labelled).'</label>';
	}
	function addFormData($title="true",$description="true")
	{
		if( !isset($this->data['form']['css']) ) 		$this->data['form']['css'] 		= "";
		if( !isset($this->data['form']['label']) ) 		$this->data['form']['label'] 	= "";
		if( !isset($this->data['form']['desc']) ) 		$this->data['form']['desc'] 	= "";
		
		$data = "";
		$data .= '<div class="fieldset '.$this->data['form']['css'].'">';
		if($title == "true"){
			if( isset($this->data['form']['display_form_name']) && intval($this->data['form']['display_form_name']) )
			{
				$data .= '<h2 id="piereg_pie_form_heading">'.esc_html($this->data['form']['label']).'</h2>';	
			}
		}
		if($description == "true"){
			$data .= '<p id="piereg_pie_form_desc" >'.nl2br(html_entity_decode(stripslashes($this->data['form']['desc']))).'</p>';
		}
		$data .= '</div>';		
		$data  = apply_filters('piereg_edit_above_form_data',$data); // newlyAddedHookFilter
		
		return $data;
	
	}
	function addDefaultField()
	{
		
		$data = "";
		$this->name = $this->field['field_name'];
		
		if(isset($this->field['placeholder'])):
			$this->field['placeholder'];
		else:
			$this->field['placeholder'] = "";
		endif;	
		
		$data .= '<div class="fieldset">'.wp_kses_post($this->addLabel());
		
		if($this->field['field_name']=="description")
		{
			$data .= '<textarea name="description" id="description" rows="5" data-field_id="'.esc_attr($this->get_pr_widget_prefix()).'piereg_field_'.esc_attr($this->no).'" cols="80" >'.esc_textarea($this->getDefaultValue()).'</textarea>';	
		}
		else
		{
			$data .= '<input id="'.esc_attr($this->id).'" name="'.esc_attr($this->field['field_name']).'" data-field_id="'.esc_attr($this->get_pr_widget_prefix()).'piereg_field_'.esc_attr($this->no).'" class="'.esc_attr($this->addClass()).'"  placeholder="'.esc_attr($this->field['placeholder']).'" type="text" value="'.esc_attr($this->getDefaultValue()).'" />';	
		}
		$data .= wp_kses_post($this->addDesc());
		$data .= '</div>';
		return $data;
	}
	function addTextField(){
		
		$data  = '<div class="fieldset">';
		$data .= wp_kses_post($this->addLabel());
		$data .= '<input '.$this->read_only.' id="'.esc_attr($this->get_pr_widget_prefix()).esc_attr($this->id).'" name="'.esc_attr($this->name).'" data-field_id="'.esc_attr($this->get_pr_widget_prefix()).'piereg_field_'.esc_attr($this->no).'" class="'.esc_attr($this->addClass()).'" '.$this->addValidation().'  placeholder="'.esc_attr($this->field['placeholder']).'" type="text" value="'.esc_attr($this->getDefaultValue()).'" />';
		$data .= wp_kses_post($this->addDesc());
		$data .= '</div>';
		return $data;
	}
	function addHiddenField($form_label)
	{
		$default_value = $this->getDefaultValue();
		$tag_fields = array(
			'admin_email'     => esc_html__( 'Site Admin Email', 'pie-register' ),
			'site_name'       => esc_html__( 'Site Name', 'pie-register' ),
			'site_url'        => esc_html__( 'Site URL', 'pie-register' ),
			'page_title'      => esc_html__( 'Page Title', 'pie-register' ),
			'page_url'        => esc_html__( 'Page URL', 'pie-register' ),
			'page_id'         => esc_html__( 'Page ID', 'pie-register' ),
			'form_name'       => esc_html__( 'Form Name', 'pie-register' ),
			'user_ip_address' => esc_html__( 'User IP Address', 'pie-register' ),
			'user_id'         => esc_html__( 'User ID', 'pie-register' ),
			'user_name'       => esc_html__( 'User Name', 'pie-register' ),
			'user_email'      => esc_html__( 'User Email', 'pie-register' ),
			'referrer_url'    => esc_html__( 'Referrer URL', 'pie-register' )
		);

		foreach ( $tag_fields as $key => $key ) {
			switch ( $key ) {
				case 'admin_email':
					$admin_email = sanitize_email( get_option( 'admin_email' ) );
					$field_name  = str_replace( '{' . $key . '}', $admin_email, $default_value );
					break;

				case 'site_name':
					$site_name    = get_option( 'blogname' );
					$default_value   = str_replace( '{' . $key . '}', $site_name, $default_value );
					break;

				case 'site_url':
					$site_url    = get_option( 'siteurl' );
					$default_value  = str_replace( '{' . $key . '}', $site_url, $default_value );
					break;

				case 'page_title':
					$page_title   = get_the_ID() ? get_the_title( get_the_ID() ) : '';
					$default_value   = str_replace( '{' . $key . '}', $page_title, $default_value );
					break;

				case 'page_url':
					$page_url    = get_the_ID() ? get_permalink( get_the_ID() ) : '';
					$default_value  = str_replace( '{' . $key . '}', $page_url, $default_value );
					break;

				case 'page_id':
					$page_id    = get_the_ID() ? get_the_ID() : '';
					$default_value = str_replace( '{' . $key . '}', $page_id, $default_value );
					break;

				case 'form_name':
					$default_value = str_replace( '{' . $key . '}', $form_label, $default_value );
					break;

				case 'user_ip_address':
					$user_ip_add = sanitize_text_field(wp_unslash($_SERVER['REMOTE_ADDR']));
					$default_value  = str_replace( '{' . $key . '}', $user_ip_add, $default_value );
					break;
				case 'user_id':
					$tag_user_id = is_user_logged_in() ? get_current_user_id() : '';
					$default_value = str_replace( '{' . $key . '}', $tag_user_id, $default_value );
					break;

				case 'user_email':
					if ( is_user_logged_in() ) {
						$user  = wp_get_current_user();
						$email = sanitize_email( $user->user_email );
					} else {
						$email = '';
					}
					$default_value = str_replace( '{' . $key . '}', $email, $default_value );
					break;

				case 'user_name':
					if ( is_user_logged_in() ) {
						$user = wp_get_current_user();
						$name = sanitize_text_field( $user->user_login );
					} else {
						$name = '';
					}
					$default_value = str_replace( '{' . $key . '}', $name, $default_value );
					break;

				case 'referrer_url':
					$referer = ! empty( $_SERVER['HTTP_REFERER'] ) ? esc_url_raw($_SERVER['HTTP_REFERER']) : '';
					$default_value = str_replace( '{' . $key . '}', $referer, $default_value );
					break;

			}
		}
		$data  = '<div class="fieldset">';
		$data .= '<input id="'.esc_attr($this->id).'" name="'.esc_attr($this->name).'" data-field_id="'.esc_attr($this->get_pr_widget_prefix()).'piereg_field_'.esc_attr($this->no).'" type="hidden" value="'.esc_attr($default_value).'" />';
		$data .= wp_kses_post($this->addDesc());
		$data .= '</div>';
		return $data;
	}
	function addUsername($form_widget = false){
		if( !isset($this->field["validation_message"]) )	$this->field["validation_message"]		= "";
		
		$formwidget = (isset($form_widget) && $form_widget == true)? '_widget' : '';
		$data  = '<div class="fieldset">';
		$data .= wp_kses_post($this->addLabel());
		$data .= '<input id="username'.esc_attr($formwidget).'" name="username" data-field_id="'.esc_attr($this->get_pr_widget_prefix()).'piereg_field_'.esc_attr($this->no).'" class="input_fields '.esc_attr($this->field['css']).' piereg_validate[required,username] piereg_username_input_field" placeholder="'.esc_attr($this->field['placeholder']).'" type="text" value="'.esc_attr($this->getDefaultValue('username')).'" data-errormessage-value-missing="'.esc_attr($this->field['validation_message']).'"  />';
		$data .= wp_kses_post($this->addDesc());
		$data .= '</div>';	
		return $data;
	}
	function addPassword($fromwidget,$field_status = "")
	{
		if( !isset($this->field["validation_message"]) )	$this->field["validation_message"]		= "";
		
		$style = "";
		$data = "";
		if($fromwidget == true)
		{
			$this->id = $this->id."_widget";
		}
		$data .= '<div class="fieldset">'.wp_kses_post($this->addLabel());
		if($this->label_alignment=="left")
			$style = 'class = "wdth-lft mrgn-lft"';		
		
		$pass_strength = apply_filters( 'pie_password_strength_length', 'minSize[8]' );	

		if( isset($this->field['password_generator']) && $this->field['password_generator'] != "" ){
			$topclass = "";
			$hide_confirm_pass = "";
			if($this->label_alignment=="top")
			{
				$topclass = "label_top"; 
				$label_alignment = "top_label";
			} else {
				$label_alignment = "left_label";
			}
			if( isset( $this->field['hide_confirm_password'] ) )
			{
				$hide_confirm_pass = '_hide_conf_pass';
			}
			
			$data .= '<div class="password_field" style="display: none;">';
			$data .= '<input type="password" name="password" id="'.esc_attr($this->id).'" name="password" data-field_id="'.esc_attr($this->get_pr_widget_prefix()).'piereg_field_'.esc_attr($this->no).'" class="'.esc_attr($this->addClass("input_fields",array($pass_strength))).' prPass1" placeholder="'.esc_attr($this->field['placeholder']).'" type="password" data-errormessage-value-missing="'.esc_attr($this->field['validation_message']).'" data-errormessage-range-underflow="'.esc_attr($this->field['validation_message']).'" data-errormessage-range-overflow="'.esc_attr($this->field['validation_message']).'" autocomplete="off" >';
			$data .= '<span class="show-hide-password-innerbtn eye-slash pass-eye-reg_'.esc_attr($label_alignment.$hide_confirm_pass).'  "></span>';
			$data .= '</div>';

				$data .= '<div class="password_generator_div">';
				$data .= '<input ';
				$data .= 'name="password_generator" data-field_id="'.esc_attr($this->get_pr_widget_prefix()).'piereg_field_'.esc_attr($this->no).'" placeholder="'.esc_attr($this->field['placeholder']).'" type="button" value="'.esc_attr(__('Generate Password','pie-register')).'" class="generate_password gen_pass pass_generator_enabled">';
				$data .= '</div>';
			
			$data .= !isset( $this->field['hide_confirm_password'] ) ? '</div>' : '';

				$field_required = "";
				if( isset($this->field['required']) && $this->field['required'] != "" )
					$field_required .= '&nbsp;<span class="piereg_field_required_label">*</span>';

				$class = '';
				$fclass = '';
				
				$label2 = (isset($this->field['label2']) and !empty($this->field['label2']))? $this->field['label2'] : "";
				
				if( !isset( $this->field['hide_confirm_password'] ) )
				{
					$data .= '<div class="fieldset edit_confirm_pass" style="display:none;">';
					if(!empty($label2)) $data .= '<label>'.esc_html($label2).$field_required.'</label>';
					$data .= '<input id="confirm_password_'.esc_attr($this->id).'" type="password" data-errormessage-value-missing="'.esc_attr($this->field['validation_message']).'" data-errormessage-range-underflow="'.esc_attr($this->field['validation_message']).'" data-errormessage-range-overflow="'.esc_attr($this->field['validation_message']).'" class="input_fields '.esc_attr($this->field['css']).' piereg_validate['.esc_attr($pass_strength).',required,equals['.esc_attr($this->id).']]  prPass2" placeholder="'.esc_attr($this->field['placeholder2']).'" autocomplete="off" >';
					$data .= '<span class="pass_generator_enabled show-hide-password-innerbtn confirm-pass-eye-reg_'.esc_attr($label_alignment).' eye"></span>';
				}
				$data .= '</div>';
			
		}else{
			$topclass = "";
			$hide_confirm_pass = "";
			$no_Desc = "";
			if($this->label_alignment=="top")
			{
				$topclass = "label_top"; 
				$label_alignment = "top_label";
			} else {
				$label_alignment = "left_label";
			}
			if( isset( $this->field['hide_confirm_password'] ) )
			{
				$hide_confirm_pass = '_hide_conf_pass';
			}
			if( empty( $this->field['desc'] ) )
			{
				$no_Desc = '_no_desc';
			}
			$data .= '<input ';		
			$data .= 'id="'.esc_attr($this->id).'" name="password" data-field_id="'.esc_attr($this->get_pr_widget_prefix()).'piereg_field_'.esc_attr($this->no).'" class="'.esc_attr($this->addClass("input_fields",array($pass_strength))).' prPass1" placeholder="'.esc_attr($this->field['placeholder']).'" type="password" data-errormessage-value-missing="'.esc_attr($this->field['validation_message']).'" data-errormessage-range-underflow="'.esc_attr($this->field['validation_message']).'" data-errormessage-range-overflow="'.esc_attr($this->field['validation_message']).'" autocomplete="off" >';
			$data .= '<span class="show-hide-password-innerbtn pass-eye-reg pass-eye-reg_'.esc_attr($label_alignment.$hide_confirm_pass.$no_Desc).'  eye"></span>';
			$data .= !isset( $this->field['hide_confirm_password'] ) ? '</div>' : '';

			$field_required = "";
			if( isset($this->field['required']) && $this->field['required'] != "" )
				$field_required .= '&nbsp;<span class="piereg_field_required_label">*</span>';

			$class = '';
			$fclass = '';
			
			$label2 = (isset($this->field['label2']) and !empty($this->field['label2']))? $this->field['label2'] : "";
			
			if( !isset( $this->field['hide_confirm_password'] ) )
			{
				$data .= '<div class="fieldset">';
				if(!empty($label2)) $data .= '<label>'.esc_html($label2).$field_required.'</label>';
				$data .= '<input id="confirm_password_'.esc_attr($this->id).'" type="password" data-errormessage-value-missing="'.esc_attr($this->field['validation_message']).'" data-errormessage-range-underflow="'.esc_attr($this->field['validation_message']).'" data-errormessage-range-overflow="'.esc_attr($this->field['validation_message']).'" class="input_fields '.esc_attr($this->field['css']).' piereg_validate['.esc_attr($pass_strength).',required,equals['.esc_attr($this->id).']]  prPass2" placeholder="'.esc_attr($this->field['placeholder2']).'" autocomplete="off" />';
				$data .= '<span class="show-hide-password-innerbtn confirm-pass-eye-reg_'.esc_attr($label_alignment.$no_Desc).' eye"></span>';
			}
			$data .= wp_kses_post($this->addDesc());
			$data .= isset( $this->field['hide_confirm_password'] ) ? '</div>' : '';
		}
			
		return $data;
	}	
	function addEmail($fromwidget)
	{
		
		$data = $_readonly = "";
		
		if($fromwidget == true)
		{
			$this->id = $this->id."_widget";
		}
		$data .= '<div class="fieldset">'.wp_kses_post($this->addLabel());
		
		// invite_through_email		
		$_email_invite = $this->getDefaultValue("e_mail");

		$data .='<input '.$_readonly.' id="'.esc_attr($this->id).'" name="e_mail" data-field_id="'.esc_attr($this->get_pr_widget_prefix()).'piereg_field_'.esc_attr($this->no).'" class="'.esc_attr($this->addClass()).'" '.$this->addValidation().' placeholder="'.esc_attr($this->field['placeholder']).'" type="text" value="'.esc_attr($_email_invite).'" />';
		
		if(isset($this->field['confirm_email']))
		{
			$class = '';
			$fclass = '';
			
			$topclass = "";
			if($this->label_alignment=="top")
				$topclass = "label_top"; 	
			
			$field_required = "";
			if( isset($this->field['required']) && $this->field['required'] != "" )
				$field_required .= '&nbsp;<span class="piereg_field_required_label">*</span>';
			
			$data .= '</div>';
			$label2 = (isset($this->field['label2']) and !empty($this->field['label2']))? $this->field['label2'] : "";
			$data .= '<div class="fieldset">';
			if(!empty($label2)) $data .= '<label>'.esc_html($label2).$field_required.'</label>';
			$data .= '<input  placeholder="'.esc_attr($this->field['placeholder2']).'" id="confirm_email_'.esc_attr($this->id).'" '.$this->addValidation().' type="text" class="input_fields piereg_validate[required,equals['.esc_attr($this->id).']]" autocomplete="off">';
		}
		$data .= wp_kses_post($this->addDesc());
		$data .= '</div>';
		return $data;
	}
	function addUpload()
	{
		$data  = '<div class="fieldset">';
		$data .= wp_kses_post($this->addLabel());
		$data .= '<input '.$this->read_only.' id="'.esc_attr($this->id).'" name="'.esc_attr($this->name).'" data-field_id="'.esc_attr($this->get_pr_widget_prefix()).'piereg_field_'.esc_attr($this->no).'" class="'.esc_attr($this->addClass()).'"  '.$this->addValidation().' type="file"  />';
		
		if( isset( $this->field['file_types'] ) && !empty($this->field['file_types'])  )
		{
			$data .= '<p class="desc style_filetypes">Allowed File Types: '.esc_html($this->field['file_types']).'</p>';	
		}
		
		$data .= wp_kses_post($this->addDesc());
		$data .= '</div>';
		return $data;
	}
	function addProfilePicUpload()
	{
		$data  = '<div class="fieldset">';
		$data .= wp_kses_post($this->addLabel());
		$data .= '<input '.$this->read_only.' id="'.esc_attr($this->id).'" data-field_id="'.esc_attr($this->get_pr_widget_prefix()).'piereg_field_'.esc_attr($this->no).'" name="'.esc_attr($this->name).'" class="'.esc_attr($this->addClass()).' piereg_validate[funcCall[checkExtensions],ext[gif|GIF|jpeg|JPEG|jpg|JPG|png|PNG|bmp|BMP]]"  '.$this->addValidation().' type="file"  />';
		$data .= wp_kses_post($this->addDesc());
		$data .= '</div>';
		return $data;
	}
	function addTextArea()
	{
		$data  = '<div class="fieldset">';
		$data .= wp_kses_post($this->addLabel());
		$data .= '<textarea '.$this->read_only.' id="'.esc_attr($this->id).'" data-field_id="'.esc_attr($this->get_pr_widget_prefix()).'piereg_field_'.esc_attr($this->no).'" name="'.esc_attr($this->name).'" rows="'.esc_attr($this->field['rows']).'" cols="'.esc_attr($this->field['cols']).'" class="'.esc_attr($this->addClass()).'" '.$this->addValidation().' placeholder="'.esc_attr($this->field['placeholder']).'">';
		$data .= esc_textarea($this->getDefaultValue());
		$data .= '</textarea>';
		$data .= wp_kses_post($this->addDesc());
		$data .= '</div>';
		return $data;
	}
	function addName()
	{
		$field_required="";
		if( isset($this->field['required']) && $this->field['required'] != "" ){
			$field_required .= '&nbsp;<span class="piereg_field_required_label">*</span>';
		}
		
		$placeholder 	= (isset($this->field['placeholder']) and !empty($this->field['placeholder']))?$this->field['placeholder'] : "";
		$placeholder2 	= (isset($this->field['placeholder2']) and !empty($this->field['placeholder2']))?$this->field['placeholder2'] : "";
		
		$data  = '<div class="fieldset">';
		if(!empty($this->field['label'])) $data .= '<label>'.esc_html(__($this->field['label'],"pie-register")) . $field_required . '</label>';
		$data .= '<input '.$this->read_only.' value="'.esc_attr($this->getDefaultValue('first_name')).'" placeholder="'.esc_attr($placeholder).'" data-field_id="'.esc_attr($this->get_pr_widget_prefix()).'piereg_field_'.esc_attr($this->no).'" id="'.esc_attr($this->id).'_firstname" name="first_name" class="'.esc_attr($this->addClass()).' input_fields piereg_name_input_field" '.$this->addValidation().'  type="text"  />';				
		
		$topclass = "";
		if($this->label_alignment=="top")
			$topclass = "label_top";
		$data .= '</div>';
		$label2 = (isset($this->field['label2']) and !empty($this->field['label2']))? $this->field['label2'] : "";
		if( !isset( $this->field['hide_last_name'] ) )
		{
			$data .= '<div class="fieldset">';
			if(!empty($label2)) $data .= '<label>'.esc_html($label2) . $field_required .'</label>';
			$data .= '<input '.$this->read_only.' value="'.esc_attr($this->getDefaultValue('last_name')).'" placeholder="'.esc_attr($placeholder2).'" id="'.esc_attr($this->id).'_lastname" name="last_name" class="'.esc_attr($this->addClass()).' input_fields piereg_name_input_field" '.$this->addValidation().'  type="text"  />';	
			$data .= wp_kses_post($this->addDesc());
			$data .= '</div>';
		}
		return $data;
		
	}
	function addTime()
	{
		$data = "";
		$this->field['hours'] = TRUE;
		$name = $this->name;
		
		$time_this_values = $this->getDefaultValue($name);
		$data .= '<div class="fieldset">'.wp_kses_post($this->addLabel());
		$data .= '<div class="piereg_time">';
		$data .= '<div class="time_fields">';
		$data .= '<input '.$this->read_only.' value="'.( (isset($time_this_values["hh"])) ? esc_attr($time_this_values["hh"]) : "" ).'" maxlength="2" id="hh_'.esc_attr($this->id).'" name="'.esc_attr($this->name).'[hh]" type="text"  class="'.esc_attr($this->addClass()).'"  '.$this->addValidation().'>';
		$data .= '<label>'.esc_html(__("HH","pie-register")).'</label>';
		$data .= '</div>';
		$this->field['hours'] = FALSE;
		
		$this->field['mins'] = TRUE;
		$data .= '<span class="colon">:</span>';
		$data .= '<div class="time_fields">';
		$data .= '<input '.$this->read_only.' value="'.( (isset($time_this_values["mm"])) ? esc_attr($time_this_values["mm"]) : "" ).'" maxlength="2" id="mm_'.esc_attr($this->id).'" type="text" name="'.esc_attr($this->name).'[mm]"  class="'.esc_attr($this->addClass()).'"  '.$this->addValidation().'>';
		$data .= '<label>'.esc_html(__("MM","pie-register")).'</label>';
		$data .= '</div>';
		$data .= '<div id="time_format_field_'.esc_attr($this->id).'" class="time_fields"></div>';
		$this->field['mins'] = FALSE;
		
		if($this->field['time_type']=="12")
		{
			$time_format_val = ( (isset($time_this_values["time_format"])) ? $time_this_values["time_format"] : "" );
			$data .= '<div class="time_fields">';
			$data .= '<select '.$this->read_only.' name="'.esc_attr($this->name).'[time_format]" >';
				$data .= '<option value="am" '; 
						$data .=($time_format_val == "am")?'selected=""':'';
						$data .='>AM</option>';
				$data .='<option value="pm"  ';
						$data .=($time_format_val == "pm")?'selected=""':'';
						$data .='>PM</option>';
			$data .= '</select>';
			$data .= '</div>';
		}
		
		$data .= '</div>';
		$data .= wp_kses_post($this->addDesc());
		$data .= '</div>';

		if($this->piereg_field_visbility_addon_active){
			$this->readibility = apply_filters("pie_add_hidden_field_addon", $this->read_only);
		}

		if($this->readibility){
			$this->read_only = "";
			$data .= '<div class="control_visibility">';
			$data .=  $this->addTime();
			$data .=  '</div>';
		}
		
		return $data;
	}	
	function addDropdown()
	{ 
		
		$data = "";
		$multiple = "";
		$name = $this->name."[]";
		$field_id = $this->name;
		$thispostedvalue = $this->getDefaultValue();
		
		$data .= '<div class="fieldset" >'.wp_kses_post($this->addLabel());
		if($this->field['type']=="multiselect")
		{
			$multiple 	= 'multiple';			
			//$name = $this->name."[]";
		} elseif($this->field['type'] == 'custom_role'){
			$multiple 	= "";
			$name 		= "custom_role";
		}

		$data .= '<select '.$this->read_only.' '.esc_attr($multiple).' id="'.esc_attr($field_id).'" name="'.esc_attr($name).'" data-field_id="'.esc_attr($this->get_pr_widget_prefix()).'piereg_field_'.esc_attr($this->no).'" class="'.esc_attr($this->addClass()).'" '.$this->addValidation().'  >';
	
		if(isset($this->field['list_type']) && $this->field['list_type']=="country")
		{
			 $countries = get_option("pie_countries");			 
			$data .= $this->createDropdown($countries);			   	
		}
		else if(isset($this->field['list_type']) && $this->field['list_type']=="us_states")
		{
			 $us_states = get_option("pie_us_states");
			 $options 	= $this->createDropdown($us_states);				 
			 $data .= $options;						   	
		}
		else if(isset($this->field['list_type']) && $this->field['list_type'] == "can_states")
		{
			$can_states = get_option("pie_can_states");			
			$data .= $options 	= $this->createDropdown($can_states);					
		}
		else if(sizeof($this->field['value']) > 0)
			{	
				for($a = 0 ; $a < sizeof($this->field['value']) ; $a++)
				{
					$selected = '';
					if(isset($this->field['selected']) && is_array($this->field['selected']) && in_array($a,$this->field['selected']))
					{
						$selected = 'selected="selected"';	
					}
					if(is_array($thispostedvalue)){
						foreach($thispostedvalue as $thissinglepostedval){
							if(!empty($this->field['value'][$a]) && $thissinglepostedval == $this->field['value'][$a]){
								$selected = 'selected="selected"';
							}
						}
					}
					elseif(!empty($this->field['value'][$a]) && $thispostedvalue == $this->field['value'][$a]){
						$selected = 'selected="selected"';
					}

					//if($this->field['value'][$a] !="" || $this->field['display'][$a] != "")
						$data .= '<option '.$selected.' value="'.esc_attr($this->field['value'][$a]).'">'.esc_html($this->field['display'][$a]).'</option>';	
				}		
			}
		$data .= '</select>';
		
		$data .= wp_kses_post($this->addDesc());
		$data .= '</div>';
		
		if($this->piereg_field_visbility_addon_active){
			$this->readibility = apply_filters("pie_add_hidden_field_addon", $this->read_only);
		}
		
		if($this->readibility){
			$this->read_only = "";
			$data .= '<div class="control_visibility">';
			$data .=  $this->addDropdown();
			$data .=  '</div>';
		}

		return $data;
	}
	function addPricing() //stripe_changes
	{
		$data = "";
		
		if ($this->check_enable_payment_method() == "true" && isset($this->field['allow_payment_gateways']) && !empty($this->field['allow_payment_gateways']) )
		{
			$send_data	= "";
			if( count( $this->field['allow_payment_gateways'] ) == 1 ) {				
				$data .= '<div class="fieldset">';
				$data .= $this->addLabel('empty');
				$send_data = apply_filters('get_payment_gateway_content',$send_data,$this->field['allow_payment_gateways']);
				$data .= $send_data;
				$data .= wp_kses_post($this->addDesc());
				$data .= '<input type="hidden" name="select_payment_method" value="'.esc_attr($this->field['allow_payment_gateways'][0]).'" /></div>';
				
			}  else  {
				
				$data .= '<div class="pieregister_payment_option fieldset">';
				do_action("add_select_payment_script"); // Add script
				$data .= "<label>".esc_html(__("Select Payment","pie-register"))." <span class='piereg_field_required_label'>*</span></label>";
				$data .= '<select id="select_payment_method" name="select_payment_method">';
				$data .= '<option value="">'.esc_html(__("Select Payment Method","pie-register")).'</option>';
				$send_data = apply_filters('Add_payment_option',$send_data,$this->field['allow_payment_gateways']);
				$data .= $send_data."</select>";
				$data .= apply_filters("get_payment_content_area",0);
				$data .= '</div>';				
			
			}
			
			if( isset( $this->field['payment_charge'] ) && !empty($this->field['payment_charge']) )
			{
				$data	.= '<input type="hidden" name="payment_charge" value="'.esc_attr(((float)$this->field['payment_charge'])).'" />';	
			}		
			if( isset( $this->field['stripe_api_description'] ) && !empty($this->field['stripe_api_description']) )
			{
				$data	.= '<input type="hidden" name="stripe_api_description" value="'.esc_attr($this->field['stripe_api_description']).'" />';	
			}		
			
			return $data;
			
		} else {
			$data = '<div class="fieldset">';
			$data .= wp_kses_post($this->addLabel());
			$data .= '<p>'.esc_html(__("No payment methods selected by administrator.","pie-register")).'</p>';
			$data .= wp_kses_post($this->addDesc());
			$data .= '</div>';
			return $data;
		}
	}
	
	function addNumberField()
	{
		$data = "";

		$data .= '<div class="fieldset">';
		$data .= wp_kses_post($this->addLabel());
		$data .= '<input '.$this->read_only.' id="'.esc_attr($this->id).'" name="'.esc_attr($this->name).'" data-field_id="'.esc_attr($this->get_pr_widget_prefix()).'piereg_field_'.esc_attr($this->no).'" class="'.esc_attr($this->addClass()).'" '.$this->addValidation().'  placeholder="'.esc_attr($this->field['placeholder']).'" type="number" value="'.esc_attr($this->getDefaultValue()).'"' ;
		
		if( $this->field['min'] !== "" )
			$data .= 'min="'.esc_attr($this->field['min']).'"';
		
		if(!empty($this->field['max']))
			$data .= 'max="'.esc_attr($this->field['max']).'"';
		
		$data .= '/>';
		$data .= wp_kses_post($this->addDesc());
		$data .= '</div>';	
		return $data;
	}
	function addPhone()
	{
		$data  = '<div class="fieldset">';
		$data .= wp_kses_post($this->addLabel());
		$data .= '<input '.$this->read_only.' id="'.esc_attr($this->id).'" class="'.esc_attr($this->addClass()).'" data-field_id="'.esc_attr($this->get_pr_widget_prefix()).'piereg_field_'.esc_attr($this->no).'"  '.$this->addValidation().' placeholder="'.((isset($this->field['placeholder']))?esc_attr($this->field['placeholder']):"").'" name="'.esc_attr($this->name).'" type="text" value="'.esc_attr($this->getDefaultValue()).'" />';
		$data .= wp_kses_post($this->addDesc());
		$data .= '</div>';
		return $data;
	}
	function addList()
	{
		$data = "";
		$width  = 90 /  intval($this->field['cols']);
		$name = $this->name;
		
		$list_this_values = array(); //$this->getDefaultValue($name);
		
		$data .= '<div class="fieldset">'.wp_kses_post($this->addLabel());
		$data .= '<div class="'.esc_attr($this->field['css']).' pie_list_cover">';
		
		for($a = 1 ,$c=0; $a <= $this->field['rows'] ; $a++,$c++)
		{
			if($a==1)
			{
				$data .= '<div class="'.esc_attr($this->id.'_'.$a).' pie_list">';
				
				
				for($b = 1 ; $b <= $this->field['cols'] ;$b++)
				{
					$data .= '<input '.$this->read_only.' data-type="list" value="'.((isset($list_this_values[$c][$b-1]))?esc_attr($list_this_values[$c][$b-1]):"").'" style="width:'.esc_attr($width).'%;" type="text" '.$this->addValidation().' name="'.esc_attr($this->name).'['.esc_attr($c).'][]" class="'.esc_attr($this->addClass()).' input_fields"> ';
				}
				if( ((int)$this->field['rows']) > 1)
				{
					$data .= ' <img src="'.esc_url(PIEREG_PLUGIN_URL."assets/images/plus.png").'" onclick="addList(this,'.esc_js($this->field['rows']).','.esc_js($this->field['id']).');" alt="add" /></div>';		
				}
				
				if( $this->field['rows'] == 1 )	$data .= '</div>';		
			}
			else
			{
				if(isset($list_this_values[$c]) != false)
					$display_list_style = (!array_filter($list_this_values[$c]))? "display:none;" : "display:block;";
				else
					$display_list_style = "display:none;";
					
				$data .= '<div style="'.esc_attr($display_list_style).'" class="'.esc_attr($this->id.'_'.$a).' pie_list">';
				for($b = 1 ; $b <= $this->field['cols'] ;$b++)
				{
					$data .= '<input '.$this->read_only.' data-type="list" value="'.((isset($list_this_values[$c][$b-1]))?esc_attr($list_this_values[$c][$b-1]):"").'" style="width:'.esc_attr($width).'%;" type="text" '.$this->addValidation().' name="'.esc_attr($this->name).'['.esc_attr($c).'][]" class="'.esc_attr($this->addClass()).' input_fields">';
				}
					$data .= ' <img src="'.esc_url(PIEREG_PLUGIN_URL."assets/images/minus.gif").'" onclick="removeList(this,'.esc_js($this->field['rows']).','.esc_js($this->field['id']).','.esc_js($a).');" alt="add" />';
					$data .= '</div>';
			}
			
			
		}
		
		$data .= '</div>';		
		$data .= wp_kses_post($this->addDesc());
		$data .= '</div>';
		return $data;
	}
	function addHTML()
	{
		$data  = '<div class="fieldset">';
		$data .= wp_kses_post($this->addLabel());
		$data .= '<div class="piereg-html-field-content" >';
		$data .= html_entity_decode($this->field['html']);
		$data .= '</div>';
		$data .= wp_kses_post($this->addDesc());
		$data .= '</div>';
		return $data;
	}
	function addSectionBreak()
	{
		$class = "";
		
		if($this->label_alignment == "left")
			$class .= "wdth-lft ";
		
		$class .= "sectionBreak";
		
		$data  = '<div class="fieldset aligncenter">';
		if($this->field['label'] != ''){
			$class .= ' break-label';
		}
			// $data .= wp_kses_post($this->addLabel());
		$data .= '<div class="'.esc_attr($class).'">';
			$data .= wp_kses_post($this->addLabel());
		$data .= '</div>';
		$data .= wp_kses_post($this->addDesc());
		$data .= '</div>';
		return $data;
	}
	function addCheckRadio()
	{
		$data = "";
		$data .= '<div class="fieldset">';
		$data .= wp_kses_post($this->addLabel());
		if(sizeof($this->field['value']) > 0)
		{
			$data .= '<div class="radio_wrap">';
			$thispostedvalue = $this->getDefaultValue();
			for($a = 0 ; $a < sizeof($this->field['value']) ; $a++)
			{
				$checked = '';
				if( (isset($this->field['selected'])) && (is_array($this->field['selected']) && in_array($a,$this->field['selected'])) )
					$checked = 'checked="checked"';	
				else
					$checked = '';
				
				if(is_array($thispostedvalue)){
					foreach($thispostedvalue as $thissinglepostedval){
						if($thissinglepostedval == $this->field['value'][$a])
							$checked = 'checked="checked"';
					}
				}
				
				$dymanic_class = $this->field['type']."_".$this->field['id'];

				$data .= '<div class="radio_container">';
				$data .= '<input '.wp_kses_post($this->read_only).' '.$checked.' value="'.esc_attr($this->field['value'][$a]).'" data-field_id="'.esc_attr($this->get_pr_widget_prefix()).'piereg_field_'.esc_attr($this->no).'" type="'.esc_attr($this->field['type']).'" name="'.esc_attr($this->name).'[]" class="'.esc_attr($this->addClass("input_fields")).' radio_fields" '.$this->addValidation().' data-map-field-by-class="'.esc_attr($dymanic_class).'" >';
				$data .= "<label>";
					$data .= $this->field['display'][$a];
				$data .= "</label>";
				$data .= '</div>';
			}
			$data .= "</div>";		
		}
		$data .= wp_kses_post($this->addDesc());
		$data .= '</div>';
		return $data;
	}
	function addAddress()
	{
		$address_values = $this->getDefaultValue($this->name);
		$data = "";
		$data .= '<div class="fieldset">'.wp_kses_post($this->addLabel());
		$data .= '<div class="address_main">';
		$data .= '<div class="address">';
		$data .= '<input '.$this->read_only.' type="text" name="'.esc_attr($this->name).'[address]" id="'.esc_attr($this->id).'" class="'.esc_attr($this->addClass()).'"  '.$this->addValidation().' value="'.((isset($address_values['address']))?esc_attr($address_values['address']):"").'">';
		$data .= '<label>'.esc_html(__("Street Address","pie-register")).'</label>';
		$data .= '</div>';
		
		 if(empty($this->field['hide_address2']))
		 {
			$data .= '<div class="address">';
			$data .= '<input '.$this->read_only.' type="text" name="'.esc_attr($this->name).'[address2]" id="address2_'.esc_attr($this->id).'"  class="'.esc_attr($this->addClass()).'"  '.$this->addValidation().' value="'.((isset($address_values['address2']))?esc_attr($address_values['address2']):"").'">';
			$data .= '<label>'.esc_html(__("Address Line 2","pie-register")).'</label>';
			$data .= '</div>';
		 }
		
		$data .= '<div class="address">';
		$data .= '<div class="address2">';
		$data .= '<input '.$this->read_only.' type="text" name="'.esc_attr($this->name).'[city]" id="city_'.esc_attr($this->id).'" class="'.esc_attr($this->addClass()).'"  '.$this->addValidation().' value="'.((isset($address_values['city']))?esc_attr($address_values['city']):"").'">';
		$data .= '<label>'.esc_html(__("City","pie-register")).'</label>';
		$data .= '</div>';
		
		 if(empty($this->field['hide_state']))
		 {
			 	if($this->field['address_type'] == "International")
				{
					$data .= '<div class="address2" >';
					$data .= '<input '.$this->read_only.' type="text" name="'.esc_attr($this->name).'[state]" id="state_'.esc_attr($this->id).'" class="'.esc_attr($this->addClass()).'" value="'.((isset($address_values['state']))?esc_attr($address_values['state']):"").'">';
					$data .= '<label>'.esc_html(__("State / Province / Region","pie-register")).'</label>';
					$data .= '</div>';
				}
				else if($this->field['address_type'] == "United States")
				{
				  $us_states = get_option("pie_us_states");
				  $selectedoption = (isset($address_values['state']))?$address_values['state']:$this->field['us_default_state'];
				  $options 	= $this->createDropdown($us_states,$selectedoption);	
				 
				  $data .= '<div class="address2" >';
					$data .= '<select '.$this->read_only.' id="state_'.esc_attr($this->id).'" name="'.esc_attr($this->name).'[state]" class="'.esc_attr($this->addClass("")).'">';
					$data .= $options;
					$data .= '</select>';
					$data .= '<label>'.esc_html(__("State","pie-register")).'</label>';
				  $data .= '</div>';
				}
				else if($this->field['address_type'] == "Canada")
				{
					
					$can_states = get_option("pie_can_states");
					$selectedoption = (isset($address_values['state']))?$address_values['state']:$this->field['canada_default_state'];
				  	$options 	= $this->createDropdown($can_states,$selectedoption);
					$data .= '<div class="address2" >';
						$data .= '<select '.$this->read_only.' id="state_'.esc_attr($this->id).'" class="'.esc_attr($this->addClass("")).'" name="'.esc_attr($this->name).'[state]">';
						$data .= $options;
						$data .= '</select>';
						$data .= '<label>'.esc_html(__("Province","pie-register")).'</label>';
					$data .= '</div>';
				}
		}
		$data .= '</div>';
		$data .= '<div class="address">';	
		$data .= ' <div class="address2">';
		$data .= '<input '.$this->read_only.' id="zip_'.esc_attr($this->id).'" name="'.esc_attr($this->name).'[zip]" type="text" class="'.esc_attr($this->addClass()).'"  '.$this->addValidation().' value="'.((isset($address_values['zip']))?esc_attr($address_values['zip']):"").'">';
		$data .= '<label>'.esc_html(__("Zip / Postal Code","pie-register")).'</label>';
		$data .= '</div>';	 
		
		
		 if($this->field['address_type'] == "International")
		 {
			 $countries = get_option("pie_countries");
			 $selectedoption = (isset($address_values['country']) && $address_values['country'])?$address_values['country']:$this->field['default_country'];		 
			 $options 	= $this->createDropdown($countries,$selectedoption);  
			 $data .= '<div  class="address2" >';
				$data .= '<select '.$this->read_only.' id="country_'.esc_attr($this->id).'" name="'.esc_attr($this->name).'[country]" class="'.esc_attr($this->addClass()).'" '.$this->addValidation().'>';
						$data .= $options;
					$data .= '</select>';
				$data .= '<label>'.esc_html(__("Country","pie-register")).'</label>';
		  	$data .= '</div>';
		 }
		 
		 
		$data .= '</div>';
		$data .= '</div>';
		$data .= wp_kses_post($this->addDesc());
		$data .= '</div>';

		if($this->piereg_field_visbility_addon_active){
			$this->readibility = apply_filters("pie_add_hidden_field_addon", $this->read_only);
		}

		if($this->readibility){
			$this->read_only = "";
			$data .= '<div class="control_visibility">';
			$data .=  $this->addAddress();
			$data .=  '</div>';
		}
		return $data;
	}	
	//pie-register-woocommerce adoon 
	function addWooCommerceBillingAddress()
	{
		if ($this->woocommerce_and_piereg_wc_addon_active)
		{
			$wc_billing_address_values = $this->getDefaultValue($this->name);
			
			// country_options
			global $woocommerce;
			$countries_obj   	= new WC_Countries();
			$countries_list  	= $countries_obj->get_allowed_countries();
			$countries			= array();
			foreach($countries_list as $iso_code => $country_name)
			{
				$countries[] = array('iso_code' => $iso_code, 'name' => $country_name);
			}
			$default_country 	= $countries_obj->get_base_country();
			$selectedoption 	= (isset($wc_billing_address_values['country']) && $wc_billing_address_values['country']) ? $wc_billing_address_values['country'] : (isset($default_country) ? $default_country : "" );		 
			$country_options 	= $this->createCountryDropdown($countries,$selectedoption);
            // states_options
            $states_list 		= $countries_obj->get_states( $default_country );
            $states				= array();
            if ($states_list) 
            {
	            foreach($states_list as $iso_code => $state_name)
	            {
	        	$states[] = array('iso_code' => $iso_code, 'name' => $state_name);
				}
            }
			$selectedoption 	= (isset($wc_billing_address_values['state']))?$wc_billing_address_values['state']:"";
			$state_options 		= $this->createStatesDropdown($states,$selectedoption);

			$arguments = array(
				'field' 						=> $this->field, 
				'id' 							=> $this->id, 
				'user'							=> (isset($this->user) ? $this->user : false), 
				'slug'							=> (isset($this->slug) ? $this->slug : false), 
				'name'							=> $this->name, 
				'addLabel'						=> $this->addLabel(), 
				'defaultLabel'					=> '<label class="" for="wc_billing_address_'.esc_attr($this->id).'">Billing Address</label>',
				'addClass'						=> $this->addClass(), 
				'addValidation'					=> $this->addValidation(), 
				'addDesc'						=> $this->addDesc(), 
				'wc_billing_address_values' 	=> $this->getDefaultValue($this->name), 
				'country_options'				=> $country_options, 
				'state_options'					=> $state_options, 
				'hidden_fields'					=> explode(",", $this->field['hidden_fields']),
				'required_fields'				=> explode(",", $this->field['required_fields'])	
			);

			return apply_filters("pieregister_print_woocommerce_billing_address_front_template", $arguments); 
		}
	}

	function addWooCommerceShippingAddress()
	{
		if ($this->woocommerce_and_piereg_wc_addon_active)
		{
			$wc_shipping_address_values = $this->getDefaultValue($this->name);
				
			// country_options
			global $woocommerce;
			$countries_obj   	= new WC_Countries();
			$countries_list  	= $countries_obj->get_allowed_countries();
			$countries			= array();
			foreach($countries_list as $iso_code => $country_name)
			{
				$countries[] = array('iso_code' => $iso_code, 'name' => $country_name);
			}
			$default_country 	= $countries_obj->get_base_country();
			$selectedoption 	= ( isset($wc_shipping_address_values['country']) && $wc_shipping_address_values['country'] ) ? $wc_shipping_address_values['country'] : (isset($default_country) ? $default_country : "" ); 
			$country_options 	= $this->createCountryDropdown($countries,$selectedoption);  

			// states_options
			$states_list 		= $countries_obj->get_states( $default_country );
			$states				= array();
			if ($states_list) 
			{
				foreach($states_list as $iso_code => $state_name)
				{
					$states[] = array('iso_code' => $iso_code, 'name' => $state_name);
				}
			}
			$selectedoption 	= (isset($wc_shipping_address_values['state']))?$wc_shipping_address_values['state']:"";
			$state_options 		= $this->createStatesDropdown($states,$selectedoption);
			
			$arguments = array(
				'field' 						=> $this->field, 
				'id' 							=> $this->id, 
				'user'							=> (isset($this->user) ? $this->user : false), 
				'slug'							=> (isset($this->slug) ? $this->slug : false), 
				'name'							=> $this->name, 
				'addLabel'						=> $this->addLabel(), 
				'defaultLabel'					=> '<label class="" for="wc_shipping_address_'.esc_attr($this->id).'">Shipping Address</label>',
				'addClass'						=> $this->addClass(), 
				'addValidation'					=> $this->addValidation(), 
				'addDesc'						=> $this->addDesc(), 
				'wc_shipping_address_values' 	=> $this->getDefaultValue($this->name), 
				'country_options'				=> $country_options, 
				'state_options'					=> $state_options,
				'hidden_fields'					=> explode(",", $this->field['hidden_fields']),
				'required_fields'				=> explode(",", $this->field['required_fields'])	
			);

			return apply_filters("pieregister_print_woocommerce_shipping_address_front_template", $arguments);
		}
	}	
	function addDate()
	{
		$data = "";
		$data .= '<div class="fieldset">';
		$data .= wp_kses_post($this->addLabel());
		$date_this_values = $this->getDefaultValue($this->name);
		
		if($this->field['date_type'] == "datefield")
		{
			
			if($this->field['date_format']=="mm/dd/yy")
			{
				$data .= '<div class="piereg_time date_format_field">';
                    $data .= '<div class="time_fields">';
                        $data .= '<input '.$this->read_only.' id="mm_'.esc_attr($this->id).'" name="'.esc_attr($this->name).'[date][mm]" maxlength="2" type="text" class="'.esc_attr($this->addClass("input_fields",array("custom[month]"))).'" '.$this->addValidation().' value="'.((isset($date_this_values['date']['mm']))?esc_attr($date_this_values['date']['mm']):"").'" data-type="date">';
                        $data .= '<label>'.esc_html(__("MM","pie-register")).'</label>';
                    $data .= '</div>';
                    $data .= '<div class="time_fields">';
                        $data .= '<input '.$this->read_only.' id="dd_'.esc_attr($this->id).'" name="'.esc_attr($this->name).'[date][dd]" maxlength="2"  type="text" class="'.esc_attr($this->addClass("input_fields",array("custom[day]"))).'" '.$this->addValidation().' value="'.((isset($date_this_values['date']['dd']))?esc_attr($date_this_values['date']['dd']): "").'" data-type="date">';
                        $data .= '<label>'.esc_html(__("DD","pie-register")).'</label>';
                    $data .= '</div>';
                    $data .= '<div class="time_fields">';
                        $data .= '<input '.$this->read_only.' id="yy_'.esc_attr($this->id).'" name="'.esc_attr($this->name).'[date][yy]" maxlength="4"  type="text" class="'.esc_attr($this->addClass("input_fields",array("custom[year]"))).'" '.$this->addValidation().' value="'.((isset($date_this_values['date']['yy']))?esc_attr($date_this_values['date']['yy']):"").'" data-type="date">';
                        $data .= '<label>'.esc_html(__("YYYY","pie-register")).'</label>';
                    $data .= '</div>';
				$data .= '</div>';
			} 
			else if($this->field['date_format']=="yy/mm/dd" || $this->field['date_format']=="yy.mm.dd")
			{
				$data .= '<div class="piereg_time date_format_field">';
                    $data .= '<div class="time_fields">';
                        $data .= '<input '.$this->read_only.' id="yy_'.esc_attr($this->id).'" name="'.esc_attr($this->name).'[date][yy]" maxlength="4"  type="text" class="'.esc_attr($this->addClass("input_fields",array("custom[year]"))).'" value="'.((isset($date_this_values['date']['yy']))?esc_attr($date_this_values['date']['yy']):"").'" data-type="date">';
                        $data .= '<label>'.esc_html(__("YYYY","pie-register")).'</label>';
                    $data .= '</div>';
                    $data .= '<div class="time_fields">';
                        $data .= '<input '.$this->read_only.' id="mm_'.esc_attr($this->id).'" name="'.esc_attr($this->name).'[date][mm]" maxlength="2" type="text" class="'.esc_attr($this->addClass("input_fields",array("custom[month]"))).'" '.$this->addValidation().' value="'.((isset($date_this_values['date']['mm']))?esc_attr($date_this_values['date']['mm']):"").'" data-type="date">';
                        $data .= '<label>'.esc_html(__("MM","pie-register")).'</label>';
                    $data .= '</div>';
                    $data .= '<div class="time_fields">';
                        $data .= '<input '.$this->read_only.' id="dd_'.esc_attr($this->id).'" name="'.esc_attr($this->name).'[date][dd]" maxlength="2"  type="text" class="'.esc_attr($this->addClass("input_fields",array("custom[day]"))).'" value="'.((isset($date_this_values['date']['dd']))?esc_attr($date_this_values['date']['dd']):"").'" data-type="date">';
                        $data .= '<label>'.esc_html(__("DD","pie-register")).'</label>';
                    $data .= '</div>';
				$data .= '</div>';
			}
			else if($this->field['date_format']=="dd/mm/yy" || $this->field['date_format']=="dd-mm-yy" || $this->field['date_format']=="dd.mm.yy")
			{
                $data .= '<div class="piereg_time date_format_field">';
                    $data .= '<div class="time_fields">';
                        $data .= '<input '.$this->read_only.' id="dd_'.esc_attr($this->id).'" name="'.esc_attr($this->name).'[date][dd]" maxlength="2"  type="text" class="'.esc_attr($this->addClass("input_fields",array("custom[day]"))).'" value="'.((isset($date_this_values['date']['dd']))?esc_attr($date_this_values['date']['dd']):"").'" data-type="date">';
                        $data .= '<label>'.esc_html(__("DD","pie-register")).'</label>';
                    $data .= '</div>';
                    $data .= '<div class="time_fields">';
                        $data .= '<input '.$this->read_only.' id="mm_'.esc_attr($this->id).'" name="'.esc_attr($this->name).'[date][mm]" maxlength="2" type="text" class="'.esc_attr($this->addClass("input_fields",array("custom[month]"))).'" '.$this->addValidation().' value="'.((isset($date_this_values['date']['mm']))?esc_attr($date_this_values['date']['mm']):"").'" data-type="date">';
                        $data .= '<label>'.esc_html(__("MM","pie-register")).'</label>';
                    $data .= '</div>';
                    $data .= '<div class="time_fields">';
                        $data .= '<input '.$this->read_only.' id="yy_'.esc_attr($this->id).'" name="'.esc_attr($this->name).'[date][yy]" maxlength="4"  type="text" class="'.esc_attr($this->addClass("input_fields",array("custom[year]"))).'" value="'.((isset($date_this_values['date']['yy']))?esc_attr($date_this_values['date']['yy']):"").'" data-type="date">';
                        $data .= '<label>'.esc_html(__("YYYY","pie-register")).'</label>';
                    $data .= '</div>';
                $data .= '</div>';
			}
			else
			{
                $data .= '<div class="piereg_time date_format_field">';
                    $data .= '<div class="time_fields">';
                        $data .= '<input '.$this->read_only.' id="dd_'.esc_attr($this->id).'" name="'.esc_attr($this->name).'[date][dd]" maxlength="2"  type="text" class="'.esc_attr($this->addClass("input_fields",array("custom[day]"))).'" value="'.((isset($date_this_values['date']['dd']))?esc_attr($date_this_values['date']['dd']):"").'" data-type="date">';
                        $data .= '<label>'.esc_html(__("DD","pie-register")).'</label>';
                    $data .= '</div>';
                    $data .= '<div class="time_fields">';
                        $data .= '<input '.$this->read_only.' id="yy_'.esc_attr($this->id).'" name="'.esc_attr($this->name).'[date][yy]" maxlength="4"  type="text" class="'.esc_attr($this->addClass("input_fields",array("custom[year]"))).'" value="'.((isset($date_this_values['date']['yy']))?esc_attr($date_this_values['date']['yy']):"").'" data-type="date">';
                        $data .= '<label>'.esc_html(__("YYYY","pie-register")).'</label>';
                    $data .= '</div>';
                    $data .= '<div class="time_fields">';
                        $data .= '<input '.$this->read_only.' id="mm_'.esc_attr($this->id).'" name="'.esc_attr($this->name).'[date][mm]" maxlength="2" type="text" class="'.esc_attr($this->addClass("input_fields",array("custom[month]"))).'" '.$this->addValidation().'" value="'.((isset($date_this_values['date']['mm']))?esc_attr($date_this_values['date']['mm']):"").'" data-type="date">';
                        $data .= '<label>'.esc_html(__("MM","pie-register")).'</label>';
                    $data .= '</div>';
                $data .= '</div>';
			}
		}
		else if($this->field['date_type'] == "datepicker")
		{
				if( $this->field['calendar_icon'] == "calendar" || $this->field['calendar_icon'] == "custom" ) 
				  $data .= '<div class="piereg_time date_format_field date_with_icon">';
				else 
				  $data .= '<div class="piereg_time date_format_field">';
				
				  $data .= '<input readonly id="'.esc_attr($this->id).'" name="'.esc_attr($this->name).'[date][]" type="text" class="'.esc_attr($this->addClass()).' date_start" title="'.esc_attr($this->field['date_format']).'" value="';
				  
				$data .= ( (isset($date_this_values['date'][0]) && !empty($date_this_values['date'][0])) ?esc_attr($date_this_values['date'][0]) : "" );
				$data .= '" data-type="date" data-field-visibility-addon="'.( (isset($this->field['enable_read_only'])) && ( ($this->field['enable_read_only'] == "registration") || ($this->field['enable_read_only'] == "profnreg") ) ?"1":"0").'"/>';
				$data .= '<input id="'.esc_attr($this->id).'_format" type="hidden"  value="'.((isset($this->field['date_format'])) ? esc_attr($this->field['date_format']) : "").'">';
				$data .= '<input id="'.esc_attr($this->id).'_firstday" type="hidden"  value="'.((isset($this->field['firstday'])) ? esc_attr($this->field['firstday']) : "").'">';
				$data .= '<input id="'.esc_attr($this->id).'_startdate" type="hidden"  value="'.((isset($this->field['startdate'])) ? esc_attr($this->field['startdate']) : "").'">';
				  
				if($this->field['calendar_icon'] == "calendar")
				{
					 $data .=  '<img id="'.esc_attr($this->id).'_icon" class="calendar_icon" src="'.esc_url(PIEREG_PLUGIN_URL."assets/images/calendar.png").'" />';
				}
				else if($this->field['calendar_icon'] == "custom")
				{
					 $data .=  '<img id="'.esc_attr($this->id).'_icon" class="calendar_icon" src="'.esc_url($this->field['calendar_icon_url']).'" />'; 
				}
				  
				 $data .= '</div>';	
		}
		else if($this->field['date_type'] == "datedropdown")
		{
				
			if($this->field['date_format']=="mm/dd/yy")
			{
			
					$data .= '<div class="piereg_time date_format_field">';
					  $data .= '<div class="time_fields">';
						$data .= '<select '.$this->read_only.' id="mm_'.esc_attr($this->id).'" name="'.esc_attr($this->name).'[date][mm]" class="'.esc_attr($this->addClass("input_fields",array("custom[month]"))).'" '.$this->addValidation().' data-type="date">';
						  $data .= '<option value="">'.esc_html(__("Month","pie-register")).'</option>';
						  for($a=1;$a<=12;$a++){
							  if(isset($date_this_values['date']['mm']) && $date_this_values['date']['mm'] == $a)
								$sel = ' selected=""';
							  else
							  $sel = '';	
							  $data .= '<option value="'.str_pad($a, 2, "0", STR_PAD_LEFT).'" '.$sel.'>'.str_pad(__($a,"pie-register"), 2, "0", STR_PAD_LEFT).'</option>';
						  }
						  $data .= '</select>';
					  $data .= '</div>';
				  $data .= '<div class="time_fields">';
					$data .= '<select '.$this->read_only.' id="dd_'.esc_attr($this->id).'" name="'.esc_attr($this->name).'[date][dd]" class="'.esc_attr($this->addClass("input_fields",array("custom[day]"))).'" '.$this->addValidation().' data-type="date">';
					  $data .= '<option value="">'.esc_html(__("Day","pie-register")).'</option>';
					  for($a=1;$a<=31;$a++){
						  if(isset($date_this_values['date']['dd']) && $date_this_values['date']['dd'] == $a)
						  	$sel = ' selected=""';
						  else
						  $sel = '';	
						  $data .= '<option value="'.str_pad($a, 2, "0", STR_PAD_LEFT).'" '.$sel.'>'.str_pad(__($a,"pie-register"), 2, "0", STR_PAD_LEFT).'</option>';
					  }
					  $data .= '</select>';
				  $data .= '</div>';
				  $data .= '<div class="time_fields">';
					$data .= '<select '.$this->read_only.' id="yy_'.esc_attr($this->id).'" name="'.esc_attr($this->name).'[date][yy]" class="'.esc_attr($this->addClass("input_fields",array("custom[year]"))).'" '.$this->addValidation().' data-type="date">';
					  $data .= '<option value="">'.esc_html(__("Year","pie-register")).'</option>';
					  for($a=((int)date("Y") + 10);$a>=(((int)date("Y"))-100);$a--){
						  if(isset($date_this_values['date']['yy']) && $date_this_values['date']['yy'] == $a)
						  	$sel = ' selected=""';
						  else
						  	$sel = '';	
						  $data .= '<option value="'.esc_attr($a).'" '.esc_attr($sel).'>'.esc_html(__($a,"pie-register")).'</option>';
					  }
					  $data .= '</select>';
				  $data .= '</div>';
				$data .= '</div>';
			}
			else if($this->field['date_format']=="yy/mm/dd" || $this->field['date_format']=="yy.mm.dd")
			{
					$data .= '<div class="piereg_time date_format_field">';
					 $data .= '<div class="time_fields">';
					$data .= '<select '.$this->read_only.' id="yy_'.esc_attr($this->id).'" name="'.esc_attr($this->name).'[date][yy]" class="'.esc_attr($this->addClass("input_fields",array("custom[year]"))).'" data-type="date">';
					  $data .= '<option value="">'.esc_html(__("Year","pie-register")).'</option>';
					  for($a=((int)date("Y") + 10);$a>=(((int)date("Y"))-100);$a--){
						  if(isset($date_this_values['date']['yy']) && $date_this_values['date']['yy'] == $a)
						  	$sel = ' selected=""';
						  else
						  $sel = '';	
						  $data .=  '<option value="'.esc_attr($a).'" '.esc_attr($sel).'>'.esc_html(__($a,"pie-register")).'</option>';
					  }
					  $data .=  '</select>';
				  $data .= '</div>';
				  $data .= '<div class="time_fields">';
					$data .= '<select '.$this->read_only.' id="mm_'.esc_attr($this->id).'" name="'.esc_attr($this->name).'[date][mm]" class="'.esc_attr($this->addClass("input_fields",array("custom[month]"))).'" '.$this->addValidation().' data-type="date">';
					  $data .= '<option value="">'.esc_html(__("Month","pie-register")).'</option>';
					  for($a=1;$a<=12;$a++){
						  if(isset($date_this_values['date']['mm']) && $date_this_values['date']['mm'] == $a)
						  	$sel = ' selected=""';
						  else
						  $sel = '';	
						  $data .=  '<option value="'.str_pad($a, 2, "0", STR_PAD_LEFT).'" '.$sel.'>'.str_pad(__($a,"pie-register"), 2, "0", STR_PAD_LEFT).'</option>';
					  }
					  $data .=  '</select>';
				  $data .= '</div>';
				  $data .= '<div class="time_fields">';
					$data .= '<select '.$this->read_only.' id="dd_'.esc_attr($this->id).'" name="'.esc_attr($this->name).'[date][dd]" class="'.esc_attr($this->addClass("input_fields",array("custom[day]"))).'" data-type="date">';
					  $data .= '<option value="">'.esc_html(__("Day","pie-register")).'</option>';
					  for($a=1;$a<=31;$a++){
						  if(isset($date_this_values['date']['dd']) && $date_this_values['date']['dd'] == $a)
						  	$sel = ' selected=""';
						  else
						  $sel = '';	
						  $data .=  '<option value="'.str_pad($a, 2, "0", STR_PAD_LEFT).'" '.$sel.'>'.str_pad(__($a,"pie-register"), 2, "0", STR_PAD_LEFT).'</option>';
					  }
					  $data .= '</select>';
				  $data .= '</div>';
				$data .= '</div>';
			}
			else
			{
				$data .= '<div class="piereg_time date_format_field">';
				  $data .= '<div class="time_fields">';
					$data .= '<select '.$this->read_only.' id="dd_'.esc_attr($this->id).'" name="'.esc_attr($this->name).'[date][dd]" class="'.esc_attr($this->addClass("input_fields",array("custom[day]"))).'" data-type="date">';
					  $data .= '<option value="">'.esc_html(__("Day","pie-register")).'</option>';
					  for($a=1;$a<=31;$a++){
						  if(isset($date_this_values['date']['dd']) && $date_this_values['date']['dd'] == $a)
						  	$sel = ' selected=""';
						  else
							  $sel = '';	
						  $data .=  '<option value="'.str_pad($a, 2, "0", STR_PAD_LEFT).'" '.$sel.'>'.str_pad(__($a,"pie-register"), 2, "0", STR_PAD_LEFT).'</option>';
					  }
					  $data .= '</select>';
				  $data .= '</div>';
				  $data .= '<div class="time_fields">';
					$data .= '<select '.$this->read_only.' id="mm_'.esc_attr($this->id).'" name="'.esc_attr($this->name).'[date][mm]" class="'.esc_attr($this->addClass("input_fields",array("custom[month]"))).'" '.$this->addValidation().' data-type="date">';
					  $data .= '<option value="">'.esc_html(__("Month","pie-register")).'</option>';
					  for($a=1;$a<=12;$a++){
						  if(isset($date_this_values['date']['mm']) && $date_this_values['date']['mm'] == $a)
						  	$sel = ' selected=""';
						  else
							  $sel = '';
						  $data .=  '<option value="'.str_pad($a, 2, "0", STR_PAD_LEFT).'" '.$sel.'>'.str_pad(__($a,"pie-register"), 2, "0", STR_PAD_LEFT).'</option>'; 
					  }
					  $data .=  '</select>';
				  $data .= '</div>';
				  	 $data .= '<div class="time_fields">';
					$data .= '<select '.$this->read_only.' id="yy_'.esc_attr($this->id).'" name="'.esc_attr($this->name).'[date][yy]" class="'.esc_attr($this->addClass("input_fields",array("custom[year]"))).'" data-type="date">';
					  $data .= '<option value="">'.esc_html(__("Year","pie-register")).'</option>';
					  for($a=((int)date("Y") + 10);$a>=(((int)date("Y"))-100);$a--){
						  if(isset($date_this_values['date']['yy']) && $date_this_values['date']['yy'] == $a)
						  	$sel = ' selected=""';
						  else
							  $sel = '';	
						  $data .=  '<option value="'.esc_attr($a).'" '.esc_attr($sel).'>'.esc_html(__($a,"pie-register")).'</option>';
					  }
					  $data .=  '</select>';
				  $data .= '</div>';
				$data .= '</div>';
			}	
			
			if($this->piereg_field_visbility_addon_active){
				$this->readibility = apply_filters("pie_add_hidden_field_addon", $this->read_only);
			}

			if($this->readibility){
				$this->read_only = "";
				$data .= '<div class="control_visibility">';
				$data .=  $this->addDate();
				$data .=  '</div>';
			}
		}
		$data .= wp_kses_post($this->addDesc());
		$data .= '</div>';
		return $data;
	}
	function addInvitationField(){
		
		// invite_through_email
		$data = "";
		$type = "text";

		$data .= '<div class="fieldset">';
		$data .= wp_kses_post($this->addLabel());
		$data .= '<input id="'.esc_attr($this->id).'" '.$this->addValidation().' name="invitation" class="'.esc_attr($this->addClass()).'" data-field_id="'.esc_attr($this->get_pr_widget_prefix()).'piereg_field_'.esc_attr($this->no).'" placeholder="'.esc_attr($this->field['placeholder']).'" type="'.esc_attr($type).'" value="" />';
		$data .= wp_kses_post($this->addDesc());
		$data .= '</div>';
		return $data;
	}
	function addTermsField(){
		$data  = '<div class="fieldset piereg-wrap-terms">';
		$data .= '<label for="terms_'.esc_attr($this->id).'">';
		$dymanic_class = $this->field['type']."_".$this->field['id'];
		$selected = (!empty($this->getDefaultValue($this->name)) && $this->getDefaultValue($this->name) == '1') ? "checked='checked'": "";
		$data .= '<input '.$selected.' id="terms_'.esc_attr($this->id).'" name="'.esc_attr($this->name).'" class="'.esc_attr($this->addClass()).'" data-field_id="'.esc_attr($this->get_pr_widget_prefix()).'piereg_field_'.esc_attr($this->no).'" type="checkbox" value="1" data-map-field-by-class="'.esc_attr($dymanic_class).'" data-errormessage-value-missing="'.esc_attr($this->field['validation_message']).'" />';
		$page_id	= $this->field['cont'];
		$page_url	= get_the_permalink($page_id);		
		if( !empty($this->field['label']) )
		{
			$data .= $this->field['label'];
			$data .= (substr_compare($this->field['label'], '.', -strlen('.')) === 0) ? '' : '. ';		
		}		
		$data  .= apply_filters('piereg_terms_field_text',sprintf(__('Click <a target="_blank" href="%s">here</a> to view.','pie-register'),$page_url));		
		$data .= '</label></div>';
		return $data;
	}
	function addCaptcha($id,$piereg_widget = false)
	{
		if( $piereg_widget ){
			$cap_id = "is_registration_widget";
			$cookie = 'registration_widget';
		}else{
			$cap_id = "not_registration_widget";
			$cookie = 'registration';
		}
		$data  = "";
		$class = "";
		$settings  	= get_option(OPTION_PIE_REGISTER);
		$recaptcha_type	= $settings['piereg_recaptcha_type'] ;
		$publickey_v3	= $settings['captcha_publc_v3'];
		$publickey	    = $settings['captcha_publc'];
		
		if($recaptcha_type == "v3" && $publickey_v3)
			$class = ' piereg-recaptcha-v3';

		$data .= '<div class="fieldset'.$class.'">';

		if($recaptcha_type == "v3" && $publickey_v3){
			$data .= '<div class="input_fields piereg_recaptcha_reg_div_v3" >';
				$data .= '<input type="hidden" name="g-recaptcha-response" id="reg_form_'.esc_attr($id).'" value="">';
			$data .= '</div>';
		}
		elseif($publickey){
			$data .= wp_kses_post($this->addLabel());
			$data .= '<div class="input_fields piereg_recaptcha_reg_div piereg_validate[required]" data-form-id="reg_form_'.esc_attr($id).'"  id="piereg_form_'.esc_attr($id).'_'.esc_attr($cap_id).'" data-type="recaptcha" >';
				// When Form is added through Gutenberg/WPBakery - Captcha is not rendered on the backend
				if( (isset($_GET['context']) && $_GET['context'] == 'edit') || (isset($_GET['vc_editable']) && $_GET['vc_editable'] == 'true'))
					$data .= esc_html__("Actual captcha will be displayed on the front-end","pie-register").'.';
			$data .= '</div>';
		}
		else {
			$data .= '<div class="input_fields pie_captcha_err" id="reg_form_'.esc_attr($id).'">';
				$data .= esc_html__("Captcha is not configured correctly","pie-register").'.';
			$data .= '</div>';
		}

		$data .= wp_kses_post($this->addDesc());
		$data .= '</div>';
		return $data;
	}
	function addMath_Captcha($piereg_widget = false){
		if( $piereg_widget ){
			$cookie = 'registration_widget';
		}else{
			$cookie = 'registration';
		}
		$data = '<div class="fieldset">'.wp_kses_post($this->addLabel());
		$operator = rand(0,1);
		////1 for add(+)
		////0 for subtract(-)
		$data = "";
		$field_id = "";
		if($piereg_widget == true){
			$data .= '<div data-cookiename="'.esc_attr($cookie).'" class="piereg_math_captcha prMathCaptcha">';
			$data .= '<div class="wrapmathcaptcha prMathCaptcha">';
			$data .= '<div id="pieregister_math_captha_widget" class="piereg_math_captcha"></div>';
			$data .= '<input id="'.esc_attr($this->id).'" type="text" data-errormessage-value-missing="'.esc_attr($this->field['validation_message']).'" data-errormessage-range-underflow="'.esc_attr($this->field['validation_message']).'" data-errormessage-range-overflow="'.esc_attr($this->field['validation_message']).'" class="'.esc_attr($this->addClass()).'" placeholder="'.esc_attr($this->field['placeholder']).'" style="width:50%;margin-top:9px;" name="piereg_math_captcha_widget"/>';
			$field_id = "#pieregister_math_captha_widget";
		}
		else{
			$data .= '<div data-cookiename="'.esc_attr($cookie).'" class="wrapmathcaptcha prMathCaptcha">';
			$data .= '<div id="pieregister_'.esc_attr($this->id).'" class="piereg_math_captcha"></div>';
			$data .= '<input id="'.esc_attr($this->id).'" type="text" data-errormessage-value-missing="'.esc_attr($this->field['validation_message']).'" data-errormessage-range-underflow="'.esc_attr($this->field['validation_message']).'" data-errormessage-range-overflow="'.esc_attr($this->field['validation_message']).'" class="'.esc_attr($this->addClass()).'" placeholder="'.esc_attr($this->field['placeholder']).'" style="width:50%;margin-top:9px;" name="piereg_math_captcha"/>';
			$data .= '</div>';
			
			$field_id = "#pieregister_math_captha";
		}
		$data .= wp_kses_post($this->addDesc());
		return $data;
	}
	
	function addSubmit($options = array())
	{
		$data = "";
		$data .= '<div class="fieldset">';
		$data .= '<div class="pie_wrap_buttons">';
		
		if($this->pages > 1)
		{
			if( $this->pageBreak_prev_type == 'url' ) {
				$data .= '<img class="pie_prev" name="pie_prev" id="pie_prev_'.esc_attr($this->pages).'" src="'.esc_url($this->pageBreak_prev_label).'"  />';				
			} else{
				if($this->pageBreak_prev_label == '')
					$this->pageBreak_prev_label = "Previous";
					
				$data .= '<input class="pie_prev" name="pie_prev" id="pie_prev_'.esc_attr($this->pages).'" type="button" value="'.esc_attr(__($this->pageBreak_prev_label,"pie-register")).'" />';
			}			
			$data .= '<input id="pie_prev_'.esc_attr($this->pages).'_curr" name="page_no" type="hidden" value="'.esc_attr($this->pages-1).'" />';						
		}
		
		$data .= '<input name="pie_submit" class="pie_submit" type="submit" value="'.esc_attr(__($this->field['text'],"pie-register")).'" />';		// stripe_changes	
		
		if($this->field['reset']==1)
		{
			$data .= '<input name="pie_reset" type="reset" value="'.esc_attr(__($this->field['reset_text'],"pie-register")).'" />';
		}
		
		$data .= wp_kses_post($this->addDesc());
		$data .= '</div>';
		$data .= '</div>';
		return $data;
	}
	
	function addPaypal()
	{
		return '<input name="pie_submit" value="paypal" type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_buynowCC_LG.gif" />';	
	}
	
	function addPagebreak($fromwidget = false)
	{
		$data = "";
		$cl = "";
		if($fromwidget)
			$cl = 'piewid_';
		
		
		$data .= '<div class="fieldset">'.wp_kses_post($this->addLabel());
		
		$data .= '<input id="'.esc_attr($cl).'total_pages" class="piereg_regform_total_pages" name="pie_total_pages" type="hidden" value="'.esc_attr($this->countPageBreaks()).'" />';
		
		if($this->pageBreak_prev_label == ''){
			if($this->field['prev_button']=="text"){
				$this->pageBreak_prev_label = $this->field['prev_button_text'];
			} else if($this->field['prev_button']=="url") {
				$this->pageBreak_prev_label = $this->field['prev_button_url'];
			}			
		}
		
		if( $this->pageBreak_prev_type == '')
			$this->pageBreak_prev_type = $this->field['prev_button'];
		
		if($this->pages > 1){
			
			$data .= '<input id="'.esc_attr($cl).'pie_prev_'.esc_attr($this->pages).'_curr" name="page_no" type="hidden" value="'.esc_attr($this->pages-1).'" />';		
			
			if($this->pageBreak_prev_type == "text")
			{
				$data .= '<input class="pie_prev" name="pie_prev" id="'.esc_attr($cl).'pie_prev_'.esc_attr($this->pages).'" type="button" value="'.esc_attr(__($this->pageBreak_prev_label,"pie-register")).'" />';	
			}
			else if($this->pageBreak_prev_type == "url")
			{
				$data .= '<img class="pie_prev" name="pie_prev" id="'.esc_attr($cl).'pie_prev_'.esc_attr($this->pages).'" src="'.esc_url($this->pageBreak_prev_label).'"  />';		
			}
			
			if($this->field['prev_button']=="text"){
				$this->pageBreak_prev_label = $this->field['prev_button_text'];
			} else if($this->field['prev_button']=="url") {
				$this->pageBreak_prev_label = $this->field['prev_button_url'];
			}
			
			$this->pageBreak_prev_type = $this->field['prev_button'];
			
		}
		
		
		$data .= '<input id="'.esc_attr($cl).'pie_next_'.esc_attr($this->pages).'_curr" name="page_no" type="hidden" value="'.esc_attr($this->pages+1).'" />';	
		if($this->field['next_button']=="text")
		{
			$data .= '<input class="'.esc_attr($cl).'pie_next" name="pie_next" id="'.esc_attr($cl).'pie_next_'.esc_attr($this->pages).'" type="button" value="'.esc_attr(__($this->field['next_button_text'],"pie-register")).'" />';
		}
		else if($this->field['next_button']=="url")
		{
			$data .= '<img style="cursor:pointer;" src="'.esc_url($this->field['next_button_url']).'" class="'.esc_attr($cl).'pie_next" name="pie_next" id="'.esc_attr($cl).'pie_next_'.esc_attr($this->pages).'" />';	
		}
		$data .= wp_kses_post($this->addDesc());
		$data .= '</div>';
		
		return $data;	
	}
	
	function countPageBreaks()
	{
		$pages = 1;
		if( isset($this->data) && !empty($this->data) && is_array($this->data) ){
			foreach($this->data as $field)
			{
				if($field['type']=="pagebreak")
					$pages++;	
			}
		}
		return $pages ;
	}
	
	function check_readability(){
		if($this->piereg_field_visbility_addon_active){
			if( isset($this->field['enable_read_only']) && $this->field['enable_read_only'] != "disabled"){
				$this->read_only = apply_filters('pie_addon_readibility', $this->read_only, $this->field, 'registration');
			}		
			return $this->read_only;
		}
	}
	function printFields($fromwidget = false,$form_id="default",$title="false",$description="false")
	{
		if(!isset( $this->data['form']['label_alignment'] )) $this->data['form']['label_alignment'] = "";
		
		if($fromwidget == true)
			$this->is_pr_widget = true;
		else
			$this->is_pr_widget = false;
		
		if($form_id == "default" || $form_id == "0")
		{
			$id = "default";
			$this->data = $this->getCurrentFields();
			$this->label_alignment = isset($this->data['form']['label_alignment']) ? $this->data['form']['label_alignment'] : "";	
			$this->pages = 1;
		}
		else
		{
			$id = intval($form_id);
			$this->data = $this->getCurrentFields($id);
			$this->label_alignment = isset($this->data['form']['label_alignment']) ? $this->data['form']['label_alignment'] : "";
			$this->pages = 1;
		}
		
		$pie_reg_fields = "";
		$update = get_option(OPTION_PIE_REGISTER);
		$pie_reg_fields .= $this->addFormData($title,$description);
		$pie_reg_fields .= '<ul id="pie_register">';

		if(is_array($this->data)){
			$form_label = isset($this->data['form']['label']) ? $this->data['form']['label'] : '';
			foreach($this->data as $this->field)
			{
				if($this->field['type']=="")
				{
					continue;
				}
				
				if( $this->field['type'] == "two_way_login_phone" ) {
					$twilio_options = get_option('pie_register_twilio');
					if( isset($twilio_options["enable_twilio"]) && $twilio_options["enable_twilio"] == "0" ){
						continue;
					}
				}
				
				if( $this->field['type']=="invitation" && $update["enable_invitation_codes"]=="0" )
				{
					continue;
				}
				
				if( $this->field['type'] == "honeypot" ) {	
					continue;
				}	
				
				if($this->field['type'] == "form"){
					$pie_reg_fields .= '<input type="hidden" value="'.$id.'" name="form_id" />';
					continue;
				}
	
				$this->name 	= $this->createFieldName($this->field['type']."_".((isset($this->field['id']))?$this->field['id']:""));
				$this->id 		= $this->name;
				$this->no		= ( isset($this->field['id']) ? $this->field['id'] : "" );

				$this->read_only         = "";
				$this->not_visible       = false;
				$this->readibility       = false;

				if($this->piereg_field_visbility_addon_active){
					if(isset($this->field['show_on']) && !empty($this->field['show_on'])){
						$this->not_visible = apply_filters('pie_visibility_on_reg', $this->field, $this->not_visible);
					}
					if($this->not_visible){
						$pie_reg_fields .= '<input id="'.esc_attr($this->id).'" name="'.esc_attr($this->name).'" data-field_id="'.esc_attr($this->get_pr_widget_prefix()).'piereg_field_'.esc_attr($this->no).'" type="hidden" value="" />';
						continue;
					}	
				}
	
				//We don't need to print li for hidden field
				if($this->field['type'] == "hidden")
				{
					$pie_reg_fields .= $this->addHiddenField($form_label);
					continue;
				}
				
				
				$topclass = "";
				if($this->label_alignment=="top")
					$topclass = " label_top";
				
				
				$_parent = isset($this->field['css']) ? $this->field['css'] : "";
				if( !empty( $_parent ) )
				{
					$_parent = 'parent_' . $_parent;
				}
				
				$pie_reg_fields .= '<li class="fields '.esc_attr($_parent.$topclass).'  pageFields_'.esc_attr($this->pages).' '.esc_attr($this->get_pr_widget_prefix()).'piereg_li_'.(isset($this->field['id'])?esc_attr($this->field['id']):"").'" >';
				
				if($this->field['type'] == "pagebreak")
				{
					$pie_reg_fields .= $this->addPagebreak($fromwidget);	
					$this->pages++;			
				}
				//Printting Field
				switch($this->field['type']){
					case 'text' :								
					case 'website' :
						$this->read_only = $this->check_readability();
						$pie_reg_fields .= $this->addTextField();
					break;				
					case 'username' :
						$pie_reg_fields .= $this->addUsername($fromwidget);
					break;
					case 'password' :
						$pie_reg_fields .= $this->addPassword($fromwidget);
					break;
					case 'email' :
						$pie_reg_fields .= $this->addEmail($fromwidget);
					break;
					case 'textarea':
						$this->read_only = $this->check_readability();
						$pie_reg_fields .= $this->addTextArea();
					break;
					case 'dropdown':
					case 'multiselect':
					case 'custom_role':
						$this->read_only = $this->check_readability();
						$pie_reg_fields .= $this->addDropdown();
					break;
					case 'number':
						$this->read_only = $this->check_readability();
						$pie_reg_fields .= $this->addNumberField();			
					break;
					case 'radio':
					case 'checkbox':
						$this->read_only = $this->check_readability();
						$pie_reg_fields .= $this->addCheckRadio();
					break;
					case 'html':
						$pie_reg_fields .= $this->addHTML();
					break;
					case 'name':
						$this->read_only = $this->check_readability();
						$pie_reg_fields .= $this->addName();
					break;
					case 'time':
						$this->read_only = $this->check_readability();
						$pie_reg_fields .= $this->addTime();
					break;
					case 'upload':
						$this->read_only = $this->check_readability();
						$pie_reg_fields .= $this->addUpload();
					break;
					case 'profile_pic':
						$this->read_only = $this->check_readability();
						$pie_reg_fields .= $this->addProfilePicUpload();
					break;
					case 'address':
						$this->read_only = $this->check_readability();
						$pie_reg_fields .= $this->addAddress();
					break;
					//pie-register-woocommerce addon
					case 'wc_billing_address':
						$pie_reg_fields .= $this->addWooCommerceBillingAddress();
					break;
					case 'wc_shipping_address':
						$pie_reg_fields .= $this->addWooCommerceShippingAddress();
					break;
					case 'captcha':
						$pie_reg_fields .= $this->addCaptcha($id,$fromwidget);
					break;
					case 'math_captcha':
						global $piereg_math_captcha_register,$piereg_math_captcha_register_widget;
						if($piereg_math_captcha_register != true && $fromwidget == false){
							$pie_reg_fields .= '<div class="fieldset">'.wp_kses_post($this->addLabel());
							$pie_reg_fields .= $this->addMath_Captcha($fromwidget);
							$pie_reg_fields .= '</div>';
							// $piereg_math_captcha_register = true;
						}elseif($piereg_math_captcha_register_widget != true && $fromwidget == true){
							$pie_reg_fields .= '<div class="fieldset">'.wp_kses_post($this->addLabel());
							$pie_reg_fields .= $this->addMath_Captcha($fromwidget);
							$pie_reg_fields .= '</div>';
							$piereg_math_captcha_register_widget = true;
						}
					break;
					case 'phone':
						$this->read_only = $this->check_readability();
						$pie_reg_fields .= $this->addPhone();
					break;
					case 'two_way_login_phone':
						if( is_plugin_active('pie-register-twilio/pie-register-twilio.php') && get_option('piereg_api_manager_addon_Twilio_activated') == "Activated") {
							$this->name = "piereg_two_way_login_phone";
							$this->read_only = $this->check_readability();
							$pie_reg_fields .= $this->addPhone();
						}
					break;
					case 'date':
						$this->read_only = $this->check_readability();
						$pie_reg_fields .= $this->addDate();			
					break;
					case 'list':
						$this->read_only = $this->check_readability();
						$pie_reg_fields .= $this->addList();
					break;
					case 'pricing':
						$pie_reg_fields .= $this->addPricing();
					break;
					case 'sectionbreak':
						$pie_reg_fields .= $this->addSectionBreak();
					break;	
					case 'default':
						$pie_reg_fields .= $this->addDefaultField();
					break;
					case 'terms':
						$pie_reg_fields .= $this->addTermsField();
					break;
					case 'invitation':
						$pie_reg_fields .= $this->addInvitationField();
					break;
					case 'submit':
						// mailchimp related code within PR
						if (is_plugin_active('pie-register-mailchimp/pie-register-mailchimp.php')  ) {
                            $pie_reg_fields .= apply_filters("pieregister_print_subscription_checkbox", $form_id);
                        }
						$pie_reg_fields .= $this->addSubmit($update);
					break;					
				}
						
				if($this->field['type'] == "password" )
				{
					$widget = (isset($fromwidget) && $fromwidget == true)? '_widget' : '';
					$pie_reg_fields .= '<input class="prMinimumPasswordStrengthlength" type="hidden" id="password_strength_meter_'.esc_attr($id).'" data-id="'.esc_attr($id).'" value="'.esc_attr((isset($this->field['restrict_strength']))?intval($this->field['restrict_strength']):0).'" />';
					//Weak Password	
					$strength_message = ((isset($this->field['strength_message']) && !empty($this->field['strength_message']))?esc_html__($this->field['strength_message'],"pie-register"):esc_html__("Weak Password","pie-register"));
                    $pie_reg_fields .= '<span class="prMinimumPasswordStrengthMessage" id="password_strength_message_'.esc_attr($id).'" style="display:none;">'.esc_html($strength_message).'</span>';
				}
	
				$pie_reg_fields .=  '</li>';
				if($this->field['type'] == "password" && $this->field['show_meter']==1)
				{
					$topclass = "";
					if($this->label_alignment=="top")
						$topclass = "label_top";
						
					$pie_reg_fields .=  '<li class="fields pageFields_'.esc_attr($this->pages).' '.esc_attr($topclass).' '.esc_attr($this->get_pr_widget_prefix()).'piereg_li_'.esc_attr($this->field['id']).'">';
					//NEW PASSWORD STRENGHT METER
					$widget = (isset($fromwidget) && $fromwidget == true)? '_widget' : '';
					$widget_style = (isset($fromwidget) && $fromwidget == true)? 'display: none;' : 'visibility: hidden;';
					$pie_reg_fields .=  '<div id="password_meter" class="fieldset">';
					$pie_reg_fields .=  '<label style="'.esc_attr($widget_style).'">'.esc_html(__("Password not entered","pie-register")).'</label>';
					$pie_reg_fields .=  '<div id="piereg_passwordStrength'.esc_attr($widget).'" class="piereg_pass prPasswordStrengthMeter" >'.esc_html__($update['pass_strength_indicator_label'],"pie-register").'</div>';
					$pie_reg_fields .=  '</div>';
					$pie_reg_fields .=  '</li>';

				}
			}
		}
		$pie_reg_fields .= '</ul>';
		return $pie_reg_fields;
	}
	function get_pr_widget_prefix(){
		if($this->is_pr_widget == true)
			return "widget_";
			
		return "";
	}
}