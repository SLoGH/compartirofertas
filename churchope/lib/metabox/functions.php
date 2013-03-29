<?php

// add select_sidebar rendering function.
add_action( 'cmb_render_select_sidebar', 'render_select_sidebar', 10, 2 );

/**
 * Render select_sidebar element
 * @param array $field [name, desc, id ...]
 * @param string $current_meta_value current element value
 */
function render_select_sidebar($field, $current_meta_value )
{
	$sidebars = getSidebarsList();
	if (is_array($sidebars) && !empty($sidebars))
	{
		$html = "<select name=".$field['id']." id=".$field['id'].">";
		foreach ($sidebars as $sidebar)
		{
			if ($current_meta_value == $sidebar['value'])
			{
				$html .= "<option value=".$sidebar['value']." selected='selected'>".$sidebar['name']."</option>\n";
			}
			else
			{
				 $html .= "<option value=".$sidebar['value']." >".$sidebar['name']."</option>\n";
			}
		}
	}
	echo $html;
}

add_filter( 'cmb_meta_boxes', 'cmb_sample_metaboxes' );

/**
 * Array of options with custom icon if exist.
 * @return array
 */
function getIconsList()
{
	$option_list = array();
	$default_icons = array('i_video.png','i_text.png','i_more.png','i_zoom.png','i_audio.png');
	
	$dir = get_template_directory_uri() . '/lib/metabox/images/';
	
	$option_list[] = array('value'=>'', 'name'=> 'Use global');
	
	foreach($default_icons as $icon)
	{
		$option_list[] = array('value'=>$dir . $icon, 'name'=> '<img src="'. $dir . $icon .'" style="max-width:50px;max-height:50px" alt="Post icon" /> ');
	}
	
	$custom_icons_option = get_option(SHORTNAME . Admin_Theme_Item_Galleries::CUSTOM_GALLERY_ICONS);

	if($custom_icons_option)
	{
		$custom_icons_list = unserialize($custom_icons_option);
		if(is_array($custom_icons_list) && count($custom_icons_list))
		{
			foreach($custom_icons_list as $icon)
			{
				$option_list[] = array('value'=>$icon, 'name'=> '<img src="'. $icon .'" style="max-width:50px;max-height:50px" alt="Post icon" /> ');
			}
		}
	}
	return $option_list;
}

function getSidebarsList()
{
	$sidebar_list = array();
	$sidebar_list[] = array('name'=>'Use global sidebar', 'value'=>'""');
	$sidebars = Sidebar_Generator::get_sidebars();
	if (is_array($sidebars) && !empty($sidebars))
	{
		foreach ($sidebars as $sidebar)
		{
			$sidebar_list[] = array('name'=>$sidebar, 'value'=>$sidebar);
		}
	}
	return $sidebar_list;
}

/**
 * Define the metabox and field configurations.
 *
 * @param  array $meta_boxes
 * @return array
 */
