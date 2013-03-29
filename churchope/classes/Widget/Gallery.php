<?php

/**
 * Show previews from gallery category 
 */
class Widget_Gallery extends Widget_Default implements Widget_Interface_Cache
{
	const GALLERY_POST_TRANSIENT = 'JH8wo0sd';
	
	public function __construct()
	{
		$this->setClassName('widget_gallery');
		$this->setName('From Gallery');
		$this->setDescription('Show previews from gallery category');
		$this->setIdSuffix('gallery');
		parent::__construct();
		add_action('save_post', array(&$this, 'action_clear_widget_cache'));
	}
	
	function action_clear_widget_cache($postID)
	{
		if(get_post_type($postID) == Custom_Posts_Type_Gallery::POST_TYPE)
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

	function widget( $args, $instance ) {
		extract( $args );

		$title = apply_filters( 'widget_title', $instance['title'] );		
		$wport = $this->getGalleries($instance);

		///HTML
		echo $before_widget;

		if ( $title )
		{
			echo $before_title . $title . $after_title;
		}
		
		if ($wport->have_posts()) : ?>
			<ul>
			<?php  while($wport->have_posts()) : $wport->the_post();?>
				<li class="<?php if( ($wport->current_post % 2 ) == 0  ) { echo("first");} ?>" >
					     
							<a href="<?php the_permalink() ?>" title="<?php echo the_title(); ?>" class="imgborder thumb">
								<?php if ( has_post_thumbnail()) { theme_post_thumbnail('gallery_widget'); } else {echo '<span class="placeholder"><span></span></span>';}?>        
							</a>
						
				</li>
			<?php endwhile; ?>
			</ul>
		<?php endif;
		
		wp_reset_postdata();   
		echo $after_widget;
	}


	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['number'] = strip_tags( $new_instance['number'] );
		$instance['category'] = strip_tags( $new_instance['category'] );
		$this->deleteWidgetCache();
	

		return $instance;
	}


	function form( $instance ) {

		// Defaults
		$gallery_terms = '';
		$defaults = array( 'title' => __( 'From gallery', 'churchope' ), 'number' => '4');
		$instance = wp_parse_args( (array) $instance, $defaults ); 
			
		$gallery_terms = get_terms(Custom_Posts_Type_Gallery::TAXONOMY);
			
		?>

		<div>
			<p>
				<label for="<?php echo $this->get_field_id( 'title' ); ?>">
					<?php _e( 'Title:', 'churchope' ); ?>
				</label>
				<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $instance['title']; ?>" style="width:100%;" />
			</p>
			<p>
				<label for="<?php echo $this->get_field_id( 'category' ); ?>" >
					<?php _e( 'Category of gallery:', 'churchope' ); ?>
				</label>
				<select name="<?php echo $this->get_field_name( 'category' ); ?>" id="<?php echo $this->get_field_id( 'category' ); ?>"  style="width:100%;">
					<option value="">None</option>
					<?php
					if($gallery_terms)
					{
						foreach ($gallery_terms as $cat)
						{
							if ($instance['category'] == $cat->slug)
							{
								$selected = "selected='selected'";
							}
							else
							{
								$selected = "";
							}
							echo "<option $selected value='" . $cat->slug . "'>" . $cat->name . "</option>";
						}
					}
					?>
				</select>
			</p>
			<p>
				<label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php _e( 'Number of items to show:', 'churchope' ); ?></label>
				<input id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="text" value="<?php echo $instance['number']; ?>" style="width:100%;" />
			</p>
	
		</div>
		<div style="clear:both;">&nbsp;</div>
	<?php
	}
	
	private function getGalleries($instance)
	{
		if( false === ($gallery = $this->getCachedWidgetData()))
		{
			$this->reinitWidgetCache($instance);
		}
		else
		{
			return $gallery;
		}
		return $this->getCachedWidgetData();
	}

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
		$key = self::GALLERY_POST_TRANSIENT;
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
		$number		= (int)$instance['number'];
		$category	= $instance['category'];
		
		$wport = new WP_Query("post_type=".Custom_Posts_Type_Gallery::POST_TYPE."&".Custom_Posts_Type_Gallery::TAXONOMY."=".$category."&post_status=publish&posts_per_page=".$number."&order=DESC");
		set_site_transient($this->getTransientId(), $wport, $this->getExparationTime());
	}
}
?>