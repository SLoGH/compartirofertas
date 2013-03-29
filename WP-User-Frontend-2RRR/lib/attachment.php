<?php

/**
 * Attachment Uploader class
 *
 * @author Tareq Hasan 
 * @package WP User Frontend
 * @version 1.1-fork-2RRR-3.0 
 * @since 0.8
 */

/*
== Changelog ==

= 1.1-fork-2RRR-3.0 professor99 =
* Attachment calls for edit/add post now direct (non-actions)
* Scripts now loaded by add_post_fields()
* All methods now static
* Ajax actions now global
* Removed unnecessary spans in attach_html()
* Renamed nonces wpuf_attachment_update/wpuf_attachment_delete 
* Fixed hover for attachment button

= 1.1-fork-2RRR-2.0 professor99 =
* Re-styled attachments.
* Only add scripts for add/edit post shortcodes
*/

//Use static methods so ajax works without loading WPUF_Attachment::scripts()
add_action( 'wp_ajax_wpuf_attach_upload', 'WPUF_Attachment::upload_file' );
add_action( 'wp_ajax_wpuf_attach_del', 'WPUF_Attachment::delete_file' );

class WPUF_Attachment {

    static function scripts() {
        $max_file_size = intval( wpuf_get_option( 'attachment_max_size' ) ) * 1024;
        $max_upload = intval( wpuf_get_option( 'attachment_num' ) );
        $attachment_enabled = wpuf_get_option( 'allow_attachment' );

        wp_enqueue_script( 'jquery' );
        wp_enqueue_script( 'plupload-handlers' );
        wp_enqueue_script( 'jquery-ui-sortable' );
        wp_enqueue_script( 'wpuf_attachment', plugins_url( 'js/attachment.js', dirname( __FILE__ ) ), array('jquery') );

        wp_localize_script( 'wpuf_attachment', 'wpuf_attachment', array(
            'nonce' => wp_create_nonce( 'wpuf_attachment_delete' ),
            'number' => $max_upload,
            'attachment_enabled' => ($attachment_enabled == 'yes') ? true : false,
            'plupload' => array(
                'runtimes' => 'html5,silverlight,flash,html4',
                'browse_button' => 'wpuf-attachment-upload-pickfiles',
                'browse_button_hover' => 'wpuf-attachment-upload-pickfiles-hover',
                'browse_button_active' => 'wpuf-attachment-upload-pickfiles-active',
                'container' => 'wpuf-attachment-upload-container',
                'file_data_name' => 'wpuf_attachment_file',
                'max_file_size' => $max_file_size . 'b',
                'url' => admin_url( 'admin-ajax.php' ) . '?action=wpuf_attach_upload&nonce=' . wp_create_nonce( 'wpuf_attachment_upload' ),
                'flash_swf_url' => includes_url( 'js/plupload/plupload.flash.swf' ),
                'silverlight_xap_url' => includes_url( 'js/plupload/plupload.silverlight.xap' ),
                'filters' => array(array('title' => __( 'Allowed Files' ), 'extensions' => '*')),
                'multipart' => true,
                'urlstream_upload' => true,
            )
        ) );
    }

    static function add_post_fields( $post_type, $curpost = null ) {
		//Attach javascript
        WPUF_Attachment::scripts();
		
        $attachments = array();

        if ( $curpost ) {
            $attachments = wpfu_get_attachments( $curpost->ID );
        }
        ?>
        <li>
            <label><?php echo wpuf_get_option( 'attachment_label' ) ?></label>
            <div class="clear"></div>
        </li>
        <li id="wpuf-attachment-upload-li">
            <div id="wpuf-attachment-upload-container">
                <div id="wpuf-attachment-upload-filelist">
                    <ul class="wpuf-attachment-list">
                        <script>window.wpufFileCount = 0;</script>
                        <?php
                        if ( $attachments ) {
                            foreach ($attachments as $attach) {
                                echo WPUF_Attachment::attach_html( $attach['id'] );
                                echo '<script>window.wpufFileCount += 1;</script>';
                            }
                        }
                        ?>
                    </ul>
                </div>
                <a id="wpuf-attachment-upload-pickfiles" class="wpuf-button" href="#"><?php echo wpuf_get_option( 'attachment_btn_label' ); ?></a>
            </div>
            <div class="clear"></div>
        </li>
        <?php
    }

    static function attach_html( $attach_id ) {

        $attachment = get_post( $attach_id );
        $fileurl = wp_get_attachment_url($attach_id);
        $filebase =  esc_attr( basename($fileurl) );
		
        $html = '';
        $html .= '<li class="wpuf-attachment">';
        $html .= '<span class="handle">Move</span>';
		$html .= '<span class="required">*&nbsp;</span>';
        $html .= sprintf( '<input type="text" class="attachment-title requiredField" name="wpuf_attach_title[]" value="%s" placeholder="%s" title="%s"/>', esc_attr( $attachment->post_title ), esc_attr__( 'Title', 'wpuf' ), esc_attr__( 'Title', 'wpuf' ) );
        $html .= sprintf( '<a class="attachment-name" href="%s">%s</a>', $fileurl, $filebase );
        $html .= sprintf( '<a href="#" class="attachment-actions track-delete button" data-attach_id="%d">%s</a>', $attach_id, __( 'Delete', 'wpuf' ) );
        $html .= sprintf( '<input type="hidden" name="wpuf_attach_id[]" value="%d" />', $attach_id );
        $html .= '</li>';

        return $html;
    }

    static function upload_file() {
        check_ajax_referer( 'wpuf_attachment_upload', 'nonce' );

        $upload = array(
            'name' => $_FILES['wpuf_attachment_file']['name'],
            'type' => $_FILES['wpuf_attachment_file']['type'],
            'tmp_name' => $_FILES['wpuf_attachment_file']['tmp_name'],
            'error' => $_FILES['wpuf_attachment_file']['error'],
            'size' => $_FILES['wpuf_attachment_file']['size']
        );

        $attach_id = wpuf_upload_file( $upload );

        if ( $attach_id ) {
            $html = WPUF_Attachment::attach_html( $attach_id );

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

    static function delete_file() {
        check_ajax_referer( 'wpuf_attachment_delete', 'nonce' );

        $attach_id = isset( $_POST['attach_id'] ) ? intval( $_POST['attach_id'] ) : 0;
        $attachment = get_post( $attach_id );

        //post author or editor role
        if ( get_current_user_id() == $attachment->post_author || current_user_can( 'delete_private_pages' ) ) {
            wp_delete_attachment( $attach_id, true );
            echo 'success';
        }

        exit;
    }

    static function attach_file_to_post( $post_id ) {
        if ( isset( $_POST['wpuf_attach_id'] ) ) {
            foreach ($_POST['wpuf_attach_id'] as $index => $attach_id) {
                $postarr = array(
                    'ID' => $attach_id,
                    'post_title' => $_POST['wpuf_attach_title'][$index],
                    'post_parent' => $post_id,
                    'menu_order' => $index
                );

                wp_update_post( $postarr );
            }
        }
    }

}
