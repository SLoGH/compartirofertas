<?php

/**
 *  
 */
class Locate_Api_Map extends Locate_Api
{

	const MAP_META_KEY_SUFFIX = '_locate_map';
	const FRONTEND_MAP_WIDTH = 3000;
	const FRONTEND_MAP_HEIGHT = 380;

	var $mapid = null,
			$width = 425,
			$height = 350,
			$zoom = null,
			$center = array('lat' => 0, 'lng' => 0),
			$mapTypeId = 'roadmap',
			$title = 'Untitled',
			$metaKey = null,
			$pois = array();

	function __construct($atts = null)
	{
		parent::__construct($atts);
		$this->_fixup_pois();
	}

	function _fixup_pois()
	{
		// Convert POIs from arrays to objects if needed
		foreach ((array) $this->pois as $index => $poi)
		{
			if (is_array($poi))
				$this->pois[$index] = new Locate_Api_Poi($poi);
		}
	}

	/**
	 * Get a map.  Called statically.
	 *
	 * @param mixed $mapid
	 * @return mixed false if failure, or a map object on success
	 */
	function get($mapid)
	{
		$maps = Locate_Api_Map::get_post_map_list($mapid);
		if ($maps && is_array($maps) && count($maps))
		{
			return array_shift($maps);
		}
		return false;
	}

	function save($postid)
	{
		$map = serialize($this);
		return update_post_meta($postid, $this->getMetaKey(), $map);
	}

	/**
	 * Delete a map and all of its post assignments
	 *
	 * @param mixed $mapid - post id with map
	 */
	function delete($mapid)
	{
		return delete_post_meta($mapid, Locate_Api_Map::getMetaKey());
	}

	/**
	 * Get a list of maps attached to the post
	 *
	 * @param int $postid Post for which to get the list
	 * @return an array of one map for the post or FALSE if no maps
	 */
	function get_post_map_list($postid)
	{
		$meta_map = get_post_meta($postid, Locate_Api_Map::getMetaKey(), true);
		if ($meta_map)
		{
			$map = unserialize($meta_map);
			$map->mapid = $postid;
			return array($map);
		}
		return false;
	}

	/**
	 * Display a map
	 *
	 * @param mixed $atts - override attributes.  Attributes applied from options -> map -> $atts
	 */
	function display($atts = null)
	{
		global $locationMap;
		static $div = 0;

		$options = Locate_Api_Options::get();

		// For anyone using WPML (wpml.org): set the selected language if it wasn't specified in the options screen
		if (defined('ICL_LANGUAGE_CODE') && !$options->language)
			$options->language = ICL_LANGUAGE_CODE;



		if (is_admin())
		{
			$width = $this->_px($this->width);
			$height = $this->_px($this->height);
		}
		else
		{
//			$width = $this->_px(self::FRONTEND_MAP_WIDTH);
			$width = '100%';
			$height = $this->_px(self::FRONTEND_MAP_HEIGHT);
		}

		// Container holds the map + poi list + directions
		// It requires a width (otherwise it'll default to 100% and can't be centered) but no height (so it can expand for directions/poilist)
		$container_style = "width:$width; ";
		$container_class = "location-map-container";

		// The canvas is just the map           
		$canvas_style = "width:$width; height:$height; ";
		$canvas_class = "location-map-canvas";

		// The canvas panel is a container for the map
		$canvas_panel_style = "width:$width; height:$height; ";
		$canvas_panel_class = "location-map-canvas-panel";

		$poi_list_class = "location-map-poi-list";
		$poi_list_style = "width:$width;max-height:$height; ";	  // POI list has a max-height to encourage scrollbars if too tall
		// Assign a map name if none provided
		if (!isset($options->mapName))
		{
			$options->mapName = "location_map_$div";
			$div++;
		}

		// Use default POI list template for each row if no alternate template was provided
		if ($options->poiList)
		{
			foreach ($this->pois as $i => $poi)
			{
				if (!$poi->poiListTemplate)
					$this->pois[$i]->poiListTemplate = $options->poiListTemplate;
			}
		}

		Locate_Api_Map::_load($options);


		echo "<script type='text/javascript'>"
		. "/* <![CDATA[ */"
		. "var mapdata = " . json_encode($this) . ";"
		. "var options = " . json_encode($options) . ";"
		. "var $options->mapName = new LocationMap(mapdata, options);"
		. "$options->mapName.display();"
		. "/* ]]> */"
		. "</script>";

		$html = "<div class='$container_class'>"
				. "<div class='$canvas_panel_class' style='$canvas_panel_style'>"
				. "<div id='$options->mapName' class='$canvas_class' style='$canvas_style'></div>"
				. "</div>";

		// List of locations
		if ($options->poiList)
		{
			$html .= "<div id='{$options->mapName}_poi_list' class='$poi_list_class' style='$poi_list_style'></div>";
		}

//		if ($options->directions == 'inline')
//			$html .= apply_filters('location_map_directions_html', null, $this, $options);

		$html .= "</div>";
		return $html;
	}

