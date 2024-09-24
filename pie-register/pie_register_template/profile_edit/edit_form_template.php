<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 

if( file_exists( (PIEREG_DIR_NAME)."/classes/edit_form.php" ) ){
	require_once( (PIEREG_DIR_NAME)."/classes/edit_form.php" );
}

class Edit_form_template extends Edit_form
{
	var $is_pr_widget = false;
	var $pageBreak_prev_label 	= ''; 
	var $pageBreak_prev_type 	= '';

	function __construct($user,$form_id = "default")	
	{
		parent::__construct($user,$form_id);
	}
	function addDesc()
	{
		if(!empty($this->field['desc']))
		{
			return '<p class="desc">'.html_entity_decode($this->field['desc']).'</p>';
		}
		return "";
	}
	function addFormData()
	{
		return ''; //return '<h1 id="piereg_pie_form_heading">'.__("Profile Page","pie-register").'</h1>';
	}
	function addDefaultField()
	{
		$data = "";
		$val = get_user_meta($this->user->data->ID , $this->field['field_name'], true);  #get_usermeta deprecated
		$data .= '<div class="fieldset">'.wp_kses_post($this->addLabel());
		
		if($this->field['field_name']=="url") {
			if( empty($val) ) {
				$val = $this->user->data->user_url;
			}			
		}
		
		if($this->field['field_name']=="description")
		{
			$data .= '<textarea name="description" data-field_id="piereg_field_'.esc_attr($this->no).'" id="description" rows="5" cols="80">'.esc_textarea($val).'</textarea>';	
		}
		else
		{
			$placeholder = isset($this->field['placeholder']) ? $this->field['placeholder'] : "";
			$data .= '<input id="'.esc_attr($this->id).'" name="'.esc_attr($this->field['field_name']).'" data-field_id="piereg_field_'.esc_attr($this->no).'" class="'.esc_attr($this->addClass()).'"  placeholder="'.esc_attr($placeholder).'" type="text" value="'.esc_attr($val).'" />';	
		}
		
		$data .= '</div>';
		return $data;
	}
	
