<?php
/*
	* New form builder screen (design: docs/form-builder-modernisation.html)
	* Step 2: add, reorder, duplicate, delete fields and steps; save.
	* Saving posts exactly what the classic builder posts (field[ID][...] in canvas order, same nonce and handler),
	* so stored forms, the front end and add-ons do not change. Every saved setting of every field is posted back as is.
	* Since v4.0.0.0
*/
if ( ! defined( 'ABSPATH' ) ) exit;

/* Same access rules as the classic builder: the free version edits its one existing form only (no new forms) */
if ( ! isset( $_GET['form_id'] ) ) {
	wp_die( esc_html__( "You don't have permission to access this page.", 'pie-register' ), '', array( 'response' => 403 ) );
}
$piereg_nb_forms = (array) $this->pie_pr_dec_vars_array['prRegFormsIds'];
if ( (int) $_GET['form_id'] < 1 || ! isset( $piereg_nb_forms[0] ) || 'reg_form_' . (int) $_GET['form_id'] !== $piereg_nb_forms[0] ) {
	echo '<div class="pieregister-admin"><h1>' . esc_html__( 'Form Not Found!', 'pie-register' ) . '</h1></div>';
	return;
}

$data = $this->getCurrentFields();
if ( ! empty( $data ) && ! is_array( $data ) ) {
	$data = unserialize( $data ); // same as the classic builder (menus/PieRegEditForm.php)
}
if ( ! is_array( $data ) || sizeof( $data ) == 0 ) {
	$data = get_option( 'pie_fields_default' );
}
$button		= get_option( OPTION_PIE_REGISTER );
$form_id	= isset( $_GET['form_id'] ) ? (int) sanitize_key( $_GET['form_id'] ) : 0;
$form_label	= ( isset( $data['form']['label'] ) && $data['form']['label'] !== '' ) ? $data['form']['label'] : __( 'Registration Form', 'pie-register' );
$user_role	= ( $form_id && isset( $button[ 'pie_regis_set_user_role_' . $form_id ] ) ) ? $button[ 'pie_regis_set_user_role_' . $form_id ] : get_option( 'default_role' );
$preview_url = '';
if ( $form_id ) {
	$preview_url = add_query_arg( array( 'pr_preview' => 1, 'form_id' => $form_id, 'prFormId' => $form_id, 'form_name' => str_replace( ' ', '_', $form_label ) ), get_permalink( $button['alternate_register'] ) );
}

/* Palette, same field types as the classic toolbox */
$palette = array(
	'basic' => array(
		'title' => __( 'Basic', 'pie-register' ),
		'items' => array(
			'username' => __( 'Username', 'pie-register' ), 'password' => __( 'Password', 'pie-register' ), 'name' => __( 'Name', 'pie-register' ),
			'url' => __( 'Website', 'pie-register' ), 'description' => __( 'About Yourself', 'pie-register' ), 'text' => __( 'Text Field', 'pie-register' ),
			'textarea' => __( 'Text Area', 'pie-register' ), 'dropdown' => __( 'Drop Down', 'pie-register' ), 'multiselect' => __( 'Multi Select', 'pie-register' ),
			'number' => __( 'Number', 'pie-register' ), 'checkbox' => __( 'Checkbox', 'pie-register' ), 'radio' => __( 'Radio', 'pie-register' ),
			'hidden' => __( 'Hidden', 'pie-register' ), 'html' => __( 'HTML', 'pie-register' ), 'sectionbreak' => __( 'Section Break', 'pie-register' ),
			'pagebreak' => __( 'Page Break', 'pie-register' ),
		),
	),
	'advanced' => array(
		'title' => __( 'Advanced', 'pie-register' ),
		'items' => array(
			'address' => __( 'Address', 'pie-register' ), 'date' => __( 'Date', 'pie-register' ), 'time' => __( 'Time', 'pie-register' ),
			'phone' => __( 'Phone', 'pie-register' ), 'upload' => __( 'Upload File', 'pie-register' ), 'profile_pic' => __( 'Profile Picture', 'pie-register' ),
			'list' => __( 'List', 'pie-register' ), 'custom_role' => __( 'Custom Role', 'pie-register' ), 'pricing' => __( 'Pricing', 'pie-register' ),
			'invitation' => __( 'Invitation Code', 'pie-register' ), 'terms' => __( 'Terms', 'pie-register' ), 'captcha' => __( 'reCAPTCHA', 'pie-register' ),
			'hcaptcha' => __( 'hCaptcha', 'pie-register' ), 'math_captcha' => __( 'Math Captcha', 'pie-register' ), 'honeypot' => __( 'Honeypot', 'pie-register' ),
		),
	),
	'legacy' => array(
		'title' => __( 'Legacy', 'pie-register' ),
		'items' => array(
			'aim' => __( 'AIM', 'pie-register' ), 'yim' => __( 'Yahoo IM', 'pie-register' ), 'jabber' => __( 'Jabber / Google Talk', 'pie-register' ),
		),
	),
);
$type_icons = array(
	'username' => 'admin-users', 'password' => 'lock', 'name' => 'id', 'url' => 'admin-links', 'description' => 'text-page', 'text' => 'editor-textcolor',
	'textarea' => 'editor-paragraph', 'dropdown' => 'arrow-down-alt2', 'multiselect' => 'list-view', 'number' => 'editor-ol', 'checkbox' => 'yes-alt', 'radio' => 'marker',
	'hidden' => 'hidden', 'html' => 'editor-code', 'sectionbreak' => 'minus', 'pagebreak' => 'media-default', 'address' => 'location', 'date' => 'calendar-alt',
	'time' => 'clock', 'phone' => 'phone', 'upload' => 'upload', 'profile_pic' => 'format-image', 'list' => 'editor-table', 'custom_role' => 'groups',
	'pricing' => 'money-alt', 'invitation' => 'tickets-alt', 'terms' => 'media-text', 'captcha' => 'shield', 'hcaptcha' => 'shield-alt', 'math_captcha' => 'calculator',
	'honeypot' => 'privacy', 'aim' => 'format-chat', 'yim' => 'format-chat', 'jabber' => 'format-chat',
);
$type_names = array( 'email' => __( 'E-mail', 'pie-register' ) );
foreach ( $palette as $g ) { $type_names = array_merge( $type_names, $g['items'] ); }