	function edit($maps = null, $postid)
	{
		global $locationMap;

		// Set options for editing
		$options = Locate_Api_Options::get();
		$options->postid = $postid;
		$options->mapName = 'location_map_0';
		$options->directions = 'none';
		$options->mapTypeControl = true;
		$options->navigationControlOptions = array('style' => 0);
		$options->initialOpenInfo = false;
		$options->traffic = false;
		$options->editable = true;
		$options->overviewMapControl = true;
		$options->overviewMapControlOptions = array('opened' => false);

		Locate_Api_Map::_load($options);
		echo "<script type='text/javascript'>"
		. "/* <![CDATA[ */"
		. "var mapdata = " . json_encode($maps) . ";"
		. "var options = " . json_encode($options) . ";"
		. "var version = '" . $locationMap->get_version() . "';"
		. "var locationMapEditor = new LocationMapEditor(mapdata, options);"
		. "/* ]]> */"
		. "</script>";
		?>
		<div id='location_map_metabox'>
			<div style='border-bottom:1px solid black; overflow: auto'>
				<div id='location_map_add_panel' style='visibility:hidden'>
					<p>
						<span class='submit' style='padding: 0; float: none' >
							<input class='button-primary' type='button' id='location_map_add_btn' value='<?php _e('Add', 'churchope'); ?>' />
						</span>

						<span  id='location_map_add_address'>
							<b><?php _e('Location', 'churchope') ?>: </b>
							<input size='50' type='text' id='location_map_saddr' />
						</span>

						<br/><span id='location_map_saddr_corrected' class='location-map-address-corrected'></span>
					</p>
				</div>
			</div>

			<table style='width:100%'>
				<tr>
					<td valign="top">
						<div id='location_map_left_panel'>
							<div id='location_map_maplist_panel'>
								<p>
									<b><?php _e('Current Maps', 'churchope') ?></b>
									<input class='button-primary' type='button' id='location_map_create_btn' value='<?php _e('New Map', 'churchope') ?>' />
								</p>

								<div id='location_map_maplist'></div>
							</div>

							<div id='location_map_adjust_panel' style='display:none'>
								<div id='location_map_adjust'>
									<p>
										<b><?php _e('Title', 'churchope') ?>: </b><input id='location_map_title' type='text' size='20' />
									</p>
									<p class='submit' style='padding: 0; float: none' >
										<input class='button-primary' type='button' id='location_map_save_btn' value='<?php _e('Save', 'churchope'); ?>' />
										<input type='button' id='location_map_recenter_btn' value='<?php _e('Center', 'churchope'); ?>' />
									</p>
									<hr/>
								</div>
								<div id='<?php echo $options->mapName ?>_poi_list' class='location-map-edit-poi-list'></div>
							</div>
						</div>
					</td>
					<td id='location_map_preview_panel' valign='top'>
						<div id='<?php echo $options->mapName ?>' class='location-map-edit-canvas'></div>
					</td>
				</tr>
			</table>
		</div>
		<?php
	}

	function _load($options)
	{
		global $locationMap;
		static $loaded;

		if ($loaded)
			return;
		else
			$loaded = true;

		$url = get_template_directory_uri();

		echo "<script type='text/javascript' src='http://www.google.com/jsapi'></script>";
		echo "<script type='text/javascript' src='$url/js/locationmap_lib.min.js?version=" . Locate_Map::$version . "'></script>";
		echo "<script type='text/javascript' src='$url/js/locationmap.js?version=" . Locate_Map::$version . "'></script>";

		$script = "var mapl10n= " . json_encode(Locate_Api_Map::_localize()) . ";";

		echo "<script type='text/javascript'>/* <![CDATA[ */ $script /* ]]> */</script>";
	}

