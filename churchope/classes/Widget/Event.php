<?php
/**
 *  Event widget
 */

//php5-calendar fix

if(!function_exists('cal_days_in_month')){
   function cal_days_in_month($cal_type="",$month, $year) {
       return date('t', mktime(0, 0, 0, $month+1, 0, $year));
    }
}

class Widget_Event extends Widget_Default
{
	// Suffixs:

	/**
	 * date meta key
	 */
	const EVENT_DATE_META_KEY	= '_event_date';

	/**
	 *time meta key
	 */
	const EVENT_TIME_META_KEY	= '_event_time';

	/**
	 * is repeating meta key
	 */
	const EVENT_REPEATING_META_KEY = '_event_is_repeat';

	/**
	 * interval
	 */
	const EVENT_INTERVAL_META_KEY = '_event_interval';
/////////////////////////////

	// widget form elements

	const CATEGORY = 'event_category';
	/**
	 * widget select field name
	 */
	const EVENT_FIELD		= 'specific_event' ;

	/**
	 * Widget checkbox field name
	 */
	const HIDE_CHECKBOX		= 'hide-expired';

	/**
	 * widget form title field name
	 */
	const TITLE		= 'title';
	/**
	 * widget form days field name
	 */
	const DAYS		= 'days';
	/**
	 * widget form hour field name
	 */
	const HOUR		= 'hr';
	/**
	 * widget form minute field name
	 */
	const MINUTE	= 'min';
	/**
	 * widget form second field name
	 */
	const SECOND	= 'sec';
////////////////////////
	// Repetition interval
	/**
	 * every day
	 */
	const INTERVAL_DAY = 'day';

	/**
	 * every week <br/>
	 * the initial day of the week
	 */
	const INTERVAL_WEEK = 'week';

	/**
	 * every month <br/>
	 * the initial day of the month
	 */
	const INTERVAL_MONTH = 'month';

	/**
	 * every year <br/>
	 * the initial day of month
	 */
	const INTERVAL_YEAR = 'year';

	/**
	 * 'all' category option value
	 */
	const ALL = 'all';
	/**
	 * 'next' event option value
	 */
	const NEXT = 'next';

	private $timezone_shift = null;

	function __construct($is_parent = false)
	{
		if($is_parent === false)
		{
			$this->setClassName('widget_event clearfix');
			$this->setName('Next Event');
			$this->setDescription('Next Event widget');
			$this->setIdSuffix('nextevent');
		}
		parent::__construct();
	}


	/**
	 * Get tie shift for current timezone
	 * @return int
	 */
	protected function getTimezoneShift()
	{
		if(is_null($this->timezone_shift))
		{
			$this->timezone_shift = get_option( 'gmt_offset', 0 ) * 3600;
		}

		return $this->timezone_shift;
	}

	/**
	 * Get timestamp in current timezone
	 * @return int
	 */
	protected function getTimezoneTime()
	{
		return time() + $this->getTimezoneShift();
	}

