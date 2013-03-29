<?php

define('SHORTCODE_URL', get_template_directory_uri().'/lib/shortcode/');
add_filter('mce_external_plugins', "th_ed_register");
add_filter('mce_buttons_3', 'th_ed_add_buttons', 0);
add_filter('widget_text', 'do_shortcode');

locate_template( array('/lib/shortcode/contact-form.php'), true, true	);
locate_template( array('/lib/shortcode/eventsCalendar.php'), true, true	);


function th_ed_add_buttons($buttons)
{
	array_push($buttons, "highlight", "notifications", "buttons", "divider", "toggle", "tabs", "contactForm", "price_table_group", 'social_link', 'social_button', 'teaser', 'testimonials','dropcaps','totop','toc');
	
	switch(get_post_type())
	{
		case 'page':
			array_push($buttons, "blog", "terms_gallery","event", "upcoming");
			break;
		case Custom_Posts_Type_Slideshow::POST_TYPE:
			array_push($buttons, 'thvideo');
			break;
	}
	array_push($buttons, 'columns');
	return $buttons;
	
	
}

function th_ed_register($plugin_array)
{
	$url = get_template_directory_uri() . "/lib/shortcode/shortcodes.js";

	$plugin_array["th_buttons"] = $url;
	return $plugin_array;
}



//raw

function my_formatter($content)
{
	$new_content = '';
	$pattern_full = '{(\[raw\].*?\[/raw\])}is';
	$pattern_contents = '{\[raw\](.*?)\[/raw\]}is';
	$pieces = preg_split($pattern_full, $content, -1, PREG_SPLIT_DELIM_CAPTURE);

	foreach ($pieces as $piece)
	{
		if (preg_match($pattern_contents, $piece, $matches))
		{
			$new_content .= $matches[1];
		}
		else
		{
			$new_content .= wptexturize(wpautop($piece));
		}
	}

	return $new_content;
}

remove_filter('the_content', 'wpautop');
remove_filter('the_content', 'wptexturize');

add_filter('the_content', 'my_formatter', 99);

//Columns
function col_one_half($atts, $content = null)
{
	extract(shortcode_atts(array(
				'last' => ''
					), $atts));

	$out = "<div class='one_half " . $last . "' >" . do_shortcode($content) . "</div>";

	return $out;
}

add_shortcode('one_half', 'col_one_half');

function col_one_third($atts, $content = null)
{
	extract(shortcode_atts(array(
				'last' => ''
					), $atts));

	$out = "<div class='one_third " . $last . "' >" . do_shortcode($content) . "</div>";

	return $out;
}

function th_price_table_group($atts, $content = null)
{
	return '<div class="offer_group clearfix">'.  do_shortcode($content).'</div>';
}
add_shortcode('price_table_group', 'th_price_table_group');


function th_price_table($atts, $content = null)
{
	extract(shortcode_atts(array(
				'title'			=> '',
				'price'			=> '',
				'button_text'	=> '',
				'button_url'	=> '#',	
				'button_color'	=> '',
					), $atts));
	$html = '';
	$style = '';
	if($title || $price || $content || $button_text || $button_color)
	{
		$html = '<div class="offer"><div class="inner_offer">';
		if($title)
		{
			$html .= '<span class="title">'.$title.'</span>';
		}
		if($price)
		{
			$html .= '<span class="price">'.$price.'</span>';
		}
		if($content)
		{
			$html .= '<span class="offer_content">'.$content.'</span>';
		}
		if($button_text)
		{
			if($button_color)
			{
				$style = 'style="background-color:'.$button_color.'"';
			}
			
			$html .= '<a class="churchope_button" rel="nofollow" '.$style.' title="'.$button_text.'" href="'.$button_url.'">';
			$html .=$button_text;
			$html .='</a>';
		}
		$html .= '</div></div>';
	}
	return $html;	
}

add_shortcode('price_table', 'th_price_table');

add_shortcode('one_third', 'col_one_third');

function col_one_fourth($atts, $content = null)
{
	extract(shortcode_atts(array(
				'last' => ''
					), $atts));

	$out = "<div class='one_fourth " . $last . "' >" . do_shortcode($content) . "</div>";

	return $out;
}

add_shortcode('one_fourth', 'col_one_fourth');

function col_two_third($atts, $content = null)
{
	extract(shortcode_atts(array(
				'last' => ''
					), $atts));

	$out = "<div class='two_third " . $last . "' >" . do_shortcode($content) . "</div>";

	return $out;
}

add_shortcode('two_third', 'col_two_third');

function col_three_fourth($atts, $content = null)
{
	extract(shortcode_atts(array(
				'last' => ''
					), $atts));

	$out = "<div class='three_fourth " . $last . "' >" . do_shortcode($content) . "</div>";

	return $out;
}

add_shortcode('three_fourth', 'col_three_fourth');

function col_clear($atts, $content = null)
{

	return "<div class='clearfix'></div>";
}

add_shortcode('clear', 'col_clear');

///Highlight
function highlight($atts, $content = null)
{
	extract(shortcode_atts(array(
					), $atts));

	$out = "<span class='hdark' >" . do_shortcode($content) . "</span>";

	return $out;
}

add_shortcode('highlight', 'highlight');

///Dropcaps
function dropcaps($atts, $content = null)
{
	extract(shortcode_atts(array(
					), $atts));

	$out = "<span class='dropcaps' >" . do_shortcode($content) . "</span>";

	return $out;
}

add_shortcode('dropcaps', 'dropcaps');

