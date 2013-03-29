<?php
/**
 * Showtime Example
 *
 * @author Andy Bruin
 * @package WP User Frontend
 * @version 1.1-fork-2RRR-2.0 
 */

/*
== Changelog ==

= 1.1-fork-2RRR-2.0 professor99 =
* Uses wpuf_get_option filter instead of wpuf_allow_cats filter
*/


$showtime_db_version = "3.0";
$showtimeTable = $wpdb->prefix . "WPShowtime";
$showtime_post_msg = '';

//***************************
// WP User Frontend additions
//***************************

//Add user show blog filter for WP User Frontend Add Post Editor

function showtime_wpuf_can_post($val) {
	global $wpdb;
	global $userdata;
	global $showtimeTable;
	global $showtime_post_msg;

	$showtime_post_msg = '';
	
	if ($val == 'no') return 'no';
	
	$showtime_taxonomy = $_GET['showtime_taxonomy'];
	if(!isset($showtime_taxonomy)) return 'yes';
	
	if (!preg_match('/^Showtime-([0-9]+)$/', $showtime_taxonomy, $matches)) {
		$showtime_post_msg = 'Query incorrect.';
		return 'no';
	}
	$id = intval($matches[1]);

	$blogsOn = get_option('showtime_use_blogs');
	if ($blogsOn == 'no' ) {
		$showtime_post_msg = 'Show blogs off.';
		return 'no';
	}
	
	$restrictBlogs = get_option('showtime_restrict_blogs');
	if ($restrictBlogs == 'no' ) {
		return 'yes';
	}
	
	if (!is_user_logged_in()) {
		$showtime_post_msg = 'User not logged in.';
		return 'no';
	}
	
	if ( current_user_can( 'edit_others_posts' )) {
		return 'yes';
	}
	
	$user_login = $userdata->user_login; 	
	$editor_row =  $wpdb->get_row( $wpdb->prepare ( "SELECT editors FROM $showtimeTable WHERE id = $id"));
	$editors = $editor_row->editors;
	$editor_array = explode(',',$editors);
	foreach($editor_array as $editor) {
		if ($user_login == $editor) {
			return 'yes';
		}	
	}

	$showtime_post_msg = 'User not authorized.';
	return 'no';
}    

add_filter( 'wpuf_can_post', 'showtime_wpuf_can_post', 10, 1 ); 

//Set info message for WP User Frontend Add Post Editor according to results of showtime_wpuf_can_post()

function showtime_wpuf_addpost_notice($info) {
	global $showtime_post_msg;
	if ($showtime_post_msg) return $showtime_post_msg;
	return $info;
}

add_filter( 'wpuf_addpost_notice', 'showtime_wpuf_addpost_notice', 10, 1 ); 


//Suppress category field for WP User Frontend for show blogs 
 
function showtime_wpuf_set_allow_cats_off($val, $option) {
	//Used by WP User Frontend Add Post and Edit Post for both get and post requests. 
	
	if ($option == 'allow_cats') {
		if (isset($_GET['pid'])) {
			//Call from wpuf-edit-post.php:edit_form()
			if (get_the_terms($_GET['pid'], 'showtime')) {
				return 'off';
			}	
		} else if (isset($_POST['post_id'])) {
			//Call from wpuf-edit-post.php:submit_form() with ?pid=???
			if (get_the_terms($_POST['post_id'], 'showtime')) {
				return 'off';
			}	
		} else if (isset($_GET['showtime_taxonomy'])) {
			//Call from wpuf-add-post.php:post_form() with ?showtime_taxonomy=???
			return 'off';
		} else if (isset($_POST['wpuf_self'])) {
			$query = parse_url($_POST['wpuf_self'], PHP_URL_QUERY);
			parse_str ($query , $output );
			if (isset($output['showtime_taxonomy'])) {
				//Call from wpuf-add-post.php:submit_form() with ?showtime_taxonomy=???
				return 'off';
			}
		}	
	}

	return $val;
}
 
add_filter( 'wpuf_get_option', 'showtime_wpuf_set_allow_cats_off', 10, 2 ); 

//On add post if showtime post set category to Showtime

function showtime_wpuf_add_post_args($post_args) {
	if (isset($_POST['_wp_http_referer'])) {
		$query = parse_url($_POST['_wp_http_referer'], PHP_URL_QUERY);
		parse_str ($query , $output );
		if (isset($output['showtime_taxonomy'])) {
			$showtime_taxonomy = $output['showtime_taxonomy'];
		}
	}
	
	if(!isset($showtime_taxonomy)) return $post_args;

	//Set category to Showtime. 
	//Note we use a Showtime category to avoid all Shows blogs ending up in 'uncategorized' category.
	//NB Although it's possible to omit a category for an article the admin edit post inserts the 
	//'uncategorized' category on update.
	$category = get_term_by('name','Showtime','category');
	$category_id = $category->term_id;
	$post_args['post_category'] = array($category_id);

	//Set taxonomy
	$taxonomy = get_term_by('name',$showtime_taxonomy,'showtime');
	$taxonomy_id = $taxonomy->term_id;
	
	$post_args['tax_input'] = array('showtime' => $taxonomy_id);

	return $post_args;
}
 
add_filter('wpuf_add_post_args', 'showtime_wpuf_add_post_args', 10, 1 ); 

// If initial post category on edit is showtime don't update category 

function showtime_wpuf_edit_post_args($post_args) {
	if (isset($_POST['post_id'])) {
		//Call from wpuf-edit-post.php:submit_form() with ?pid=???
		$post = get_post( $_POST['post_id'] );
		if ($post->post_category == 'Showtime') {
			unset($post_args['post_category']);
		}
	}
	
	return $post_args;
}
 
add_filter('wpuf_edit_post_args', 'showtime_wpuf_edit_post_args', 10, 1 ); 

//Add custom editor for WP User Frontend

function showtime_wpuf_custom_editor($editor, $description, $editor_id, $textarea_name) {
	wp_editor( 
		$description, 
		$editor_id, 
		array(
			'textarea_name' => $textarea_name, 
			'editor_class' => 'requiredField',
			'teeny' => true, 
			'textarea_rows' => 8, 
			'quicktags' => false,
			'tinymce' => array(
				'theme_advanced_statusbar_location' => 'bottom',
				'theme_advanced_path' => false,
				'theme_advanced_resizing' => true,
				'theme_advanced_resize_horizontal' => false,
				'plugins' => 'inlinepopups,spellchecker,tabfocus,paste,media,fullscreen,wordpress,wpeditimage,wpgallery,wplink,wpdialogs',
				'paste_text_sticky' => true,
				'paste_text_sticky_default' => true,
				'theme_advanced_buttons1' => 'bold,italic,underline,justifyleft,justifycenter,justifyright,justifyfull,bullist,numlist,outdent,indent,blockquote,wp_more,charmap,spellchecker,link,unlink,undo,redo'
			)
		) 
	); 
}

add_action('wpuf_custom_editor', 'showtime_wpuf_custom_editor', 10, 4);

//Add Showtime Custom Basic Editor to WP User FrontEnd options

function showtime_wpuf_options_frontend_map($element) {
	if ($element['name'] == 'editor_type') {
			$element['options']['basic'] =  __( 'Rich Text (basic)', 'wpuf');
	}

	return $element;
}

function showtime_wpuf_options_frontend($options) {
	return array_map('showtime_wpuf_options_frontend_map', $options);
}

add_filter('wpuf_options_frontend', 'showtime_wpuf_options_frontend', 10, 1 ); 
?>
