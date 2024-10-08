var currHTML, endHtml, dragType, fieldMeta;

var no = 3;
var piereg = jQuery.noConflict();


function getStyle(type) {

    var meta = "";
    var label_val = "";

    if (type == "text") {
        label_val = "Text Field";
    } else if (type == "textarea") {
        label_val = "Text Area";
    } else if (type == "dropdown") {
        label_val = "Drop Down";
    } else if (type == "multiselect") {
        label_val = "Multi Select";
    } else if (type == "number") {
        label_val = "Number";
    } else if (type == "checkbox") {
        label_val = "Checkbox";
    } else if (type == "radio") {
        label_val = "Radio Button";
    } else if (type == "hidden") {
        label_val = "Hidden Field";
    } else if (type == "html") {
        label_val = "HTML Script";
    } else if (type == "sectionbreak") {
        label_val = "Section Break";
    } else if (type == "pagebreak") {
        label_val = "Page Break";
    } else if (type == "address") {
        label_val = "Address";
    } //pie-register wooCommerce
	  else if ( type == "wc_billing_address" ) {
		label_val = "Billing Address";
	} else if ( type == "wc_shipping_address" ) {
		label_val = "Shipping Address";
	} else if (type == "date") {
        label_val = "Date";
    } else if (type == "time") {
        label_val = "Time";
    } else if (type == "phone") {
        label_val = "Phone";
    } else if (type == "list") {
        label_val = "List";
    } // dropdown_ur
      else if (type == "custom_role") {
        label_val = "Custom User Role";
    } else if (type == "invitation") {
        label_val = "Invitation Code";
    } else if (type == "terms") {
        label_val = "Terms and Conditions";
    } else if (type == "captcha") {
        label_val = "Re-Captcha";
    }
    /*
    if( label_val == "" ) {
    	label_val = type;	
    }
    */

    var html = piereg('<div id="holder_' + no + '"/>').addClass("fields_options fields_optionsbg").html('<a href="javascript:;" title="' + piereg_validation_engn['edit'] + '" class="edit_btn"></a><div class="label_position"  id="field_label_' + no + '"><label>' + label_val + ' </label></div><a rel="' + no + '" href="javascript:;" class="delete_btn" title="' + piereg_validation_engn['delete'] + '">X</a><input type="hidden" name="field[' + no + '][id]" value="' + no + '" id = "id_' + no + '"><div id="field_position_' + no + '" class="fields_position"></div>');

    if (type == "text") {
        html.find("#field_position_" + no).html('<input type="text" disabled="disabled"  id="field_' + no + '" class="input_fields">');
    } else if (type == "textarea") {

        html.find("#field_position_" + no).html('<textarea disabled="disabled" id="field_' + no + '" rows="5" style="width:100%;"> </textarea>');

    } else if (type == "dropdown") {

        html.find("#field_position_" + no).html('<select disabled="disabled" id="field_' + no + '"><option></option></select>');

    } else if (type == "multiselect") {

        html.find("#field_position_" + no).html('<select disabled="disabled" id="field_' + no + '" multiple="multiple"><option></option></select>');

    } else if (type == "number") {

        html.find("#field_position_" + no).html('<input disabled="disabled" id="field_' + no + '" type="number" class="input_fields" />');

    } else if (type == "checkbox") {

        html.find("#field_position_" + no).html('<div class="radio_wrap"><div class="wrapcheckboxes"><label>My Label</label><input disabled="disabled" id="field_' + no + '" type="checkbox" class="radio_fields" /></div><div class="wrapcheckboxes"><label>My Label</label><input disabled="disabled" id="field_' + no + '" type="checkbox" class="radio_fields" /></div></div>');

    } else if (type == "hidden") {

        html.find("#field_position_" + no).html('<input type="text" disabled="disabled"  id="field_' + no + '" class="input_fields">');

    } else if (type == "radio") {

        html.find("#field_position_" + no).html('<div class="radio_wrap"><div class="wrapcheckboxes"><label>My Label</label><input disabled="disabled" id="field_' + no + '" type="radio" class="radio_fields" /></div><div class="wrapcheckboxes"><label>My Label</label><input disabled="disabled" id="field_' + no + '" type="radio" class="radio_fields" /></div></div>');

    } else if (type == "html") {

        html.find("#field_position_" + no).html('<div id="field_' + no + '" class="htmldiv">');

    } else if (type == "sectionbreak") {

        html.find("#field_position_" + no).html('<div style="width:100%;float:left;border: 1px solid #aaaaaa;margin-top:25px;"></div>');

    } else if (type == "two_way_login_phone") {
        html.find("#field_label_" + no + " label").html("2Way Login Phone #");
        html.find("#field_position_" + no).html('<input type="text" disabled="disabled"  id="field_' + no + '" class="input_fields">');
    } else if (type == "username") {
        html.find("#field_label_" + no + " label").html("Username");
        html.find("#field_position_" + no).html('<input type="text" disabled="disabled"  id="field_' + no + '" class="input_fields">');
    } else if (type == "password") {
        html.find("#field_label_" + no + " label").html("Password");

        html.find("#field_position_" + no).html('<input disabled="disabled" type="text" class="input_fields">');

        html.find("#field_position_" + no).after('<div id="confirm_password_field" class=" confirm_password_field fields_position"> <input disabled="disabled" type="text" class="input_fields"></div>');

        html.find("#field_position_" + no).after('<div class="confirm_password_field_label label_position"><label>Confirm Password</label></div>');
    }

    if (type == "pagebreak") {

        html.find("#field_position_" + no).html('<img src="' + piereg_wp_pie_register_url + 'assets/images/pagebreak.png" style="max-width:100%;" />');

    } else if (type == "time") {

        html.find("#field_position_" + no).html('<div class="time"><div class="time_fields"><input disabled="disabled" type="text" class="input_fields"><label>HH</label></div><span class="colon">:</span><div class="time_fields"><input disabled="disabled" type="text" class="input_fields"><label>MM</label></div><div id="time_format_field_' + no + '" class="time_fields"><select disabled="disabled"><option>AM</option><option>PM</option></select></div></div>');

    } else if (type == "website") {

        html.find("#field_position_" + no).html('<input disabled="disabled" type="text" id="field_' + no + '" class="input_fields">');

    } else if (type == "name") {



        html.find("#field_label_" + no + " label").html("First Name");

        html.find("#field_position_" + no).html('<input disabled="disabled" type="text" class="input_fields">');

        html.find("#field_position_" + no).after('<div id="last_name_field" class="fields_position last_name_field"> <input disabled="disabled" type="text" class="input_fields"></div>');

        html.find("#field_position_" + no).after('<div class="label_position last_name_field_label"><label>Last Name</label></div>');

        html.find("#field_position_" + no).after('<input type="hidden" name="field[' + no + '][label]" id="label_' + no + '" value="First Name">');

    } else if (type == "captcha") {
        html.find("#field_label_" + no + " label").html("Re-Captcha");
        html.find("#field_position_" + no).html('<img id="captcha_img" src="' + piereg_wp_pie_register_url + 'assets/images/new-recatpcha.png" data-captcha-img-src="' + piereg_wp_pie_register_url + 'assets/images/" /><p><strong>Note: </strong>Please ensure that Re-captcha keys are entered on the Settings page</p>');

    } else if (type == "math_captcha") {
        html.find("#field_label_" + no + " label").html("Math Captcha");
        html.find("#field_position_" + no).html('<img id="captcha_img" src="' + piereg_wp_pie_register_url + 'assets/images/math_catpcha.png" />');

    } else if (type == "upload" || type == "profile_pic") {
        if (type == "profile_pic") {
            html.find("#field_label_" + no + " label").html("Profile Picture");
        } else if (type == "upload") {
            html.find("#field_label_" + no + " label").html("Upload File");
        }

        html.find("#field_position_" + no).html('<input disabled="disabled" type="file" id="field_' + no + '" class="input_fields">');

    } else if (type == "address") {

        html.find("#field_position_" + no).html('<div id="address_fields" class="address">  <input type="text" class="input_fields">  <label>Street Address</label></div><div class="address" id="address_address2_' + no + '">  <input type="text" class="input_fields">  <label>Address Line 2</label></div><div class="address">  <div class="address2"><input type="text" class="input_fields"><label>City</label>  </div>  <div  class="address2 state_div_' + no + '" id="state_' + no + '"><input type="text" class="input_fields"><label>State / Province / Region</label>  </div>  <div  class="address2 state_div_' + no + '" id="state_us_' + no + '" style="display:none;"><select id="state_us_field_' + no + '"><option value="" selected="selected"></option><option value="Alabama">Alabama</option><option value="Alaska">Alaska</option><option value="Arizona">Arizona</option><option value="Arkansas">Arkansas</option><option value="California">California</option><option value="Colorado">Colorado</option><option value="Connecticut">Connecticut</option><option value="Delaware">Delaware</option><option value="District of Columbia">District of Columbia</option><option value="Florida">Florida</option><option value="Georgia">Georgia</option><option value="Hawaii">Hawaii</option><option value="Idaho">Idaho</option><option value="Illinois">Illinois</option><option value="Indiana">Indiana</option><option value="Iowa">Iowa</option><option value="Kansas">Kansas</option><option value="Kentucky">Kentucky</option><option value="Louisiana">Louisiana</option><option value="Maine">Maine</option><option value="Maryland">Maryland</option><option value="Massachusetts">Massachusetts</option><option value="Michigan">Michigan</option><option value="Minnesota">Minnesota</option><option value="Mississippi">Mississippi</option><option value="Missouri">Missouri</option><option value="Montana">Montana</option><option value="Nebraska">Nebraska</option><option value="Nevada">Nevada</option><option value="New Hampshire">New Hampshire</option><option value="New Jersey">New Jersey</option><option value="New Mexico">New Mexico</option><option value="New York">New York</option><option value="North Carolina">North Carolina</option><option value="North Dakota">North Dakota</option><option value="Ohio">Ohio</option><option value="Oklahoma">Oklahoma</option><option value="Oregon">Oregon</option><option value="Pennsylvania">Pennsylvania</option><option value="Rhode Island">Rhode Island</option><option value="South Carolina">South Carolina</option><option value="South Dakota">South Dakota</option><option value="Tennessee">Tennessee</option><option value="Texas">Texas</option><option value="Utah">Utah</option><option value="Vermont">Vermont</option><option value="Virginia">Virginia</option><option value="Washington">Washington</option><option value="West Virginia">West Virginia</option><option value="Wisconsin">Wisconsin</option><option value="Wyoming">Wyoming</option><option value="Armed Forces Americas">Armed Forces Americas</option><option value="Armed Forces Europe">Armed Forces Europe</option><option value="Armed Forces Pacific">Armed Forces Pacific</option></select><label>State</label>  </div>  <div class="address2 state_div_' + no + '" id="state_canada_' + no + '" style="display:none;"><select id="state_canada_field_' + no + '"><option value="" selected="selected"></option><option value="Alberta">Alberta</option><option value="British Columbia">British Columbia</option><option value="Manitoba">Manitoba</option><option value="New Brunswick">New Brunswick</option><option value="Newfoundland &amp; Labrador">Newfoundland &amp; Labrador</option><option value="Northwest Territories">Northwest Territories</option><option value="Nova Scotia">Nova Scotia</option><option value="Nunavut">Nunavut</option><option value="Ontario">Ontario</option><option value="Prince Edward Island">Prince Edward Island</option><option value="Quebec">Quebec</option><option value="Saskatchewan">Saskatchewan</option><option value="Yukon">Yukon</option></select></select><label>Province</label>  </div></div><div class="address">  <div class="address2"><input type="text" class="input_fields"><label>Zip / Postal Code</label>  </div>  <div id="address_country_' + no + '" class="address2"><select disabled="disabled"><option></option></select><label>Country</label></div></div>');

    }
    //pie-register WooCommerce 
    else if (type == "wc_billing_address") {

		var wc_billing_address_html = '';

		var hide_btn_wc_billing_first_name 		= '<small>(<a href="javascript:void(0);" onclick="hideWooCommerceField(\'div_wc_billing_first_name\', this, ' + no + ');">hide</a>)</small>';
		var hide_btn_wc_billing_last_name 		= '<small>(<a href="javascript:void(0);" onclick="hideWooCommerceField(\'div_wc_billing_last_name\', this, ' + no + ');">hide</a>)</small>';
		var hide_btn_wc_billing_company 		= '<small>(<a href="javascript:void(0);" onclick="hideWooCommerceField(\'div_wc_billing_company\', this, ' + no + ');">hide</a>)</small>';
		var hide_btn_wc_billing_address_line_1 	= '<small>(<a href="javascript:void(0);" onclick="hideWooCommerceField(\'div_wc_billing_address_line_1\', this, ' + no + ');">hide</a>)</small>';
		var hide_btn_wc_billing_address_line_2 	= '<small>(<a href="javascript:void(0);" onclick="hideWooCommerceField(\'div_wc_billing_address_line_2\', this, ' + no + ');">hide</a>)</small>';
		var hide_btn_wc_billing_city 			= '<small>(<a href="javascript:void(0);" onclick="hideWooCommerceField(\'div_wc_billing_city\', this, ' + no + ');">hide</a>)</small>';
		var hide_btn_wc_billing_zipcode 		= '<small>(<a href="javascript:void(0);" onclick="hideWooCommerceField(\'div_wc_billing_zipcode\', this, ' + no + ');">hide</a>)</small>';
		var hide_btn_wc_billing_country 		= '<small>(<a href="javascript:void(0);" onclick="hideWooCommerceField(\'div_wc_billing_country\', this, ' + no + ');">hide</a>)</small>';
		var hide_btn_wc_billing_state_int 		= '<small>(<a href="javascript:void(0);" onclick="hideWooCommerceField(\'div_wc_billing_state_int\', this, ' + no + ');">hide</a>)</small>';
		var hide_btn_wc_billing_state_usa 		= '<small>(<a href="javascript:void(0);" onclick="hideWooCommerceField(\'div_wc_billing_state_usa\', this, ' + no + ');">hide</a>)</small>';
		var hide_btn_wc_billing_state_can 		= '<small>(<a href="javascript:void(0);" onclick="hideWooCommerceField(\'div_wc_billing_state_can\', this, ' + no + ');">hide</a>)</small>';
		var hide_btn_wc_billing_emailaddress 	= '<small>(<a href="javascript:void(0);" onclick="hideWooCommerceField(\'div_wc_billing_emailaddress\', this, ' + no + ');">hide</a>)</small>';
		var hide_btn_wc_billing_phone 			= '<small>(<a href="javascript:void(0);" onclick="hideWooCommerceField(\'div_wc_billing_phone\', this, ' + no + ');">hide</a>)</small>';

		var req_chk_wc_billing_first_name 		= '<input type="checkbox" onclick="requireWooCommerceField(\'first_name\', this, ' + no + ');" /> required';
		var req_chk_wc_billing_last_name 		= '<input type="checkbox" onclick="requireWooCommerceField(\'last_name\', this, ' + no + ');" /> required';
		var req_chk_wc_billing_company 			= '<input type="checkbox" onclick="requireWooCommerceField(\'company\', this, ' + no + ');" /> required';
		var req_chk_wc_billing_address_line_1 	= '<input type="checkbox" onclick="requireWooCommerceField(\'address_line_1\', this, ' + no + ');" /> required';
		var req_chk_wc_billing_address_line_2 	= '<input type="checkbox" onclick="requireWooCommerceField(\'address_line_2\', this, ' + no + ');" /> required';
		var req_chk_wc_billing_city 			= '<input type="checkbox" onclick="requireWooCommerceField(\'city\', this, ' + no + ');" /> required';
		var req_chk_wc_billing_zipcode 			= '<input type="checkbox" onclick="requireWooCommerceField(\'zipcode\', this, ' + no + ');" /> required';
		var req_chk_wc_billing_country 			= '<input type="checkbox" onclick="requireWooCommerceField(\'country\', this, ' + no + ');" /> required';
		var req_chk_wc_billing_state_int 		= '<input type="checkbox" onclick="requireWooCommerceField(\'state_int\', this, ' + no + ');" /> required';
		var req_chk_wc_billing_state_usa 		= '<input type="checkbox" onclick="requireWooCommerceField(\'state_usa\', this, ' + no + ');" /> required';
		var req_chk_wc_billing_state_can 		= '<input type="checkbox" onclick="requireWooCommerceField(\'state_can\', this, ' + no + ');" /> required';
		var req_chk_wc_billing_emailaddress 	= '<input type="checkbox" onclick="requireWooCommerceField(\'emailaddress\', this, ' + no + ');" /> required';
		var req_chk_wc_billing_phone 			= '<input type="checkbox" onclick="requireWooCommerceField(\'phone\', this, ' + no + ');" /> required';

		wc_billing_address_html += '<div class="address">';
			
			// first name field
			wc_billing_address_html += '<div class="address2 div_wc_billing_first_name">';
                wc_billing_address_html += '<label>First Name </label>';
				wc_billing_address_html += '<input disabled="disabled"  type="text" class="input_fields">';
                wc_billing_address_html += req_chk_wc_billing_first_name;
				wc_billing_address_html += hide_btn_wc_billing_first_name;
			wc_billing_address_html += '</div>';
			
			// last name field
			wc_billing_address_html += '<div class="address2 div_wc_billing_last_name">';
                wc_billing_address_html += '<label>Last Name </label>';
				wc_billing_address_html += '<input disabled="disabled"  type="text" class="input_fields">';
                wc_billing_address_html += req_chk_wc_billing_last_name;
				wc_billing_address_html += hide_btn_wc_billing_last_name;
			wc_billing_address_html += '</div>';

		wc_billing_address_html += '</div>';

		// company field
		wc_billing_address_html += '<div id="address_fields" class="address div_wc_billing_company">';
            wc_billing_address_html += '<label>Company </label>';	
            wc_billing_address_html += '<input disabled="disabled"  type="text" class="input_fields">';
            wc_billing_address_html += req_chk_wc_billing_company;
			wc_billing_address_html += hide_btn_wc_billing_company;
		wc_billing_address_html += '</div>';

		// address line 1 field
		wc_billing_address_html += '<div id="address_fields" class="address div_wc_billing_address_line_1">';
            wc_billing_address_html += '<label>Address Line 1 </label>';
			wc_billing_address_html += '<input disabled="disabled"  type="text" class="input_fields">';
            wc_billing_address_html += req_chk_wc_billing_address_line_1;
			wc_billing_address_html += hide_btn_wc_billing_address_line_1;

		wc_billing_address_html += '</div>';

		// address line 2 field
		wc_billing_address_html += '<div class="address div_wc_billing_address_line_2" id="address_address2_' + no + '">';
            wc_billing_address_html += '<label>Address Line 2 </label>';	
            wc_billing_address_html += '<input disabled="disabled"  type="text" class="input_fields">';
            wc_billing_address_html += req_chk_wc_billing_address_line_2;
			wc_billing_address_html += hide_btn_wc_billing_address_line_2;
		wc_billing_address_html += '</div>';

		wc_billing_address_html += '<div class="address">';
			
			// city field
			wc_billing_address_html += '<div class="address2 div_wc_billing_city">';
                wc_billing_address_html += '<label>City </label>';
				wc_billing_address_html += '<input disabled="disabled"  type="text" class="input_fields">';
				wc_billing_address_html += req_chk_wc_billing_city;
                wc_billing_address_html += hide_btn_wc_billing_city;
			wc_billing_address_html += '</div>';
			
			// zip code field
			wc_billing_address_html += '<div class="address2 div_wc_billing_zipcode">';
                wc_billing_address_html += '<label>Zip / Postal Code </label>';
				wc_billing_address_html += '<input disabled="disabled"  type="text" class="input_fields">';
                wc_billing_address_html += req_chk_wc_billing_zipcode;
				wc_billing_address_html += hide_btn_wc_billing_zipcode;
			wc_billing_address_html += '</div>';
		
		wc_billing_address_html += '</div>';
		
		wc_billing_address_html += '<div class="address">';
			
			// country field
			wc_billing_address_html += '<div id="address_country_' + no + '" class="address2 div_wc_billing_country">';
                wc_billing_address_html += '<label>Country </label>';
				wc_billing_address_html += '<select disabled="disabled">';
					wc_billing_address_html += '<option></option>';
				wc_billing_address_html += '</select>';
                wc_billing_address_html += req_chk_wc_billing_country;
				wc_billing_address_html += hide_btn_wc_billing_country;
			wc_billing_address_html += '</div>';

			// state / province / region field -> International
			wc_billing_address_html += '<div  class="address2 state_div_' + no + ' div_wc_billing_state_usa" id="state_' + no + '" >';
                wc_billing_address_html += '<label>State / Province / Region </label>';
				wc_billing_address_html += '<select disabled="disabled"  id="state_us_field_' + no + '">';
					wc_billing_address_html += '<option value="" selected="selected"></option><option value="Alabama">Alabama</option><option value="Alaska">Alaska</option><option value="Arizona">Arizona</option><option value="Arkansas">Arkansas</option><option value="California">California</option><option value="Colorado">Colorado</option><option value="Connecticut">Connecticut</option><option value="Delaware">Delaware</option><option value="District of Columbia">District of Columbia</option><option value="Florida">Florida</option><option value="Georgia">Georgia</option><option value="Hawaii">Hawaii</option><option value="Idaho">Idaho</option><option value="Illinois">Illinois</option><option value="Indiana">Indiana</option><option value="Iowa">Iowa</option><option value="Kansas">Kansas</option><option value="Kentucky">Kentucky</option><option value="Louisiana">Louisiana</option><option value="Maine">Maine</option><option value="Maryland">Maryland</option><option value="Massachusetts">Massachusetts</option><option value="Michigan">Michigan</option><option value="Minnesota">Minnesota</option><option value="Mississippi">Mississippi</option><option value="Missouri">Missouri</option><option value="Montana">Montana</option><option value="Nebraska">Nebraska</option><option value="Nevada">Nevada</option><option value="New Hampshire">New Hampshire</option><option value="New Jersey">New Jersey</option><option value="New Mexico">New Mexico</option><option value="New York">New York</option><option value="North Carolina">North Carolina</option><option value="North Dakota">North Dakota</option><option value="Ohio">Ohio</option><option value="Oklahoma">Oklahoma</option><option value="Oregon">Oregon</option><option value="Pennsylvania">Pennsylvania</option><option value="Rhode Island">Rhode Island</option><option value="South Carolina">South Carolina</option><option value="South Dakota">South Dakota</option><option value="Tennessee">Tennessee</option><option value="Texas">Texas</option><option value="Utah">Utah</option><option value="Vermont">Vermont</option><option value="Virginia">Virginia</option><option value="Washington">Washington</option><option value="West Virginia">West Virginia</option><option value="Wisconsin">Wisconsin</option><option value="Wyoming">Wyoming</option><option value="Armed Forces Americas">Armed Forces Americas</option><option value="Armed Forces Europe">Armed Forces Europe</option><option value="Armed Forces Pacific">Armed Forces Pacific</option>';
				wc_billing_address_html += '</select>';
                wc_billing_address_html += req_chk_wc_billing_state_int;
				wc_billing_address_html += hide_btn_wc_billing_state_int;
			wc_billing_address_html += '</div>';

		wc_billing_address_html += '</div>';
		
		wc_billing_address_html += '<div class="address">';
			
			// email address field
			wc_billing_address_html += '<div class="address2 div_wc_billing_emailaddress">';
                wc_billing_address_html += '<label>Email Address </label>';
				wc_billing_address_html += '<input disabled="disabled"  type="text" class="input_fields">';
                wc_billing_address_html += req_chk_wc_billing_emailaddress;
				wc_billing_address_html += hide_btn_wc_billing_emailaddress;
			wc_billing_address_html += '</div>';
			
			// phone field
			wc_billing_address_html += '<div class="address2 div_wc_billing_phone">';
                wc_billing_address_html += '<label>Phone </label>';
				wc_billing_address_html += '<input disabled="disabled"  type="text" class="input_fields">';
                wc_billing_address_html += req_chk_wc_billing_phone;
				wc_billing_address_html += hide_btn_wc_billing_phone;
			wc_billing_address_html += '</div>';

		wc_billing_address_html += '</div>';

		html.find("#field_position_" + no).html(wc_billing_address_html);

	} else if (type == "wc_shipping_address") {

		var wc_shipping_address_html = '';

		var hide_btn_wc_shipping_first_name 	= '<small>(<a href="javascript:void(0);" onclick="hideWooCommerceField(\'div_wc_shipping_first_name\', this, ' + no + ');">hide</a>)</small>';
		var hide_btn_wc_shipping_last_name 		= '<small>(<a href="javascript:void(0);" onclick="hideWooCommerceField(\'div_wc_shipping_last_name\', this, ' + no + ');">hide</a>)</small>';
		var hide_btn_wc_shipping_company 		= '<small>(<a href="javascript:void(0);" onclick="hideWooCommerceField(\'div_wc_shipping_company\', this, ' + no + ');">hide</a>)</small>';
		var hide_btn_wc_shipping_address_line_1 = '<small>(<a href="javascript:void(0);" onclick="hideWooCommerceField(\'div_wc_shipping_address_line_1\', this, ' + no + ');">hide</a>)</small>';
		var hide_btn_wc_shipping_address_line_2 = '<small>(<a href="javascript:void(0);" onclick="hideWooCommerceField(\'div_wc_shipping_address_line_2\', this, ' + no + ');">hide</a>)</small>';
		var hide_btn_wc_shipping_city 			= '<small>(<a href="javascript:void(0);" onclick="hideWooCommerceField(\'div_wc_shipping_city\', this, ' + no + ');">hide</a>)</small>';
		var hide_btn_wc_shipping_zipcode 		= '<small>(<a href="javascript:void(0);" onclick="hideWooCommerceField(\'div_wc_shipping_zipcode\', this, ' + no + ');">hide</a>)</small>';
		var hide_btn_wc_shipping_country 		= '<small>(<a href="javascript:void(0);" onclick="hideWooCommerceField(\'div_wc_shipping_country\', this, ' + no + ');">hide</a>)</small>';
		var hide_btn_wc_shipping_state_int 		= '<small>(<a href="javascript:void(0);" onclick="hideWooCommerceField(\'div_wc_shipping_state_int\', this, ' + no + ');">hide</a>)</small>';
		var hide_btn_wc_shipping_state_usa 		= '<small>(<a href="javascript:void(0);" onclick="hideWooCommerceField(\'div_wc_shipping_state_usa\', this, ' + no + ');">hide</a>)</small>';
		var hide_btn_wc_shipping_state_can 		= '<small>(<a href="javascript:void(0);" onclick="hideWooCommerceField(\'div_wc_shipping_state_can\', this, ' + no + ');">hide</a>)</small>';
		var hide_btn_wc_shipping_emailaddress 	= '<small>(<a href="javascript:void(0);" onclick="hideWooCommerceField(\'div_wc_shipping_emailaddress\', this, ' + no + ');">hide</a>)</small>';
		var hide_btn_wc_shipping_phone 			= '<small>(<a href="javascript:void(0);" onclick="hideWooCommerceField(\'div_wc_shipping_phone\', this, ' + no + ');">hide</a>)</small>';

		var req_chk_wc_shipping_first_name 		= '<input type="checkbox" onclick="requireWooCommerceField(\'first_name\', this, ' + no + ');" /> required';
		var req_chk_wc_shipping_last_name 		= '<input type="checkbox" onclick="requireWooCommerceField(\'last_name\', this, ' + no + ');" /> required';
		var req_chk_wc_shipping_company 		= '<input type="checkbox" onclick="requireWooCommerceField(\'company\', this, ' + no + ');" /> required';
		var req_chk_wc_shipping_address_line_1 	= '<input type="checkbox" onclick="requireWooCommerceField(\'address_line_1\', this, ' + no + ');" /> required';
		var req_chk_wc_shipping_address_line_2 	= '<input type="checkbox" onclick="requireWooCommerceField(\'address_line_2\', this, ' + no + ');" /> required';
		var req_chk_wc_shipping_city 			= '<input type="checkbox" onclick="requireWooCommerceField(\'city\', this, ' + no + ');" /> required';
		var req_chk_wc_shipping_zipcode 		= '<input type="checkbox" onclick="requireWooCommerceField(\'zipcode\', this, ' + no + ');" /> required';
		var req_chk_wc_shipping_country 		= '<input type="checkbox" onclick="requireWooCommerceField(\'country\', this, ' + no + ');" /> required';
		var req_chk_wc_shipping_state_int 		= '<input type="checkbox" onclick="requireWooCommerceField(\'state_int\', this, ' + no + ');" /> required';
		var req_chk_wc_shipping_state_usa 		= '<input type="checkbox" onclick="requireWooCommerceField(\'state_usa\', this, ' + no + ');" /> required';
		var req_chk_wc_shipping_state_can 		= '<input type="checkbox" onclick="requireWooCommerceField(\'state_can\', this, ' + no + ');" /> required';
		var req_chk_wc_shipping_emailaddress 	= '<input type="checkbox" onclick="requireWooCommerceField(\'emailaddress\', this, ' + no + ');" /> required';
		var req_chk_wc_shipping_phone 			= '<input type="checkbox" onclick="requireWooCommerceField(\'phone\', this, ' + no + ');" /> required';

		wc_shipping_address_html += '<div class="address">';
			
			// first name field
            wc_shipping_address_html += '<div class="address2 div_wc_shipping_first_name">';
                wc_shipping_address_html += '<label>First Name</label>';
                wc_shipping_address_html += '<input disabled="disabled"  type="text" class="input_fields">';
                wc_shipping_address_html += req_chk_wc_shipping_first_name;
                wc_shipping_address_html += hide_btn_wc_shipping_first_name;
            wc_shipping_address_html += '</div>';
			
			// last name field
			wc_shipping_address_html += '<div class="address2 div_wc_shipping_last_name">';
                wc_shipping_address_html += '<label>Last Name </label>';
				wc_shipping_address_html += '<input disabled="disabled" type="text" class="input_fields">';
                wc_shipping_address_html += req_chk_wc_shipping_last_name;
				wc_shipping_address_html += hide_btn_wc_shipping_last_name;
			wc_shipping_address_html += '</div>';

		wc_shipping_address_html += '</div>';

		// company field
		wc_shipping_address_html += '<div class="address div_wc_shipping_company">';
            wc_shipping_address_html += '<label>Company </label>';
			wc_shipping_address_html += '<input disabled="disabled"  type="text" class="input_fields">';
            wc_shipping_address_html += req_chk_wc_shipping_company;
			wc_shipping_address_html += hide_btn_wc_shipping_company;
		wc_shipping_address_html += '</div>';

		// address line 1 field
		wc_shipping_address_html += '<div id="address_fields" class="address div_wc_shipping_address_line_1">';
            wc_shipping_address_html += '<label>Address Line 1 </label>';	
			wc_shipping_address_html += '<input disabled="disabled"  type="text" class="input_fields">';
            wc_shipping_address_html += req_chk_wc_shipping_address_line_1;
			wc_shipping_address_html += hide_btn_wc_shipping_address_line_1;
		wc_shipping_address_html += '</div>';

		// address line 2 field
		wc_shipping_address_html += '<div class="address div_wc_shipping_address_line_2" id="address_address2_' + no + '">';
            wc_shipping_address_html += '<label>Address Line 2 </label>';
			wc_shipping_address_html += '<input disabled="disabled"  type="text" class="input_fields">';
            wc_shipping_address_html += req_chk_wc_shipping_address_line_2;
			wc_shipping_address_html += hide_btn_wc_shipping_address_line_2;

		wc_shipping_address_html += '</div>';

		wc_shipping_address_html += '<div class="address">';
			
			// city field
			wc_shipping_address_html += '<div class="address2 div_wc_shipping_city">';
                wc_shipping_address_html += '<label>City </label>';
				wc_shipping_address_html += '<input disabled="disabled" type="text" class="input_fields">';
                wc_shipping_address_html += req_chk_wc_shipping_city;
				wc_shipping_address_html += hide_btn_wc_shipping_city;
			wc_shipping_address_html += '</div>';
			
			// zip code field
			wc_shipping_address_html += '<div class="address2 div_wc_shipping_zipcode">';
                wc_shipping_address_html += '<label>Zip / Postal Code </label>';
				wc_shipping_address_html += '<input disabled="disabled" type="text" class="input_fields">';
                wc_shipping_address_html += req_chk_wc_shipping_zipcode;
				wc_shipping_address_html += hide_btn_wc_shipping_zipcode;
			wc_shipping_address_html += '</div>';
		
		wc_shipping_address_html += '</div>';
		
		wc_shipping_address_html += '<div class="address">';
			
			// country field
			wc_shipping_address_html += '<div id="address_country_' + no + '" class="address2 div_wc_shipping_country">';
                wc_shipping_address_html += '<label>Country </label>';	
                wc_shipping_address_html += '<select disabled="disabled">';
					wc_shipping_address_html += '<option></option>';
				wc_shipping_address_html += '</select>';
                wc_shipping_address_html += req_chk_wc_shipping_country;
				wc_shipping_address_html += hide_btn_wc_shipping_country;
			wc_shipping_address_html += '</div>';

            // state / province / region field -> International
            
			wc_shipping_address_html += '<div  class="address2 state_div_' + no + ' div_wc_shipping_state_usa" id="state_' + no + '" >';
                wc_shipping_address_html += '<label>State / Province / Region </label>';   	
                wc_shipping_address_html += '<select disabled="disabled" id="state_us_field_' + no + '">';
					wc_shipping_address_html += '<option value="" selected="selected"></option><option value="Alabama">Alabama</option><option value="Alaska">Alaska</option><option value="Arizona">Arizona</option><option value="Arkansas">Arkansas</option><option value="California">California</option><option value="Colorado">Colorado</option><option value="Connecticut">Connecticut</option><option value="Delaware">Delaware</option><option value="District of Columbia">District of Columbia</option><option value="Florida">Florida</option><option value="Georgia">Georgia</option><option value="Hawaii">Hawaii</option><option value="Idaho">Idaho</option><option value="Illinois">Illinois</option><option value="Indiana">Indiana</option><option value="Iowa">Iowa</option><option value="Kansas">Kansas</option><option value="Kentucky">Kentucky</option><option value="Louisiana">Louisiana</option><option value="Maine">Maine</option><option value="Maryland">Maryland</option><option value="Massachusetts">Massachusetts</option><option value="Michigan">Michigan</option><option value="Minnesota">Minnesota</option><option value="Mississippi">Mississippi</option><option value="Missouri">Missouri</option><option value="Montana">Montana</option><option value="Nebraska">Nebraska</option><option value="Nevada">Nevada</option><option value="New Hampshire">New Hampshire</option><option value="New Jersey">New Jersey</option><option value="New Mexico">New Mexico</option><option value="New York">New York</option><option value="North Carolina">North Carolina</option><option value="North Dakota">North Dakota</option><option value="Ohio">Ohio</option><option value="Oklahoma">Oklahoma</option><option value="Oregon">Oregon</option><option value="Pennsylvania">Pennsylvania</option><option value="Rhode Island">Rhode Island</option><option value="South Carolina">South Carolina</option><option value="South Dakota">South Dakota</option><option value="Tennessee">Tennessee</option><option value="Texas">Texas</option><option value="Utah">Utah</option><option value="Vermont">Vermont</option><option value="Virginia">Virginia</option><option value="Washington">Washington</option><option value="West Virginia">West Virginia</option><option value="Wisconsin">Wisconsin</option><option value="Wyoming">Wyoming</option><option value="Armed Forces Americas">Armed Forces Americas</option><option value="Armed Forces Europe">Armed Forces Europe</option><option value="Armed Forces Pacific">Armed Forces Pacific</option>';
				wc_shipping_address_html += '</select>';
                wc_shipping_address_html += req_chk_wc_shipping_state_int;
				wc_shipping_address_html += hide_btn_wc_shipping_state_int;            
			wc_shipping_address_html += '</div>';

		wc_shipping_address_html += '</div>';
		
		wc_shipping_address_html += '<div class="address">';
			
			// email address field
			wc_shipping_address_html += '<div class="address2 div_wc_shipping_emailaddress">';
                wc_shipping_address_html += '<label>Email Address </label>';
				wc_shipping_address_html += '<input disabled="disabled"  type="text" class="input_fields">';
                wc_shipping_address_html += req_chk_wc_shipping_emailaddress;
				wc_shipping_address_html += hide_btn_wc_shipping_emailaddress;
			wc_shipping_address_html += '</div>';
			
			// phone field
			wc_shipping_address_html += '<div class="address2 div_wc_shipping_phone">';
                wc_shipping_address_html += '<label>Phone </label>';
				wc_shipping_address_html += '<input disabled="disabled"  type="text" class="input_fields">';
                wc_shipping_address_html += req_chk_wc_shipping_phone;
				wc_shipping_address_html += hide_btn_wc_shipping_phone;
			wc_shipping_address_html += '</div>';

		wc_shipping_address_html += '</div>';
		
		html.find("#field_position_" + no).html(wc_shipping_address_html);

	} 
    //pie-register WooCommerce --
    
    else if (type == "phone") {

        html.find("#field_position_" + no).html('<input type="text" id="field_' + no + '" class="input_fields">');

    } else if (type == "date") {

        html.find("#field_position_" + no).html('<div class="time date_format_field" id="datefield_' + no + '">  <div class="time_fields" id="mm_' + no + '">    <input disabled="disabled" type="text" class="input_fields">    <label>MM</label>  </div>  <div class="time_fields" id="dd_' + no + '">    <input disabled="disabled" type="text" class="input_fields">    <label>DD</label>  </div>  <div class="time_fields" id="yy_' + no + '">    <input disabled="disabled" type="text" class="input_fields">    <label>YY</label>  </div></div><div class="time date_format_field" id="datepicker_' + no + '"  style="display:none;">  <input type="text" class="input_fields">  <img src="' + piereg_wp_pie_register_url + 'assets/images/calendar.png" id="calendar_image_' + no + '" style="display:none;" /> </div><div class="time date_format_field" id="datedropdown_' + no + '"  style="display:none;">  <div class="time_fields" id="month_' + no + '"><select disabled="disabled">      <option>Month</option>    </select></div>    <div class="time_fields" id="day_' + no + '"><select disabled="disabled">      <option>Day</option>    </select>  </div>   <div class="time_fields" id="year_' + no + '"><select disabled="disabled">      <option>Year</option>    </select> </div></div>');

    } else if (type == "list") {

        html.find("#field_position_" + no).html('<input type="text" id="field_' + no + '" class="input_fields" disabled="disabled" style="width:90%;margin-right:2px;" ><img src="' + piereg_wp_pie_register_url + 'assets/images/plus.png" />');

    } // dropdown_ur
      else if (type == "custom_role") {

        html.find("#field_position_" + no).html('<select disabled="disabled" id="field_' + no + '"><option></option></select>');
 
    } else if (type == "pricing") {
        html.find("#field_label_" + no + " label").html("Membership");
        html.find("#field_position_" + no).html('<select disabled="disabled" id="field_' + no + '"><option></option></select>');

    } else if (type == "invitation") {

        html.find("#field_position_" + no).html('<input type="text" disabled="disabled"  id="invitation_field" class="input_fields">');

    } else if (type == "terms") {

        html.find("#field_position_" + no).html('<input type="checkbox" name="terms" id="terms_field" value="1" checked disabled>');

    } else if (type == "url" || type == "aim" || type == "yim" || type == "jabber" || type == "phone" || type == "description") {

        var label = piereg('a[name="' + type + '"]').html();

        if (type == "description") {

            html.find("#field_position_" + no).html('<textarea disabled="disabled" id="default_' + type + '" rows="5" style="width:100%;" cols="73"> </textarea>');

        } else {

            html.find("#field_position_" + no).html('<input type="text" disabled="disabled"  id="default_' + type + '" class="input_fields">');

        }

        html.find("#field_label_" + no + " label").html(label);

        //html.find(".edit_btn").remove();

        meta = '<input type="hidden" name="field[' + no + '][id]" value="' + no + '" id="id_' + no + '">';

        meta += '<input type="hidden" name="field[' + no + '][type]" value="default" id="type_' + no + '">';

        meta += '<input type="hidden" name="field[' + no + '][label]" value="' + label + '" id="label_' + no + '">';

        meta += '<input type="hidden" name="field[' + no + '][field_name]" value="' + type + '" id="label_' + no + '">';

    }
    return piereg("<div/>").append(html.clone()).html() + meta;

}



