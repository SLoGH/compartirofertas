<?php

/**
 * Add Post form class
 *
 * @author Tareq Hasan 
 * @package WP User Frontend
 * @version 1.1-fork-2RRR-4.4 
 */

/*
== Changelog ==

= 1.1-fork-2RRR-4.4 professor99 =
* Fixed redirect bug.
* Added redirect url option
* Dropped redirect current option
* Category select and validate code moved to wpuf-cat.php
* Changed wpuf-post-area to wpuf and extended to include delete and close buttons
* Enqueue wpuf.css, custom css, wpuf-cat.js, wpuf-post.js, and localize wpuf javascript object

= 1.1-fork-2RRR-4.3 professor99 =
* Added post status field
* Added slug
* Fixed wpuf_suppress_edit_post_link bug
* Fixed wpuf_referer
* Fixed expiration date
* Fixed excerpt
* Added login message label
* Fix Insert Media Bug
* Auto Draft added

= 1.1-fork-2RRR-4.1 professor99 =
* Implemented Post Format field.

= 1.1-fork-2RRR-4.0 professor99 =
* Implemented "enable_post_add" option.
* Better language support for info div
* Implemented "default" parameter for "Post Status" option.
* Updated user mapping
* Added $post_type parameter to wpuf_can_post filter.
* Fixed Description alignment for all users

= 1.1-fork-2RRR-3.0 professor99 =
* Added excerpt
* Re-styled form to suit
* Change expiration time to actual date/time
* Made hour two digits with leading zero
* Checks for valid times
* Made attachment calls inline (was actions)
* Featured image html moved to WPUF_Featured_Image::add_post_fields()
* Removed attachment code replaced by ajax
* Can display top line info message anytime
* Removed 'Your theme doesn't support featured image' message
* Fixed javascript success clear form bug
* Redirects now filtered by wpuf_post_redirect 
* Form actions consolidated under wpuf_post_form
* Escaped info message XML tags

= 1.1-fork-2RRR-2.1 professor99 = 
* Replaced anonymous function with suppress_edit_post_link()

= 1.1-fork-2RRR-2.0 professor99 =
* Now uses jquery.form to do Ajax style updates.
* Post redirect shortcut option added.
* Better info and error messages.
* Suppress "edit_post_link" on this page
* Added wpuf prefix to some css classes
* Re-styled buttons.
* Re-styled attachment display

= 1.1-fork-2RRR-1.0 professor99 =
* Custom editor option added.
* Editors use max availiable width.
* Close button added as shortcut option and redirects set to suit.
* wpuf_allow_cats filter added.
* Security checks updated.
* Code updated to allow use of wpuf_can_post filter for non logged in users.
*/

/*
 *  Shortcode examples::
 * 
 * 	[wpuf_addpost]
 * 	[wpuf_addpost close="false"]
 * 	[wpuf_addpost close="false" redirect="none"]
 * 
 *  Shortcode options:
 * 
 * 	post_type: post | <otherPostType>
 * 		post: (default)
 * 		<otherPostType>: other post types
 * 	close: true | false 
 * 		true: will display close button and redirect to last page on close (default)
 * 		false: 
 * 	redirect: none | auto | new | last | %url%
 * 		auto: If close==true will goto last page. 
 *		      Else will stay on current page. (default)
 * 		none: will stay on current page
 * 		new: will goto new page
 * 		last: will goto last page
 *		%url%:will goto given %url%
 *
 * NB redirect only applies to posts successfully submitted
 */
 
 /* Notes
 *
 * The action 'wpuf_post_form' is common to both this file and wpuf_add_post.php.
 * It is invoked as function($form, $location, $post_type, $post). 
 * For this file $form = 'add' and $post = ''. 
 *
 * The filter 'wpuf_post_redirect' is common to both this file and wpuf_add_post.php.
 * It is invoked as function($form, $location, $redirect_url, $post_id). 
 * For this file  $form = 'add' and $post_id = '' if not defined.
 */ 

require_once(ABSPATH . '/wp-admin/includes/post.php');

/**
 * Add Post Class
 * 
 * @author Tareq Hasan
 * @package WP User Frontend
 * @subpackage WPUF_Add_Post
 */
class WPUF_Add_Post {
	var $wpuf_self = '';
	var $wpuf_referer = '';
	var $logged_in = false;
	
	function __construct() {
		//Ajax calls for Submit Post button 
		add_action('wp_ajax_wpuf_add_post_action', array($this, 'submit_post'));
		add_action('wp_ajax_nopriv_wpuf_add_post_action', array($this, 'submit_post'));
		
		add_shortcode( 'wpuf_addpost', array($this, 'shortcode') );
	}

