<?php

/**
 * EditProfile class
 *
 * @author Tareq Hasan
 * @package WP User Frontend
 * @version 1.1-fork-2RRR-4.4
 */
 
/*
== Changelog ==

= 1.1-fork-2RRR-4.4 professor99 = 
* Added user_id GET parameter
* checkuser_id changed to user_id
* Added wpuf prefix to some selectors to harden style
* Enqueue wpuf and custom css and wpuf-editprofile.js
* Added wpuf div

= 1.1-fork-2RRR-4.3 professor99 = 
* Fixed wpuf_suppress_edit_post_link bug

= 1.1-fork-2RRR-2.1 professor99 = 
* Replaced anonymous function with suppress_edit_post_link()

= 1.1-fork-2RRR-2.0 professor99 =
* Suppress "edit_post_link" on this page
* Added wpuf prefix to some class names
*/

/**
 * Edit Profile Class
 * 
 * @author Tareq Hasan
 * @package WP User Frontend
 * @subpackage WPUF_Edit_Profile
 */
class WPUF_Edit_Profile {

	function __construct() {
		add_shortcode( 'wpuf_editprofile', array($this, 'shortcode') );

		add_action( 'personal_options_update', array($this, 'post_lock_update') );
		add_action( 'edit_user_profile_update', array($this, 'post_lock_update') );

		add_action( 'show_user_profile', array($this, 'post_lock_form') );
		add_action( 'edit_user_profile', array($this, 'post_lock_form') );
	}