/*
	* Default settings of a new field, read from the same settings form the classic builder used (getDefaultMeta()).
	* What a browser would post for an untouched form is what gets saved: text values, checked boxes, selected or first options.
*/
if ( ! function_exists( 'piereg_nb_meta_defaults' ) ) {
	function piereg_nb_meta_defaults( $html ) {
		$html  = str_replace( '%payment_gateways_list_box%', '', $html );
		$pairs = array();
		$doc   = new DOMDocument();
		$prev  = libxml_use_internal_errors( true );
		$doc->loadHTML( '<?xml encoding="utf-8"?><div>' . $html . '</div>' );
		libxml_clear_errors();
		libxml_use_internal_errors( $prev );
		$xp = new DOMXPath( $doc );
		foreach ( $xp->query( '//input|//select|//textarea' ) as $el ) {
			$name = $el->getAttribute( 'name' );
			if ( strpos( $name, 'field[%d%]' ) !== 0 || $el->hasAttribute( 'disabled' ) ) {
				continue;
			}
			$tag = strtolower( $el->nodeName );
			if ( $tag === 'select' ) {
				$vals = array();
				$opts = $xp->query( './/option', $el );
				foreach ( $opts as $o ) {
					if ( $o->hasAttribute( 'selected' ) ) { $vals[] = $o->hasAttribute( 'value' ) ? $o->getAttribute( 'value' ) : $o->textContent; }
				}
				if ( ! $vals && ! $el->hasAttribute( 'multiple' ) && $opts->length ) {
					$o = $opts->item( 0 );
					$vals[] = $o->hasAttribute( 'value' ) ? $o->getAttribute( 'value' ) : $o->textContent;
				}
				foreach ( $vals as $v ) { $pairs[] = array( $name, $v ); }
				continue;
			}
			if ( $tag === 'textarea' ) {
				$pairs[] = array( $name, $el->textContent );
				continue;
			}
			$type = strtolower( $el->getAttribute( 'type' ) ? $el->getAttribute( 'type' ) : 'text' );
			if ( in_array( $type, array( 'button', 'submit', 'reset', 'file', 'image' ), true ) ) { continue; }
			if ( in_array( $type, array( 'checkbox', 'radio' ), true ) ) {
				if ( ! $el->hasAttribute( 'checked' ) ) { continue; }
				$pairs[] = array( $name, $el->hasAttribute( 'value' ) ? $el->getAttribute( 'value' ) : 'on' );
				continue;
			}
			$pairs[] = array( $name, $el->getAttribute( 'value' ) );
		}
		$q = array();
		foreach ( $pairs as $p ) {
			$q[] = rawurlencode( str_replace( '%d%', 'NBID', $p[0] ) ) . '=' . rawurlencode( str_replace( '%d%', 'NBID', $p[1] ) );
		}
		parse_str( implode( '&', $q ), $arr );
		return ( isset( $arr['field']['NBID'] ) && is_array( $arr['field']['NBID'] ) ) ? $arr['field']['NBID'] : array();
	}
}

/* Legacy fields and "About Yourself" are saved with type "default", as the classic builder did */
$as_default = array( 'aim', 'yim', 'jabber', 'description' );
$meta       = $this->getDefaultMeta();
$defaults   = array();
foreach ( $palette as $g ) {
	foreach ( $g['items'] as $type => $tname ) {
		if ( empty( $meta[ $type ] ) ) { continue; }
		$d = piereg_nb_meta_defaults( $meta[ $type ] );
		$d['id']   = 'NBID';
		$d['type'] = in_array( $type, $as_default, true ) ? 'default' : $type;
		if ( in_array( $type, $as_default, true ) ) { $d['field_name'] = $type; } // as the classic builder did
		$defaults[ $type ] = $d;
	}
}

/* Fields the classic toolbox only offered when a feature is switched on */
$unavailable = array();
if ( ! ( ( isset( $button['enable_paypal'] ) && $button['enable_paypal'] == 1 ) || $this->check_payment_plugin_activation() == 'true' ) ) {
	$unavailable['pricing'] = __( 'Turn on a payment method first (Payment Gateways).', 'pie-register' );
}
if ( empty( $button['enable_invitation_codes'] ) || $button['enable_invitation_codes'] != 1 ) {
	$unavailable['invitation'] = __( 'Turn on invitation codes first (Invitation Codes).', 'pie-register' );
}
if ( function_exists( 'piereg_captcha_enabled' ) && ! piereg_captcha_enabled( $button ) ) {
	$unavailable['captcha'] = __( 'Turn on reCAPTCHA first (Settings > Security).', 'pie-register' );
}
/* hCaptcha and Honeypot are premium-only (the classic free builder never offered them) */
$unavailable['hcaptcha'] = $unavailable['honeypot'] = __( 'Available in the premium version.', 'pie-register' );
$addable = array_values( array_diff( array_keys( $defaults ), array_keys( $unavailable ) ) );

/* Types a form may hold only once (as in the classic builder) */
$single		= array( 'username', 'password', 'email', 'name', 'url', 'description', 'two_way_login_phone', 'profile_pic', 'custom_role', 'pricing', 'invitation', 'terms', 'captcha', 'hcaptcha', 'math_captcha', 'honeypot', 'aim', 'yim', 'jabber' );
/* Fields the save handler requires; they cannot be deleted here */
$locked		= array( 'email' );