	public function widget( $args, $instance ) {

		$event = null;
		$event_url = '';
		$left_time = 0;

		if(!$instance[self::EVENT_FIELD])
		{
			return;
		}
		if(!isset($instance[self::CATEGORY]))
		{
			$instance[self::CATEGORY] = self::ALL;
		}


		$data =  $this->getTimeToSelectedEvent($instance[self::EVENT_FIELD], $instance[self::CATEGORY]);

		if(is_object($data))
		{
			if(isset($data->time_left) && !is_null($data->time_left))
			{
				$left_time  = $data->time_left;
				if(isset($data->event_id) && $data->event_id)
				{
					$event = get_post($data->event_id);
					$event_url = get_permalink($data->event_id);
				}
			}
		}
		elseif(is_integer($data))
		{
			$left_time = $data;
			$event = get_post($instance[self::EVENT_FIELD]);
			$event_url = get_permalink($instance[self::EVENT_FIELD]);
		}

		if( !($left_time == 0 && $instance[self::HIDE_CHECKBOX] == 'on') )
		{
			wp_enqueue_script('nextevent');
			extract($args);

			$title = apply_filters('widget_title', $instance[self::TITLE]);

			echo $before_widget;

			?>

			<script type='text/javascript'>
				jQuery(document).ready(function(){
					var widget_ = new  event_countdown();
					widget_.init('<?php echo $args['widget_id']?>', <?php echo $left_time?>);
					widget_ = null;
				});
			</script>


			<?php

			if ($title)
			{
				echo $before_title .'<a href ="'.$event_url.'">'. $title .'</a>'. $after_title;
			}
			?>
			<ul class="expiration-timer">

					<li >
						<strong class="scale-1"><span>0</span><span>0</span></strong>
						<a href="<?php echo $event_url ?>"></a>
						<span class="descr"><?php echo $instance[self::DAYS]?></span>
					</li>
					<li >
						<strong class="scale-2"><span>0</span><span>0</span></strong>
						<a href="<?php echo $event_url ?>"></a>
						<span class="descr"><?php echo $instance[self::HOUR]?></span>
					</li>
					<li >
						<strong class="scale-3"><span>0</span><span>0</span></strong>
						<a href="<?php echo $event_url ?>"></a>
						<span class="descr"><?php echo $instance[self::MINUTE]?></span>
					</li>
					<li >
						<strong class="scale-4"><span>0</span><span>0</span></strong>
						<a href="<?php echo $event_url ?>"></a>
						<span class="descr"><?php echo $instance[self::SECOND]?></span>
					</li>

			</ul>

			<?php
			echo $after_widget;
		}
	}


	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance[self::CATEGORY]		= strip_tags($new_instance[self::CATEGORY]);
		$instance[self::EVENT_FIELD]	= strip_tags($new_instance[self::EVENT_FIELD]);
		$instance[self::HIDE_CHECKBOX]	= strip_tags($new_instance[self::HIDE_CHECKBOX]);
		$instance[self::TITLE]	= strip_tags($new_instance[self::TITLE]);
		$instance[self::DAYS]	= strip_tags($new_instance[self::DAYS]);
		$instance[self::HOUR]	= strip_tags($new_instance[self::HOUR]);
		$instance[self::MINUTE]	= strip_tags($new_instance[self::MINUTE]);
		$instance[self::SECOND]	= strip_tags($new_instance[self::SECOND]);

