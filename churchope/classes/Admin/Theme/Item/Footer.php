<?php
/**
 * 'Footer' admin menu page
 */
class Admin_Theme_Item_Footer extends Admin_Theme_Menu_Item
{
	public function __construct($parent_slug = '')
	{
		
		$this->setPageTitle('Footer');
		$this->setMenuTitle('Footer');
		$this->setCapability('administrator');
		$this->setMenuSlug(SHORTNAME.'_footer');
		parent::__construct($parent_slug);
		
		$this->init();
	}

	public function init()
	{
		$option = null;
		$option = new Admin_Theme_Element_Pagetitle();
		$option->setName('Footer Settings');
		$this->addOption($option);
		
		$option = null;
		
				
		$option = new Admin_Theme_Element_Checkbox();
		$option->setName('Enable footer widget area')
				->setDescription('Check this box if you want to enable footer widgets area for whole site.')
				->setId(SHORTNAME."_footer_widgets_enable")
				->setStd('true');
		$this->addOption($option);
		$option = null;
		
		$option = new Admin_Theme_Element_Select();
		$option->setName('Columns number for footer widgets area')
				->setDescription('Select how many columns(sidebars) you want display for footer widgets area.')
				->setId(SHORTNAME."_footer_widgets_columns")
				->setStd('4')
				->setOptions(array('1','2','3', '4'));
		$this->addOption($option);
		$option = null;
		
		$option = new Admin_Theme_Element_Separator();
		$this->addOption($option);
		$option = null;
		
		
		
		$option = new Admin_Theme_Element_Text();
		$option->setName('Footer text')
				->setDescription('Type here text that appear at botttom of each page - copyrights, etc..')
				->setId(SHORTNAME."_copyright")
				->setStd("Churchope 2012 &copy; <a href='http://themoholics.com'>Premium WordPress Themes</a> by Themoholics");
		$this->addOption($option);
		$option = null;
		
		$option = new Admin_Theme_Element_Textarea();
		$option->setName('Google Analytics')
				->setDescription('Insert your Google Analytics (or other) code here.')
				->setId(SHORTNAME."_GA")
				->setStd("");
		$this->addOption($option);
		$option = null;
		
		$option = new Admin_Theme_Element_Separator();
		$option->setType(Admin_Theme_Menu_Element::TYPE_SEPARATOR);
		$this->addOption($option);
		$option = null;	
		
		
		$option = new Admin_Theme_Element_Colorchooser();
		$option->setName('Footer background color')
				->setDescription('Please select your custom color for footer background')
				->setId( SHORTNAME."_footerbgcolor")
				->setStd('#fafafa');
		$this->addOption($option);
		
		
		$option = new Admin_Theme_Element_Colorchooser();
		$option->setName('Footer headings color')
				->setDescription('Please select your custom color for footer headings')
				->setId( SHORTNAME."_footerheadingscolor")
				->setStd('#545454');
		$this->addOption($option);
		
		$option = new Admin_Theme_Element_Colorchooser();
		$option->setName('Footer text color')
				->setDescription('Please select your custom color for footer text')
				->setId( SHORTNAME."_footertextcolor")
				->setStd('#919191');
		$this->addOption($option);
		
		$option = new Admin_Theme_Element_Colorchooser();
		$option->setName('Footer links color')
				->setDescription('Please select your custom color for footer links')
				->setId( SHORTNAME."_footerlinkscolor")
				->setStd('#c62b02');
		$this->addOption($option);
		
		
		$option = new Admin_Theme_Element_Colorchooser();
		$option->setName('Footer copyright text color')
				->setDescription('Please select your custom color for footer copyright text')
				->setId( SHORTNAME."_footercopyrightcolor")
				->setStd('#afafaf');
		$this->addOption($option);
		
		$option = new Admin_Theme_Element_Colorchooser();
		$option->setName('Footer active menu item text color')
				->setDescription('Please select your custom color for footer active menu item text')
				->setId( SHORTNAME."_footeractivemenucolor")
				->setStd('#656565');
		$this->addOption($option);		
	
	}
}
?>