///Dropcaps
function totop($atts, $content = null)
{
	extract(shortcode_atts(array(
					), $atts));

	$out = "<a href='#main' class='th_totop' >" . do_shortcode($content) . "</a>";

	return $out;
}

add_shortcode('totop', 'totop');

///Buttons
function button($atts, $content = null)
{
	extract(shortcode_atts(array(
				'type' => '',
				'url' => '',
				'target' => ''
					), $atts));
	if ($target != '') : $target = "target='_blank'";
	endif;
	$out = "<a class='th_button " . $type . "' href='" . $url . "' " . $target . "  ><span>" . do_shortcode($content) . "</span></a>";

	return $out;
}

add_shortcode('button', 'button');

///Table of Contents
function toc($atts, $content = null)
{
	extract(shortcode_atts(array(
				'title' => '',
				'hide' => '',
				'show' => ''
					), $atts));
	
	$out = '<div class="toc"><h4 class="toc_title">'.$title.'<a href="#" data-hide="'.$hide.'" data-show="'.$show.'" class="toc_hide">['.$hide.']</a></h4><div class="toc_content">' . do_shortcode($content). '</div></div>';

	return $out;
}

add_shortcode('toc', 'toc');

// Blog
function blog_shortcode($atts, $content = null)
{
	$out = '';
	// get the current page
	if (is_front_page())
	{
		$current_page = (get_query_var('page')) ? get_query_var('page') : 1;
	}
	else
	{
		$current_page = (get_query_var('paged')) ? get_query_var('paged') : 1;
	}

	extract(shortcode_atts(array(
				'category' => '',
				'perpage' => '1',
				'pagination' => ''
					), $atts));

	$args = array('posts_per_page'	=> $perpage,
				'post_status'		=> 'publish',
				'cat'				=> $category,
				'post_type'			=> 'post',
				'paged'				=> $current_page,
				'ignore_sticky_posts' => true,
				'order'				=> 'DESC',
		);
	$post_list = new WP_Query($args);

	ob_start();
	if ($post_list && $post_list->have_posts()) : ?>
		
			<?php   while ($post_list->have_posts()) : $post_list->the_post();?>
				<article <?php post_class('posts_listing blog_shortcode clearfix') ?> id="post-<?php the_ID(); ?>">

				<?php
				if (has_post_thumbnail())
				{
					?>		
					<a href="<?php the_permalink() ?>" title="<?php echo the_title(); ?>" class="imgborder clearfix thumb"><?php get_theme_post_thumbnail(get_the_ID(), 'blog_shortcode'); ?></a>
				<?php } ?>

				
				
					<h2 class="entry-title"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php _e('Permanent Link to', 'churchope'); ?> <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
						


						

					
					<div class="entry-content">

							<?php
							
								the_excerpt();
							
							?>
						</div>
				</article>
			<?php endwhile;?>
		<?php
		
		$total = $post_list->max_num_pages;
		
		if ($pagination && $total > 1)
		{?>
			<div class="pagination clearfix">
				<?php
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
					'base'		=> get_pagenum_link(1) . '%_%',
					'format'	=> $format,
					'current'	=> $current_page,
					'total'		=> $total,
					'mid_size'	=> 10,
					'type'		=> 'list'
				));?>
			</div><?php
		}
	endif; 
	$out = ob_get_clean();
	
	wp_reset_postdata();
	
	return $out;
}

add_shortcode('blog', 'blog_shortcode');

