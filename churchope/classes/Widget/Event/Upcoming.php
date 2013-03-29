<?php

class Widget_Event_Upcoming extends Widget_Event implements Widget_Interface_Cache
{
	/**
	 * Form Fields
	 */
	const TITLE		= 'title';
	const CATEGORY	= 'category';
	const COUNT		= 'count';
	const ALL		= 'all';
	const PHONE		= 'phone';
	const TIME		= 'time';
	const PLACE		= 'place';
	const EMAIL		= 'email';

	/**
	 * meta field
	 */
	const CONTACT_PHONE = '_contact_phone';
	const CONTACT_EMAIL = '_contact_email';
	const EVENT_ADDRESS = '_event_address';
	
	const UPCOMING_TRANSIENT = 'GafdflJJ_dmf';
	
	function __construct()
	{
		$this->setClassName('widget_upcoming clearfix');
		$this->setName('Upcoming Events');
		$this->setDescription('Upcoming Events widget');
		$this->setIdSuffix('upcomingevent');
		parent::__construct(true);
		
		add_action('save_post', array(&$this, 'action_clear_widget_cache'));
	}
	
	function action_clear_widget_cache($postID)
	{
		if(get_post_type($postID) == Custom_Posts_Type_Event::POST_TYPE)
		{
			$temp_number = $this->number;

			$settings = $this->get_settings();
			
			if ( is_array($settings) ) {
				foreach ( array_keys($settings) as $number ) {
					if ( is_numeric($number) ) {
						$this->number = $number;
						$this->deleteWidgetCache();
					}
				}
			}
			$this->number = $temp_number;
		}
	}
	
	function widget( $args, $instance )
	{
		$upcoming_events = $this->getUpcomingEvents($instance);
		
		$html = '';
		if(is_array($upcoming_events) && count($upcoming_events))
		{
			extract( $args );
			$title = apply_filters( 'widget_title', $instance[self::TITLE] );		
			
			echo $before_widget;
			
			if ( $title )
			{
				echo $before_title . $title . $after_title;
			}
			$html .= '<ul>';
			foreach ($upcoming_events as $event)
			{
				$url =  get_permalink($event->post_id);
				$html .= <<<HTML_ENTITIES
				<li>
				<p class="meta_date">
					
				<strong>{$event->day}</strong>
				<a href="{$url}"></a>
				<span>{$event->month}</span>
				</p>
				<a href="{$url}" class="entry-title">
					
						{$event->title}						
					
				</a>	
HTML_ENTITIES;
				
				
				if(isset($instance[self::PLACE]) && $instance[self::PLACE])
				{
					$html .= "<span>{$event->place}</span>";
				}
				if(isset($instance[self::PHONE]) && $instance[self::PHONE])
				{
					$html .= "<span>{$event->phone}</span>";
				}
				if(isset($instance[self::TIME]) && $instance[self::TIME])
				{
					$html .= "<span>{$event->time}</span>";
				}
				
				$html .= "</li>";
			}
			$html .= '</ul>';
			echo $html;
			echo $after_widget;
		}
		echo '';
	}
	
	function update( $new_instance, $old_instance )
	{
		$instance = $old_instance;
		$instance[self::TITLE]		= strip_tags($new_instance[self::TITLE]);
		$instance[self::COUNT]		= strip_tags($new_instance[self::COUNT]);
		$instance[self::CATEGORY]	= strip_tags($new_instance[self::CATEGORY]);
		$instance[self::PHONE]		= strip_tags($new_instance[self::PHONE]);
		$instance[self::TIME]		= strip_tags($new_instance[self::TIME]);
		$instance[self::PLACE]		= strip_tags($new_instance[self::PLACE]);
		$instance[self::EMAIL]		= strip_tags($new_instance[self::EMAIL]);
		
		$this->deleteWidgetCache();
		return $instance;
	}
	
