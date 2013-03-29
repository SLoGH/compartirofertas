<?php
defined('WP_ADMIN') || define('WP_ADMIN', true);
require_once('../../../../../../wp-load.php');
?>
<!doctype html>
<html lang="en">
	<head>
	<title>Insert Toggle</title>
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
		var toggle_type = jQuery('#toggle_type').val();	
		var toggle_title = jQuery('#toggle_title').val();	
		var toggle_active = '';	
		if (jQuery('#toggle_active').is(':checked')) {
			toggle_active = 'active';
		}
		shortcode = ' [toggle type="'+toggle_type+'" title="'+toggle_title+'" active="'+toggle_active+'"]'+selectedContent+'[/toggle] ';			
			
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
	<form name="toggle" action="#" >
		<div class="tabs">
			<ul>
				<li id="toggle_tab" class="current"><span><a href="javascript:mcTabs.displayTab('toggle_tab','toggle_panel');" onMouseDown="return false;">Toggle</a></span></li>
			</ul>
		</div>
		<div class="panel_wrapper">
			
				<fieldset style="margin-bottom:10px;padding:10px">
					<legend>Type of toggle:</legend>
					<label for="toggle_type">Choose a type:</label><br><br>
					<select name="toggle_type" id="toggle_type"  style="width:250px">
						<option value="" disabled selected>Select type</option>						
						<option value="white">White</option>
						<option value="gray">Gray</option>
					</select>	<br>
<br><br>

<label for="toggle_title">Type toggle title:</label><br><br>
                    <input type="text" name="toggle_title" id="toggle_title"   style="width:250px" />	
					
						<br>
<br><br>

<label for="toggle_active">Opened by default:</label><br><br>
                    <input type="checkbox" name="toggle_active" id="toggle_active" />	
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