	/**
	 * Enqueue scripts and styles
	 *
	 * @author Andrew Bruin (professor99)
	 * @since 1.1-fork-2RRR-4.4
	 */
	function enqueue() {
		$path = plugins_url( 'wp-user-frontend' );

		//Add wpuf css
		wp_enqueue_style( 'wpuf', $path . '/css/wpuf.css' );

		//Add custom css
		wp_add_inline_style( 'wpuf', wpuf_get_option( 'custom_css' ) );

		//Add scripts
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'wpuf-editprofile', $path . '/js/wpuf-editprofile.js', array( 'jquery' ) );
	}	

	/**
	 * Handles the editprofile shortcode
	 *
	 * @author Tareq Hasan
	 */
	function shortcode() {
		//Suppress "edit_post_link" on this page
		add_filter( 'edit_post_link', 'wpuf_suppress_edit_post_link', 10, 2 );

		//Enqueue scripts and styles
		$this->enqueue();

		ob_start();

		echo "<div id='wpuf'>\n";

		if ( is_user_logged_in() ) {
			$current_user = wp_get_current_user();
			$current_user_id = $current_user->ID ;

			if ( isset( $_POST['user_id'] ) ) {
				$user_id = intval( $_POST['user_id'] );
			} else if ( isset( $_GET['user_id'] ) ) {
				$user_id = intval( $_GET['user_id'] );
			} else {
				$user_id = $current_user_id;
			}

			if ( $user_id != $current_user_id && !current_user_can( 'edit_users' ) ) {
					printf( __( "You don't have permission for this purpose", 'wpuf' ) );
			} else {
					$this->show_form( $user_id );
			}
		} else {
			printf( __( "This page is restricted. Please %s to view this page.", 'wpuf' ), wp_loginout( '', false ) );
		}

		echo "</div>\n";

		$content = ob_get_contents();
		ob_end_clean();

		return $content;
	}

	/**
	 * Shows the user profile form
	 *
	 * @author Tareq Hasan
	 * @global type $userdata
	 *
	 * @param type $user_id
	 */
	function show_form( $user_id ) {
		global $userdata, $wp_http_referer;
		get_currentuserinfo();

		if ( !(function_exists( 'get_user_to_edit' )) ) {
			require_once(ABSPATH . '/wp-admin/includes/user.php');
		}

		if ( !(function_exists( '_wp_get_user_contactmethods' )) ) {
			require_once(ABSPATH . '/wp-includes/registration.php');
		}

		if ( isset( $_POST['submit'] ) ) {
			check_admin_referer( 'update-profile_' . $user_id );
			$errors = edit_user( $user_id );
			if ( is_wp_error( $errors ) ) {
				$message = $errors->get_error_message();
				$style = 'wpuf-error';
			} else {
				$message = __( '<strong>Success</strong>: Profile updated', 'wpuf' );
				$style = 'wpuf-success';
				do_action( 'personal_options_update', $user_id );
			}
		}

		$profileuser = get_user_to_edit( $user_id );

		if ( isset( $message ) ) {
			echo '<div class="' . $style . '">' . $message . '</div>';
		}
		?>
		<div class="wpuf-profile">
			<form name="profile" id="wpuf-your-profile" action="" method="post">
				<?php wp_nonce_field( 'update-profile_' . $user_id ) ?>
				<?php if ( $wp_http_referer ) : ?>
					<input type="hidden" name="wp_http_referer" value="<?php echo esc_url( $wp_http_referer ); ?>" />
				<?php endif; ?>
				<input type="hidden" name="from" value="profile" />
				<table class="wpuf-table">
					<?php do_action( 'personal_options', $profileuser ); ?>
				</table>
				<?php do_action( 'profile_personal_options', $profileuser ); ?>

				<fieldset>
					<legend><?php _e( 'Name' ) ?></legend>

					<table class="wpuf-table">
						<tr>
							<th><label for="wpuf-user_login1"><?php _e( 'Username' ); ?></label></th>
							<td><input type="text" name="user_login" id="wpuf-user_login1" value="<?php echo esc_attr( $profileuser->user_login ); ?>" disabled="disabled" class="regular-text" /><br /><em><span class="description"><?php _e( 'Usernames cannot be changed.' ); ?></span></em></td>
						</tr>
						<tr>
							<th><label for="wpuf-first_name"><?php _e( 'First Name' ) ?></label></th>
							<td><input type="text" name="first_name" id="wpuf-first_name" value="<?php echo esc_attr( $profileuser->first_name ) ?>" class="regular-text" /></td>
						</tr>

						<tr>
							<th><label for="wpuf-last_name"><?php _e( 'Last Name' ) ?></label></th>
							<td><input type="text" name="last_name" id="wpuf-last_name" value="<?php echo esc_attr( $profileuser->last_name ) ?>" class="regular-text" /></td>
						</tr>

						<tr>
							<th><label for="wpuf-nickname"><?php _e( 'Nickname' ); ?> <span class="description"><?php _e( '(required)' ); ?></span></label></th>
							<td><input type="text" name="nickname" id="wpuf-nickname" value="<?php echo esc_attr( $profileuser->nickname ) ?>" class="regular-text" /></td>
						</tr>

						<tr>
							<th><label for="wpuf-display_name"><?php _e( 'Display to Public as' ) ?></label></th>
							<td>
								<select name="display_name" id="wpuf-display_name">
									<?php
									$public_display = array();
									$public_display['display_username'] = $profileuser->user_login;
									$public_display['display_nickname'] = $profileuser->nickname;
									if ( !empty( $profileuser->first_name ) )
										$public_display['display_firstname'] = $profileuser->first_name;
									if ( !empty( $profileuser->last_name ) )
										$public_display['display_lastname'] = $profileuser->last_name;
									if ( !empty( $profileuser->first_name ) && !empty( $profileuser->last_name ) ) {
										$public_display['display_firstlast'] = $profileuser->first_name . ' ' . $profileuser->last_name;
										$public_display['display_lastfirst'] = $profileuser->last_name . ' ' . $profileuser->first_name;
									}
									if ( !in_array( $profileuser->display_name, $public_display ) ) // Only add this if it isn't duplicated elsewhere
										$public_display = array('display_displayname' => $profileuser->display_name) + $public_display;
									$public_display = array_map( 'trim', $public_display );
									$public_display = array_unique( $public_display );
									foreach ($public_display as $id => $item) {
										?>
										<option id="<?php echo $id; ?>" value="<?php echo esc_attr( $item ); ?>"<?php selected( $profileuser->display_name, $item ); ?>><?php echo $item; ?></option>
										<?php
									}
									?>
								</select>
							</td>
						</tr>
					</table>
				</fieldset>

				<fieldset>
					<legend><?php _e( 'Contact Info' ) ?></legend>

					<table class="wpuf-table">
						<tr>
							<th><label for="wpuf-email"><?php _e( 'E-mail' ); ?> <span class="description"><?php _e( '(required)' ); ?></span></label></th>
							<td><input type="text" name="email" id="wpuf-email" value="<?php echo esc_attr( $profileuser->user_email ) ?>" class="regular-text" /> </td>
						</tr>

						<tr>
							<th><label for="wpuf-url"><?php _e( 'Website' ) ?></label></th>
							<td><input type="text" name="url" id="wpuf-url" value="<?php echo esc_attr( $profileuser->user_url ) ?>" class="regular-text code" /></td>
						</tr>

						<?php
						foreach (_wp_get_user_contactmethods() as $name => $desc) {
							?>
							<tr>
								<th><label for="<?php echo $name; ?>"><?php echo apply_filters( 'user_' . $name . '_label', $desc ); ?></label></th>
								<td><input type="text" name="<?php echo $name; ?>" id="<?php echo $name; ?>" value="<?php echo esc_attr( $profileuser->$name ) ?>" class="regular-text" /></td>
							</tr>
							<?php
						}
						?>
					</table>
				</fieldset>

				<fieldset>
					<legend><?php _e( 'About Yourself' ); ?></legend>

					<table class="wpuf-table">
						<tr>
							<th><label for="wpuf-biographical"><?php _e( 'Biographical Info', 'wpuf' ); ?></label></th>
							<td><textarea name="description" id="wpuf-biographical" rows="5" cols="30"><?php echo esc_html( $profileuser->description ); ?></textarea><br />
								<span class="description"><?php _e( 'Share a little biographical information to fill out your profile. This may be shown publicly.' ); ?></span></td>
						</tr>
						<tr id="wpuf-password">
							<th><label for="wpuf-pass1"><?php _e( 'New Password', 'wpuf' ); ?></label></th>
							<td>
								<input type="password" name="pass1" id="wpuf-pass1" size="16" value="" autocomplete="off" /><br /><br />
							</td>
						</tr>
						<tr>
							<th><label><?php _e( 'Confirm Password', 'wpuf' ); ?></label></th>
							<td>
								<input type="password" name="pass2" id="wpuf-pass2" size="16" value="" autocomplete="off" />&nbsp;<em><span class="description"><?php _e( "Type your new password again." ); ?></span></em>
							</td>
						</tr>
						<tr>

							<th><label><?php _e( 'Password Strength', 'wpuf' ); ?></label></th>
							<td>
								<div id="wpuf-pass-strength-result"><?php _e( 'Strength indicator' ); ?></div>
								<script src="<?php echo admin_url(); ?>/js/password-strength-meter.js"></script>
								<script type="text/javascript">
									var pwsL10n = {
										empty: "Strength indicator",
										short: "Very weak",
										bad: "Weak",
										good: "Medium",
										strong: "Strong",
										mismatch: "Mismatch"
									};
									try{convertEntities(pwsL10n);}catch(e){};
								</script>
							</td>
						</tr>
					</table>
				</fieldset>

				<?php do_action( 'show_user_profile', $profileuser ); ?>

				<p class="wpuf-submit-p">
					<input type="hidden" name="action" value="update" />
					<input type="hidden" name="user_id" id="wpuf-user-id" value="<?php echo esc_attr( $user_id ); ?>" />
					<input type="submit" class="wpuf-submit" value="<?php _e( 'Update Profile', 'wpuf' ); ?>" name="submit" />
				</p>
			</form>
		</div>
		<?php
	}

	/**
	 * Adds the postlock form in users profile
	 *
	 * @author Tareq Hasan
	 *
	 * @param object $profileuser
	 */
	function post_lock_form( $profileuser ) {
		global $wpuf_subscription;

		if ( is_admin() && current_user_can( 'edit_users' ) ) {
			$select = ( $profileuser->wpuf_postlock == 'yes' ) ? 'yes' : 'no';
			?>

			<h3><?php _e( 'WPUF Post Lock', 'wpuf' ); ?></h3>
			<table class="wpuf-form-table">
				<tr>
					<th><label for="wpuf-post-lock"><?php _e( 'Lock Post:', 'wpuf' ); ?> </label></th>
					<td>
						<select name="wpuf_postlock" id="wpuf-post-lock">
							<option value="no"<?php selected( $select, 'no' ); ?>>No</option>
							<option value="yes"<?php selected( $select, 'yes' ); ?>>Yes</option>
						</select>
						<span class="description"><?php _e( 'Lock user from creating new post.', 'wpuf' ); ?></span></em>
					</td>
				</tr>

				<tr>
					<th><label for="wpuf-lock-cause"><?php _e( 'Lock Reason:', 'wpuf' ); ?> </label></th>
					<td>
						<input type="text" name="wpuf_lock_cause" id="wpuf-lock-cause" class="wpuf-regular-text" value="<?php echo esc_attr( $profileuser->wpuf_lock_cause ); ?>" />
					</td>
				</tr>
			</table>

			<?php
			if ( wpuf_get_option( 'charge_posting' ) == 'yes' ) {
				$validity = (isset( $profileuser->wpuf_sub_validity )) ? $profileuser->wpuf_sub_validity : date( 'Y-m-d G:i:s', time() );
				$count = ( isset( $profileuser->wpuf_sub_pcount ) ) ? $profileuser->wpuf_sub_pcount : 0;

				if ( isset( $profileuser->wpuf_sub_pack ) ) {
					$pack = $wpuf_subscription->get_subscription( $profileuser->wpuf_sub_pack );
					$pack = $pack->name;
				} else {
					$pack = 'Free';
				}
				?>

				<h3><?php _e( 'WPUF Subscription', 'wpuf' ); ?></h3>

				<table class="wpuf-form-table">
					<tr>
						<th><label for="wpuf-sub-pack"><?php _e( 'Pack:', 'wpuf' ); ?> </label></th>
						<td>
							<input type="text" disabled="disabled" name="wpuf_sub_pack" id="wpuf-sub-pack" class="wpuf-regular-text" value="<?php echo $pack; ?>" />
						</td>
					</tr>
					<tr>
						<th><label for="wpuf-sub-pcount"><?php _e( 'Post Count:', 'wpuf' ); ?> </label></th>
						<td>
							<input type="text" name="wpuf_sub_pcount" id="wpuf-sub-pcount" class="wpuf-regular-text" value="<?php echo $count; ?>" />
						</td>
					</tr>
					<tr>
						<th><label for="wpuf-sub-validity"><?php _e( 'Validity:', 'wpuf' ); ?> </label></th>
						<td>
							<input type="text" name="wpuf_sub_validity" id="wpuf-sub-validity" class="wpuf-regular-text" value="<?php echo $validity; ?>" />
						</td>
					</tr>
				</table>

			<?php } ?>

			<?php
		}
	}

	/**
	 * Update user profile lock
	 *
	 * @author Tareq Hasan
	 *
	 * @param int $user_id
	 */
	function post_lock_update( $user_id ) {
		if ( is_admin() && current_user_can( 'edit_users' ) ) {
			update_user_meta( $user_id, 'wpuf_postlock', $_POST['wpuf_postlock'] );
			update_user_meta( $user_id, 'wpuf_lock_cause', $_POST['wpuf_lock_cause'] );
			update_user_meta( $user_id, 'wpuf_sub_validity', $_POST['wpuf_sub_validity'] );
			update_user_meta( $user_id, 'wpuf_sub_pcount', $_POST['wpuf_sub_pcount'] );
		}
	}

}

$edit_profile = new WPUF_Edit_Profile();