function galleries_shortcode($atts, $content = nul)
{
	/**
	 * @todo Layout 3a4em oH Ham Hy}|{eH
	 */
	$out = '';
	// get the current page
	if (is_front_page())
	{
		$current_page = (get_query_var('page')) ? get_query_var('page') : 1;
	}
	else
	{
		$current_page = (get_query_var('paged')) ? get_query_var('paged') : 1;
	}

	extract(shortcode_atts(array(
				'terms'		=> '',
				'isotope'		=> '',
				'perpage'		=> '1',
				'pagination'	=> '',
				'layout'		=> '',
					), $atts));
					if ($isotope) {$perpage = -1;}
	$args = array('posts_per_page'	=> $perpage,
				'post_status'		=> 'publish',
				
				'post_type'			=> Custom_Posts_Type_Gallery::POST_TYPE,
				'paged'				=> $current_page,
				'ignore_sticky_posts' => true,
				'order'				=> 'DESC',
				'tax_query' => array(
						array(
							'taxonomy' => Custom_Posts_Type_Gallery::TAXONOMY,
							'field' => 'id',
							'terms'	=> explode(',', $terms),
						)
	)
		);
	
	$post_list = new WP_Query($args);

	ob_start();
	if ($post_list && $post_list->have_posts()) : ?>

<?php if ($isotope) {
	wp_enqueue_script('isotope');
	?>
<div class="clearfix filters">
<?php 

$term = get_term_by('id', $terms, Custom_Posts_Type_Gallery::TAXONOMY);
$parent = $term->term_id;


$args = array(
  'taxonomy'     => Custom_Posts_Type_Gallery::TAXONOMY,
  'child_of'      => $parent,
  'title_li'     => '',
  'show_option_none' 		=> '',
  'hierarchical'       => false,
  'hide_empty' => 1
);
?>
  <ul>	  
	  <li class="*"><a href="#" class="selected"><?php _e('All', 'churchope') ?></a></li>
    <?php echo wp_list_categories( $args ); ?>
  </ul>
</div>
<?php } ?>

<div class="row">
	<div class="gallery_wrap">
			<?php   while ($post_list->have_posts()) : $post_list->the_post();?>
<?php
$disable_thumb = (get_post_meta(get_the_ID(), SHORTNAME . '_gallery_hide_thumb', true)) ? get_post_meta(get_the_ID(), SHORTNAME . '_gallery_hide_thumb', true) : null;
		$icon = (get_post_meta(get_the_ID(), SHORTNAME . '_gallery_icon', true)) ? get_post_meta(get_the_ID(), SHORTNAME . '_gallery_icon', true) : get_option(SHORTNAME . Admin_Theme_Item_Galleries::CUSTOM_GALLERY_ICONS . '_active', '');
		$live_url = (get_post_meta(get_the_ID(), SHORTNAME . '_gallery_url', true)) ? get_post_meta(get_the_ID(), SHORTNAME . '_gallery_url', true) : null;
		$live_button = (get_post_meta(get_the_ID(), SHORTNAME . '_gallery_url_button', true)) ? get_post_meta(get_the_ID(), SHORTNAME . '_gallery_url_button', true) : __('Launch project', 'churchope');
		$preview_url = (get_post_meta(get_the_ID(), SHORTNAME . '_url_lightbox', true)) ? get_post_meta(get_the_ID(), SHORTNAME . '_url_lightbox', true) : null;
		$use_lightbox = (get_post_meta(get_the_ID(), SHORTNAME . '_use_lightbox', true)) ? get_post_meta(get_the_ID(), SHORTNAME . '_use_lightbox', true) : null;
		$hide_more = (get_post_meta(get_the_ID(), SHORTNAME . '_gallery_hide_more', true)) ? get_post_meta(get_the_ID(), SHORTNAME . '_gallery_hide_more', true) : null;
		$live_target = (get_post_meta(get_the_ID(), SHORTNAME . '_gallery_target', true)) ? get_post_meta(get_the_ID(), SHORTNAME . '_gallery_target', true) : null;
		$ext = null;
		$term_id = NULL;
		$terms = wp_get_post_terms(get_the_ID(), Custom_Posts_Type_Gallery::TAXONOMY);
		foreach ($terms as $termid) {  
			$term_id =  $term_id.' cat-item-'.$termid->term_id;  
		} 
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


			global $post_layout;
			
			
			$layout_page = ($post_layout == 'layout_none_sidebar') ? 'grid_12' : 'grid_8'; 

			switch  ($layout)
			{
				
				case 'medium':
					global $post_layout;
					 $num =  ($post_layout == 'layout_none_sidebar') ? '3' : '2'; 
					  
					 $linebreak = (($post_list->current_post) % $num == 0  )? 'clearboth':'';
					?>
					<article <?php post_class($term_id.' gallery_listing  small grid_4 '.$linebreak) ?> id="post-<?php the_ID(); ?>">
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
							} ?>" <?php echo ($use_lightbox) ? 'data-pp="lightbox[]"' : ''; ?>  title="<?php echo the_title(); ?>" class="imgborder thumb  <?php echo $ext; ?>"><?php get_theme_post_thumbnail(get_the_ID(), 'gallery_small'); ?></a>	
				<?php } ?>		   
					<div class="postcontent  clearfix">   
						<h2 class="entry-title"><?php if ($icon)
				{ ?><img src="<?php echo $icon ?>" alt="<?php the_title() ?>"  ><?php } ?><a href="<?php the_permalink(); ?>" ><?php the_title(); ?></a></h2>
						<div class="entry-content">
				<?php the_excerpt(); ?>
						</div>
								
					</div>			
				</article>
				<?php	break;
				
				
				case 'small':
					global $post_layout;
					 $num =  ($post_layout == 'layout_none_sidebar') ? '3' : '2'; 
					  
					 $linebreak = (($post_list->current_post+1) % $num == 0  )? 'clearfix':'';
					?>
					<?php if (has_post_thumbnail())
									{ ?>
					<article <?php post_class($term_id.' gallery_listing  small grid_4 '.$linebreak) ?> id="post-<?php the_ID(); ?>">
								
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
							} ?>" <?php echo ($use_lightbox) ? 'data-pp="lightbox[]"' : ''; ?>  title="<?php echo the_title(); ?>" class="imgborder thumb 	<?php echo $ext; ?>"><?php get_theme_post_thumbnail(get_the_ID(), 'gallery_small'); ?></a>
						   
							
				</article>
				<?php } ?>
				<?php	break;
				
				
				default:
				?>

				<article <?php post_class($term_id.' gallery_listing '.$layout_page) ?> id="post-<?php the_ID(); ?>">
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
				<?php
				
				excerpt(240); ?>
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

				<?php 
				break;
				 ?>
					
				<?php } ?>

			<?php endwhile;?>
	</div>
		<?php
		
		$total = $post_list->max_num_pages;
		
		if ($pagination && !$isotope && $total > 1)
		{?>
			<div class="pagination clearfix">
				<?php
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
					'base'		=> get_pagenum_link(1) . '%_%',
					'format'	=> $format,
					'current'	=> $current_page,
					'total'		=> $total,
					'mid_size'	=> 10,
					'type'		=> 'list'
				));?>
			
				</div><?php
		} ?>
	</div>
<?php
	endif; 
	$out = ob_get_clean();
	
	wp_reset_postdata();
	
	return $out;
	
	
}
function contactForm($atts, $content = null)
{
	return '';
}
add_shortcode('contactForm', 'contactForm');

add_shortcode('terms_gallery', 'galleries_shortcode');
///Notifications
function notification($atts, $content = null)
{
	extract(shortcode_atts(array(
				'type' => '',
					), $atts));

	$out = "<div class='th_notification " . $type . "' >" . do_shortcode($content) . "</div>";

	return $out;
}

