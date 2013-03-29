/**
 * Category Javascript 
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
* Fixed ajax category.
* Added wpuf prefix for css
*/

jQuery(document).ready(function($) {

	WPUF_Cat = {
		init: function () {
			this.ajaxCategory();
		},
		ajaxCategory: function () {
			var el = '#wpuf-cat-ajax';
			var wrap = '#wpuf-category';

			$(wrap).on('change', el, function(){
				currentLevel = parseInt( $(this).parent().attr('level') );
				WPUF_Cat.getChildCats( $(this), 'wpuf-cat-lvl', currentLevel+1, wrap, 'category');
			});
		},
		getChildCats: function (dropdown, result_div, level, wrap_div, taxonomy) {
			cat = $(dropdown).val();
			results_div = result_div + level;
			taxonomy = typeof taxonomy !== 'undefined' ? taxonomy : 'category';

			$(dropdown).parent().nextAll().each(function(){
				$(this).remove();
			});

			$(dropdown).parent().removeClass('wpuf-cat-has-child');

			if ( cat > 0 ) {
				$.ajax({
					type: 'post',
					url: wpuf.ajaxurl,
					data: {
						action: 'wpuf_get_child_cats',
						catID: cat,
						nonce: wpuf.nonce
					},
					beforeSend: function() {
						$(dropdown).parent().parent().next('.loading').addClass('wpuf-loading');
					},
					complete: function() {
						$(dropdown).parent().parent().next('.loading').removeClass('wpuf-loading');
					},
					success: function(html) {
						if(html != "") {
							$(dropdown).parent().addClass('wpuf-cat-has-child').parent().append('<div id="'+result_div+level+'" level="'+level+'"></div>');
							dropdown.parent().parent().find('#'+results_div).html(html).slideDown('fast');
						}
					}
				});
			}
		},

	};

	//run the bootstrap
	WPUF_Cat.init();

});

