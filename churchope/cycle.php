<?php
$pid = (isset($post->ID)) ? $post->ID : NULL;

if (is_home())
{
	$pid = get_option("page_for_posts");
}
	global $wp_query;
	$current_term = $wp_query->get_queried_object();

// taxonomy page
if( (is_tax() || is_tag() || is_category()) && $current_term && get_tax_meta($current_term->term_id, SHORTNAME . "_tax_slider", true))
{
	$slider_cat		= get_tax_meta($current_term->term_id, SHORTNAME . "_tax_slider_cat", true);
	$slider_count	= get_tax_meta($current_term->term_id, SHORTNAME . "_tax_slider_count", true);
}
//post page
elseif( !is_tax() && !is_tag() && !is_category() && get_post_meta($pid, SHORTNAME . "_post_slider", true))
{
	$slider_cat		= get_post_meta($pid, SHORTNAME . "_post_slider_cat", true);
	$slider_count	= get_post_meta($pid, SHORTNAME . "_post_slider_count", true);
}
//global slideshow settings
else
{
	$slider_cat		= get_option(SHORTNAME . Admin_Theme_Item_Slideshow::CATEGORY);
	$slider_count	= get_option(SHORTNAME . Admin_Theme_Item_Slideshow::COUNT);
}
$args = array(
	'post_type'			=> Custom_Posts_Type_Slideshow::POST_TYPE,
	'post_status'		=> 'publish',
	'posts_per_page'	=> $slider_count,
	'order'				=> 'DESC',
	'tax_query'			=> array(
								array(
									'taxonomy' => Custom_Posts_Type_Slideshow::TAXONOMY,
									'field' => 'slug',
									'terms' => $slider_cat
								)));

$slider_query = new WP_Query($args);

if ($slider_query->have_posts()) :
	wp_enqueue_script('jcycle');

