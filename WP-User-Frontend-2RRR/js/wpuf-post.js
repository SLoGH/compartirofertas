/**
 * Post Javascript 
 *
 * @author Andrew Bruin (professor99) 
 * @package WP User Frontend
 * @version 1.1.0-fork-2RRR-4.4
 * @since 1.1-fork-2RRR-4.4 
 */

/*
== Changelog ==

= 1.1.0-fork-2RRR-4.4 professor99 =
* Compiled from functions in wpuf.js
* checksubmit() renamed wpuf_post_check_submit()
* checksubmit now called by ajaxForm beforesubmit in wpuf-add-post.js and wpuf-edit-post.js
*/

function wpuf_post_check_submit( form ) { 
	//Save tinymce iframe to textarea
	if ( typeof( tinyMCE ) != "undefined" ) {
		tinyMCE.triggerSave();
	}

	jQuery('#wpuf-info-msg').html( '&nbsp;' );

	jQuery('*',form).each( function() {
		if( jQuery(this).hasClass( 'wpuf-invalid' ) ) {
			jQuery(this).removeClass( 'wpuf-invalid' );
		}
	});

	var hasError = false;

	form.find('.requiredField').each( function() {
		var el = jQuery(this);

		if( jQuery.trim( el.val() ) == '' ) {
			//Highlights closest visible container.
			//Still slight bug in tinyMCE editor when submitted when display tab is "HTML"
			//In this case the "Visible" tab won't be highlighted but this is very insignificant.
			el.closest(':visible').addClass( 'wpuf-invalid' );
			hasError = true;
		} else if( el.hasClass( 'email' ) ) {
			var emailReg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
			if( !emailReg.test( jQuery.trim( el.val() ) ) ) {
				el.closest(':visible').addClass( 'wpuf-invalid' );
				hasError = true;
			}
		} else if( el.hasClass( 'cat' ) ) {
			if( el.val() == '-1' ) {
				el.closest(':visible').addClass( 'wpuf-invalid' );
				hasError = true;
			}
		}
	});

	if( ! hasError ) {
		return true;
	}

	jQuery('#wpuf-info-msg').html( '<div class="wpuf-error">Required field(s) empty.</div>' );
	jQuery('#wpuf-info-msg').fadeTo( 0,1 );

	return false;
}


