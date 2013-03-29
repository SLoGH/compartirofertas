<!doctype html>
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7" <?php language_attributes(); ?>> <![endif]-->
<!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8" <?php language_attributes(); ?>> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie9" <?php language_attributes(); ?>> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" <?php language_attributes(); ?>> <!--<![endif]-->
	<head>
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<?php if (!get_option(SHORTNAME . "_gfontdisable"))
		{
			?>
			<link href='//fonts.googleapis.com/css?family=<?php
		if (get_option(SHORTNAME . "_preview") != "")
		{
			if (isset($_SESSION[SHORTNAME . "_gfont"]))
			{
				$gfont = trim($_SESSION[SHORTNAME . "_gfont"]);
			}
			else
			{
				$gfont = "Open Sans";
			}
		}
		else
		{
			if (get_option(SHORTNAME . "_gfont") != '')
			{
				$gfont = get_option(SHORTNAME . "_gfont");
			}
			else
			{
				$gfont = "Open Sans";
			}
		} echo str_replace(" ", "+", $gfont);
		?>:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800' rel='stylesheet' type='text/css'> <?php } ?>
		<meta charset="<?php bloginfo('charset'); ?>">
		
		<meta name="viewport" content="width=device-width">
		<meta name="author" content="<?php echo home_url(); ?>">
		<title>
			<?php
			if(get_query_var('pagename') == 'customeventslist')
			{
				
				$events_for  = date_i18n(get_option('date_format'), strtotime(get_query_var('event_month').'/'.get_query_var('event_day').'/'.get_query_var('event_year')));
				if($events_for && strlen($events_for))
				{
					printf(__('Events for %s', 'churchope'), $events_for);	?> | <?php
				}
				bloginfo('name');
				
			}
			elseif (!defined('WPSEO_VERSION'))
			{
				// if there is no WordPress SEO plugin activated
				
				wp_title(' | ', true, 'right');
				bloginfo('name'); ?> | <?php
			bloginfo('description'); // or some WordPress default
		
			}	else {				
				wp_title();
			}		
			?>	
		</title>
		<link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> Feed" href="<?php echo home_url(); ?>/feed/">	
		<script> var THEME_URI = '<?php echo get_template_directory_uri(); ?>';</script>
		<?php 
		if (is_singular() && get_option('thread_comments'))
		{
			wp_enqueue_script('comment-reply');
		}
		wp_head();
		?>
		<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" media="screen" />
	</head>
	<?php
	global $post, $post_layout;
	if (!is_404() && !is_search()){
	$pid = get_the_ID();
	} else {
		$pid = null;
	}
	if (is_home() && get_option("page_for_posts"))
	{
		$pid = get_option("page_for_posts");
	}
	
	if (is_single() && $post->post_type == 'post')
	{
		$post_layout = (get_post_meta($pid, SHORTNAME . "_post_layout", true)) ? get_post_meta($pid, SHORTNAME . "_post_layout", true) : 'layout_' . get_option(SHORTNAME . '_post_layout') . '_sidebar';
	}
	elseif (is_single() && $post->post_type == Custom_Posts_Type_Event::POST_TYPE)
	{
		$post_layout = (get_post_meta($pid, SHORTNAME . "_post_layout", true)) ? get_post_meta($pid, SHORTNAME . "_post_layout", true) : 'layout_' . get_option(SHORTNAME . '_events_layout') . '_sidebar';
	}
	elseif (is_single() && $post->post_type == Custom_Posts_Type_Gallery::POST_TYPE)
	{
		$post_layout = (get_post_meta($pid, SHORTNAME . "_post_layout", true)) ? get_post_meta($pid, SHORTNAME . "_post_layout", true) : 'layout_' . get_option(SHORTNAME . '_gallery_layout') . '_sidebar';
	}
	elseif (is_single() && $post->post_type == Custom_Posts_Type_Testimonial::POST_TYPE)
	{
		$post_layout = (get_post_meta($pid, SHORTNAME . "_post_layout", true)) ? get_post_meta($pid, SHORTNAME . "_post_layout", true) : 'layout_' . get_option(SHORTNAME . '_testimonial_layout') . '_sidebar';
	}
	elseif (is_category() || is_tag())
	{
		global $wp_query;
		$term = $wp_query->get_queried_object();
		$post_layout = (get_tax_meta($term->term_id, SHORTNAME . "_post_listing_layout", true)) ? get_tax_meta($term->term_id, SHORTNAME . "_post_listing_layout", true) : 'layout_' . get_option(SHORTNAME . '_post_listing_layout') . '_sidebar';
	}
	elseif (is_tax(Custom_Posts_Type_Gallery::TAXONOMY))
	{
		global $wp_query;
		$term = $wp_query->get_queried_object();
		$post_layout = (get_tax_meta($term->term_id, SHORTNAME . "_post_listing_layout", true)) ? get_tax_meta($term->term_id, SHORTNAME . "_post_listing_layout", true) : 'layout_' . get_option(SHORTNAME . '_galleries_listing_layout') . '_sidebar';
	}
	elseif (is_tax(Custom_Posts_Type_Testimonial::TAXONOMY))
	{
		global $wp_query;
		$term = $wp_query->get_queried_object();
		$post_layout = (get_tax_meta($term->term_id, SHORTNAME . "_post_listing_layout", true)) ? get_tax_meta($term->term_id, SHORTNAME . "_post_listing_layout", true) : 'layout_' . get_option(SHORTNAME . '_testimonials_listing_layout') . '_sidebar';
	}
	elseif (is_tax(Custom_Posts_Type_Event::TAXONOMY))
	{
		global $wp_query;
		$term = $wp_query->get_queried_object();
		$post_layout = (get_tax_meta($term->term_id, SHORTNAME . "_post_listing_layout", true)) ? get_tax_meta($term->term_id, SHORTNAME . "_post_listing_layout", true) : 'layout_' . get_option(SHORTNAME . '_events_listing_layout') . '_sidebar';
	}
	
	elseif (is_home() || (is_404() && get_query_var('pagename') != 'customeventslist') || is_search() || is_date())
	{
		$post_layout = 'layout_' . get_option(SHORTNAME . '_post_listing_layout') . '_sidebar';
	}
	elseif (is_page())
	{
		if (get_post_meta($pid, "_wp_page_template", true) == 'template-leftsidebar.php') {
			 $post_layout = 'layout_left_sidebar';
		}
		elseif (get_post_meta($pid, "_wp_page_template", true) == 'template-rightsidebar.php') {
			 $post_layout = 'layout_right_sidebar';
		}
		else {
			$post_layout = 'layout_none_sidebar';
		}
		
	}
	elseif(get_query_var('pagename') == 'customeventslist')
	{
		$post_layout = 'layout_' . get_option(SHORTNAME . '_events_listing_layout') . '_sidebar';
	}
	else
	{
		$post_layout = 'layout_none_sidebar';
	}
	
	/**
	 * slideshow...
	 */
	if (!is_404() && !is_search() /*&& !is_archive() */){
		
		global $wp_query;
		$current_term = $wp_query->get_queried_object();
	
		if( (is_tax() || is_tag() || is_category())
			&& $current_term
			&& get_tax_meta($current_term->term_id, SHORTNAME . "_tax_slider", true) 
			&& (get_tax_meta($current_term->term_id, SHORTNAME . "_tax_slider", true) !== 'Disable'))
		{
			$slideshow = 'slideshow';
		}
		//post page
		elseif( !is_archive()
				&& get_post_meta($pid, SHORTNAME . "_post_slider", true) 
				&& (get_post_meta($pid, SHORTNAME . "_post_slider", true) !== 'Disable'))
		{
			$slideshow = 'slideshow';
		}
		elseif($current_term && (isset($current_term->term_id)  &&  get_tax_meta($current_term->term_id, SHORTNAME . "_tax_slider", true) == 'Disable')
				|| ($pid && get_post_meta($pid, SHORTNAME . "_post_slider", true) == 'Disable'))
		{
			$slideshow = '';
		}
		//global slideshow settings
		else
		{
			$slideshow = (get_option(SHORTNAME . "_global_slider") !== 'Disable') ? 'slideshow' : '';
		}
		
		$widget_title = (get_post_meta($pid, SHORTNAME . "_title_sidebar", true)) ? 'widget_title' : '';
	} else {
		$slideshow = '';
		$widget_title = '';
	}
	?>
	<body <?php body_class($post_layout . ' ' . $slideshow . ' ' . $widget_title); ?>>
		  <!--[if lt IE 7]><p class=chromeframe>Your browser is <em>ancient!</em> <a href="http://browsehappy.com/">Upgrade to a different browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">install Google Chrome Frame</a> to experience this site.</p><![endif]-->
		<header class="clearfix">
			<div class="header_bottom">
				<div class="header_top">
					<div class="row">
						<div class="logo grid_6">
								<?php if (is_front_page())
								{
									?><h1><?php } ?>
								<?php
								if (get_option(SHORTNAME . "_logo_txt"))
								{
									if (get_bloginfo('name'))
									{
										?><a href="<?php echo wpml_get_home_url() ?>"><span><?php bloginfo('name'); ?></span></a><?php
							}
						}
						else
						{
							?>
									<a href="<?php echo wpml_get_home_url() ?>"><img src="<?php echo get_option(SHORTNAME . "_logo_custom"); ?>" alt="<?php bloginfo('name'); ?>" /><span class="hidden"><?php bloginfo('name'); ?></span></a>
<?php } ?>
<?php if (is_front_page())
{
	?></h1><?php } ?>
						</div>
						<div class="grid_6">
<?php dynamic_sidebar("header") ?>
						</div>
						<?php if (get_option(SHORTNAME . "_ribbon")) { ?>
						<div class="ribbon_holder">
						<span class="ribbon_bg"></span>
						<a href="<?php echo get_option(SHORTNAME . "_ribbon") ?>" class="ribbon"><span>+</span></a>
						</div>
						<?php } ?>
					</div>        
				</div>
			</div>
		</header>

		<section id="color_header" class="clearfix">
			<div class="mainmenu <?php echo (get_option(SHORTNAME . "_menu_left"))?'menu_left':''; ?>"><div class="mainmenu_inner"><div class="row clearfix"><div class="grid_12">
<?php
wp_nav_menu(array('theme_location' => 'header-menu', 'container_class' => 'main_menu', 'menu_class' => 'sf-menu clearfix', 'fallback_cb' => '', 'container' => 'nav', 'link_before' => '', 'link_after' => '', 'walker' => new Walker_Nav_Menu_Sub()));
wp_nav_menu(array('theme_location' => 'header-menu', 'container_class' => 'main_menu_select', 'menu_class' => '', 'fallback_cb' => '', 'container' => 'nav', 'items_wrap' => '<select>%3$s</select>', 'walker' => new Walker_Nav_Menu_Dropdown()));
?>
						<div class="widget post widget_search grid_4" style="margin-top:20px;"><form role="search" method="get" id="searchform" action="http://compartirofertas.com/">
							<div><label class="screen-reader-text" for="s">Buscar por:</label>
							<input type="text" value="" name="s" id="s" placeholder="Buscar oferta...">
							<input type="submit" id="searchsubmit" value="Buscar">
							</div>
							</form></div>

						</div></div></div></div>	
					<?php get_template_part('title'); ?>
		</section>	
		<section class="gray_line clearfix" id="title_sidebar">	
			<div class="row"><div class="grid_12">
<?php
if (is_singular() || (get_option("show_on_front") == 'page' && is_home()))
{
	$curid = (get_option("show_on_front") == 'page' && is_home()) ? get_option("page_for_posts") : get_the_ID();

	if (get_post_meta($curid, SHORTNAME . '_title_sidebar', true))
	{
		$sidebar = get_post_meta($curid, SHORTNAME . '_title_sidebar', true);
		generated_dynamic_sidebar_th($sidebar);
	}
}
?>
				</div></div>
		</section>
		<div role="main" id="main">