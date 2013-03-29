<?php get_header();
global $post_layout;
$post_sidebar = get_option(SHORTNAME . '_events_listing_sidebar');
?>
<div id="contentarea" class="row">
	<?php if ($post_layout == 'layout_left_sidebar')
	{ ?>
		<aside class="grid_4 left-sidebar">
			<?php $sidebar = ($post_sidebar) ? $post_sidebar : "default-sidebar";
			generated_dynamic_sidebar_th($sidebar);
			?>
		</aside>
		<?php } ?>
	<div class="<?php echo ($post_layout == 'layout_none_sidebar') ? 'grid_12' : 'grid_8'; ?>">
	<?php get_template_part('loop','eventsforaday'); ?>
	</div>
		<?php if ($post_layout == 'layout_right_sidebar')
		{ ?>
		<aside class="grid_4 right-sidebar">
		<?php $sidebar = ($post_sidebar) ? $post_sidebar : "default-sidebar";
		generated_dynamic_sidebar_th($sidebar);
		?>
		</aside>
<?php } ?>
</div>
<?php get_footer(); ?>