if( (is_tax() || is_tag() || is_category()) && $current_term &&  get_tax_meta($current_term->term_id, SHORTNAME . "_tax_slider", true))
{
	$fx			= get_tax_meta($current_term->term_id, SHORTNAME . "_tax_slider_effect", true);
	$timeout	= get_tax_meta($current_term->term_id, SHORTNAME . "_tax_slider_timeout", true);
	$speed		= get_tax_meta($current_term->term_id, SHORTNAME . "_tax_slider_speed", true);
	$navigation	= get_tax_meta($current_term->term_id, SHORTNAME . "_tax_slider_navigation", true);
	$fixedheight= get_tax_meta($current_term->term_id, SHORTNAME . "_tax_slider_fixedheight", true);
	$padding	= get_tax_meta($current_term->term_id, SHORTNAME . "_tax_slider_padding", true);
	$pause		= get_tax_meta($current_term->term_id, SHORTNAME . "_tax_slider_pause", true);
	$autoscroll	= get_tax_meta($current_term->term_id, SHORTNAME . "_tax_slider_autoscroll", true);
	
}
elseif( !is_tax() && !is_tag() && !is_category() && get_post_meta($pid, SHORTNAME . "_post_slider", true))
{
	$fx			= get_post_meta($pid, SHORTNAME . "_post_slider_effect", true);
	$timeout	= get_post_meta($pid, SHORTNAME . "_post_slider_timeout", true);
	$speed		= get_post_meta($pid, SHORTNAME . "_post_slider_speed", true);
	$navigation	= get_post_meta($pid, SHORTNAME . "_post_slider_navigation", true);
	$fixedheight= get_post_meta($pid, SHORTNAME . "_post_slider_fixedheight", true);
	$padding	= get_post_meta($pid, SHORTNAME . "_post_slider_padding", true);
	$pause		= get_post_meta($pid, SHORTNAME . "_post_slider_pause", true);
	$autoscroll	= get_post_meta($pid, SHORTNAME . "_post_slider_autoscroll", true);
}
else
{
	$fx			= get_option(SHORTNAME . Admin_Theme_Item_Slideshow::EFFECT);
	$timeout	= get_option(SHORTNAME . Admin_Theme_Item_Slideshow::TIMEOUT);
	$speed		= get_option(SHORTNAME . Admin_Theme_Item_Slideshow::SPEED);
	$navigation	= get_option(SHORTNAME . Admin_Theme_Item_Slideshow::NAVIGATION);
	$fixedheight= get_option(SHORTNAME . Admin_Theme_Item_Slideshow::FIXEDHEIGHT);
	$padding	= get_option(SHORTNAME . Admin_Theme_Item_Slideshow::PADDING);
	$pause		= get_option(SHORTNAME . Admin_Theme_Item_Slideshow::PAUSE);
	$autoscroll	= get_option(SHORTNAME . Admin_Theme_Item_Slideshow::AUTOSCROLL);
	
}
?>
	<script>
		var slider_fx = '<?php echo $fx; ?>';
		var slider_timeout = <?php echo $timeout; ?>;
		var slider_speed = <?php echo $speed; ?>;
		var slider_navigation = <?php echo (int)!!$navigation; ?>;
		var slider_fixedheight = <?php echo (preg_replace ( '/[^0-9]/', '', $fixedheight ))?preg_replace ( '/[^0-9]/', '', $fixedheight ):'0'; ?>;
		var slider_padding = <?php echo (int)!!$padding; ?>;
		var slider_pause = <?php echo (int)!!$pause; ?>;
		var autoscroll	= <?php echo (int)!!$autoscroll; ?>
	</script>
	
	<div class="row" id="jcyclemain_navigation"><a href="#" id="slide_prev"><span>&lt;</span></a><a href="#" id="slide_next"><span>&gt;</span></a></div>
	
		<div id="jcyclemain">
			<?php
			while ($slider_query->have_posts()) : $slider_query->the_post();

				$cycle_position = (get_post_meta($post->ID, SHORTNAME . "_sliders_cycle_position", true) ) ? get_post_meta($post->ID, SHORTNAME . "_sliders_cycle_position", true) : 'right';
				$slider_title = (get_post_meta($post->ID, SHORTNAME . "_sliders_cycle_title", true) ) ? get_post_meta($post->ID, SHORTNAME . "_sliders_cycle_title", true) : NULL;
				$slider_frame = (get_post_meta($post->ID, SHORTNAME . "_sliders_cycle_frame", true) ) ? ' hide_frame' : NULL;
				$link = (get_post_meta($post->ID, SHORTNAME . "_sliders_link", true) ) ? get_post_meta($post->ID, SHORTNAME . "_sliders_link", true) : NULL;
				$btntxt = (get_post_meta($post->ID, SHORTNAME . "_sliders_cycle_btntxt", true) ) ? get_post_meta($post->ID, SHORTNAME . "_sliders_cycle_btntxt", true) : NULL;
				$usebg = (get_post_meta($post->ID, SHORTNAME . "_slidebg", true) ) ? get_post_meta($post->ID, SHORTNAME . "_slidebg", true) : NULL;
				$slidebg_width = (get_post_meta($post->ID, SHORTNAME . "_slidebg_width", true) ) ? get_post_meta($post->ID, SHORTNAME . "_slidebg_width", true) : NULL;
				$slidebg_repeat = (get_post_meta($post->ID, SHORTNAME . "_slidebg_repeat", true) ) ? get_post_meta($post->ID, SHORTNAME . "_slidebg_repeat", true) : NULL;
				$slidebg_positiony = (get_post_meta($post->ID, SHORTNAME . "_slidebg_positiony", true) ) ? get_post_meta($post->ID, SHORTNAME . "_slidebg_positiony", true) : NULL;
				$slidebg_positionx = (get_post_meta($post->ID, SHORTNAME . "_slidebg_positionx", true) ) ? get_post_meta($post->ID, SHORTNAME . "_slidebg_positionx", true) : NULL;
				$content_align = (get_post_meta($post->ID, SHORTNAME . "_sliders_cycle_content_align", true) ) ? get_post_meta($post->ID, SHORTNAME . "_sliders_cycle_content_align", true) : NULL;
				
				$slidebg = NULL;
				if(has_post_thumbnail()) :

						$post_thumbnail_id = get_post_thumbnail_id( $post->ID );

						$image_attributes = wp_get_attachment_image_src($post_thumbnail_id, 'full');	

						$slidebg = 'data-background="'.$image_attributes[0].'" data-slidesize="'.$image_attributes[2].'"';
				endif; 
				?>
				<div class="jcyclemain <?php echo $cycle_position.$slider_frame; ?>" <?php echo ($usebg)? $slidebg : ''; ?>>
					<div class="bgimage <?php echo ($usebg)? $slidebg_width.' '.$slidebg_repeat.' '.$slidebg_positiony.' '.$slidebg_positionx: ''; ?>" >
					<div class="row">
					<?php
					if (has_post_thumbnail($post->ID) && ($cycle_position == 'left'))
					{
						if (!$usebg){
						echo '<div class="grid_6 cycle_image  ' . $cycle_position . '"><div class="holder">';
						if ($link)
						{
							echo '<a href="' . $link . '" >';
						}
						if ($slider_frame){
							get_theme_post_thumbnail($post->ID, 'full');
						} else {
							get_theme_post_thumbnail($post->ID, 'cycle_side');
						}
						if ($link)
						{
							echo '</a>';
						}
						echo '</div></div>';
						} else {
						echo '<div class="grid_6" style="height:1px"></div>';
					}
					}
					?>

					<?php
					if ($cycle_position == 'full' && !$usebg)
					{

						echo '<div class="grid_12 cycle_image  ' . $cycle_position . '"><div class="holder">';
						if ($link)
						{
							echo '<a href="' . $link . '" >';
						}
						get_theme_post_thumbnail($post->ID, 'cycle_full');
						if ($link)
						{
							echo '</a>';
						}
						echo '</div></div>';
					}
					else
					{
						?>

						<div class="cycle_content cycle_col <?php echo ($cycle_position == 'empty' ) ? 'grid_12' : 'grid_6';  echo ' '.$content_align;?>">

								<?php
								if (!$slider_title)
								{
									?>
								<h3 class="entry-title">
				<?php the_title(); ?>
								</h3>
						<?php } ?>
							<div class="entry-content">
						<?php
						global $more;
						$more = 1;
						the_content();


						if ($btntxt && $link)
						{
							echo '<a href="' . $link . '" class="cycle_btn"><span>' . $btntxt . '</span></a>';
						}
						?>
							</div>


						</div>
				<?php } ?>
				<?php
				if (has_post_thumbnail($post->ID) && ($cycle_position == 'right'))
				{
					if (!$usebg) {
						echo '<div class="grid_6 cycle_image  ' . $cycle_position . '"><div class="holder">';
						if ($link)
						{
							echo '<a href="' . $link . '" >';
						}
						if ($slider_frame){
							get_theme_post_thumbnail($post->ID, 'full');
						} else {
							get_theme_post_thumbnail($post->ID, 'cycle_side');
						}
						if ($link)
						{
							echo '</a>';
						}
						echo '</div></div>';
					} else {
						echo '<div class="grid_6" style="height:1px"></div>';
					}
				} 
				?>
					</div>
					</div>
				</div>
	<?php
	endwhile;
	wp_reset_postdata();
	?>
					
		</div>

	<div id="navcycle"><span></span></div>

<?php endif; ?>