<?php
defined('WP_ADMIN') || define('WP_ADMIN', true);
require_once('../../../../../../wp-load.php');
?>
<!doctype html>
<html lang="en">
	<head>
	<title>Insert Tabs</title>
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
		if (!selectedContent) {selectedContent = "Tab 1 content goes here.";}			
		var tabs_type = jQuery('#tabs_type').val();	
			
		shortcode = '[tabgroup type="'+tabs_type+'"] <br>'; 
		shortcode += '[tab title="Tab 1"]'+selectedContent+'[/tab] <br>';
		shortcode += '[tab title="Tab 2"]Tab 2 content goes here.[/tab] <br>';
		shortcode += '[tab title="Tab 3"]Tab 3 content goes here.[/tab] <br>';
		shortcode += '[/tabgroup]';	
		
			
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
	<form name="tabs" action="#" >
		<div class="tabs">
			<ul>
				<li id="tabs_tab" class="current"><span><a href="javascript:mcTabs.displayTab('tabs_tab','tabs_panel');" onMouseDown="return false;">Tabs</a></span></li>
			</ul>
		</div>
		<div class="panel_wrapper">
			
				<fieldset style="margin-bottom:10px;padding:10px">
					<legend>Type of tabs:</legend>
					<label for="tabs_type">Choose a type:</label><br><br>
					<select name="tabs_type" id="tabs_type"  style="width:250px">
						<option value="" disabled selected>Select type</option>
						<option value="white">White</option>
						<option value="gray">Gray</option>
						
					</select>		
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