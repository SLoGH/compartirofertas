<?php
// is_tax is_tag is_category => get_tax_meta 
$usemap = null;
if(is_tax() || is_tag() || is_category())
{
	global $wp_query;
	$term = $wp_query->get_queried_object();
	$slider = (get_tax_meta($term->term_id, SHORTNAME . "_tax_slider", true)) ? get_tax_meta($term->term_id, SHORTNAME . "_tax_slider", true) : get_option(SHORTNAME . Admin_Theme_Item_Slideshow::TYPE);
}
elseif (is_home() && !get_post_meta(get_option("page_for_posts"), SHORTNAME . "_post_slider", true))
{
	$slider = get_option(SHORTNAME . Admin_Theme_Item_Slideshow::TYPE);
}
elseif(is_home() && get_post_meta(get_option("page_for_posts"), SHORTNAME . "_post_slider", true))
{
	$slider = get_post_meta(get_option("page_for_posts"), SHORTNAME . "_post_slider", true);
}
elseif (is_page() || (is_single() && $post->post_type == 'post') || (is_single() && $post->post_type == Custom_Posts_Type_Gallery::POST_TYPE) || (is_single() && $post->post_type == Custom_Posts_Type_Testimonial::POST_TYPE)) {
	
	// Slideshow
	$pid = (isset($post->ID)) ? $post->ID : NULL;
	$slider = (get_post_meta($pid, SHORTNAME . "_post_slider", true)) ?get_post_meta($pid, SHORTNAME . "_post_slider", true): get_option(SHORTNAME . Admin_Theme_Item_Slideshow::TYPE);
}
else
{
	$slider = '';
}

/**
 * If map was added to this post, 
 * forced off slideshow!
 */
if(is_singular() && isset($post->ID) && get_post_meta($post->ID, Locate_Api_Map::getMetaKey(), true))
{
	$slider = 'Disable';
	$usemap = 'use';
}


if($slider !== false && $slider !== '')	
{
	switch ($slider)
	{
		case "jCycle":
		{
			locate_template(array('cycle.php'), true, true);
			break;
		}
		case "Disable":
		{
			break;
		}
		default:
		{
			if ($slider = get_option(SHORTNAME . Admin_Theme_Item_Slideshow::TYPE))
			{
				switch ($slider)
				{
					case "jCycle":
					{
							locate_template(array('cycle.php'), true, true);
							break;
					}
				}
			}
		}
	}
}

// Title	
if ((!is_front_page() && (!$slider || $slider == 'Disable') && !is_single() && !$usemap) || get_query_var('pagename') == 'customeventslist')
{?>
	<div id="pagetitle" class="clearfix row">
		<div class="container_12">
			<div class="<?php
				if (is_singular())
				{
					echo (get_post_meta(get_the_ID(), SHORTNAME . '_page_extratitle', true)) ? "grid_7" : "grid_12";
				}
				else
				{

					if (get_option("show_on_front") == 'page' && is_home())
					{
						$blogpage = get_option("page_for_posts");
						echo (get_post_meta($blogpage, SHORTNAME . '_page_extratitle', true)) ? "grid_7" : "grid_12";
					}
					else
					{
						echo "grid_12";
					}
				}
				?>">
				<h1>
					<?php
					if (is_day())
					{
						printf(__('Daily Archives: <span>%s</span>', 'churchope'), get_the_date());
					}
					elseif (is_month())
					{
						printf(__('Monthly Archives: <span>%s</span>', 'churchope'), get_the_date('F Y'));
					}
					elseif (is_year())
					{
						printf(__('Yearly Archives: <span>%s</span>', 'churchope'), get_the_date('Y'));
					}
					elseif (is_tag())
					{
						echo single_tag_title("", false);
					}
					elseif (is_category())
					{
						echo single_cat_title("", false);
					}
					elseif (is_404() && get_query_var('pagename') !== 'customeventslist')
					{
						_e('404 - Oops!', 'churchope');
					}
					elseif (is_search())
					{
						_e( 'Results for: ', 'churchope' ); the_search_query();;
					}
					elseif (is_tax())
					{
						global $wp_query;
						$term = $wp_query->get_queried_object();
						echo $term->name;
					}
					elseif (get_option("show_on_front") == 'page' && is_home())
					{
						echo get_the_title(get_option("page_for_posts"));
					}
					elseif (is_author())
					{
						if (have_posts()) :
							the_post();
							_e('Author Archives: ', 'churchope');
							the_author();
						else:
							_e('No posts for current author', 'churchope');
						endif;
					}
					elseif(get_query_var('pagename') == 'customeventslist')
					{
							$events_for  = date_i18n(get_option('date_format'), strtotime(get_query_var('event_month').'/'.get_query_var('event_day').'/'.get_query_var('event_year')));
							if($events_for && strlen($events_for))
							{
								printf(__('Events for %s', 'churchope'), $events_for);
							}
					}
					else
					{
						the_title();
					}
					?>	
				</h1>
			</div>
			<?php
			if (is_singular() || (get_option("show_on_front") == 'page' && is_home()))
			{
				$curid = (get_option("show_on_front") == 'page' && is_home()) ? get_option("page_for_posts") : get_the_ID();
				if (get_post_meta($curid, SHORTNAME . '_page_extratitle', true))
				{?>
					<div class="grid_5 extratitle">
						<?php echo stripslashes(get_post_meta($curid, SHORTNAME . '_page_extratitle', true)); ?>
					</div><?php
				}
			}?>
		</div>
	</div>
	
<?php }?>