/* Split the saved fields into steps at each page break; remember each page break's own settings */
$steps		= array( array( 'break' => null, 'fields' => array() ) );
$present	= array();
$max_id		= 0;
foreach ( $data as $key => $field ) {
	if ( ! is_array( $field ) || empty( $field['type'] ) || in_array( (string) $key, array( 'form', 'submit' ), true ) || in_array( $field['type'], array( 'form', 'submit' ), true ) ) {
		continue;
	}
	$max_id = max( $max_id, (int) $key, isset( $field['id'] ) ? (int) $field['id'] : 0 );
	if ( in_array( $field['type'], array( 'honeypot', 'hcaptcha' ), true ) ) {
		continue; // not shown in the free builder (same as the classic free builder)
	}
	$field['_key'] = (string) $key;
	if ( $field['type'] === 'pagebreak' ) {
		$steps[] = array( 'break' => $field, 'fields' => array() );
		continue;
	}
	$field['_ptype'] = $field['type'];
	if ( $field['type'] === 'default' && ! empty( $field['field_name'] ) && isset( $type_names[ $field['field_name'] ] ) ) {
		$field['_ptype'] = $field['field_name'];
	} elseif ( $field['type'] === 'default' ) {
		foreach ( array( 'aim', 'yim', 'jabber', 'description' ) as $lt ) {
			if ( isset( $field['label'], $type_names[ $lt ] ) && strcasecmp( $field['label'], $type_names[ $lt ] ) === 0 ) { $field['_ptype'] = $lt; }
		}
	}
	$present[] = $field['_ptype'];
	$steps[ count( $steps ) - 1 ]['fields'][] = $field;
}

/* Hidden inputs that post one saved value (arrays become field[ID][key][sub]...) */
if ( ! function_exists( 'piereg_nb_hidden' ) ) {
	function piereg_nb_hidden( $name, $value ) {
		if ( is_array( $value ) ) {
			$out = '';
			foreach ( $value as $k => $v ) {
				$out .= piereg_nb_hidden( $name . '[' . $k . ']', $v );
			}
			return $out;
		}
		// htmlspecialchars with double encoding (esc_attr keeps existing entities), so a saved "&amp;" is posted back as "&amp;"
		return '<input type="hidden" name="' . esc_attr( $name ) . '" value="' . htmlspecialchars( (string) $value, ENT_QUOTES, 'UTF-8', true ) . '" />';
	}
}
if ( ! function_exists( 'piereg_nb_field_inputs' ) ) {
	function piereg_nb_field_inputs( $key, $field ) {
		$out = '';
		foreach ( $field as $k => $v ) {
			if ( $k === '_key' || $k === '_ptype' ) { continue; }
			$out .= piereg_nb_hidden( 'field[' . $key . '][' . $k . ']', $v );
		}
		return $out;
	}
}

/* A front-end looking preview of one field (display only) */
if ( ! function_exists( 'piereg_nb_preview' ) ) {
	function piereg_nb_preview( $f ) {
		$ph = isset( $f['placeholder'] ) ? $f['placeholder'] : '';
		$opts = ( isset( $f['display'] ) && is_array( $f['display'] ) ) ? $f['display'] : array();
		switch ( $f['type'] ) {
			case 'textarea': case 'description':
				return '<div class="nb-in nb-area">' . esc_html( $ph ) . '</div>';
			case 'dropdown': case 'multiselect': case 'custom_role':
				return '<div class="nb-in nb-select">' . esc_html( isset( $opts[0] ) ? $opts[0] : __( 'Select…', 'pie-register' ) ) . '</div>';
			case 'checkbox': case 'radio':
				$o = '';
				foreach ( array_slice( $opts, 0, 4 ) as $opt ) { $o .= '<span class="nb-opt nb-' . esc_attr( $f['type'] ) . '">' . esc_html( $opt ) . '</span>'; }
				return '<div class="nb-opts">' . ( $o ? $o : '<span class="nb-opt nb-' . esc_attr( $f['type'] ) . '">' . esc_html__( 'Option', 'pie-register' ) . '</span>' ) . '</div>';
			case 'password':
				/* Confirm box shown unless "Hide Confirm Password" is ticked, as on the front end. Each box shows its placeholder when one is set. */
				$out = '<div class="nb-in">' . ( '' !== $ph ? esc_html( $ph ) : '••••••••' ) . '</div>';
				if ( ! isset( $f['hide_confirm_password'] ) ) {
					$ph2  = isset( $f['placeholder2'] ) ? $f['placeholder2'] : '';
					$out .= '<label class="nb-label nb-label2">' . esc_html( ! empty( $f['label2'] ) ? $f['label2'] : __( 'Confirm Password', 'pie-register' ) ) . '</label><div class="nb-in">' . ( '' !== $ph2 ? esc_html( $ph2 ) : '••••••••' ) . '</div>';
				}
				return $out;
			case 'email':
				/* Confirm box shown when "Confirm E-mail" is ticked */
				$out = '<div class="nb-in">' . esc_html( $ph ) . '&nbsp;</div>';
				if ( ! empty( $f['confirm_email'] ) ) {
					$out .= '<label class="nb-label nb-label2">' . esc_html( ! empty( $f['label2'] ) ? $f['label2'] : __( 'Confirm Email', 'pie-register' ) ) . '</label><div class="nb-in">' . esc_html( isset( $f['placeholder2'] ) ? $f['placeholder2'] : '' ) . '&nbsp;</div>';
				}
				return $out;
			case 'name':
				/* As on the front end: Placeholder in the first name box; Label2 and Placeholder 2 on the last name box */
				$first = '<div class="nb-in">' . esc_html( $ph ) . '&nbsp;</div>';
				if ( isset( $f['hide_last_name'] ) ) {
					return $first;
				}
				$label2 = ! empty( $f['label2'] ) ? '<label class="nb-label nb-label2">' . esc_html( $f['label2'] ) . '</label>' : '';
				$ph2    = isset( $f['placeholder2'] ) ? $f['placeholder2'] : '';
				return '<div class="nb-row nb-name-row"><div class="nb-col">' . $first . '</div><div class="nb-col">' . $label2 . '<div class="nb-in">' . esc_html( $ph2 ) . '&nbsp;</div></div></div>';
			case 'address':
				/* Same boxes as the front end: Address Line 2, State and Country follow "Hide Address 2", "Hide State" and "List Type" */
				$type = isset( $f['address_type'] ) ? $f['address_type'] : '';
				$city = '<div class="nb-in">' . esc_html__( 'City', 'pie-register' ) . '</div>';
				$zip  = '<div class="nb-in">' . esc_html__( 'Zip / Postal Code', 'pie-register' ) . '</div>';
				if ( empty( $f['hide_state'] ) ) {
					if ( $type === 'International' ) {
						$city .= '<div class="nb-in">' . esc_html__( 'State / Province / Region', 'pie-register' ) . '</div>';
					} elseif ( $type === 'United States' ) {
						$city .= '<div class="nb-in nb-select">' . esc_html( ! empty( $f['us_default_state'] ) ? $f['us_default_state'] : __( 'State', 'pie-register' ) ) . '</div>';
					} elseif ( $type === 'Canada' ) {
						$city .= '<div class="nb-in nb-select">' . esc_html( ! empty( $f['canada_default_state'] ) ? $f['canada_default_state'] : __( 'Province', 'pie-register' ) ) . '</div>';
					}
				}
				if ( $type === 'International' ) {
					$zip .= '<div class="nb-in nb-select">' . esc_html( ! empty( $f['default_country'] ) ? $f['default_country'] : __( 'Country', 'pie-register' ) ) . '</div>';
				}
				$rows = array( '<div class="nb-row">' . $city . '</div>', '<div class="nb-row">' . $zip . '</div>' );
				if ( isset( $f['address_order'] ) && $f['address_order'] === 'zip_city' ) {
					$rows = array_reverse( $rows );
				}
				$out = '<div class="nb-in">' . esc_html__( 'Street Address', 'pie-register' ) . '</div>';
				if ( empty( $f['hide_address2'] ) ) {
					$out .= '<div class="nb-row"><div class="nb-in">' . esc_html__( 'Address Line 2', 'pie-register' ) . '</div></div>';
				}
				return $out . implode( '', $rows );
			case 'upload': case 'profile_pic':
				return '<div class="nb-in nb-file">' . esc_html__( 'Choose file…', 'pie-register' ) . '</div>';
			case 'terms':
				return '<span class="nb-opt nb-checkbox">' . esc_html__( 'I agree to the terms', 'pie-register' ) . '</span>';
			case 'captcha': case 'hcaptcha':
				return '<div class="nb-in nb-captcha">' . esc_html__( "I'm not a robot", 'pie-register' ) . '</div>';
			case 'math_captcha':
				/* The sum, then the answer box with its Placeholder, as on the front end */
				return '<div class="nb-math">3 + 4 = ?</div><div class="nb-in nb-math-answer">' . esc_html( $ph ) . '&nbsp;</div>';
			case 'honeypot': case 'hidden':
				return '<div class="nb-in nb-ghost">' . esc_html__( 'Hidden from visitors', 'pie-register' ) . '</div>';
			case 'html':
				return '<div class="nb-html">' . esc_html__( 'HTML content', 'pie-register' ) . '</div>';
			case 'sectionbreak':
				return '<hr class="nb-hr" />';
			case 'list':
				return '<div class="nb-in nb-ghost">' . esc_html__( 'Row 1 · + Add another', 'pie-register' ) . '</div>';
			default:
				return '<div class="nb-in">' . esc_html( $ph ) . '&nbsp;</div>';
		}
	}
}