function getOptions(no, optionType) {
    // dropdown_ur
    var cr_dropdown = piereg('.sel_options_' + no).find('select').html();
	var role_options = '';
	if(cr_dropdown != undefined){
		var role_options = 'role_options';
    }
    
    var html = piereg('<div class="advance_fields '+ role_options +' multi_options sel_options_' + no + '"/>');

    html.append('<label for="display_' + no + '">Display Value</label>');

    html.append('<input type="text" name="field[' + no + '][display][]" id="display_' + no + '" class="input_fields character_fields select_option_display" />');

    html.append('<label for="value_' + no + '">Value</label>');

    html.append('<input type="text" name="field[' + no + '][value][]" id="value_' + no + '" class="input_fields character_fields select_option_value" />');
    
    // dropdown_ur
    if(cr_dropdown != undefined){
		html.append('<label class="label-role" for="custom_role_' + no +'">Role</label>');
		html.append('<select class="select-role checkbox_fields" id="role_selection_'+ no +'" name="field['+ no +'][role_selection][]" >'+cr_dropdown+'</select>');
    }
    
    html.append('<label>Checked</label>');

    html.append('<input type="' + optionType + '" value="0" id="check_' + no + '" name = "field[' + no + '][selected][]" class="select_option_checked">');

    html.append('<a class="pie_add_options_icon" style="color:white" href="javascript:;" onclick="addOptions(' + no + ',\'' + optionType + '\',piereg(this));">+</a><a class="pie_remove_options_icon" style="color:white;font-size: 13px;margin-left: 2px;" href="javascript:;" onclick="piereg(this).parent().remove();">x</a></div>');

    piereg("#field_" + no).append("<option></option>");

    return html;

}

