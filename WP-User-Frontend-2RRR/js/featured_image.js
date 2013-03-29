/**
 * Featured Image Javascript
 *
 * @author Tareq Hasan 
 * @package WP User Frontend
 * @version 1.1-fork-2RRR-4.4
 * @since 1.1-fork-2RRR-3.0  
 */
 
/*
== Changelog ==

= 1.1-fork-2RRR-4.4 professor99 =
* Added reset function
* WPUF_Featured_Image now global

= 1.1-fork-2RRR-3.0 professor99 =
* Compiled from functions in wpuf.js in previous version
* Created new javascript object WPUF_Featured_Image
* Updated along similar lines as attachment.js
* Bugfix: Fixed Flash/Silverlight button issues
* Made compatible with IE7+
*/
 
jQuery(document).ready(function($) {

	WPUF_Featured_Image = {

		init: function () {
			//initialize the featured image uploader
			this.featImgUploader();
		},
		featImgUploader: function() {
			var self = this;
			
			if(typeof plupload === 'undefined') {
				return;
			}

			if(wpuf_featured_image.featEnabled !== '1') {
				return;
			}

			this.uploader = new plupload.Uploader(wpuf_featured_image.plupload);
			$('.wpuf-post-form').on('click', 'a.wpuf-del-ft-image', this.removeFeatImg);

			this.uploader.bind('Init', function(up, params) {
				if ($('.wpuf-del-ft-image').length) {
					up.disableBrowse(true); //Disable Upload button
				}
			});

			$('#wpuf-ft-upload-pickfiles').click(function(e) {
				self.uploader.start();
				e.preventDefault();
			});

			this.uploader.init();

			this.uploader.bind('FilesAdded', function(up, files) {
				$.each(files, function(i, file) {
					$('#wpuf-ft-upload-filelist').append(
						'<div id="' + file.id + '">' +
						file.name + ' (' + plupload.formatSize(file.size) + ') <b></b>' +
						'</div>');
				});
				up.refresh(); // Reposition Flash/Silverlight
				up.disableBrowse(true); //Disable Upload button
				self.uploader.start();
			});

			this.uploader.bind('UploadProgress', function(up, file) {
				$('#' + file.id + " b").html(file.percent + "%");
			});

			this.uploader.bind('Error', function(up, err) {
				$('#wpuf-ft-upload-filelist').append("<div>Error: " + err.code +
					", Message: " + err.message +
					(err.file ? ", File: " + err.file.name : "") +
					"</div>"
					);
				up.disableBrowse(false); //Enable Upload button
				up.refresh(); // Reposition Flash/Silverlight
			});

			this.uploader.bind('FileUploaded', function(up, file, response) {
				var resp = $.parseJSON(response.response);
				//$('#' + file.id + " b").html("100%");
				$('#' + file.id).remove();
				//console.log(resp);
				if( resp.success ) {
					// Add thumbnail and removeImage button 
					$('#wpuf-ft-upload-filelist').append(resp.html);
					
					up.refresh(); // Reposition Flash/Silverlight
					
					// Make Upload button invisible but reserve space for Flash/Silverlight overlay. 
					// Unavoidable as Flash/Silverlight breaks if made invisible or overlayed.
					// Image floated to minimise space from Flash/Silverlight overlay
					// Bug: Flash/Silverlight leave Update cursor as hand even though disabled.
					$('#wpuf-ft-upload-pickfiles').css('visibility','hidden');
				}
				else {
					up.refresh(); // Reposition Flash/Silverlight
					up.disableBrowse(false); //Enable Upload button
				}
			});
		},
		removeFeatImg: function(e) {
			e.preventDefault();

			if( confirm( wpuf.confirmMsg ) ) {
				var el = $(this),
					data = {
						'attach_id' : el.data('id'),
						'nonce' : wpuf_featured_image.nonce,
						'action' : 'wpuf_feat_img_del'
					}

				$.post( wpuf.ajaxurl, data, WPUF_Featured_Image.reset );
			}
		},
		reset: function () {
            //done on form reset or removeFeatImg
            $('#wpuf-ft-upload-filelist div').remove();
			$('#wpuf-ft-upload-pickfiles').css('visibility','visible');
			WPUF_Featured_Image.uploader.refresh();  // Reposition Flash/Silverlight
			WPUF_Featured_Image.uploader.disableBrowse( false ); //Enable Upload button
		},
	};	

	//run the bootstrap
	WPUF_Featured_Image.init();

});
