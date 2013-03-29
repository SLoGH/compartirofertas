<?php
if(!empty($_POST) && isset($_POST['calendar']) && $_POST['calendar'] == true)
{
	require_once('../../../../../wp-load.php');
	$layout		= isset($_POST['layout'])	? $_POST['layout']				: '';
	$category	= isset($_POST['category'])	? explode(',',$_POST['category']): array();
	$from		= isset($_POST['from'])		? $_POST['from']				: '';
	$to			= isset($_POST['to'])		? $_POST['to']					: '';
	$month		= isset($_POST['month'])	? $_POST['month']				: date('n', timezome_time());
	$year		= isset($_POST['year'])		? $_POST['year']				: date('Y', timezome_time());
	
	
	if($layout && $category)
	{
		header('Content-type: application/json');
		$calendar = array('html'=>getEventCalendar($layout, $category, $from, $to, $month, $year)); 
		echo json_encode($calendar);	
	}
}
else
{
	add_shortcode('event', 'event_shotcode');
}

/**
 * Event shortcode function
 * @param array $atts
 * @param array $content
 * @return string  html
 */
function event_shotcode($atts, $content = null)
{
	$category_array = array();
	$calendar = '';
	$month_from = null;
	$month_year = null;	
	extract(shortcode_atts(
					array(
				'category'	=> '',
				'from'		=> '',
				'to'		=> '',
				'layout'	=> 'full',
					), $atts));
	if($category)
	{
		$category_array = explode(',', $category);
	}
	
	if($from)
	{
		$from_thimestamp = strtotime($from);
		$month_from = date('n', $from_thimestamp);
		$month_year = date('Y', $from_thimestamp);
	}
	
	$calendar = '<div class="events_calendar">';
	$calendar .= getEventCalendar($layout, $category_array, $from, $to, $month_from, $month_year);
	$calendar .= '</div>';
	return $calendar;
	
}
/**
 * Get full html code of calendar of events
 * @param type $layout - full - all dates, active - dates only with event
 * @param type $category - events category to show
 * @param type $from - calendar first day 
 * @param type $to - calendar last day
 * @param type $month - month to show 
 * @param type $year - year to show
 * @return string - calendar html
 */
function getEventCalendar($layout, $category, $from, $to, $month=null, $year = null)
{
	$calendar			 = '';										// html
	$show_next_month	 = true;									// show next month flag, false if $month is last in from-to diapason
	$show_previous_month = true;									// show previous month flag, false if $month is first in from-to diapason
	
	if(is_null($month) && is_null($year))
	{
		$month = date('n');
		$year = date('Y');
	}
	
	if(isFirstInDiapason($month, $year, $from))
	{
		$show_previous_month = false;
	}

	if(isLastInDiapason($month, $year, $to))
	{
		$show_next_month = false;
	}
	
	/**
	 * week days name ugly hack
	 */
	$day_of_week = array(
				date_i18n('D', strtotime('Sunday')),
				date_i18n('D', strtotime('Monday')),
				date_i18n('D', strtotime('Tuesday')),
				date_i18n('D', strtotime('Wednesday')),
				date_i18n('D', strtotime('Thursday')),				
				date_i18n('D', strtotime('Friday')),
				date_i18n('D', strtotime('Saturday')),
				date_i18n('D', strtotime('Sunday')),
				date_i18n('D', strtotime('Monday')),
				date_i18n('D', strtotime('Tuesday')),
				date_i18n('D', strtotime('Wednesday')),
				date_i18n('D', strtotime('Thursday')),				
				date_i18n('D', strtotime('Friday')),
				date_i18n('D', strtotime('Saturday')));
	$next_month_name = date_i18n("F",mktime(0, 0, 0, $month + 1, 1));
	$prev_month_name = date_i18n("F",mktime(0, 0, 0, $month - 1, 1));
	
	
	$tempDate			= mktime(0, 0, 0, $month,1);					// showing month
	$month_name			= date_i18n("F",$tempDate);					// Month name(translated)
	
	// < previus month next >
	$calendar .= '<form>';
	
	
		$calendar .= '<div class="calendar_header">';
	
	if($show_previous_month)
	{
		$calendar .= '<a href="#" id="previous_month"><span>'.__($prev_month_name) . '</span></a>';
	}
	$calendar .= '<span class="month">'.__($month_name). '</span>';
	if($show_next_month)
	{
		$calendar .= '<a href="#" id="next_month"><span>' . __($next_month_name).'</span></a>';
	}
	
		$calendar .= '</div>'; //<div class="calendar_header">
	
	if($layout === 'full')
	{
		$calendar .= '<div class="week">';
		for($d = 0; $d<7; $d++)
		{
			if(get_option( 'start_of_week' ) + $d == 7) // is sunday
			{
				$calendar .= '<span class="sunday">';
			}
			else
			{
				$calendar .= '<span>';
			}
			$calendar .= $day_of_week[get_option( 'start_of_week' ) + $d];
			$calendar .= '</span>';
		}
		$calendar .= '</div>'; // <div class="week">
	}
	
	
	
	$calendar .= '<div class="month">';
	
	
	$calendar .= getCalendarHtml($category,$layout, $from, $to, $month, $year);
	
	
	$calendar .= '</div>';	//<div class="month">
	$calendar .= '<input type="hidden" id="calendar_month" value="'.$month.'">';
	$calendar .= '<input type="hidden" id="calendar_year" value="'.$year.'">';
	$calendar .= '<input type="hidden" id="calendar_layout" value="'.$layout.'">';
	$calendar .= '<input type="hidden" id="calendar_from" value="' . $from . '">';
	$calendar .= '<input type="hidden" id="calendar_to" value="' . $to . '">';
	$calendar .= '<input type="hidden" id="calendar_category" value="'.implode(',', $category).'">';
	$calendar .= '</form>';
	
	return $calendar;
}