function cmb_sample_metaboxes( array $meta_boxes ) {
	
	$meta_boxes[] = array(
		'id'         => 'page_sidebar',
		'title'      => 'Custom sidebar',
		'pages'      => array('page'), // Page type
		'context'    => 'side',
		'priority'   => 'low',
		'show_names' => false, // Show field names on the left
		'fields'     => array(
			array(
				'name'    => 'Sidebar',
				'desc'    => 'Sidebar to display',
				'id'      => SHORTNAME . '_page_sidebar',
				'type'    => 'select_sidebar',
            ),
		),
	);
	
	$meta_boxes[] = array(
		'id'         => 'page_title',
		'title'      => 'Title area settings',
		'pages'      => array('page','post', Custom_Posts_Type_Gallery::POST_TYPE,
								Custom_Posts_Type_Gallery::POST_TYPE,
								Custom_Posts_Type_Event::POST_TYPE), // Page type
		'context'    => 'normal',
		'priority'   => 'high',
		'show_names' => true, // Show field names on the left
		'fields'     => array(
			array(
				'name'    => 'Title additional text',
				'desc'    => 'Extra text on right side of title. HTML markup supported.',
				'id'      => SHORTNAME . '_page_extratitle',
				'type'    => 'textarea',
            ),
			array(
				'name'    => 'Under title sidebar instance',
				'desc'    => 'Sidebar to display under title on gray line.',
				'id'      => SHORTNAME . '_title_sidebar',
				'type'    => 'select_sidebar',
            ),
		),
	);
	
	$meta_boxes[] = array(
		'id'         => 'testimonial_box',
		'title'      => 'Testimonial Options',
		'pages'      => array(Custom_Posts_Type_Testimonial::POST_TYPE), // Post type
		'context'    => 'normal',
		'priority'   => 'high',
		'show_names' => true, // Show field names on the left
		'fields'     => array(
			array(
				'name' => 'Author',
				'desc' => 'Testimonial Author',
				'id'   => SHORTNAME . '_testimonial_author',
				'type' => 'text',
			),
			array(
				'name' => 'Job',
				'desc' => 'Testimonial Author Job',
				'id'   => SHORTNAME . '_testimonial_author_job',
				'type' => 'text',
			),
			
			
		),
	);
	
	// Custom page lightBox
	$meta_boxes[] = array(
		'id'         => 'light_box',
		'title'      => 'LightBox Options',
		'pages'      => array(  Custom_Posts_Type_Gallery::POST_TYPE), // Post type
		'context'    => 'normal',
		'priority'   => 'high',
		'show_names' => true, // Show field names on the left
		'fields'     => array(
			array(
				'name' => 'Use lightbox',
				'desc' => 'Use LightBox for preview thumbnail',
				'id'   => SHORTNAME . '_use_lightbox',
				'type' => 'checkbox',
			),
			array(
				'name' => 'URL',
				'desc' => 'Custom URL LightBox',
				'id'   => SHORTNAME . '_url_lightbox',
				'type' => 'text',
			),
			
		),
	);
	
	$meta_boxes[] = array(
		'id'         => 'event_date_option',
		'title'      => 'Event date',
		'pages'      => array(Custom_Posts_Type_Event::POST_TYPE), // Post type
		'context'    => 'normal',
		'priority'   => 'high',
		'show_names' => true, // Show field names on the left
		'fields'     => array(
			array(
				'name' => 'Event Date',
				'desc' => 'Event will happen',
				'id'   => SHORTNAME . Widget_Event::EVENT_DATE_META_KEY,
				'type' => 'text_date',
			),
			array(
	            'name' => 'Event Time',
	            'desc' => 'Event start at',
	            'id'   => SHORTNAME . Widget_Event::EVENT_TIME_META_KEY,
	            'type' => 'text_time',
	        ),
			array(
				'name' => 'Is Repeating?',
				'desc' => 'Is this event repetition?',
				'id'   => SHORTNAME . Widget_Event::EVENT_REPEATING_META_KEY,
				'type' => 'checkbox',
			),
			array(
				'name'    => 'Repeat every:',
				'desc'    => 'Repetition interval',
				'id'      => SHORTNAME . Widget_Event::EVENT_INTERVAL_META_KEY,
				'type'    => 'select',
				'options' => array(
					array( 'value' => Widget_Event::INTERVAL_DAY,	'name' => 'Every day'),
					array( 'value' => Widget_Event::INTERVAL_WEEK,	'name' => 'Every week(initial day of week)'),
					array( 'value' => Widget_Event::INTERVAL_MONTH,	'name' => 'Evert month(initial day of month)'),
					array( 'value' => Widget_Event::INTERVAL_YEAR,	'name' => 'Every year(initial day of month)'),
				),
			),
			
		),
	);
	
	
	$meta_boxes[] = array(
		'id'         => 'event_additional_option',
		'title'      => 'Event Additional Information',
		'pages'      => array(Custom_Posts_Type_Event::POST_TYPE), // Post type
		'context'    => 'normal',
		'priority'   => 'high',
		'show_names' => true, // Show field names on the left
		'fields'     => array(
			array(
				'name' => 'Phone',
				'desc' => 'Contact phone',
				'id'   => SHORTNAME . Widget_Event_Upcoming::CONTACT_PHONE,
				'type' => 'text',
			),
//			array(
//				'name' => 'Email',
//				'desc' => 'Contact Email',
//				'id'   => SHORTNAME . Widget_Event_Upcoming::CONTACT_EMAIL,
//				'type' => 'text',
//			),
			array(
				'name' => 'Address',
				'desc' => 'Exact Address',
				'id'   => SHORTNAME . Widget_Event_Upcoming::EVENT_ADDRESS,
				'type' => 'text',
			),
			
		),
	);
	
	//Custom page Gallery Option
	$meta_boxes[] = array(
		'id'         => 'gallery_option',
		'title'      => 'Gallery Options',
		'pages'      => array( Custom_Posts_Type_Gallery::POST_TYPE), // Post type
		'context'    => 'normal',
		'priority'   => 'high',
		'show_names' => true, // Show field names on the left
		'fields'     => array(
			array(
				'name' => 'Live URL',
				'desc' => ' Live URL',
				'id'   => SHORTNAME . '_gallery_url',
				'type' => 'text',
			),
			array(
				'name' => 'Live URL button text',
				'desc' => ' Live URL button text',
				'id'   => SHORTNAME . '_gallery_url_button',
				'type' => 'text_medium',
			),
			array(
				'name' => 'Live URL target at new window',
				'desc' => 'Live URL  _blank ',
				'id'   => SHORTNAME . '_gallery_target',
				'type' => 'checkbox',
			),
			array(
				'name' => 'Hide more',
				'desc' => ' Hide more button from preview',
				'id'   => SHORTNAME . '_gallery_hide_more',
				'type' => 'checkbox',
			),
			array(
				'name' => 'Hide feature image',
				'desc' => ' Hide feature image from single gallery post',
				'id'   => SHORTNAME . '_gallery_hide_thumb',
				'type' => 'checkbox',
			),
			array(
				'name'    => 'Preview icon',
				'desc'    => '',
				'id'      => SHORTNAME . '_gallery_icon',
				'type'    => 'radio_inline',
				'options' => getIconsList(),
			),
		),
	);
	
	$meta_boxes[] = array(
		'id'         => 'layout_type',
		'title'      => 'Layout Type',
		'pages'      => array( 'post', Custom_Posts_Type_Gallery::POST_TYPE,
								Custom_Posts_Type_Testimonial::POST_TYPE,
								Custom_Posts_Type_Event::POST_TYPE), // Post type
		'context'    => 'side',
		'priority'   => 'low',
		'show_names' => true, // Show field names on the left
		'fields'     => array(
			array(
				'name'    => 'Template',
				'desc'    => '',
				'id'      => SHORTNAME . '_post_layout',
				'type'    => 'select',
				'options' => array(
					array( 'name' => 'Use global', 'value' => '', ),
					array( 'name' => 'Full width', 'value' => 'layout_none_sidebar', ),
					array( 'name' => 'Left sidebar', 'value' => 'layout_left_sidebar', ),
					array( 'name' => 'Right sidebar', 'value' => 'layout_right_sidebar', ),
				),
			),
			array(
				'name'    => 'Sidebar',
				'desc'    => 'Sidebar to display',
				'id'      => SHORTNAME . '_post_sidebar',
				'type'    => 'select_sidebar',
            ),
			
		),
	);
	
	//Slideshow 
	$meta_boxes[] = array(
		'id'         => 'slide_link',
		'title'      => 'Link current slide to',
		'pages'      => array(  Custom_Posts_Type_Slideshow::POST_TYPE), // Post type
		'context'    => 'normal',
		'priority'   => 'high',
		'show_names' => true, // Show field names on the left
		'fields'     => array(
			array(
				'name'    => 'Write your URL',
				'desc'    => 'Future image and call to action button wil be linked to that URL.',
				'id'      => SHORTNAME . '_sliders_link',
				'type'    => 'text'
			),			
		),
	);
	
	$meta_boxes[] = array(
		'id'         => 'jcycle_options',
		'title'      => 'Options for jCycle slider',
		'pages'      => array(  Custom_Posts_Type_Slideshow::POST_TYPE), // Post type
		'context'    => 'normal',
		'priority'   => 'high',
		'show_names' => true, // Show field names on the left
		'fields'     => array(			
			array(
				'name'    => 'Slide layout',
				'desc'    => '',
				'id'      => SHORTNAME . '_sliders_cycle_position',
				'type'    => 'select',
				'options' => array(
					array( 'name' => 'Left content', 'value' => 'right'),
					array( 'name' => 'Right content', 'value' => 'left', ),					
					array( 'name' => 'Only image', 'value' => 'full', ),
					array( 'name' => 'Full width content', 'value' => 'empty'),										
				),
			),
			array(
				'name'    => 'Inner content align',
				'desc'    => '',
				'id'      => SHORTNAME . '_sliders_cycle_content_align',
				'type'    => 'select',
				'options' => array(
					array( 'name' => 'Left align', 'value' => ''),
					array( 'name' => 'Right align', 'value' => 'right', ),					
					array( 'name' => 'Center align', 'value' => 'center', ),														
				),
			),
			array(
				'name'    => 'Hide title',
				'desc'    => '',
				'id'      => SHORTNAME . '_sliders_cycle_title',
				'type'    => 'checkbox'
			),
			array(
				'name'    => 'Hide feature image frame for content',
				'desc'    => '',
				'id'      => SHORTNAME . '_sliders_cycle_frame',
				'type'    => 'checkbox'
			),
			array(
				'name'    => 'Text for call to action button',
				'desc'    => 'It will display only if this option active.',
				'id'      => SHORTNAME . '_sliders_cycle_btntxt',
				'type'    => 'text'
			),	
			array(
				'name'    => 'Use feature image as background of slide',
				'desc'    => '',
				'id'      => SHORTNAME . '_slidebg',
				'type'    => 'checkbox'
			),
			array(
				'name'    => 'Background image width',
				'desc'    => '',
				'id'      => SHORTNAME . '_slidebg_width',
				'type'    => 'select',
				'options' => array(					
					array( 'name' => 'Fluid', 'value' => '', ),
					array( 'name' => 'Fixed', 'value' => 'fixed'),
				),
			),
			array(
				'name'    => 'Background image repeating',
				'desc'    => '',
				'id'      => SHORTNAME . '_slidebg_repeat',
				'type'    => 'select',
				'options' => array(
					array( 'name' => 'No repeat', 'value' => ''),
					array( 'name' => 'Repeat', 'value' => 'repeat', ),
					array( 'name' => 'Repeat vertically only', 'value' => 'repeaty'),
					array( 'name' => 'Repeat horizontally only', 'value' => 'repeatx', ),
				),
			),
			array(
				'name'    => 'Background image vertical position',
				'desc'    => '',
				'id'      => SHORTNAME . '_slidebg_positiony',
				'type'    => 'select',
				'options' => array(
					array( 'name' => 'Top', 'value' => ''),
					array( 'name' => 'Middle', 'value' => 'middle', ),
					array( 'name' => 'Bottom', 'value' => 'bottom'),					
				),
			),
			array(
				'name'    => 'Background image horizontal position',
				'desc'    => '',
				'id'      => SHORTNAME . '_slidebg_positionx',
				'type'    => 'select',
				'options' => array(
					array( 'name' => 'Center', 'value' => '', ),
					array( 'name' => 'Left', 'value' => 'left'),					
					array( 'name' => 'Right', 'value' => 'right'),					
				),
			),
			
		),
	);
	
	$meta_boxes[] = array(
		'id'         => 'sldieshow_options',
		'title'      => 'Slideshow options',
		'pages'      => array(  'page','post', Custom_Posts_Type_Gallery::POST_TYPE,
								Custom_Posts_Type_Testimonial::POST_TYPE), // 
		'context'    => 'normal',
		'priority'   => 'high',
		'show_names' => true, // Show field names on the left
		'fields'     => array(			
			array(
				'name'    => 'Select a slideshow type for current page',
				'desc'    => '',
				'id'      => SHORTNAME . '_post_slider',
				'type'    => 'select',
				'options' => array(
					array( 'name' => 'Use global', 'value' => ''),
					array( 'name' => 'jCycle', 'value' => 'jCycle', ),					
					array( 'name' => 'Disable', 'value' => 'Disable', ),															
				),
				),
			array(
				'name'    => 'Select a slider category',
				'desc'    => '',
				'id'      => SHORTNAME . '_post_slider_cat',
				'type'    => 'taxonomy_select',
				'taxonomy' => Custom_Posts_Type_Slideshow::TAXONOMY,
				),
			array(
				'name'    => 'How many slides to display:',
				'desc'    => 'Set a number of how many slides you want to use at current slider',
				'id'      => SHORTNAME . '_post_slider_count',
				'type'    => 'text_small',
				'std'	  => 4,
			),	
		),
	);
	
//	$Theme_Slideshow = new Admin_Theme_Item_Slideshow();
	$meta_boxes[] = array(
		'id'         => 'slideshow_effect_options',
		'title'      => 'Slideshow effect options',
		'pages'      => array( 'page', 'post', Custom_Posts_Type_Gallery::POST_TYPE,
								Custom_Posts_Type_Testimonial::POST_TYPE), // 
		'context'    => 'normal',
		'priority'   => 'high',
		'show_names' => true, // Show field names on the left
		'fields'     => array(			
//			array( // effect
//				'name'		=> 'Select a slideshow effect',
//				'desc'		=> '',
//				'id'		=> SHORTNAME . '_post_slider_effect',
//				'type'		=> 'select',
//				'options'	=> Admin_Theme_Item_Slideshow::getMetaSlideshowEffectList(),
//				'std'		=> 'fade',
//				),

			array( // timeout
				'name'		=> 'Slideshow timeout',
				'desc'		=> 'Milliseconds between slide transitions (0 to disable auto advance)',
				'id'		=> SHORTNAME . '_post_slider_timeout',
				'type'		=> 'text_small',
				'std'		=> '6000',
				),
			
			array( // naviagation
				'name'		=> 'Next/Prev navigation',
				'desc'		=> 'Check to show Next/Prev navigation for slideshow',
				'id'		=> SHORTNAME . '_post_slider_navigation',
				'type'		=> 'checkbox',
				),
			
			array( // fixed height
				'name'		=> 'Slideshow fixed height',
				'desc'		=> 'Set custom fixed slideshow height. Write only number of pixels!',
				'id'		=> SHORTNAME . '_post_slider_fixedheight',
				'type'		=> 'text_small',
				'std'		=> '',
				),
				
			array( //padding
				'name'		=> 'Remove top and bottom paddings from slideshow',
				'desc'		=> 'Check to remove top and bottom paddings from slideshow',
				'id'		=> SHORTNAME . '_post_slider_padding',
				'type'		=> 'checkbox',
				
			),
			array( //pause
				'name'		=> 'Slideshow pause',
				'desc'		=> 'On to Slideshow pause enable "pause on hover"',
				'id'		=> SHORTNAME . '_post_slider_pause',
				'type'		=> 'checkbox',
				
			),

			array( //pause
				'name'		=> 'Disable autoplay',
				'desc'		=> '"On" to disable Slideshow autoplay',
				'id'		=> SHORTNAME . '_post_slider_autoscroll',
				'type'		=> 'checkbox',
				
			),	
			array( // speed
				'name'		=> 'Slideshow speed',
				'desc'		=> 'Speed of the transition(Milliseconds)',
				'id'		=> SHORTNAME . '_post_slider_speed',
				'type'		=> 'text_small',
				'std'		=> '1000',
				),
			),	
	);
	
	
	
	


	// Add other metaboxes as needed

	return $meta_boxes;
}

add_action( 'init', 'cmb_initialize_cmb_meta_boxes', 9999 );
/**
 * Initialize the metabox class.
 */
function cmb_initialize_cmb_meta_boxes() {

	if ( ! class_exists( 'cmb_Meta_Box' ) )
		require_once 'init.php';

}
