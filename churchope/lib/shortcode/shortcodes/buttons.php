<?php	
require_once('../../../../../../wp-load.php');
if( get_option(SHORTNAME."_linkscolor")) { $customcolor = get_option(SHORTNAME."_linkscolor"); } else {$customcolor = "#c62b02"; }
?>
<!doctype html>
<html lang="en">
	<head>
	<title>Insert Button</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<script language="javascript" type="text/javascript" src="<?php echo includes_url();?>/js/tinymce/tiny_mce_popup.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo includes_url();?>/js/tinymce/utils/mctabs.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo includes_url();?>/js/tinymce/utils/form_utils.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo includes_url();?>/js/jquery/jquery.js?ver=1.4.2"></script>
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
		shortcode = ' [button type="'+button_type+'" url="'+button_url+'" target="'+button_target+'" ]'+selectedContent+'[/button] ';			
			
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
        jQuery("#preview").html(type ? "<a class='"+type+"' style='cursor:pointer'><span>Test button</span></a>"  : "");
    });	
	});
	
	</script>

	<style type="text/css">
a {transition: color, background 200ms ease-in-out;
											  -webkit-transition: color, background 200ms ease-in-out;
											  -moz-transition: color, background 200ms ease-in-out;
											  -o-transition: color, background 200ms ease-in-out;}
a;hover {transition: color, background 200ms ease-in-out;
											  -webkit-transition: color, background 200ms ease-in-out;
											  -moz-transition: color, background 200ms ease-in-out;
											  -o-transition: color, background 200ms ease-in-out;}
.simple_button_link {background:<?php echo $customcolor ?>;padding: 3px 9px 4px;display: inline-block;color:#fff;text-decoration: none;-webkit-border-radius: 3px; -moz-border-radius: 3px; border-radius: 3px;margin-right: 7px;}
.simple_button_link:hover {background:#281e1f;color:#fff}
.simple_button_black {background:#281e1f;padding: 3px 9px 4px;display: inline-block;color:#fff;text-decoration: none;-webkit-border-radius: 3px; -moz-border-radius: 3px; border-radius: 3px;margin-right: 7px; }
.simple_button_black:hover {background:<?php echo $customcolor ?>;color:#fff}
.churchope_button {background:<?php echo $customcolor ?> url(../../../images/bg_button.png) repeat-x 0 0; -webkit-border-radius: 3px; -moz-border-radius: 3px; border-radius: 3px;text-decoration: none;font-size:12px;line-height: 15px;padding: 15px 23px 14px;display: inline-block;color:#fff;border:none; }
.churchope_button:hover{padding-bottom: 12px;margin-top: 2px;color:#fff;}
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
						<option value="simple_button_link">Simple button</option>
						<option value="simple_button_black">Simple button black</option>
						<option value="churchope_button">Big color button</option>                        
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