function getEditor() {

    var html = piereg('<div class="advance_fields"/>');

    html.append('<textarea rows="8" id="htmlbox_' + no + '" class="ckeditor" name="field[' + no + '][html]" cols="16"></textarea>');

    return html;

}



function changeParaText(id) {

    var textarea = piereg("#paragraph_textarea_" + id).val();

    piereg("#paragraph_" + id).html(textarea);

}

//This will add options for select or multiselect

function addOptions(id, type, elem) {
    var html = piereg("<div/>").append(getOptions(id, type).clone()).html();

    if (!elem) {
        piereg(".sel_options_" + id).last().after(html);
    } else {
        piereg(elem).parent().after(html);
    }
}

function getPricingOptions(no, optionType) {
    var html = piereg('<div class="advance_fields dropdown_field_value sel_options_' + no + '"/>');

    html.append('<div class="advance_fields dropdown_field_value"><hr />');

    html.append('<label class="select_option_display" for="display_' + no + '">Display Value</label>');
    html.append('<input type="text" name="field[' + no + '][display][]" id="display_' + no + '" class="input_fields character_fields select_option_display" />');

    html.append('<label for="starting_price_' + no + '">Starting Price</label>');
    html.append('<input type="text" name="field[' + no + '][starting_price][]" id="starting_price_' + no + '" class="input_fields character_fields select_option_starting_price">');


    html.append('<label for="for_' + no + '">For</label>');
    html.append('<input type="text" name="field[' + no + '][for][]" id="for_' + no + '" class="input_fields character_fields select_option_for">');
    html.append('<select class="input_fields character_fields_mon select_option_for_period" name="field[' + no + '][for_period][]" id="for_period_' + no + '" ><option value="days">Days</option><option value="weeks">Weeks</option><option value="months">Months</option></select>');

    html.append('</div><div class="advance_fields dropdown_field_value">');

    html.append('<label class="select_option_then_price" for="then_price_' + no + '">Then Price</label>');
    html.append('<input type="text" name="field[' + no + '][then_price][]" id="then_price_' + no + '" class="input_fields character_fields select_option_then_price">');


    html.append('<label for="activation_cycle_' + no + '">Activation Cycle</label>');
    html.append('<select class="input_fields character_fields_sec select_option_activation_cycle" name="field[' + no + '][activation_cycle][]" id="activation_cycle_' + no + '" ><option value="-1">Use Default</option><option value="0">One Time</option><option value="7">Weekly</option><option value="30">Monthly</option><option value="182">Half Yearly</option><option value="273">Quarterly</option><option value="365">Yearly</option></select>');

    var piereg_pricing_options = "";
    var piereg_selected_user_role = "";
    for (var key in piereg_user_roles_array) {
        if (piereg_user_default_role == key) {
            piereg_selected_user_role = ' selected="selected" ';
        }
        piereg_pricing_options += '<option value="' + key + '" ' + piereg_selected_user_role + '>' + piereg_user_roles_array[key] + '</option>';
        piereg_selected_user_role = "";
    }

    html.append('<label for="role_' + no + '">Role</label>');
    html.append('<select class="input_fields character_fields_sec select_option_role" name="field[' + no + '][role][]" id="role_' + no + '" >' + piereg_pricing_options + '</select>');

    html.append('<label>Checked</label>');
    html.append('<input type="' + optionType + '" value="0" id="check_' + no + '" name = "field[' + no + '][selected][]" class="select_option_checked">');
    html.append('<a style="color:white" href="javascript:;" onclick="addPricingOptions(' + no + ',\'' + optionType + '\',piereg(this));">+</a><a style="color:white;font-size: 13px;margin-left: 2px;" href="javascript:;" onclick="piereg(this).parent().remove();">x</a></div>');

    html.append('</div>');

    piereg("#field_" + no).append("<option></option>");

    return html;
}