	function form( $instance )
	{
		$instance = wp_parse_args( (array) $instance, $this->getDefaultValue());
		$show_place = isset($instance[self::PLACE]) && $instance[self::PLACE];
		$show_phone = isset($instance[self::PHONE]) && $instance[self::PHONE];
		$show_time	= isset($instance[self::TIME]) && $instance[self::TIME];
		$show_email = isset($instance[self::EMAIL]) && $instance[self::EMAIL];
		?>
		<div>
		<p>
			<label for="<?php echo $this->get_field_id( self::TITLE ); ?>">
				<?php _e( 'Title:', 'churchope' ); ?>
			</label>
			<input id="<?php echo $this->get_field_id( self::TITLE ); ?>" name="<?php echo $this->get_field_name( self::TITLE ); ?>" type="text" value="<?php echo $instance[self::TITLE]; ?>" style="width:100%;" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( self::CATEGORY ); ?>" >
				<?php _e( 'Category of events:', 'churchope' ); ?>
			</label>
			<select name="<?php echo $this->get_field_name( self::CATEGORY ); ?>" id="<?php echo $this->get_field_id( self::CATEGORY ); ?>"  style="width:100%;">
				<option value="<?php echo self::ALL; ?>">All</option>
				<?php
				if($category_list = $this->getEventsCategoryList())
				{
					foreach ($category_list  as $cat)
					{
						$selected = "";
						if ($instance[self::CATEGORY] == $cat->slug)
						{
							$selected = "selected='selected'";
						}
						echo "<option $selected value='" . $cat->slug . "'>" . $cat->name . "</option>";
					}
				}?>
			</select>
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id( self::COUNT ); ?>">
				<?php _e( 'Number of events:', 'churchope' ); ?>
			</label>
			<input id="<?php echo $this->get_field_id( self::COUNT ); ?>" name="<?php echo $this->get_field_name( self::COUNT ); ?>" type="text" value="<?php echo $instance[self::COUNT]; ?>" style="width:100%;" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id(self::TIME); ?>"><?php _e('Show event time:', 'churchope'); ?>
				<input id="<?php echo $this->get_field_id(self::TIME); ?>" name="<?php echo $this->get_field_name(self::TIME); ?>" type="checkbox" <?php echo $show_time ? 'checked="checked"' : ''; ?> />
			</label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id(self::PLACE); ?>"><?php _e('Show event place:', 'churchope'); ?>
				<input id="<?php echo $this->get_field_id(self::PLACE); ?>" name="<?php echo $this->get_field_name(self::PLACE); ?>" type="checkbox" <?php echo $show_place ? 'checked="checked"' : ''; ?> />
			</label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id(self::PHONE); ?>"><?php _e('Show event phone:', 'churchope'); ?>
				<input id="<?php echo $this->get_field_id(self::PHONE); ?>" name="<?php echo $this->get_field_name(self::PHONE); ?>" type="checkbox" <?php echo $show_phone ? 'checked="checked"' : ''; ?> />
			</label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id(self::EMAIL); ?>"><?php _e('Show contact email:', 'churchope'); ?>
				<input id="<?php echo $this->get_field_id(self::EMAIL); ?>" name="<?php echo $this->get_field_name(self::EMAIL); ?>" type="checkbox" <?php echo $show_email ? 'checked="checked"' : ''; ?> />
			</label>
		</p>
		</div>
		<div style="clear:both;">&nbsp;</div>
		<?php
	}
	
	private function getDefaultValue()
	{
		return array(
			self::TITLE		=> __('Upcoming events', 'churchope'),
			self::CATEGORY	=> self::ALL,
			self::COUNT		=> 3,
			self::TIME		=> 'on',
			self::PLACE		=> 'on',
			self::PHONE		=> 'on',
			self::EMAIL		=> '',
			
		);
	}
	
	/**
	 * List of events categories
	 * @return array
	 */
	private function getEventsCategoryList()
	{
			return  get_terms(Custom_Posts_Type_Event::TAXONOMY);
	}

	/**
	 * Delete cache
	 * @global type $sitepress WPML plugin
	 * @param boolean $all - delete for all language cache
	 */
	public function deleteWidgetCache()
	{
		
		global $sitepress;

		if($sitepress && is_object($sitepress) &&  method_exists($sitepress, 'get_active_languages'))
		{
			foreach($sitepress->get_active_languages() as $lang)
			{

				if(isset($lang['code']))
				{
					delete_site_transient($this->getTransientId($lang['code']));
				}
			}
		}
		
		delete_site_transient($this->getTransientId()); // clear cache
	}

	/**
	 * Return cached widget data
	 * @return array
	 */
	public function getCachedWidgetData()
	{
		return  get_site_transient($this->getTransientId());
	}

	/**
	 * get cache expiration time
	 * @return int
	 */
	public function getExparationTime()
	{
		return self::EXPIRATION_HALF_HOUR;
	}

	/**
	 * Get cache id
	 * @return string
	 */
	public function getTransientId($code ='')
	{
		$key =  self::UPCOMING_TRANSIENT;

		if($code)
		{
			$key .= '_' . $code;
		}
		elseif($this->isWPML_PluginActive()) // wpml
		{
			$key .= '_' . ICL_LANGUAGE_CODE;
		}
		
		return $this->get_field_id( $key );
	}

	/**
	 * create upcomming widget data and set it to WP Transient cache
	 * @param array $instance
	 */
	public function reinitWidgetCache($instance)
	{
		set_site_transient($this->getTransientId(), $this->getNextEvents($instance), $this->getExparationTime());
	}
	
