<?php	
defined('WP_ADMIN') || define('WP_ADMIN', true);
require_once('../../../../../../wp-load.php');
?>
<!doctype html>
<html lang="en">
<head>
	<title>Insert Testimonials</title>
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
		
		var category	= jQuery('#category').val();
		var time		= jQuery('#time').val();
		var randomize	= jQuery('#randomize').is(':checked');
		var effect		= jQuery('#effect').val();
		
		shortcode = ' [testimonials';
		if(category.length)
		{
			shortcode += ' category="'+decodeURIComponent(category)+'" ';
		}
		if(time.length)
		{
			shortcode += ' time="'+time+'" ';
		}
		
		if(randomize)
		{
			shortcode += ' randomize="on" ';
		}
	
		if(effect)
		{
			shortcode += ' effect="'+effect+'" ';
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
				<li id="notifications_tab" class="current"><span><a href="javascript:mcTabs.displayTab('notifications_tab','notifications_panel');" onMouseDown="return false;">Testimonials</a></span></li>
			</ul>
		</div>
		<div class="panel_wrapper">			
			<fieldset style="margin-bottom:10px;padding:10px">
				<legend>Testimonial Category:</legend>
				<label for="category">Choose testimonial category:</label><br><br>
				<select name="category" id="category"  style="width:250px">
					<option value="all">All</option>
					<?php 
					if($categories = get_terms(Custom_Posts_Type_Testimonial::TAXONOMY, array('taxonomy' => Custom_Posts_Type_Testimonial::TAXONOMY)) )
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
				<legend>Number of second to show:</legend>
					<label for="time">Second:</label><br><br>
					<input name="time" type="text" id="time" style="width:250px" value="10">
			</fieldset>			
			<fieldset style="margin-bottom:10px;padding:10px">
				<legend>Randomize testimonial:</legend>
				<label for="randomize">Randomize:</label>
					<input name="randomize" type="checkbox" id="randomize">
			</fieldset>
			<fieldset style="margin-bottom:10px;padding:10px">
				<legend>Transition effect:</legend>
				<label for="effect">Choose effect:</label><br><br>
				<select name="effect" id="effect"  style="width:250px">
					<?php 
					if($categories = get_terms(Custom_Posts_Type_Testimonial::TAXONOMY))
					{
						foreach(Widget_Testimonial::getEffectList() as $name => $description)
						{
							$selected = '';
							if($name == 'fade') // default effect
							{
								$selected = ' selected="selected"';
							}
							?>
							<option value="<?php echo esc_html($name)?>"<?php echo $selected?>><?php echo esc_html($description); ?></option>
						<?php }
					}
					?>
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