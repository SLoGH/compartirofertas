<?php

define('CONTACT_FORM_SHORTCODE_DIR', plugin_dir_path(__FILE__));
define('CONTACT_FORM_SHORTCODE_URL', get_template_directory_uri() . '/lib/shortcode/contactForm/');

if (is_admin())
	require_once CONTACT_FORM_SHORTCODE_DIR . '/contactForm/admin.php';

// take the content of a contact-form shortcode and parse it into a list of field types
function th_contact_form_parse($content)
{
	// first parse all the contact-field shortcodes into an array
	global $contact_form_fields;
	$contact_form_fields = array();

	

	$out = do_shortcode($content);

	return $out;
}

function th_contact_form_render_field($field)
{
	global $contact_form_last_id, $contact_form_fields, $current_user, $user_identity, $js_validation;

	$r = '';

	$field_id = $field['id'];
	if (isset($_POST[$field_id]))
	{
		$field_value = stripslashes($_POST[$field_id]);
	}
	elseif (is_user_logged_in())
	{
		// Special defaults for logged-in users
		switch($field['type'])
		{
			case 'email':
				$field_value = $current_user->data->user_email;
				break;
			case 'name':
				$field_value = $user_identity;
				break;
			case 'url':
				$field_value = $current_user->data->user_url;
				break;
			default :
			$field_value = $field['default'];
				break;
		}
	}
	else
	{
		$field_value = $field['default'];
	}
	
	$field_value = wp_kses($field_value, array());

	$field['label'] = html_entity_decode($field['label']);
	$field['label'] = wp_kses($field['label'], array());
	
	$js_validation[] = get_validation_field_data($field);
	switch ($field['type'])
	{
		case 'email':
			$r .= "\n<div class='form_line'>\n";
			$r .= "\t\t<label for='" . esc_attr($field_id) . "' class='th-field-label " . esc_attr($field['type']) . "' style='display:none;'>" . htmlspecialchars($field['label']) . ( $field['required'] ? '<span>' . __("(required)", 'churchope') . '</span>' : '' ) . "</label><input type='text' name='" . esc_attr($field_id) . "' id='" . esc_attr($field_id) . "' value='" . esc_attr($field_value) . "' placeholder='".htmlspecialchars($field['label'])."' class='" . esc_attr($field['type']) . "'/>\n";
			$r .= "\t</div>\n";
			break;
		
		case 'textarea':
			$r .= "\n<div class='form_line'>\n";
			$r .= "\t\t<label for='" . esc_attr($field_id) . "' class='" . esc_attr($field['type']). "' style='display:none;'>" . htmlspecialchars($field['label']) . ( $field['required'] ? '<span>' . __("(required)", 'churchope') . '</span>' : '' ) . "</label><textarea name='" . esc_attr($field_id) . "' id='contact-form-comment-" . esc_attr($field_id) . "' placeholder='".htmlspecialchars($field['label'])."' rows='14'>" . htmlspecialchars($field_value) . "</textarea>\n";
			$r .= "\t</div>\n";
			break;
		
		case 'radio':
			$r .= "\t<div class='form_line'><label class='" .  "'>" . htmlspecialchars($field['label']) . ( $field['required'] ? '<span>' . __("(required)", 'churchope') . '</span>' : '' ) . "</label>\n";
			foreach ($field['options'] as $option)
			{
				$r .= "\t\t<input type='radio' name='" . esc_attr($field_id) . "' value='" . esc_attr($option) . "' class='" . esc_attr($field['type']) . "' " . ( $option == $field_value ? "checked='checked' " : "") . " /><label for='". esc_attr($field_id) . "' class='" . esc_attr($field['type']) . "'>" . htmlspecialchars($option) . "</label>";
			}
			$r .= "\t\t</div>\n";
			break;
			
		case 'checkbox':
			$r .= "\t<div class='form_line'><input type='checkbox' name='" . esc_attr($field_id) . "' value='" . __('Yes', 'churchope') . "' class='" . esc_attr($field['type']) . "' " . ( $field_value ? "checked='checked' " : "") . " /><label for='". esc_attr($field_id) . "' class='" . esc_attr($field['type']) .   "'>" . htmlspecialchars($field['label']) . ( $field['required'] ? '<span>' . __("(required)", 'churchope') . '</span>' : '' ) . "</label></div>\n";
			break;
		
		case 'select':
			$r .= "\n<div class='form_line'>\n";
			$r .= "\t\t<label for='" . esc_attr($field_id) . "' class='" . esc_attr($field['type']) .   "'>" . htmlspecialchars($field['label']) . ( $field['required'] ? '<span>' . __("(required)", 'churchope') . '</span>' : '' ) . "</label>\n";
			$r .= "\t<select name='" . esc_attr($field_id) . "' id='" . esc_attr($field_id) . "' value='" . esc_attr($field_value) . "' class='" . esc_attr($field['type']) . "'/>\n";
			foreach ($field['options'] as $option)
			{
				$option = html_entity_decode($option);
				$option = wp_kses($option, array());
				$r .= "\t\t<option" . ( $option == $field_value ? " selected='selected'" : "") . ">" . esc_html($option) . "</option>\n";
			}
			$r .= "\t</select>\n";
			$r .= "\t</div>\n";
			break;
			
		default:
			// default: text field
			// note that any unknown types will produce a text input, so we can use arbitrary type names to handle
			// input fields like name, email, url that require special validation or handling at POST
			$r .= "\n<div class='form_line'>\n";
			$r .= "\t\t<label for='" . esc_attr($field_id) . "' class='" . esc_attr($field['type']) .   "' style='display:none;'>" . htmlspecialchars($field['label']) . ( $field['required'] ? '<span>' . __("(required)", 'churchope') . '</span>' : '' ) . "</label><input type='text' name='" . esc_attr($field_id) . "' id='" . esc_attr($field_id) . "' value='" . esc_attr($field_value) . "' placeholder='".htmlspecialchars($field['label'])."' class='" . esc_attr($field['type']) . "'/>\n";
			$r .= "\t</div>\n";
			break;
	}
	
	return $r;
}

