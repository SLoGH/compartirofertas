<?php
defined('WP_ADMIN') || define('WP_ADMIN', true);
require_once('../../../../../../wp-load.php');
?>
<!doctype html>
<html lang="en">
	<head>
	<title>Insert Video</title>
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
		var shortcode = '[thvideo';
		var url = jQuery('#url').val();
		var w = jQuery('#width').val();
		var h = jQuery('#height').val();
		var wu = jQuery('#width_units').val();
		var hu = jQuery('#height_units').val();
		
		if(w.length)
		{
			shortcode += ' w="'+w+wu+'"';
		}
		if(h.length)
		{
			shortcode += ' h="'+h+hu+'"';
		}
		shortcode += ']';
		if(url.length)
		{
			shortcode += url;
		}
		
		shortcode += '[/thvideo]';
			
		if(window.tinyMCE) {
			window.tinyMCE.execInstanceCommand('content', 'mceInsertContent', false, shortcode);
			tinyMCEPopup.editor.execCommand('mceRepaint');
			tinyMCEPopup.close();
		}
		return;
	}
	
	
	</script>

	<base target="_self" />
	</head>
	<body  onload="init();">
	<form name="video" action="#" >
		<div class="tabs">
			<ul>
				<li id="video_tab" class="current"><span><a href="javascript:mcTabs.displayTab('video_tab','video_panel');" onMouseDown="return false;">Video</a></span></li>
			</ul>
		</div>
		<div class="panel_wrapper">
				<fieldset style="margin-bottom:10px;padding:10px">
					<legend>Video URL:</legend>
					<label for="url">YouTube or Vimeo URL:</label><br><br>
					<input type="text" name="url" id="url" style="width:250px" />
				</fieldset>

				<fieldset style="margin-bottom:10px;padding:10px">
					<legend>Size:</legend>
					<label for="width">Width:</label>
					&nbsp;<input type="text" name="width" id="width"   style="width:60px"/> <select id="width_units"><option>px</option><option value="%">%</option></select>
					<br><br>
					<label for="height">Height:</label>
					<input type="text" name="height" id="height"   style="width:60px" /> <select id="height_units"><option>px</option><option value="%">%</option></select>
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