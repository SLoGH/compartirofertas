<?php
/*
  Template Name: LeftSidebar
 */
?>
<?php get_header(); ?>
<div id="contentarea" class="row">
	<aside class="grid_4 left-sidebar">
		<?php (get_post_meta(get_the_ID(), SHORTNAME . '_page_sidebar', true)) ? $sidebar = get_post_meta(get_the_ID(), SHORTNAME . '_page_sidebar', true) : $sidebar = "default-sidebar";
		generated_dynamic_sidebar_th($sidebar); ?>
	</aside>
	<div class="grid_8">    
		<?php get_template_part('loop'); ?>
	</div>	
</div>
<?php get_footer(); ?>