<?php if((!$slider || $slider == 'Disable') && (is_single() && $post->post_type == 'post') && get_option('show_on_front') == 'page'):
	$blogpage = get_option('page_for_posts'); 
	$additional_text = (get_post_meta(get_the_ID(), SHORTNAME . '_page_extratitle', true)) ? get_post_meta(get_the_ID(), SHORTNAME . '_page_extratitle', true) : get_post_meta($blogpage, SHORTNAME . '_page_extratitle', true) ;
?>
	<div id="pagetitle" class="clearfix row">
		<div class="container_12">
			<div class="<?php echo (get_post_meta($blogpage, SHORTNAME . '_page_extratitle', true)) ? "grid_7" : "grid_12" ?>">
				<h1><?php echo get_the_title($blogpage); ?></h1>
			</div>
				<?php if ($additional_text != NULL)
				{ ?>
				<div class="grid_5 extratitle">
					<?php echo $additional_text; ?>
				</div>
				<?php } ?>
		</div>
	</div>
<?php endif;?>

<?php if (is_single() && $post->post_type == Custom_Posts_Type_Gallery::POST_TYPE && (!$slider || $slider == 'Disable')):	 ?>
	<div id="pagetitle" class="clearfix row">
		<div class="container_12">
			<div class="<?php echo (get_post_meta(get_the_ID(), SHORTNAME . '_page_extratitle', true)) ? "grid_7" : "grid_12" ?>">
				<h1><?php echo get_the_title(get_the_ID()); ?></h1>
			</div>
				<?php if (get_post_meta(get_the_ID(), SHORTNAME . '_page_extratitle', true) != NULL)
				{ ?>
				<div class="grid_5 extratitle">
					<?php echo stripslashes(get_post_meta(get_the_ID(), SHORTNAME . '_page_extratitle', true)); ?>
				</div>
				<?php } ?>
		</div>
	</div>
<?php endif;?>
	
<?php if ((!$slider || $slider == 'Disable') && get_query_var('pagename') != 'customeventslist'  && !$usemap) { ?>
<div id="breadcrumbs" class="clearfix">
		<?php
		if (function_exists('yoast_breadcrumb') && !is_front_page())
		{
			yoast_breadcrumb('<div class="row"><div class="grid_12">', '</div></div>');
		}
		?>
</div>
<?php }?>
	<?php
	if (is_singular() && isset($post->ID) && get_post_meta($post->ID, Locate_Api_Map::getMetaKey(), true))
	{
		$map = apply_filters('the_event_map', null);
		echo do_shortcode($map);
	}
?>