/* Inspector data for one field (read-only in step 2) */
if ( ! function_exists( 'piereg_nb_info' ) ) {
	function piereg_nb_info( $f, $label, $type_names, $data ) {
		$logic = ! empty( $f['conditional_logic'] ) && (string) $f['conditional_logic'] === '1';
		$info = array(
			'type'		=> isset( $type_names[ $f['type'] ] ) ? $type_names[ $f['type'] ] : $f['type'],
			'label'		=> $label,
			'required'	=> ! empty( $f['required'] ) ? __( 'Yes', 'pie-register' ) : __( 'No', 'pie-register' ),
			'placeholder' => isset( $f['placeholder'] ) ? $f['placeholder'] : '',
			'logic'		=> $logic ? __( 'Yes', 'pie-register' ) : __( 'No', 'pie-register' ),
			'desc'		=> isset( $f['desc'] ) ? wp_strip_all_tags( $f['desc'] ) : '',
			'rule'		=> isset( $f['validation_rule'] ) ? $f['validation_rule'] : '',
			'message'	=> isset( $f['validation_message'] ) ? $f['validation_message'] : '',
			'css'		=> isset( $f['css'] ) ? $f['css'] : '',
			'profile'	=> ( ! isset( $f['show_in_profile'] ) || (string) $f['show_in_profile'] === '1' ) ? __( 'Yes', 'pie-register' ) : __( 'No', 'pie-register' ),
			'show'		=> ( isset( $f['field_status'] ) && (string) $f['field_status'] === '0' ) ? __( 'Hide', 'pie-register' ) : __( 'Show', 'pie-register' ),
			'rules'		=> array(),
		);
		if ( $logic && ! empty( $f['conditional'] ) && is_array( $f['conditional'] ) ) {
			foreach ( $f['conditional'] as $group ) {
				$parts = array();
				foreach ( (array) $group as $r ) {
					if ( ! is_array( $r ) ) { continue; }
					$sf  = isset( $r['selected_field'] ) ? $r['selected_field'] : '';
					$sfl = ( $sf !== '' && isset( $data[ $sf ]['label'] ) ) ? $data[ $sf ]['label'] : $sf;
					$parts[] = trim( $sfl . ' ' . ( isset( $r['field_rule_operator'] ) ? $r['field_rule_operator'] : '' ) . ' ' . ( isset( $r['conditional_value'] ) ? $r['conditional_value'] : '' ) );
				}
				if ( $parts ) { $info['rules'][] = implode( ' AND ', $parts ); }
			}
		}
		return $info;
	}
}

