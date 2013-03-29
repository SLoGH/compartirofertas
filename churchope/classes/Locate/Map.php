<?php
/**
 * 
 */
class Locate_Map
{
	static $version = '1.0';
	
    var $debug = false,
		$basename,
		$baseurl,
		$basepath,
		$pagehook,
		$updater;

	function __construct(){
		$options = Locate_Api_Options::get();

		$this->basename = plugin_basename(__FILE__);
		$this->baseurl = plugins_url('', __FILE__);
		$this->basepath = dirname(__FILE__);

        add_action('admin_init', array(&$this, 'admin_init'));

		add_shortcode('eventmap', array(&$this, 'shortcode_map'));

		// Ajax
		add_action('wp_ajax_eventmap_map_save', array(&$this, 'ajax_map_save'));
		add_action('wp_ajax_eventmap_map_delete', array(&$this, 'ajax_map_delete'));
		add_action('wp_ajax_eventmap_map_create', array(&$this, 'ajax_map_create'));

		// Filter to automatically add maps to post/page content
		add_filter('the_event_map', array(&$this, 'the_content'), 2);
        
        // Filter to generate the directions panel before display
		add_filter('location_map_directions_html', array('Locate_Api_Map', '_directions_html'), 10, 3);    
	}

	function get_version() {
		$version = __('Version', 'churchope') . ":" . self::$version;
		return $version;
	}

	function ajax_map_save() {
		$mapdata = (isset($_POST['map'])) ? json_decode(stripslashes($_POST['map']), true) : null;
		$postid = (isset($_POST['postid'])) ? $_POST['postid'] : null;

		if (!$mapdata)
			$this->ajax_response(__('Internal error, map was missing.  Your data has not been saved!', 'churchope'));

		$map = new Locate_Api_Map($mapdata);
		$res = $map->save($postid);
		
		if ($res === false) {
			$this->ajax_response(__('Internal error - unable to save map.  Your data has not been saved!', 'churchope'));
		} else {
			$this->ajax_response('OK', 1);
		}
	}

	function ajax_map_delete() {
		$mapid = (isset($_POST['mapid'])) ? $_POST['mapid'] : null;

		if (Locate_Api_Map::delete($mapid) === false) {
			$this->ajax_response(__("Internal error when deleting map ID '$mapid'!", 'churchope'));
		} else {
			$this->ajax_response('OK', $mapid);
		}
	}

	function ajax_map_create() {
		$postid = (isset($_POST['postid'])) ? $_POST['postid'] : null;

		$map = new Locate_Api_Map();
		$map->title = __('Untitled', 'churchope');

		$this->ajax_response('OK', array('map' => $map));
	}

	function ajax_response($status, $data=null) {
		header( "Content-Type: application/json" );
		$response = json_encode(array('status' => $status, 'data' => $data));
		die ($response);
	}

	/**
	* Automatic map display.
	* If set, the [eventmap] shortcode will be prepended/appended to the post body, once for each map
	* The shortcode is used so it can be filtered - for example WordPress will remove it in excerpts by default.
	*
	* @param mixed $content
	*/
	function the_content($content="") {
		global $post;
		global $wp_current_filter;
		static $last_post_id;

		$options = Locate_Api_Options::get();
		$autodisplay = $options->autodisplay;

		// No auto display
		if (!$autodisplay || $autodisplay == 'none')
			return $content;

		// Don't add the shortcode for feeds or admin screens
		if (is_feed() || is_admin())
			return $content;

		// If this is an excerpt don't attempt to add the map to it
		if (in_array('get_the_excerpt', $wp_current_filter))
			return $content;

		// Don't auto display if the post already contains map shortcode
		if (stristr($content, '[eventmap') !== false)
			return $content;

		// Don't auto display more than once for the same post (some other plugins call the_content() filter multiple times for same post ID)
		if ($post->ID && $last_post_id == $post->ID)
			return $content;
		else
			$last_post_id = $post->ID;

		// Get maps associated with post
		$maps = Locate_Api_Map::get_post_map_list($post->ID);
		if (empty($maps))
			return $content;

		// Add the shortcode once for each map
		$shortcodes = "";
		foreach($maps as $map)
			$shortcodes .= '[eventmap mapid="' . $map->mapid . '"]';
		
		if ($autodisplay == 'top')
			return $shortcodes . $content;
		else
			return $content . $shortcodes;
	}

