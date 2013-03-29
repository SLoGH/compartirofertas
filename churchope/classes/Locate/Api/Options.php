<?php
class Locate_Api_Options extends Locate_Api
{
	var $directions = 'inline',                             // inline | google | none
        $directionsServer = 'maps.google.com',
        $mapTypeControl = true,
        $streetViewControl = false,
        $scrollwheel = true,
        $keyboardShortcuts = true,
        $navigationControlOptions = array('style' => 0),
		$zoomControl = false,
        $overviewMapControl = false,
        $overviewMapControlOptions = array('opened' => false),
        $initialOpenInfo = false,
        $initialOpenDirections = false,
        $country = null,
        $language = null,
        $traffic = false,
        $initialTraffic = false,        // Initial setting for traffic checkbox (true = checked)        
        $tooltips = true,
        $alignment = 'default',
        $autodisplay = 'top',
        $editable = false,
        $mapName = null,
        $postid = null,
        $postTypes = array( 'post',
							'page',
							Custom_Posts_Type_Event::POST_TYPE,
							Custom_Posts_Type_Testimonial::POST_TYPE,
							Custom_Posts_Type_Gallery::POST_TYPE,
					),
        $geoRSS = false,
        $control = true,
        $poiList = false,
        $poiListTemplate = "<td class='location-map-marker'>[icon]</td><td><b>[title]</b>[directions]</td>",
        $metaKey = null,
        $metaSyncSave = true,
        $metaSyncUpdate = true,
        $metaKeyErrors = null,
        $mapSizes = array(array('label' => null, 'width' => 300, 'height' => 300), array('label' => null, 'width' => 425, 'height' => 350), array('label' => null, 'width' => 640, 'height' => 480)),
        $border = array('style' => null, 'width' => 1, 'radius' => 0, 'color' => '#000000', 'shadow' => false),
        $demoMap = true,
        $user = true,
        $userInitial = false,
        $userCenter = false,
        $userTitle = "Your location",
        $userBody = null
        ;
        
    // Options are saved as array because WP settings API is fussy about objects
    static function get() {
		$options = new Locate_Api_Options();
		foreach(Locate_Api_Options::getFieldList() as $property => $option_name)
		{
			$options->$property = get_option($option_name, null);
		}
		$options->zoomControl = get_option(SHORTNAME."_maps_weel_zoom", false)?false:true;
		
        return $options;
    }

    function save() {
		return true;
    }
	
	function getFieldList()
	{
		return array('mapTypeControl'	=> SHORTNAME."_maps_types_switch",
					'scrollwheel'		=> SHORTNAME."_maps_weel_zoom",
					'tooltips'			=> SHORTNAME."_maps_tooltips",
					'language'			=> SHORTNAME."_maps_language",
			);
	}
}
?>
