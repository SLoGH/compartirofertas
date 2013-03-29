<?php

/**
 * Downgrade handler
 *
 * @author Andy Bruin 
 * @package WP User Frontend
 * @version 1.1-fork-2RRR-4.0
 * @since 1.1-fork-2RRR-4.0  
 */
 

function wpuf_downgrade() {
    if (isset($_POST['ok'])) {
        // Downgrade button pressed.
        // Do downgrade.
        wpuf_downgrade_ok();
    } else {
        // Downgrade menu item selected. 
        // Display downgrade confirm form.
        wpuf_downgrade_confirm();
    }
}

function wpuf_downgrade_confirm() {
?>

    <div class="wrap">
        <div class="icon32" id="icon-options-general"><br></div>
        <h2><?php _e( 'WP User Frontend', 'wpuf' ) ?>: <?php _e( 'Downgrade', 'wpuf' ) ?></h2>
        <br/>
        <br/>
        <form action="" method="post">
            <input name="ok" type="submit" class="button-primary" value="<?php _e( 'Downgrade to version 1.1', 'wpuf' ) ?>" />
        </form>
    </div>
<?php
}

/**
 * Downgrade to version 1.1.
 *
 * Set values to compatible Version 1.1 values.
 * If editor type not valid 1.1 type it will be set to 'full'.
 *
 * @since 1.1-fork-2RRR-4.0
 */
function wpuf_downgrade_ok() {
    $wpuf_frontend_posting = get_option('wpuf_frontend_posting');
    $wpuf_others = get_option('wpuf_others');
    
    //Reset editor type to 'full' if not valid 1.1 editor type
    
    $editor_type = $wpuf_frontend_posting['editor_type'];
    
    switch ($editor_type) {
        case 'full':
        case 'tiny':
        case 'plain':
            break;
        default:
            $wpuf_frontend_posting['editor_type'] = 'full';	
    }
        
    //If post status 'default' set it to 'publish'
    
    $post_status = $wpuf_frontend_posting['post_status'];
    
    if ( $post_status == 'default' )
        $wpuf_frontend_posting['post_status'] = 'publish';
    
    //Update wpuf_frontend_posting options
    update_option( 'wpuf_frontend_posting' , $wpuf_frontend_posting );

    //If enable_post_edit 'default' set it to 'yes'
    
    $enable_post_edit = $wpuf_others['enable_post_edit'];

    if ( $enable_post_edit == 'default' )
        $wpuf_others['enable_post_edit'] = 'yes';
    
    //If enable_post_del 'default' set it to 'yes'
    
    $enable_post_del = $wpuf_others['enable_post_del'];
    
    if ( $enable_post_del == 'default' )
        $wpuf_others['enable_post_del'] = 'yes';
            
    //Update wpuf_others options		
    update_option( 'wpuf_others' , $wpuf_others );
?>

    <div class="wrap">
        <div class="icon32" id="icon-options-general"><br></div>
        <h2><?php _e( 'WP User Frontend', 'wpuf' ) ?>: <?php _e( 'Downgrade', 'wpuf' ) ?></h2>
        <br/>
        <br/>
        <strong style="color:red; font-size:16px;">Restore Version 1.1 files NOW!</strong>
        
    </div>
<?php
    die();
}