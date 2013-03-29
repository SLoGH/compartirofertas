<?php
defined('WP_ADMIN') || define('WP_ADMIN', true);
require_once('../../../../../../wp-load.php');
?>
<!doctype html>
<html lang="en">
	<head>
	<title>Insert Table of Contents</title>
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
		var shortcode = '[toc';
		var title = jQuery('#title').val();
		var hide = jQuery('#hide').val();
		var show = jQuery('#show').val();
	
		
		if(title.length)
		{
			shortcode += ' title="'+title+'"';
		}
		if(hide.length)
		{
			shortcode += ' hide="'+hide+'"';
		}
		if(show.length)
		{
			shortcode += ' show="'+show+'"';
		}
		shortcode += ']';
		
		shortcode += '<ul><li><a href="#topic1" >Topic 1</a></li><li><a href="#topic2" >Topic 2</a></li><li><a href="#topic3" >Topic 3</a></li><li><a href="#topic4" >Topic 4</a></li><li><a href="#topic5" >Topic 5</a></li></ul>';
		
		
		shortcode += '[/toc]';
			
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
	<form name="toc" action="#" >
		<div class="tabs">
			<ul>
				<li id="toc_tab" class="current"><span><a href="javascript:mcTabs.displayTab('toc_tab','toc_panel');" onMouseDown="return false;">Table of Contents</a></span></li>
			</ul>
		</div>
		<div class="panel_wrapper">
				<fieldset style="margin-bottom:10px;padding:10px">
					<legend>Table of Contents settings:</legend>
					<label for="title">Title:</label><br><br>
					<input type="text" name="title" id="title" style="width:250px" value="Table of Contents" />
					<br><br>
					<label for="hide">"Hide" link text:</label>
					<input type="text" name="hide" id="hide"   style="width:250px" value="hide"/>					
					<br><br>
					<label for="show">"Show" link text:</label>
					<input type="text" name="show" id="show"   style="width:250px" value="show" /> 
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