function addPricingOptions(id, type, elem) {

    var html = piereg("<div/>").append(getPricingOptions(id, type).clone()).html();

    if (!elem) {

        piereg(".sel_options_" + id).last().after(html);

    } else {

        piereg(elem).parent().after(html);

    }

}


//Function to check numeric value

function isNumeric(val) {

    var numberRegex = /^[+-]?\d+(\.\d+)?([eE][+-]?\d+)?$/;

    if (!numberRegex.test(val) || val < 1) {

        return false;

    }

}



function changeDropdown(elm) {

    var id = elm.attr("id");

    id = id.replace("display_", "");

    id = id.replace("value_", "");

    id = id.replace("check_", "");

    piereg("#field_" + id).html("");

    piereg('.sel_options_' + id).each(function(a, b) {

        var html = piereg('.sel_options_' + id).eq(a).find("input.select_option_display").val();

        var val = piereg('.sel_options_' + id).eq(a).find("input.select_option_value").val();

        piereg('.sel_options_' + id).eq(a).find("input.select_option_checked").val(a);

        var option = piereg("<option/>").attr("value", val).html(html);

        if (piereg('.sel_options_' + id).eq(a).find("input.select_option_checked").is(':checked')) {

            option.attr('selected', 'selected');

        }

        option.appendTo("#field_" + id);

    });

}



