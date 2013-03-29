/**
 * WP User Frontend Attachment Javascript
 *
 * @author Tareq Hasan
 * @package WP User Frontend
 * @version 1.1-fork-2RRR-4.4 
 */
 
/*
== Changelog ==

= 1.1-fork-2RRR-4.4 professor99 =
* Added reset function
* WPUF_Attachment now global

= 1.1-fork-2RRR-3.0 professor99 =
* Bugfix: Fixed Flash/Silverlight button issues
* hideUploadBtn renamed hideShowUploadBtn and now shows button as well

= 1.1-fork-2RRR-2.0 professor99 =
* Updated delete for attachment list
*/

jQuery(document).ready(function($) {

    WPUF_Attachment = {
        init: function () {
            window.wpufFileCount = typeof window.wpufFileCount == 'undefined' ? 0 : window.wpufFileCount;
            this.maxFiles = parseInt(wpuf_attachment.number);

            this.attachUploader();
            this.hideShowUploadBtn();
        },
        hideShowUploadBtn: function (up) {
            if(WPUF_Attachment.maxFiles !== 0 && window.wpufFileCount >= WPUF_Attachment.maxFiles) {
                //Hide button
			
                $('#wpuf-attachment-upload-pickfiles').css('visibility','hidden');
				
                if(typeof up !== 'undefined') {
                    up.disableBrowse(true); //Disable Attachment button
                }
            }
            else {
                // Show button
				
                $('#wpuf-attachment-upload-pickfiles').css('visibility','visible');
				
                if(typeof up !== 'undefined') {
                    up.disableBrowse(false); //Enable Attachment button
                }
            }
        },
        attachUploader: function() {
            var self = this;

            if(typeof plupload === 'undefined') {
                return;
            }

            if(wpuf_attachment.attachment_enabled !== '1') {
                return
            }

            this.attachUploader = new plupload.Uploader(wpuf_attachment.plupload);

            $('#wpuf-attachment-upload-filelist').on('click', 'a.track-delete', this.attachUploader, this.removeTrack);

            $('#wpuf-attachment-upload-filelist ul.wpuf-attachment-list').sortable({
                cursor: 'crosshair',
                handle: '.handle'
            });

            $('#wpuf-attachment-upload-pickfiles').click(function(e) {
                self.attachUploader.start();
                e.preventDefault();
            });

            this.attachUploader.init();

            this.attachUploader.bind('FilesAdded', function(up, files) {
                $.each(files, function(i, file) {
                    $('#wpuf-attachment-upload-filelist').append(
                        '<div id="' + file.id + '">' +
                        file.name + ' (' + plupload.formatSize(file.size) + ') <b></b>' +
                        '</div>');
                });

                up.refresh(); // Reposition Flash/Silverlight
                    up.disableBrowse(true); //Disable Attachment button
                self.attachUploader.start();
            });

            this.attachUploader.bind('UploadProgress', function(up, file) {
                $('#' + file.id + " b").html(file.percent + "%");
            });

            this.attachUploader.bind('Error', function(up, err) {
                $('#wpuf-attachment-upload-filelist').append("<div>Error: " + err.code +
                    ", Message: " + err.message +
                    (err.file ? ", File: " + err.file.name : "") +
                    "</div>"
                );

                up.refresh(); // Reposition Flash/Silverlight
                WPUF_Attachment.hideShowUploadBtn(up);
            });

            this.attachUploader.bind('FileUploaded', function(up, file, response) {
                var resp = $.parseJSON(response.response);
                $('#' + file.id).remove();

                if( resp.success ) {
                    window.wpufFileCount += 1;
                    $('#wpuf-attachment-upload-filelist ul').append(resp.html);
                }
				
                up.refresh(); // Reposition Flash/Silverlight
                WPUF_Attachment.hideShowUploadBtn(up);
            });
        },
        removeTrack: function(e) {
            e.preventDefault();

            if(confirm(wpuf.confirmMsg)) {
                var el = $(this),
                data = {
                    'attach_id' : el.data('attach_id'),
                    'nonce' : wpuf_attachment.nonce,
                    'action' : 'wpuf_attach_del'
                };

                $.post(wpuf.ajaxurl, data, function(){
                    el.parent().remove();
                    window.wpufFileCount -= 1;
                    WPUF_Attachment.attachUploader.refresh();  // Reposition Flash/Silverlight
                    WPUF_Attachment.hideShowUploadBtn( WPUF_Attachment.attachUploader );
                });
            }
        },
        reset: function() {
            //done on form reset
            $('.wpuf-attachment-list li').remove();
            window.wpufFileCount = 0;
            WPUF_Attachment.attachUploader.refresh();  // Reposition Flash/Silverlight
            WPUF_Attachment.hideShowUploadBtn( WPUF_Attachment.attachUploader );
        }

    };

    //run the bootstrap
    WPUF_Attachment.init();
});