add_shortcode('notification', 'notification');

//Toggles
function toggle_shortcode($atts, $content = null)
{
	wp_enqueue_script('jquery-ui-core');
	
	extract(shortcode_atts(
					array(
				'title' => '',
				'type' => '',
				'active' =>''
					), $atts));
	return '<div class="toggle toggle-' . $type . '"><h4 class="trigger '.$active.'"><span class="t_ico"></span><a href="#">' . $title . '</a></h4><div class="toggle_container '.$active.'">' . do_shortcode($content) . '</div></div>';
	
}

add_shortcode('toggle', 'toggle_shortcode');
///Tabs
add_shortcode('tabgroup', 'jquery_tab_group');

function jquery_tab_group($atts, $content)
{
	wp_enqueue_script('jquery-ui-tabs');

	extract(shortcode_atts(array(
				'type' => ''
					), $atts));

	$GLOBALS['tab_count'] = 0;

	do_shortcode($content);

	if (is_array($GLOBALS['tabs']))
	{
		$int = '1';
		foreach ($GLOBALS['tabs'] as $tab)
		{
			$tabs[] = '

  <li><a href="#tabs-' . $int . '">' . $tab['title'] . '</a></li>

';
			$panes[] = '
<div id="tabs-' . $int . '">
' . $tab['content'] . '

</div>
';
			$int++;
		}
		$return = "\n" . '
<div class="tabgroup ' . $type . '">
<ul class="tabs">' . implode("\n", $tabs) . '</ul>
' . "\n" . ' ' . implode("\n", $panes) . '

</div>
' . "\n";
	}
	return $return;
}

add_shortcode('tab', 'jquery_tab');

function jquery_tab($atts, $content)
{
	extract(shortcode_atts(array(
				'title' => 'Tab %d'
					), $atts));

	$x = $GLOBALS['tab_count'];
	$GLOBALS['tabs'][$x] = array('title' => sprintf($title, $GLOBALS['tab_count']), 'content' => do_shortcode($content));

	$GLOBALS['tab_count']++;
}

///
function refresh_mce($ver)
{
	$ver += 3;
	return $ver;
}

add_filter('tiny_mce_version', 'refresh_mce');

function html_editor()
{

	if (basename($_SERVER['SCRIPT_FILENAME']) == 'post-new.php' || basename($_SERVER['SCRIPT_FILENAME']) == 'post.php')
	{

		echo "<style type='text/css'>#ed_toolbar input#one_half, #ed_toolbar input#one_third, #ed_toolbar input#one_fourth, #ed_toolbar input#two_third, #ed_toolbar input#one_half_last, #ed_toolbar input#one_third_last, #ed_toolbar input#one_fourth_last, #ed_toolbar input#two_third_last, #ed_toolbar input#clear {font-weight:700;color:#2EA2C8;text-shadow:1px 1px white}
#ed_toolbar input#one_half_last, #ed_toolbar input#one_third_last, #ed_toolbar input#one_fourth_last, #ed_toolbar input#two_third_last, #ed_toolbar input#three_fourth, #ed_toolbar input#three+fourth_last {color:#888;text-shadow:1px 1px white}
#ed_toolbar input#raw {color:red;text-shadow:1px 1px white;font-weight:700;}</style>";
	}
}

add_action('admin_head', 'html_editor');

function custom_quicktags()
{
	if (basename($_SERVER['SCRIPT_FILENAME']) == 'post-new.php' || basename($_SERVER['SCRIPT_FILENAME']) == 'post.php')
	{
		wp_enqueue_script('custom_quicktags', get_template_directory_uri() . '/lib/shortcode/shortcodes/quicktags.js', array('quicktags'), '1.0.0');
	}
}

add_action('admin_print_scripts', 'custom_quicktags');