/**
 * Check month is it a start month in calendar diapason
 * @param int $month - shown month
 * @param int $year - shown year
 * @param string $from - diapason start date
 * @return boolean false if from not set or current month is first diapason month
 */
function isFirstInDiapason($month, $year, $from)
{
	if($from == '')
	{
		return false;
	}
	$previus_month	= ($month == 1)?12:$month-1;
	$previus_year	= ($month == 1)?$year-1:$year;
	
	$from_timestamp = strtotime($from);
	
	if(mktime(0,0,0, $previus_month, 1, $previus_year) < mktime(0,0,0, date('n', $from_timestamp), 1 , date('Y', $from_timestamp)))
	{
		return true;
	}
	return false;
}

/**
 * Check month is it a last month in calendar diapason
 * @param int $month - shown month
 * @param int $year - shown year
 * @param string $from - diapason start date
 * @return boolean false if to not set or current month is last diapason month
 */
function isLastInDiapason($month, $year, $to)
{
	if($to == '')
	{
		return false;
	}
	$next_month	= ($month == 12)?1:$month+1;
	$next_year	= ($month == 12)?$year+1:$year;
	
	$to_timestamp = strtotime($to);
	
	if(mktime(0,0,0, $next_month, 1, $next_year) > mktime(0,0,0, date('n', $to_timestamp), 1 , date('Y', $to_timestamp)))
	{
		return true;
	}
	return false;
	
}

/**
 * Check is date today
 * @param int $month
 * @param int $day
 * @param int $year
 * @return bool
 */
function isToday($month, $day, $year)
{
	return strtotime("$month/$day/$year") == strtotime(date('n/j/Y', timezome_time()));
}

/**
 * HTML of calendar
 * @param array $categories events caregories ID to show
 * @param string $layout full|active - layout of calendar
 * @param string $from - start calendar date or ""
 * @param string $to - end calendar date or "" 
 * @param int $month - month to show 
 * @param int $year - year to show
 * @return string - html of calendar
 */
