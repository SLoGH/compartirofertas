<?php
/**
 * 'Blog' admin menu page
 */
class Admin_Theme_Item_Blog extends Admin_Theme_Menu_Item
{
	
	public function __construct($parent_slug = '')
	{
		
		$this->setPageTitle('Blog');
		$this->setMenuTitle('Blog');
		$this->setCapability('administrator');
		$this->setMenuSlug(SHORTNAME.'_blog');
		parent::__construct($parent_slug);
		
		$this->init();
	}

	public function init()
	{
		
		$option = new Admin_Theme_Element_Pagetitle();
		$option->setName('Blog Settings');
		$this->addOption($option);
		$option = null;
				
				
				
		$option = new Admin_Theme_Element_Select();
		$option->setName('Sidebar position for blog listing')
				->setDescription('Choose a sidebar position for blog listing')
				->setId(SHORTNAME."_post_listing_layout")
				->setStd('none')
				->setOptions(array("none", "left", "right"));
		$this->addOption($option);
		$option = null;
		
		$option = new Admin_Theme_Element_SelectSidebar();
		$option->setName('Sidebar for blog listing')
				->setDescription('Choose a sidebar for blog listing')
				->setId(SHORTNAME."_post_listing_sidebar");					
		$this->addOption($option);
		$option = null;
		
		$option = new Admin_Theme_Element_Separator();
		$this->addOption($option);
		$option = null;
		
		$option = new Admin_Theme_Element_Checkbox();
		$option->setDescription ('Disable excerpts')
				->setName ('Check to disable excerpts on blog listing')
				->setId (SHORTNAME."_excerpt");
		$this->addOption($option);
		$option = null;
		
		$option = new Admin_Theme_Element_Checkbox();
		$option->setDescription ('Disable about author box')
				->setName ('Check to disable about author box on post entry')
				->setId (SHORTNAME."_authorbox");
		$this->addOption($option);
		$option = null;
		
		$option = new Admin_Theme_Element_Separator();
		$this->addOption($option);
		$option = null;

		$option = new Admin_Theme_Element_Select();
		$option->setName('Sidebar position for single post')
				->setDescription('Choose a sidebar position for single post')
				->setId(SHORTNAME."_post_layout")
				->setStd('none')
				->setOptions(array("none", "left", "right"));
		$this->addOption($option);
		$option = null;
		
		$option = new Admin_Theme_Element_SelectSidebar();
		$option->setName('Sidebar for single post')
				->setDescription('Choose a sidebar for single post')
				->setId(SHORTNAME."_post_sidebar");					
		$this->addOption($option);
		$option = null;		
		
		$option = new Admin_Theme_Element_Separator();
		$this->addOption($option);
		$option = null;
	}
	
	
}
?>
