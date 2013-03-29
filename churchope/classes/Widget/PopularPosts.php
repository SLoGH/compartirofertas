<?php
/**
 * Popular Post Widget
 */
class Widget_PopularPosts extends Widget_Default implements Widget_Interface_Cache
{
	const TITLE		= 'title';	// Widget Title
	const NUMBER	= 'number';	// Number of posts to show

	const POPULARPOST_TRANSIENT = 'qCdf[rp';
	
	function __construct()
	{
		$this->setClassName('widget_popular_posts');
		$this->setName('Popular posts');
		$this->setDescription('Show popular posts');
		$this->setIdSuffix('popular-posts');
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
		global $post;
		$shown_post = 1;		// count how many pages it has been shown
		$current_post_id = -1;	// 
		if (is_single())
		{
			$current_post_id = $post->ID; // 
		}
		
		$popular_posts = $this->getPopularPosts($instance);
		
		if(isset($instance[self::TITLE]))
		{
			$title = apply_filters( 'widget_title', $instance[self::TITLE] );			
		}	
		
		echo $args['before_widget'];
		if ( $title )
		{
			echo $args['before_title'] . $title . $args['after_title'];
		}
		
		if($popular_posts !== false):?>
			<ul>
				<?php while ($popular_posts->have_posts()): $popular_posts->the_post(); ?>
					<?php if($current_post_id != get_the_ID() && $shown_post <= $instance[self::NUMBER]):?>
						<li>
							<a href="<?php echo get_permalink(get_the_ID()); ?>" title="<?php the_title(); ?>" class="imgborder">
								<?php
								if (has_post_thumbnail(get_the_ID()))
								{
									get_theme_post_thumbnail(get_the_ID(), 'recent_posts');
								}
								else {echo '<span class="placeholder"><span></span></span>';}
								?>        
							</a>
							<div class="recent_txt">
								<a href="<?php echo get_permalink(get_the_ID()); ?>" title="<?php the_title(); ?>" ><?php the_title(); ?></a> 
								<?php if (comments_open()) { echo "<p>"; comments_popup_link(__('0 - Comments', 'churchope'), __('1 - Comment', 'churchope'), __('% - Comments', 'churchope'), 'comments'); echo "</p>";} ?>
							</div>
						</li>
						<?php $shown_post++;
					endif;?>
				<?php endwhile;?>
			</ul>
		<?php
		endif;
		echo $args['after_widget'];
		wp_reset_postdata();
	}

	function update($new_instance, $old_instance)
	{
		$this->deleteWidgetCache();
		
		$instance = $old_instance;
		$instance[self::TITLE] = strip_tags(trim($new_instance[self::TITLE]));
		$instance[self::NUMBER] = strip_tags(trim($new_instance[self::NUMBER]));

		return $instance;
	}

	function form($instance)
	{
		$instance = wp_parse_args((array) $instance, $this->getDefaultFieldValues());
		?>
		<div>
			<p>
				<label for="<?php echo $this->get_field_id(self::TITLE); ?>"><?php _e('Title:', 'churchope'); ?></label>
				<input id="<?php echo $this->get_field_id(self::TITLE); ?>" name="<?php echo $this->get_field_name(self::TITLE); ?>" type="text" value="<?php echo $instance[self::TITLE]; ?>" style="width:100%;" />
			</p>
			<p>
				<label for="<?php echo $this->get_field_id(self::NUMBER); ?>"><?php _e('Number of posts to show:', 'churchope'); ?></label>
				<input id="<?php echo $this->get_field_id(self::NUMBER); ?>" name="<?php echo $this->get_field_name(self::NUMBER); ?>" type="text" value="<?php echo $instance[self::NUMBER]; ?>" style="width:100%;" />
			</p>
		</div>
		<div style="clear:both;">&nbsp;</div>
		<?php
	}
	
	/**
	 * Default field values
	 * @return array 
	 */
	private function getDefaultFieldValues()
	{
		$default_values = array(
					self::TITLE		=> __('Popular posts', 'churchope'),
					self::NUMBER	=> 5,
					);
		
		return $default_values;
	}
	
	/**
	 * Return list of popular post
	 * @param array $instance
	 * @return object 
	 */
	private function getPopularPosts($instance)
	{
		if( false === ($post_list = $this->getCachedWidgetData()) || !$post_list->have_posts())
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
	 * cached query result 
	 * @return false|mix
	 * @see http://codex.wordpress.org/Transients_API
	 */
	function getCachedWidgetData()
	{
		return get_site_transient($this->getTransientId());
	}
	
	/**
	 * Reinit cache result of popular page
	 * @param array $instance widget saved data
	 * @see http://codex.wordpress.org/Transients_API
	 */
	function reinitWidgetCache($instance)
	{
		$posts_per_page = 0;
		if(isset($instance[self::NUMBER]) && $instance[self::NUMBER] > 0)
		{
			$posts_per_page = $instance[self::NUMBER];
		}
		$args = array(
			'orderby'				=> 'comment_count',
			'posts_per_page'		=> $posts_per_page + 1,	
			'ignore_sticky_posts'	=> 1
		);
		$popular_query = new WP_Query($args);
		
		if(isset($popular_query->posts) && count ($popular_query->posts))
		{
			set_site_transient( $this->getTransientId(), $popular_query, $this->getExparationTime() );
		}
	}
	
	/**
	 * Default field values
	 * @param string $field_id Field Id
	 * @return mixed
	 */
	function getDefaultFieldValueByID($field_id)
	{
		$list = $this->getDefaultFieldValues();
		if(isset($list[$field_id]))
		{
			return $list[$field_id];
		}
		return false;
	}
	
	public function getTransientId($code = '')
	{
		$key = self::POPULARPOST_TRANSIENT;
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
	
	function getExparationTime()
	{
		return self::EXPIRATION_HOUR;
	}

	/**
	 * Delete cache
	 * @global type $sitepress WPML plugin
	 * @param boolean $all - delete for all language cache
	 */
	function deleteWidgetCache()
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
}
?>
