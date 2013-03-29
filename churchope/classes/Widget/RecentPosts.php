<?php
/**
 *  Show recent posts
 */
class Widget_RecentPosts extends Widget_Default implements Widget_Interface_Cache
{
	const RECENT_POST_TRANSIENT = 'sDF12as';
	
	function __construct()
	{
		$this->setClassName('widget_recent_posts');
		$this->setName('Recent posts');
		$this->setDescription('Show recent posts');
		$this->setIdSuffix('recent-posts');
		parent::__construct();
		add_action('save_post', array(&$this, 'action_clear_widget_cache'));
	}
	
	function action_clear_widget_cache($postID)
	{
		if(get_post_type($postID) == 'post')
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

	function widget($args, $instance)
	{
		extract($args);

		$title = apply_filters('widget_title', $instance['title']);

		echo $before_widget;

		if ($title)
		{
			echo $before_title . $title . $after_title;
		}

		$recent_posts = $this->getRecentPosts($instance);
	
		if ($recent_posts->have_posts()) : ?>
        
        <ul>
        
         <?php   while ($recent_posts->have_posts()) : $recent_posts->the_post();
        
            ?>
        
            <li >
            
            
            
                   
                <a href="<?php the_permalink() ?>" title="<?php echo esc_attr(get_the_title() ? get_the_title() : get_the_ID()); ?>" class="imgborder">
                      <?php if ( has_post_thumbnail(get_the_ID())) { echo theme_post_thumbnail('recent_posts'); } else {echo '<span class="placeholder"><span></span></span>';}?>        
                </a>
              
            
            <div class="recent_txt">
            <a href="<?php the_permalink() ?>" title="<?php echo esc_attr(get_the_title() ? get_the_title() : get_the_ID()); ?>"><?php if ( get_the_title() ) the_title(); else the_ID(); ?></a>
            <p><?php echo  get_the_date(get_option('date_format')); ?></p>
            </div>
          </li>
        
            <?php endwhile; ?>
            </ul>
        <?php		endif; 

		echo $after_widget;
	}

	function update($new_instance, $old_instance)
	{
		$this->deleteWidgetCache();
		
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['number'] = abs(strip_tags($new_instance['number']));
		$instance['category'] = strip_tags($new_instance['category']);

		return $instance;
	}

	function form($instance)
	{

		// Defaults
		$defaults = array('title' => __('Recent posts', 'churchope'), 'number' => '5');
		$instance = wp_parse_args((array) $instance, $defaults);
		?>
		<div>
			<p>
				<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'churchope'); ?></label>
				<input id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $instance['title']; ?>" style="width:100%;" />
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('number'); ?>"><?php _e('Number of posts to show:', 'churchope'); ?></label>
				<input id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="text" value="<?php echo $instance['number']; ?>" style="width:100%;" />
			</p>
		</div>
		<div style="clear:both;">&nbsp;</div>
		<?php
	}
	
	private function getRecentPosts($instance)
	{
		if( false === ($post_list = $this->getCachedWidgetData()))
		{
			$this->reinitWidgetCache($instance);
		}
		else
		{
			return $post_list;
		}
		return $this->getCachedWidgetData();
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

	public function getCachedWidgetData()
	{
		return  get_site_transient($this->getTransientId());
	}

	public function getExparationTime()
	{
		return self::EXPIRATION_HOUR;
	}

	public function getTransientId($code = '')
	{
		$key = self::RECENT_POST_TRANSIENT;
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

	public function reinitWidgetCache($instance)
	{
		$number = (int) $instance['number'];
		$recent_posts = new WP_Query(array('posts_per_page' => $number, 'no_found_rows' => true, 'post_status' => 'publish', 'ignore_sticky_posts' => true));
		
		set_site_transient($this->getTransientId(), $recent_posts, $this->getExparationTime());
	}
}
?>
