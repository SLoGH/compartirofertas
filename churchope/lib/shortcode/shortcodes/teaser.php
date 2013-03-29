<?php
	defined('WP_ADMIN') || define('WP_ADMIN', true);
	require_once('../../../../../../wp-load.php');
	$posts = get_posts( array(  'post_type'		=> array('page', 'post', Custom_Posts_Type_Gallery::POST_TYPE),
								'post_status'	=> 'publish',
								'numberposts'	=> -1,
								'suppress_filters' => '0',
							)); 
?>	
<!doctype html>
<html lang="en">
	<head>
	<title>Insert Teaser</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<script language="javascript" type="text/javascript" src="<?php echo includes_url();?>/js/tinymce/tiny_mce_popup.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo includes_url();?>/js/tinymce/utils/mctabs.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo includes_url();?>/js/tinymce/utils/form_utils.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo includes_url();?>/js/jquery/jquery.js?ver=1.4.2"></script>
	<script language="javascript" type="text/javascript">
		var posts_data = {};
	<?php if($posts && count($posts)):?>
		<?php foreach ($posts as $post):?>
			var data = {};
			data.id = <?php echo $post->ID?>;
			data.title = '<?php echo str_replace(array("\r", "\n"), '', addslashes($post->post_title)); ?>';
			data.post_url = '<?php echo get_permalink( $post->ID ); ?>';
			
			posts_data['<?php echo $post->post_name?>'] = data;
		<?php endforeach;?>
	<?php endif;?>
	</script>
	<script language="javascript" type="text/javascript">
		
	jQuery(document).ready(function() {
		jQuery("#select_post").change(function() {
			var slug = jQuery(this).val();
			if(typeof posts_data[slug] != 'undefined')
			{
				var details = posts_data[slug];
				jQuery('#title').val(details.title);
				jQuery('#button_url').val(details.post_url);
			}
		});	
	});
	function init() {
		
		tinyMCEPopup.resizeToInnerSize();
	}
	
	
	function submitData() {				
		var shortcode;
		
		var url = jQuery('#button_url').val();
		var title = jQuery('#title').val();
		var button_title = jQuery('#button_title').val();
		var src = jQuery('#image_url').val();
		shortcode = ' [teaser';
		if(url.length)
		{
			shortcode += ' url="'+url+'" ';
		}
		
		if(title.length)
		{
			shortcode += ' title="'+title+'" ';
		}
		
		if(src.length)
		{
			shortcode += ' src="'+src+'" ';
			
		}
		else
		{
			var slug = jQuery('#select_post').val();
			if(typeof posts_data[slug] != 'undefined')
			{
				var details = posts_data[slug];
				shortcode += ' post="'+details.id+'" ';
			}
		}
		if (jQuery('#target').is(':checked')) {
			shortcode += ' target="_blank"';
		}
		
		shortcode += ']';
		if(button_title.length)
		{
			shortcode +=button_title;
		}
		shortcode += '[/teaser]';
			
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
				<li id="notifications_tab" class="current"><span><a href="javascript:mcTabs.displayTab('notifications_tab','notifications_panel');" onMouseDown="return false;">Teaser</a></span></li>
			</ul>
		</div>
		<div class="panel_wrapper">
				<fieldset style="margin-bottom:10px;padding:10px">
					<legend>Post, Page, Gallery:</legend>
					<label for="select_post">Choose a template:</label><br><br>
					<select name="select_post" id="select_post"  style="width:250px">
						<option value="" disabled selected>Select type</option>
						<?php 
						if($posts && count($posts))
						{
							foreach($posts as $post)
							{?>
								<option value="<?php echo esc_html($post->post_name)?>"><?php echo esc_html($post->post_title); ?></option>
							<?php }
						}
						?>
					</select>					
				</fieldset>
			<fieldset style="margin-bottom:10px;padding:10px">
				<legend>Image src:</legend>
					<label for="image_url">Type your image URL here:</label><br><br>
					<input name="image_url" type="text" id="image_url" style="width:250px">
			</fieldset>
			<fieldset style="margin-bottom:10px;padding:10px">
				<legend>Title:</legend>
					<label for="title">Type your teaser title:</label><br><br>
					<input name="title" type="text" id="title" style="width:250px">
			</fieldset>
			<fieldset style="margin-bottom:10px;padding:10px">
				<legend>Button title:</legend>
					<label for="button_title">Type your button title here:</label><br><br>
					<input name="button_title" type="text" id="button_title" style="width:250px" value="learn  more...">
			</fieldset>
			<fieldset style="margin-bottom:10px;padding:10px">
				<legend>URL for button:</legend>
					<label for="button_url">Type your button URL here:</label><br><br>
					<input name="button_url" type="text" id="button_url" style="width:250px" >
			</fieldset>
			<fieldset style="margin-bottom:10px;padding:10px">
				<legend>Target Blank:</legend>
					<label for="target">Check if you want open in new window:</label><br><br>
					<input name="target" type="checkbox" id="target">
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