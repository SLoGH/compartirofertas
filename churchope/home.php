<?php get_header(); ?>
<div id="contentarea" class="row">
	<?php if (get_option( SHORTNAME . '_post_listing_layout') == 'left') { ?>
	<aside class="grid_4 left-sidebar">
		<?php (get_option( SHORTNAME . '_post_listing_sidebar')) ? $sidebar = get_option( SHORTNAME . '_post_listing_sidebar') : $sidebar = "default-sidebar";
		generated_dynamic_sidebar_th($sidebar); ?>
	</aside>
	<?php } ?>
	<div class="<?php echo (get_option( SHORTNAME . '_post_listing_layout') == 'none') ? 'grid_12' : 'grid_8'; ?>">    
		<?php get_template_part('loop'); ?>
	</div>
	<?php if (get_option( SHORTNAME . '_post_listing_layout') == 'right') { ?>
	<aside class="grid_4 right-sidebar">
		<?php (get_option( SHORTNAME . '_post_listing_sidebar')) ? $sidebar = get_option( SHORTNAME . '_post_listing_sidebar') : $sidebar = "default-sidebar";
		generated_dynamic_sidebar_th($sidebar); ?>
	</aside>
	<?php } ?>
</div>
<?php get_footer(); ?>
