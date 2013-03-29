<?php
class Widget_Twitter extends Widget_Default implements Widget_Interface_Cache
{
	const TWITTER_TRANSIENT = 'sdf_2L4d';
	
	function __construct()
	{
		$this->setClassName('widget_twitter');
		$this->setName('Twitter');
		$this->setDescription('Show latest tweets');
		$this->setIdSuffix('twitter');
		parent::__construct();
	}

	function widget($args, $instance)
	{
		extract($args);
		$title = esc_attr($instance['title']);
		$username = $instance['username'];
		$num = $instance['num'];
		$update = $instance['update'];
		$extractLinks = $instance['hyperlinks'];
		$extractUsers = $instance['twitter_users'];
		$encode = $instance['encode_utf8'];

		if (empty($username))
		{
			return false;
		}
		$aMessage = $this->getTwitterFeedItems($instance);
		if ($aMessage == "Error")
		{
			$content = '<p class="notice">' . __('Can\'t connect to twitter.', 'churchope') . '</p>';
		}
		else
		{
			if (empty($aMessage))
			{
				$content = '<p class="notice">' . __('There are no public messages.', 'churchope') . '</p>';
			}
			else
			{
				$content = $this->generateMessageOutput($aMessage, $update, $username);
			}
		}



		echo $before_widget;

		if ($title)
		{
			echo $before_title . $title . $after_title;
		}
		echo $content;

		echo $after_widget;
	}
	
	private function getTwitterFeedItems($instance)
	{
		$feeds_items = $this->getCachedWidgetData();

		if( false == $feeds_items || 'Error' == $feeds_items || empty($feeds_items))
		{
			$this->reinitWidgetCache($instance);
		}
		else
		{
			return $feeds_items;
		}
		
		return $this->getCachedWidgetData();
	}
	
	public function reinitWidgetCache($instance)
	{
		$username		= $instance['username'];
		$num			= $instance['num'];
		$extractLinks	= $instance['hyperlinks'];
		$extractUsers	= $instance['twitter_users'];
		$encode			= $instance['encode_utf8'];

		$oFeed = new Widget_Twitter_Feed();
		$feedsItems = $oFeed->getFeedItems($username, $num, $encode, $extractLinks, $extractUsers);
		
		set_site_transient($this->getTransientId(), $feedsItems, $this->getExparationTime());
	}
	
	/**
	 * Cached twitter feed items
	 * @return false|array 
	 */
	function getCachedWidgetData()
	{
		return  get_site_transient($this->getTransientId());
	}
	
	function getTransientId()
	{
		return $this->get_field_id( self::TWITTER_TRANSIENT );
	}
	
	

	protected function generateMessageOutput($aMessage, $update = true,  $username)
	{

		$output = '<ul class="tweet_list">';

		foreach ($aMessage as $item)
		{
			$content = $item['description'];
			$link = $item['link'];

			$output .= sprintf(
					'<li class="twitter-item">%s%s</li>', $content, $update ? sprintf('<a href="%s" class="twitter-date">%s</a>', $link, $item['date-posted']) : ''
			);
		}
		$output .= '</ul>';
		
		return $output;
	}

	function update($new_instance, $old_instance)
	{
		$this->deleteWidgetCache();
		$instance = $old_instance;
		$instance['title']		= strip_tags($new_instance['title']);
		$instance['username']	= strip_tags($new_instance['username']);
		$instance['num']		= strip_tags($new_instance['num']);
		$instance['update']		= strip_tags($new_instance['update']);
		$instance['linked']		= strip_tags($new_instance['linked']);
		$instance['hyperlinks'] = strip_tags($new_instance['hyperlinks']);
		$instance['twitter_users'] = strip_tags($new_instance['twitter_users']);
		$instance['encode_utf8'] = strip_tags($new_instance['encode_utf8']);
		
		return $instance;
	}

	function form($instance)
	{


		// Defaults
		$defaults = array('title' => __('Twitter', 'churchope'), 'username' => 'themoholics', 'num' => '3',  'update' => '1', 'linked' => '1', 'hyperlinks' => '1', 'twitter_users' => '1', 'encode_utf8' => '1');
		$instance = wp_parse_args((array) $instance, $defaults);

		$title = esc_attr($instance['title']);
		$username = esc_attr($instance['username']);
		$number = esc_attr($instance['num']);
		$update = esc_attr($instance['update']);
		$linked = esc_attr($instance['linked']);
		$hyperlinks = esc_attr($instance['hyperlinks']);
		$twitter_users = esc_attr($instance['twitter_users']);
		$encode = esc_attr($instance['encode_utf8']);
		?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'churchope'); ?>
				<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
			</label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('username'); ?>"><?php _e('Twitter username:', 'churchope'); ?>
				<input class="widefat" id="<?php echo $this->get_field_id('username'); ?>" name="<?php echo $this->get_field_name('username'); ?>" type="text" value="<?php echo $username; ?>" />
			</label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('num'); ?>"><?php _e('Number of tweets:', 'churchope'); ?>
				<input class="widefat" id="<?php echo $this->get_field_id('num'); ?>" name="<?php echo $this->get_field_name('num'); ?>" type="text" value="<?php echo $number; ?>" />
			</label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('update'); ?>"><?php _e('Show date posted:', 'churchope'); ?>
				<input id="<?php echo $this->get_field_id('update'); ?>" name="<?php echo $this->get_field_name('update'); ?>" type="checkbox" <?php echo $update ? 'checked="checked"' : ''; ?> />
			</label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('hyperlinks'); ?>"><?php _e('Discover hyperlinks:', 'churchope'); ?>
				<input id="<?php echo $this->get_field_id('hyperlinks'); ?>" name="<?php echo $this->get_field_name('hyperlinks'); ?>" type="checkbox" <?php echo $hyperlinks ? 'checked="checked"' : ''; ?> />
			</label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('twitter_users'); ?>"><?php _e('Discover @replies:', 'churchope'); ?>
				<input id="<?php echo $this->get_field_id('twitter_users'); ?>" name="<?php echo $this->get_field_name('twitter_users'); ?>" type="checkbox" <?php echo $twitter_users ? 'checked="checked"' : ''; ?> />
			</label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('encode_utf8'); ?>"><?php _e('UTF8 Encode:', 'churchope'); ?>
				<input id="<?php echo $this->get_field_id('encode_utf8'); ?>" name="<?php echo $this->get_field_name('encode_utf8'); ?>" type="checkbox" <?php echo $encode ? 'checked="checked"' : ''; ?> />
			</label>
		</p>
		<?php
	}
	
	function getExparationTime()
	{
		return self::EXPIRATION_HALF_HOUR;
	}
	
	function deleteWidgetCache()
	{
		delete_site_transient($this->getTransientId()); // clear cache
	}
	
		
}
?>
