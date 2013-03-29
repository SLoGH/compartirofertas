jQuery(document).ready(function($) {
	
	
	
	var stylechanger = ' \
	<div id="stylechanger"> \
		  <div class="shead"><h5>&quot;ChurcHope&quot;<br> \
				sample settings</h5></div> \
                                <a href="" class="stoggle sshow"></a> \
            <form action="" method="post"> \
			<a href="#" class="section_toggle">GENERAL</a> \
			<fieldset> \
            <label for="ch_gfont" class="select_label">Google Web Fonts for headings</label><select name="ch_gfont" id="gfont_select"><\select>\
            <label for="ch_textcolor">Text</label><input  type="color" name="ch_textcolor" data-hex="true" id="ch_textcolor" value="'+ ch_textcolor +'" > \
            <label for="ch_headingscolor">Headings</label><input  type="color" name="ch_headingscolor" data-hex="true" id="ch_headingscolor" value="'+ ch_headingscolor +'" > \
            <label for="ch_linkscolor">Links</label><input  type="color" name="ch_linkscolor" data-hex="true" id="ch_linkscolor" value="'+ ch_linkscolor +'" > \
			</fieldset> \
			<a href="#" class="section_toggle">HEADER</a> \
			<fieldset> \
            <label for="ch_headerbgcolor">Header background</label><input  type="color" name="ch_headerbgcolor" data-hex="true" id="ch_headerbgcolor" value="'+ ch_headerbgcolor +'" > \
            <label for="ch_headertextcolor">Header text</label><input  type="color" name="ch_headertextcolor" data-hex="true" id="ch_headertextcolor" value="'+ ch_headertextcolor +'" > \
            <label for="ch_menubgcolor">Color area background</label><input  type="color" name="ch_menubgcolor" data-hex="true" id="ch_menubgcolor" value="'+ ch_menubgcolor +'" > \
			<label for="ch_menutextcolor">Color area text</label><input  type="color" name="ch_menutextcolor" data-hex="true" id="ch_menutextcolor" value="'+ ch_menutextcolor +'" > \
            </fieldset>\
			<a href="#" class="section_toggle">FOOTER</a> \
			<fieldset> \
            <label for="ch_footerheadingscolor">Headings</label><input  type="color" name="ch_footerheadingscolor" data-hex="true" id="ch_footerheadingscolor" value="'+ ch_footerheadingscolor +'" > \
            <label for="ch_footerbgcolor">Background</label><input  type="color" name="ch_footerbgcolor" data-hex="true" id="ch_footerbgcolor" value="'+ ch_footerbgcolor +'" > \
            <label for="ch_footertextcolor">Text</label><input  type="color" name="ch_footertextcolor" data-hex="true" id="ch_footertextcolor" value="'+ ch_footertextcolor +'" > \
			<label for="ch_footerlinkscolor">Links</label><input  type="color" name="ch_footerlinkscolor" data-hex="true" id="ch_footerlinkscolor" value="'+ ch_footerlinkscolor +'" > \
            <label for="ch_footercopyrightcolor">Copyright text</label><input  type="color" name="ch_footercopyrightcolor" data-hex="true" id="ch_footercopyrightcolor" value="'+ ch_footercopyrightcolor +'" > \
			<label for="ch_footeractivemenucolor">Active menu item text</label><input  type="color" name="ch_footeractivemenucolor" data-hex="true" id="ch_footeractivemenucolor" value="'+ ch_footeractivemenucolor +'" > \
			</fieldset><div class="sfoot"><button type="submit" class="churchope_button"><span>Apply style</span></button></div> \n\
			<input type="hidden" name="use_session_values" value="1" /></form> \
            <form action="" method="post" id="sreset"><input type="hidden" name="reset_session_values" value="1" /><button type="submit">Reset</button></form> \
	</div> \
	';
	
	jQuery("body").append( stylechanger );
	
	if( typeof google_font_list != 'undefined' && google_font_list.length)
	{
		var fonts = google_font_list.split(',');
			jQuery.each(fonts, function(key, value) {
				jQuery('select#gfont_select')
				 .append(jQuery("<option></option>")
				 .attr("value",value)
				 .text(value));
		});
		
		if(typeof ch_gfont != 'undefined' && ch_gfont.length)
		{
			jQuery('select#gfont_select').val(ch_gfont);
		}
	}
	
	
    jQuery(".sshow").live('click',function(e){
    
    jQuery("#stylechanger").animate({ left: "0"} , 500);
    jQuery(this).removeClass("sshow");
    jQuery(this).addClass("shide");
 	return false;
    });
    
    jQuery(".shide").live('click',function(e){
    
    jQuery("#stylechanger").animate({ left: "-254"} , 500);
    jQuery(this).removeClass("shide");
    jQuery(this).addClass("sshow");
 	return false;
    });
	jQuery("a.section_toggle:first").toggleClass("active").next().slideToggle("normal");
	jQuery("a.section_toggle").live('click',function(e){    
	jQuery(this).toggleClass("active").next().slideToggle("normal");
	return false;    
    });


	
	
});   

//disable links

    function disableLink(e) {
        // cancels the event
        e.preventDefault();
    
        return false;
    }	