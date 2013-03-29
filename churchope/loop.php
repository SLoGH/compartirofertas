<?php
/* Start the Loop  */
if (have_posts()) :
	while (have_posts()) : the_post();


		/* Default page */
		if (is_page()) :
			?>

			<article id="page-<?php the_ID(); ?>" <?php post_class('clearfix'); ?>>
				<div class="entry-content">
					<?php the_content(); ?>
				</div>
				<?php wp_link_pages(); ?>

				<?php comments_template('', true); ?>

			</article>


			<?php
		/* Single post */
		elseif (is_single()) :
			?>
			<article <?php post_class('clearfix') ?> >
				<div class="post_title_area">				
				<?php
					if ( function_exists( 'add_theme_support' ) ) {
						add_theme_support( 'post-thumbnails' );
						set_post_thumbnail_size( 180, 130, true);
					}
				?>
				<h1 class="entry-title"><span><?php the_title(); ?></span></h1>
					<div style="float:left;"> 						
					<?php if (has_post_thumbnail())
										{ ?>
					<article class='gallery_listing  small grid_3 ' id="post-<?php the_ID(); ?>"><a href="
								<?php $imagen = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), 'full');
								echo $imagen[0];
								?>" data-pp="lightbox[]"  title="<?php echo the_title(); ?>" class="imgborder thumb jpg"><?php echo get_the_post_thumbnail(get_the_ID(), 'post-thumbnail'); ?></a></article>
				<?php } ?>	
					
					</div>
					<div class="blogtitles">
						<p>
							<?php
							if (get_the_tags())
							{
								the_tags();
								?> <?php } ?>
						</p>
						<p><?php _e('Categoría:', 'churchope'); ?> <?php the_category(','); ?></p>
						<br />
						<?php $price = get_post_meta($post->ID, 'cf_user_submit_price', true); 
						if (!empty($price)){
						?>
						
						<h4><?php echo("Precio: ".$price."€");  ?></h4>
						<?php } ?>
						
						<a class="th_button churchope_button"  target="_blank" href="<?php  echo get_post_meta($post->ID,'cf_user_submit_url',true);?>"><span>Ir a Oferta</span></a>
						<?php if(function_exists('the_ratings')) { the_ratings(); } ?>
					</div>
				</div>
				<div class="entry-content">
				<h3>Información del producto:</h3><br />
					<?php the_content(); ?>
				</div>



				<?php
				if (!get_option(SHORTNAME . "_authorbox"))
				{
					?>
					<div id="authorbox">
						<?php
						if (function_exists('get_avatar'))
						{
							echo get_avatar(get_the_author_meta('email'), '100');
						}
						?>
						<div>
							<h5><?php
				_e('Author: ', 'churchope');
				echo '<span>';
				the_author_meta('display_name');
				echo '</span>';
						?></h5>
							<p><?php the_author_meta('description'); ?></p>
						</div>
					</div>
				<?php } ?>



				<?php
				if (get_option(SHORTNAME . "_related") == '')
				{
					?>
					<?php locate_template(array('inc/related.php'), true, true); ?>	
				<?php } ?>

				<?php comments_template('', true); ?>
			</article>

			<?php
		/* Categories/tags/archives listing */
		elseif (is_archive()) :
			?>

			<article <?php post_class('posts_listing') ?> id="post-<?php the_ID(); ?>">

				<?php
				if (has_post_thumbnail())
				{
					?>		
					<a href="<?php the_permalink() ?>" title="<?php echo the_title(); ?>" class="imgborder clearfix thumb listing"><?php get_theme_post_thumbnail(get_the_ID(), 'blog_thumbnail'); ?></a>
				<?php } ?>

				<div class="post_title_area">
					<div class="postdate"><span></span><strong class="day"><?php echo get_the_date('d') ?></strong><strong class="month"><?php echo get_the_date('M') ?></strong></div>
					<div class="blogtitles"><h2 class="entry-title"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php _e('Permanent Link to', 'churchope'); ?> <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
						<p class="postmeta"><?php
			if (comments_open()) : comments_popup_link(__('Comments (0)', 'churchope'), __('Comment (1)', 'churchope'), __('Comments (%)', 'churchope'), 'commentslink');
				echo "<span>|</span>";
			endif;
				?><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php _e('Permanent Link to', 'churchope'); ?> <?php the_title_attribute(); ?>"  class=""><?php _e('Read more', 'churchope'); ?></a></p>


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

			<?php
		/* Blog posts */
		else :
			?>



			<article <?php post_class('posts_listing') ?> id="post-<?php the_ID(); ?>">

				<?php
				if (has_post_thumbnail())
				{
					?>		
					<a href="<?php the_permalink() ?>" title="<?php echo the_title(); ?>" class="imgborder clearfix thumb listing"><?php get_theme_post_thumbnail(get_the_ID(), 'blog_thumbnail'); ?></a>
				<?php } ?>

				<div class="post_title_area">
					<div class="postdate"><span></span><strong class="day"><?php echo get_the_date('d') ?></strong><strong class="month"><?php echo get_the_date('M') ?></strong></div>
					<div class="blogtitles"><h2 class="entry-title"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php _e('Permanent Link to', 'churchope'); ?> <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
						<p class="postmeta"><?php
			if (comments_open()) : comments_popup_link(__('Comments (0)', 'churchope'), __('Comment (1)', 'churchope'), __('Comments (%)', 'churchope'), 'commentslink');
				echo "<span>|</span>";
			endif;
				?><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php _e('Permanent Link to', 'churchope'); ?> <?php the_title_attribute(); ?>"  class=""><?php _e('Read more', 'churchope'); ?></a></p>


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

		<?php endif; ?>
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
			// structure of “format” depends on whether we’re using pretty permalinks
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
