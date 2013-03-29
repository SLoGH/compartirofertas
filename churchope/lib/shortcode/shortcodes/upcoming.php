<?php
	defined('WP_ADMIN') || define('WP_ADMIN', true);
	require_once('../../../../../../wp-load.php');
?>	
<!doctype html>
<html lang="en">
	<head>
	<title>Insert Upcoming events</title>
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
		
		var category = jQuery('#<?php echo Widget_Event_Upcoming::CATEGORY?>').val();
		var count = jQuery('#<?php echo Widget_Event_Upcoming::COUNT?>').val();
		
		var place = jQuery('#<?php echo Widget_Event_Upcoming::PLACE?>').is(':checked');
	
		var time = jQuery('#<?php echo Widget_Event_Upcoming::TIME?>').is(':checked');
		var phone = jQuery('#<?php echo Widget_Event_Upcoming::PHONE?>').is(':checked');
		
		shortcode = ' [upcoming';
		if(category.length)
		{
			shortcode += ' <?php echo Widget_Event_Upcoming::CATEGORY?>="'+decodeURIComponent(category)+'" ';
		}
		if(count.length)
		{
			shortcode += ' <?php echo Widget_Event_Upcoming::COUNT?>="'+count+'" ';
		}
		
		if(place)
		{
			shortcode += ' <?php echo Widget_Event_Upcoming::PLACE?>="1" ';
		}
	
		if(time)
		{
			shortcode += ' <?php echo Widget_Event_Upcoming::TIME?>="1" ';
		}
		if(phone)
		{
			shortcode += ' <?php echo Widget_Event_Upcoming::PHONE?>="1" ';
		}
		
		shortcode += ']';
			
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
	<form name="notifications" action="#" >
		<div class="tabs">
			<ul>
				<li id="notifications_tab" class="current"><span><a href="javascript:mcTabs.displayTab('notifications_tab','notifications_panel');" onMouseDown="return false;">Upcoming Events</a></span></li>
			</ul>
		</div>
		<div class="panel_wrapper">
			<fieldset style="margin-bottom:10px;padding:10px">
				<legend>Event Category:</legend>
				<label for="select_post">Choose event category:</label><br><br>
				<select name="select_post" id="<?php echo  Widget_Event_Upcoming::CATEGORY?>"  style="width:250px">
					<option value="<?php echo Widget_Event_Upcoming::ALL?>">All</option>
					<?php 
					if($categories = get_terms(Custom_Posts_Type_Event::TAXONOMY))
					{
						foreach($categories as $category)
						{?>
							<option value="<?php echo esc_html($category->slug)?>"><?php echo esc_html($category->name); ?></option>
						<?php }
					}
					?>
				</select>					
			</fieldset>
			
			
			<fieldset style="margin-bottom:10px;padding:10px">
				<legend>Displaying Count:</legend>
					<label for="<?php echo Widget_Event_Upcoming::COUNT?>">Choose how many events show:</label><br><br>
					<input name="<?php echo Widget_Event_Upcoming::COUNT?>" type="text" id="<?php echo Widget_Event_Upcoming::COUNT?>" style="width:250px" value="4">
			</fieldset>
			
			<fieldset style="margin-bottom:10px;padding:10px">
				<legend>Show event time:</legend>
				<label for="<?php echo Widget_Event_Upcoming::TIME?>">Choose to show:</label>
					<input name="<?php echo Widget_Event_Upcoming::TIME?>" type="checkbox" id="<?php echo Widget_Event_Upcoming::TIME?>">
			</fieldset>
			<fieldset style="margin-bottom:10px;padding:10px">
				<legend>Show event place:</legend>
				<label for="<?php echo Widget_Event_Upcoming::PLACE?>">Choose to show:</label>
					<input name="<?php echo Widget_Event_Upcoming::PLACE?>" type="checkbox" id="<?php echo Widget_Event_Upcoming::PLACE?>">
			</fieldset>
			<fieldset style="margin-bottom:10px;padding:10px">
				<legend>Show event phone:</legend>
				<label for="<?php echo Widget_Event_Upcoming::PHONE?>">Choose to show:</label>
					<input name="<?php echo Widget_Event_Upcoming::PHONE?>" type="checkbox" id="<?php echo Widget_Event_Upcoming::PHONE?>">
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