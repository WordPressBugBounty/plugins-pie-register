<?php
if( file_exists( dirname(__FILE__) . '/base.php') ) 
	require_once('base.php');
class Profile_front extends PieReg_Base
{
    var $field;
    var $user_id;
    var $slug;
    var $type;
    var $name;
    var $id;
    var $data;
	var $user;

    function __construct($user,$form_id = "default")
    {
        $this->data = $this->getCurrentFields($form_id);
		$this->user = $user;
		$this->user_id = $user->ID;
				
		parent::__construct();
    }

	function createFieldName($text)
    {
        return "pie_".$this->getMetaKey($text);
    }

    function createFieldID()
    {
        return "field_" . ((isset($this->field['id']))?$this->field['id']:"");
    }	

    function addLabel()
    {
        return '<label for="' . esc_attr($this->id) . '">' . esc_html__($this->field['label'],"pie-register") . '</label>';
    }   

	function print_user_profile($form_id = "default")
    {
		$data = "";
        if (is_array($this->data) && sizeof($this->data) > 0) 
		{
			$data .= '<div class="piereg_profile_cont">';
			$data .= '<span class="piereg-profile-logout-url"><a href="'.esc_url(wp_logout_url()).'">'.esc_html__("Logout","pie-register").'</a></span>';
			$option =  get_option(OPTION_PIE_REGISTER);
			$profile_page = $option['alternate_profilepage'];
			if($profile_page > 0){
				$data .= '<a class="piereg_edit_profile_link" href="' . esc_url( (add_query_arg( array("edit_user" => "1"), get_permalink($profile_page) )) ) . '"></a>';	

				$pie_pricing = get_user_meta($this->user_id,'pie_pricing',true);

				if ( ($pie_pricing == 'PaypalStandard') && is_plugin_active('pie-register-paypal-recurring/pie-register-paypal-recurring.php') )
				{
					if ( ( $this->checkAddonActivated( 'PayPal_Recurring' ) == true ) && !isset($_GET['view_payments']) )
					{
						$data .= '<span class="piereg-profile-view-payments"> | <a href="' . esc_url( (add_query_arg( array("view_payments" => "true"), get_permalink($profile_page) )) ) . '">'.esc_html__("View Subscription","pie-register").'</a></span>';
					}
					else 
					{
						$data .= '<span class="piereg-profile-view-profile"> | <a href="' . esc_url( get_permalink($profile_page) ) . '">'.esc_html__("View Profile","pie-register").'</a></span>';
					}
				}
				else if ( is_plugin_active('pie-register-stripe-recurring/pie-register-stripe-recurring.php') )
				{
					if ( ( $this->checkAddonActivated( 'Stripe_Recurring' ) == true ) && !isset($_GET['view_payments']) )
					{
						$data .= '<span class="piereg-profile-view-payments"> | <a href="' . esc_url( (add_query_arg( array("view_payments" => "true"), get_permalink($profile_page) )) ) . '">'.esc_html__("View Payments","pie-register").'</a></span>';
					}
					else 
					{
						$data .= '<span class="piereg-profile-view-profile"> | <a href="' . esc_url( get_permalink($profile_page) ) . '">'.esc_html__("View Profile","pie-register").'</a></span>';
					}
				}
			}
			if ( !isset($_GET['view_payments']) )
			{
				$data .= '<table border="0" cellpadding="0" cellspacing="0" class="pie_profile" id="pie_register">';
				
				if(is_array($this->data)){
					foreach ($this->data as $this->field)
					{
						$this->visibility_check = [true, true];
						if($this->piereg_field_visbility_addon_active){
							if(isset($this->field['show_on']) && !empty($this->field['show_on'])){
								$this->visibility_check = apply_filters('pie_addon_field_visibility_conditions',$this->visibility_check,$this->field);
							}
		
							if(!$this->visibility_check[0])
							{
								continue;
							}
						}

						if(isset($this->field['show_in_profile']) && $this->field['show_in_profile']=="0" && $this->visibility_check[1])
						{
							continue;
						}
						
						$this->slug = $this->createFieldName($this->field['type']."_".((isset($this->field['id']))?$this->field['id']:""));
						$this->type = $this->field['type'];
						$this->id   = $this->createFieldID();	
						if($this->type=="default")
							$this->slug   = $this->field['field_name'];
						/*
							*	Just Work 2Way Login Phone
						*/
						elseif($this->type == "two_way_login_phone")
								$this->slug = "piereg_two_way_login_phone";
						
						//When to add label
						switch($this->type) :				
							case 'password':
							case 'form':
								continue 2;
							break;
							case 'username' :
								$data .= '<tr><td class="fields fields2">'.wp_kses_post($this->addLabel());
								$data .= '</td><td class="fields"><span>'.esc_html($this->user->data->user_login).'</span></td></tr>';
							break;
							case 'email' :
								$data .= '<tr><td class="fields fields2">'.wp_kses_post($this->addLabel());
								$data .= '</td><td class="fields"><span>'.esc_html($this->user->data->user_email).'</span></td></tr>';
							break;
							case 'url':
								$data .= '<tr><td class="fields fields2">'.wp_kses_post($this->addLabel());
								$data .= '</td><td class="fields"><span>'.esc_url($this->getValue($this->type, $this->slug)).'</span></td></tr>';
							break;
							case 'name':
								$this->slug = "first_name";
								$data .= '<tr><td class="fields fields2"><label>'.esc_html($this->field['label'],"pie-register").'</label>';
								$data .= '</td><td class="fields"><span>'.esc_html($this->getValue()).'</span></td></tr>';
								if( !isset( $this->field['hide_last_name'] ) )
								{
									$this->slug = "last_name";
									$data .= '<tr><td class="fields fields2"><label>'.esc_html($this->field['label2'],"pie-register").'</label>';
									$data .= '</td><td class="fields"><span>'.esc_html($this->getValue()).'</span></td></tr>';
								}
							break;
							case 'profile_pic':
								$data .= '<tr><td class="fields fields2">'.wp_kses_post($this->addLabel());
								$imgPath = (trim($this->getValue($this->type, $this->slug)) != "")? $this->getValue($this->type, $this->slug) : plugins_url("assets/images/userImage.png",dirname(__FILE__));
								global $current_user;
								$imgPath = apply_filters("piereg_profile_image_url",$imgPath,$current_user);
								$data .= '</td><td class="fields"><span><img src="'.esc_url($imgPath).'" style="max-width:150px;" /></span></td></tr>';
							break;			
							case 'upload':
								$data .= '<tr><td class="fields fields2">'.wp_kses_post($this->addLabel());
								$upload_file_value = $this->getValue($this->type, $this->slug);
								$temp_dir = $this->piereg_get_uploaded_dir($this->user_id, $this->slug);
								if(!file_exists($temp_dir."/".basename($upload_file_value))){
									$upload_file_value = '';
								}
								$data .= '</td><td class="fields"><a class="uploaded_file" href="'.$upload_file_value.'" target="_blank">'.basename($upload_file_value).'</a></td></tr>';
							break;						
							case 'address':
								$data .= '<tr><td class="fields fields2" style="vertical-align:top;">'.wp_kses_post($this->addLabel());
								$data .= '</td><td class="fields"><span>'.wp_kses_post($this->getValue($this->type, $this->slug)).'</span></td></tr>';
							break;
							//pie-register-WooCommerce Addon
							case 'wc_billing_address':

								if(isset($this->field['show_in_profile']) && $this->field['show_in_profile']=="1")
								{
									if ($this->woocommerce_and_piereg_wc_addon_active)
									{
										$label = "";
										
										if ($this->field['label']) 
										{
											$label = wp_kses_post($this->addLabel());	
										} 
										else 
										{
											$label = '<label for="' . esc_attr($this->id) . '">' . esc_html__("Billing Address","pie-register") . '</label>';
										}
										
										$wc_billing_first_name 		= get_user_meta($this->user_id, "billing_first_name", true);
										$wc_billing_last_name 		= get_user_meta($this->user_id, "billing_last_name", true);
										$wc_billing_company 		= get_user_meta($this->user_id, "billing_company", true);
										$wc_billing_address_1 		= get_user_meta($this->user_id, "billing_address_1", true);
										$wc_billing_address_2 		= get_user_meta($this->user_id, "billing_address_2", true);
										$wc_billing_city 			= get_user_meta($this->user_id, "billing_city", true);
										$wc_billing_postcode 		= get_user_meta($this->user_id, "billing_postcode", true);
										$wc_billing_country 		= get_user_meta($this->user_id, "billing_country", true);
										$wc_billing_state 			= get_user_meta($this->user_id, "billing_state", true);
										$wc_billing_email 			= get_user_meta($this->user_id, "billing_email", true);
										$wc_billing_phone 			= get_user_meta($this->user_id, "billing_phone", true);

										if (!empty($wc_billing_country))
										{
											global $woocommerce;					
											$countries_obj   	= new WC_Countries();
											$states 			= $countries_obj->get_states($wc_billing_country);
											$wc_billing_country = $countries_obj->countries[$wc_billing_country];
											if ($states) {
												if ( array_key_exists($wc_billing_state, $states) )
												{
													$wc_billing_state	= $states[$wc_billing_state];
												}
												else 
												{
													$wc_billing_state	= "";
												}
											}
										}

										$data .= '<tr><td class="fields fields2" style="vertical-align:top;">'.wp_kses_post($label).'</td>';
										$data .= '<td class="fields"><span>';

											$data .= "<table class='woo_fields_profile' border='0' cellpadding='0' cellspacing='0'>";
												$data .= "<tr>";
													$data .= "<td style='width: 50%;'>First Name: </td><td>".esc_html($wc_billing_first_name)."</td>";
												$data .= "</tr>";
												$data .= "<tr>";
													$data .= "<td style='width: 50%;'>Last Name: </td><td>".esc_html($wc_billing_last_name)."</td>";
												$data .= "</tr>";
												$data .= "<tr>";
													$data .= "<td style='width: 50%;'>Company: </td><td>".esc_html($wc_billing_company)."</td>";
												$data .= "</tr>";
												$data .= "<tr>";
													$data .= "<td style='width: 50%;'>Address Line 1: </td><td>".esc_html($wc_billing_address_1)."</td>";
												$data .= "</tr>";
												$data .= "<tr>";
													$data .= "<td style='width: 50%;'>Address Line 2: </td><td>".esc_html($wc_billing_address_2)."</td>";
												$data .= "</tr>";
												$data .= "<tr>";
													$data .= "<td style='width: 50%;'>City: </td><td>".esc_html($wc_billing_city)."</td>";
												$data .= "</tr>";
												$data .= "<tr>";
													$data .= "<td style='width: 50%;'>Zipcode: </td><td>".esc_html($wc_billing_postcode)."</td>";
												$data .= "</tr>";
												$data .= "<tr>";
													$data .= "<td style='width: 50%;'>Country: </td><td>".esc_html($wc_billing_country)."</td>";
												$data .= "</tr>";
												$data .= "<tr>";
													$data .= "<td style='width: 50%;'>State: </td><td>".esc_html($wc_billing_state)."</td>";
												$data .= "</tr>";
												$data .= "<tr>";
													$data .= "<td style='width: 50%;'>Email Address: </td><td>".esc_html($wc_billing_email)."</td>";
												$data .= "</tr>";
												$data .= "<tr>";
													$data .= "<td style='width: 50%;'>Phone: </td><td>".esc_html($wc_billing_phone)."</td>";
												$data .= "</tr>";
											$data .= "</table>";

										$data .= '</span></td></tr>';
									}
								}

								break;
							case 'wc_shipping_address':
								
								if(isset($this->field['show_in_profile']) && $this->field['show_in_profile']=="1")
								{
									if ($this->woocommerce_and_piereg_wc_addon_active)
									{
										$label = "";
										
										if ($this->field['label']) 
										{
											$label = wp_kses_post($this->addLabel());	
										} 
										else 
										{
											$label = '<label for="' . esc_attr($this->id) . '">' . esc_html__("Shipping Address","pie-register") . '</label>';
										}
										
										$wc_shipping_first_name 	= get_user_meta($this->user_id, "shipping_first_name", true);
										$wc_shipping_last_name 		= get_user_meta($this->user_id, "shipping_last_name", true);
										$wc_shipping_company 		= get_user_meta($this->user_id, "shipping_company", true);
										$wc_shipping_address_1 		= get_user_meta($this->user_id, "shipping_address_1", true);
										$wc_shipping_address_2 		= get_user_meta($this->user_id, "shipping_address_2", true);
										$wc_shipping_city 			= get_user_meta($this->user_id, "shipping_city", true);
										$wc_shipping_postcode 		= get_user_meta($this->user_id, "shipping_postcode", true);
										$wc_shipping_country 		= get_user_meta($this->user_id, "shipping_country", true);
										$wc_shipping_state 			= get_user_meta($this->user_id, "shipping_state", true);
										$wc_shipping_email 			= get_user_meta($this->user_id, "shipping_email", true);
										$wc_shipping_phone 			= get_user_meta($this->user_id, "shipping_phone", true);

										if (!empty($wc_shipping_country))
										{
											global $woocommerce;					
											$countries_obj   		= new WC_Countries();
											$states 				= $countries_obj->get_states($wc_shipping_country);
											$wc_shipping_country 	= $countries_obj->countries[$wc_shipping_country];
											if ($states) {
												if ( array_key_exists($wc_shipping_state, $states) )
												{
													$wc_shipping_state	= $states[$wc_shipping_state];
												}
												else
												{
													$wc_shipping_state	= "";
												}
											}
										}

										$data .= '<tr><td class="fields fields2" style="vertical-align:top;">'.wp_kses_post($label).'</td>';
										$data .= '<td class="fields"><span>';

											$data .= "<table class='woo_fields_profile' id='woo_profile' border='0' cellpadding='0' cellspacing='0'>";
												$data .= "<tr>";
													$data .= "<td style='width: 50%;'>First Name: </td><td>".esc_html($wc_shipping_first_name)."</td>";
												$data .= "</tr>";
												$data .= "<tr>";
													$data .= "<td style='width: 50%;'>Last Name: </td><td>".esc_html($wc_shipping_last_name)."</td>";
												$data .= "</tr>";
												$data .= "<tr>";
													$data .= "<td style='width: 50%;'>Company: </td><td>".esc_html($wc_shipping_company)."</td>";
												$data .= "</tr>";
												$data .= "<tr>";
													$data .= "<td style='width: 50%;'>Address Line 1: </td><td>".esc_html($wc_shipping_address_1)."</td>";
												$data .= "</tr>";
												$data .= "<tr>";
													$data .= "<td style='width: 50%;'>Address Line 2: </td><td>".esc_html($wc_shipping_address_2)."</td>";
												$data .= "</tr>";
												$data .= "<tr>";
													$data .= "<td style='width: 50%;'>City: </td><td>".esc_html($wc_shipping_city)."</td>";
												$data .= "</tr>";
												$data .= "<tr>";
													$data .= "<td style='width: 50%;'>Zipcode: </td><td>".esc_html($wc_shipping_postcode)."</td>";
												$data .= "</tr>";
												$data .= "<tr>";
													$data .= "<td style='width: 50%;'>Country: </td><td>".esc_html($wc_shipping_country)."</td>";
												$data .= "</tr>";
												$data .= "<tr>";
													$data .= "<td style='width: 50%;'>State: </td><td>".esc_html($wc_shipping_state)."</td>";
												$data .= "</tr>";
												$data .= "<tr>";
													$data .= "<td style='width: 50%;'>Email Address: </td><td>".esc_html($wc_shipping_email)."</td>";
												$data .= "</tr>";
												$data .= "<tr>";
													$data .= "<td style='width: 50%;'>Phone: </td><td>".esc_html($wc_shipping_phone)."</td>";
												$data .= "</tr>";
											$data .= "</table>";

										$data .= '</span></td></tr>';
									}
								}
								
							break;

							case 'two_way_login_phone':
								include_once( $this->admin_path . 'includes/plugin.php' );
								$twilio_option = get_option("pie_register_twilio");
								$plugin_status = get_option('piereg_api_manager_addon_Twilio_activated');
								if( is_plugin_active("pie-register-twilio/pie-register-twilio.php") && isset($twilio_option["enable_twilio"]) && $twilio_option["enable_twilio"] == 1 && $plugin_status == "Activated" ){
									$data .= '<tr><td class="fields fields2">'.wp_kses_post($this->addLabel());
									$data .= '</td><td class="fields"><span>'.esc_html($this->getValue($this->type, $this->slug)).'</span></td></tr>';
								}
							break;
							case 'text' :
							case 'textarea':
							case 'dropdown':
							case 'multiselect':
							case 'number':
							case 'radio':
							case 'checkbox':
							case 'custom_role':
							case 'time':
							case 'phone':
							case 'date':
							case 'list':						
							case 'invitation':
							case "default":
								$data .= '<tr><td class="fields fields2">'.wp_kses_post($this->addLabel());
								$data .= '</td><td class="fields"><span>'.wp_kses_post($this->getValue()).'</span></td></tr>';
							break;
							case 'sectionbreak':
								$class = "sectionBreak";
								
								$data  .= '<tr class="section-profile">';
								if($this->field['label'] != ''){
									$class .= ' break-label';
								}
								$data .= '<td class="fields fields2 '.esc_attr($class).'">';
									$data .= wp_kses_post($this->addLabel());
								$data .= '</td>';
								$data .= '<td class="fields">';
								$data .= '</td>';
								$data .= '</tr>';
							break;
							case 'terms':
								$data .= '<tr><td class="fields fields2">'.wp_kses_post($this->addLabel());
								$data .= '</td><td class="fields"><span class="pr-profile-terms">';
								$data .= '<input disabled'. checked($this->getValue() == '1', true, false) .' type="checkbox" value="1" >';
								$page_id	= $this->field['cont'];
								$page_url	= get_the_permalink($page_id);		

								// translators: %s is replaced by the URL link to view
								$data .= apply_filters('piereg_terms_field_text',sprintf(__('Click <a target="_blank" href="%s">here</a> to view.','pie-register'),$page_url));
								$data .= '</span></td></tr>';
			
							break;
						endswitch;
					}
				}
				$data = apply_filters('piereg_filter_profile_front_table',$data);
				$data .= '</table>';
			}
			else
			{
				$pie_pricing = get_user_meta($this->user_id,'pie_pricing',true);

				if ( $pie_pricing == 'PaypalStandard' )
				{
					$payment_grid = apply_filters('piereg_display_recurring_paypal_payments_grid',"");
					$data .= $payment_grid; 
				}
				else
				{
					$payment_grid = apply_filters('piereg_display_recurring_stripe_payments_grid',"");
					$data .= $payment_grid; 
				}
			}
           $data .= '</div>';
        }
		return $data;
    }
	
}