function get_validation_field_data($field)
{
	$data		= array();
	$message	= array();
	$rules		= array();
	
	if(!is_null($field))
	{
		if(isset($field['type']))
		{
			switch($field['type'])
			{
				case 'email':
					$rules[] = 'email';
					$message['email'] = sprintf(__('%s requires a valid email address', 'churchope'), $field['label']);
					break;
				case 'url':
					$rules[] = 'url';
					$message['url'] = sprintf(__('%s requires a valid url', 'churchope'), $field['label']);
					break;
				default:
					break;
			}
		}
		if( isset($field['required']) && $field['required'] )
		{
			$rules[] = 'required';
			$message['required'] = sprintf(__('%s is required', 'churchope'), $field['label']);
		}
	}
	
	$data[esc_attr($field['id'])] = array(
								'rules'		=> $rules,
								'message'	=> $message,);
	return $data;
}

function get_js_validation_code($form_code)
{
	global $js_validation;
	
	$form_id = "id-$form_code";
	
	$rules = '';
	$message = '';
	$code = '';
	if(is_array($js_validation)&& count($js_validation))
	{
		$code = "<script type='text/javascript'>";
		$code .= "jQuery(document).ready(function(){
		jQuery('#$form_id').validate({
				submitHandler: function(form) {
							ajaxContact(form);							
							jQuery('#$form_id div.contact-submit input').attr('disabled', 'disabled');	
							return false;
						}";
		foreach($js_validation as $element)
		{
			foreach($element as $name => $info)
			{
				if(isset($info['rules']) && is_array($info['rules']) && count($info['rules']))
				{
					$rules .= "'$name' : '".implode(' ', $info['rules'])."',";
				}

				if(isset($info['message']) && is_array($info['message']) && count($info['message']))
				{
					$element_message = '';

					foreach($info['message'] as $rule => $text)
					{
						$element_message .= "'$rule' : '$text',";
					}

					if($element_message)
					{
						$element_message = rtrim($element_message, ',');
						$message .= "'$name' :{".$element_message."},";
					}
				}
			}
		}

		if($rules)
		{
			$rules = rtrim($rules, ',');
			$code .= ", rules: {".$rules."}";
		}

		if($message)
		{
			$message = rtrim($message, ',');
			$code .= ", message: {".$message."}";
		}

		$code .= "});});";
		$code .= "</script>";
	}
	return $code;
}

// generic shortcode that handles all of the major input types
// this parses the field attributes into an array that is used by other functions for rendering, validation etc
function th_contact_form_field($atts, $content, $tag)
{
	global $contact_form_fields, $contact_form_last_id, $js_validation;
	
	$field = shortcode_atts(array(
		'label' => null,
		'type' => 'text',
		'required' => false,
		'options' => array(),
		'id' => null,
		'default' => null,
			), $atts);

	// special default for subject field
	if ($field['type'] == 'subject' && is_null($field['default']))
		$field['default'] = '';

	// allow required=1 or required=true
	if ($field['required'] == '1' || strtolower($field['required']) == 'true')
		$field['required'] = true;
	else
		$field['required'] = false;

	// parse out comma-separated options list
	if (!empty($field['options']) && is_string($field['options']))
		$field['options'] = array_map('trim', explode(',', $field['options']));

	// make a unique field ID based on the label, with an incrementing number if needed to avoid clashes
	$id = $field['id'];
	if (empty($id))
	{
		$id = sanitize_title_with_dashes($contact_form_last_id . '-' . $field['label']);
		$i = 0;
		while (isset($contact_form_fields[$id]))
		{
			$i++;
			$id = sanitize_title_with_dashes($contact_form_last_id . '-' . $field['label'] . '-' . $i);
		}
		$field['id'] = $id;
	}

	$contact_form_fields[$id] = $field;

	return th_contact_form_render_field($field);
}

add_shortcode('contact-field', 'th_contact_form_field');

function th_contact_form_shortcode($atts, $content)
{
	global $post, $js_validation;
	$js_validation = '';

	$default_to = get_option('admin_email');
	$default_subject = "[" . get_option('blogname') . "]";

	if (!empty($atts['widget']) && $atts['widget'])
	{
		$default_subject .= " Sidebar";
	}
	elseif ($post->ID)
	{
		$default_subject .= " " . wp_kses($post->post_title, array());
		$post_author = get_userdata($post->post_author);
		$default_to = $post_author->user_email;
	}
// default values
	extract(shortcode_atts(array(
				'to' => '',
				'subject' => '',
				'show_subject' => 'no', // only used in back-compat mode
				'widget' => 0 //This is not exposed to the user. Works with contact_form_widget_atts
					), $atts));

	$widget = esc_attr($widget);

	if (( function_exists('faux_faux') && faux_faux() ) || is_feed())
		return '[contact-form]';

	global $wp_query,  $contact_form_values, $user_identity, $contact_form_last_id, $contact_form_message;

	// used to store attributes, configuration etc for access by contact-field shortcodes

	if ($widget)
		$id = 'widget-' . $widget;
	elseif (is_singular())
		$id = $wp_query->get_queried_object_id();
	else
		$id = $GLOBALS['post']->ID;
	if (!$id) // something terrible has happened
		return '[contact-form]';

	
	$contact_form_last_id = $id;

	ob_start();
	wp_nonce_field('contact-form_' . $id);
	$nonce = ob_get_contents();
	ob_end_clean();


	$body = th_contact_form_parse($content);

	$r = "<div id='contact-form-$id'>\n";

	$errors = array();
	$random_form_id = mt_rand();
	$r .= "<form action='#contact-form-$id' method='post' class='contact-form commentsblock forms_shortcode' id='id-$random_form_id' >\n";
	$r .= $body;
	$r .= "\t<div class='contact-submit'>\n";
	$r .= "\t\t<input type='submit' value='" . __("Send a message", 'churchope') . "' class='churchope_button'/>";
	$r .= "$nonce";
	$r .= "<input type='hidden' name='contact-form-id' value='$id' />";
	$r .= "</div>";
	$emails = str_replace(' ', '', $to);
	$emails = explode(',', $emails);
	$valid_emails = array();
	foreach ((array) $emails as $email)
	{
		if (is_email($email) && (!function_exists('is_email_address_unsafe') || !is_email_address_unsafe($email) ))
			$valid_emails[] = $email;
	}

	$to = ( $valid_emails ) ? $valid_emails : $default_to;
	if(is_array($to))
	{
		$to_string = implode(',', $to);
	}
	else
	{
		$to_string = $to;	
	}
	
	
	if($to_string)
	{
		$r .= "<input type='hidden' name='to' value='$to_string' />";
	}
	if($subject)
	{
		$r .= "<input type='hidden' name='subject' value='$subject' />";
	}
	$r .= "</form>\n";
	$r .= get_js_validation_code($random_form_id);
	$r .= "</div>";
	wp_enqueue_script('validate');
	return $r; 
}

add_shortcode('contact-form', 'th_contact_form_shortcode');

function th_contact_form_widget_atts($text)
{
	static $widget = 0;

	$widget++;

	return str_replace('[contact-form', '[contact-form widget="' . $widget . '"', $text);
}

add_filter('widget_text', 'th_contact_form_widget_atts', 0);
?>