$cfg = array(
	'maxId'		=> $max_id,
	'addable'	=> $addable,
	'defaults'	=> $defaults,
	'meta'		=> array_intersect_key( $meta, $type_names ), // the classic settings form of each field type, used by the settings panel
	'unavail'	=> $unavailable,
	'single'	=> $single,
	'locked'	=> $locked,
	'present'	=> array_values( array_unique( $present ) ),
	'names'		=> $type_names,
);
$msg_ok		= ! empty( $this->pie_post_array['success_message'] ) ? $this->pie_post_array['success_message'] : '';
$msg_err	= ! empty( $this->pie_post_array['error_message'] ) ? $this->pie_post_array['error_message'] : '';
?>
<div class="pieregister-admin piereg-nb" id="piereg-nb" data-cfg="<?php echo esc_attr( wp_json_encode( $cfg ) ); ?>">
<form method="post" action="" id="nb-form">
	<?php wp_nonce_field( 'piereg_wp_reg_form_nonce', 'piereg_reg_form_nonce' ); ?>
	<?php if ( $form_id ) : ?>
		<input type="hidden" name="form_id" value="<?php echo esc_attr( base64_encode( $form_id ) ); ?>" />
		<input type="hidden" name="page" value="edit" />
	<?php endif; ?>
	<input type="hidden" name="pie_form" value="1" />
	<?php
	// Form settings and the submit button: settings edited in their panels (right column) are left out here, everything else is posted back unchanged
	$form_settings	= ( isset( $data['form'] ) && is_array( $data['form'] ) ) ? $data['form'] : array();
	$form_settings['type'] = 'form'; // the save handler needs the form, e-mail and submit types
	$form_edit		= array( 'label', 'display_form_name', 'desc', 'css', 'label_alignment', 'user_verification' );
	if ( $this->piereg_pro_is_activate ) {
		$form_edit = array_merge( $form_edit, array( 'conditional_logic', 'notification', 'selected_field', 'field_rule_operator', 'conditional_value' ) );
	}
	echo piereg_nb_field_inputs( 'form', array_diff_key( $form_settings, array_flip( $form_edit ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in piereg_nb_hidden()
	$submit_settings	= ( isset( $data['submit'] ) && is_array( $data['submit'] ) ) ? $data['submit'] : array( 'label' => 'Submit', 'text' => 'Submit' );
	$submit_settings['type'] = 'submit';
	$submit_edit		= array( 'text', 'reset', 'reset_text', 'confirmation', 'redirect_url', 'page', 'message' );

	// "Display form name": same rule as the classic builder (on for a new form with a title)
	$show_form_name = ( isset( $data['form']['display_form_name'] ) && $data['form']['display_form_name'] == '1' ) || ( ! $form_id && ! empty( $data['form']['label'] ) );
	$fs = function ( $key, $default = '' ) use ( $form_settings ) { return isset( $form_settings[ $key ] ) ? $form_settings[ $key ] : $default; };
	$ss = function ( $key, $default = '' ) use ( $submit_settings ) { return isset( $submit_settings[ $key ] ) ? $submit_settings[ $key ] : $default; };
	$progress_label = $fs( 'progress_label' );
	?>

	<div class="nb-top">
		<a class="nb-back" href="<?php echo esc_url( admin_url( 'admin.php?page=pie-register' ) ); ?>">&lsaquo; <?php esc_html_e( 'Forms', 'pie-register' ); ?></a>
		<input type="text" class="nb-name" id="nb-title" name="field[form][label]" value="<?php echo esc_attr( isset( $data['form']['label'] ) ? $data['form']['label'] : '' ); ?>" placeholder="<?php esc_attr_e( 'Form title', 'pie-register' ); ?>" aria-label="<?php esc_attr_e( 'Form title', 'pie-register' ); ?>" required="required" maxlength="200" />
		<span class="nb-sp"></span>
		<span class="nb-dev"><button type="button" class="on" data-dev="desktop"><?php esc_html_e( 'Desktop', 'pie-register' ); ?></button><button type="button" data-dev="mobile"><?php esc_html_e( 'Mobile', 'pie-register' ); ?></button></span>
		<span class="nb-status<?php echo $msg_err ? ' nb-bad' : ( $msg_ok ? ' nb-good' : '' ); ?>" id="nb-status" aria-live="polite"><?php echo esc_html( $msg_err ? $msg_err : ( $msg_ok ? $msg_ok : __( 'All changes saved', 'pie-register' ) ) ); ?></span>
		<button type="button" class="nb-btn nb-ghostbtn" id="nb-undo" disabled="disabled"><?php esc_html_e( 'Undo', 'pie-register' ); ?></button>
		<?php if ( $preview_url ) : ?>
			<a class="nb-btn nb-ghostbtn" href="<?php echo esc_url( $preview_url ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Preview', 'pie-register' ); ?></a>
		<?php endif; ?>
		<button type="button" class="nb-btn nb-ghostbtn" id="nb-formset"><span class="dashicons dashicons-admin-generic" aria-hidden="true"></span> <?php esc_html_e( 'Form Settings', 'pie-register' ); ?></button>
		<button type="submit" class="nb-btn nb-save" id="nb-save"><?php esc_html_e( 'Save', 'pie-register' ); ?></button>
		<?php do_action( 'piereg_builder_header' ); ?>
	</div>

	<div class="nb-body">
		<aside class="nb-left">
			<div class="nb-search"><input type="search" id="nb-q" placeholder="<?php esc_attr_e( 'Search fields…', 'pie-register' ); ?>" aria-label="<?php esc_attr_e( 'Search fields', 'pie-register' ); ?>" /></div>
			<?php foreach ( $palette as $gk => $g ) : ?>
				<div class="nb-group<?php echo $gk === 'legacy' ? ' nb-collapsed' : ''; ?>">
					<h5 class="nb-gh" tabindex="0"><?php echo esc_html( $g['title'] ); ?><?php if ( $gk === 'legacy' ) { echo ' <small>(' . esc_html( count( $g['items'] ) ) . ')</small>'; } ?></h5>
					<div class="nb-grid">
						<?php foreach ( $g['items'] as $type => $name ) : ?>
							<div class="nb-tile" data-type="<?php echo esc_attr( $type ); ?>" tabindex="0" role="button"><span class="nb-ico dashicons dashicons-<?php echo esc_attr( isset( $type_icons[ $type ] ) ? $type_icons[ $type ] : 'admin-generic' ); ?>" aria-hidden="true"></span><?php echo esc_html( $name ); ?></div>
						<?php endforeach; ?>
					</div>
				</div>
			<?php endforeach; ?>
			<p class="nb-nomatch" style="display:none;"><?php esc_html_e( 'No matching fields', 'pie-register' ); ?></p>
			<p class="nb-hint"><?php esc_html_e( 'Click a field, or drag it onto the form.', 'pie-register' ); ?></p>
		</aside>

		<main class="nb-center">
			<div class="nb-steps" role="tablist" id="nb-steps"></div>
			<div class="nb-canvas" id="nb-canvas">
				<div class="nb-formhead<?php echo $show_form_name ? '' : ' nb-title-off'; ?>" id="nb-formhead" tabindex="0" role="button" title="<?php esc_attr_e( 'Form Settings', 'pie-register' ); ?>">
					<h3 class="nb-form-title" id="nb-canvas-title"><?php echo esc_html( $form_label ); ?></h3>
					<span class="nb-chip nb-title-chip"><?php esc_html_e( 'name hidden on the form', 'pie-register' ); ?></span>
					<p class="nb-form-desc" id="nb-canvas-desc"<?php echo empty( $data['form']['desc'] ) ? ' style="display:none;"' : ''; ?>><?php echo esc_html( wp_strip_all_tags( html_entity_decode( stripslashes( (string) $fs( 'desc' ) ) ) ) ); ?></p>
				</div>
				<div class="nb-progress" id="nb-progress" data-label="<?php echo esc_attr( $progress_label ); ?>"><div class="nb-progress-bar"></div><span class="nb-progress-text"></span></div>
				<div id="nb-pages">
				<?php foreach ( $steps as $i => $s ) : ?>
					<div class="nb-page" data-step="<?php echo esc_attr( $i ); ?>"<?php echo $i ? ' style="display:none;"' : ''; ?>>
						<?php if ( $s['break'] ) : ?>
							<div class="nb-break" data-type="pagebreak" data-key="<?php echo esc_attr( $s['break']['_key'] ); ?>"><div class="nb-inputs"><?php echo piereg_nb_field_inputs( $s['break']['_key'], $s['break'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in piereg_nb_hidden() ?></div></div>
						<?php endif; ?>
						<?php foreach ( $s['fields'] as $f ) :
							$label = ( isset( $f['label'] ) && $f['label'] !== '' ) ? $f['label'] : ( isset( $type_names[ $f['type'] ] ) ? $type_names[ $f['type'] ] : $f['type'] );
							$logic = ! empty( $f['conditional_logic'] ) && (string) $f['conditional_logic'] === '1';
						?>
							<div class="nb-field" tabindex="0" data-type="<?php echo esc_attr( $f['_ptype'] ); ?>" data-key="<?php echo esc_attr( $f['_key'] ); ?>" data-info="<?php echo esc_attr( wp_json_encode( piereg_nb_info( $f, $label, $type_names, $data ) ) ); ?>">
								<label class="nb-label"><?php echo esc_html( $label ); ?><?php if ( ! empty( $f['required'] ) ) : ?> <span class="nb-req">*</span><?php endif; ?><?php if ( $logic ) : ?><span class="nb-chip"><?php esc_html_e( 'logic', 'pie-register' ); ?></span><?php endif; ?></label>
								<div class="nb-pv"><?php echo piereg_nb_preview( $f ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- every part is escaped inside piereg_nb_preview() ?></div>
								<?php if ( ! empty( $f['desc'] ) ) : ?><div class="nb-help"><?php echo esc_html( wp_strip_all_tags( $f['desc'] ) ); ?></div><?php endif; ?>
								<div class="nb-inputs"><?php echo piereg_nb_field_inputs( $f['_key'], $f ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in piereg_nb_hidden() ?></div>
							</div>
						<?php endforeach; ?>
						<div class="nb-empty"<?php echo empty( $s['fields'] ) ? '' : ' style="display:none;"'; ?>><?php esc_html_e( 'Drop fields here, or click a field on the left.', 'pie-register' ); ?></div>
					</div>
				<?php endforeach; ?>
				</div>
				<div class="nb-nav"><span class="nb-fbtn nb-prev"><?php esc_html_e( 'Previous', 'pie-register' ); ?></span><span class="nb-fbtn nb-next"><?php esc_html_e( 'Next', 'pie-register' ); ?></span><span class="nb-fbtn nb-reset"<?php echo (string) $ss( 'reset' ) === '1' ? '' : ' style="display:none;"'; ?>><?php echo esc_html( $ss( 'reset_text', __( 'Reset', 'pie-register' ) ) ); ?></span><span class="nb-fbtn nb-submit" tabindex="0" role="button" title="<?php esc_attr_e( 'Submit Button Settings', 'pie-register' ); ?>"><?php echo esc_html( $ss( 'text' ) !== '' ? $ss( 'text' ) : __( 'Register', 'pie-register' ) ); ?></span></div>
				<?php // after every field, so the submit button stays last in the saved form (the front end prints in saved order)
				echo piereg_nb_field_inputs( 'submit', array_diff_key( $submit_settings, array_flip( $submit_edit ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in piereg_nb_hidden() ?>
			</div>
		</main>

		<aside class="nb-right">
			<div class="nb-rtabs" role="tablist"><button type="button" class="on" data-tab="basic"><?php esc_html_e( 'Settings', 'pie-register' ); ?></button></div><?php // no Conditional Logic tab: conditional logic is a premium feature ?>
			<div class="nb-rbody" id="nb-insp"></div>

			<?php
			/* Form settings: same settings and names as the classic builder's "Edit Form" box (menus/PieRegEditForm.php) */
			$verifications = array(
				'0' => __( 'Use Default', 'pie-register' ),
				'1' => __( 'Admin Verification', 'pie-register' ),
				'2' => __( 'E-mail Verification', 'pie-register' ),
				'3' => __( 'E-mail & Admin Verification', 'pie-register' ),
				'4' => __( 'None', 'pie-register' ),
			);
			$operators = array(
				'==' => __( 'equal', 'pie-register' ), '!=' => __( 'not equal', 'pie-register' ), 'empty' => __( 'empty', 'pie-register' ),
				'not_empty' => __( 'not empty', 'pie-register' ), '>' => __( 'greater than', 'pie-register' ), '<' => __( 'less than', 'pie-register' ),
				'contains' => __( 'contains', 'pie-register' ), 'not_contains' => __( 'does not contain', 'pie-register' ),
				'starts_with' => __( 'starts with', 'pie-register' ), 'not_starts_with' => __( 'does not start with', 'pie-register' ),
				'ends_with' => __( 'ends with', 'pie-register' ), 'not_ends_with' => __( 'does not end with', 'pie-register' ),
			);
			// Fields a verification rule can test, by the name they are posted with (same list the classic builder offers)
			$post_names = array();
			foreach ( $data as $fk => $fv ) {
				if ( ! is_array( $fv ) || empty( $fv['type'] ) || in_array( $fv['type'], array( 'form', 'submit', 'pagebreak', 'sectionbreak', 'html', 'captcha', 'hcaptcha', 'math_captcha', 'honeypot', 'password', 'upload', 'profile_pic', 'list' ), true ) ) { continue; }
				$fl = ( isset( $fv['label'] ) && $fv['label'] !== '' ) ? $fv['label'] : $fv['type'];
				if ( $fv['type'] === 'name' ) {
					$post_names['first_name'] = $fl;
					$post_names['last_name']  = ( isset( $fv['label2'] ) && $fv['label2'] !== '' ) ? $fv['label2'] : __( 'Last Name', 'pie-register' );
					continue;
				}
				$pn = $fv['type'] === 'invitation' ? 'invitation' : $this->getPieregFieldName( $fv, $fk ); // the code field is posted as "invitation"
				if ( $pn !== '' ) { $post_names[ $pn ] = $fl; }
			}
			$rules = ( isset( $form_settings['notification'] ) && is_array( $form_settings['notification'] ) && $form_settings['notification'] ) ? array_keys( $form_settings['notification'] ) : array( 0 );
			$rule_val = function ( $key, $i, $default = '' ) use ( $form_settings ) { return isset( $form_settings[ $key ][ $i ] ) ? $form_settings[ $key ][ $i ] : $default; };
			?>
			<div class="nb-rbody nb-editor nb-panel" id="nb-form-panel" hidden>
				<div class="nb-ptitle"><?php esc_html_e( 'Form Settings', 'pie-register' ); ?></div>
				<div class="advance_fields">
					<input type="checkbox" value="1" name="field[form][display_form_name]" id="nb-display-name" <?php checked( $show_form_name ); ?> /><label for="nb-display-name"><?php esc_html_e( 'Display form name', 'pie-register' ); ?></label>
				</div>
				<div class="advance_fields">
					<label for="nb-form-desc"><?php esc_html_e( 'Description', 'pie-register' ); ?></label>
					<textarea name="field[form][desc]" id="nb-form-desc" rows="4"><?php echo esc_textarea( html_entity_decode( stripslashes( (string) $fs( 'desc' ) ) ) ); ?></textarea>
				</div>
				<div class="advance_fields">
					<label for="nb-form-css"><?php esc_html_e( 'CSS Class Name', 'pie-register' ); ?></label>
					<input type="text" name="field[form][css]" id="nb-form-css" value="<?php echo esc_attr( $fs( 'css' ) ); ?>" />
				</div>
				<div class="advance_fields">
					<label for="nb-form-align"><?php esc_html_e( 'Label Alignment', 'pie-register' ); ?></label>
					<select name="field[form][label_alignment]" id="nb-form-align">
						<option value="top" <?php selected( $fs( 'label_alignment', 'top' ), 'top' ); ?>><?php esc_html_e( 'Top', 'pie-register' ); ?></option>
						<option value="left" <?php selected( $fs( 'label_alignment' ), 'left' ); ?>><?php esc_html_e( 'Left', 'pie-register' ); ?></option>
					</select>
				</div>
				<div class="advance_fields">
					<label for="nb-user-role"><?php esc_html_e( 'Registering User Role', 'pie-register' ); ?></label>
					<select name="set_user_role_" id="nb-user-role">
						<?php
						global $wp_roles;
						$cur_role = $user_role ? $user_role : 'subscriber';
						foreach ( $wp_roles->roles as $rk => $rv ) {
							echo '<option value="' . esc_attr( $rk ) . '"' . selected( $cur_role, $rk, false ) . '>' . esc_html( translate_user_role( $rv['name'] ) ) . '</option>';
						}
						?>
					</select>
				</div>
				<div class="advance_fields">
					<label for="nb-user-verify"><?php esc_html_e( 'User Verification', 'pie-register' ); ?></label>
					<select name="field[form][user_verification]" id="nb-user-verify">
						<option value="0" <?php selected( (string) $fs( 'user_verification', '0' ), '0' ); ?>><?php esc_html_e( 'Use Default', 'pie-register' ); ?></option>
						<option value="4" <?php selected( (string) $fs( 'user_verification' ), '4' ); ?>><?php esc_html_e( 'No Verification', 'pie-register' ); ?></option>
						<option value="1" <?php selected( (string) $fs( 'user_verification' ), '1' ); ?>><?php echo esc_html( $verifications['1'] ); ?></option>
						<option value="2" <?php selected( (string) $fs( 'user_verification' ), '2' ); ?>><?php echo esc_html( $verifications['2'] ); ?></option>
						<?php if ( $this->piereg_pro_is_activate ) : ?>
						<option value="3" <?php selected( (string) $fs( 'user_verification' ), '3' ); ?>><?php echo esc_html( $verifications['3'] ); ?></option>
						<?php endif; ?>
					</select>
				</div>
				<?php if ( $this->piereg_pro_is_activate ) : ?>
				<div class="advance_fields">
					<label for="nb-form-logic"><?php esc_html_e( 'Verification by Conditional Logic', 'pie-register' ); ?></label>
					<select name="field[form][conditional_logic]" id="nb-form-logic">
						<option value="0" <?php selected( (string) $fs( 'conditional_logic', '0' ) !== '1' ); ?>><?php esc_html_e( 'No', 'pie-register' ); ?></option>
						<option value="1" <?php selected( (string) $fs( 'conditional_logic' ), '1' ); ?>><?php esc_html_e( 'Yes', 'pie-register' ); ?></option>
					</select>
					<a class="info"><?php esc_html_e( 'Choose the verification from what the visitor entered. The first matching rule is used.', 'pie-register' ); ?></a>
				</div>
				<div class="nb-vrules" id="nb-vrules"<?php echo (string) $fs( 'conditional_logic' ) === '1' ? '' : ' hidden'; ?>>
					<?php foreach ( $rules as $ri ) :
						$sel_field = (string) $rule_val( 'selected_field', $ri );
						?>
					<div class="nb-lgroup nb-vrule">
						<button type="button" class="nb-v-x" title="<?php esc_attr_e( 'Remove rule', 'pie-register' ); ?>">&times;</button>
						<label><?php esc_html_e( 'Use', 'pie-register' ); ?></label>
						<select name="field[form][notification][]">
							<?php foreach ( $verifications as $vk => $vl ) : ?>
							<option value="<?php echo esc_attr( $vk ); ?>" <?php selected( (string) $rule_val( 'notification', $ri, '0' ), (string) $vk ); ?>><?php echo esc_html( $vl ); ?></option>
							<?php endforeach; ?>
						</select>
						<label><?php esc_html_e( 'when', 'pie-register' ); ?></label>
						<select name="field[form][selected_field][]" class="nb-v-field" data-value="<?php echo esc_attr( $sel_field ); ?>">
							<option value=""><?php esc_html_e( '- Select field -', 'pie-register' ); ?></option>
							<?php foreach ( $post_names as $pn => $pl ) : ?>
							<option value="<?php echo esc_attr( $pn ); ?>" <?php selected( $sel_field, (string) $pn ); ?>><?php echo esc_html( $pl ); ?></option>
							<?php endforeach; ?>
						</select>
						<select name="field[form][field_rule_operator][]" class="nb-v-op">
							<?php foreach ( $operators as $ok => $ol ) : ?>
							<option value="<?php echo esc_attr( $ok ); ?>" <?php selected( html_entity_decode( (string) $rule_val( 'field_rule_operator', $ri, '==' ) ), $ok ); ?>><?php echo esc_html( $ol ); ?></option>
							<?php endforeach; ?>
						</select>
						<input type="text" name="field[form][conditional_value][]" class="nb-v-val" value="<?php echo esc_attr( $rule_val( 'conditional_value', $ri ) ); ?>" placeholder="<?php esc_attr_e( 'Enter Value', 'pie-register' ); ?>" />
					</div>
					<?php endforeach; ?>
					<button type="button" class="nb-l-or nb-v-add">+ <?php esc_html_e( 'Add rule', 'pie-register' ); ?></button>
				</div>
				<?php endif; ?>
			</div>

			<?php /* Submit button: same settings as the classic builder's "Edit Button" box */ ?>
			<div class="nb-rbody nb-editor nb-panel" id="nb-submit-panel" hidden>
				<div class="nb-ptitle"><?php esc_html_e( 'Submit Button', 'pie-register' ); ?></div>
				<div class="advance_fields">
					<label for="nb-submit-text"><?php esc_html_e( 'Submit Button Text', 'pie-register' ); ?></label>
					<input type="text" name="field[submit][text]" id="nb-submit-text" value="<?php echo esc_attr( $ss( 'text' ) ); ?>" />
				</div>
				<div class="advance_fields">
					<label for="nb-submit-reset"><?php esc_html_e( 'Show Reset Button', 'pie-register' ); ?></label>
					<select name="field[submit][reset]" id="nb-submit-reset">
						<option value="0" <?php selected( (string) $ss( 'reset', '0' ) !== '1' ); ?>><?php esc_html_e( 'No', 'pie-register' ); ?></option>
						<option value="1" <?php selected( (string) $ss( 'reset' ), '1' ); ?>><?php esc_html_e( 'Yes', 'pie-register' ); ?></option>
					</select>
				</div>
				<div class="advance_fields nb-dep-reset">
					<label for="nb-submit-reset-text"><?php esc_html_e( 'Reset Button Text', 'pie-register' ); ?></label>
					<input type="text" name="field[submit][reset_text]" id="nb-submit-reset-text" value="<?php echo esc_attr( $ss( 'reset_text' ) ); ?>" />
				</div>
				<div class="advance_fields">
					<label><?php esc_html_e( 'Confirmation Message', 'pie-register' ); ?></label>
					<?php $confirm = $ss( 'confirmation', 'text' ); ?>
					<input type="radio" value="text" name="field[submit][confirmation]" id="nb-conf-text" <?php checked( $confirm, 'text' ); ?> /><label for="nb-conf-text"><?php esc_html_e( 'Text', 'pie-register' ); ?></label>
					<input type="radio" value="page" name="field[submit][confirmation]" id="nb-conf-page" <?php checked( $confirm, 'page' ); ?> /><label for="nb-conf-page"><?php esc_html_e( 'Page', 'pie-register' ); ?></label>
					<input type="radio" value="redirect" name="field[submit][confirmation]" id="nb-conf-redirect" <?php checked( $confirm, 'redirect' ); ?> /><label for="nb-conf-redirect"><?php esc_html_e( 'Redirect', 'pie-register' ); ?></label>
				</div>
				<div class="advance_fields nb-dep-conf" data-conf="redirect">
					<label for="nb-submit-url"><?php esc_html_e( 'Redirect URL', 'pie-register' ); ?></label>
					<input type="text" name="field[submit][redirect_url]" id="nb-submit-url" value="<?php echo esc_attr( $ss( 'redirect_url' ) ); ?>" />
				</div>
				<div class="advance_fields nb-dep-conf" data-conf="page">
					<label for="nb-submit-page"><?php esc_html_e( 'Select Page', 'pie-register' ); ?></label>
					<?php
					echo wp_kses(
						wp_dropdown_pages( array( 'name' => 'field[submit][page]', 'id' => 'nb-submit-page', 'selected' => (int) $ss( 'page' ), 'echo' => false ) ),
						array( 'select' => array( 'name' => array(), 'id' => array(), 'class' => array() ), 'option' => array( 'value' => array(), 'selected' => array(), 'class' => array() ), 'optgroup' => array( 'label' => array() ) )
					);
					?>
				</div>
				<div class="advance_fields nb-dep-conf" data-conf="text">
					<label for="nb-submit-msg"><?php esc_html_e( 'Registration Success Message', 'pie-register' ); ?></label>
					<textarea name="field[submit][message]" id="nb-submit-msg" rows="4"><?php echo esc_textarea( stripcslashes( (string) $ss( 'message' ) ) ); ?></textarea>
				</div>
			</div>
		</aside>
	</div>
</form>
</div>