	function _localize()
	{
		// Localize script texts
		return array(
			'maps_in_post' => __('Maps in this post', 'churchope'),
			'no_maps_in_post' => __('There are no maps yet for this post', 'churchope'),
			'create_map' => __('Create a new map', 'churchope'),
			'map_id' => __('Map ID', 'churchope'),
			'untitled' => __('Untitled', 'churchope'),
			'dir_not_found' => __('The starting or ending address could not be found.', 'churchope'),
			'dir_zero_results' => __('Google cannot return directions between those addresses.  There is no route between them or the routing information is not available.', 'churchope'),
			'dir_default' => __('Unknown error, unable to return directions.  Status code = ', 'churchope'),
			'enter_address' => __('Enter address', 'churchope'),
			'no_address' => __('No matching address', 'churchope'),
			'did_you_mean' => __('Did you mean: ', 'churchope'),
			'directions' => __('Directions', 'churchope'),
			'edit' => __('Edit', 'churchope'),
			'save' => __('Save', 'churchope'),
			'cancel' => __('Cancel', 'churchope'),
			'del' => __('Delete', 'churchope'),
			'view' => __('View', 'churchope'),
			'back' => __('Back', 'churchope'),
			'insert_into_post' => __('Insert into post', 'churchope'),
			'select_a_map' => __('Select a map', 'churchope'),
			'title' => __('Title', 'churchope'),
			'delete_prompt' => __('Delete this map marker?', 'churchope'),
			'delete_map_prompt' => __('Delete this map?', 'churchope'),
			'del' => __('Delete', 'churchope'),
			'map_saved' => __('Map saved', 'churchope'),
			'map_deleted' => __('Map deleted', 'churchope'),
			'ajax_error' => __('Error: AJAX failed!  ', 'churchope'),
			'click_and_drag' => __('Click & drag to move this marker', 'churchope'),
			'zoom' => __('Zoom', 'churchope'),
			'traffic' => __('Traffic', 'churchope'),
			'standard_icons' => __('Standard icons', 'churchope'),
			'my_icons' => __('My icons', 'churchope')
		);
	}

	/**
	 * Filter HTML for directions
	 *
	 * Default filter to generate the directions panel HTML
	 *
	 * @param mixed $html
	 * @param mixed $map
	 * @param mixed $options
	 */
	function _directions_html($html, $map, $options)
	{
		if (!is_object($options))
		{
			$options = new stdClass();
			$options->mapName = 'location_map_0';
		}
		$html = "
			<div id='{$options->mapName}_directions' class='location-map-directions'>
				<form action=''>
			<p><span id='{$options->mapName}_car_button' class='location-map-car-button location-map-travelmode selected' title='" . __('By car', 'churchope') . "' ></span><span id='{$options->mapName}_walk_button' class='location-map-walk-button location-map-travelmode' title='" . __('Walking', 'churchope') . "' ></span><span id='{$options->mapName}_bike_button' class='location-map-bike-button location-map-travelmode' title='" . __('Bicycling', 'churchope') . "' ></span></p>
			<p><span class='location-map-a' title='" . __('Start', 'churchope') . "'></span><input  type='text' class='location-map-txt' id='{$options->mapName}_saddr' value='' /></p>
						<p><span class='location-map-swap' id='{$options->mapName}_addrswap' title='" . __('Swap start and end', 'churchope') . "'></span></p>
						<p><span class='location-map-b' title='" . __('End', 'churchope') . "'></span><input   class='location-map-txt'  type='text' id='{$options->mapName}_daddr' value='' /></p>
						<p><input type='submit' class='location-map-button churchope_button' value='" . __('Get Directions', 'churchope') . "' id='{$options->mapName}_get_directions' /><input type='button' class='location-map-button churchope_button' value='" . __('Print Directions', 'churchope') . "' id='{$options->mapName}_print_directions' /><input type='button' class='location-map-button churchope_button' value ='" . __('Close', 'churchope') . "' id='{$options->mapName}_close_directions' />
					</p>
				</form>
				<div id='{$options->mapName}_directions_renderer' class='location-map-direction-list'></div>
			</div>
		";

		return $html;
	}

	/**
	 * Append 'px' to a dimension (width/height) 
	 * Some browsers like Chrome are fussy about the 'px' suffix and won't render correctly with just a number 
	 * 
	 * If there is a 'px' or '%' suffix already present, the original value is returned unchanged
	 *     
	 * @param mixed $size
	 */
	function _px($size)
	{
		return ( stripos($size, 'px') || strpos($size, '%')) ? $size : $size . 'px';
	}

	function getMetaKey()
	{
		return SHORTNAME . self::MAP_META_KEY_SUFFIX;
	}

}
?>