function getCalendarHtml($categories, $layout, $from, $to, $month, $year)
{
	$calendar			= '';										// HTML
	$temp_list			= '';										// 'cashed' list of events
	$eventsObj			= new Widget_Event();						// Event Object
	$calendar_events	= $eventsObj->getMonthEvents($month, $year);// array of month days with events
	$from_timestamp		= 0;										// first day interval timestamp
	$to_timestamp		= 0;										//kast day inteval timestamp
//	$tempDate			= mktime(0, 0, 0, $month);					// showing month
	$today_was_find		= false;		
	$today_class		= '';


	/**
	 * How match empty days before first date
	 */
	if($from 
		&& strtotime($from) > mktime(0, 0, 0, $month, 1, $year)
		&& strtotime($from) < mktime(23, 59, 59, $month, cal_days_in_month(CAL_GREGORIAN, $month, $year), $year) )
	{
		$start_weekday = date("w",mktime(0, 0, 0, $month, date('d',strtotime($from)), $year));
	}
	else
	{
		$start_weekday = date("w",mktime(0, 0, 0, $month, 1, $year));
	}
	
	
	if($calendar_events && is_array($calendar_events) && count($calendar_events))
	{
		if($from)
		{
			$from_timestamp = strtotime($from);
		}
		
		if($to)
		{
			$to_timestamp = strtotime($to);
		}
		/**
		 * Dosn't show day of week if layout active
		 */
		if($layout === 'full')
		{
			$empty_days = $start_weekday - get_option('start_of_week') ;
			if($empty_days<0)
			{
				$empty_days  = $empty_days + 7;
			}


			while($empty_days > 0)
			{
				$calendar .= '<div class="day empty"></div>';
				$empty_days--;
			}
		}
		
		foreach($calendar_events as $day_number => $events)
		{
			$Custom_Posts_Type_Event = new Custom_Posts_Type_Event();
			$domain = home_url();
			
			$events_count_for_day = count($events);
			$calendar_event = '';
			if( 
				(!$from_timestamp || ($from_timestamp && strtotime("$month/$day_number/$year") >= $from_timestamp))
				&&
				(!$to_timestamp || ($to_timestamp && strtotime("$month/$day_number/$year") <= strtotime($to))) )
			{
				
				if($events && is_array($events) && count($events))
				{
					foreach ($events as $event)
					{

						if(isset($temp_list[$event->post_id]))
						{
							$details = $temp_list[$event->post_id];
						}
						else
						{
							$event_terms = get_the_terms( $event->post_id, Custom_Posts_Type_Event::TAXONOMY);
							$shown_category = false;
							// check is this event in selected shortcode category
							if($event_terms)
							{
								foreach((array) $event_terms as $term)
								{
									if($shown_category === false)
									{
										$shown_category = in_array($term->term_id, $categories); 
									}
								}
							}

							$temp_list[$event->post_id] = array(
																	'post'	=> get_post($event->post_id),
																	'title'	=> get_the_title($event->post_id),
																	'url'	=> get_permalink($event->post_id),
																	'show_event' => $shown_category,
																	'time'	=> get_post_meta($event->post_id, SHORTNAME.'_event_time', true),
							);
							$details = $temp_list[$event->post_id];
						}

						if($details && isset($details['show_event']))
						{
							if($details['show_event'] === true)
							{

								// add concatenation to show all events in day
								if(!$calendar_event)
								{
									$calendar_event = '<div class="event"><a href="'.$details['url'].'"><span class="time">'.$details['time'].'</span>'.$details['title'].'</a></div>';
								}
							}
						}
					}
				}
				if($calendar_event || $layout === 'full')
				{
					$day_has_event_class = '';
					$today_class= '';
					$day_has_few_events = '';
					if(!$today_was_find && isToday($month, $day_number, $year))
					{
						$today_class = " today"; 
						$today_was_find = true;
					}

					if($calendar_event)
					{
						$day_has_event_class = ' has_event';
					}
					
					if($calendar_event && $events_count_for_day >1)
					{
						$day_has_few_events = ' multi';
						
					}

					$calendar .=  '<div class="day'.$today_class.$day_has_event_class.$day_has_few_events.'">';
					
					if(strlen($day_number) == 1)
					{
						$day_number = "0".$day_number;
					}

					if($calendar_event)
					{
						
						$int_day = intval($day_number);
						if(get_option('permalink_structure'))
						{
							$href_to_events_for_the_day =  "<a href='"."{$domain}/{$Custom_Posts_Type_Event->getTaxSlug()}/{$year}/{$month}/{$int_day}/"."'>%s</a>";
						}
						else
						{
							$href_to_events_for_the_day = "<a href='"."{$domain}/index.php?pagename=customeventslist&event_year={$year}&event_month={$month}&event_day={$int_day}"."'>%s</a>";
						}
					}
					else
					{
						$href_to_events_for_the_day = '';
					}
//					else
//					{
//						$href_to_events_for_the_day = $day_number;
//					}
					$calendar .=  "<span class='number'><span>$day_number</span>".sprintf($href_to_events_for_the_day, '')."</span>";
					$calendar .= $calendar_event;
					if($calendar_event && $events_count_for_day >1)
					{
						$calendar .= "<div class='multi_button'><span>".__('More then one event in this day','churchope')."</span>".sprintf($href_to_events_for_the_day, __('view all events', 'churchope'))."</div>";
						
					}
					$calendar .= '</div>';
					$href_to_events_for_the_day = '';
				}
			}
		}
	}
	return $calendar;
}?>