function checkEvents(elm, target) {

    if (elm.checked) {

        piereg("#" + target).hide();

        piereg("." + target).hide();

    } else {

        piereg("#" + target).show();

        piereg("." + target).show();

    }

}

var pie_scroll_counter1 = false,
    pie_scroll_counter2 = false,
    pie_scroll_counter3 = false;

function add_scroll_dragable_area(id) // for piereg scroll in right side accordion

{

    piereg(id).mCustomScrollbar({

        scrollButtons: {

            enable: true

        }

    });

}

function bindButtons() {

    //Adding Functionalities to right menu

    piereg(".right_menu_heading").on("click", function(e) {
        e.preventDefault();
        if (piereg(this).parent().find("ul").is(':visible')) {

            piereg(this).parent().find("ul").slideUp();

            return;

        }

        piereg("ul.picker").slideUp();

        piereg(this).parent().find("ul").slideDown().promise().done(function() {

            var id = piereg(this).attr("id");

            if (pie_scroll_counter1 == false && id == "content_1")

            {

                add_scroll_dragable_area("ul#content_1");

                pie_scroll_counter1 = true;

            } else

            if (pie_scroll_counter2 == false && id == "content_2")

            {

                add_scroll_dragable_area("ul#content_2");

                pie_scroll_counter2 = true;

            } else

            if (pie_scroll_counter3 == false && id == "content_3")

            {

                add_scroll_dragable_area("ul#content_3");

                pie_scroll_counter3 = true;

            }

        });



        e.preventDefault();

    });

    //Adding Functionalities to Edit buttons

    piereg('body').on("click", ".edit_btn", function(e) {
        var curr_field_id = jQuery(this).closest('.fields_options').attr('id');
        piereg(this).parents(".fields").find(".fields_main").toggle();
        e.preventDefault();
        if ( typeof curr_field_id !== "undefined" )
        {
            if ( piereg(piereg('#'+curr_field_id).siblings('.woocommerce_field')).length)
            {
                woocommerce_scroll_element = piereg(piereg(piereg(piereg('#'+curr_field_id).children('[class*=fields_position]'))).children('.address')).last();
                piereg('html,body').animate({scrollTop: piereg(woocommerce_scroll_element).offset().top},'slow');
            } else {
                piereg('html,body').animate({scrollTop: piereg("#"+curr_field_id).offset().top},'slow');
            }
        }
    });
    //Adding Functionalities to delete (X) buttons

    piereg("body").on("click", ".delete_btn", function() {

        var delId = piereg(this).attr("rel");

        var delType = piereg("#type_" + delId).val();

        var field = piereg("input[name='field[" + delId + "][field_name]']").val();

        piereg(this).parents("li").fadeOut(function() {

            piereg(this).remove();

            /*
             * Add snipt in 2.0.12
             */
            try {
                if (piereg(this).closest('li').find('textarea.ckeditor').attr('id')) {
                    var piereg_ckeditorid;
                    piereg_ckeditorid = piereg(this).closest('li').find('textarea.ckeditor').attr('id');
                    if (piereg_ckeditorid.length > 0) {
                        CKEDITOR.remove(CKEDITOR.instances[piereg_ckeditorid]);
                    }
                }
            } catch (e) {
                console.log(e);
            }
// wc_billing_address check -- pie-register WooCommerce
            if (delType == "default" ||  delType == "password" || delType == "name" || delType == "address" || delType == "wc_billing_address" || delType == "wc_shipping_address" || delType == "captcha" || delType == "math_captcha" || delType == "two_way_login_phone" || delType == "invitation" || delType == "terms" || delType == "pricing" || delType == "username" || delType == "profile_pic" || delType == "custom_role") {

                piereg("ul.controls li a[name='" + field + "']").parent().show();

                piereg("ul.controls li a[name='" + delType + "']").parent().show();

            }

        });

    });

    //Change Label ehile editing label field

    piereg(".field_label").on("keyup", function() {

        var id = piereg(this).attr("id");

        var val = piereg(this).val();

        //On retyping the label and removing comletely it leaves the first letter to remove. Should replace the label with default http://prntscr.com/c6ri2q
        piereg('#field_' + id + ' label').text(piereg(this).val());
        /*if (val != "") { 

        	piereg('#field_' + id + ' label').html(piereg(this).val());

        }*/

    });



    piereg(".field_label2").on("keyup", function() {

        var id = piereg(this).attr("id");

        var val = piereg(this).val();
        piereg('#field_' + id + ' label').html(piereg(this).val());
        /*if (val != "") {

        	piereg('#field_' + id + ' label').html(piereg(this).val());

        }*/

    });

    //Change Field length

    piereg(".field_length").on("keyup", function() {

        var id = piereg(this).attr("id");

        id = id.replace("length_", "");

        var val = piereg(this).val();

        if (val != "") {

            piereg('#field_' + id).attr("maxlength", val);

        } else {

            piereg('#field_' + id).removeAttr("maxlength");

        }

    });

    //Change Field default value

    piereg(".field_default_value").on("keyup", function() {

        var id = piereg(this).attr("id");

        id = id.replace("default_value_", "");

        var val = piereg(this).val();

        piereg('#field_' + id).val(val);

    });

    //Change Field placeholder

    piereg(".field_placeholder").on("keyup", function() {

        var id = piereg(this).attr("id");

        id = id.replace("placeholder_", "");

        var val = piereg(this).val();

        piereg('#field_' + id).attr("placeholder", val);

    });

    //Change Field 2nd placeholder
    piereg(".field_placeholder2").on("keyup", function() {

        var id = piereg(this).attr("id");

        id = id.replace("placeholder2_", "");

        var val = piereg(this).val();

        piereg('#pie2_field_' + id).attr("placeholder", val);

    });

    //Change Field rows

    piereg(".field_rows").on("keyup", function() {

        var id = piereg(this).attr("id");

        id = id.replace("rows_", "");

        var val = piereg(this).val();

        piereg('textarea#field_' + id).attr("rows", val);

    });

    //Change Field Cols

    piereg(".field_cols").on("keyup", function() {

        var id = piereg(this).attr("id");

        id = id.replace("cols_", "");

        var val = piereg(this).val();

        piereg('textarea#field_' + id).attr("cols", val);

    });

    //Next Button

    piereg('body').on("change", ".next_button", function() {

        if (piereg(this).attr('checked')) {
            pageBreakSelectionNext(piereg(this));
        }
    });

    piereg('body').on("click", ".next_button", function() {
        pageBreakSelectionNext(piereg(this));
    });

    //Previous Button

    // When the page loads
    piereg('body').on("change", ".prev_button", function() {

        if (piereg(this).attr('checked')) {
            pageBreakSelectionPrevious(piereg(this));
        }
    });

    // When the admin selects an option
    piereg('body').on("click", ".prev_button", function() {
        pageBreakSelectionPrevious(piereg(this));
    });

    //Calendar Icon

    piereg(".calendar_icon").on("change", function() {


        var val = piereg(this).val();

        var id = piereg(this).attr("id");

        id = id.replace("calendar_icon_", "");

        id = id.replace("_" + val, "");

        if (val == "none") {

            piereg("#icon_url_container_" + id).hide();

            piereg("#calendar_image_" + id).hide();

        } else if (val == "calendar") {

            piereg("#icon_url_container_" + id).hide();

            piereg("#calendar_image_" + id).show();

        } else if (val == "custom") {

            piereg("#icon_url_container_" + id).show();

            piereg("#calendar_image_" + id).hide();

        }


    });

    piereg("select.date_type").on("change", function() {

        var id = piereg(this).attr("id");

        id = id.replace("date_type_", "");

        var val = piereg(this).val();

        piereg("#datefield_" + id).hide();

        piereg("#datepicker_" + id).hide();

        piereg("#datedropdown_" + id).hide();

        piereg("#icon_div_" + id).hide();

        if (val == "datefield") {

            piereg("#datefield_" + id).show();

        } else if (val == "datepicker") {

            piereg("#datepicker_" + id).show();

            piereg("#icon_div_" + id).show();

        } else if (val == "datedropdown") {

            piereg("#datedropdown_" + id).show();

        }

    });

    //Change Date Format

    piereg("select.date_format").on("change", function() {

        var id = piereg(this).attr("id");

        id = id.replace("date_format_", "");

        var val = piereg(this).val();

        if (val.charAt(0) == "m" && val.charAt(val.length - 1) == "y") {

            piereg("#dd_" + id).insertBefore(piereg("#yy_" + id));

            piereg("#mm_" + id).insertBefore(piereg("#dd_" + id));

            piereg("#day_" + id).insertBefore(piereg("#year_" + id));

            piereg("#month_" + id).insertBefore(piereg("#day_" + id));

        } else if (val.charAt(0) == "d" && val.charAt(val.length - 1) == "y") {

            piereg("#mm_" + id).insertBefore(piereg("#yy_" + id));

            piereg("#dd_" + id).insertBefore(piereg("#mm_" + id));

            piereg("#month_" + id).insertBefore(piereg("#year_" + id));

            piereg("#day_" + id).insertBefore(piereg("#month_" + id));

        } else if (val.charAt(0) == "y" && val.charAt(val.length - 1) == "d") {

            piereg("#mm_" + id).insertBefore(piereg("#dd_" + id));

            piereg("#yy_" + id).insertBefore(piereg("#mm_" + id));

            piereg("#month_" + id).insertBefore(piereg("#day_" + id));

            piereg("#year_" + id).insertBefore(piereg("#month_" + id));

        }

    });

    //Change Time Format

    piereg("select.time_format").on("change", function() {

        var id = piereg(this).attr("id");

        id = id.replace("time_type_", "");

        var val = piereg(this).val();

        if (val == "12")

            piereg("#time_format_field_" + id).show();

        else if (val == "24")

            piereg("#time_format_field_" + id).hide();

    });

    //Change Address Type

    piereg("select.address_type").on("change", function() {

        var id = piereg(this).attr("id");

        id = id.replace("address_type_", "");

        var val = piereg(this).val();

        if (val == "International") {

            piereg("#default_country_div_" + id).show();

            piereg("#address_country_" + id).show();

            piereg("#state_" + id).show();

            piereg("#state_us_" + id).hide();

            piereg("#state_canada_" + id).hide();

            piereg("#default_state_div_" + id).hide();

        } else if (val == "United States") {

            piereg("#default_country_div_" + id).hide();

            piereg("#address_country_" + id).hide();

            piereg("#state_" + id).hide();

            piereg("#state_us_" + id).show();

            piereg("#state_canada_" + id).hide();

            piereg("#default_state_div_" + id).show();

            piereg(".can_states_" + id).hide();

            piereg(".us_states_" + id).show();

        } else if (val == "Canada") {

            piereg("#default_country_div_" + id).hide();

            piereg("#address_country_" + id).hide();

            piereg("#state_" + id).hide();

            piereg("#state_us_" + id).hide();

            piereg("#state_canada_" + id).show();

            piereg("#default_state_div_" + id).show();

            piereg(".can_states_" + id).show();

            piereg(".us_states_" + id).hide();

        }

        if (document.getElementById("hide_state_" + id).checked) {

            piereg(".state_div_" + id).hide();

            piereg("#default_state_div_" + id).hide();

        }

    });

    //Change Default Country

    piereg("select.default_country").on("change", function() {

        var id = piereg(this).attr("id");

        id = id.replace("default_country_", "");

        var val = piereg(this).val();

        piereg("#address_country_" + id + " select").html('<option>' + val + '<option>');

    });

    //Change Name Format 

    piereg("select.name_format").on("change", function() {

        var id = piereg(this).attr("id");

        id = id.replace("name_format", "");

        var val = piereg(this).val();

        if (val == "normal") {

            piereg("#field_label" + id + " label").hide();

            piereg('#first_name_field input').appendTo('#first_name_field');

            piereg('#last_name_field input').appendTo('#last_name_field');

        } else if (val == "extended") {

            piereg("#field_label" + id + " label").show();

            piereg('#first_name_field label').appendTo('#first_name_field');

            piereg('#last_name_field label').appendTo('#last_name_field');

        }

    });

    //Adding option Display Value

    piereg("input.select_option_display,input.select_option_value").on("keyup", function() {

        changeDropdown(piereg(this));

    });

    piereg("input.select_option_checked").on("click", function() {

        changeDropdown(piereg(this));

    });

    piereg(".paypal").on("click", function() {

        piereg("#paypal_button").remove();

        piereg("#submit_ul").hide();

        piereg("#submit_ul").after('<ul id="paypal_button"><li class="fields"><div class="fields_options submit_field"><img src="https://www.paypalobjects.com/en_US/i/btn/btn_buynowCC_LG.gif" /></div> <input  name="field[submit][type]" value="PaypalStandard" type="hidden" /></li></ul>');

    });

    piereg(".submit_button").on("click", function() {

        piereg("#paypal_button").remove();

        piereg("#submit_ul").show();

    });

    piereg("ul.controls li a.default").on("click", function(e) {
        e.preventDefault();
        piereg(this).parent().hide();

    });

    piereg("ul.controls li a.default").each(function() {

        var type = piereg(this).attr("name");

        if (document.getElementById("default_" + type))

            piereg(this).parent().hide();

    });

    //Allow only numeric on Length, Rows, Cols, Length Field 

    if (piereg(".pieregister-admin").length > 0) {
        piereg(".numeric").on("keydown", function(event) {

            if (event.keyCode == 46 || event.keyCode == 8 || event.keyCode == 9 || event.keyCode == 27 || event.keyCode == 13 ||

                // Allow: Ctrl+A

                (event.keyCode == 65 && event.ctrlKey === true) ||

                // Allow: home, end, left, right

                (event.keyCode >= 35 && event.keyCode <= 39) ||
                
                //Allow: negative ( - ) sign
                
                (event.keyCode == 189) || (event.keyCode == 109) ){

                // let it happen, don't do anything

                return;

            } else {

                // Ensure that it is a number and stop the keypress

                if (event.shiftKey || (event.keyCode < 48 || event.keyCode > 57) && (event.keyCode < 96 || event.keyCode > 105)) {

                    event.preventDefault();

                }

            }

        });
    }

    //Submit Button Properties Icon

    piereg(".reg_success").on("change", function() {

        piereg(".submit_meta").hide();

        piereg(".reg_success").each(function(index, element) {

            if (piereg(this).is(':checked')) {

                var val = piereg(this).val();

                piereg(".submit_meta_" + val).show();

            }

        });

    });

    //default state

    piereg(".default_state").on("change", function() {

        var id = piereg(this).attr("id");

        id = id.replace("can_states_", "");

        id = id.replace("us_states_", "");

        var val = piereg(this).val();

        piereg("#state_canada_field_" + id).html('<option>' + val + '</option>');

        piereg("#state_us_field_" + id).html('<option>' + val + '</option>');

    });

    piereg(document).on("change", '.hide_state', function() {
        var id = piereg(this).attr("id");

        id = id.replace("hide_state_", "");

        var val = piereg(this).val();

        var hide_state_checkbox = document.getElementById("hide_state_"+id);
        
        if (hide_state_checkbox.checked) {

            piereg(".state_div_" + id).hide();

            piereg("#default_state_div_" + id).hide();

        } else {
            piereg(".state_div_" + id).show();

            piereg("#default_state_div_" + id).show();

            var val = piereg("#address_type_" + id).val();

            if (val == "International") {

                piereg("#default_country_div_" + id).show();

                piereg("#address_country_" + id).show();

                piereg("#state_" + id).show();

                piereg("#state_us_" + id).hide();

                piereg("#state_canada_" + id).hide();

                piereg("#default_state_div_" + id).hide();

            } else if (val == "United States") {

                piereg("#default_country_div_" + id).hide();

                piereg("#address_country_" + id).hide();

                piereg("#state_" + id).hide();

                piereg("#state_us_" + id).show();

                piereg("#state_canada_" + id).hide();

                piereg("#default_state_div_" + id).show();

                piereg(".can_states_" + id).hide();

                piereg(".us_states_" + id).show();

            } else if (val == "Canada") {

                piereg("#default_country_div_" + id).hide();

                piereg("#address_country_" + id).hide();

                piereg("#state_" + id).hide();

                piereg("#state_us_" + id).hide();

                piereg("#state_canada_" + id).show();

                piereg("#default_state_div_" + id).show();

                piereg(".can_states_" + id).show();

                piereg(".us_states_" + id).hide();

            }

        }

    });
    piereg(document).on("change", '.hide_confirm_password', function() {
        if (this.checked) {
            piereg('.confirm_password_field_label').fadeOut(1000);
            piereg(".confirm_password_field").fadeOut(1000);
        } else {
            piereg('.confirm_password_field_label').fadeIn(1000);
            piereg(".confirm_password_field").fadeIn(1000);
        }

    });
    if ( piereg('.hide_confirm_password').attr('checked') ) {
        piereg('.confirm_password_field_label').fadeOut(1000);
        piereg(".confirm_password_field").fadeOut(1000);
    }else{
        piereg('.confirm_password_field_label').fadeIn(1000);
        piereg(".confirm_password_field").fadeIn(1000);
    }
    piereg(document).on("change", '.hide_last_name', function() {
        if (this.checked) {
            piereg('.last_name_field_label').fadeOut(1000);
            piereg(".last_name_field").fadeOut(1000);
        } else {
            piereg('.last_name_field_label').fadeIn(1000);
            piereg(".last_name_field").fadeIn(1000);
        }

    });
    if ( piereg('.hide_last_name').attr('checked') ) {
        piereg('.last_name_field_label').fadeOut(1000);
        piereg(".last_name_field").fadeOut(1000);
    }else{
        piereg('.last_name_field_label').fadeIn(1000);
        piereg(".last_name_field").fadeIn(1000);
    }
    piereg("#confirm_email").on("change", function() {
        var confirm_email_checkbox = document.getElementById("confirm_email");
        if (confirm_email_checkbox.checked) {
            /*piereg("#confirm_email_label_1").show();*/
            piereg(".confrim_email_label2").fadeIn(1000);
            piereg("#confirm_email_field_1").fadeIn(1000);
        } else {
            /*piereg("#confirm_email_label_1").hide();*/
            piereg(".confrim_email_label2").fadeOut(1000);
            piereg("#confirm_email_field_1").fadeOut(1000);
        }

    });
    if (document.getElementById("confirm_email")) {
        if(document.getElementById("confirm_email").checked){
            piereg(".confrim_email_label2").show();
        }else{
            piereg(".confrim_email_label2").hide();
        }
    }

    piereg(".right_menu_heading").parent().find("ul").slideUp();

}

