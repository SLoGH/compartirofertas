<?php get_header(); ?>
<div id="contentarea" class="row">
	<?php if (get_option(SHORTNAME . '_post_listing_layout') == 'left')
	{ ?>
		<aside class="grid_4 left-sidebar">
			<?php (get_option(SHORTNAME . '_post_listing_sidebar')) ? $sidebar = get_option(SHORTNAME . '_post_listing_sidebar') : $sidebar = "default-sidebar";
			generated_dynamic_sidebar_th($sidebar);
			?>
		</aside>
<?php } ?>
	<div class="<?php echo (get_option(SHORTNAME . '_post_listing_layout') == 'none') ? 'grid_12' : 'grid_8'; ?>">    
		<article  class="clearfix grid_8">

			<h2 class="entry-title">
				<?php _e('The page you are trying to reach can&apos;t be found', 'churchope'); ?>
			</h2>
			<p>
<?php _e('Try refining your search, or use the navigation above to locate the post.', 'churchope'); ?>
			</p><p>
				<a href="<?php echo get_home_url() ?>" class="churchope_button">

<?php _e('back to home', 'churchope'); ?>
				</a></p>
		</article>
	</div>
		<?php if (get_option(SHORTNAME . '_post_listing_layout') == 'right')
		{ ?>
		<aside class="grid_4 right-sidebar">
		<?php (get_option(SHORTNAME . '_post_listing_sidebar')) ? $sidebar = get_option(SHORTNAME . '_post_listing_sidebar') : $sidebar = "default-sidebar";
		generated_dynamic_sidebar_th($sidebar);
		?>
		</aside>
<?php } ?>
</div>
<?php get_footer(); ?>