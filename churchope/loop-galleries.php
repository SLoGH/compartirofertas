<?php
/* Start the Loop  */
if (have_posts()) :?>
<div class="gallery_wrap">
<?php	while (have_posts()) : the_post();

		$disable_thumb = (get_post_meta(get_the_ID(), SHORTNAME . '_gallery_hide_thumb', true)) ? get_post_meta(get_the_ID(), SHORTNAME . '_gallery_hide_thumb', true) : null;
		$icon = (get_post_meta(get_the_ID(), SHORTNAME . '_gallery_icon', true)) ? get_post_meta(get_the_ID(), SHORTNAME . '_gallery_icon', true) : get_option(SHORTNAME . Admin_Theme_Item_Galleries::CUSTOM_GALLERY_ICONS . '_active', '');
		$live_url = (get_post_meta(get_the_ID(), SHORTNAME . '_gallery_url', true)) ? get_post_meta(get_the_ID(), SHORTNAME . '_gallery_url', true) : null;
		$live_button = (get_post_meta(get_the_ID(), SHORTNAME . '_gallery_url_button', true)) ? get_post_meta(get_the_ID(), SHORTNAME . '_gallery_url_button', true) : __('Launch project', 'churchope');
		$preview_url = (get_post_meta(get_the_ID(), SHORTNAME . '_url_lightbox', true)) ? get_post_meta(get_the_ID(), SHORTNAME . '_url_lightbox', true) : null;
		$use_lightbox = (get_post_meta(get_the_ID(), SHORTNAME . '_use_lightbox', true)) ? get_post_meta(get_the_ID(), SHORTNAME . '_use_lightbox', true) : null;
		$hide_more = (get_post_meta(get_the_ID(), SHORTNAME . '_gallery_hide_more', true)) ? get_post_meta(get_the_ID(), SHORTNAME . '_gallery_hide_more', true) : null;
		$live_target = (get_post_meta(get_the_ID(), SHORTNAME . '_gallery_target', true)) ? get_post_meta(get_the_ID(), SHORTNAME . '_gallery_target', true) : null;
		$ext = null;
		
		
		if ($preview_url)
		{

			$hostname = parse_url($preview_url, PHP_URL_HOST);

			if (preg_match("/\b(?:vimeo|youtube|dailymotion|youtu)\.(?:com|be)\b/i", $hostname))
			{
				$ext = "video";
			}
			else
			{

				$path = parse_url($preview_url, PHP_URL_PATH);

				$ext = pathinfo($path, PATHINFO_EXTENSION);
			}
		}
		/* Single page */
		if (is_single()) :
			?>
			<article <?php post_class('clearfix') ?> >

			<?php if (has_post_thumbnail() && !$disable_thumb)
			{ ?>				
					<span class="imgborder thumb"><?php get_theme_post_thumbnail(get_the_ID(), 'full'); ?></span>				
					<?php } ?>


							
				<div class="entry-content">
				<?php the_content(); ?>
				</div>

			<?php if ($live_url)
			{ ?>
					<a href="<?php echo $live_url; ?>" class="churchope_button clearfix" <?php echo ($live_target) ? 'target="_blank"' : ''; ?> ><?php echo $live_button; ?></a>
			<?php } ?>

			<?php comments_template('', true); ?>
			</article>

			<?php
		/* Categories/tags/archives listing */
		elseif (is_archive()) :

			global $wp_query,$post_layout;;
			$term = $wp_query->get_queried_object();
			$gal_layout = (get_tax_meta($term->term_id, SHORTNAME . "_layout", true)) ? get_tax_meta($term->term_id, SHORTNAME . "_layout", true) : null;
			$layout = ($post_layout == 'layout_none_sidebar') ? 'grid_12' : 'grid_8'; 

			switch ($gal_layout)
			{
				
				case 'medium':
					
					 global $post_layout;
					 $num =  ($post_layout == 'layout_none_sidebar') ? '3' : '2'; 
					  
					 $linebreak = ($wp_query->current_post % $num == 0  )? 'clearboth':'';
					?>
					<article <?php post_class('gallery_listing  small grid_4 '.$linebreak) ?> id="post-<?php the_ID(); ?>">
						<?php if (has_post_thumbnail())
						{ ?>		
						<a href="<?php if ($preview_url)
							{
								echo $preview_url;
							} elseif (!$preview_url && $use_lightbox) {
								
								$imgsrc = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), 'full');	
								
								echo $imgsrc[0];
								
								$ext = 'jpg';
							}
							else
							{
								the_permalink();
							} ?>" <?php echo ($use_lightbox) ? 'data-pp="lightbox[]"' : ''; ?>  title="<?php echo the_title(); ?>" class="imgborder thumb  <?php echo $ext; ?>"><?php get_theme_post_thumbnail(get_the_ID(), 'gallery_big'); ?></a>	
				<?php } ?>		   
					<div class="postcontent  clearfix">   
						<h2 class="entry-title"><?php if ($icon)
				{ ?><img src="<?php echo $icon ?>" alt="<?php the_title() ?>"  ><?php } ?><a href="<?php the_permalink(); ?>" ><?php the_title(); ?></a></h2>
						<div class="entry-content">
				<?php the_excerpt(); ?>
						</div>
								
					</div>			
				</article>
				<?php	
				break;
			
				case 'small':
					
						 global $post_layout;
					 $num =  ($post_layout == 'layout_none_sidebar') ? '3' : '2'; 
					  
					 $linebreak = ($wp_query->current_post % $num == 0  )? 'clearboth':'';
					?>
					<?php if (has_post_thumbnail())
										{ ?>
					<article <?php post_class('gallery_listing  small grid_4 '.$linebreak) ?> id="post-<?php the_ID(); ?>"><a href="<?php if ($preview_url)
							{
								echo $preview_url;
							} elseif (!$preview_url && $use_lightbox) {
								
								$imgsrc = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), 'full');	
								
								echo $imgsrc[0];
								
								$ext = 'jpg';
							}
							else
							{
								the_permalink();
							} ?>" <?php echo ($use_lightbox) ? 'data-pp="lightbox[]"' : ''; ?>  title="<?php echo the_title(); ?>" class="imgborder thumb  <?php echo $ext; ?>"><?php get_theme_post_thumbnail(get_the_ID(), 'gallery_big'); ?></a></article>
				<?php } ?>	
				<?php	
				break;
				
				
				
				default:
				?>

				<article <?php post_class('gallery_listing '.$layout) ?> id="post-<?php the_ID(); ?>">
						<?php if (has_post_thumbnail())
						{ ?>		
						<a href="<?php if ($preview_url)
							{
								echo $preview_url;
							} elseif (!$preview_url && $use_lightbox) {
								
								$imgsrc = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), 'full');	
								
								echo $imgsrc[0];
								
								$ext = 'jpg';
							}
							else
							{
								the_permalink();
							} ?>" <?php echo ($use_lightbox) ? 'data-pp="lightbox[]"' : ''; ?>  title="<?php echo the_title(); ?>" class="imgborder thumb  <?php echo $ext; ?>"><?php get_theme_post_thumbnail(get_the_ID(), 'gallery_big'); ?></a>	
				<?php } ?>		   
					<div class="postcontent  clearfix">   
						<h2 class="entry-title"><?php if ($icon)
				{ ?><img src="<?php echo $icon ?>" alt="<?php the_title() ?>"  ><?php } ?><a href="<?php the_permalink(); ?>" ><?php the_title(); ?></a></h2>
						<div class="entry-content">
				<?php excerpt(220);  ?>
						</div>
				<?php if ($live_url || !$hide_more)
				{ ?>
							<div class="buttons">						
					<?php if ($live_url)
					{ ?>
									<a href="<?php echo $live_url; ?>" class="simple_button_link clearfix" <?php echo ($live_target) ? 'target="_blank"' : ''; ?>  ><?php echo $live_button; ?></a>
					<?php } ?>
					<?php if (!$hide_more)
					{ ?>
									<a href="<?php the_permalink(); ?>" class="simple_button_black clearfix" ><?php _e('more info', 'churchope') ?></a>
						<?php } ?>
							</div>
					<?php } ?>					
					</div>			
				</article>

				<?php break;?>
					
				
				<?php } ?>

			<?php endif; ?>
		<?php endwhile; ?>
</div>
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
<?php wp_reset_query(); ?>