<?php

/**
 * Edit Post form class
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
* If no Post Status field then reset the post's status to the default.
* wpuf_delete_post no longer uses wpuf_self
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

= 1.1-fork-2RRR-4.1 professor99 =
* Implemented Post Format field.

= 1.1-fork-2RRR-4.0 professor99 =
* Implemented "enable_post_edit" default option.
* Implemented "enable_post_del" default option.
* Better language support for info div
* Enhanced security
* Added $post_id parameter to wpuf_can_edit filter.
* Added wpuf_can_delete filter
* Fixed Description alignment for all users

= 1.1-fork-2RRR-3.0 professor99 =
* Added excerpt
* Added publish date
* Added expiration time
* Re-styled form to suit
* Made attachment calls inline (was actions)
* Featured image html moved to WPUF_Featured_Image::add_post_fields()
* Removed attachment code replaced by ajax
* Fixed custom fields update bug
* Can display top line info message anytime
* Removed 'Your theme doesn't support featured image' message
* Fixed javascript success clear form bug
* Added optional delete button
* Redirects now filtered by wpuf_post_redirect 
* Form actions consolidated under wpuf_post_form
* Escaped info message XML tags
* Added wpuf_error_msgs();

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
* Code updated to allow use of wpuf_can_post filter for non-logged in users.
* Post updated to new post on save
*/
 
/*
 *  Shortcode examples::
 * 
 * 	[wpuf_edit]
 * 	[wpuf_edit close="false"]
 * 	[wpuf_edit close="false" redirect="none"]
 *
 *  Shortcode options:
 * 
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
 * For this file $form = 'edit'.
 *
 * The filter 'wpuf_post_redirect' is common to both this file and wpuf_add_post.php.
 * It is invoked as function($form, $location, $redirect_url, $post_id).
 * For this file $form = 'edit'.
 */ 
 
/**
 * Edit Post Class
 * 
 * @author Tareq Hasan
 * @package WP User Frontend
 * @subpackage WPUF_Edit_Post
 */
class WPUF_Edit_Post {
	var $wpuf_self = '';
	var $wpuf_referer = '';
	var $logged_in = false;

	function __construct() {
		//Ajax calls for Update Post button
		add_action('wp_ajax_wpuf_edit_post_action', array($this, 'submit_post'));
		add_action('wp_ajax_nopriv_wpuf_edit_post_action', array($this, 'submit_post'));

		//Ajax calls for Delete Post button
		add_action('wp_ajax_wpuf_delete_post_action', array($this, 'delete_post'));
		add_action('wp_ajax_nopriv_wpuf_delete_post_action', array($this, 'delete_post'));
		
		add_shortcode( 'wpuf_edit', array($this, 'shortcode') );
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
		wp_enqueue_script( 'wpuf-edit-post', $path . '/js/wpuf-edit-post.js', array( 'jquery', 'jquery-form' ) );

		//Create wpuf javascript object
		wpuf_post_localize();
	}	

	/**
	 * Delete a post
	 *
	 * @author Andrew Bruin (professor99)
	 * @since 1.1-fork-2RRR-3.0 
	 */
	function delete_post() {
		//Set header content type to XML
		header( 'Content-Type: text/xml' );

		$post_id = trim( $_POST['post_id'] );
		
		if ( !wp_verify_nonce( $_POST['nonce'], 'wpuf-delete-post' . $post_id  ) ) {
			$message = wpuf_error_msg( __( 'Cheating?' ) );
			echo '<root><success>false</success><message>' . htmlspecialchars( $message, ENT_QUOTES, "UTF-8" ) . '</message></root>';
			exit;
		}

		//Delete post
		wp_delete_post( $post_id );
		
		//Set after delete redirect

		$redirect_url = '';

		switch ($_POST['wpuf_redirect']) {
			case "auto":
				if ( $_POST['wpuf_close'] == true )
					$redirect_url = $_POST['wpuf_referer'];
				break;
			case "last":
				$redirect_url = $_POST['wpuf_referer'];
				break;
		}
		
		//Use this filter if you want to change the return address on delete
		$redirect_url = apply_filters( 'wpuf_post_redirect', 'edit', 'delete', $redirect_url, $post_id );
		$redirect_url = htmlspecialchars( $redirect_url, ENT_QUOTES, "UTF-8" );

		$message = wpuf_error_msg( __('Post deleted', 'wpuf') );
		echo '<root><success>true</success><message>' . htmlspecialchars( $message, ENT_QUOTES, "UTF-8" ) . '</message><post_id>' . $post_id . '</post_id><redirect_url>' . $redirect_url . '</redirect_url></root>';
		
		exit;
	}