piereg(document).ready(function() {

    //Hiding Advanced Options By default	

    piereg(".fields_main").hide();

    //Click functions on control

    piereg(".controls li a").click(function() {

        dragType = piereg(this).attr("name");

        piereg(this).parent().addClass("fields");




        var response = defaultMeta[dragType];

        endHtml = getStyle(dragType);

        if (response)

        {

            if (dragType == "pricing") {
                response = response.split("%payment_gateways_list_box%").join(piereg_payment_gateways_list);
            }

            response = response.split("%d%").join(no);

            endHtml += response;

            if (dragType == "url" || dragType == "aim" || dragType == "yim" || dragType == "jabber" || dragType == "description") {
                endHtml += '<input value = "default" type="hidden"  name="field[' + no + '][type]" id="type_' + no + '">';
            } else {
                endHtml += '<input value = "' + dragType + '" type="hidden"  name="field[' + no + '][type]" id="type_' + no + '">';
            }
        }

        if (endHtml)

        {

            endHtml = '<li class="fields">' + endHtml + '</li>';

            piereg("#elements").append(endHtml);

            if (dragType == "html") {

                CKEDITOR.replace("htmlbox_" + no, {});
                /*
                 wp.editor.initialize("htmlbox_" + no, { 
                	tinymce: { 
                	  wpautop:true, 
                	  plugins : 'charmap colorpicker compat3x directionality fullscreen hr image lists media paste tabfocus textcolor wordpress wpautoresize wpdialogs wpeditimage wpemoji wpgallery wplink wptextpattern wpview', 
                	  toolbar1: 'formatselect bold italic | bullist numlist | blockquote | alignleft aligncenter alignright | link unlink | wp_more | spellchecker' 
                	}, 
                	quicktags: true 
                 });
                 */

            }

            piereg(".swap_class").trigger("change");

            no++;

            dragType = "";

            endHtml = "";

        }




    });

    piereg("form#formeditor").submit(function(e) {

        piereg(".field_label").each(function(index, element) {

            /*if (dragType == "url" || dragType == "aim" || dragType == "yim" || dragType == "jabber"  || dragType == "description")
            {
            	
            }*/

            if (piereg.trim(piereg(this).val()) == '') {

                var id = piereg(this).attr("id");

                id = id.replace("label_", "");

                var type = piereg("#type_" + id).val();
                var label_val = piereg("#field_label_" + id + " label").html();

                /*
                if(type != "html")
                	piereg(this).val(label_val);
                */
            }

        });



        piereg(".greaterzero").each(function(index, element)

            {

                if (piereg.trim(piereg(this).val()) == '' || piereg.trim(piereg(this).val()) < 1 || piereg.trim(piereg(this).val()) == "0") {



                    piereg(this).val("1");

                }

            });




    });

    //This will handle sorting and after drop changes		

    piereg("#elements").droppable({

        // accept: ".controls li p",

        drop: function(event, ui) {

            ui.draggable.addClass("fields");

            if (endHtml) {



                endHtml += fieldMeta;

                fieldMeta = "";

                ui.draggable.html(endHtml);

                no++;

                piereg(".swap_class").trigger("change");
                //Pie-Register WooCommerce
                if (dragType == "invitation" || dragType == "terms" || dragType == "name" || dragType == "address1" || dragType == "wc_billing_address" || dragType == "wc_shipping_address" || dragType == "captcha" || dragType == "math_captcha" || dragType == "two_way_login_phone" || dragType == "aim" || dragType == "yim" || dragType == "jabber" || dragType == "description" || dragType == "url" || dragType == "pricing" || dragType == "username" || dragType == "profile_pic") {
                    piereg('ul.controls li a[name="' + dragType + '"]').parent().hide();

                }

                dragType = "";

                endHtml = "";



            }

        }

    }).sortable({

        cursor: 'crosshair',

        placeholder: "sort-state-highlight",

        start: function(event, ui) {

            for (var i in CKEDITOR.instances) {

                CKEDITOR.instances[i].destroy();

            }

        },

        stop: function(event, ui) {

            piereg('.ckeditor').each(function() {

                var id = piereg(this).attr("id");

                CKEDITOR.replace(id, {});

            });

            dragType = "";

            endHtml = "";

        }

    });

    //Selecting and Dragging control from the list

    piereg(".controls li").draggable({

        connectToSortable: "#elements",

        helper: "clone",

        revert: "invalid"

    });



    //This will change the html of a control while dragging

    piereg(".controls li").on("dragstart", function(event, ui) {

        ui.helper.attr("class", "");

        fieldMeta = "";

        dragType = ui.helper.find("a").attr("name");

        //currHTML = ui.helper.html();

        endHtml = getStyle(dragType);


        if (dragType == "url" || dragType == "aim" || dragType == "yim" || dragType == "jabber" || dragType == "description")

        {
            fieldMeta = defaultMeta[dragType];

            fieldMeta = fieldMeta.split("%d%").join(no);

        } else if (dragType == "pricing") {
            fieldMeta = defaultMeta[dragType];
            fieldMeta = fieldMeta.split("%d%").join(no);
            fieldMeta = fieldMeta.split("%payment_gateways_list_box%").join(piereg_payment_gateways_list);
        } else

        {
            fieldMeta = defaultMeta[dragType];

            fieldMeta = fieldMeta.split("%d%").join(no);

        }




        fieldMeta += '<input value = "' + dragType + '" type="hidden"  name="field[' + no + '][type]" id="type_' + no + '">';



        //console.log(fieldMeta);



        if (endHtml) {

            ui.helper.addClass("fields");

        }

    });




    //This will change the html of a control while dragging

    piereg(".controls li").on("drag", function(event, ui) {

        ui.helper.attr("class", "");

        endHtml = getStyle(dragType);

        if (endHtml) {

            ui.helper.addClass("fields");
            ui.helper.css("height", "auto");
            ui.helper.css("width", "100%");

        }

    });

    bindButtons();

    showHideReset();

    piereg(".pieregister-admin").on("change", ".piereg_recaptcha_type select", function() {
        var img_object = piereg(this).parent().parent().parent().parent().find(".fields_optionsbg img");
        if (piereg(this).val() == 2) {
            var img_src = img_object.attr("data-captcha-img-src");
            img_src = img_src + "new-recatpcha.png";
            piereg(".pieregister-admin .piereg_recaptcha_skin").hide();
        } else {
            var img_src = img_object.attr("data-captcha-img-src");
            img_src = img_src + "recatpcha.jpg";
            piereg(".pieregister-admin .piereg_recaptcha_skin").show();
        }
        img_object.attr("src", img_src);
    });

    /* need to optimize this in 3.0.17 */
    if (piereg("#allow_payment_gateways_stripe").length > 0 || piereg("#allow_payment_gateways_authorizeNet").length > 0) {
        piereg('#allow_payment_gateways_stripe, #allow_payment_gateways_authorizeNet').click(function() {
            if (piereg('#allow_payment_gateways_stripe').is(":checked") || piereg('#allow_payment_gateways_authorizeNet').is(":checked")) {
                piereg('.stripe_price').show();
            } else if (piereg('#allow_payment_gateways_stripe').is(":not(:checked)") || piereg('#allow_payment_gateways_authorizeNet').is(":not(:checked)")) {

                if (piereg("#allow_payment_gateways_stripe").length > 0 && piereg("#allow_payment_gateways_authorizeNet").length > 0) {
                    if (piereg('#allow_payment_gateways_stripe').is(":not(:checked)") && piereg('#allow_payment_gateways_authorizeNet').is(":not(:checked)")) {
                        piereg('.stripe_price').hide();
                    }
                } else {
                    piereg('.stripe_price').hide();
                }
            }
        });
        if (piereg('#allow_payment_gateways_stripe').is(":checked") || piereg('#allow_payment_gateways_authorizeNet').is(":checked")) {
            piereg('.stripe_price').show();
        } else if (piereg('#allow_payment_gateways_stripe').is(":not(:checked)") || piereg('#allow_payment_gateways_authorizeNet').is(":not(:checked)")) {

            if (piereg("#allow_payment_gateways_stripe").length > 0 && piereg("#allow_payment_gateways_authorizeNet").length > 0) {
                if (piereg('#allow_payment_gateways_stripe').is(":not(:checked)") && piereg('#allow_payment_gateways_authorizeNet').is(":not(:checked)")) {
                    piereg('.stripe_price').hide();
                }
            } else {
                piereg('.stripe_price').hide();
            }
        }
    }
    piereg(".pieregister-admin").on("change", "#allow_payment_gateways_stripe", function() {
        strip_api_desc();
    });
    strip_api_desc();
    function strip_api_desc(){
        if (piereg('#allow_payment_gateways_stripe').is(":checked") ) {
            piereg('.stripe_api_description').show();
        } else {
            piereg('.stripe_api_description').hide();
        }
    }
    if (piereg(".pieregister-admin .piereg_recaptcha_type").length > 0) {
        if (piereg(this).val() == 2) {
            piereg(".pieregister-admin .piereg_recaptcha_skin").hide();
        } else {
            piereg(".pieregister-admin .piereg_recaptcha_skin").show();
        }
    }

    /** 
     * Invitation settings page
     * Version 3.3.0
     */
    if (piereg('.pieregister-admin .invitation').length > 0) {
        // Submit Enable/Disable Invitation Code Form
        piereg('#enable_invitation_codes').change(function() {
            piereg('#enable_invitation_codes').parents('form').submit();
        })

        if(piereg('.pieregister-admin .btn-send-invite').length > 0){
            if(piereg('#enable_invitation_codes').prop("checked") == true){
                piereg('input.btn-send-invite').prop('disabled', false);
                piereg(".notice-invite-status").hide();                
            }
            else if(piereg('#enable_invitation_codes').prop("checked") == false){
                piereg('input.btn-send-invite').prop('disabled', true);
                piereg(".notice-invite-status").show();
            }
        }
        
        var dateToday = new Date();

        piereg('.invite-tabs li').click(function() {
            piereg(".expiry_datepicker").datepicker("destroy");
            piereg(this).addClass('invite-active').siblings().removeClass('invite-active');
            var divId = piereg(this).attr('data-invitetab-id');
            piereg('#' + divId).addClass('invite-active').siblings('.invitetab-inner').removeClass('invite-active');
            if (piereg('#' + divId).has('#invitation_code_prefix')) {
                piereg('#' + divId).siblings('.invitetab-inner').find('#piereg_codepass').val('');
            }
            if (piereg('#' + divId).has('#piereg_codepass')) {
                piereg('#' + divId).siblings('.invitetab-inner').find('#invitation_code_prefix').val('');
            }
            if (!piereg('#' + divId).find('li.code_usageItem, li.code_expiryDate').length > 0) {
                piereg('#' + divId).append('<li class="clearfix code_usageItem">' + piereg('.code_usageItem').html() + '</li><li class="clearfix code_expiryDate">' + piereg('.code_expiryDate').html() + '</li>').siblings('.invitetab-inner').find('li.code_usageItem,li.code_expiryDate').remove();
                piereg(".expiry_datepicker").datepicker({
                    defaultDate: null,
                    changeMonth: true,
                    numberOfMonths: 1,
                    minDate: dateToday,
                    dateFormat: 'yy-mm-dd',

                });
            }
            if (piereg('#' + divId).find('#invitation_expiry_date').attr('disabled') && piereg('#' + divId).attr('id') == 'auto-generate-codes') {
                piereg('#' + divId).find('#invitation_code_usage').attr('disabled', '');
            } else {
                piereg('#' + divId).find('#invitation_code_usage').removeAttr('disabled');
            }

        });
    }

    piereg('body').on("click", ".piereg-toggle-tags", function(e) {
        e.preventDefault();
        check = piereg(this).hasClass('piereg-not-show-tags');
        if(check){
            piereg('.piereg-toggle-tag-display').slideDown();
            piereg('.piereg-toggle-tag-display').slideDown();
            piereg('.piereg-toggle-tags').removeClass('piereg-not-show-tags');
        }else{
            piereg('.piereg-toggle-tag-display').slideUp();
            piereg('.piereg-toggle-tags').addClass('piereg-not-show-tags');
        }

    });
    piereg('body').on("click", ".smart-tag-field", function(){
        tag_val = piereg(this).data('field_name');
        input_replace = piereg(this).parent().parent().parent().find('input');
        input_replace.val('{'+tag_val+'}');
        piereg('.piereg-toggle-tag-display').slideUp();
        piereg('.piereg-toggle-tags').addClass('piereg-not-show-tags');
    });
});

