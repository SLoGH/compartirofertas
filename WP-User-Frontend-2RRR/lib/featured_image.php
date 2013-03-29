<?php

/**
 * Featured Image Uploader class
 *
 * @author Tareq Hasan 
 * @package WP User Frontend
 * @version 1.1-fork-2RRR-4.3 
 * @since 1.1-fork-2RRR-3.0 
 */

/*
== Changelog ==

= 1.1-fork-2RRR-4.3 professor99 =
* Attaches featured image to post if unattached

= 1.1-fork-2RRR-3.0 professor99 =
* Compiled from functions in wpuf.php/wpuf-ajax.php/wpuf-functions.php in previous version
* Created new PHP object WPUF_Featured_Image
* Updated along similar lines as attachment.php
* Added float left to wpuf-ft-upload-container for IE7
*/

//Use static methods so ajax works without loading WPUF_Featured_Image::scripts()
add_action( 'wp_ajax_wpuf_feat_img_upload', 'WPUF_Featured_Image::upload_file' );
add_action( 'wp_ajax_wpuf_feat_img_del', 'WPUF_Featured_Image::delete_file' );

class WPUF_Featured_Image {

	/**
	 * Add javascript scripts and variables
	 *
	 * @since 1.1-fork-2RRR-3.0 	 
	 */
	static function scripts() {
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'plupload-handlers' );
		wp_enqueue_script( 'wpuf_featured_image', plugins_url( 'js/featured_image.js', dirname( __FILE__ ) ), array('jquery') );

		$feat_img_enabled = ( wpuf_get_option( 'enable_featured_image' ) == 'yes') ? true : false;
		
		wp_localize_script( 'wpuf_featured_image', 'wpuf_featured_image', array(
			'nonce' => wp_create_nonce( 'wpuf_featured_img_delete' ),
			'featEnabled'  => $feat_img_enabled,
			'plupload' => array(
				'runtimes' => 'html5,silverlight,flash,html4',
				'browse_button' => 'wpuf-ft-upload-pickfiles',
				'browse_button_hover' => 'wpuf-ft-upload-pickfiles-hover',
				'browse_button_active' => 'wpuf-ft-upload-pickfiles-active',
				'container' => 'wpuf-ft-upload-container',
				'file_data_name' => 'wpuf_featured_img',
				'max_file_size' => wp_max_upload_size() . 'b',
				'url' => admin_url( 'admin-ajax.php' ) . '?action=wpuf_feat_img_upload&nonce=' . wp_create_nonce( 'wpuf_featured_img_upload' ),
				'flash_swf_url' => includes_url( 'js/plupload/plupload.flash.swf' ),
				'silverlight_xap_url' => includes_url( 'js/plupload/plupload.silverlight.xap' ),
				'filters' => array(array('title' => __( 'Allowed Files' ), 'extensions' => '*')),
				'multipart' => true,
				'urlstream_upload' => true,
			)
		) );
	}

	/**
	 * Add featured image files to add/edit post forms
	 *
	 * @param string $post_type 
	 * @param object $curpost 
	 * @return string
	 * @since 1.1-fork-2RRR-3.0 	 
	 */
	static function add_post_fields( $post_type, $curpost = null ) {
		//Attach javascript
		WPUF_Featured_Image::scripts();
?>		
		<li id="wpuf-ft-upload-li">
			<label for="post-thumbnail"><?php echo wpuf_get_option( 'ft_image_label' ); ?></label>
			<!--[if IE 7]><div id="wpuf-ft-upload-container" style="float:left;"><![endif]-->
			<!--[if !(IE 7)|!(IE) ]><!--><div id="wpuf-ft-upload-container"><!--<![endif]-->
				<div id="wpuf-ft-upload-filelist">
					<?php
					$style = '';
					if ( $curpost && has_post_thumbnail( $curpost->ID ) ) {
						$style = ' style="visibility:hidden;"';

						$post_thumbnail_id = get_post_thumbnail_id( $curpost->ID );
						echo WPUF_Featured_Image::attach_html( $post_thumbnail_id );
					}
					?>
				</div>
				<a id="wpuf-ft-upload-pickfiles" class="wpuf-button" <?php echo $style?> href="#"><?php echo wpuf_get_option( 'ft_image_btn_label' ); ?></a>
			</div>
			<div class="clear"></div>
		</li>
<?php
	}

	/**
	 * Displays attachment information upon upload as featured image
	 *
	 * @param int $attach_id attachment id
	 * @return string
	 * @since 1.1-fork-2RRR-3.0 	 
	 */
	static function attach_html( $attach_id ) {
		$image = wp_get_attachment_image_src( $attach_id, 'thumbnail' );
		$post = get_post( $attach_id );

		$html = sprintf( '<div class="wpuf-item" id="attachment-%d">', $attach_id );
		$html .= sprintf( '<img src="%s" alt="%s" />', $image[0], esc_attr( $post->post_title ) );
		$html .= sprintf( '<a class="wpuf-del-ft-image button" href="#" data-id="%d">%s</a> ', $attach_id, __( 'Remove Image', 'wpuf' ) );
		$html .= sprintf( '<input type="hidden" name="wpuf_featured_img" value="%d" />', $attach_id );
		$html .= '</div>';

		return $html;
	}
		
	/**
	* Upload Featured image via ajax
	*
	* @return string
	* @since 1.1-fork-2RRR-3.0 	 
	*/
	static function upload_file() {
		check_ajax_referer( 'wpuf_featured_img_upload', 'nonce' );

		$upload_data = array(
			'name' => $_FILES['wpuf_featured_img']['name'],
			'type' => $_FILES['wpuf_featured_img']['type'],
			'tmp_name' => $_FILES['wpuf_featured_img']['tmp_name'],
			'error' => $_FILES['wpuf_featured_img']['error'],
			'size' => $_FILES['wpuf_featured_img']['size']
		);

		$attach_id = wpuf_upload_file( $upload_data );

		if ( $attach_id ) {
			$html = WPUF_Featured_Image::attach_html( $attach_id );

			$response = array(
				'success' => true,
				'html' => $html,
			);

			echo json_encode( $response );
			exit;
		}

		$response = array('success' => false);
		echo json_encode( $response );
		exit;
	}

	/**
	* Delete a featured image via ajax
	*
	* @return string
	* @since 1.1-fork-2RRR-3.0 	 
	*/
	static function delete_file() {
		check_ajax_referer( 'wpuf_featured_img_delete', 'nonce' );

		$attach_id = isset( $_POST['attach_id'] ) ? intval( $_POST['attach_id'] ) : 0;
		$attachment = get_post( $attach_id );

		//post author or editor role
		if ( get_current_user_id() == $attachment->post_author || current_user_can( 'delete_private_pages' ) ) {
			wp_delete_attachment( $attach_id, true );
			echo 'success';
		}

		exit;
	}

	/**
	* Attach a featured image to a post
	*
	* @since 1.1-fork-2RRR-3.0 	 
	*/
    static function attach_file_to_post( $post_id ) {
		//get featured image attach id 
		$attach_id = isset( $_POST['wpuf_featured_img'] ) ? intval( $_POST['wpuf_featured_img'] ) : 0;

		//set post thumbnail to featured image attach id
		if ( $attach_id ) {
			$attachment = get_post( $attach_id );
			
			// If this attachment is unattached, attach it. 
			if ( $attachment->post_parent == 0 ) 
				wp_update_post( array( 'ID' => $attach_id, 'post_parent' => $post_id ) );
			
			set_post_thumbnail( $post_id, $attach_id );
		}
    }
	
}
