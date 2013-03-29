jQuery.noConflict();

function file_rm_ajax() {
	jQuery("input[name^='file_rm']").bind("click",function(){
		
		var $fileId = jQuery(this).attr("id");
//		var $fileName = jQuery("#"+$fileId).val();
		jQuery("#"+$fileId).remove();
		
		
			
		jQuery.ajax({
			type: "post",
			url: $AjaxUrl,
			data: { 
				action: "file_rm",
				file_id: $fileId,
				_ajax_nonce: $ajaxNonce },
			beforeSend: function() {jQuery("."+$fileId).css({display:""});}, //fadeIn loading just when link is clicked
			success: function(){ //so, if data is retrieved, store it in html
				jQuery("#file_deleted_"+$fileId).fadeOut("fast"); //animation
				jQuery("#image_"+$fileId).fadeOut("fast");
				jQuery("#th_img_frame_"+$fileId).css({display:""});
			}
		}); //close jQuery.ajax
		return false;
	});
}


function install_dummy() {
	jQuery("input[name^='install_dummy']").bind("click",function(){
	
	jQuery.ajax({
		
			type: "post",
			url: $AjaxUrl,
			dataType: 'json',
			data: {action: "install_dummy",   _ajax_nonce: $ajaxNonce},
			beforeSend: function() {
				jQuery(".install_dummy_result").html('');
				jQuery(".install_dummy_loading").css({display:""});
				jQuery("input[name^='install_dummy']").attr('disabled', 'disabled');
				jQuery(".install_dummy_result").html("Importing dummy content...<br /> Please wait, it can take up to a few minutes.");					
			
			}, //fadeIn loading just when link is clicked
			success: function(response){ //so, if data is retrieved, store it in html
				var dummy_result = jQuery(".install_dummy_result");
				jQuery("input[name^='install_dummy']").remove();
				if(typeof response != 'undefined')
				{
					if(response.hasOwnProperty('status'))
					{
						switch(response.status)
						{
							case 'success':
									dummy_result.html('Completed');
								break;
							case 'error':
									dummy_result.html('<font color="red">'+response.data+'</font>');
								break;
							default:
								break;
						}
						
					}
				}
				jQuery(".install_dummy_loading").css({display:"none"});
			}
		}); //close jQuery.ajax
	
	return false;
	});
}



jQuery(document).ready(function(){
jQuery(":checkbox").iButton();

jQuery('.th_help[title], .th_img[title]').qtip({
	content: {
		text: false
	},
	style: {
		tip: "bottomLeft",
		classes: "ui-tooltip-dark"
	},
	position: {
		at: "top right",
		my: "bottom left"
	}
}
);
   

function sidebar_rm_ajax() {
	jQuery("input[name^='sidebar_rm']").bind("click",function(){
		
		var $sidebarId = jQuery(this).attr("id");
		var $sidebarName = jQuery("#sidebar_generator_"+$sidebarId).val();
		jQuery("#sidebar_generator_"+$sidebarId).remove();
		
		var $arraySidebarInputs = new Array;
		jQuery("input[name^='sidebar_generator_']").each(function(id) {
                     $updateSidebars = jQuery("input[name^='sidebar_generator_']").get(id);
                     $arraySidebarInputs.push($updateSidebars.value);
                    });
		
		var $sidebarInputsStr = $arraySidebarInputs.join(",");
			
		jQuery.ajax({
			type: "post",
			url: $AjaxUrl,
			data: {
				action: "sidebar_rm",
				sidebar: $sidebarInputsStr,
				sidebar_id: $sidebarId,
				sidebar_name: $sidebarName,
				_ajax_nonce: $ajaxNonce
			},
			beforeSend: function() {
				jQuery(".sidebar_rm_"+$sidebarId).css({display:""}); //fadeIn loading just when link is clicked
			}, 
			success: function(html){ //so, if data is retrieved, store it in html
				jQuery("#sidebar_cell_"+$sidebarId).fadeOut("fast"); //animation
			}
		}); //close jQuery.ajax
		return false;
	});
}


//Google fonts preview
function changegfont() {
  var str = "";
    jQuery("[id$=_gfont] option:selected").each(function() {
        str += jQuery(this).text() + "";
    });
	
	if(str && str.length)
	{
		var link = ("<link rel='stylesheet' type='text/css' href='//fonts.googleapis.com/css?family=" + str + "' media='screen' />");
		jQuery("head").append(link);
		jQuery(".gfont_preview").css("font-family", str);
	}

}
jQuery("[id$=_gfont]").closest("div").before('<div class="gfont_preview">The quick brown fox jumps over the lazy dog</div>');
changegfont();
jQuery("[id$=_gfont]").change(function() {
  changegfont();
});


jQuery("[id$=_gfont]").keyup(function() {
    changegfont();
});


jQuery("[id$=_gfont]").keydown(function() {
    changegfont();
});

/*
 * Toggling group of elements.
 */
jQuery('label.toggle').click(function(){
	var ul = jQuery(this).closest('li').find('ul:first');
	if(ul && ul.length>0)
	{
		jQuery(this).toggleClass('down');
		ul.toggle('slow');
	}
});

sidebar_rm_ajax();
file_rm_ajax();
install_dummy();
});