piereg(window).on('load', function(){

    piereg("select.date_type").trigger("change");

    piereg("select.date_format").trigger("change");

    piereg(".calendar_icon").each(function(){
        if(piereg(this).attr('checked')){
            piereg(this).trigger("change");
        }
    });

    piereg(".swap_class").trigger("change");

    piereg(".submit_meta").hide();

    piereg(".reg_success").trigger("change");

    piereg("select.address_type").trigger("change");

    piereg(".next_button").trigger("change");

    piereg(".prev_button").trigger("change");

    //piereg(".name_format").trigger("change");	
});

// To decode HTML entities in the data
function pieregDecodeHTMLEntities(text) {
    return piereg("<textarea/>")
      .html(text)
      .text();
}

function fillValues(data, num) //Function to fill meta data

{
    var field = unserialize(data);

    var mt = "field[" + num + "][type]";

    var maintype = piereg('*[name="' + mt + '"]').val();

    for (index in field) {
        var fieldname = "field[" + num + "][" + index + "]";

        var fieldvalue = pieregDecodeHTMLEntities(field[index]);

        var fieldtag = piereg('*[name="' + fieldname + '"]').prop('tagName');

        var fieldtype = piereg('*[name="' + fieldname + '"]').prop('type');

        if (fieldtag == "SELECT" || fieldtag == "TEXTAREA" || (fieldtag == "INPUT" && (fieldtype == "text" || fieldtype == "hidden" || fieldtype == "number"))) //For textfields , select textareas

        {

            piereg('*[name="' + fieldname + '"]').val(fieldvalue);

        } else if (fieldtag == "INPUT" && (fieldtype == "radio" || fieldtype == "checkbox")) //For Radio && checkbox

        {

            if (fieldvalue != "") {

                piereg('*[name="' + fieldname + '"][value="' + fieldvalue + '"]').attr('checked', 'checked');

            } else {

                piereg('*[name="' + fieldname + '"]').attr('checked', 'checked');

            }

        } //dropdown_ur
         else if ((maintype == "dropdown" || maintype == "multiselect" || maintype == "radio" || maintype == "checkbox" || maintype == "pricing" || maintype == "custom_role") && index == "display") {

            if (maintype == "dropdown" || maintype == "radio" || maintype == "pricing" || maintype == "custom_role")

                var subtype = "radio";

            else if (maintype == "multiselect" || maintype == "checkbox")

                var subtype = "checkbox";

            for (a = 1; a < field["display"].length; a++) {
                if (maintype == "pricing" && index == "display") {
                    addPricingOptions(num, subtype);
                } else {
                    addOptions(num, subtype);
                }
            }

            for (a = 0; a < field["display"].length; a++) {
                if (maintype == "pricing" && index == "display") {
                    /*starting price*/
                    var fieldname = "field[" + num + "][starting_price][]";
                    piereg('input[name="' + fieldname + '"]').eq(a).val(field['starting_price'][a]);
                    /*For*/
                    var fieldname = "field[" + num + "][for][]";
                    piereg('input[name="' + fieldname + '"]').eq(a).val(field['for'][a]);
                    /*for period*/
                    var fieldname = "field[" + num + "][for_period][]";
                    piereg('input[name="' + fieldname + '"]').eq(a).val(field['for_period'][a]);
                    /*then price*/
                    var fieldname = "field[" + num + "][then_price][]";
                    piereg('input[name="' + fieldname + '"]').eq(a).val(field['then_price'][a]);
                    /*then price*/
                    var fieldname = "field[" + num + "][then_price][]";
                    piereg('input[name="' + fieldname + '"]').eq(a).val(field['then_price'][a]);
                    /*Role*/
                    var fieldname = "field[" + num + "][role][]";
                    piereg('select[name="' + fieldname + '"]').eq(a).val(field['role'][a]);
                    /*Activation Cycle*/
                    var fieldname = "field[" + num + "][activation_cycle][]";
                    piereg('select[name="' + fieldname + '"]').eq(a).val(field['activation_cycle'][a]);

                } else {
                    var fieldname = "field[" + num + "][value][]";
                    piereg('input[name="' + fieldname + '"]').eq(a).val(field['value'][a]);
                }
                var fieldname = "field[" + num + "][display][]";

                piereg('input[name="' + fieldname + '"]').eq(a).val(field['display'][a]);

                var fieldname = "field[" + num + "][selected][]";

                piereg('input[name="' + fieldname + '"]').eq(a).val(a);

            }
            // dropdown_ur
            if(field['role_selection']){
            	var field_parent = "field[" + num + "][role_selection][]";
				fieldname = piereg('select[name="' + field_parent + '"]');
				a = 0
				fieldname.each(function(index, el) {
					piereg(this).find('option').each(function(){
						if(piereg(this).val() == field['role_selection'][a]){
							piereg(this).attr('selected','selected');
							return false;
						}
					}); a++;
				});
            }

            if (field["selected"]) {

                //If Item is selected

                var fieldname = "field[" + num + "][selected][]";

                fieldname = piereg('input[name="' + fieldname + '"]');

                for (a = 0; a < fieldname.length; a++) {

                    for (b = 0; b < field['selected'].length; b++) {

                        if (a == field['selected'][b]) {

                            fieldname.eq(a).attr("checked", "checked");

                        }

                    }

                }

            }

        }else if(index == 'allowed_codes'){
            if (maintype == "invitation"){
                var field_parent = "field[" + num + "][allowed_codes][]";
                fieldname = piereg('select[name="' + field_parent + '"]');
    
                for(a = 0; a <= field['allowed_codes'].length; a++){
                    fieldname.find('option').each(function(index, el) {
                        
                        if(piereg(this).val() == field['allowed_codes'][a]){
                            piereg(this).attr('selected','selected');
                            return false;
                        }
        
                    });
                }
            }
        }

    }

}



