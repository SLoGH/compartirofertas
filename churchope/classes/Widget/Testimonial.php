<?php


class Widget_Testimonial extends Widget_Default implements Widget_Interface_Cache
{
	/**
	 * Form fields 
	 */
	const TIME			= 'time';
	const TITLE			= 'title';
	const CATEGORY		= 'category';
	const RANDOMIZE		= 'randomize';
	const EFFECT		= 'effect';
	const TESTIMONIAL_POST_TRANSIENT = 'JkH83gha903';
	
	
	
	public function __construct()
	{
		$this->setClassName('widget_testimonial');
		$this->setName('Testimonials');
		$this->setDescription('Show Testimonials');
		$this->setIdSuffix('testimonials');
		parent::__construct();
		add_action('save_post', array(&$this, 'action_clear_widget_cache'));
	}
	
	function action_clear_widget_cache($postID)
	{
		if(get_post_type($postID) == Custom_Posts_Type_Testimonial::POST_TYPE)
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
		extract( $args );

		$title		= apply_filters( 'widget_title', $instance[self::TITLE] );		
		$time		= (int) $instance[self::TIME];
		
		$wport = $this->getTestimonials($instance);	
		$have_posts = $wport->have_posts();
		/////////////////////////////////html 
		echo $before_widget;
		if ( $title )
		{
			echo $before_title . $title . $after_title;
		}
		if ($have_posts ) : ?>
			<?php if($wport->post_count > 1):?>
				<div class="controls">
					<a class="prev" href="#">Previous</a>
					<a class="next" href="#">Next</a>
				</div>
			<?php endif;?>
				<div class="jcycle">
					<?php  while($wport->have_posts()) : $wport->the_post();?>
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
		echo $after_widget;
		
		
		if($wport->found_posts < 2)
		{
			$randomize = false;
		}
		else
		{
			$randomize = $instance[self::RANDOMIZE];
		}
		
		if($have_posts && $wport->post_count > 1)
		{
			self::printWidgetJS($args['widget_id'], $instance[self::EFFECT], $randomize, $time);
		}
		wp_reset_postdata();
	}
	
