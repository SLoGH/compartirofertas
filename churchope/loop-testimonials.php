<?php
/* Start the Loop  */
if (have_posts()) :
	while (have_posts()) : the_post();

		
			?>
			<article <?php post_class('clearfix testimonial') ?> >
				<div class="quote">
						<?php echo the_content();?>
						</div>
						<div class="testimonial_meta">
							<span class="testimonial_author"><?php echo get_post_meta(get_the_ID(), SHORTNAME.'_testimonial_author', true);?></span>
							<span><?php echo get_post_meta(get_the_ID(), SHORTNAME.'_testimonial_author_job', true);?></span>
						</div>
			</article>

				
			<?php endwhile; ?>
			<?php
			// get total number of pages
			global $wp_query;
			$total = $wp_query->max_num_pages;
// only bother with the rest if we have more than 1 page!
			if ($total > 1)
			{
				?>
		<div class="pagination clearfix">
		<?php
		// get the current page
		if (get_query_var('paged'))
		{
			$current_page = get_query_var('paged');
		}
		else if (get_query_var('page'))
		{
			$current_page = get_query_var('page');
		}
		else
		{
			$current_page = 1;
		}
		// structure of "format" depends on whether we're using pretty permalinks
		$permalink_structure = get_option('permalink_structure');
		if (empty($permalink_structure))
		{
			if (is_front_page())
			{
				$format = '?paged=%#%';
			}
			else
			{
				$format = '&paged=%#%';
			}
		}
		else
		{
			$format = 'page/%#%/';
		}



		echo paginate_links(array(
			'base' => get_pagenum_link(1) . '%_%',
			'format' => $format,
			'current' => $current_page,
			'total' => $total,
			'mid_size' => 10,
			'type' => 'list'
		));
		?>
		</div>
	<?php } ?>
<?php else : ?>
	<article class="hentry">
		<h1>
	<?php _e('Not Found', 'churchope'); ?>
		</h1>
		<p class="center">
	<?php _e('Sorry, but you are looking for something that isn\'t here.', 'churchope'); ?>
		</p>
	</article>
<?php endif; ?>
<?php wp_reset_query();?>