	/**
	 * Enqueue scripts and styles
	 *
	 * @author Andrew Bruin (professor99)
	 * @since 1.1-fork-2RRR-2.0 
	 */
	function enqueue() {
		$path = plugins_url( 'wp-user-frontend' );

		//Add wpuf css
		wp_enqueue_style( 'wpuf', $path . '/css/wpuf.css' );

		//Add custom css
		wp_add_inline_style( 'wpuf', wpuf_get_option( 'custom_css' ) );

		//Add scripts
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'jquery-form' );
        wp_enqueue_script( 'wpuf-cat', $path . '/js/wpuf-cat.js', array( 'jquery' ) );
        wp_enqueue_script( 'wpuf-post', $path . '/js/wpuf-post.js', array( 'jquery' ) );
		wp_enqueue_script( 'wpuf-add-post', $path . '/js/wpuf-add-post.js', array( 'jquery', 'jquery-form' ) );

		//Create wpuf javascript object
		wpuf_post_localize();
	}

	/**
	 * Handles the add post shortcode
	 *
	 * Generates the add post form
	 *
	 * @author Tareq Hasan
	 * @global WP_User $userdata
	 *
	 * @param array $atts attributes
	 */
	function shortcode( $atts ) {
		global $userdata;

		//echo '<div>REQUEST=' . print_r($_REQUEST, true) . '<br>POST=' . print_r($_POST,true) . '<br>$_GET=' . print_r($_GET,true) . '<br>$_SERVER='. print_r($_SERVER,true) . '<br>$userdata=' . print_r($userdata,true) . '</div>'; 

		//Suppress "edit_post_link" on this page
		add_filter( 'edit_post_link', 'wpuf_suppress_edit_post_link', 10, 2 ); 

		//Enqueue scripts and styles
		$this->enqueue();

		extract( shortcode_atts( array('post_type' => 'post', 'close' => 'true', 'redirect' => 'auto'), $atts ) );

		ob_start();

		echo "<div id='wpuf'>\n";

		//Set referer URL. 
		if ( isset( $_GET['wpuf_referer'] ) ) {
			$this->wpuf_referer = $_GET['wpuf_referer'];
		} else if ( isset( $_SERVER['HTTP_REFERER'] ) ) {
			$this->wpuf_referer = $_SERVER['HTTP_REFERER'];
		} else {
			$this->wpuf_referer = '';
		}

		//URL of this page
		$this->wpuf_self = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

		//login url

		$login_url = $this->wpuf_self;

		if ( $this->wpuf_referer )
			$login_url = add_query_arg( 'wpuf_referer', $this->wpuf_referer, $this->wpuf_self );

		$login_url = htmlspecialchars( $login_url, ENT_QUOTES, "UTF-8" );

		$this->logged_in = is_user_logged_in();

		$post_type_object = get_post_type_object( $post_type );
		$enable_post_add = wpuf_get_option( 'enable_post_add', 'default' );

		$can_post = 'yes';
		$info = ''; 

		if ( $enable_post_add == 'no' ) {
			$can_post = 'no';
			$info = 'Add Post is disabled';
		} 
		else if ( !$this->logged_in ) {
			$can_post = 'no';
			$info = 'restricted';
		}
		else if ( $enable_post_add == 'default' && !current_user_can( $post_type_object->cap->edit_posts ) ) {
			$can_post = 'no';
			$info = 'You are not permitted to add posts of this type.';
		}

		//If you use this filter to allow non logged in users make sure use a Catcha or similar.
		$can_post = apply_filters( 'wpuf_can_post', $can_post, $post_type );

		$info = apply_filters( 'wpuf_addpost_notice', $info );

		if ($info) {
			if ( $info == 'restricted' )
				$info = sprintf( wpuf_get_option( 'login_label' ), wp_loginout( $login_url, false ) );
			else
				$info = __( $info, 'wpuf' );

			echo '<div class="wpuf-info">' . $info . '</div>';
		}

		if ( $can_post == 'yes' ) {
			// Schedule auto-draft cleanup
			if ( ! wp_next_scheduled( 'wp_scheduled_auto_draft_delete' ) )
				wp_schedule_event( time(), 'daily', 'wp_scheduled_auto_draft_delete' );

			//Create Auto Draft
			$post = get_default_post_to_edit( $post_type, true );	

			//Display Post Form
			$this->post_form( $post, $close, $redirect );
		}

		//Use this filter if you want to change the return address on Close
		$redirect_url = apply_filters( 'wpuf_post_redirect', 'add', 'close', $this->wpuf_referer, '');

		if ($redirect_url != "" && $close == "true") {
			$redirect_url = htmlspecialchars( $redirect_url, ENT_QUOTES, "UTF-8" );
			echo '<div id="wpuf-button-close"><a class="wpuf-button" href="' . $redirect_url . '">' . esc_attr( wpuf_get_option( 'close_label' ) ) . '</a></div>';
		}

		echo "</div>\n";

		$content = ob_get_contents();
		ob_end_clean();

		return $content;
	}

	/**
	 * Add posting main form
	 *
	 * @author Tareq Hasan
	 * @global WP_User $userdata
	 * @global WPUF_Cat $wpuf_cat
	 *
	 * @param WP_Post $post Post object
	 * @param string $close Display Close Button "true"|"false"
	 * @param string $redirect Redirect after post  "none" | "auto" | "new" | "last" | %url%
	 */
	function post_form( $post, $close, $redirect ) {
		global $userdata;
		global $wpuf_cat;

		$post_id = $post->ID;
		$post_type = $post->post_type;

		//Fix Insert Media Bug
		wpuf_insert_media_fix( $post_id );

		$title = '';
		$description = '';				
?>
		<form id="wpuf_new_post_form" name="wpuf_new_post_form" action="" enctype="multipart/form-data" method="POST">
			<?php wp_nonce_field( 'wpuf-add-post' ) ?>

			<ul class="wpuf-post-form">
				<?php
				do_action( 'wpuf_post_form', 'add', 'top', $post_type, $post ); 
				wpuf_build_custom_field_form( 'top' );

				//Add featured image field if enabled and the current theme supports thumbnails

				$featured_image = wpuf_get_option( 'enable_featured_image' );

				if ( $featured_image == 'yes' && current_theme_supports( 'post-thumbnails' ) ) {
					WPUF_Featured_Image::add_post_fields( $post_type );
				}
				?>

				<li>
					<label for="wpuf-post-title">
						<?php echo wpuf_get_option( 'title_label' ); ?> <span class="required">*</span>
					</label>
					<input class="requiredField" type="text" value="<?php echo $title; ?>" name="wpuf_post_title" id="wpuf-post-title" minlength="2">
					<div class="clear"></div>
					<?php
					$helptxt = stripslashes( wpuf_get_option( 'title_help' ) );
					if ($helptxt) 
						echo '<p class="description">' . $helptxt . '</p>';
					?>
				</li>
				<?php 
				if ( wpuf_get_option( 'allow_slug' ) == 'on' ) {
				?>
					<li>
						<label for="wpuf-slug">
							<?php echo wpuf_get_option( 'slug_label' ); ?>
						</label>
						<input type="text" name="wpuf_slug" id="wpuf-slug" value="<?php echo esc_html( $post->post_name ); ?>">
						<div class="clear"></div>
						<?php
						$helptxt = stripslashes( wpuf_get_option( 'slug_help' ) );
						if ($helptxt) 
							echo '<p class="description">' . $helptxt . '</p>';
						?>
					</li>
				<?php
				}

				do_action( 'wpuf_post_form', 'add', 'description', $post_type, $post ); 
				wpuf_build_custom_field_form( 'description' );
				?>
				
				<li>
					<label for="wpuf-post-content">
						<?php echo wpuf_get_option( 'desc_label' ); ?> <span class="required">*</span>
					</label>
					<div class="clear"></div>
					<?php
					$editor = wpuf_get_option( 'editor_type' );

					//Filter $editor. Useful for adding custom editors or assigning editors according to users..
					$editor = apply_filters( 'wpuf_editor_type', $editor );

					if ( $editor == 'full' ) {
						wp_editor( $description, 'wpuf-post-content', array('textarea_name' => 'wpuf_post_content', 'editor_class' => 'requiredField', 'teeny' => false, 'textarea_rows' => 8) ); 
					} else if ( $editor == 'rich' ) {
						wp_editor( $description, 'wpuf-post-content', array('textarea_name' => 'wpuf_post_content', 'editor_class' => 'requiredField', 'teeny' => true, 'textarea_rows' => 8) ); 
					} else if ( $editor == 'plain' ) { 
					?>
						<textarea name="wpuf_post_content" class="requiredField wpuf-editor-plain" id="wpuf-post-content" cols="60" rows="8"><?php echo esc_textarea( $description ); ?></textarea>
					<?php 
					} else { 
						//Use custom editor. 
						//Two ways to enable.
						//1. wpuf_editor_type filter above.
						//2. showtime_wpuf_options_frontend filter.
						do_action('wpuf_custom_editor', $editor, $description, 'wpuf-post-content', 'wpuf_post_content');
					}
					?>
					<div class="clear"></div>
					<?php
					$helptxt = stripslashes( wpuf_get_option( 'desc_help' ) );
					if ($helptxt) 
						echo '<p class="description-left">' . $helptxt . '</p>';
					?>
				</li>

				<?php
				do_action( 'wpuf_post_form', 'add', 'after_description', $post_type, $post ); 
				wpuf_build_custom_field_form( 'after_description' );

				if ( wpuf_get_option( 'allow_excerpt' ) == 'on' ) {
					$max_chars = wpuf_get_option( 'excerpt_max_chars' );
				?>
					<li>
						<label for="wpuf-excerpt">
						<?php 
						if ($max_chars == 0) {
							echo wpuf_get_option( 'excerpt_label' );
							$maxlength = '';
						} else {	
							echo wpuf_get_option( 'excerpt_label' ) . ' (max '. $max_chars . ' chars)';
							$maxlength = 'maxlength="' . $max_chars . '"';
						}	

						if ( wpuf_get_option( 'require_excerpt' ) == 'on' ) {
						?>	
							<span class="required">*</span>
						<?php
						}
						?>
						</label>
						<div class="clear"></div>
						<div class="wpuf-textarea-container">
							<?php
							if ( wpuf_get_option( 'require_excerpt' ) == 'on' ) {
							?>	
								<textarea class="requiredField" id="wpuf-excerpt" name="wpuf_excerpt" cols="80" rows="2" <?php echo $maxlength ?> ></textarea>
							<?php
							} else {
							?>
								<textarea id="wpuf-excerpt" name="wpuf_excerpt" cols="80" rows="2" <?php echo $maxlength ?> ></textarea>
							<?php	
							}
							?>
						</div>
						<div class="clear"></div>
						<?php	
						$helptxt = stripslashes( wpuf_get_option( 'excerpt_help' ) );
						if ($helptxt) 
							echo '<p class="description-left">' . $helptxt . '</p>';
						?>
					</li>
				<?php
				}

				if ( wpuf_get_option( 'allow_tags' ) == 'on' ) {
				?>
					<li>
						<label for="wpuf-post-tags">
							<?php echo wpuf_get_option( 'tag_label' ); ?>
						</label>
						<input type="text" name="wpuf_post_tags" id="wpuf-post-tags">
						<div class="clear"></div>
						<?php	
						$helptxt = stripslashes( wpuf_get_option( 'tag_help' ) );
						if ($helptxt) 
							echo '<p class="description">' . $helptxt . '</p>';
						?>
					</li>
				<?php
				}

				do_action( 'wpuf_post_form', 'add', 'tag', $post_type, $post ); 
				wpuf_build_custom_field_form( 'tag' );

				//Add attachment fields if enabled
				
				$allow_upload = wpuf_get_option( 'allow_attachment' );
				
				if ( $allow_upload == 'yes' ) {
					WPUF_Attachment::add_post_fields( $post_type );
				}

				//Add Post Format field if enabled and is supported by current theme and post type

				if ( wpuf_get_option( 'allow_format' ) == 'on' && current_theme_supports( 'post-formats' ) && post_type_supports( $post_type, 'post-formats' ) ) {
					$post_formats = get_theme_support( 'post-formats' );
					
					if ( is_array( $post_formats[0] ) ) {
						$default_format = get_option( 'default_post_format' );
				?>
						<li>
							<label for="wpuf-post-format">
								<?php echo wpuf_get_option( 'format_label' ); ?>
							</label>

							<select name="wpuf_post_format"  id="wpuf-post-format">
								<option <?php selected( $default_format, '0' ); ?> value="0" > <?php _e('Standard'); ?></option>
								<?php 
								foreach ( $post_formats[0] as $format ) { 
								?>
									<option <?php selected( $default_format, $format ); ?> value="<?php echo esc_attr( $format ); ?>" > <?php echo esc_html( get_post_format_string( $format ) ); ?></option>
								<?php 
								}
								?>
							</select>
							<div class="clear"></div>
							<?php
							$helptxt = stripslashes( wpuf_get_option( 'format_help' ) );
							if ($helptxt) 
								echo '<p class="description">' . $helptxt . '</p>';
							?>
						</li>
				<?php
					}
				}

				if ( wpuf_get_option( 'allow_cats' ) == 'on' ) {
				?>
					<li>
						<label>
							<?php echo wpuf_get_option( 'cat_label' ); ?> <span class="required">*</span>
						</label>
						<?php
						$wpuf_cat->select();				
						?>
						<div class="loading"></div>
						<div class="clear"></div>
						<?php
						$helptxt = stripslashes( wpuf_get_option( 'cat_help' ) );
						if ($helptxt) 
							echo '<p class="description">' . $helptxt . '</p>';
						?>
					</li>
				<?php
				}

				//Set author according to 'post author' option.
				//If user logged in and 'post_author' option is set to 'original' then set post author to user.
				//Else set post author to 'map_author' option value.
				if ($this->logged_in  && wpuf_get_option( 'post_author' ) == 'original') {
					$post_author = $userdata->ID;
				} else {
					$post_author = wpuf_get_option( 'map_author' );
				}
				
				//Add Post Status field if enabled and user is logged in
				if ( wpuf_get_option( 'allow_status') == 'on' && $this->logged_in) {
					$post_status = wpuf_get_option( 'post_status' );

					$post_type_object = get_post_type_object( $post_type );

					if ( $post_status == 'default' ) {
						if( user_can( $post_author, $post_type_object->cap->publish_posts ) ) { 
							$post_status = 'publish';
						}
						else {
							$post_status = 'pending';
						}
					}
				?>
					<li>
						<label for="wpuf-post-status">
							<?php echo wpuf_get_option( 'status_label' ); ?>
						</label>

						<select name="wpuf_post_status"  id="wpuf-post-status">
							<option <?php selected( $post_status, 'draft' ); ?> value="draft" > <?php _e('Draft'); ?></option>
							<option <?php selected( $post_status, 'pending' ); ?> value="pending" > <?php _e('Pending'); ?></option>
							<?php
							if( user_can( $post_author, $post_type_object->cap->publish_posts ) ) { 
							?>
								<option <?php selected( $post_status, 'publish' ); ?> value="publish" > <?php _e('Publish'); ?></option>
								<option <?php selected( $post_status, 'private' ); ?> value="private" > <?php _e('Private'); ?></option>
							<?php
							}
							?>
						</select>
						<div class="clear"></div>
						<?php
						$helptxt = stripslashes( wpuf_get_option( 'status_help' ) );
						if ($helptxt) 
							echo '<p class="description">' . $helptxt . '</p>';	
						?>
					</li>
				<?php
				}

				$this->publish_date_form();
				$this->expiry_date_form();

				wpuf_build_custom_field_form( 'bottom' );

				do_action( 'wpuf_post_form', 'add', 'submit', $post_type, $post ); 
				?>

				<li id="wpuf-submit-li">
					<div id="wpuf-info-msg">&nbsp;</div>
					<input class="wpuf-submit" type="submit" name="wpuf_new_post_submit" value="<?php echo esc_attr( wpuf_get_option( 'submit_label' ) ); ?>">
					<input type="hidden" name="post_id" value="<?php echo $post_id; ?>">
					<input type="hidden" name="wpuf_close" value="<?php echo $close ?>" />
					<input type="hidden" name="wpuf_redirect" value="<?php echo $redirect ?>" />
					<input type="hidden" name="wpuf_referer" value="<?php echo $this->wpuf_referer ?>" />
				</li>

				<?php do_action( 'wpuf_post_form', 'add', 'bottom', $post_type, $post ); ?>
			</ul>
		</form>
<?php
	}

	/**
	 * Prints the post publish date on form
	 *
	 * @author Tareq Hasan
	 */
	function publish_date_form() {
		$enable_date = wpuf_get_option( 'enable_post_date' );

		if ( $enable_date != 'on' ) {
				return;
		}

		$timezone_format = _x( 'Y-m-d H:i:s', 'timezone date format' );
		$month = date_i18n( 'm' );
		$month_array = array(
			'01' => 'Jan',
			'02' => 'Feb',
			'03' => 'Mar',
			'04' => 'Apr',
			'05' => 'May',
			'06' => 'Jun',
			'07' => 'Jul',
			'08' => 'Aug',
			'09' => 'Sep',
			'10' => 'Oct',
			'11' => 'Nov',
			'12' => 'Dec'
		);
?>
		<li>
			<label for="timestamp-wrap">
			<?php _e( 'Publish Time:', 'wpuf' ); ?> <span class="required">*</span>
			</label>
			<div class="timestamp-wrap">
				<select name="mm">
					<?php
					foreach ($month_array as $key => $val) {
						$selected = ( $key == $month ) ? ' selected="selected"' : '';
						echo '<option value="' . $key . '"' . $selected . '>' . $val . '</option>';
					}
					?>
				</select>
				<input class="requiredField" type="text" autocomplete="off" tabindex="4" maxlength="2" size="2" value="<?php echo date_i18n( 'd' ); ?>" name="jj">,
				<input class="requiredField" type="text" autocomplete="off" tabindex="4" maxlength="4" size="4" value="<?php echo date_i18n( 'Y' ); ?>" name="aa">
				@ <input class="requiredField" type="text" autocomplete="off" tabindex="4" maxlength="2" size="2" value="<?php echo date_i18n( 'H' ); ?>" name="hh">
				: <input class="requiredField" type="text" autocomplete="off" tabindex="4" maxlength="2" size="2" value="<?php echo date_i18n( 'i' ); ?>" name="mn">
			</div>
			<div class="clear"></div>
			<p class="description"></p>
		</li>
<?php
	}
	
	/**
	 * Prints post expiration date on the form
	 *
	 * @author Tareq Hasan
	 */
	function expiry_date_form() {
		$post_expiry = wpuf_get_option( 'enable_post_expiry' );

		if ( $post_expiry != 'on' ) {
			return;
		}
		
		$timezone_format = _x( 'Y-m-d H:i:s', 'timezone date format' );
		$month = date_i18n( 'm' );
		$month_array = array(
			'01' => 'Jan',
			'02' => 'Feb',
			'03' => 'Mar',
			'04' => 'Apr',
			'05' => 'May',
			'06' => 'Jun',
			'07' => 'Jul',
			'08' => 'Aug',
			'09' => 'Sep',
			'10' => 'Oct',
			'11' => 'Nov',
			'12' => 'Dec'
		);
?>		
		<li>
			<label for="timestamp-wrap">
			<?php _e( 'Expiration Time:', 'wpuf' ); ?> <span class="required">*</span>
			</label>
			<div class="timestamp-wrap">
				<select name="expiration-mm">
					<?php
					foreach ($month_array as $key => $val) {
						$selected = ( $key == $month ) ? ' selected="selected"' : '';
						echo '<option value="' . $key . '"' . $selected . '>' . $val . '</option>';
					}
					?>
				</select>
				<input class="requiredField" type="text" autocomplete="off" tabindex="4" maxlength="2" size="2" value="<?php echo date_i18n( 'd' ); ?>" name="expiration-jj">,
				<input class="requiredField" type="text" autocomplete="off" tabindex="4" maxlength="4" size="4" value="<?php echo date_i18n( 'Y' ); ?>" name="expiration-aa">
				@ <input class="requiredField" type="text" autocomplete="off" tabindex="4" maxlength="2" size="2" value="<?php echo date_i18n( 'H' ); ?>" name="expiration-hh">
				: <input class="requiredField" type="text" autocomplete="off" tabindex="4" maxlength="2" size="2" value="<?php echo date_i18n( 'i' ); ?>" name="expiration-mn">
				<input type="checkbox" tabindex="4" value="on" name="expiration-enable">Enable
			</div>
			<div class="clear"></div>
			<p class="description"><?php _e( 'Post expiration time if enabled.', 'wpuf' ); ?></p>
		</li>
<?php
	}

	/**
	 * Validate and insert post message.
	 *
	 * Called by AjaxForm on form submit.
	 * Returns XML message via AjaxForm.
	 *
	 * @author Tareq Hasan
	 * @global WP_User $userdata
	 * @global WPUF_Cat $wpuf_cat
	 */
	function submit_post() {
		global $userdata;
		global $wpuf_cat;

		//$message = '<div>REQUEST=' . print_r($_REQUEST, true) . '<br>POST=' . print_r($_POST,true) . '<br>$_SERVER='. print_r($_SERVER,true) . '<br>$userdata=' . print_r($userdata,true) . '</div>'; 
 		//echo '<root><success>false></success><message>' . <htmlspecialchars($message, ENT_QUOTES, "UTF-8") . '</message></root>'; 

		$post_id = trim( $_POST['post_id'] );
		$title = trim( $_POST['wpuf_post_title'] );
		$content = trim( $_POST['wpuf_post_content'] );

		$curpost = get_post( $post_id );
		$post_type = $curpost->post_type;

		$this->logged_in = is_user_logged_in();

		//Set header content type to XML
		header( 'Content-Type: text/xml' );

		if ( !wp_verify_nonce( $_REQUEST['_wpnonce'], 'wpuf-add-post' ) ) {
			$message = wpuf_error_msg( __( 'Cheating?' ) );
			echo '<root><success>false</success><message>' . htmlspecialchars($message, ENT_QUOTES, "UTF-8") . '</message></root>';
			exit;
		}

		$errors = array();

		//validate title
		if ( empty( $title ) ) {
			$errors[] = __( 'Empty post title', 'wpuf' );
		} else {
			$title = trim( strip_tags( $title ) );
		}

		//validate categories
		$post_category = $wpuf_cat->validate( $_POST['category'], $errors );

		//validate post content
		if ( empty( $content ) ) {
			$errors[] = __( 'Empty post content', 'wpuf' );
		} else {
			$content = trim( $content );
		}

		//process the custom fields

		$custom_fields = array();

		$fields = wpuf_get_custom_fields();

		if ( is_array( $fields ) ) {
			foreach ($fields as $cf) {
				if ( array_key_exists( $cf['field'], $_POST ) ) {
					$temp = trim( strip_tags( $_POST[$cf['field']] ) );
					//var_dump($temp, $cf);

					if ( ( $cf['type'] == 'yes' ) && !$temp ) {
						$errors[] = sprintf( __( '%s is missing', 'wpuf' ), $cf['label'] );
					} else {
						$custom_fields[$cf['field']] = $temp;
					}
				} //array_key_exists
			} //foreach
		} //is_array

		//validate post date

		$post_date = '';

		$post_date_enable = wpuf_get_option( 'enable_post_date' );

		if ( $post_date_enable == 'on' ) {
			$month = $_POST['mm'];
			$day = $_POST['jj'];
			$year = $_POST['aa'];
			$hour = $_POST['hh'];
			$min = $_POST['mn'];

			if ( !checkdate( $month, $day, $year ) ) {
				$errors[] = __( 'Invalid publish date', 'wpuf' );
			}
			else {
				$date = mktime( $hour, $min, 59, $month, $day, $year );
				
				if (!$date) 
					$errors[] = __( 'Invalid publish time', 'wpuf' );
				else
					$post_date = date( 'Y-m-d H:i:s', $date );
			}
		}

		//validate post expiry date

		$post_expiry_date = '';
		
		$post_expiry_enable = wpuf_get_option( 'enable_post_expiry' );
		
		if ( $post_expiry_enable == 'on' && isset( $_POST['expiration-enable'] ) && $_POST['expiration-enable'] == 'on' ) {
			$month = $_POST['expiration-mm'];
			$day = $_POST['expiration-jj'];
			$year = $_POST['expiration-aa'];
			$hour = $_POST['expiration-hh'];
			$min = $_POST['expiration-mn'];

			if ( !checkdate( $month, $day, $year ) ) {
				$errors[] = __( 'Invalid expiry date', 'wpuf' );
			}
			else {
				$post_expiry_date = mktime( $hour, $min, 59, $month, $day, $year );
				
				if (!$post_expiry_date) 
					$errors[] = __( 'Invalid expiry time', 'wpuf' );
			}
		}

		$errors = apply_filters( 'wpuf_add_post_validation', $errors );

		//if not any errors, proceed
		if ( $errors ) {
			$message = wpuf_error_msgs( $errors );
			echo '<root><success>false</success><message>' . htmlspecialchars( $message, ENT_QUOTES, "UTF-8" ) . '</message></root>';
			exit;
		}

		//process tags

		$tags = '';

		if ( isset( $_POST['wpuf_post_tags'] ) ) {
			$tags = wpuf_clean_tags( $_POST['wpuf_post_tags'] );
		}

		if ( !empty( $tags ) ) {
			$tags = explode( ',', $tags );
		}

		//Set author according to 'post author' option.
		//If user logged in and 'post_author' option is set to 'original' then set post author to user.
		//Else set post author to 'map_author' option value.
		if ($this->logged_in  && wpuf_get_option( 'post_author' ) == 'original') {
			$post_author = $userdata->ID;
		} else {
			$post_author = wpuf_get_option( 'map_author' );
		}

		$post_type_object = get_post_type_object( $post_type );

		//Set post status
		if ( wpuf_get_option( 'allow_status' ) == 'on' && $this->logged_in && isset( $_POST['wpuf_post_status'] ) ) {
			$post_stat = $_POST['wpuf_post_status'];

			// Prevent un-authorised publishment 
			if ( $post_stat != 'draft' && !user_can( $post_author, $post_type_object->cap->publish_posts ) ) 
				$post_stat = 'pending';
		} else {
			$post_stat = wpuf_get_option( 'post_status' );

			if ( $post_stat == 'default' ) {
				if( user_can( $post_author, $post_type_object->cap->publish_posts ) ) { 
					$post_stat = 'publish';
				}
				else {
					$post_stat = 'pending';
				}
			}
		}

		$post_update = array(
			'ID' => $post_id,
			'post_title' => $title,
			'post_content' => $content,
			'post_status' => $post_stat,
			'post_category' => $post_category,
			'tags_input' => $tags,
			'post_author' => $post_author,
		);

		if ( wpuf_get_option( 'allow_slug' ) == 'on' && isset( $_POST['wpuf_slug'] ) ) {
			$slug = trim( strip_tags(  $_POST['wpuf_slug'] ) );
			$post_update['post_name'] = $slug ;
		}

		if ( wpuf_get_option( 'allow_excerpt' ) == 'on' && isset( $_POST['wpuf_excerpt'] ) ) {
			$excerpt = trim( strip_tags(  $_POST['wpuf_excerpt'] ) );
			$post_update['post_excerpt'] = $excerpt ;
		}

		if ( $post_date_enable == 'on' ) {
			$post_update['post_date'] = $post_date ;
		}

		//plugin API to extend the functionality
		$post_update = apply_filters( 'wpuf_add_post_args', $post_update );

		//update the post
		$post_id = wp_update_post( $post_update, true);

		//if update post ok, proceed
		if (is_wp_error($post_id)) {
			$message = wpuf_error_msg( __( 'Post update failed. ', 'wpuf' ) . $post_id->get_error_message());
			echo '<root><success>false</success><message>' . htmlspecialchars( $message, ENT_QUOTES, "UTF-8" ) . '</message></root>';
			exit;
		}

		//send mail notification
		if ( wpuf_get_option( 'post_notification' ) == 'yes' ) {
			if (isset($userdata)) {
				wpuf_notify_post_mail( $userdata, $post_id );
			} else {
				//If not logged in user ($userdata null) map user to 'map_author' option
				wpuf_notify_post_mail(get_userdata(wpuf_get_option( 'map_author' )), $post_id );
			}
		}

		//add the custom fields
		if ( $custom_fields ) {
			foreach ($custom_fields as $key => $val) {
				add_post_meta( $post_id, $key, $val, true );
			}
		}

		//set post format
		if ( isset( $_POST['wpuf_post_format'] ) )
			set_post_format( $post_id, $_POST['wpuf_post_format'] );
		
		//set post expiration date
		if ( $post_expiry_enable == 'on' ) {
			add_post_meta( $post_id, 'expiration-date', $post_expiry_date, true);
		}

		//Attach featured image file to post  
		//$_POST['wpuf_featured_img']
		WPUF_Featured_Image::attach_file_to_post( $post_id );	
		
		//Attach attachment info  
		//$_POST['wpuf_attach_id'][] 
		//$_POST['wpuf_attach_title'][]
		WPUF_Attachment::attach_file_to_post( $post_id );	
		
		//plugin API to extend the functionality
		do_action( 'wpuf_add_post_after_insert', $post_id );

		//Set after post redirect
		
		$redirect_url = '';

		switch ( $_POST['wpuf_redirect'] ) {
			case "auto":
				if ( $_POST['wpuf_close'] == true )
					$redirect_url = $_POST['wpuf_referer'];
				break;
			case "none":
				break;
			case "new":
				$redirect_url = get_permalink( $post_id );
				break;
			case "last":
				$redirect_url = $_POST['wpuf_referer'];
				break;
			default:
				if ( $_POST['wpuf_redirect'] ) {
					//Redirect to url given by wpuf_redirect
					$redirect_url = add_query_arg( 'wpuf_referer', $_POST['wpuf_referer'], $redirect_url );
				} 		
		}

		$redirect_url = apply_filters( 'wpuf_post_redirect', 'add', 'insert', $redirect_url, $post_id );
		$redirect_url = htmlspecialchars( $redirect_url, ENT_QUOTES, "UTF-8" );
		
		$post_status = get_post_status($post_id);

		switch ($post_status) {
			case "publish":
				$message = '<div class="wpuf-success">' . __('Post published successfully', 'wpuf') . '</div>';
				break;
			case "pending":
				$message = '<div class="wpuf-success">' . __('Post pending review', 'wpuf') . '</div>';
				break;
			case "draft":
				$message = '<div class="wpuf-success">' . __('Post saved as draft', 'wpuf') . '</div>';
				break;
			case "future":
				$message = '<div class="wpuf-success">' . __('Post to be published in future', 'wpuf') . '</div>';
				break;
			default:
				$message = '<div class="wpuf-success">' . __('Post status', 'wpuf') . ': ' . $post_status . '</div></message>';
		}

		echo '<root><success>true</success><message>' . htmlspecialchars( $message, ENT_QUOTES, "UTF-8" ) . '</message><post_id>' . $post_id . '</post_id><redirect_url>' . $redirect_url . '</redirect_url></root>';
		
		exit;
	}

}

$wpuf_postform = new WPUF_Add_Post();