function createckeditor() {

    piereg(".ckeditor").ckeditor();

}



function swapClass(val) {

    if (val == "top") {

        piereg(".fields_position").addClass("fields_position_top");

        piereg(".fields_position_top").removeClass("fields_position");

        piereg(".label_position").addClass("label_position_top");

        piereg(".label_position_top").removeClass("label_position");

    } else if (val == "left") {

        piereg(".fields_position_top").addClass("fields_position");

        piereg(".fields_position").removeClass("fields_position_top");

        piereg(".label_position_top").addClass("label_position");

        piereg(".label_position").removeClass("label_position_top");

    }

}

function showHideReset()

{

    var val = piereg("#show_reset").val();

    var elm      = piereg("#reset_btn");
    var elm_text = piereg(".reset-button-text");

    if (val == 0){
        elm.hide();
        elm_text.hide();
    }
    else{
        elm.show();
        elm_text.show();
    }

}

function pageBreakSelectionNext($this){
    
    var val = $this.val();
    var id  = $this.attr("id");
    id      = id.replace("next_button_", "");
    id      = id.replace("_" + val, "");

    if (val == "text") {

        piereg("#next_button_url_container_" + id).hide();
        piereg("#next_button_text_container_" + id).show();

    } else if (val == "url") {

        piereg("#next_button_url_container_" + id).show();
        piereg("#next_button_text_container_" + id).hide();

    }
}

function pageBreakSelectionPrevious($this){
    var val = $this.val();
    var id  = $this.attr("id");
    id      = id.replace("prev_button_", "");
    id      = id.replace("_" + val, "");

    if (val == "text") {

        piereg("#prev_button_url_container_" + id).hide();
        piereg("#prev_button_text_container_" + id).show();

    } else if (val == "url") {

        piereg("#prev_button_url_container_" + id).show();
        piereg("#prev_button_text_container_" + id).hide();

    }
}

// Declare jQuery Object to $.
$ = jQuery;