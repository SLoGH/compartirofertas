<?php
/**
 * 'Custom Icon' admin menu page
 */
class Admin_Theme_Item_Galleries extends Admin_Theme_Menu_Item
{
	/**
	 * prefix of file icons option
	 */
	const CUSTOM_GALLERY_ICONS = '_custom_gallery_icons';
	
	public function __construct($parent_slug = '')
	{
		$this->setPageTitle('Galleries');
		$this->setMenuTitle('Galleries');
		$this->setCapability('administrator');
		$this->setMenuSlug(SHORTNAME . '_galleries');
		parent::__construct($parent_slug);
		$this->init();
	}

	public function init()
	{
		$option = new Admin_Theme_Element_Pagetitle();
		$option->setName('Galleries settings');
		$this->addOption($option);
		$option = null;
		
				$option = new Admin_Theme_Element_Select();
		$option->setName('Sidebar position for galleries listing')
				->setDescription('Choose a sidebar position for galleries listing')
				->setId(SHORTNAME."_galleries_listing_layout")
				->setStd('none')
				->setOptions(array("none", "left", "right"));
		$this->addOption($option);
		$option = null;
		
		$option = new Admin_Theme_Element_SelectSidebar();
		$option->setName('Sidebar for galleries listing')
				->setDescription('Choose a sidebar for galleries listing')
				->setId(SHORTNAME."_galleries_listing_sidebar");					
		$this->addOption($option);
		$option = null;
		
		$option = new Admin_Theme_Element_Separator();
		$this->addOption($option);
		$option = null;
		
		
		$option = new Admin_Theme_Element_Select();
		$option->setName('Sidebar position for single gallery')
				->setDescription('Choose a sidebar position for single gallery')
				->setId(SHORTNAME."_gallery_layout")
				->setStd('none')
				->setOptions(array("none", "left", "right"));
		$this->addOption($option);
		$option = null;
		
		$option = new Admin_Theme_Element_SelectSidebar();
		$option->setName('Sidebar for single gallery')
				->setDescription('Choose a sidebar for single gallery')
				->setId(SHORTNAME."_gallery_sidebar");					
		$this->addOption($option);
		$option = null;		
		
		$option = new Admin_Theme_Element_Separator();
		$this->addOption($option);
		$option = null;
		
		
		$custom_page = new Custom_Posts_Type_Gallery();
		$option = new Admin_Theme_Element_Text();
		$option->setName('Gallery Post slug')
				->setDescription('Some description')
				->setId($custom_page->getPostSlugOptionName())
				->setStd($custom_page->getDefaultPostSlug());
		$this->addOption($option);
		$option = null;
		
		$option = new Admin_Theme_Element_Text();
		$option->setName('Gallery Taxonomy slug')
				->setDescription('Some description')
				->setId($custom_page->getTaxSlugOptionName())
				->setStd($custom_page->getDefaultTaxSlug());
		$this->addOption($option);
		
		$option = new Admin_Theme_Element_Separator();
		$this->addOption($option);
		$option = null;
		
		$option = new Admin_Theme_Element_File_Icons();
		$option->setName('Custom icons for posts listing')
				->setDescription('You can upload custom icons.')
				->setId(SHORTNAME . self::CUSTOM_GALLERY_ICONS);
		$this->addOption($option);
		$option = null;	
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
