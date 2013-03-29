<?php
/**
 * 'Header' admin menu page
 */
class Admin_Theme_Item_Events extends Admin_Theme_Menu_Item
{
	public function __construct($parent_slug = '')
	{
		
		$this->setPageTitle('Events');
		$this->setMenuTitle('Events');
		$this->setCapability('administrator');
		$this->setMenuSlug(SHORTNAME . '_events');
		parent::__construct($parent_slug);
		
		$this->init();
	}

	public function init()
	{
		$option = new Admin_Theme_Element_Pagetitle();
		$option->setName('Events Settings');
		$this->addOption($option);
		$option = null;
		
		$option = new Admin_Theme_Element_Select();
		$option->setName('Sidebar position for events listing')
				->setDescription('Choose a sidebar position for events listing')
				->setId(SHORTNAME."_events_listing_layout")
				->setStd('none')
				->setOptions(array("none", "left", "right"));
		$this->addOption($option);
		$option = null;
		
		$option = new Admin_Theme_Element_SelectSidebar();
		$option->setName('Sidebar for events listing')
				->setDescription('Choose a sidebar for events listing')
				->setId(SHORTNAME."_events_listing_sidebar");					
		$this->addOption($option);
		$option = null;
		
				
		$option = new Admin_Theme_Element_Separator();
		$this->addOption($option);
		$option = null;

		$option = new Admin_Theme_Element_Select();
		$option->setName('Sidebar position for single events')
				->setDescription('Choose a sidebar position for single event')
				->setId(SHORTNAME."_events_layout")
				->setStd('none')
				->setOptions(array("none", "left", "right"));
		$this->addOption($option);
		$option = null;
		
		$option = new Admin_Theme_Element_SelectSidebar();
		$option->setName('Sidebar for single events')
				->setDescription('Choose a sidebar for single event')
				->setId(SHORTNAME."_events_sidebar");					
		$this->addOption($option);
		$option = null;		
		
		$option = new Admin_Theme_Element_Separator();
		$this->addOption($option);
		$option = null;
		
		$option = new Admin_Theme_Element_Checkbox();
		$option->setName('Map types')
				->setDescription('Allow your readers to change the map type (street, satellite, or hybrid)')
				->setId(SHORTNAME."_maps_types_switch")
				->setStd('true');
		$this->addOption($option);
		$option = null;
		
		$option = new Admin_Theme_Element_Checkbox();
		$option->setName('Scroll wheel zoom')
				->setDescription('Enable zoom with the mouse scroll wheel')
				->setId(SHORTNAME."_maps_weel_zoom")
				->setStd('');
		$this->addOption($option);
		$option = null;
		
		$option = new Admin_Theme_Element_Checkbox();
		$option->setName('Tooltips')
				->setDescription(htmlentities('Show marker titles as a "tooltip" on mouse-over'))
				->setId(SHORTNAME."_maps_tooltips")
				->setStd('true');
		$this->addOption($option);
		$option = null;
		
		
		$option = new Admin_Theme_Element_Text();
		$option->setName('<a href="http://code.google.com/apis/maps/faq.html#languagesupport" target="_blank">Language</a>	')
				->setDescription('Use a specific for map controls (defaults to browser language). Read Google Maps API doc.')
				->setId(SHORTNAME."_maps_language");	
		$this->addOption($option);
		$option = null;
		
		$option = new Admin_Theme_Element_Separator();
		$this->addOption($option);
		$option = null;
		
		$custom_page = new Custom_Posts_Type_Event();
		$option = new Admin_Theme_Element_Text();
		$option->setName('Event Post slug')
				->setDescription('Some description')
				->setId($custom_page->getPostSlugOptionName())
				->setStd($custom_page->getDefaultPostSlug());
		$this->addOption($option);
		$option = null;
		
		$option = new Admin_Theme_Element_Text();
		$option->setName('Event Tax slug')
				->setDescription('Some description')
				->setId($custom_page->getTaxSlugOptionName())
				->setStd($custom_page->getDefaultTaxSlug());
		$this->addOption($option);
	}
	
	/**
	 * Save form and set option-flag for reinit rewrite rules on init
	 */
	public function saveForm()
	{
		parent::saveForm();
		$this->setNeedReinitRulesFlag();
	}
	
	/**
	 * Reset form and set option-flag for reinit rewrite rules on init
	 */
	public function resetForm()
	{
		parent::resetForm();
		$this->setNeedReinitRulesFlag();
	}
	
	/**
	 * save to DB flag of need do flush_rewrite_rules on next init
	 */
	private function setNeedReinitRulesFlag()
	{
		update_option(SHORTNAME.'_need_flush_rewrite_rules', '1');
	}
}
?>
