<?php
/**
 * Show photos from Flickr.com 
 */
class Widget_Flickr extends Widget_Default
{
	protected $classname = 'widget_flickr';


	public function __construct()
	{
		$this->setClassName('widget_flickr');
		$this->setName('Flickr');
		$this->setDescription('Show photos from Flickr.com');
		$this->setIdSuffix('flickr');
		parent::__construct();
	}	
	
	function widget( $args, $instance ) {
		extract( $args );

		$title = apply_filters( 'widget_title', $instance['title'] );		
		$number = (int)$instance['number'];
		$user = $instance['user'];


		$args = array(			
			'number' => $number,
			'user' => $user,
			
		);

		echo $before_widget;

		if ( $title )
		{
			echo $before_title . $title . $after_title;
		}
		$__html = <<<HTML
			<div class="box">
		
				<script type="text/javascript"  src="http://www.flickr.com/badge_code_v2.gne?count={$number}&amp;display=latest&amp;size=s&amp;layout=x&amp;source=user&amp;user={$user}"></script>        
	
			</div>
HTML;
		echo $__html;
		echo $after_widget;
	}


	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title']	 = strip_tags( $new_instance['title'] );
		$instance['number']	 = strip_tags( $new_instance['number'] );
		$instance['user']	 = strip_tags( $new_instance['user'] );

		return $instance;
	}


	function form( $instance ) {

		// Defaults
		$defaults = array( 'title' => __( 'Flickr', 'churchope' ), 'number' => '4');
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>
		<div>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>">
				<?php _e( 'Title:', 'churchope' ); ?>
			</label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $instance['title']; ?>" style="width:100%;" />
		</p>
		
		
		<p>
			<label for="<?php echo $this->get_field_id( 'number' ); ?>">
				<?php _e( 'Number of photos:', 'churchope' ); ?>
			</label>
			<input id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="text" value="<?php echo $instance['number']; ?>" style="width:100%;" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'user' ); ?>">
				<?php _e( 'Flickr ID', 'churchope' ); ?> (<a href="http://www.idgettr.com" target="_blank">idGettr</a>) :
			</label>
			<input id="<?php echo $this->get_field_id( 'user' ); ?>" name="<?php echo $this->get_field_name( 'user' ); ?>" type="text" value="<?php $user =  (isset($instance['user'])) ? $user = $instance['user'] : $user = NULL; echo $user; ?>" style="width:100%;" />
		</p>
	
		</div>
		<div style="clear:both;">&nbsp;</div>
	<?php
	}
}
?>