		return $instance;
	}


	public function form( $instance ) {
		$defaults = array(
				self::TITLE			=> __('Next event in:', 'churchope'),
				self::CATEGORY		=> self::ALL,
				self::DAYS			=> __('DAYS', 'churchope'),
				self::HOUR			=> __('HR', 'churchope'),
				self::MINUTE		=> __('MIN', 'churchope'),
				self::SECOND		=> __('SEC', 'churchope'),
				self::HIDE_CHECKBOX	=> '',
			);

		$instance = wp_parse_args((array) $instance, $defaults);

		$events = $this->getFeaturedEventListByCategory($instance[self::CATEGORY]);

		$hide_expired = isset($instance[self::HIDE_CHECKBOX]) && $instance[self::HIDE_CHECKBOX];
		?>
		<div>
			<label for="<?php echo $this->get_field_id(self::CATEGORY); ?>"><?php _e('Category:', 'churchope'); ?></label>
			<select name ="<?php echo $this->get_field_name(self::CATEGORY); ?>" id="<?php echo $this->get_field_id( self::CATEGORY ); ?>" style="width:100%;">
				<option value='<?php echo self::ALL?>'>- ALL -</option>
				<?php
					$terms_list = $this->getFormCategoryList();
					foreach((array) $terms_list as $term):
						$selected = "";
						if (isset($instance[self::CATEGORY])&& $instance[self::CATEGORY] == $term->term_id)
						{
							$selected = "selected='selected'";
						}
						$option = '<option value="'.$term->term_id.'" '.$selected.'>';
						$option .= $term->name;
						$option .= '</option>';

						echo $option;
					endforeach;?>
			</select>

			<?php if(true || $events):?>
				<label for="<?php echo $this->get_field_id(self::EVENT_FIELD); ?>"><?php _e('Event:', 'churchope'); ?></label>
				<select name ="<?php echo $this->get_field_name(self::EVENT_FIELD); ?>" id="<?php echo $this->get_field_id( self::EVENT_FIELD ); ?>" style="width:100%;">
				<?php
					$selected = "";
					if (isset($instance[self::EVENT_FIELD])
						&& $instance[self::EVENT_FIELD] == self::NEXT)
					{
						$selected = "selected='selected'";
					}
					echo "<option $selected value='".self::NEXT."'>Next Nearest</option>";

					foreach((array) $events as $event):
						if(isset($event->post_id))
						{
							$event_post = get_post($event->post_id);
							if($event_post)
							{
								if (isset($instance[self::EVENT_FIELD])
									&& $instance[self::EVENT_FIELD] == $event->post_id)
								{
									$selected = "selected='selected'";
								}
								else
								{
									$selected = "";
								}
								echo "<option $selected value='" . $event->post_id . "'>" . $event_post->post_title . "</option>";
							}
						}
					endforeach;?>
				</select>
			<?php else:?>
				<span style="color:red"><?php _e('No upcoming events, please create new one', 'churchope')?></span>
			<?php endif;?>
			<p>
				<label for="<?php echo $this->get_field_id(self::TITLE); ?>"><?php _e('Title:', 'churchope'); ?></label>
				<input id="<?php echo $this->get_field_id(self::TITLE); ?>" name="<?php echo $this->get_field_name(self::TITLE); ?>" type="text" value="<?php echo $instance[self::TITLE]; ?>" style="width:100%;" />
			</p>
			<p>
				<label for="<?php echo $this->get_field_id(self::DAYS); ?>"><?php _e('Days title:', 'churchope'); ?></label>
				<input id="<?php echo $this->get_field_id(self::DAYS); ?>" name="<?php echo $this->get_field_name(self::DAYS); ?>" type="text" value="<?php echo $instance[self::DAYS]; ?>" style="width:100%;" />
			</p>
			<p>
				<label for="<?php echo $this->get_field_id(self::HOUR); ?>"><?php _e('Hour title:', 'churchope'); ?></label>
				<input id="<?php echo $this->get_field_id(self::HOUR); ?>" name="<?php echo $this->get_field_name(self::HOUR); ?>" type="text" value="<?php echo $instance[self::HOUR]; ?>" style="width:100%;" />
			</p>
			<p>
				<label for="<?php echo $this->get_field_id(self::MINUTE); ?>"><?php _e('Min title:', 'churchope'); ?></label>
				<input id="<?php echo $this->get_field_id(self::MINUTE); ?>" name="<?php echo $this->get_field_name(self::MINUTE); ?>" type="text" value="<?php echo $instance[self::MINUTE]; ?>" style="width:100%;" />
			</p>
			<p>
				<label for="<?php echo $this->get_field_id(self::SECOND); ?>"><?php _e('Sec title:', 'churchope'); ?></label>
				<input id="<?php echo $this->get_field_id(self::SECOND); ?>" name="<?php echo $this->get_field_name(self::SECOND); ?>" type="text" value="<?php echo $instance[self::SECOND]; ?>" style="width:100%;" />
			</p>
			<p>
			<label for="<?php echo $this->get_field_id(self::HIDE_CHECKBOX); ?>"><?php _e('Hide expired event:', 'churchope'); ?>
				<input id="<?php echo $this->get_field_id(self::HIDE_CHECKBOX); ?>" name="<?php echo $this->get_field_name(self::HIDE_CHECKBOX); ?>" type="checkbox" <?php echo $hide_expired ? 'checked="checked"' : ''; ?> />
			</label>
		</p>
		</div>
		<div style="clear:both;">&nbsp;</div>
		<script>
		jQuery(document).ready(function(){
			window.event_temp = {};
			jQuery("#<?php echo $this->get_field_id( self::CATEGORY ); ?>").change(function(){
//					var $this = this;
					var cat = jQuery(this).val();
					if( typeof window.event_temp[cat] == "undefined" )
					{
						jQuery.ajax({
							type: "post",
							url: '<?php echo admin_url('admin-ajax.php'); ?>',
							dataType: 'json',
							data: {	action: "get_events_cat",cat: cat },
//							beforeSend:function(){
//								jQuery($this).closest('form').find('img.ajax-feedback').css('visibility', 'visible');
//
//							},
							success: function(data){ //so, if data is retrieved, store it in html

								window.event_temp[cat] = data;
//								jQuery($this).closest('form').find('img.ajax-feedback').hide();
								var sel = jQuery("#<?php echo $this->get_field_id( self::EVENT_FIELD ); ?>");
								sel.empty();
								sel.append("<option value='<?php echo self::NEXT?>'>Next Nearest</option>");
								for (var i=0; i<data.length; i++) {
									sel.append('<option value="' + data[i].post_id + '">' + data[i].title + '</option>');
								}
//								jQuery($this).closest('form').find('img.ajax-feedback').css('visibility', 'hidden');
							}

						}); //close jQuery.ajax
					}
					else
					{
					 // use cached values
						var data = window.event_temp[cat];
						var sel = jQuery("#<?php echo $this->get_field_id( self::EVENT_FIELD ); ?>");
						sel.empty();
						sel.append("<option value='<?php echo self::NEXT?>'>Next Nearest</option>");
						for (var i=0; i<data.length; i++) {
							sel.append('<option value="' + data[i].post_id + '">' + data[i].title + '</option>');
						}
					}
				});
			});
		</script>
		<?php
	}

	/**
	 * Find nearest event from now<br/>
	 * on success return object
	 * stdClass->time_left - next date
	 * stdClass->event_id - event date
	 * @param string $category Nearest
	 * @return \stdClass|boolean
	 */
	private function getNextNearestEvent($category)
	{
//		$events = $this->getFeaturedEventsList();
		$events = $this->getFeaturedEventListByCategory($category);

		$result = new stdClass();
		$result->event_id = null;
		$result->time_left = null;

		if($events && is_array($events) && count($events))
		{
			foreach($events as $event)
			{
				if(is_null($result->time_left))
				{
					if($event->time_left > 0)
					{
						$result->time_left = $event->time_left;
						$result->event_id = $event->post_id;
					}
					elseif($event->is_repeat)
					{

						$result->time_left = $this->getTimeBeforeEvent($event->post_id);
						$result->event_id = $event->post_id;
					}
				}
				else
				{
					if($event->time_left > 0 && $event->time_left < $result->time_left)
					{
						$result->time_left = $event->time_left;
						$result->event_id = $event->post_id;
					}
					else
					{
						$next = $this->getTimeBeforeEvent($event->post_id);

						if($next < $result->time_left)
						{
							$result->time_left = $next;
							$result->event_id = $event->post_id;
						}
					}
				}

			}
			return $result;
		}
		return false;
	}

	/**
	 * Return second to next event
	 * if id = 'next' return object @see getNextNearestEvent
	 * @param int|string $id - event id or 'next'
	 * @return \stdClass|0
	 */
	private function getTimeToSelectedEvent($id, $category = self::ALL)
	{
		if($id != self::NEXT)
		{
			return $this->getTimeBeforeEvent($id);
		}
		else
		{
			$result = $this->getNextNearestEvent($category);
			if($result)
			{
				return $result;
			}
		}
		return 0;
	}

	/**
	 * Get next event date skipping month count of day less thet our date
	 * @param string $date
	 * @param string $format
	 */
	private function getNextMonthEventDate($date, $format = 'm/d/Y H:i')
	{
		if(is_integer($date))
		{
			$date = date($format, $date);
		}

		$dObj = new DateTime($date);
		$day_number = $dObj->format('j');
		$month_number = $dObj->format('m');
		$year_number = $dObj->format('Y');

		if($month_number == 12)
		{
			$next_month = 1;
			$year_number++;
		}
		else
		{
			$next_month = ++$month_number;
		}

		$day_in_next_month = cal_days_in_month(CAL_GREGORIAN, $next_month, $year_number);
		if($day_number <= $day_in_next_month)
		{
			return $this->getNextMonth(strtotime($date), 1);
		}
		else
		{
			return $this->getNextMonth(strtotime($date), 2);
		}
	}

	/**
	* Calculates a date lying a given number of months in the future of a given date.
	* The results resemble the logic used in MySQL where '2009-01-31 +1 month'
	* is '2009-02-28' rather than '2009-03-03' (like in PHP's strtotime).
	*
	* @author akniep
	* @since 2009-02-03
	* @param $base_time long, The timestamp used to calculate the returned value .
	* @param $months int, The number of months to jump to the future of the given $base_time.
	* @return long, The timestamp of the day $months months in the future of $base_time
	*/
	private function getNextMonth($base_time = null, $months = 1)
	{
		if (is_null($base_time))
			$base_time = time();

		$x_months_to_the_future = strtotime("+" . $months . " months", $base_time);

		$month_before = (int) date("m", $base_time) + 12 * (int) date("Y", $base_time);
		$month_after = (int) date("m", $x_months_to_the_future) + 12 * (int) date("Y", $x_months_to_the_future);

		if ($month_after > $months + $month_before)
			$x_months_to_the_future = strtotime(date("Ym01His", $x_months_to_the_future) . " -1 day");

		return $x_months_to_the_future;
	}

	/**
	 * Time in sec to event start
	 * @param int $event_id
	 * @return int
	 */
	protected function getTimeBeforeEvent($event_id, $start_from = false)
	{
			$event_details = $this->getEventsTimeDetails($event_id);

			if($event_details && isset($event_details->time_left))
			{
				if($event_details->time_left > 0 && false == $start_from)
				{
					return (int) $event_details->time_left;
				}
				else
				{
					if(!$start_from)
					{
						$start_from = $this->getTimezoneTime();
					}

					if($event_details->is_repeat)
					{
						$date = $event_details->date . ' ' . $event_details->time;
						$time_format = 'm/d/Y H:i';

						if(preg_match('/m$/', $event_details->time))
						{
							$time_format = 'm/d/Y h:i a';
						}
						elseif(preg_match('/M$/', $event_details->time))
						{
							$time_format = 'm/d/Y h:i A';
						}

						if($event_details->event_interval == self::INTERVAL_MONTH)
						{
							do
							{
								$date = date($time_format, $this->getNextMonthEventDate($date,$time_format));
							}
							while(strtotime($date) <= $start_from); // UTC

							return strtotime($date) - $this->getTimezoneTime();
						}
						else
						{
							do{
								$date = date($time_format, strtotime($date . " +1 {$event_details->event_interval}"));
							}
							while(strtotime($date) <= $start_from);

							return strtotime($date) - $this->getTimezoneTime();
						}
					}
				}
			}
		return 0;
	}


	/**
	 * Return on succes object
	 *  ->post_id - event date<br/>
	 *	->date - strted date<br/>
	 *  ->time - time of event<br/>
	 *  ->event_interval - event interval<br/>
	 *	->is_repeat - is repitition event (1|0)<br/>
	 *	->time_left - time left to event (event in future >0 | in past < 0)<br/>
	 * @global object $wpdb
	 * @param int $event_id
	 * @return object|boolean
	 */
	private function getEventsTimeDetails($event_id)
	{
		global $wpdb;
		if($wpdb)
		{
			$result = '';
			$wpdb->query('SET time_zone="+00:00"');
			$query = <<<SQL
				SELECT
					postmeta.post_id as post_id,
					meta_date.meta_value as date,
					meta_time.meta_value as time,
					meta_interval.meta_value as event_interval,
					IF(ISNULL(meta_repeat.meta_value),0,IF(meta_repeat.meta_value = 'on',1,0)) as is_repeat,
					(UNIX_TIMESTAMP( STR_TO_DATE( CONCAT(meta_date.meta_value, ' ', IF(ISNULL(meta_time.meta_value),'12:00 AM',meta_time.meta_value)),CONCAT('%m/%d/%Y' , ' ', IF(meta_time.meta_value REGEXP '[^m]$', '%H:%i', '%h:%i %p' )) ) ) - UNIX_TIMESTAMP() - {$this->getTimezoneShift()} ) as time_left
					FROM {$wpdb->postmeta} postmeta
					INNER JOIN {$wpdb->posts} post ON (
														post.ID = postmeta.post_id
														AND
														post.post_status  = 'publish'
														)
					LEFT JOIN {$wpdb->postmeta} meta_date ON (
																postmeta.post_id = meta_date.post_id
																AND
																meta_date.meta_key = '{$this->getDateMetaKeyName()}'
															)
					LEFT JOIN {$wpdb->postmeta} meta_time ON (
																postmeta.post_id = meta_time.post_id
																AND
																meta_time.meta_key = '{$this->getTimeMetaKeyName()}'
															)
					LEFT JOIN {$wpdb->postmeta} meta_interval ON (
																postmeta.post_id = meta_interval.post_id
																AND
																meta_interval.meta_key = '{$this->getIntervalMetaKeyName()}'
															)
					LEFT JOIN {$wpdb->postmeta} meta_repeat ON (
																postmeta.post_id = meta_repeat.post_id
																AND
																meta_repeat.meta_key = '{$this->getRepetitionMetaKeyName()}'
															)
					WHERE
						postmeta.meta_key = '{$this->getDateMetaKeyName()}'
						AND
						postmeta.post_id = '{$event_id}'
					ORDER BY post_id
SQL;

			$result = $wpdb->get_row($query, OBJECT);
			return $result;
		}
		return false;
	}

	/**
	 * Get list repeating events(status = 'publish') and events which time has not come yet
	 * @global object $wpdb
	 * @return mix
	 */
	protected function getFeaturedEventsList()
	{
		global $wpdb;
		if($wpdb)
		{
			$result = '';
			$wpdb->query('SET time_zone="+00:00"');
			$query = <<<SQL
				SELECT
					postmeta.post_id as post_id,
					meta_date.meta_value as date,
					meta_time.meta_value as time,
					meta_interval.meta_value as event_interval,
					IF(ISNULL(meta_repeat.meta_value),0,IF(meta_repeat.meta_value = 'on',1,0)) as is_repeat,
					(UNIX_TIMESTAMP( STR_TO_DATE( CONCAT(meta_date.meta_value, ' ', IF(ISNULL(meta_time.meta_value),'12:00 AM',meta_time.meta_value)),CONCAT('%m/%d/%Y' , ' ', IF(meta_time.meta_value REGEXP '[^m]$', '%H:%i', '%h:%i %p' )) ) ) - UNIX_TIMESTAMP() - {$this->getTimezoneShift()} ) as time_left

					FROM {$wpdb->postmeta} postmeta
					INNER JOIN {$wpdb->posts} post ON (
														post.ID = postmeta.post_id
														AND
														post.post_status  = 'publish'
															)
					LEFT JOIN {$wpdb->postmeta} meta_date ON (
																postmeta.post_id = meta_date.post_id
																AND
																meta_date.meta_key = '{$this->getDateMetaKeyName()}'
															)
					LEFT JOIN {$wpdb->postmeta} meta_time ON (
																postmeta.post_id = meta_time.post_id
																AND
																meta_time.meta_key = '{$this->getTimeMetaKeyName()}'
															)
					LEFT JOIN {$wpdb->postmeta} meta_interval ON (
																postmeta.post_id = meta_interval.post_id
																AND
																meta_interval.meta_key = '{$this->getIntervalMetaKeyName()}'
															)
					LEFT JOIN {$wpdb->postmeta} meta_repeat ON (
																postmeta.post_id = meta_repeat.post_id
																AND
																meta_repeat.meta_key = '{$this->getRepetitionMetaKeyName()}'
															)
SQL;
			// Adding condition if WPML plugin is exist.
			if($this->isWPML_PluginActive())
			{
				$query .=
				" INNER JOIN {$wpdb->prefix}icl_translations translations on(postmeta.post_id = translations.element_id AND translations.language_code = '".ICL_LANGUAGE_CODE."') ";
			}

			$query .= <<<SQL
					WHERE
						postmeta.meta_key = '{$this->getDateMetaKeyName()}'
						AND (
							meta_repeat.meta_value = 'on'
							OR
							(UNIX_TIMESTAMP( STR_TO_DATE( CONCAT(meta_date.meta_value, ' ', IF(ISNULL(meta_time.meta_value),'12:00 AM', meta_time.meta_value)),CONCAT('%m/%d/%Y' , ' ', IF(meta_time.meta_value REGEXP '[^m]$', '%H:%i', '%h:%i %p' )) ) ) - UNIX_TIMESTAMP() - {$this->getTimezoneShift()}) > 0
						)
					ORDER BY post_id
SQL;

			$result = $wpdb->get_results($query, OBJECT);
			return $result;
		}
		return false;
	}

	/**
	 * Events in month
	 * @param int|null $month
	 * @param int|null $year
	 * @return array
	 */
	public function getMonthEvents($month=null, $year=null)
	{
		if(is_null($month) && is_null($year))
		{
			$month = date('n');
			$year = date('Y');
		}

		$calendar = array();
		$day_in_month = cal_days_in_month(CAL_GREGORIAN, $month, $year);
		$month_start = mktime(0, 0, 0, $month, 1, $year);
		$month_end = mktime(23, 59, 59, $month, $day_in_month, $year);

		for ($d = 1; $d<=$day_in_month; $d++ )
		{
			$calendar[$d] = array();
		}
		////////// no repeating events //////////////////
		$no_repeating_events = $this->getNotRepeatingMonthEvents($month,$year );
		foreach ((array) $no_repeating_events as $event)
		{
			$calendar[$event->day][] = $event;
		}
		////////// calculate repeating events /////////////
		$repeating_events = $this->getRepeatingEvents();
		foreach ((array) $repeating_events as $event)
		{

			$start_event_datetimestamp  = strtotime($event->date);
			$one_of_event_date = 0;

			if($start_event_datetimestamp < $month_end )
			{
				if($event->event_interval == self::INTERVAL_MONTH)
				{
					while($one_of_event_date <= $month_end)
					{
						if($one_of_event_date)
						{
							$one_of_event_date = $this->getNextMonthEventDate($one_of_event_date);
						}
						else
						{
//							$one_of_event_date = $this->getNextMonthEventDate($start_event_datetimestamp);
							$one_of_event_date = $start_event_datetimestamp;
						}


						if($one_of_event_date >= $month_start && $one_of_event_date <= $month_end)
						{
							$day = date('j', $one_of_event_date);
							$calendar[$day][] = $event;
						}
					}
				}
				else
				{
					while($one_of_event_date <= $month_end)
					{
						if($one_of_event_date)
						{
							$one_of_event_date = strtotime(date('m/d/Y H:i:s', $one_of_event_date) . " +1 {$event->event_interval}");
						}
						else
						{
							$one_of_event_date = $start_event_datetimestamp;
//							$one_of_event_date = strtotime(date('m/d/Y H:i:s', $start_event_datetimestamp) . " +1 {$event->event_interval}");
						}

						if($one_of_event_date >= $month_start &&  $one_of_event_date <= $month_end)
						{
							$day = date('j', $one_of_event_date);
							$calendar[$day][] = $event;
						}
					}
				}
			}
		}
		return $this->sortMonthEvents($calendar);
	}

	private function sortMonthEvents($month_events)
	{
		if(count($month_events))
		{
			$sorted = array();
			foreach($month_events as $day=>$events)
			{
				usort($events, array($this, 'sort'));
				$sorted[$day] = $events;
			}
		}
		return $sorted;
	}

	function sort($a, $b)
	{
		$a_time = strtotime($a->time);
		$b_time = strtotime($b->time);
		if($a_time == $b_time)
		{
			return 0;
		}
		return ($a_time > $b_time)?1:-1;
	}

	/**
	 * List of repeating events(status='publish')
	 * @global object $wpdb
	 * @return mix
	 */
	private function getRepeatingEvents()
	{
		global $wpdb;
		if($wpdb)
		{
			$result = '';

			$query = <<<SQL
				SELECT
					postmeta.post_id as post_id,
					meta_date.meta_value as date,
					DAY(STR_TO_DATE(meta_date.meta_value, '%m/%d/%Y')) as day,
					meta_time.meta_value as time,
					meta_interval.meta_value as event_interval

					FROM {$wpdb->postmeta} postmeta
					INNER JOIN {$wpdb->posts} post ON (
														post.ID = postmeta.post_id
														AND
														post.post_status  = 'publish'
														)
					LEFT JOIN {$wpdb->postmeta} meta_date ON (
																postmeta.post_id = meta_date.post_id
																AND
																meta_date.meta_key = '{$this->getDateMetaKeyName()}'
															)
					LEFT JOIN {$wpdb->postmeta} meta_time ON (
																postmeta.post_id = meta_time.post_id
																AND
																meta_time.meta_key = '{$this->getTimeMetaKeyName()}'
															)
					LEFT JOIN {$wpdb->postmeta} meta_interval ON (
																postmeta.post_id = meta_interval.post_id
																AND
																meta_interval.meta_key = '{$this->getIntervalMetaKeyName()}'
															)
					LEFT JOIN {$wpdb->postmeta} meta_repeat ON (
																postmeta.post_id = meta_repeat.post_id
																AND
																meta_repeat.meta_key = '{$this->getRepetitionMetaKeyName()}'
															)
SQL;
			// Adding condition if WPML plugin is exist.
			if($this->isWPML_PluginActive())
			{
				$query .=
				" INNER JOIN {$wpdb->prefix}icl_translations translations on(postmeta.post_id = translations.element_id AND translations.language_code = '".ICL_LANGUAGE_CODE."') ";
			}

			$query .= <<<SQL
					WHERE
						postmeta.meta_key = '{$this->getDateMetaKeyName()}'
						AND meta_repeat.meta_value = 'on'

					ORDER BY post_id
SQL;

			$result = $wpdb->get_results($query, OBJECT);
			return $result;
		}
		return false;
	}


	/**
	 * Get not repeating events in a given month.
	 * if month and year are null then use current month and date
	 * @global object $wpdb
	 * @param mix $month
	 * @param mix $year
	 * @return mix
	 */
	private function getNotRepeatingMonthEvents($month = null, $year = null)
	{
		global $wpdb;
		if(is_null($month) && is_null($year))
		{
			$month = date('n');
			$year = date('Y');
		}
		if($wpdb)
		{
			$result = '';
			$wpdb->query('SET time_zone="+00:00"');
			$query = <<<SQL
			SELECT
					postmeta.post_id as post_id,
					DAY(STR_TO_DATE(meta_date.meta_value, '%m/%d/%Y')) as day,
					meta_date.meta_value as date,
					meta_time.meta_value as time,
					meta_interval.meta_value as event_interval,
					IF(ISNULL(meta_repeat.meta_value),0,IF(meta_repeat.meta_value = 'on',1,0)) as is_repeat,
					(UNIX_TIMESTAMP( STR_TO_DATE( CONCAT(meta_date.meta_value, ' ', meta_time.meta_value),CONCAT('%m/%d/%Y' , ' ', IF(meta_time.meta_value REGEXP '[^m]$', '%H:%i', '%h:%i %p' )) ) ) - UNIX_TIMESTAMP() - {$this->getTimezoneShift()}) as time_left
					FROM {$wpdb->postmeta} postmeta
					INNER JOIN {$wpdb->posts} post ON (
														post.ID = postmeta.post_id
														AND
														post.post_status  = 'publish'
														)
					LEFT JOIN {$wpdb->postmeta} meta_date ON (
																postmeta.post_id = meta_date.post_id
																AND
																meta_date.meta_key = '{$this->getDateMetaKeyName()}'
															)
					LEFT JOIN {$wpdb->postmeta} meta_time ON (
																postmeta.post_id = meta_time.post_id
																AND
																meta_time.meta_key = '{$this->getTimeMetaKeyName()}'
															)
					LEFT JOIN {$wpdb->postmeta} meta_interval ON (
																postmeta.post_id = meta_interval.post_id
																AND
																meta_interval.meta_key = '{$this->getIntervalMetaKeyName()}'
															)
					LEFT JOIN {$wpdb->postmeta} meta_repeat ON (
																postmeta.post_id = meta_repeat.post_id
																AND
																meta_repeat.meta_key = '{$this->getRepetitionMetaKeyName()}'
															)
SQL;
			// Adding condition if WPML plugin is exist.
			if($this->isWPML_PluginActive())
			{
				$query .=
				" INNER JOIN {$wpdb->prefix}icl_translations translations on(postmeta.post_id = translations.element_id AND translations.language_code = '".ICL_LANGUAGE_CODE."') ";
			}

			$query .= <<<SQL
					WHERE
						postmeta.meta_key = '{$this->getDateMetaKeyName()}'
						AND
							(meta_repeat.meta_value = '' or ISNULL(meta_repeat.meta_value))
						AND
							MONTH(STR_TO_DATE(meta_date.meta_value, '%m/%d/%Y') ) = {$month}
						AND
							YEAR (STR_TO_DATE(meta_date.meta_value, '%m/%d/%Y') ) = {$year}
					ORDER BY post_id
SQL;

			$result = $wpdb->get_results($query, OBJECT);
			return $result;
		}
		return false;
	}

	/**
	 * Get date meta key for current theme
	 * @return type
	 */
	private function getDateMetaKeyName()
	{
		return SHORTNAME . self::EVENT_DATE_META_KEY;
	}

	/**
	 * Get time meta key for current theme
	 * @return type
	 */
	private function getTimeMetaKeyName()
	{
		return SHORTNAME . self::EVENT_TIME_META_KEY;
	}

	/**
	 * Get repetition flag meta key for current theme
	 * @return type
	 */
	private function getRepetitionMetaKeyName()
	{
		return SHORTNAME . self::EVENT_REPEATING_META_KEY;
	}

	/**
	 * Get interval meta key for current theme
	 * @return type
	 */
	private function getIntervalMetaKeyName()
	{
		return SHORTNAME . self::EVENT_INTERVAL_META_KEY;
	}

	/**
	 *
	 * @param type $category
	 * @return type
	 */
	private function getFeaturedEventListByCategory($category = self::ALL)
	{
		$all_featured_events = $this->getFeaturedEventsList();

		if($category == self::ALL)
		{
			return $all_featured_events;
		}
		else
		{
			$featured_by_category = array();
			if($all_featured_events && is_array($all_featured_events) && count($all_featured_events))
			{
				foreach ($all_featured_events as $event)
				{
					if(has_term($category, Custom_Posts_Type_Event::TAXONOMY, $event->post_id))
					{
						$featured_by_category[] = $event;
					}
				}
			}
			return $featured_by_category;
		}
	}

	/**
	 * ajax gate
	 * @param string $category category for event choose
	 * @return array Featured events for this category
	 */
	public function ajaxRun($category = self::ALL)
	{
		$events = $this->getFeaturedEventListByCategory($category);
		return $this->getPreparedEventListDataForAjax($events);
	}


	/**
	 * Get array of object with event id and title values
	 * @param array $events
	 */
	private function getPreparedEventListDataForAjax($events)
	{
		$ajax_data= array();
		if($events && is_array($events) && count($events))
		{
			foreach ($events as $event)
			{
				$event_post = get_post($event->post_id);
				if($event_post)
				{
					$data = new stdClass();
					$data->post_id = $event_post->ID;
					$data->title = $event_post->post_title;
					$ajax_data[] = $data;
				}
			}
		}
		return $ajax_data ;
	}

	/**
	 * Get categories list with fetured events
	 * @return array
	 */
	private function getFormCategoryList()
	{
		$resulted_terms = array();
		$terms_list = get_terms(Custom_Posts_Type_Event::TAXONOMY);
		$events = $this->getFeaturedEventListByCategory();

		if($terms_list && $events)
		{
			foreach ($events as $event)
			{
				foreach ($terms_list as $term)
				{
					if(!key_exists($term->term_id, $resulted_terms))
					{
						if(has_term($term->term_id, Custom_Posts_Type_Event::TAXONOMY, $event->post_id))
						{
							$resulted_terms[$term->term_id] = $term;
						}
					}
				}
			}
		}

		return $resulted_terms;
	}

}
