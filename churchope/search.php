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

<?php if (have_posts()) : ?>
	<?php while (have_posts()) : the_post(); ?>

				<article <?php post_class('posts_listing') ?> id="post-<?php the_ID(); ?>">

					<?php
					if (has_post_thumbnail())
					{
						?>		
						<a href="<?php the_permalink() ?>" title="<?php echo the_title(); ?>" class="imgborder clearfix thumb"><?php get_theme_post_thumbnail(get_the_ID(), 'blog_thumbnail'); ?></a>
		<?php } ?>

					<div class="post_title_area">
						<div class="postdate"><span></span><strong class="day"><?php echo get_the_date('d') ?></strong><strong class="month"><?php echo get_the_date('M') ?></strong></div>
						<div class="blogtitles"><h2 class="entry-title"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php _e('Permanent Link to', 'churchope'); ?> <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
							


							<div class="entry-content">

								<?php
								if (get_option(SHORTNAME . "_excerpt"))
								{
									the_content('', false);
								}
								else
								{
									the_excerpt();
								}
								?>
							</div>

						</div>
					</div>
				</article>

	<?php endwhile; ?>



			<?php
			global $wp_query, $wp_rewrite;
			$total = $wp_query->max_num_pages;
// only bother with the rest if we have more than 1 page!
			if ($total > 1)
			{
				?>
				<div class="pagination clearfix">
					<?php
					$wp_query->query_vars['paged'] > 1 ? $current = $wp_query->query_vars['paged'] : $current = 1;

					$pagination = array(
						'base' => @add_query_arg('paged', '%#%'),
						'format' => '',
						'total' => $wp_query->max_num_pages,
						'current' => $current,
						'show_all' => true,
						'type' => 'list'
					);

					if ($wp_rewrite->using_permalinks())
						$pagination['base'] = user_trailingslashit(trailingslashit(remove_query_arg('s', get_pagenum_link(1))) . 'page/%#%/', 'paged');

					if (!empty($wp_query->query_vars['s']))
						$pagination['add_args'] = array('s' => get_query_var('s'));

					echo paginate_links($pagination);
					?>
				</div>
			<?php } ?>



			<?php else : ?>
			<h2 class="entry-title">
			<?php _e('Not Found', 'churchope'); ?>
			</h2>
			<p class="center">
		<?php _e("Sorry, but you are looking for something that isn't here.", 'churchope'); ?>
			</p>
		<?php endif; ?>
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
<?php get_footer();?>