	/**
	* Map a shortcode in a post.
	*
	* @param mixed $atts - shortcode attributes
	*/
	function shortcode_map($atts='') {
		global $post;

		// No feeds
		if (is_feed())
			return;

		// Try to protect against Relevanssi, which calls do_shortcode() in the post editor...
		if (is_admin())
			return;

		$options = Locate_Api_Options::get();
		$atts = $this->scrub_atts($atts);

		// Determine what to show
		$mapid = (isset($atts['mapid'])) ? $atts['mapid'] : null;
		$meta_key = $options->metaKey;

		if ($mapid) {
			// Show map by mapid
			$map = Locate_Api_Map::get($mapid);
		} else {
			// Get the first map attached to the post
			$maps = Locate_Api_Map::get_post_map_list($post->ID);
			$map = (isset ($maps[0]) ? $maps[0] : false);
		}

		if (!$map)
			return;

		return $map->display($atts);
	}

	/**
	* Post edit
	*
	* @param mixed $post
	*/
	function meta_box($post) {
		global $post;

		$maps = Locate_Api_Map::get_post_map_list($post->ID);
		Locate_Api_Map::edit($maps, $post->ID);
	}
    
    function admin_init() {
        $options = Locate_Api_Options::get();
        
        // Add editing meta box to standard & custom post types
        foreach((array)$options->postTypes as $post_type)
		{
			if($post_type_object = get_post_type_object($post_type))
			{
				if(isset($post_type_object->labels->name_admin_bar))
				{
					// Set map meta title
					$meta_title = $post_type_object->labels->name_admin_bar . ' Map';
					add_meta_box('eventmap', $meta_title, array($this, 'meta_box'), $post_type, 'normal', 'high');
				}
			}
			
		}
                
    }

	/**
	* Scrub attributes
	* The WordPress shortcode API passes shortcode attributes in lowercase and with boolean values as strings (e.g. "true")
	* It's also impossible to pass array attributes without using a serialized array
	* This function converts atts to lowercase, replaces boolean strings with booleans, and creates arrays from 'flattened' attributes
	* Like center, point, viewport, etc.
	*
	* Returns empty array if $atts is empty or not an array
	*/
	function scrub_atts($atts=null) {
		if (!$atts || !is_array($atts))
			return array();

		// WP unfortunately passes booleans as strings
		foreach((array)$atts as $key => $value) {
			if ($value === "true")
				$atts[$key] = true;
			if ($value === "false")
				$atts[$key] = false;
		}

		// Shortcode attributes are lowercase so convert everything to lowercase
		$atts = array_change_key_case($atts);

		// Array attributes are 'flattened' when passed via shortcode
		// Point
		if (isset($atts['point_lat']) && isset($atts['point_lng'])) {
			$atts['point'] = array('lat' => $atts['point_lat'], 'lng' => $atts['point_lng']);
			unset($atts['point_lat'], $atts['point_lng']);
		}

		// Viewport
		if (isset($atts['viewport_sw_lat']) && isset($atts['viewport_sw_lng']) && isset($atts['viewport_ne_lat'])
		&& isset($atts['viewport_ne_lng'])) {
			$atts['viewport'] = array(
				'sw' => array('lat' => $atts['viewport_sw_lat'], 'lng' => $atts['viewport_sw_lng']),
				'ne' => array('lat' => $atts['viewport_ne_lat'], 'lng' => $atts['viewport_ne_lng'])
			);
			unset($atts['viewport_sw_lat'], $atts['viewport_sw_lng'], $atts['viewport_ne_lat'], $atts['viewport_ne_lng']);
		}

		// Center
		if (isset($atts['center_lat']) && isset($atts['center_lng'])) {
			$atts['center'] = array('lat' => $atts['center_lat'], 'lng' => $atts['center_lng']);
			unset($atts['center_lat'], $atts['center_lng']);
		}

		// OverviewMapControlOptions
		if (isset($atts['initialopenoverviewmap']) && $atts['initialopenoverviewmap'] == true) {
			$atts['overviewmapcontroloptions']['opened'] = true;
		}

		return $atts;
	}

	function rss_ns() {
		echo 'xmlns:georss="http://www.georss.org/georss"';
	}

	function rss_item() {
		global $post;

		if (!is_feed())
			return;

		$maps = get_post_maps($post->ID);
		foreach ($maps as $map) {
			foreach ($map->pois as $poi) {
				echo '<georss:point>' . $poi->point['lat'] . ' ' . $poi->point['lng'] . '</georss:point>';
			}
		}
	}
}
?>