	function addTextField(){
		$val   = get_user_meta($this->user->data->ID , $this->slug, true); #get_usermeta deprecated
		if(is_array($val)){
			$val = implode( ",", $val );
		}
		
		$data  = '<div class="fieldset">'.wp_kses_post($this->addLabel());
		$data .= '<input '.esc_attr($this->read_only).' id="'.esc_attr($this->id).'" name="'.esc_attr($this->name).'" data-field_id="piereg_field_'.esc_attr($this->no).'" class="'.esc_attr($this->addClass()).'" '.$this->addValidation().' placeholder="'.esc_attr($this->field['placeholder']).'" type="text" value="'.esc_attr($val).'" />';
		$data .= wp_kses_post($this->addDesc());
		$data .= '</div>';
		return $data;
	}
	function addHiddenField()
	{
		$val = get_user_meta($this->user->data->ID , $this->slug, true); #get_usermeta deprecated
		return '<input id="'.esc_attr($this->id).'" name="'.esc_attr($this->name).'" data-field_id="piereg_field_'.esc_attr($this->no).'"  type="hidden" value="'.esc_attr($val).'" />';		
	}
	function addUsername(){
		
		$data  = '<div class="fieldset">'.wp_kses_post($this->addLabel());
		$data .= '<input type="text" data-field_id="piereg_field_'.esc_attr($this->no).'" value="'.esc_attr($this->user->data->user_login).'" readonly="readonly" disabled="disabled" class="'.esc_attr($this->field['css']).' input_fields" />';
		$data .= wp_kses_post($this->addDesc());
		$data .= '</div>';
		return $data;
	}
	function addPassword(  $noPasswordField=false ){
		$class = "";
		$fclass = "";
		$topclass = "";
		$data = "";	

		if ( $noPasswordField )
		{
			$this->field = array();
			//  added 3.7.5.1 - Need to change in the next release
			$data .= '<li class="fields pageFields_'.esc_attr($this->pages).' '.esc_attr($topclass).'">';
		}
		$pass_strength = apply_filters( 'pie_password_strength_length', 'minSize[8]' );
		$data .= '<div class="fieldset"><label>'.esc_html(__("Current Password","pie-register")).'</label><div '.esc_attr($fclass).'><input id="old_password_'.esc_attr($this->id).'" type="password" class="input_fields" value="" name="old_password" autocomplete="off"><span class="show-hide-password-innerbtn confirm-pass-eye-reg eye"></span></div></li>';
		
		if($this->label_alignment=="top")
			$topclass = "label_top"; 
				
		if( isset($this->field['password_generator']) && $this->field['password_generator'] != "" ){
			$data .= '<li class="password_field fields pageFields_'.esc_attr($this->pages).' '.esc_attr($topclass).'" style="display: none;">';
				$data .= '<div class="fieldset">';
					$data .= '<label>'.esc_html(__("Password","pie-register")).'</label>';
					$data .= '<input id="'.esc_attr($this->id).'" name="password" data-field_id="piereg_field_'.esc_attr($this->no).'" class="'.esc_attr($this->addClass("input_fields",array($pass_strength))).' prPass1" placeholder="'.esc_attr($this->field['placeholder']).'" type="password" value="" autocomplete="off" >';
					$data .= '<span class="show-hide-password-innerbtn eye-slash"></span>';
				$data .= '</div>';
			$data .= '</li>';

			$data .= '<li class="fields edit_confirm_pass pageFields_'.esc_attr($this->pages).' '.esc_attr($topclass).'" style="display: none;">';
				$data .= '<div class="edit_confirm_pass fieldset" style="display:none;">';
				if( !isset( $this->field['hide_confirm_password'] ) )
				{
						if(!empty($this->field['label2'])) 
							$data .= '<label>'.esc_html(__($this->field['label2'],"pie-register")).'</label>';

					$data .= '<div '.esc_attr($fclass).'><input id="confirm_password_'.esc_attr($this->id).'" type="password" class="input_fields prPass2 '.esc_attr($this->field['css']).' piereg_validate[equals['.esc_attr($this->id).']]" placeholder="'.esc_attr($this->field['placeholder2']).'" value="" name="confirm_password" autocomplete="off">';
					$data .= '<span class="show-hide-password-innerbtn confirm-pass-eye-reg eye"></span>';
					$data .= '</div>';
				}
				$data .= '</div>';
			$data .= '</li>';
			
			$data .= '<li class="fields pageFields_'.esc_attr($this->pages).' '.esc_attr($topclass).' li_edit_prof_gen_pass ">';
				$data .= '<div class="fieldset">';
					$data .= '<label>'.esc_html(__("Password","pie-register")).'</label>';
					$data .= '<div class="edit_profile_gen_pass">';
					$data .= '<input ';
					$data .= 'name="password_generator" data-field_id="'.esc_attr($this->get_pr_widget_prefix()).'piereg_field_'.esc_attr($this->no).'" placeholder="'.esc_attr($this->field['placeholder']).'" type="button" value="'.esc_attr(__('Generate Password','pie-register')).'" class="generate_password gen_pass" >';
					$data .= '</div>';
				$data .= '</div>';
			$data .= '</li>';
		}else{

			if ( !$noPasswordField )
			{
				$label = $this->field['label'] === 'Password' ? 'New Password' : $this->field['label'] ;
				$data .= '<li class="fields pageFields_'.esc_attr($this->pages).' '.esc_attr($topclass).'">';
					$data .='<div class="fieldset">'.'<label for="'.esc_attr($this->id).'">'. esc_html(__($label,"pie-register")).'</label>';
						$data .= '<input id="'.esc_attr($this->id).'" name="password" data-field_id="piereg_field_'.esc_attr($this->no).'" class="'.esc_attr($this->addClass("input_fields",array($pass_strength))).'" placeholder="'.esc_attr($this->field['placeholder']).'" type="password" value="" autocomplete="off" >';
						$data .= '<span class="show-hide-password-innerbtn pass-eye-update eye"></span>';
					$data .= '</div>';
				$data .= '</li>';
				
				$data .= '<li class="fields pageFields_'.esc_attr($this->pages).' '.esc_attr($topclass).'"><div class="fieldset">';
				if( !isset( $this->field['hide_confirm_password'] ) )
				{
					if(!empty($this->field['label2'])) $data .= '<label>'.esc_html(__($this->field['label2'],"pie-register")).'</label>';
					$data .= '<div '.esc_attr($fclass).'><input id="confirm_password_'.esc_attr($this->id).'" type="password" class="input_fields '.esc_attr($this->field['css']).' piereg_validate[equals['.esc_attr($this->id).']]" placeholder="'.esc_attr($this->field['placeholder2']).'" value="" name="confirm_password" autocomplete="off">';
					$data .= '<span class="show-hide-password-innerbtn confirm-pass-eye-reg eye"></span>';
					$data .= wp_kses_post($this->addDesc());
					$data .= '</div>';
				}
					$data .= '</div>';
				$data .= '</li>';
			}else{
				$data .= '<li class="fields pageFields_'.esc_attr($this->pages).' '.esc_attr($topclass).'">';
					$data .= '<div class="fieldset"><label>'.esc_html(__("Password","pie-register")).'</label>';
						$data .= '<input id="new_password" name="password" data-field_id="piereg_field_'.esc_attr($this->no).'" class="input_fields piereg_validate[minSize[8]]" type="password" value="" autocomplete="off" >';
						$data .= '<span class="show-hide-password-innerbtn pass-eye-update eye"></span>';
					$data .= '</div>';
				$data .= '</li>';
				$data .= '<li class="fields pageFields_'.esc_attr($this->pages).' '.esc_attr($topclass).'"><div class="fieldset">';
				if( !isset( $this->field['hide_confirm_password'] ) )
				{
					$data .= '<label>'.esc_html(__("Confirm Password","pie-register")).'</label>';
					$data .= '<div '.esc_attr($fclass).'><input id="confirm_password_'.esc_attr($this->id).'" type="password" class="input_fields  piereg_validate[equals[new_password]]" value="" name="confirm_password" autocomplete="off">';
					$data .= '<span class="show-hide-password-innerbtn confirm-pass-eye-reg eye"></span>';
					$data .= wp_kses_post($this->addDesc());
					$data .= '</div>';
				}
					$data .= '</div>';
				$data .= '</li>';
			}
		}
		return $data;	
	}	
	function addEmail(){
		
		$data  = '<div class="fieldset">'.wp_kses_post($this->addLabel());
		$data .= '<input '.esc_attr($this->read_only).' id="'.esc_attr($this->id).'" name="e_mail" data-field_id="piereg_field_'.esc_attr($this->no).'" class="'.esc_attr($this->addClass()).'" '.$this->addValidation().' placeholder="'.esc_attr($this->field['placeholder']).'" type="text" value="'.esc_attr($this->user->data->user_email).'" autocomplete="off" />';
		$data .= wp_kses_post($this->addDesc());
		$data .= '</div>';
		return $data;
	}
	function addUpload()
	{
		$data = "";
		$data .= '<div class="fieldset">'.wp_kses_post($this->addLabel());
		$val = get_user_meta($this->user->data->ID , $this->slug, true); #get_usermeta deprecated
		
		if(is_array($val))
		{
			$val = !empty($val) ? $val[0] : '';
			$temp_dir = $this->piereg_get_uploaded_dir($this->user_id, $this->slug);
			if(!file_exists($temp_dir."/".basename($val))){
				$val = '';
			}
		}
		
		$data .= '<input '.wp_kses_post($this->read_only).' id="'.esc_attr($this->id).'" name="'.esc_attr($this->name).'" data-field_id="piereg_field_'.esc_attr($this->no).'" class="'.esc_attr($this->addClass()).'" '.$this->addValidation().' type="file"  />';
		$data .= '<input id="'.esc_attr($this->id).'_hidden" name="'.esc_attr($this->name).'_hidden" value="'.esc_attr($val).'" type="hidden"  />';
		if( !empty($val) )
		{
			$data .= '<div class="edit-profile-file"><div class="file-wrapper"><a href="'.esc_attr($val).'" target="_blank">'.esc_html(basename($val)).'</a><br /><a class="file-remove" href="javascript:;">Remove</a></div><input type="hidden" name="'.esc_attr($this->slug).'_removed" value="0" /></div>';
		}
		$data .= "</div>";
		return $data;
	}
	function addProfilePic()
	{
		$data = "";
		$data .= '<div class="fieldset">'.wp_kses_post($this->addLabel());
		$val = get_user_meta($this->user->data->ID , $this->slug, true); #get_usermeta deprecated
		if(is_array($val))
		{
			$val = implode( ",", $val );	
		}
		$data .= '<input '.wp_kses_post($this->read_only).' id="'.esc_attr($this->id).'" name="'.esc_attr($this->name).'" data-field_id="piereg_field_'.esc_attr($this->no).'" class="'.esc_attr($this->addClass()).' piereg_validate[funcCall[checkExtensions],ext[gif|GIF|jpeg|JPEG|jpg|JPG|png|PNG|bmp|BMP]] re-upload-pic" '.$this->addValidation().' type="file"  />';
		$data .= '<input id="'.esc_attr($this->id).'_hidden" name="'.esc_attr($this->name).'_hidden" value="'.esc_attr($val).'" type="hidden"  />';
		$ext 		 = (trim(basename($val)))? $val." Not Found" : "Profile Pictuer Not Found";
		$hide_delete = (trim($val) == "") ? true : false;
		$imgPath = (trim($val) != "")? $val : plugins_url("assets/images/userImage.png",PIEREG_DIR_NAME.'/pie_register_template');
		
			$data .= '<div class="edit-profile-img">';
				$data .= '<div class="file-wrapper"><img src="'.esc_url($imgPath).'" alt="'.esc_attr(__('User Profile Picture',"pie-register")).'" />';
					if( $hide_delete == false ) {
						$data .= '<a href="javascript:;" class="file-remove" style="">Remove</a>';
					}
				$data .= '</div>';
				$data .= '<input type="hidden" name="'.esc_attr($this->slug).'_removed" value="0" />';
			$data .= '</div>';
		$data .= "</div>";
		return $data;
	}
	function addTextArea(){
		
		$val = stripslashes(get_user_meta($this->user->data->ID , $this->slug, true)); #get_usermeta deprecated
		$data = '<div class="fieldset">'.wp_kses_post($this->addLabel());
		$data .='<textarea '.esc_attr($this->read_only).' id="'.esc_attr($this->id).'" data-field_id="piereg_field_'.esc_attr($this->no).'" name="'.esc_attr($this->name).'" rows="'.esc_attr($this->field['rows']).'" cols="'.esc_attr($this->field['cols']).'"  class="'.esc_attr($this->addClass()).'"  placeholder="'.esc_attr($this->field['placeholder']).'">'.esc_textarea($val).'</textarea>';
		$data .= wp_kses_post($this->addDesc());
		$data .= '</div>';
		return $data;
	}
	function addName(){
		$data = "";
		$val = get_user_meta($this->user->data->ID , "first_name", true); #get_usermeta deprecated
		if(is_array($val))
		{
			$val = implode(',', $val);	
		}
		$data .= '<div class="fieldset">';
		if(!empty($this->field['label'])) $data .= '<label>'.esc_html(__($this->field['label'],"pie-register")).'</label>';
		$data .= '<input '.esc_attr($this->read_only).' id="'.esc_attr($this->id).'_firstname" data-field_id="piereg_field_'.esc_attr($this->no).'" value="'.esc_attr($val) .'" placeholder="'.esc_attr($this->field['placeholder']).'" name="first_name" class="'.esc_attr($this->addClass()).' input_fields piereg_name_input_field" '.$this->addValidation().' type="text"  />';
		$val = get_user_meta($this->user->data->ID , "last_name", true); #get_usermeta deprecated
		if(is_array($val)){
			$val = implode(',', $val);
		}
		$topclass = "";
		if($this->label_alignment=="top")
			$topclass = "label_top"; 					
		$data .= '</div>';
		if( !isset( $this->field['hide_last_name'] ) )
		{
			$data .= '<div class="fieldset">';
			if(!empty($this->field['label2'])) $data .= '<label>'.esc_html(__($this->field['label2'],"pie-register")).'</label>';
			$data .= '<input '.esc_attr($this->read_only).' id="'.esc_attr($this->id).'_lastname" value="'.esc_attr($val) .'" placeholder="'.esc_attr($this->field['placeholder2']).'" name="last_name" class="'.esc_attr($this->addClass()).' input_fields piereg_name_input_field" '.$this->addValidation().' type="text"  />';
			$data .= wp_kses_post($this->addDesc());
			$data .= '</div>';
		}
		return $data;
	}
	function addTime(){
		$data = "";
		$data .= '<div class="fieldset">'.wp_kses_post($this->addLabel());
		$val = get_user_meta($this->user->data->ID , $this->slug, true); #get_usermeta deprecated
		$data .= '<div class="piereg_time">
					<div class="time_fields">
						<input '.esc_attr($this->read_only).' maxlength="2" id="hh_'.esc_attr($this->id).'" name="'.esc_attr($this->name).'[hh]" type="text"  class="'.esc_attr($this->addClass()).'" '.$this->addValidation().' value="'.((isset($val['hh']))?esc_attr($val['hh']) : "").'">
						<label>'.esc_html(__("HH","pie-register")).'</label>
					</div>
					<span class="colon">:</span>
					<div class="time_fields">
						<input '.esc_attr($this->read_only).' maxlength="2" id="mm_'.esc_attr($this->id).'" type="text" name="'.esc_attr($this->name).'[mm]"  class="'.esc_attr($this->addClass()).'" '.$this->addValidation().' value="'.((isset($val['mm']))?esc_attr($val['mm']):"").'">
						<label>'.esc_html(__("MM","pie-register")).'</label>
					</div>
				<div id="time_format_field_'.esc_attr($this->id).'" class="time_fields"></div>';
		if($this->field['time_type']=="12")
		{
			$time_format = ((isset($val['time_format']))?$val['time_format']:"");
			$data .= '<div id="time_format_field_'.esc_attr($this->id).'" class="time_fields">
				<select '.esc_attr($this->read_only).' name="'.esc_attr($this->name).'[time_format]" >
					<option ' . (($time_format == "am") ? ' selected="selected" ' : "") . ' value="am">'.esc_html(__("AM","pie-register")).'</option>
					<option ' . (($time_format == "pm") ? ' selected="selected" ' : "") . ' value="pm">'.esc_html(__("PM","pie-register")).'</option>
				</select>
			</div>';
		}
		$data .= '</div>';
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
	function addDropdown(){
		
		$data = "";
		$data .= '<div class="fieldset">'.wp_kses_post($this->addLabel());
		$multiple = "";
		$name = $this->name."[]";
		$val = get_user_meta($this->user->data->ID , $this->slug, true); #get_usermeta deprecated
		
		if(!is_array($val)):
			$sel = !empty($val) ? $val : "";
		else:
			$sel = !empty($val) ? $val[0] : "";
		endif;
		
		if($this->field['type']=="multiselect")
		{
			$multiple 	= 'multiple';			
			$sel = $val;
		}		
		
		$data .= '<select '.esc_attr($this->read_only).' '.esc_attr($multiple).' id="'.esc_attr($this->name).'" data-field_id="piereg_field_'.esc_attr($this->no).'" name="'.esc_attr($name).'" class="'.esc_attr($this->addClass("")).'" '.$this->addValidation().' >';
		if($this->field['list_type']=="country")
		{
			$countries = get_option("pie_countries");			 
			$data .= $this->createDropdown($countries,$sel);			   	
		}
		else if($this->field['list_type']=="us_states")
		{
			$us_states	= get_option("pie_us_states");
			$data .= $this->createDropdown($us_states,$sel);
		}
		else if($this->field['list_type']=="can_states")
		{
			$can_states	= get_option("pie_can_states");
			$data .= $this->createDropdown($can_states,$sel);
		}
		else if($this->field['list_type']=="months")
		{
			$data .= '<option value = "1">'.esc_html(__("January","pie-register")).'</option>
				<option value = "2">'.esc_html(__("February","pie-register")).'</option>
				<option value = "3">'.esc_html(__("March","pie-register")).'</option>
				<option value = "4">'.esc_html(__("April","pie-register")).'</option>
				<option value = "5">'.esc_html(__("May","pie-register")).'</option>
				<option value = "6">'.esc_html(__("June","pie-register")).'</option>
				<option value = "7">'.esc_html(__("July","pie-register")).'</option>
				<option value = "8">'.esc_html(__("August","pie-register")).'</option>
				<option value = "9">'.esc_html(__("September","pie-register")).'</option>
				<option value = "10">'.esc_html(__("October","pie-register")).'</option>
				<option value = "11">'.esc_html(__("November","pie-register")).'</option>
				<option value = "12">'.esc_html(__("December","pie-register")).'</option>';
		}
		else if(sizeof($this->field['value']) > 0)
		{
			for($a = 0 ; $a < sizeof($this->field['value']) ; $a++)
			{
				$selected 	= "";				
				
				if( (is_array($val) && in_array($this->field['value'][$a],$val)) || (!empty($this->field['value'][$a]) && $val == $this->field['value'][$a]) ){
					$selected = 'selected="selected"';	
				}				
				
				$data .= '<option '.esc_attr($selected).' value="'.esc_attr($this->field['value'][$a]).'">'.esc_html($this->field['display'][$a]).'</option>';	
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
	function addNumberField(){
		
		$val = get_user_meta($this->user->data->ID , $this->slug, true); #get_usermeta deprecated
		if(is_array($val))
		{
			$val = implode( ",", $val );	
		}
		$data = '<div class="fieldset">'.wp_kses_post($this->addLabel());
		$data .= '<input '.esc_attr($this->read_only).' id="'.esc_attr($this->id).'" data-field_id="piereg_field_'.esc_attr($this->no).'" name="'.esc_attr($this->name).'" class="'.esc_attr($this->addClass()).'" '.$this->addValidation().' placeholder="'.esc_attr($this->field['placeholder']).'" min="'.esc_attr($this->field['min']).'" max="'.esc_attr($this->field['max']).'" type="number" value="'.esc_attr($val).'" />';
		$data .= wp_kses_post($this->addDesc());
		$data .= '</div>';
		return $data;
	}
	function addPhone(){
		$val = get_user_meta($this->user->data->ID , $this->slug, true); #get_usermeta deprecated
		if(is_array($val))
		{
			$val = implode( ",", $val );
		}
		$data = '<div class="fieldset">'.wp_kses_post($this->addLabel());
		$data .= '<input '.esc_attr($this->read_only).' id="'.esc_attr($this->id).'" data-field_id="piereg_field_'.esc_attr($this->no).'" name="'.esc_attr($this->name).'" class="'.esc_attr($this->addClass()).' input_fields" '.$this->addValidation().' placeholder="'.((isset($this->field['placeholder']))?esc_attr($this->field['placeholder']):"").'" type="text" value="'.esc_attr($val).'" />';
		$data .= '</div>';
		return $data;
	}
	function addList()
	{
		$data = "";
		$data .= '<div class="fieldset">'.wp_kses_post($this->addLabel());
		$val = get_user_meta($this->user->data->ID , $this->slug, true); #get_usermeta deprecated
		if(!is_array($val))
			$val = array();
		$arr_val = array();
		$width  = 85 /  $this->field['cols'];

		for($a = 1 ,$c=0; $a <= $this->field['rows'] ; $a++,$c++)
		{
			for($b = 1 ; $b <= $this->field['cols'] ;$b++)
			{
				if(isset($val[$c][$b-1])){
					array_push($arr_val,$val[$c][$b-1]);
				}
			}	
		}
		
		$total_actual_val = $this->field['rows'] * $this->field['cols'];
		$total_user_val   = count($arr_val);
		$val_rows         = ceil($total_user_val / $this->field['cols']);
		$data .= '<div class="pie_list_cover">';
		$total_values = 0;
		for($a = 1 ,$c=0; $a <= $this->field['rows'] ; $a++,$c++)
		{
			if($a==1)
			{
				$data .= '<div class="'.esc_attr($this->id).'_'.esc_attr($a).' pie_list">';
				
				for($b = 1 ; $b <= $this->field['cols'] ;$b++)
				{
					$data .= '<input '.esc_attr($this->read_only).' style="width:'.esc_attr($width).'%;" type="text" name="'.esc_attr($this->name).'['.esc_attr($c).'][]" class="input_fields" value="'.(isset($arr_val[$total_values]) ? esc_attr($arr_val[$total_values]):"").'"> ';
					array_shift($arr_val);
				}
				if( ((int)$this->field['rows']) > 1 && ($val_rows != $this->field['rows']))
				{
					$data .= ' <img src="'.esc_url(PIEREG_PLUGIN_URL."assets/images/plus.png").'" onclick="addList(this,'.esc_js($this->field['rows']).','.esc_js($this->field['id']).');" alt="add" /></div>';	
				}
				
				if( $this->field['rows'] == 1 )	$data .= '</div>';

			}
			else
			{
				if( empty($arr_val) )
					$display_list_style = "display:none;";
				else
					$display_list_style = "display:block;";
					
				$data .= '<div style="'.esc_attr($display_list_style).'" class="'.esc_attr($this->id).'_'.esc_attr($a).' pie_list">';
				for($b = 1 ; $b <= $this->field['cols'] ;$b++)
				{
					$data .= '<input '.esc_attr($this->read_only).' data-type="list" value="'.(isset($arr_val[$total_values]) ? esc_attr($arr_val[$total_values]):"").'" style="width:'.esc_attr($width).'%;" type="text" '.$this->addValidation().' name="'.esc_attr($this->name).'['.esc_attr($c).'][]" class="'.esc_attr($this->addClass()).' input_fields">';
					array_shift($arr_val);
				}
				if($a > $val_rows){
					$data .= ' <img src="'.esc_url(PIEREG_PLUGIN_URL."assets/images/minus.gif").'" onclick="removeList(this,'.esc_js($this->field['rows']).','.esc_js($this->field['id']).','.esc_js($a).');" alt="add" />';
					$data .= '</div>';
				}
			}
			
			
		}
		
		$data .= '</div>';
		$data .= '</div>';
		return $data;
	}
	function addHTML()
	{
		$data  = '<div class="fieldset">';
		$data .= wp_kses_post($this->addLabel());
		$data .= html_entity_decode($this->field['html']);
		$data .= wp_kses_post($this->addDesc());
		$data .= '</div>';
		return $data;
	}
	function addSectionBreak(){
		$class = "";
		
		if($this->label_alignment == "left")
			$class .= "wdth-lft ";
		
		$class .= "sectionBreak";
		
		$data  = '<div class="fieldset aligncenter">';
		if($this->field['label'] != ''){
			$class .= ' break-label';
		}
			// $data .= $this->addLabel();
		$data .= '<div class="'.esc_attr($class).'">';
			$data .= wp_kses_post($this->addLabel());
		$data .= '</div>';
		$data .= wp_kses_post($this->addDesc());
		$data .= '</div>';
		return $data;
	}
	function addPagebreak()
	{
		$data = "";
		$cl = "";
		
		$data .= '<div class="fieldset">';
		
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
			
			$data .= '<input id="'.esc_attr($cl).'pie_prev_'.esc_attr($this->pages).'_curr" name="page_no" type="hidden" value="'.esc_attr(($this->pages-1)).'" />';	
			
			if($this->pageBreak_prev_type == "text")
			{
				//$data .= '<input class="pie_prev" name="pie_prev" id="'.esc_attr($cl).'pie_prev_'.esc_attr($this->pages).'" type="button" value="'.__($this->field['prev_button_text'],"pie-register").'" />';
				$data .= '<input class="pie_prev" name="pie_prev" id="'.esc_attr($cl).'pie_prev_'.esc_attr($this->pages).'" type="button" value="'.esc_attr(__($this->pageBreak_prev_label,"pie-register")).'" />';	
			}
			else if($this->pageBreak_prev_type == "url")
			{
				//$data .= '<img class="pie_prev" name="pie_prev" id="'.esc_attr($cl).'pie_prev_'.esc_attr($this->pages).'" src="'.$this->field['prev_button_url'].'"  />';
				$data .= '<img class="pie_prev" name="pie_prev" id="'.esc_attr($cl).'pie_prev_'.esc_attr($this->pages).'" src="'.esc_url($this->pageBreak_prev_label).'"  />';	
			}
			
			if($this->field['prev_button']=="text"){
				$this->pageBreak_prev_label = $this->field['prev_button_text'];
			} else if($this->field['prev_button']=="url") {
				$this->pageBreak_prev_label = $this->field['prev_button_url'];
			}
			
			$this->pageBreak_prev_type = $this->field['prev_button'];
			
		}
		
		$data .= '<input id="'.esc_attr($cl).'pie_next_'.esc_attr($this->pages).'_curr" name="page_no" type="hidden" value="'.($this->pages+1).'" />';	
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
	function addCheckRadio()
	{
		$data = "";
		$data = '<div class="fieldset">'.wp_kses_post($this->addLabel());
		$val = get_user_meta($this->user->data->ID , $this->slug, true); #get_usermeta deprecated
		if(sizeof($this->field['value']) > 0)
		{
			$data .= '<div class="radio_wrap">';
			for($a = 0 ; $a < sizeof($this->field['value']) ; $a++)
			{
				$checked = '';
				
				if( (is_array($val) && in_array($this->field['value'][$a],$val)) || (is_array($val) && in_array($this->field['value'][$a],$val)) )
				{
					$checked = 'checked="checked"';	
				}				
				if(!empty($this->field['display'][$a]))
				{
					$dymanic_class = $this->field['type']."_".$this->field['id'];
					$data .= "<label>";
					$data .= '<input '.wp_kses_post($this->read_only).' '.$checked.' value="'.esc_attr($this->field['value'][$a]).'" data-field_id="piereg_field_'.esc_attr($this->no).'" type="'.$this->field['type'].'" name="'.esc_attr($this->name).'[]" class="'.esc_attr($this->addClass("")).' radio_fields" '.$this->addValidation().' data-map-field-by-class="'.esc_attr($dymanic_class).'" >';
					$data .= esc_html($this->field['display'][$a]);
					$data .= "</label>";
				}
			}
			$data .= "</div>";		
		}
		$data .= "</div>";
		return $data;
	}
	function addAddress()
	{
		$data = "";
		$data .= '<div class="fieldset">'.wp_kses_post($this->addLabel());
		$val = get_user_meta($this->user->data->ID , $this->slug, true); #get_usermeta deprecated
		$data .= '<div class="address_main">';
		$data .= '<div class="address">
		  <input '.esc_attr($this->read_only).' type="text" name="'.esc_attr($this->name).'[address]" id="'.esc_attr($this->id).'" class="'.esc_attr($this->addClass()).'" '.$this->addValidation().' value="'.((isset($val['address']))?esc_attr($val['address']):"").'">
		  <label>'.esc_html(__("Street Address","pie-register")).'</label>
		</div>';
		 if(empty($this->field['hide_address2']))
		 {
			$data .= '<div class="address">
			  <input '.esc_attr($this->read_only).' type="text" name="'.esc_attr($this->name).'[address2]" id="address2_'.esc_attr($this->id).'" class="input_fields '.esc_attr($this->field['css']).'" '.$this->addValidation().' value="'.((isset($val['address2']))?esc_attr($val['address2']):"").'">
			  <label>'.esc_html(__("Address Line 2","pie-register")).'</label>
			</div>';
		 }
		$data .= '<div class="address">
		  <div class="address2">
			<input '.esc_attr($this->read_only).' type="text" name="'.esc_attr($this->name).'[city]" id="city_'.esc_attr($this->id).'" class="input_fields addressLine2" '.$this->addValidation().' value="'.((isset($val['city']))?esc_attr($val['city']):"").'">
			<label>'.esc_html(__("City","pie-register")).'</label>
		  </div>';
		 if(empty($this->field['hide_state']))
		 {
			 	if($this->field['address_type'] == "International")
				{
					$data .= '<div class="address2"  >
					<input '.esc_attr($this->read_only).' type="text" name="'.esc_attr($this->name).'[state]" id="state_'.esc_attr($this->id).'" class="'.esc_attr($this->addClass()).'" value="'.((isset($val['state']))?esc_attr($val['state']):"").'">
					<label>'.esc_html(__("State / Province / Region","pie-register")).'</label>
				 	 </div>';		
				}
				else if($this->field['address_type'] == "United States")
				{
				  $us_states = get_option("pie_us_states");
				  $options 	= $this->createDropdown($us_states,((isset($val['state']))?$val['state']:""));	
				  $data .= '<div class="address2"  >
				  	<select '.esc_attr($this->read_only).' id="state_'.esc_attr($this->id).'" name="'.esc_attr($this->name).'[state]" class="'.esc_attr($this->addClass("")).'">
					 '.$options.' 
					</select>
					<label>'.esc_html(__("State","pie-register")).'</label>
				  </div>';	
				}
				else if($this->field['address_type'] == "Canada")
				{
					$can_states = get_option("pie_can_states");
				  	$options 	= $this->createDropdown($can_states,((isset($val['state']))?$val['state']:""));
					$data .= '<div class="address2">
						<select '.esc_attr($this->read_only).' id="state_'.esc_attr($this->id).'" class="'.esc_attr($this->addClass("")).'" name="'.esc_attr($this->name).'[state]">
						  '.$options.'
						</select>
						<label>'.esc_html(__("Province","pie-register")).'</label>
					  </div>';		
				}
		 }
		$data .= '</div>';
		$data .= '<div class="address">';	
		$data .= ' <div class="address2">
		<input '.esc_attr($this->read_only).' id="zip_'.esc_attr($this->id).'" name="'.esc_attr($this->name).'[zip]" type="text" class="'.esc_attr($this->addClass()).'" '.$this->addValidation().' value="'.((isset($val['zip']))?esc_attr($val['zip']):"").'">
		<label>'.esc_html(__("Zip / Postal Code","pie-register")).'</label>
		</div>';	 
		if($this->field['address_type'] == "International")
		{
			 $countries = get_option("pie_countries");			 
			 $options 	= $this->createDropdown($countries,((isset($val['country']))?$val['country']:""));  
			 $data .= '<div  class="address2" >
			 		<select '.esc_attr($this->read_only).' id="country_'.esc_attr($this->id).'" name="'.esc_attr($this->name).'[country]" class="'.esc_attr($this->addClass()).'" '.$this->addValidation().'> 
                    <option value="">'.esc_html(__("Select Country","pie-register")).'</option>
					'. $options .'
					 </select>
					<label>'.esc_html(__("Country","pie-register")).'</label>
		  		</div>';
		}
		$data .= '</div>';
		$data .= '</div>';
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
	//pie-register-woocommerce addon
	
	function addWooCommerceBillingAddress()
	{
		if ($this->woocommerce_and_piereg_wc_addon_active)
		{
			$addLabel 		= $this->addLabel();
			$addClass 		= $this->addClass();
			$addValidation 	= $this->addValidation();
			$addDesc		= $this->addDesc();

			// country_options
			global $woocommerce;
			$countries_obj   	= new WC_Countries();
			$countries_list  	= $countries_obj->get_allowed_countries();
			$countries			= array();
			$wc_billing_country = get_user_meta($this->user->data->ID, "billing_country", true);
			foreach($countries_list as $iso_code => $country_name)
			{
				$countries[] = array('iso_code' => $iso_code, 'name' => $country_name);
			}
			$selectedoption 	= (isset($wc_billing_country) && $wc_billing_country)?$wc_billing_country:"";
			$country_options 	= $this->createCountryDropdown($countries,$selectedoption);  

				// states_options
				$states_list 		= $countries_obj->get_states( $selectedoption );
				$states				= array();
				$wc_billing_state 	= get_user_meta($this->user->data->ID, "billing_state", true);
				if ($states_list) 
				{
					foreach($states_list as $iso_code => $state_name)
					{
						$states[] = array('iso_code' => $iso_code, 'name' => $state_name);
					}
				}
				$selectedoption 	= (isset($wc_billing_state))?$wc_billing_state:"";
				$state_options 		= $this->createStatesDropdown($states,$selectedoption);
	
			$arguments = array(
				'field'							=> $this->field, 
				'id' 							=> $this->id, 
				'user' 							=> $this->user, 
				'slug' 							=> $this->slug, 
				'name' 							=> $this->name, 
				'addLabel' 						=> $addLabel, 
				'addClass' 						=> $addClass, 
				'addValidation' 				=> $addValidation, 
				'addDesc' 						=> $addDesc, 
				'country_options' 				=> $country_options, 
				'state_options' 				=> $state_options, 
				'hidden_fields'					=> explode(",", $this->field['hidden_fields']),
				'required_fields'				=> explode(",", $this->field['required_fields'])
			);

			return apply_filters("pieregister_print_woocommerce_billing_address_front", $arguments);
		}
	}

	function addWooCommerceShippingAddress()
	{
		if ($this->woocommerce_and_piereg_wc_addon_active)
		{
			$addLabel 		= $this->addLabel();
			$addClass 		= $this->addClass();
			$addValidation 	= $this->addValidation();
			$addDesc		= $this->addDesc();

			// country_options
			global $woocommerce;
			$countries_obj   	= new WC_Countries();
			$countries_list  	= $countries_obj->get_allowed_countries();
			$countries			= array();
			$wc_shipping_country = get_user_meta($this->user->data->ID, "shipping_country", true);
			foreach($countries_list as $iso_code => $country_name)
			{
				$countries[] = array('iso_code' => $iso_code, 'name' => $country_name);
			}
			$default_country 	= $countries_obj->get_base_country();
			$selectedoption 	= (isset($wc_shipping_country) && $wc_shipping_country)?$wc_shipping_country:$default_country;		 
			$country_options 	= $this->createCountryDropdown($countries,$selectedoption);  

			// states_options
			$states_list 		= $countries_obj->get_states( $selectedoption );
			$states				= array();
			$wc_shipping_state 	= get_user_meta($this->user->data->ID, "shipping_state", true);
			if ($states_list) 
			{
				foreach($states_list as $iso_code => $state_name)
				{
					$states[] = array('iso_code' => $iso_code, 'name' => $state_name);
				}
			}
			$selectedoption 	= (isset($wc_shipping_state))?$wc_shipping_state:"";
			$state_options 		= $this->createStatesDropdown($states,$selectedoption);

			$arguments = array(
				'field'							=> $this->field, 
				'id' 							=> $this->id, 
				'user' 							=> $this->user, 
				'slug' 							=> $this->slug, 
				'name' 							=> $this->name, 
				'addLabel' 						=> $addLabel, 
				'addClass' 						=> $addClass, 
				'addValidation' 				=> $addValidation, 
				'addDesc' 						=> $addDesc, 
				'country_options' 				=> $country_options, 
				'state_options' 				=> $state_options, 
				'hidden_fields'					=> explode(",", $this->field['hidden_fields']),
				'required_fields'				=> explode(",", $this->field['required_fields'])
			);

			return apply_filters("pieregister_print_woocommerce_shipping_address_front", $arguments);
		}
	}

	function addDate()
	{
		$data = "";
		$data .= '<div class="fieldset">'.wp_kses_post($this->addLabel());
		$val = get_user_meta($this->user->data->ID , $this->slug, true);  #get_usermeta deprecated
		
		if($this->field['date_type'] == "datefield")
		{
			if(isset($val['date']) && !is_array($val['date']))
			{
				$val['date']['mm']	= "";
				$val['date']['dd']	= "";
				$val['date']['yy']	= "";
			}
			if($this->field['date_format']=="mm/dd/yy")
			{
			$data .= '<div class="piereg_time date_format_field">
				  <div class="time_fields">
					<input '.esc_attr($this->read_only).' id="mm_'.esc_attr($this->id).'" name="'.esc_attr($this->name).'[date][mm]" maxlength="2" type="text" class="'.esc_attr($this->addClass("input_fields",array("custom[month]"))).'" '.$this->addValidation().' value="'.((isset($val['date']['mm']))?esc_attr($val['date']['mm']):"").'"  data-type="date">
					<label>'.esc_html(__("MM","pie-register")).'</label>
				  </div>
				  <div class="time_fields">
					<input '.esc_attr($this->read_only).' id="dd_'.esc_attr($this->id).'" name="'.esc_attr($this->name).'[date][dd]" maxlength="2"  type="text" class="'.esc_attr($this->addClass("input_fields",array("custom[day]"))).'" '.$this->addValidation().' value="'.((isset($val['date']['dd']))?esc_attr($val['date']['dd']):"").'" data-type="date">
					<label>'.esc_html(__("DD","pie-register")).'</label>
				  </div>
				  <div class="time_fields">
					<input '.esc_attr($this->read_only).' id="yy_'.esc_attr($this->id).'" name="'.esc_attr($this->name).'[date][yy]" maxlength="4"  type="text" class="'.esc_attr($this->addClass("input_fields",array("custom[year]"))).'" '.$this->addValidation().' value="'.((isset($val['date']['yy']))?esc_attr($val['date']['yy']):"").'" data-type="date">
					<label>'.esc_html(__("YYYY","pie-register")).'</label>
				  </div>
				</div>';
			} 
			else if($this->field['date_format']=="yy/mm/dd" || $this->field['date_format']=="yy.mm.dd")
			{
				$data .= '<div class="piereg_time time date_format_field">
				 <div class="time_fields">
					<input '.esc_attr($this->read_only).' value="'.(isset($val['date']['yy'])?esc_attr($val['date']['yy']):"").'" id="yy_'.esc_attr($this->id).'" name="'.esc_attr($this->name).'[date][yy]" maxlength="4"  type="text" class="'.esc_attr($this->addClass("input_fields",array("custom[year]"))).'" data-type="date">
					<label>'.esc_html(__("YYYY","pie-register")).'</label>
				  </div>
				  <div class="time_fields">
					<input '.esc_attr($this->read_only).' value="'.(isset($val['date']['mm'])?esc_attr($val['date']['mm']):"").'" id="mm_'.esc_attr($this->id).'" name="'.esc_attr($this->name).'[date][mm]" maxlength="2" type="text" '.$this->addValidation().'  class="'.esc_attr($this->addClass("input_fields",array("custom[month]"))).'" data-type="date">
					<label>'.esc_html(__("MM","pie-register")).'</label>
				  </div>
				  <div class="time_fields">
					<input '.esc_attr($this->read_only).' value="'.(isset($val['date']['dd'])?esc_attr($val['date']['dd']):"").'" id="dd_'.esc_attr($this->id).'" name="'.esc_attr($this->name).'[date][dd]" maxlength="2"  type="text" class="'.esc_attr($this->addClass("input_fields",array("custom[day]"))).'" data-type="date">
					<label>'.esc_html(__("DD","pie-register")).'</label>
				  </div>				  
				</div>';	
			}
			else
			{
				$data .= '<div class="piereg_time date_format_field">
				 <div class="time_fields">
					<input '.esc_attr($this->read_only).' value="'.(isset($val['date']['dd']) ? esc_attr($val['date']['dd']) :"").'" id="dd_'.esc_attr($this->id).'" name="'.esc_attr($this->name).'[date][dd]" maxlength="2" type="text" class="'.esc_attr($this->addClass("input_fields",array("custom[day]"))).'"  data-type="date">
					<label>'.esc_html(__("DD","pie-register")).'</label>
				  </div>				 
				  <div class="time_fields">
					<input '.esc_attr($this->read_only).' value="'.(isset($val['date']['mm'])?esc_attr($val['date']['mm']):"").'" id="mm_'.esc_attr($this->id).'" name="'.esc_attr($this->name).'[date][mm]" maxlength="2" type="text" class="'.esc_attr($this->addClass("input_fields",array("custom[month]"))).'" data-type="date">
					<label>'.esc_html(__("MM","pie-register")).'</label>
				  </div>	
				  <div class="time_fields">
					<input '.esc_attr($this->read_only).' value="'.(isset($val['date']['yy'])?esc_attr($val['date']['yy']):"").'" id="yy_'.esc_attr($this->id).'" name="'.esc_attr($this->name).'[date][yy]" maxlength="4"  type="text" '.$this->addValidation().' class="'.esc_attr($this->addClass("input_fields",array("custom[year]"))).'" data-type="date">
					<label>'.esc_html(__("YYYY","pie-register")).'</label>
				  </div>
				</div>';	
			}
		}
		else if($this->field['date_type'] == "datepicker")
		{
			if(isset($val['date']))
			if(isset($val['date']['yy']) && is_array($val['date']['yy']))
			{
				$val = 	$val['date']['yy']."-".($val['date']['mm'])."-".($val['date']['dd']);
			}
			else
			{
				$val = 	(isset($val['date'][0])) ? $val['date'][0] : "";	
			}	
				if( $this->field['calendar_icon'] == "calendar" || $this->field['calendar_icon'] == "custom" ) 
				  $data .=	'<div class="piereg_time date_format_field date_with_icon">';
				else
				  $data .=	'<div class="piereg_time date_format_field">';
				
				$data .= '<input readonly id="'.esc_attr($this->id).'" name="'.esc_attr($this->name).'[date][]" type="text" class="'.esc_attr($this->addClass()).' date_start" value="'.esc_attr($val).'"  data-field-visibility-addon="'.( (isset($this->field['enable_read_only'])) && ( ($this->field['enable_read_only'] == "profile") || ($this->field['enable_read_only'] == "profnreg") ) ?"1":"0").'">';
				if($this->field['calendar_icon'] == "calendar")
				{
					 $data .=  '<img id="'.esc_attr($this->id).'_icon" class="calendar_icon" src="'.esc_url(PIEREG_PLUGIN_URL."assets/images/calendar.png").'" >';
				}
				else if($this->field['calendar_icon'] == "custom")
				{
					 $data .=  '<img id="'.esc_attr($this->id).'_icon" class="calendar_icon" src="'.esc_url($this->field['calendar_icon_url']).'"  />'; 
				}
				 $data .= '</div>';	
		}
		else if($this->field['date_type'] == "datedropdown")
		{
			if($this->field['date_format']=="mm/dd/yy")
			{
					$data .= '<div class="piereg_time date_format_field">
				  <div class="time_fields">
					<select '.esc_attr($this->read_only).' id="mm_'.esc_attr($this->id).'" name="'.esc_attr($this->name).'[date][mm]" class="'.esc_attr($this->addClass("")).'" '.$this->addValidation().'  data-type="date">
					  <option value="">'.esc_html(__("Month","pie-register")).'</option>';
					  for($a=1;$a<=12;$a++){
					  	$data .= '<option value="'.str_pad($a, 2, "0", STR_PAD_LEFT).'"';
						$data .= (isset($val['date']['mm']) && $val['date']['mm'] == $a)? 'selected="selected"' : "";
						$data .= '>'.str_pad(__($a,"pie-register"), 2, "0", STR_PAD_LEFT).'</option>';
					  }
					  $data .= '
					</select>
				  </div>';

				  $data .=
				  '<div class="time_fields">
					<select '.esc_attr($this->read_only).' id="dd_'.esc_attr($this->id).'" name="'.esc_attr($this->name).'[date][dd]" class="'.esc_attr($this->addClass("")).'" '.$this->addValidation().' data-type="date">
					  <option value="">'.esc_attr(__("Day","pie-register")).'</option>';
					  for($a=1;$a<=31;$a++){
					  	$data .= '<option value="'.str_pad($a, 2, "0", STR_PAD_LEFT).'"';
						$data .= (isset($val['date']['dd']) && $val['date']['dd'] == $a)? 'selected="selected"' : "";
						$data .= '>'.str_pad(__($a,"pie-register"), 2, "0", STR_PAD_LEFT).'</option>';
					  }
					$data .= '
					</select>
				  </div>';
				  $data .= '
				  <div class="time_fields">
					<select '.esc_attr($this->read_only).' id="yy_'.esc_attr($this->id).'" name="'.esc_attr($this->name).'[date][yy]" class="'.esc_attr($this->addClass("")).'" '.$this->addValidation().' data-type="date">
					  <option value="">'.esc_html(__("Year","pie-register")).'</option>';
					  for($a=((int)date("Y") + 10);$a>=(((int)date("Y"))-100);$a--){
					  	$data .= '<option value="'.esc_attr($a).'"';
						$data .= (isset($val['date']['yy']) && $val['date']['yy'] == $a)? 'selected="selected"' : "";
						$data .= '>'.esc_html(__($a,"pie-register")).'</option>';
					  }
					  $data .= '
					</select>
				  </div>
				</div>';
			}
			else if($this->field['date_format']=="yy/mm/dd" || $this->field['date_format']=="yy.mm.dd")
			{
					$data .= '<div class="piereg_time date_format_field">
					 <div class="time_fields">
					<select '.esc_attr($this->read_only).' id="yy_'.esc_attr($this->id).'" name="'.esc_attr($this->name).'[date][yy]" class="'.esc_attr($this->addClass("")).'"  data-type="date">
					  <option value="">'.esc_html(__("Year","pie-register")).'</option>';
					  for($a=((int)date("Y") + 10);$a>=(((int)date("Y"))-100);$a--){
					  	$data .= '<option value="'.esc_attr($a).'"';
						$data .= (isset($val['date']) && $val['date']['yy'] == $a)? 'selected="selected"' : "";
						$data .= '>'.esc_html(__($a,"pie-register")).'</option>';
					  }
					$data .= '
					</select>
				  </div>';
				  $data .= '
				  <div class="time_fields">
					<select '.esc_attr($this->read_only).' id="mm_'.esc_attr($this->id).'" name="'.esc_attr($this->name).'[date][mm]" class="'.esc_attr($this->addClass("")).'" '.$this->addValidation().' data-type="date">
					  <option value="">'.esc_html(__("Month","pie-register")).'</option>';
					  for($a=1;$a<=12;$a++){
					  	$data .= '<option value="'.str_pad($a, 2, "0", STR_PAD_LEFT).'"';
						$data .= (isset($val['date']) && $val['date']['mm'] == $a)? 'selected="selected"' : "";
						$data .= '>'.str_pad(__($a,"pie-register"), 2, "0", STR_PAD_LEFT).'</option>';
					  }
					  $data .= '
					</select>
				  </div>';
				   $data .=
				  '<div class="time_fields">
					<select '.esc_attr($this->read_only).' id="dd_'.esc_attr($this->id).'" name="'.esc_attr($this->name).'[date][dd]" class="'.esc_attr($this->addClass("")).'"  data-type="date">
					  <option value="">'.esc_html(__("Day","pie-register")).'</option>';
					  for($a=1;$a<=31;$a++){
					  	$data .= '<option value="'.str_pad($a, 2, "0", STR_PAD_LEFT).'"';
						$data .= (isset($val['date']) && $val['date']['dd'] == $a)? 'selected="selected"' : "";
						$data .= '>'.str_pad(__($a,"pie-register"), 2, "0", STR_PAD_LEFT).'</option>';
					  }
					$data .= '
					</select>
				  </div>				 
				</div>';
			}
			else
			{
				$data .= '<div class="piereg_time date_format_field">';
				
				  
				  $data .=
				  '<div class="time_fields">
					<select '.esc_attr($this->read_only).' id="dd_'.esc_attr($this->id).'" name="'.esc_attr($this->name).'[date][dd]" class="'.esc_attr($this->addClass("")).'"  data-type="date">
					  <option value="">'.esc_html(__("Day","pie-register")).'</option>';
					  for($a=1;$a<=31;$a++){
					  	$data .= '<option value="'.str_pad($a, 2, "0", STR_PAD_LEFT).'"';
						$data .= (isset($val['date']['dd']) && $val['date']['dd'] == $a)? 'selected="selected"' : "";
						$data .= '>'.str_pad(__($a,"pie-register"), 2, "0", STR_PAD_LEFT).'</option>';
					  }
					$data .= '
					</select>
				  </div>';
				  
				  $data .= '
				  <div class="time_fields">
					<select  '.esc_attr($this->read_only).' id="mm_'.esc_attr($this->id).'" name="'.esc_attr($this->name).'[date][mm]" class="'.esc_attr($this->addClass("")).'" '.$this->addValidation().' data-type="date">
					  <option value="">'.esc_html(__("Month","pie-register")).'</option>';
					  for($a=1;$a<=12;$a++){
					  	$data .= '<option value="'.str_pad($a, 2, "0", STR_PAD_LEFT).'"';
						$data .= (isset($val['date']['mm']) && $val['date']['mm'] == $a)? 'selected="selected"' : "";
						$data .= '>'.str_pad(__($a,"pie-register"), 2, "0", STR_PAD_LEFT).'</option>';
					  }
						
					  $data .= '
					</select>
				  </div>';
				  	 $data .= '
				  <div class="time_fields">
					<select  '.esc_attr($this->read_only).' id="yy_'.esc_attr($this->id).'" name="'.esc_attr($this->name).'[date][yy]" class="'.esc_attr($this->addClass("")).'" data-type="date">
					  <option value="">'.esc_html(__("Year","pie-register")).'</option>';
					  for($a=((int)date("Y") + 10);$a>=(((int)date("Y"))-100);$a--){
					  	$data .= '<option value="'.esc_attr($a).'"';
						$data .= (isset($val['date']['yy']) && $val['date']['yy'] == $a)? 'selected="selected"' : "";
						$data .= '>'.esc_html(__($a,"pie-register")).'</option>';
					  }
					  
					  $data .= '
					</select>
				  </div>';	 
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
		
	function addLabel()
	{
		if( isset($this->field['label']) && empty($this->field['label']) )
		{
			return "";
		}

		if($this->field['type']=="name" && $this->field['name_format']=="normal")
		{
			return "";
		}
		
		$field_required = "";
		if(isset($this->field['required']) && $this->field['required'] != "") {

			$field_required .= '&nbsp;<span class="piereg_field_required_label">*</span>';
			if( $this->field['type'] == 'password' || $this->field['type'] == 'username') {
				$field_required = "";	
			}
		}
		
		return '<label for="'.esc_attr($this->id).'">'. esc_html(__($this->field['label'],"pie-register")).wp_kses_post($field_required).'</label>';		
	}
	function addClass($default = "input_fields",$val = array())
	{
		$fieldcss = isset($this->field['css']) ? $this->field['css'] : "";
		$class = $default." ".$fieldcss;
		
		if(isset($this->field['required']) && $this->field['required'] && $this->field['type'] != "password") {
			
			if($this->field['type'] == 'upload' || $this->field['type'] == 'profile_pic') {
				
				$uploaded = get_user_meta($this->user->data->ID , $this->slug, true); #get_usermeta deprecated
				if( empty($uploaded) ){
					$val[] = "required";
				}	
			} else {
				$val[] = "required";	
			}
		}
		
		if(isset($this->field['length']) && intval($this->field['length']) > 0 )
		{
			$val[] = "maxSize[".intval($this->field['length'])."]";
		}

		if(isset($this->field['validation_rule']) && $this->field['validation_rule']=="number" )
		{
			$val[] = "custom[number]";
		}
		else if(isset($this->field['validation_rule']) && $this->field['validation_rule']=="alphanumeric")
		{
			$val[] = "custom[alphanumeric]";
		}
		else if((isset($this->field['validation_rule']) && $this->field['validation_rule']  =="alphabetic" ) || $this->field['type']=="name")
		{
			$val[] = "custom[alphabetic]";
		}
		else if((isset($this->field['validation_rule']) && $this->field['validation_rule']=="email") || $this->field['type']=="email")
		{
			$val[] = "custom[email]";
		}
		else if( 
				(isset($this->field['validation_rule'])) && ($this->field['validation_rule']=="website" || $this->field['type']=="website") 
				|| (isset($this->field['field_name']) && $this->field['field_name'] == 'url') 
			)
		{
			$val[] = "custom[url]";
		}
		else if((isset($this->field['validation_rule']) && $this->field['validation_rule']=="standard") || (isset($this->field['phone_format']) && $this->field['phone_format']=="standard" ))
		{
			$val[] = "custom[phone_standard]";		
		}
		else if((isset($this->field['validation_rule']) && $this->field['validation_rule']=="international") || (isset($this->field['phone_format']) && $this->field['phone_format']=="international"))
		{
			$val[] = "custom[phone_international]";		
		}
		else if($this->field['type']=="time")
		{
			$val[] = "custom[number]";	
			$val[] = "minSize[1]";
			$val[] = "maxSize[2]";	
		}
		else if($this->field['type']=="upload" && explode(",",$this->field['file_types']) > 0)
		{
			//$val[] = "funcCall[checkExtensions]";	
			//$val[] = "ext[".str_replace(",","|",$this->field['file_types'])."]";	
			$val[] = "funcCall[checkExtensions]"; 
            $val[] = "ext[".str_replace(array(","," "),array("|",""),$this->field['file_types'])."]";
			
		}
		
		if(sizeof($val) > 0)
		{
			$val = " piereg_validate[".implode(",",$val)."]";
			$class .= $val;	
		}
		
		return $class;	
	}

	function addSubmit()
	{
		$data  = "";
		$data .= '<div class="pie_wrap_buttons">';
		$data .= '<input name="pie_submit_update" type="submit" value="'.esc_attr(__('Update',"pie-register")).'" />';
  
		if($this->pages > 1)
		{
			if( $this->pageBreak_prev_type == 'url' ) 
			{
				$data .= '<img class="pie_prev" name="pie_prev" id="pie_prev_'.esc_attr($this->pages).'" src="'.$this->pageBreak_prev_label.'"  />';				
			}else
			{
				if($this->pageBreak_prev_label == '')
					$this->pageBreak_prev_label = "Previous";
					
				$data .= '<input class="pie_prev" name="pie_prev" id="pie_prev_'.esc_attr($this->pages).'" type="button" value="'.esc_attr(__($this->pageBreak_prev_label,"pie-register")).'" />';
			}			
			$data .= '<input id="pie_prev_'.esc_attr($this->pages).'_curr" name="page_no" type="hidden" value="'.esc_attr(($this->pages-1)).'" />';						
		}
		$check_payment = get_option(OPTION_PIE_REGISTER);
		$cancel_url = $this->get_current_permalink();
		if( isset($check_payment['alternate_profilepage']) && !empty($check_payment['alternate_profilepage']) && empty($cancel_url) ){
			$cancel_url = $this->get_page_uri( $check_payment['alternate_profilepage'] );
		}
		$data .= '<input type="button" class="piereg_cancel_profile_edit_btn" onclick="location.replace(\''.($cancel_url).'\');" value="'.esc_attr(__("Cancel","pie-register")).'" />';
		$data .= '</div>';
		return $data;
	}
	
	function check_readability(){
		if($this->piereg_field_visbility_addon_active){
			if( isset($this->field['enable_read_only']) && $this->field['enable_read_only'] != "disabled"){
				$this->read_only = apply_filters('pie_addon_readibility', $this->read_only, $this->field, 'profile');
			}		
			return $this->read_only;
		}
	}
	function editProfile($user){
		
		$email_user_password = get_user_meta( $user->ID , "email_user_password" , true);
		$noPasswordField = ( $email_user_password == true ) ? true : false ;
		$profile_fields_data = "";
		$this->pages = 1;
		$update = get_option(OPTION_PIE_REGISTER);
		$profile_fields_data .= $this->addFormData();
		$profile_fields_data .= '<ul id="pie_register">';
		
		if( is_array($this->data) && count($this->data) > 0 )
		{
			foreach($this->data as $this->field)
			{
				$this->read_only        = "";
				$this->visibility_check = [true, true];
				$this->not_visible      = "";
				$this->readibility      = "";
				
				if($this->piereg_field_visbility_addon_active){
					if(isset($this->field['show_on']) && !empty($this->field['show_on'])){
						$this->visibility_check = apply_filters('pie_addon_field_visibility_conditions',$this->visibility_check,$this->field);
					}
					if(!$this->visibility_check[0])
					{
						$this->not_visible     = "control_visibility";
					}
				}
				
				if(!is_admin() && isset($this->field['show_in_profile']) && $this->field['show_in_profile']=="0" && $this->visibility_check[1])
				{
					$this->not_visible     = "control_visibility";
				}
				elseif($this->field['type']=="" || $this->field['type'] == "form"){
					continue;
				}
				elseif($this->field['type']=="math_captcha"){
					continue;
				}
				
				$this->name 	= $this->createFieldName($this->field['type']."_".((isset($this->field['id']))?$this->field['id']:""));
				$this->slug 	= $this->createFieldName("pie_".$this->field['type']."_".((isset($this->field['id']))?$this->field['id']:""));
				$this->id 		= $this->createFieldID();
				$this->no		= (isset($this->field['id'])) ? $this->field['id'] : "";
				
				//We don't need to print li for hidden field
				if ($this->field['type'] == "hidden")
				{
					$profile_fields_data .= $this->addHiddenField();
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
				
				$class_x				 = (isset($this->field['id'])) ? $this->field['id'] : "";
				$profile_fields_data 	.= '<li class="fields '.esc_attr($_parent.$topclass).' pageFields_'.esc_attr($this->pages).' piereg_li_'.esc_attr($class_x).' '.esc_attr($this->not_visible).'">';
				
				//Page Break
				if($this->field['type'] == "pagebreak")
				{
					$profile_fields_data .= $this->addPagebreak();	
					$this->pages++;			
				}
				//Printting Field
				switch($this->field['type']) :
					case 'text' :								
					case 'website' :
						$this->read_only = $this->check_readability();
						$profile_fields_data .= $this->addTextField();
					break;				
					case 'username' :
						$profile_fields_data .= $this->addUsername();
					break;
					case 'password' :
						$profile_fields_data .= $this->addPassword();
					break;
					case 'email' :
						$this->read_only = $this->check_readability();
						$profile_fields_data .= $this->addEmail();
						if ( isset( $noPasswordField ) && $noPasswordField )
						{
							$profile_fields_data .= $this->addPassword( $noPasswordField );
						}
					break;
					case 'textarea':
						$this->read_only = $this->check_readability();
						$profile_fields_data .= $this->addTextArea();
					break;
					case 'dropdown':
					case 'multiselect':
						$this->read_only = $this->check_readability();
						$profile_fields_data .= $this->addDropdown();
					break;
					case 'number':
						$this->read_only = $this->check_readability();
						$profile_fields_data .= $this->addNumberField();
					break;
					case 'radio':
					case 'checkbox':
						$this->read_only = $this->check_readability();
						$profile_fields_data .= $this->addCheckRadio();
					break;
					case 'html':
						$this->read_only = $this->check_readability();
						$profile_fields_data .= $this->addHtml();
					break;
					case 'name':
						$this->read_only = $this->check_readability();
						$profile_fields_data .= $this->addName();
					break;
					case 'time':
						$this->read_only = $this->check_readability();
						$profile_fields_data .= $this->addTime();
					break;
					case 'upload':
						$this->read_only = $this->check_readability();
						$profile_fields_data .= $this->addUpload();
					break;
					case 'profile_pic':
						$this->read_only = $this->check_readability();
						$profile_fields_data .= $this->addProfilePic();
					break;
					case 'address':
						$this->read_only = $this->check_readability();
						$profile_fields_data .= $this->addAddress();
					break;
					//pie-register-woocommerce addon
					case 'wc_billing_address':
						if($this->woocommerce_and_piereg_wc_addon_active)
						{
							$profile_fields_data .= $this->addWooCommerceBillingAddress();
						}						
					break;
					case 'wc_shipping_address':
						if($this->woocommerce_and_piereg_wc_addon_active)
						{
							$profile_fields_data .= $this->addWooCommerceShippingAddress();
						}
					break;

					case 'phone':
						$this->read_only = $this->check_readability();
						$profile_fields_data .= $this->addPhone();
					break;
					/*
						*	Just For Two Way Login
					*/
					case 'two_way_login_phone':
						include_once( $this->admin_path . 'includes/plugin.php' );
						$twilio_option = get_option("pie_register_twilio");
		 				$plugin_status = get_option('piereg_api_manager_addon_Twilio_activated');
						if( is_plugin_active("pie-register-twilio/pie-register-twilio.php") && isset($twilio_option["enable_twilio"]) && $twilio_option["enable_twilio"] == 1 && $plugin_status == "Activated" ){
							$this->name = "piereg_two_way_login_phone";
							$this->slug = "piereg_two_way_login_phone";
							$this->read_only = $this->check_readability();
							$profile_fields_data .= $this->addPhone();
						}
					break;
					case 'date':
						$this->read_only = $this->check_readability();
						$profile_fields_data .= $this->addDate();
					break;
					case 'list':
						$this->read_only = $this->check_readability();
						$profile_fields_data .= $this->addList();
					break;			
					case 'default':
						$profile_fields_data .= $this->addDefaultField();
					break;
					case "sectionbreak":
						$profile_fields_data .= $this->addSectionBreak();
					break;
					case 'submit':
						$profile_fields_data .= $this->addSubmit();
					break;	
				endswitch;
				
				$profile_fields_data .= '</li>';
			}
		}
		
		$profile_fields_data .= '</ul>';
		return $profile_fields_data;	
	}
	
	function get_pr_widget_prefix(){
		if($this->is_pr_widget == true)
			return "widget_";
			
		return "";
	}

}