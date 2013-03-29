<?php
defined('WP_ADMIN') || define('WP_ADMIN', true);
require_once('../../../../../../wp-load.php');
if( get_option(SHORTNAME."_linkscolor")) { $customcolor = get_option(SHORTNAME."_linkscolor"); } else {$customcolor = "#c62b02"; }
?>
<!doctype html>
<html lang="en">
	<head>
	<title>Insert Social Link</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<script language="javascript" type="text/javascript" src="<?php echo includes_url();?>/js/tinymce/tiny_mce_popup.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo includes_url(); ?>/js/tinymce/utils/mctabs.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo includes_url();?>/js/tinymce/utils/form_utils.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo includes_url(); ?>/js/jquery/jquery.js?ver=1.4.2"></script>
	<script language="javascript" type="text/javascript">
	function init() {
		
		tinyMCEPopup.resizeToInnerSize();
	}
	function submitData() {				
		var shortcode;
		var selectedContent = tinyMCE.activeEditor.selection.getContent();				
		var button_type = jQuery('#button_type').val();		
		var button_url = jQuery('#button_url').val();	
		if (jQuery('#button_target').is(':checked')) {
		var button_target = jQuery('#button_target:checked').val();} else {var button_target = '';}			
		shortcode = ' [social_link type="'+button_type+'" url="'+button_url+'" target="'+button_target+'" ]';			
			
		if(window.tinyMCE) {
			window.tinyMCE.execInstanceCommand('content', 'mceInsertContent', false, shortcode);
			tinyMCEPopup.editor.execCommand('mceRepaint');
			tinyMCEPopup.close();
		}
		
		return;
	}
	
	jQuery(document).ready(function() {
    jQuery("#button_type").change(function() {
        var type = jQuery(this).val();
        jQuery("#preview").html(type ? "<a class='social_links "+type+"' style='cursor:pointer'> </a>"  : "");
    });	
	});
	
	</script>
		<?php
		/**
		 * @todo add  correct classes
		 */
		?>
	<style type="text/css">
.social_links { text-indent: -9999px; display: inline-block; width: 40px; height: 40px; background: #f0f0f0 url('<?php echo  get_template_directory_uri()?>/images/sprite_socialbuttons.png') no-repeat 0 0; -webkit-border-radius: 20px; -moz-border-radius: 20px; border-radius: 20px; margin: 0 6px 6px 0; transition: background 200ms ease-in-out; -webkit-transition: background 200ms ease-in-out; -moz-transition: background 200ms ease-in-out; -o-transition: background 200ms ease-in-out; }
.social_links.rss_feed { background-position:0 0 }
.social_links.rss_feed:hover { background-position:100% 0 }
.social_links.facebook_account { background-position:0 -160px }
.social_links.facebook_account:hover { background-position:100% -160px }
.social_links.twitter { background-position:0 -200px }
.social_links.twitter:hover { background-position:100% -200px }
.social_links.dribble_account { background-position:0 -120px }
.social_links.dribble_account:hover { background-position:100% -120px }
.social_links.email_to { background-position:0 -240px }
.social_links.email_to:hover { background-position:100% -240px }
.social_links.google_plus_account { background-position:0 -280px }
.social_links.google_plus_account:hover { background-position:100% -280px }
.social_links.flicker_account { background-position:0 -40px }
.social_links.flicker_account:hover { background-position:100% -40px }
.social_links.vimeo_account { background-position:0 -80px }
.social_links.vimeo_account:hover { background-position:100% -80px }
.social_links.linkedin_account { background-position:0 -320px }
.social_links.linkedin_account:hover { background-position:100% -320px }
.social_links.youtube_account { background-position:0 -360px }
.social_links.youtube_account:hover { background-position:100% -360px }
.social_links.pinterest_account { background-position:0 -400px }
.social_links.pinterest_account:hover { background-position:100% -400px }
.social_links:hover {background-color: <?php echo $customcolor ?>; transition: background 200ms ease-in-out; -webkit-transition: background 200ms ease-in-out; -moz-transition: background 200ms ease-in-out; -o-transition: background 200ms ease-in-out; }	
	</style>
	<base target="_self" />
	</head>
	<body  onload="init();">
	<form name="buttons" action="#" >
		<div class="tabs">
			<ul>
				<li id="buttons_tab" class="current"><span><a href="javascript:mcTabs.displayTab('buttons_tab','buttons_panel');" onMouseDown="return false;">Buttons</a></span></li>
			</ul>
		</div>
		<div class="panel_wrapper">
				<fieldset style="margin-bottom:10px;padding:10px">
					<legend>Type of button:</legend>
					<label for="button_type">Choose a type:</label><br><br>
					<select name="button_type" id="button_type"  style="width:250px">
						<option value="" disabled selected>Select type</option>
						<option value="rss_feed">RSS</option>
						<option value="facebook_account">Facebook</option>
						<option value="twitter">Twitter</option>
                        <option value="dribble_account">Dribbble</option>
						<option value="email_to">Email to</option>
						<option value="google_plus_account">Google+</option>
                        <option value="flicker_account">Flickr</option>
                        <option value="vimeo_account">Vimeo</option>
						<option value="linkedin_account">LinkedIn</option>
						<option value="youtube_account">Youtube</option>
						<option value="pinterest_account">Pinterest</option>
					</select>					
				</fieldset>
			
				<fieldset style="margin-bottom:10px;padding:10px">
				<legend>URL for button:</legend>
					<label for="button_url">Type your URL here:</label><br><br>
					<input name="button_url" type="text" id="button_url" style="width:250px">
				</fieldset>
				<fieldset style="margin-bottom:10px;padding:10px">
				<legend>Link target:</legend>
					<label for="button_target">Check if you want open in new window:</label><br><br>
					<input name="button_target" type="checkbox" id="button_target">
				</fieldset>
				<fieldset style="margin-bottom:10px;padding:10px">
					<legend>Preview:</legend>
					<div id="preview" style="height:70px"></div>
				</fieldset>
			
		</div>
		<div class="mceActionPanel">
			<div style="float: right">
				<input type="submit" id="insert" name="insert" value="Insert" onClick="submitData();" />
			</div>
		</div>
	</form>
</body>
</html>