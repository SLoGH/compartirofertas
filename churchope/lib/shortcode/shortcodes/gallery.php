<?php
defined('WP_ADMIN') || define('WP_ADMIN', true);
require_once('../../../../../../wp-load.php');
?>
<!doctype html>
<html lang="en">
	<head>
	<title>Gallery</title>
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
//		var selectedContent = tinyMCE.activeEditor.selection.getContent();				
		var taxonomy_terms = jQuery('#taxonomy_terms').val();		
		var perpage = jQuery('#perpage').val();	
		var layout = jQuery('#layout_type').val();	
		if (jQuery('#pagination').is(':checked')) {
		var pagination = jQuery('#pagination:checked').val();} else {var pagination = '';}	
		if (jQuery('#isotope').is(':checked')) {
		var isotope = jQuery('#isotope:checked').val();} else {var isotope = '';}	
		shortcode = ' [terms_gallery terms="'+taxonomy_terms+'" perpage="'+perpage+'" pagination="'+pagination+'" layout="'+layout+'" isotope="'+isotope+'" ]';			
			
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
	<form name="gallery" action="#" >
		<div class="tabs">
			<ul>
				<li id="gallery_tab" class="current"><span><a href="javascript:mcTabs.displayTab('gallery_tab','gallery_panel');" onMouseDown="return false;">Gallery</a></span></li>
			</ul>
		</div>
		<div class="panel_wrapper">
			
				<fieldset style="margin-bottom:10px;padding:10px">
					<legend>Taxonomy terms:</legend>
					<label for="taxonomy_terms">Choose a taxonomy terms:</label><br><br>
				
					<?php wp_dropdown_categories('name=taxonomy_terms&id=taxonomy_terms&show_count=1&hierarchical=1&taxonomy='.Custom_Posts_Type_Gallery::TAXONOMY); ?>
				</fieldset>
				<fieldset style="margin-bottom:10px;padding:10px">
				<legend>Filterable gallery:</legend>
					<label for="isotope">Check if you want use filterable gallery:</label><br><br>
					<input name="isotope" type="checkbox" id="isotope">
				</fieldset>
				<fieldset style="margin-bottom:10px;padding:10px">
				<legend>Show per page:</legend>
					<label for="perpage">Number to show:</label><br><br>
					<input name="perpage" type="text" id="perpage" style="width:250px">
				</fieldset>
				<fieldset style="margin-bottom:10px;padding:10px">
				<legend>Pagination:</legend>
					<label for="pagination">Check if you want show pagination:</label><br><br>
					<input name="pagination" type="checkbox" id="pagination">
				</fieldset>
			
				<fieldset style="margin-bottom:10px;padding:10px">
					<legend>Layout Type:</legend>
					<label for="layout_type">Choose a layout type:</label><br><br>
					<select name="layout_type" id="layout_type"  style="width:250px">
						<option value="">Big</option>
						<option value="medium">Medium</option>	
						<option value="small">Small</option>						
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