	/**
	 * Handles the edit post shortcode
	 *
	 * Generates the edit post form
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

		extract( shortcode_atts( array('close' => 'true', 'redirect' => 'auto'), $atts ) );

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

		//Get updated post
		$post_id = isset( $_GET['pid'] ) ? intval( $_GET['pid'] ) : 0;
		$post = get_post( $post_id );

		$invalid = false;
		$can_edit = 'yes';
		$info = ''; 

		$enable_post_edit = wpuf_get_option( 'enable_post_edit', 'default' );

		if ( !$post ) {
			$invalid = true;
			$can_edit = 'no';
			$info = 'Invalid post';
		}
		else if ( $enable_post_edit == 'no' ) {
			$can_edit = 'no';
			$info = 'Post Editing is disabled';
		} 
		else if (!$this->logged_in) {
			$can_edit = 'no';
			$info = 'restricted';
		}
		else if ( !current_user_can( 'edit_post', $post_id ) ) {
			if ( $enable_post_edit != 'yes' || $userdata->ID != $post->post_author ) {
				$can_edit = 'no';
				$info = 'You are not allowed to edit this post';
			}
		}

		if (!$invalid) {
			$can_edit = apply_filters( 'wpuf_can_edit', $can_edit, $post_id );
			$info = apply_filters( 'wpuf_editpost_notice', $info );
		}

		if ($info) {
			if ( $info == 'restricted' )
				$info = sprintf( wpuf_get_option( 'login_label' ), wp_loginout( $login_url, false ) );
			else
				$info = __( $info, 'wpuf' );

			echo '<div class="wpuf-info">' . $info . '</div>';
		}

		if ( $can_edit == 'yes' ) {
			//show post form
			$this->edit_form( $post, $close, $redirect );

			if ( wpuf_get_option( 'enable_delete_button' ) == 'yes' ) {
				$can_delete = false;

				$enable_post_del = wpuf_get_option( 'enable_post_del', 'default' );

				if ( $enable_post_del != 'no' && $this->logged_in ) {
					if ( current_user_can( 'delete_post', $post_id ) ) {
						$can_delete = true;
					} else if ( $enable_post_del == 'yes' && $userdata->ID == $post->post_author ) {
						$can_delete = true;
					}	
				}

				$can_delete = apply_filters( 'wpuf_can_delete', $can_delete, $post_id );

				if ( $can_delete ) {
					$referer = htmlspecialchars( $this->wpuf_referer, ENT_QUOTES, "UTF-8" );
					$nonce = wp_create_nonce( 'wpuf-delete-post' . $post_id );
					$onclick = "wpuf_delete_post( $post_id, \"$redirect\", \"$close\", \"$referer\", \"$nonce\" );return false;";
					$delete_label = esc_attr( wpuf_get_option( 'delete_label' ) );
					echo '<div id="wpuf-button-delete"><button class="wpuf-button" type="button" onclick=\'' . $onclick . '\'>' . $delete_label . '</button></div>' . "\n";
				}	
			} 
		}

		//Use this filter if you want to change the return address on Close
		$redirect_url = apply_filters( 'wpuf_post_redirect', 'add', 'close', $this->wpuf_referer, '');

		if ($redirect_url != "" && $close == "true") {
			$redirect_url = htmlspecialchars( $redirect_url, ENT_QUOTES, "UTF-8" );
			echo '<div id="wpuf-button-close"><a class="wpuf-button" href="' . $redirect_url . '">' . esc_attr( wpuf_get_option( 'close_label' ) ) . '</a></div>' . "\n";
		}

		echo "</div>\n";
		$content = ob_get_contents();
		ob_end_clean();
		return $content;
	}

	/**
	 * Main edit post form
	 *
	 * @author Tareq Hasan
	 * @global $userdata;
	 * @global wpdb $wpdb
	 * @global WPUF_Cat $wpuf_cat
	 *
	 * @param WP_Post $post Post object
	 * @param string $close Display Close Button "true"|"false"
	 * @param string $redirect Redirect after post "none" | "auto" | "new" | "last" | %url%
	 */
	function edit_form( $post, $close, $redirect ) {
		global $userdata;
		global $wpdb;
		global $wpuf_cat;

		$post_id = $post->ID;
		$post_type = $post->post_type;

		//Fix Insert Media Bug
		wpuf_insert_media_fix( $post_id );

		$post_tags = wp_get_post_tags( $post_id );
		$tagsarray = array();

		foreach ($post_tags as $tag) {
			$tagsarray[] = $tag->name;
		}
		
		$tagslist = implode( ', ', $tagsarray );
		$featured_image = wpuf_get_option( 'enable_featured_image' );
?>
		<form id="wpuf_edit_post_form" name="wpuf_edit_post_form" action="" enctype="multipart/form-data" method="POST">
			<?php wp_nonce_field( 'wpuf-edit-post' . $post_id ) ?>
			<ul class="wpuf-post-form">
				<?php 
				do_action( 'wpuf_post_form', 'edit', 'top', $post_type, $post ); 
				wpuf_build_custom_field_form( 'top', true, $post_id );

				if ( $featured_image == 'yes' && current_theme_supports( 'post-thumbnails' ) ) {
					WPUF_Featured_Image::add_post_fields( $post_type, $post );
				}
				?>

				<li>
					<label for="wpuf-post-title">
						<?php echo wpuf_get_option( 'title_label' ); ?> <span class="required">*</span>
					</label>
					<input class="requiredField" type="text" name="wpuf_post_title" id="wpuf-post-title" minlength="2" value="<?php echo esc_html( $post->post_title ); ?>">
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
				
				do_action( 'wpuf_post_form', 'edit', 'description', $post_type, $post ); 
				wpuf_build_custom_field_form( 'description', true, $post_id );

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
						wp_editor( $post->post_content, 'wpuf-post-content', array('textarea_name' => 'wpuf_post_content', 'editor_class' => 'requiredField', 'teeny' => false, 'textarea_rows' => 8) );
					} else if ( $editor == 'rich' ) {
						wp_editor( $post->post_content, 'wpuf-post-content', array('textarea_name' => 'wpuf_post_content', 'editor_class' => 'requiredField', 'teeny' => true, 'textarea_rows' => 8) );
					} else if ( $editor == 'plain' ) { 
					?>
						<textarea name="wpuf_post_content" class="requiredField wpuf-editor-plain" id="wpuf-post-content" cols="60" rows="8"><?php echo esc_textarea( $post->post_content ); ?></textarea>
					<?php 
					} else {
						//Use custom editor. 
						//Two ways to enable.
						//1. wpuf_editor_type filter above.
						//2. showtime_wpuf_options_frontend filter.
						do_action('wpuf_custom_editor', $editor, $post->post_content, 'wpuf-post-content', 'wpuf_post_content');
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
				do_action( 'wpuf_post_form', 'edit', 'after_description', $post_type, $post ); 
				wpuf_build_custom_field_form( 'after_description', true, $post_id );

				if ( wpuf_get_option( 'allow_excerpt' ) == 'on' ) {
					$max_chars = wpuf_get_option( 'excerpt_max_chars' );

					//Get excerpt
					$query = "SELECT post_excerpt FROM $wpdb->posts WHERE ID=$post_id LIMIT 1";
					$result = $wpdb->get_results($query, ARRAY_A);
					$excerpt = $result[0]['post_excerpt'];						
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
								<textarea class="requiredField" id="wpuf-excerpt" name="wpuf_excerpt" cols="80" rows="2" <?php echo $maxlength ?> ><?php echo $excerpt; ?></textarea>
							<?php
							} else {
							?>
								<textarea id="wpuf-excerpt" name="wpuf_excerpt" cols="80" rows="2" <?php echo $maxlength ?> ><?php echo $excerpt; ?></textarea>
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
						<input type="text" name="wpuf_post_tags" id="wpuf-post-tags" value="<?php echo $tagslist; ?>">
						<div class="clear"></div>
						<?php
						$helptxt = stripslashes( wpuf_get_option( 'tag_help' ) );
						if ($helptxt) 
							echo '<p class="description">' . $helptxt . '</p>';
						?>
					</li>
				<?php 
				}

				do_action( 'wpuf_post_form', 'edit', 'tag', $post_type, $post ); 
				wpuf_build_custom_field_form( 'tag', true, $post_id ); 
				
				//Add attachment fields if enabled
				
				$allow_upload = wpuf_get_option( 'allow_attachment' );
				
				if ( $allow_upload == 'yes' ) {
					WPUF_Attachment::add_post_fields( $post_type, $post );
				}

				//Add Post Format field if enabled and is supported by current theme and post type

				if ( wpuf_get_option( 'allow_format' ) == 'on' && current_theme_supports( 'post-formats' ) && post_type_supports( $post_type, 'post-formats' ) ) {
					$post_formats = get_theme_support( 'post-formats' );
					
					if ( is_array( $post_formats[0] ) ) {
						$post_format = get_post_format( $post_id );

						if ( !$post_format ) {
							$post_format = '0';
						} else if ( !in_array( $post_format, $post_formats[0] ) ) {
							// Add the format to the post format array if it isn't there.
							$post_formats[0][] = post_format;
						}	
				?>
						<li>
							<label for="wpuf_post_format">
								<?php echo wpuf_get_option( 'format_label' ); ?>
							</label>

							<select name="wpuf_post_format"  id="wpuf_post_format">
								<option <?php selected( $post_format, '0' ); ?> value="0" > <?php _e('Standard'); ?></option>
								<?php 
								foreach ( $post_formats[0] as $format ) { 
								?>
									<option <?php selected( $post_format, $format ); ?> value="<?php echo esc_attr( $format ); ?>" > <?php echo esc_html( get_post_format_string( $format ) ); ?></option>
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
						$wpuf_cat->select( $post_id );				
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
				if ( wpuf_get_option( 'allow_status' ) == 'on' && $this->logged_in ) {
					$post_status = get_post_status( $post_id ) ;

					$post_type_object = get_post_type_object( $post_type );
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

				$this->publish_date_form( $post );
				$this->expiry_date_form( $post );

				wpuf_build_custom_field_form( 'bottom', true, $post_id ); 

				do_action( 'wpuf_post_form', 'edit', 'submit', $post_type, $post ); 
				?>

				<li id="wpuf-submit-li">
					<div id="wpuf-info-msg">&nbsp;</div>
					<input class="wpuf-submit" type="submit" name="wpuf_edit_post_submit" value="<?php echo esc_attr( wpuf_get_option( 'update_label' ) ); ?>">
					<input type="hidden" name="post_id" value="<?php echo $post_id; ?>">
					<input type="hidden" name="wpuf_close" value="<?php echo $close ?>" />
					<input type="hidden" name="wpuf_redirect" value="<?php echo $redirect ?>" />
					<input type="hidden" name="wpuf_referer" value="<?php echo $this->wpuf_referer ?>" />
				</li>

				<?php do_action( 'wpuf_post_form', 'edit', 'bottom', $post_type, $post ); ?>
			</ul>
		</form>
<?php
	}

	/**
	 * Prints the post publish date on form
	 *
	 * @author Andrew Bruin (professor99)
	 * @since 1.1-fork-2RRR-3.0 
	 */
	function publish_date_form( $curpost ) {
		$enable_date = wpuf_get_option( 'enable_post_date' );

		if ( $enable_date != 'on' ) {
				return;
		}

		$datetime = $curpost->post_date;
		
		sscanf( $datetime, "%4s-%2s-%2s %2s:%2s:%2s", $year, $month, $day, $hour, $minute, $second );

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
				<input class="requiredField" type="text" autocomplete="off" tabindex="4" maxlength="2" size="2" value="<?php echo $day; ?>" name="jj">,
				<input class="requiredField" type="text" autocomplete="off" tabindex="4" maxlength="4" size="4" value="<?php echo $year; ?>" name="aa">
				@ <input class="requiredField" type="text" autocomplete="off" tabindex="4" maxlength="2" size="2" value="<?php echo $hour; ?>" name="hh">
				: <input class="requiredField" type="text" autocomplete="off" tabindex="4" maxlength="2" size="2" value="<?php echo $minute; ?>" name="mn">
			</div>
			<div class="clear"></div>
			<p class="description"></p>
		</li>
<?php
	}

	/**
	 * Prints post expiration date on the form
	 *
	 * @author Andrew Bruin (professor99)
	 * @since 1.1-fork-2RRR-3.0 
	 */
	function expiry_date_form( $curpost ) {
		$post_expiry = wpuf_get_option( 'enable_post_expiry' );

		if ( $post_expiry != 'on' ) {
			return;
		}

		$expiration_date = get_post_meta($curpost->ID, 'expiration-date', true);
		
		if ($expiration_date) {
			$checked = 'checked="checked"';
			$datetime = date('Y-m-d H:i:s', $expiration_date);
		} else {
			$checked = '';
			$datetime = $curpost->post_date;
		}

		sscanf( $datetime, "%4s-%2s-%2s %2s:%2s:%2s", $year, $month, $day, $hour, $minute, $second );

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
				<input class="requiredField" type="text" autocomplete="off" tabindex="4" maxlength="2" size="2" value="<?php echo $day; ?>" name="expiration-jj">,
				<input class="requiredField" type="text" autocomplete="off" tabindex="4" maxlength="4" size="4" value="<?php echo $year; ?>" name="expiration-aa">
				@ <input class="requiredField" type="text" autocomplete="off" tabindex="4" maxlength="2" size="2" value="<?php echo $hour; ?>" name="expiration-hh">
				: <input class="requiredField" type="text" autocomplete="off" tabindex="4" maxlength="2" size="2" value="<?php echo $minute; ?>" name="expiration-mn">
				<input type="checkbox" tabindex="4" value="on" <?php echo $checked ?> name="expiration-enable">Enable
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

		//$message = '<div>REQUEST=' . print_r($_REQUEST, true) . '<br>POST=' . print_r($_POST,true) . '<br>$_SERVER='. print_r($_SERVER,true) . '<br>$userdata=' . print_r($userdata,true) . '</div>' ;
 		//echo '<root><success>false></success><message>' . <htmlspecialchars($message, ENT_QUOTES, "UTF-8") . '</message></root>'; 

		$post_id = trim( $_POST['post_id'] );
		$title = trim( $_POST['wpuf_post_title'] );
		$content = trim( $_POST['wpuf_post_content'] );

		$post = get_post( $post_id );
		$post_type = $post->post_type;

		//Set header content type to XML
		header( 'Content-Type: text/xml' );

		if ( !wp_verify_nonce( $_REQUEST['_wpnonce'], 'wpuf-edit-post' . $post_id) ) {
			$message = wpuf_error_msg( __( 'Cheating?' ) );
			echo '<root><success>false</success><message>' . htmlspecialchars( $message, ENT_QUOTES, "UTF-8" ) . '</message></root>';
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
		
		if ( $post_expiry_enable == 'on' && isset( $_POST['expiration-enable'] ) && $_POST['expiration-enable'] == 'on') {
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

		$errors = apply_filters( 'wpuf_edit_post_validation', $errors );

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

		$post_author = $userdata->ID;

		$post_type_object = get_post_type_object( $post_type );

		//Set post status
		if ( wpuf_get_option( 'allow_status' ) == 'on' && isset( $_POST['wpuf_post_status'] ) ) {
			$post_stat = $_POST['wpuf_post_status'];

			// Prevent un-authorised publishment 
			if ( $post_stat != 'draft' && !user_can( $post_author, $post_type_object->cap->publish_posts ) ) 
				$post_stat = 'pending';
		} else {
			//Reset post status according to post_status option
			
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
			'tags_input' => $tags
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

		//add the custom fields
		if ( $custom_fields ) {
			foreach ($custom_fields as $key => $val) {
				update_post_meta( $post_id, $key, $val );
			}
		}

		//set post format
		if ( isset( $_POST['wpuf_post_format'] ) )
			set_post_format( $post_id, $_POST['wpuf_post_format'] );

		//set post expiration date
		if ( $post_expiry_enable == 'on' ) {
			update_post_meta( $post_id, 'expiration-date', $post_expiry_date);
		}

		//Attach featured image file to post  
		//$_POST['wpuf_featured_img']
		WPUF_Featured_Image::attach_file_to_post( $post_id );	
	
		//Attach attachment info  
		//$_POST['wpuf_attach_id'][] 
		//$_POST['wpuf_attach_title'][]
		WPUF_Attachment::attach_file_to_post( $post_id );	
		
		//plugin API to extend the functionality
		do_action( 'wpuf_edit_post_after_update', $post_id );

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

		$message = '<div class="wpuf-success">' . __('Post updated succesfully', 'wpuf') . '</div>';
		
		echo '<root><success>true</success><message>' . htmlspecialchars( $message, ENT_QUOTES, "UTF-8" ) . '</message><post_id>' . $post_id . '</post_id><redirect_url>' . $redirect_url . '</redirect_url></root>';
		exit;
	}

}

$wpuf_edit = new WPUF_Edit_Post();