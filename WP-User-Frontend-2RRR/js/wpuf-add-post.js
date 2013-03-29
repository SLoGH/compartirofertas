/**
 * Add Post Javascript
 *
 * @author Andrew Bruin (professor99) 
 * @package WP User Frontend
 * @version 1.1.0-fork-2RRR-4.4
 * @since 1.1-fork-2RRR-3.0  
 */
 
/*
== Changelog ==

= 1.1.0-fork-2RRR-4.4 professor99 =
* Reset FeaturedImage and Attachments on form reset
* Updated error handling.
* Added updating message (was in wpuf.js)
* wpuf_add_post_before_submit now validates using checkSubmit
* Updated function structure

= 1.1.0-fork-2RRR-4.2 professor99 =
* Fixed Jquery $ conflict bug

= 1.1-fork-2RRR-3.0 professor99 =
* Was wpuf_add_post_javascript() in wpuf_add_post.php
* Escaped info message XML tags
*/

//Submit Post button
//-------------------

//Uses jquery.form

//Note if a file upload is included this uses an iframe instead of Ajax for the current Wordpress 3.4.2 version
//which uses jquery.form version 2.73. Versions of jquery.form 2.90 and later can use html5 ajax to do this.
//For iframe uploads timeout and error functions wont fire.

jQuery( document ).ready( function() {
	var options = { 
		datatype:	'xml',
		beforeSubmit: wpuf_add_post_before_submit,
		success:	wpuf_add_post_success,
		error:		wpuf_add_post_error,
		timeout:	3000, 
		url:		wpuf.ajaxurl,
		data:		{ action: 'wpuf_add_post_action' }
	}

	// bind form using 'ajaxForm' 
	jQuery('#wpuf_new_post_form').ajaxForm( options );
	
	// On form reset do WPUF_Featured_Image & WPUF_Attachment reset
	jQuery('#wpuf_new_post_form').on( "reset", function() {
		WPUF_Featured_Image.reset();
		WPUF_Attachment.reset();
	});
});

function wpuf_add_post_before_submit( formData, jqForm, options ) { 
	if ( wpuf_post_check_submit( jqForm ) ) {
		wpuf_add_post_updating();
		return true;
	}
	else
		return false;
}

function wpuf_add_post_success( responseXML ) { 
	success = jQuery('success', responseXML).text();
	message = jQuery('message', responseXML).text();
	post_id = jQuery('post_id', responseXML).text();
	redirect_url = jQuery('redirect_url', responseXML).text();
	//alert('success=' + success + '\nmessage=' + message + '\npost_id=' + post_id + '\nredirect_url=' + redirect_url);
	
	if ( success == "true" ) {
		wpuf_add_post_show_message( message, true );
		
		if ( redirect_url != "" ) {
			setTimeout( function() { window.location.replace( redirect_url ), 3000 } );
		} else {
			//Reset form		
			jQuery('#wpuf_new_post_form').resetForm();
			
			//Enable buttons
			wpuf_add_post_enable();	
		}
	}
	else if ( success == "false" ) {
		wpuf_add_post_show_message( message, true );
		wpuf_add_post_enable();
	}
	else {
		//submit_post() crashed.
		//Use alert as message can be more than one line.
		alert( 'Submit Error: ' + responseXML );
		wpuf_add_post_enable();
	}
}

function wpuf_add_post_updating() {
	//Display wait message
	jQuery('#wpuf_new_post_form .wpuf-submit').attr( {
		'value': wpuf.updating_msg,
	} );

	//Clear info line
	jQuery('#wpuf-info-msg').html( '&nbsp;' );

	//Disable buttons
	wpuf_add_post_disable();
}	

function wpuf_add_post_disable() {
	//Disable submit button
	jQuery('#wpuf_new_post_form .wpuf-submit').attr( {
		'disabled': true
	} );
}

function wpuf_add_post_enable() {
	//Enable submit button
	jQuery('#wpuf_new_post_form .wpuf-submit').attr( {
		'value': wpuf.submit_msg,
		'disabled': false
	});
}

function wpuf_add_post_show_message( message, fade ) {
	jQuery('#wpuf-info-msg').html( message );
	jQuery('#wpuf-info-msg').fadeTo( 0,1 );
	
	if ( fade )
		jQuery('#wpuf-info-msg').fadeTo( 4000,0 );
}

function wpuf_add_post_error( XMLHttpRequest, textStatus, errorThrown ) {
	//Triggered on ajax errors including timeout.
	//Use alert as message can be more than one line.
	alert( "AjaxForm Error\nStatus: " + textStatus + "\nError: " + errorThrown + "\nResponse: " + jQuery(XMLHttpRequest.responseXML).text() );
	wpuf_add_post_enable();
}