	public function getNextEvents($instance)
	{
		$result				= array();
		$category			= $instance[self::CATEGORY];
		$count				= $instance[self::COUNT];
		$upcoming_events	= array();
		
		$list = $this->getFeaturedEventsList();
		
		if($list && is_array($list) && count($list))
		{
				foreach($list as $event)
				{
					if( !is_null($event->time_left) )
					{

						/*
						 * or all or neaded category
						 */
						if( $category == self::ALL || has_term($category, Custom_Posts_Type_Event::TAXONOMY, $event->post_id))
						{
							$event = $this->completeEventData($event);
							
							if($event->is_repeat)
							{

								if($event->time_left > 0)
								{
									$upcoming_events[] = $this->getEventMainInfo($event);
									
									for($i = 0; $i<$count-1; $i++)
									{
										if($i === 0)
										{
											$to_next = $this->getTimeBeforeEvent($event->post_id, $event->time_left + $this->getTimezoneTime() /*+ time()*/);
										}
										else
										{
											$to_next = $this->getTimeBeforeEvent($event->post_id, $to_next + $this->getTimezoneTime()/*time()*/);
										}
										$upcoming_events[] = $this->getEventMainInfo($event, $to_next);
									}
								}
								else
								{
									for($i = 0; $i<(int)$count; $i++)
									{
										if($i == 0)
										{
											$to_next = $this->getTimeBeforeEvent($event->post_id);
										}
										else
										{
											$to_next = $this->getTimeBeforeEvent($event->post_id, $to_next + /*time()*/ $this->getTimezoneTime());
											
										}
										$upcoming_events[] = $this->getEventMainInfo($event, $to_next);
									}
								}
							}
							else
							{
								$upcoming_events[] = $this->getEventMainInfo($event);
							}
						}
					}
				}
		}
		
		if(is_array($upcoming_events) && count($upcoming_events))
		{
			$sorted_upcoming_events = $this->sortUpcomingEvents($upcoming_events);
			
			$result = array_slice($sorted_upcoming_events, 0, $count);
		}
		return $result;
	}
	
	private function completeEventData($event)
	{
		$id = $event->post_id;
		
		$event->title = get_the_title($id);
		$event->place = get_post_meta($id, SHORTNAME.self::EVENT_ADDRESS, true);
		$event->phone = get_post_meta($id, SHORTNAME.self::CONTACT_PHONE, true);
		$event->email = get_bloginfo('admin_email');
//		$event->email = get_post_meta($id, SHORTNAME.self::CONTACT_EMAIL, true);
		
		return $event;
	}
	
	
	/**
	 * Sort events list in increasing
	 * @param array $events
	 * @return type
	 */
	private function sortUpcomingEvents($events)
	{
		if(count($events))
		{
			usort($events, array($this, 'sort'));
			return $events;
		}
	}
	
	/**
	 * Callback for usort function
	 * @param object $a
	 * @param object $b
	 * @return int
	 */
	function sort($a, $b)
	{
		if($a->timestamp == $b->timestamp)
		{
			return 0;
		}
		
		return ($a->timestamp > $b->timestamp)?1:-1;
	}


	/**
	 * extract the desired data and add missing 
	 * @param object $event
	 * @param int $time_left
	 * @return \stdClass
	 */
	private function getEventMainInfo($event, $time_left = false)
	{
		if($time_left)
		{
			$timestamp = $this->getTimezoneTime() + $time_left;
		}
		else
		{
			$timestamp = $this->getTimezoneTime() + $event->time_left;
		}
		
		$data = new stdClass();
		$data->post_id		= $event->post_id;
		$data->day			= date('d',$timestamp);
		$data->month		= date('M',$timestamp);
		$data->year			= date('Y',$timestamp);
		$data->time			= $event->time;
		$data->title		= $event->title;
		$data->place		= $event->place;
		$data->phone		= $event->phone;
		$data->email		= $event->email;
		$data->timestamp	= $timestamp;

		return $data;
	}
	
	/**
	 * Get cached data and create it if not exist
	 * @param array $instance
	 * @return array-of-object
	 */
	private function getUpcomingEvents($instance)
	{
		/**
		 * @todo delete befor commit;
		 */
//		$this->deleteWidgetCache(); 
		// --------------------------
		$events = $this->getCachedWidgetData();

		if( false == $events || 'Error' == $events || empty($events) || $this->isFirstEventCome($events))
		{
			$this->reinitWidgetCache($instance);
		}
		else
		{
			return $events;
		}
		
		return $this->getCachedWidgetData();
	}
	
	/**
	 * Check is first event in cached data has been come
	 * @param array $events
	 * @return boolean
	 */
	private function isFirstEventCome($events)
	{
		if(is_array($events)&& count($events))
		{
			$first_upcoming_event = array_shift($events);
		
			return $first_upcoming_event->timestamp < $this->getTimezoneTime();
		}
		return false;
	}
}
?>