	function form($instance)
	{
		$defaults = array( 
			self::TITLE		=> __( 'Testimonials', 'churchope' ),
			self::TIME		=> '10',
			self::CATEGORY	=> 'all',
			self::RANDOMIZE => '',
			self::EFFECT	=> 'fade');
		
		$testimonial_category = null;
		$instance = wp_parse_args( (array) $instance, $defaults ); 

		$testimonial_category = get_terms(Custom_Posts_Type_Testimonial::TAXONOMY);
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
					<?php _e( 'Category of testimonials:', 'churchope' ); ?>
				</label>
				<select name="<?php echo $this->get_field_name( self::CATEGORY ); ?>" id="<?php echo $this->get_field_id( self::CATEGORY ); ?>"  style="width:100%;">
					<option value="all">All</option>
					<?php
					if($testimonial_category)
					{
						foreach ($testimonial_category as $cat)
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
				<label for="<?php echo $this->get_field_id( self::TIME ); ?>"><?php _e( 'Number of second to show:', 'churchope' ); ?></label>
				<input id="<?php echo $this->get_field_id( self::TIME ); ?>" name="<?php echo $this->get_field_name( self::TIME ); ?>" type="text" value="<?php echo $instance[self::TIME]; ?>" style="width:100%;" />
			</p>
			<p>
				<label for="<?php echo $this->get_field_id(self::RANDOMIZE); ?>"><?php _e('Randomize testimonial:', 'churchope'); ?>
					<input id="<?php echo $this->get_field_id(self::RANDOMIZE); ?>"
					   name="<?php echo $this->get_field_name(self::RANDOMIZE); ?>"
					   type="checkbox" <?php echo esc_attr(isset($instance[self::RANDOMIZE]) && $instance[self::RANDOMIZE]) ? 'checked="checked"' : ''; ?> />
				</label>
			</p>
			<p>
				<label for="<?php echo $this->get_field_id( self::EFFECT ); ?>" >
					<?php _e( 'Transition effect:', 'churchope' ); ?>
				</label>
				<select name="<?php echo $this->get_field_name( self::EFFECT ); ?>" id="<?php echo $this->get_field_id( self::EFFECT ); ?>"  style="width:100%;">
					<option value="all">All</option>
					<?php
						foreach (self::getEffectList() as $effect => $descr)
						{
							$selected = "";
							if ($instance[self::EFFECT] == $effect)
							{
								$selected = "selected='selected'";
							}
							echo "<option $selected value='" . $effect . "'>" . __($descr, 'churchope') . "</option>";
						}?>
				</select>
			</p>
		</div>
		<div style="clear:both;">&nbsp;</div>
	<?php
	}
	
	function update($new_instance, $old_instance)
	{
		$instance = $old_instance;
		
		$instance[self::CATEGORY]	= strip_tags( $new_instance[self::CATEGORY] );
		$instance[self::EFFECT]		= strip_tags( $new_instance[self::EFFECT] );
		$instance[self::RANDOMIZE]	= strip_tags( $new_instance[self::RANDOMIZE] );
		$instance[self::TIME]		= strip_tags( $new_instance[self::TIME] );
		$instance[self::TITLE]		= strip_tags( $new_instance[self::TITLE] );
		$this->deleteWidgetCache();
		return $instance;
	}
	
	/**
	 * Js code for testimonialwidget work
	 * @param string $widget_id widget div id
	 * @param string $effect one of cycle effect.
	 * @param string $randomize - set randomize to cycle plugin
	 * @param int $interval interval in second 
	 */
	static function printWidgetJS($widget_id, $effect, $randomize, $interval = 10)
	{?>
		<script type="text/javascript">
			jQuery(document).ready(function() {
						var conf = {
								fx: '<?php echo $effect?>',
								timeout: <?php echo $interval * 1000  ?>,
								next: '#<?php echo $widget_id ?> .prev',
								prev: '#<?php echo $widget_id ?> .next',
								before: animateHeight,
								slideResize: true,
								containerResize: false,
								width: '100%',
								fit: 1	
							};
						<?php if($randomize):?>
							conf.random = true;
						<?php endif;?>
							
						if(jQuery('#<?php echo $widget_id ?> .jcycle').length){
							jQuery('#<?php echo $widget_id ?> .jcycle').cycle(conf);
						}
						
						function animateHeight(currElement, nextElement, opts, isForward) { 							

								jQuery(nextElement).closest('.jcycle').animate({height:jQuery(nextElement).innerHeight()});	
						}
						
					});
		</script><?php
		wp_enqueue_script('jcycle');
	}

	/**
	 * JS cycle effect list
	 */
	static function getEffectList()
	{
		// Effect name => Description.
		return array(
				'blindX'		=> 'Blind X',
				'blindY'		=> 'Blind Y',
				'blindZ'		=> 'Blind Z',
				'cover'			=> 'Cover',
				'curtainX'		=> 'Curtain X',
				'curtainY'		=> 'Curtain Y',
				'fade'			=> 'Fade',
				'fadeZoom'		=> 'Fade Zoom',
				'growX'			=> 'Grow X',
				'growY'			=> 'Grow Y', 
				'none'			=> 'None',
				'scrollUp'		=> 'Scroll UP',
				'scrollDown'	=> 'Scroll DOWN',
				'scrollLeft'	=> 'Scroll Left',
				'scrollRight'	=> 'Scroll Right',
				'scrollHorz'	=> 'Scroll Horz',
				'scrollVert'	=> 'Scroll Vert',
				'shuffle'		=> 'Shuffle',
				'slideX'		=> 'Slide X',
				'slideY'		=> 'Slide Y',
				'toss'			=> 'Toss',
				'turnUp'		=> 'Turn Up',
				'turnDown'		=> 'Turn Down',
				'turnLeft'		=> 'Turn Left',
				'turnRight'		=> 'Turn Right',
				'uncover'		=> 'Uncover',
				'wipe'			=> 'Wipe',
				'zoom'			=> 'Zoom',
		);
	}
	
	private function getTestimonials($instance)
	{
		if( false === ($testimonials = $this->getCachedWidgetData()))
		{
			$this->reinitWidgetCache($instance);
		}
		else
		{
			return $testimonials;
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
		$key = self::TESTIMONIAL_POST_TRANSIENT;
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
		$query		= "post_type=".Custom_Posts_Type_Testimonial::POST_TYPE."&post_status=publish&posts_per_page=100&order=DESC";
		$category	= $instance[self::CATEGORY];
		
		
		if($category != 'all')
		{
			$query .="&".Custom_Posts_Type_Testimonial::TAXONOMY."=".$category;
		}
		$wport = new WP_Query($query);
		
		set_site_transient($this->getTransientId(), $wport, $this->getExparationTime());
	}
}?>