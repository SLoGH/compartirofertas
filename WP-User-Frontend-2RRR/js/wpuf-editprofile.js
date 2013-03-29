/**
 * Edit Profile Javascript
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
*/
 
jQuery(document).ready(function($) {

	WPUF_EditProfile = {
		init: function () {
			//editprofile password strength
			$('#pass1').val('').keyup( this.passStrength );
			$('#pass2').val('').keyup( this.passStrength );
			$('#pass-strength-result').show();
		},
		passStrength: function () {
			var pass1 = $('#pass1').val(), user = $('#user_login1').val(), pass2 = $('#pass2').val(), strength;

			$('#pass-strength-result').removeClass('short bad good strong');
			if ( ! pass1 ) {
				$('#pass-strength-result').html( pwsL10n.empty );
				return;
			}

			strength = passwordStrength(pass1, user, pass2);

			switch ( strength ) {
				case 2:
					$('#pass-strength-result').addClass('bad').html( pwsL10n['bad'] );
					break;
				case 3:
					$('#pass-strength-result').addClass('good').html( pwsL10n['good'] );
					break;
				case 4:
					$('#pass-strength-result').addClass('strong').html( pwsL10n['strong'] );
					break;
				case 5:
					$('#pass-strength-result').addClass('short').html( pwsL10n['mismatch'] );
					break;
				default:
					$('#pass-strength-result').addClass('short').html( pwsL10n['short'] );
			}
		}
	};

	//run the bootstrap
	WPUF_EditProfile.init();

});