function th_gallery_shortcode($attr) {
  global $post, $wp_locale;

  static $instance = 0;
  $instance++;

 
  $output = apply_filters('post_gallery', '', $attr);
  if ( $output != '' )
    return $output;

 
  if ( isset( $attr['orderby'] ) ) {
    $attr['orderby'] = sanitize_sql_orderby( $attr['orderby'] );
    if ( !$attr['orderby'] )
      unset( $attr['orderby'] );
  }

  extract(shortcode_atts(array(
    'order'      => 'ASC',
    'orderby'    => 'menu_order ID',
    'id'         => $post->ID,
    'icontag'    => 'figure',
    'captiontag' => 'figcaption',
    'columns'    => 3,
    'size'       => 'thumbnail',
    'include'    => '',
    'exclude'    => ''
  ), $attr));

  $id = intval($id);
  if ( 'RAND' == $order )
    $orderby = 'none';

  if ( !empty($include) ) {
    $include = preg_replace( '/[^0-9,]+/', '', $include );
    $_attachments = get_posts( array('include' => $include, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );

    $attachments = array();
    foreach ( $_attachments as $key => $val ) {
      $attachments[$val->ID] = $_attachments[$key];
    }
  } elseif ( !empty($exclude) ) {
    $exclude = preg_replace( '/[^0-9,]+/', '', $exclude );
    $attachments = get_children( array('post_parent' => $id, 'exclude' => $exclude, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );
  } else {
    $attachments = get_children( array('post_parent' => $id, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );
  }

  if ( empty($attachments) )
    return '';

  if ( is_feed() ) {
    $output = "\n";
    foreach ( $attachments as $att_id => $attachment )
      $output .= wp_get_attachment_link($att_id, $size, true) . "\n";
    return $output;
  }

  $captiontag = tag_escape($captiontag);
  $columns = intval($columns);
  $itemwidth = $columns > 0 ? floor(100/$columns) : 100;
  $float = is_rtl() ? 'right' : 'left';

  $selector = "gallery-{$instance}";

  $gallery_style = $gallery_div = '';
  if ( apply_filters( 'use_default_gallery_style', true ) )
    $gallery_style = "";
  $size_class = sanitize_html_class( $size );
  $gallery_div = "<section id='$selector' class='clearfix gallery galleryid-{$id} gallery-columns-{$columns} gallery-size-{$size_class}'>";
  $output = apply_filters( 'gallery_style', $gallery_style . "\n\t\t" . $gallery_div );

  $i = 0;
  foreach ( $attachments as $id => $attachment ) {
   
    $link = isset($attr['link']) && $attr['link'] === 'attachment' ?
     str_replace('class="attachment-thumbnail"','class="attachment-thumbnail imgborder"', wp_get_attachment_link($id, $size, true, false))  :
     str_replace('class="attachment-thumbnail"','class="attachment-thumbnail imgborder"', wp_get_attachment_link($id, $size, false, false));
    $output .= "
      <{$icontag} class=\"gallery-item\">
        $link
      ";
    if ( $captiontag && trim($attachment->post_excerpt) ) {
      $output .= "
        <{$captiontag} class=\"gallery-caption\">
        " . wptexturize($attachment->post_excerpt) . "
        </{$captiontag}>";
    }
    $output .= "</{$icontag}>";
    if ( $columns > 0 && ++$i % $columns == 0 )
      $output .= '';
  }

  $output .= "</section>\n";

  return $output;
}

remove_shortcode('gallery');
add_shortcode('gallery', 'th_gallery_shortcode');


function th_social_link($atts, $content = null)
{
	extract(shortcode_atts(array(
				'url' => '#',
				'type' => '',
				'target'=>'',
					), $atts));
	
	if($target)
	{
		$target = 'target="_blank"';
	}
	/**
	 * @todo add  correct classes
	 */
	return '<a class="social_links '.$type.'" href="'.$url.'" '.$target.'>'.$type.'</a>';
	
	
}

add_shortcode('social_link', 'th_social_link');

/**
 * Insert social buttons(google+, facebook, twitter)
 */
function th_social_button($atts, $content = null)
{
	$default_values = array(
				'button'		=> '',
				'gurl'			=> in_the_loop()?get_permalink():'', // google
				'gsize'			=> '',
				'gannatation'	=> '',
				'ghtml5'		=> '',
				'turl'			=> in_the_loop()?get_permalink():'', //twitter
				'ttext'			=> in_the_loop()?get_the_title():'',
				'tcount'		=> '',
				'tsize'			=> '',
				'tvia'			=> '',
				'trelated'		=> '',
				'furl'			=> in_the_loop()?get_permalink():'', //facebook
				'flayout'		=> '',
				'fsend'			=> '',
				'fshow_faces'	=> '',
				'fwidth'		=> 450,
				'faction'		=> '',
				'fcolorsheme'	=> '',
				'purl'			=> in_the_loop()?get_permalink():'', // pinterest
				'pmedia'		=> wp_get_attachment_url(get_post_thumbnail_id()),
				'ptext'			=> in_the_loop()?get_the_title():'',
				'pcount'		=> '',
					);
	
	$shortcode_html = $shortcode_js = ''; 
	extract(shortcode_atts($default_values, $atts));
	
	switch($button) 
	{
		/**
		 * insert google+ button
		 */
		case 'google':
			$shortcode_js = "<script type='text/javascript'>(function() {var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true; po.src = 'https://apis.google.com/js/plusone.js'; var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);})();</script>";
			if($ghtml5)
			{
				$shortcode_html = sprintf('<div class="g-plusone" data-size="%s" data-annotation="%s" data-href="%s"></div>', $gsize, $gannatation, $gurl);
			}
			else
			{
				$shortcode_html= sprintf('<g:plusone size="%s" annotation="%s" href="%url"></g:plusone>', $gsize, $gannatation, $gurl);
			}
			break;
		/**
		 * Insert Twitter follow button
		 */
		case 'twitter':
			$shortcode_js = '<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>';
			$template = '<a href="https://twitter.com/share" class="twitter-share-button"  data-url="%s"	data-text="%s" data-count="%s" data-size="%s" data-via="%s" data-related="%s" data-lang="msa">Tweet</a>';
			$shortcode_html = sprintf($template,
					$turl,
					$ttext,
					$tcount,
					$tsize,
					$tvia,
					$trelated);
			break;
		/**
		 * Insert facebook button. 
		 */
		case 'facebook':
			$shortcode_js = <<<JS
					<div id="fb-root"></div>
				  <script>(function(d, s, id) {
					var js, fjs = d.getElementsByTagName(s)[0];
					if (d.getElementById(id)) return;
					js = d.createElement(s); js.id = id;
					js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";
					fjs.parentNode.insertBefore(js, fjs);
				  }(document, 'script', 'facebook-jssdk'));</script> 
JS;
			$template = <<<HTML
				<div class="fb-like" data-href="%s" data-send="%s" data-layout="%s" data-width="%d" data-show-faces="%s" data-action="%s" data-colorscheme="%s"></div>
HTML;
			$shortcode_html = sprintf($template,
					$furl,
					($fsend)?'true':'false',
					$flayout,
					$fwidth,
					($fshow_faces)?'true':'false',
					$faction,
					$fcolorsheme
					);
			break;
		case 'pinterest':
			$query_params = $template = '';
			$filtered_params = array();
			
			$params = array('url'			=>$purl,	
							'media'			=>$pmedia,
							'description'	=>$ptext);
			
			$filtered_params = array_filter($params);
			
			
			$query_params = http_build_query($filtered_params);
			
			if(strlen($query_params))
			{
				$query_params = '?'.$query_params;
			}
			
			$template = '<a href="http://pinterest.com/pin/create/button/%s" class="pin-it-button" count-layout="%s"><img border="0" src="//assets.pinterest.com/images/PinExt.png" title="Pin It" /></a>';
			
			$shortcode_html = sprintf($template, $query_params, $pcount);
			$shortcode_js = '<script type="text/javascript" src="//assets.pinterest.com/js/pinit.js"></script>';
			
			break;
	}
	return $shortcode_html.$shortcode_js;
}

add_shortcode('social_button', 'th_social_button');

add_shortcode('teaser', 'th_teaser');

function th_teaser($atts, $button_title = null)
{
	$target_html = '';
	extract(shortcode_atts(array(
				'url'	=> home_url(),
				'title'	=> get_bloginfo('name'),
				'src'	=> '',
				'post'	=> '',
				'target'=> '',
					), $atts));
	if($target)
	{
		$target_html = ' target="_blank"';
	}
	
	$html = '<a href="'.esc_url($url).'" class="teaser_box"'.$target_html.'>';

	if($src && strlen($src))
	{
		$html .= '<img src="'.esc_url($src).'" alt="'.esc_html($title).'">';
	}
	elseif($post)
	{
		ob_start();
		get_theme_post_thumbnail($post, 'teaser-thumbnail');
		$img = ob_get_clean();
		
		$html .= $img;
	}
	
	if($title)
	{
		$html .= '<span class="teaser_title"><span class="teaser_title_inner">'.$title.'</span>';
	}
	
	if($button_title)
	{
		$html .= '<span class="teaser_more">'.$button_title.'</span>';
	}
	
	$html .= '</span></a>';

	return $html;
}

function th_audio($atts, $title = null)
{
	if(!isset($GLOBALS['audio_iterator']))
	{
		$GLOBALS['audio_iterator'] = 1;
	}
	
	extract(shortcode_atts(array(
				'href' => '',
				'hide_title' => '',
					), $atts));
	
	if(filter_var($href, FILTER_VALIDATE_URL))
	{
			wp_enqueue_script('jplayer');
			
			switch(pathinfo($href, PATHINFO_EXTENSION))
			{
				case 'mp3':  //mp3
					$media = "{mp3: '$href'}";
					$supplied = 'supplied: "mp3",';
					break;
				case 'm4a':  //mp4
					$media = "{m4a: '$href'}";
					$supplied = 'supplied: "m4a, mp3",';
					break;
				case 'ogg': //ogg
					$media = "{oga: '$href'}";
					$supplied = 'supplied: "oga, ogg, mp3",';
					break;
				case 'oga': //oga
					$media = "{oga: '$href'}";
					$supplied = 'supplied: "oga, ogg, mp3",';
					break;
				case 'webma': //webma
					$media = "{webma: '$href'}";
					$supplied = 'supplied: "webma, mp3",';	
					break;
				case 'webm': //webma
					$media = "{webma: '$href'}";
					$supplied = 'supplied: "webma, mp3",';	
					break;
				case 'wav':
					$media = "{wav: '$href'}";
					$supplied = 'supplied: "wav, mp3",';	
					break;
				default:
					// not supporteg audio format
					return;
					break;
				

			}
			
			
			$html = <<<HTML
			<div id="jquery_jplayer_{$GLOBALS['audio_iterator']}" class="jp-jplayer"></div>
			<div id="jp_container_{$GLOBALS['audio_iterator']}" class="jp-audio">
            <div class="jp-type-single"><div class="jp-control"><a href="javascript:;" class="jp-play" tabindex="1">play</a><a href="javascript:;" class="jp-pause" tabindex="1">pause</a></div> <div class="jp-gui jp-interface"><div class="jp-progress"><div class="jp-seek-bar"><div class="jp-play-bar"></div></div></div><div class="jp-volume"><div class="jp-volume-bar"><div class="jp-volume-bar-value"></div></div></div>                         
                </div>
HTML;
			if (!$hide_title) {
			$html .= <<<HTML
                <div class="jp-title"><strong>{$title}</strong> -  <span class="jp-current-time"></span> / <span class="jp-duration"></span></div>
HTML;
			}
			$html .= <<<HTML
                <div class="jp-no-solution"><span>Update Required</span>To play the media you will need to either update your browser to a recent version or update your <a href="http://get.adobe.com/flashplayer/" target="_blank">Flash plugin</a>.</div></div></div>
		<script type='text/javascript'>
			jQuery(document).ready(function() {
				jQuery("#jquery_jplayer_{$GLOBALS['audio_iterator']}").jPlayer({
					ready: function(event) {
						jQuery(this).jPlayer("setMedia", {$media});
					},
					play: function() {
						jQuery(this).jPlayer("pauseOthers");
					},
					swfPath: THEME_URI+"/swf",
					solution: "html, flash",
					preload: "metadata",
					wmode: "window",
					{$supplied}
					cssSelectorAncestor: '#jp_container_{$GLOBALS['audio_iterator']}'
				});
			});
		</script>
HTML;
		$GLOBALS['audio_iterator'] = $GLOBALS['audio_iterator'] +1;
		return $html;	
	}
}
add_shortcode('thaudio', 'th_audio');


function upcoming_events($atts, $title = null)
{
	/**
	 * Shortcode based on Widget_Event_Upcoming
	 */
	$instance = shortcode_atts(array(
						Widget_Event_Upcoming::PLACE	=> '', // checkbox
						Widget_Event_Upcoming::PHONE	=> '', // checkbox
						Widget_Event_Upcoming::TIME		=> '', // checkbox
					
						Widget_Event_Upcoming::CATEGORY	=> Widget_Event_Upcoming::ALL, 
						Widget_Event_Upcoming::COUNT	=> 4 
					), $atts);
	
	$html = '<ul class="upcoming_events">';
	$eventObj = new Widget_Event_Upcoming();
	$upcoming_events = $eventObj->getNextEvents($instance);
	
	if($upcoming_events)
	{
				foreach ($upcoming_events as $event)
			{
				$url =  get_permalink($event->post_id);
				$html .= <<<HTML_ENTITIES
				<li>
				<p class="meta_date"><strong>{$event->day}</strong><a href="{$url}"></a><span>{$event->month}</span></p><a href="{$url}" class="entry-title">{$event->title}</a>	
HTML_ENTITIES;
				
				
				if($instance[Widget_Event_Upcoming::PLACE])
				{
					$html .= "<span>{$event->place}</span>";
				}
				if($instance[Widget_Event_Upcoming::PHONE])
				{
					$html .= "<span>{$event->phone}</span>";
				}	
				if($instance[Widget_Event_Upcoming::TIME])
				{
					$html .= "<span>{$event->time}</span>";
				}
				
				$html .= "</li>";
			}
	}
	$html .= '</ul>';
	
	return $html;
}

add_shortcode('upcoming', 'upcoming_events');

function th_testimonial($atts)
{
	if(!isset($GLOBALS['shortcode_iterator']))
	{
		$GLOBALS['shortcode_iterator'] = 1;
	}
	
	extract(shortcode_atts(array(
				'category'	=> 'all',
				'time'		=>10,
				'effect'	=>'fade',
				'randomize'	=>'',
			), $atts));
	
	$query	= "post_type=".Custom_Posts_Type_Testimonial::POST_TYPE."&post_status=publish&posts_per_page=-1&order=DESC";
	if($category != 'all')
	{
		$query .="&".Custom_Posts_Type_Testimonial::TAXONOMY."=".$category;
	}
	$testimonials = new WP_Query($query);
	$have_posts = $testimonials->have_posts();
	ob_start();
		?><div id="shortcode_testimonial_<?php echo $GLOBALS['shortcode_iterator']?>" class='shortcode_testimonial'><?php
			
		if ($have_posts ) : ?>
			<?php if($testimonials->post_count > 1):?>
				<div class="controls">
					<a class="prev" href="#">Previous</a>
					<a class="next" href="#">Next</a>
				</div>
			<?php endif;?>
				<div class="jcycle">
					<?php  while($testimonials->have_posts()) : $testimonials->the_post();?>
					<div class="testimonial">
						<div class="quote">
						<?php echo the_content();?>
						</div>
						<div class="testimonial_meta">
							<span class="testimonial_author"><?php echo get_post_meta(get_the_ID(), SHORTNAME.'_testimonial_author', true);?></span>
							<span><?php echo get_post_meta(get_the_ID(), SHORTNAME.'_testimonial_author_job', true);?></span>
						</div>
					</div>
					<?php endwhile; ?>
				</div>
		<?php endif;
		if($testimonials->post_count < 2)
		{
			$randomize = false;
		}
		echo "</div>"; //<div id="shortcode_testimpnial_
		if($have_posts && $testimonials->post_count > 1)
		{
			Widget_Testimonial::printWidgetJS("shortcode_testimonial_{$GLOBALS['shortcode_iterator']}", $effect, $randomize, $time);
		}
		wp_reset_postdata();
	$GLOBALS['shortcode_iterator']++;
	$html = ob_get_clean();
	return $html;
}
add_shortcode('testimonials', 'th_testimonial');

function th_video($atts, $url = null)
{
	$html = '';
	$id = '';
	extract(shortcode_atts(array(
				'w'		=> 560,
				'h'		=> 315,
			), $atts));
	
	
	
	
	if($id = youtube_id_from_url($url))
	{
		wp_enqueue_script('swfobject');
		$html = <<<HTML
			<div id="id$id" style="width:{$w}px;height:{$h}px"><iframe width="{$w}" height="{$h}" src="http://www.youtube.com/embed/{$id}" frameborder="0" allowfullscreen></iframe></div>
			<script type="text/javascript">
					jQuery(document).ready(function() {
						var params = {allowfullscreen: 'true', allowscriptaccess: 'always', wmode: 'opaque'};
						var flashvars = {enablejsapi: '1', playerapiid: 'id$id'};
						swfobject.embedSWF("http://www.youtube.com/v/$id", "id$id", "$w", "$h", "9.0.0", false, flashvars, params);
					});
			</script>
HTML;
	}
	elseif($id = vimeo_id_from_url($url))
	{
		wp_enqueue_script('th_frogoloop');
		
		
		$html = <<<HTML
   [raw]
		 <iframe id="id$id" src="http://player.vimeo.com/video/$id?api=1&&player_id=id$id" width="$w" height="$h" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>
		<script type="text/javascript">
					jQuery(document).ready(function() {
							var iframe = jQuery('#jcyclemain #id$id').get(0);
HTML;
$html .= '						var player = $f(iframe);';
		$html .= <<<HTML
								player.addEvent('ready', function() {
									player.addEvent('play', function() {slideshowVideo.play();jQuery('#jcyclemain').cycle('pause');});
									player.addEvent('seek', function() {slideshowVideo.play();jQuery('#jcyclemain').cycle('pause');});
									player.addEvent('pause', function() {slideshowVideo.pause();if(!slider_pause){jQuery('#jcyclemain').cycle('resume')}});
									player.addEvent('finish', function() {slideshowVideo.pause();if(!slider_pause){jQuery('#jcyclemain').cycle('resume')}});
								});
						});
				</script>
		[/raw]
HTML;
	}
	
	return $html;
}
add_shortcode('thvideo', 'th_video');

function youtube_id_from_url($url) {
    $pattern = 
        '%^# Match any youtube URL
        (?:https?://)?  # Optional scheme. Either http or https
        (?:www\.)?      # Optional www subdomain
        (?:             # Group host alternatives
          youtu\.be/    # Either youtu.be,
        | youtube\.com  # or youtube.com
          (?:           # Group path alternatives
            /embed/     # Either /embed/
          | /v/         # or /v/
          | /watch\?v=  # or /watch\?v=
          )             # End path alternatives.
        )               # End host alternatives.
        ([\w-]{10,12})  # Allow 10-12 for 11 char youtube id.
        %x'
        ;
    $result = preg_match($pattern, $url, $matches);
    if (false !== $result && isset($matches[1])) {
        return $matches[1];
    }
    return false;
}
function vimeo_id_from_url($url)
{
	$result = preg_match('/(\d+)/', $url, $matches);
	if (false !== $result && isset($matches[1])) {
        return $matches[1];
    }
    return false;
}

function swf_header()
{
	
}


function inicio_shortcode($atts, $content = null) //mod by SLoGH
{
	$out = '';
	// get the current page
	if (is_front_page())
	{
		$current_page = (get_query_var('page')) ? get_query_var('page') : 1;
	}
	else
	{
		$current_page = (get_query_var('paged')) ? get_query_var('paged') : 1;
	}

	extract(shortcode_atts(array(
				'category' => '',
				'perpage' => '1',
				'pagination' => ''
					), $atts));

	$args = array('posts_per_page'	=> $perpage,
				'post_status'		=> 'publish',
				'cat'				=> $category,
				'post_type'			=> 'post',
				'paged'				=> $current_page,
				'ignore_sticky_posts' => true,
				'order'				=> 'DESC',
				'orderby' => 'meta_value', 
				'meta_key' => 'ratings_average' 
		);
			
	// Create a new filtering function that will add our where clause to the query
	function filter_where( $where = '' ) {
		// posts in the last 30 days
		$where .= " AND post_date > '" . date('Y-m-d', strtotime('-30 days')) . "'";
		return $where;
	}

	add_filter( 'posts_where', 'filter_where' );		
	$post_list = new WP_Query($args);
	remove_filter( 'posts_where', 'filter_where' );
	ob_start();
	if ($post_list && $post_list->have_posts()) : ?>		
			<?php
			$i = 1;
			while ($post_list->have_posts()) : $post_list->the_post(); 
				//$category = get_the_category();
				$price = get_post_meta(get_the_ID(), 'cf_user_submit_price', true); 
				$title = (strlen(the_title('','',false)) > 45) ? (substr(the_title('','',false),0,45)."...") : the_title('','',false); //mod by SLoGH
				add_image_size( 'homepage-thumb', 9999, 180, false); //mod by SLoGH
			?>
				<div class="one_third <?php if($i == 3){echo("last");} ?>">
				<article  id="post-<?php the_ID(); ?>" style="text-align:center;">
				<a href="<?php the_permalink() ?>" title="<?php echo the_title(); ?>" class="teaser_box">
					<?php echo (get_the_post_thumbnail(get_the_ID(), 'homepage-thumb')); ?>
					<span class="teaser_title"><span class="teaser_title_inner"><?php echo $title;  //mod by SLoGH ?></span>
					<span class="teaser_more"><?php //echo $category[0]->cat_name; 
					echo $price." €";
					?></span>
				</span></a>	
				<?php if(function_exists('the_ratings')) { the_ratings(); } ?>
				</article>
				</div>
				<?php if($i == 3){echo("<div style='clear: both;'></div> <hr>"); $i=0;} ?>
			<?php 
			$i++;
			endwhile;?>
		<?php
		
		$total = $post_list->max_num_pages;
		
		if ($pagination && $total > 1)
		{?>
			<div class="pagination clearfix">
				<?php
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
					'base'		=> get_pagenum_link(1) . '%_%',
					'format'	=> $format,
					'current'	=> $current_page,
					'total'		=> $total,
					'mid_size'	=> 10,
					'type'		=> 'list'
				));?>
			</div><?php
		}
	endif; 
	$out = ob_get_clean();
	
	wp_reset_postdata();
	
	return $out;
}

add_shortcode('inicio', 'inicio_shortcode');

function high_rate_shortcode()
{
	ob_start();
	if (function_exists('get_highest_rated')):
    echo"<ul>";
         get_highest_rated('post', 0, 10);
    echo"</ul>";
	endif; 
	echo paginate_links(array(
					'base'		=> get_pagenum_link(1) . '%_%',
					'format'	=> 'page/%#%/',
					'current'	=> 0,
					'total'		=> 2,
					'mid_size'	=> 2,
					'type'		=> 'list'
	));
	$out = ob_get_clean();
	return $out;
	
}

add_shortcode('high_rate', 'high_rate_shortcode');
?>