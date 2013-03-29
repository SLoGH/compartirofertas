<?php
/**
 * 'Header' admin menu page
 */
class Admin_Theme_Item_Header extends Admin_Theme_Menu_Item
{
	public function __construct($parent_slug = '')
	{
		
		$this->setPageTitle('Header');
		$this->setMenuTitle('Header');
		$this->setCapability('administrator');
		$this->setMenuSlug(SHORTNAME . '_header');
		parent::__construct($parent_slug);
		
		$this->init();
	}

	public function init()
	{
		$option = new Admin_Theme_Element_Pagetitle();
		$option->setName('Header Settings');
		$this->addOption($option);
		$option = null;
		
		$option = new Admin_Theme_Element_File();
		$option->setName('Use custom logo image')
				->setDescription('You can upload custom logo image.')
				->setId(SHORTNAME."_logo_custom")
				->setStd(get_template_directory_uri().'/images/logo.png');
		$this->addOption($option);
		$option = null;		
		
		$option = new Admin_Theme_Element_Checkbox();
		$option->setName('Hide logo image')
				->setDescription('Check this box if you want to hide logo image and use text site name instead')
				->setId(SHORTNAME."_logo_txt")
				->setStd('');
		$this->addOption($option);
		$option = null;
		
		$option = new Admin_Theme_Element_Separator();
		$this->addOption($option);
		$option = null;
		
		$option = new Admin_Theme_Element_File();
		$option->setName('Use custom pattern image for  header section')
				->setDescription('You can upload custom pattern image.')
				->setId(SHORTNAME."_headerpattern")
				->setStd(get_template_directory_uri().'/images/bg_header_pattern.png');
		$this->addOption($option);
		$option = null;	
		
		$option = new Admin_Theme_Element_Select();
		$option->setName('Custom pattern repeat')
				->setDescription('Custom pattern repeat settings')
				->setId(SHORTNAME."_headerpattern_repeat")
				->setStd('repeat')
				->setOptions(array('repeat','no-repeat','repeat-x','repeat-y'));
		$this->addOption($option);
		$option = null;
		
		$option = new Admin_Theme_Element_Select();
		$option->setName('Custom pattern horizontal position')
				->setDescription('Custom pattern horizontal position')
				->setId(SHORTNAME."_headerpattern_x")
				->setStd('0')
				->setOptions(array('0','50%','100%'));
		$this->addOption($option);
		$option = null;
		
				$option = new Admin_Theme_Element_Select();
		$option->setName('Custom pattern vertical position')
				->setDescription('Custom pattern vertical position')
				->setId(SHORTNAME."_headerpattern_y")
				->setStd('0')
				->setOptions(array('0','50%','100%'));
		$this->addOption($option);
		$option = null;
		
		
		$option = new Admin_Theme_Element_Separator();
		$this->addOption($option);
		$option = null;
		
		$option = new Admin_Theme_Element_File();
		$option->setName('Use custom pattern image for color header section')
				->setDescription('You can upload custom pattern image.')
				->setId(SHORTNAME."_menupattern")
				->setStd(get_template_directory_uri().'/images/menu_pattern.png');
		$this->addOption($option);
		$option = null;	
		
		$option = new Admin_Theme_Element_Select();
		$option->setName('Custom pattern repeat for color header section')
				->setDescription('Custom pattern repeat settings for color header')
				->setId(SHORTNAME."_menupattern_repeat")
				->setStd('repeat')
				->setOptions(array('repeat','no-repeat','repeat-x','repeat-y'));
		$this->addOption($option);
		$option = null;
		
		$option = new Admin_Theme_Element_Select();
		$option->setName('Custom pattern horizontal position  for color header')
				->setDescription('Custom pattern horizontal position  for color header')
				->setId(SHORTNAME."_menupattern_x")
				->setStd('0')
				->setOptions(array('0','50%','100%'));
		$this->addOption($option);
		$option = null;
		
				$option = new Admin_Theme_Element_Select();
		$option->setName('Custom pattern vertical position  for color header')
				->setDescription('Custom pattern vertical position  for color header')
				->setId(SHORTNAME."_menupattern_y")
				->setStd('0')
				->setOptions(array('0','50%','100%'));
		$this->addOption($option);
		$option = null;
		
		$option = new Admin_Theme_Element_Separator();
		$this->addOption($option);
		$option = null;
		
		$option = new Admin_Theme_Element_Text();
		$option->setName('Call to action header ribbon URL')
				->setDescription('Type here URL for call to action header ribbon')
				->setId(SHORTNAME."_ribbon")
				->setStd("http://themoholics.com");
		$this->addOption($option);
		$option = null;
		
		$option = new Admin_Theme_Element_Separator();
		$this->addOption($option);
		$option = null;
		
		$option = new Admin_Theme_Element_Checkbox();
		$option->setName('Align menu to left')
				->setDescription('Switch on to align menu to left side')
				->setId(SHORTNAME."_menu_left")
				->setStd('');
		$this->addOption($option);
		$option = null;
		
		$option = new Admin_Theme_Element_Separator();
		$this->addOption($option);
		$option = null;
		
		
		
		
		$option = new Admin_Theme_Element_Colorchooser();
		$option->setName('Header background color')
				->setDescription('Please select your custom color for header background')
				->setId( SHORTNAME."_headerbgcolor")
				->setStd('#261c1e');
		$this->addOption($option);
		
		$option = new Admin_Theme_Element_Colorchooser();
		$option->setName('Header text color')
				->setDescription('Please select your custom color for header text')
				->setId( SHORTNAME."_headertextcolor")
				->setStd('#eeeeee');
		$this->addOption($option);
		
		
		$option = new Admin_Theme_Element_Colorchooser();
		$option->setName('Color area background color')
				->setDescription('Please select your custom color for color area background')
				->setId( SHORTNAME."_menubgcolor")
				->setStd('#c62b02');
		$this->addOption($option);
		
		$option = new Admin_Theme_Element_Colorchooser();
		$option->setName('Color area text color')
				->setDescription('Please select your custom color for color area text')
				->setId( SHORTNAME."_menutextcolor")
				->setStd('#ffffff');
		$this->addOption($option);
		
		$option = new Admin_Theme_Element_Separator();
		$this->addOption($option);
		$option = null;
		
	
	}
}
?>
