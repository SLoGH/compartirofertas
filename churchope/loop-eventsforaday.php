<?php
/***
 * Showing events for a particular day
 */
$events_list = array();
$eventsObj			= new Widget_Event();						// Event Object
$calendar_events	= $eventsObj->getMonthEvents(get_query_var('event_month'), get_query_var('event_year'));// array of month days with events
$events_day			= (int) get_query_var('event_day');
$per_page			= (int)get_option('posts_per_page');
if (get_query_var('page'))
			{
				$current_page = get_query_var('page');
			}
			else
			{
				$current_page = 1;
			}
$start    = ($current_page-1)*$per_page;
$to     = $start+$per_page;

if(isset($calendar_events[$events_day]) &&  is_array($calendar_events[$events_day])&& count($calendar_events[$events_day]) )
{
	$events_list = $calendar_events[$events_day];
}

if ($events_list && count($events_list)) :
	for($i = $start;$i < $to; $i++):
		if(isset($events_list[$i])):
		$event = $events_list[$i];
//	foreach ($events_list as $event):
		$repeat = (get_post_meta($event->post_id, SHORTNAME . '_event_is_repeat', true)) ? get_post_meta($event->post_id, SHORTNAME . '_event_is_repeat', true) : null;
		$repeat_interval = (get_post_meta($event->post_id, SHORTNAME . '_event_interval', true)) ? get_post_meta($event->post_id, SHORTNAME . '_event_interval', true) : null;
		
		$event_date = (get_post_meta($event->post_id, SHORTNAME . '_event_date', true)) ? get_post_meta($event->post_id, SHORTNAME . '_event_date', true) : null;
		$event_time = (get_post_meta($event->post_id, SHORTNAME . '_event_time', true)) ? get_post_meta($event->post_id, SHORTNAME . '_event_time', true) : null;
		
		$event_address = (get_post_meta($event->post_id, SHORTNAME . '_event_address', true)) ? get_post_meta($event->post_id, SHORTNAME . '_event_address', true) : null;
		$event_phone = (get_post_meta($event->post_id, SHORTNAME . '_contact_phone', true)) ? get_post_meta($event->post_id, SHORTNAME . '_contact_phone', true) : null;

	

		$time = strtotime($event_date);
		$day = date_i18n('d', $time);
		$month = date_i18n('M', $time);

		if ($repeat_interval)
		{
			switch ($repeat_interval)
			{
				case 'day':
					$repeat_interval = __('Day', 'churchope'); //dayofweek
					break;

				case 'week':
					$repeat_interval = __('Week', 'churchope'); //dayofweek
					break;

				case 'month':
					$repeat_interval = __('Month', 'churchope'); //dayofweek	
					break;

				case 'year':
					$repeat_interval = __('Year', 'churchope'); //dayofweek	
					break;
			}
		}

			global $wp_query, $post_layout;
			;
		
			
			$layout = ($post_layout == 'layout_none_sidebar') ? 'grid_12' : 'grid_8';

			
				?>

				<article class="clearfix events" >
				<div class="postdate"><span></span><strong class="day"><?php echo ($repeat) ? '<img src="' . get_template_directory_uri() . '/images/i_repeat.png" alt="Repeat" >' : $day; ?></strong><strong class="month"><?php echo ($repeat) ? _e('Every ', 'churchope') . $repeat_interval : _e($month); ?></strong></div>

				<div class="content_wrap">
					<div class="post_title_area">
						<div class="blogtitles">
							<h2 class="entry-title"><span><a href="<?php echo get_permalink($event->post_id); ?>" ><?php echo get_the_title($event->post_id); ?></a></span></h2>
						</div>
					</div>	
					<?php if ($event_time || $event_address || $event_phone) { ?>
					<ul class="events_meta">
						<?php if ($event_time){ ?><li class="event_time"><?php echo $event_time; ?></li><?php } ?>
						<?php if ($event_address){  ?><li class="event_address"><?php echo $event_address; ?></li><?php } ?>
						<?php if ($event_phone){ ?><li class="event_phone"><?php echo $event_phone; ?></li><?php } ?>
					</ul>
					<?php } ?>
					<div class="entry-content">
						<?php echo get_post($event->post_id)->post_excerpt ?>
					</div>					

				</div>
			</article>
	<?php // endforeach; 
			endif;
		endfor;
		
	?>
<?php $total = (int)ceil(count($events_list)/$per_page); 

if ($total > 1) {

?>
		
<div class="pagination clearfix">
			<?php
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
				<?php _e('No Found Events For This Day', 'churchope'); ?>
			</h1>
			<p class="center">
				<?php _e('Sorry, but you are looking for something that isn\'t here.', 'churchope'); ?>
			</p>
		</article>
<?php endif; ?>
<?php wp_reset_query(); ?>