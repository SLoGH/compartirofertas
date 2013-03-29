<?php
/**
 * 'Footer' admin menu page
 */
class Admin_Theme_Item_Slideshow extends Admin_Theme_Menu_Item
{
	const CATEGORY	= '_global_slider_cat';
	const COUNT		= '_global_slider_count';
	const TYPE		= '_global_slider';
	
	const EFFECT			= '_global_slider_fx';
	const TIMEOUT			= '_global_slider_timeout';
	const SPEED				= '_global_slider_speed';
	const NAVIGATION		= '_global_slider_navigation';
	const FIXEDHEIGHT		= '_global_slider_fixedheight';
	const PADDING			= '_global_slider_padding';
	const AUTOSCROLL		= '_global_slider_autoscroll';
	const PAUSE				= '_global_slider_pause';
	
	public static $effects_list = array('fade', 'blindX', 'blindY', 'blindZ', 'cover', 'curtainX', 'curtainY',
					'fadeZoom', 'growX', 'growY', 'none', 'scrollUp',
					'scrollDown', 'scrollLeft', 'scrollRight', 'scrollHorz', 'scrollVert',
					'shuffle', 'slideX', 'slideY', 'toss', 'turnUp', 'turnDown', 'turnLeft',
					'turnRight', 'uncover', 'wipe', 'zoom');

	
	public function __construct($parent_slug = '')
	{
		$this->setPageTitle('Slideshows');
		$this->setMenuTitle('Slideshows');
		$this->setCapability('administrator');
		$this->setMenuSlug(SHORTNAME.'_slideshows');
		parent::__construct($parent_slug);
		$this->init();
		
	}

	public function init()
	{
		$option = null;
		$option = new Admin_Theme_Element_Pagetitle();
		$option->setName('Slideshow Settings');
		$this->addOption($option);
		$option = null;
		
		$option = new Admin_Theme_Element_Select();
			$option->setName('Select a global slideshow type')
					->setDescription('Select a global slideshow type ')
					->setId(SHORTNAME.self::TYPE)
					->setStd('Disable')
					->setOptions(array('Disable','jCycle'));
			$this->addOption($option);
			$option = null;
		
		$option = new Admin_Theme_Element_Select_Taxonomy();
			$option->setName('Select a slideshow category')
					->setDescription('Select a slideshow category')
					->setId(SHORTNAME.self::CATEGORY)
					->setStd('')
					->setTaxonomy(Custom_Posts_Type_Slideshow::TAXONOMY);
			$this->addOption($option);
			$option = null;
		
		$option = new Admin_Theme_Element_Text();
			$option->setName('How many slides to display')
					->setDescription('Set a number of how many slides you want to use at current slider')
					->setId(SHORTNAME.self::COUNT)
					->setStd(4);
			$this->addOption($option);
			$option = null;
		
		
		$option = new Admin_Theme_Element_Separator();
			$this->addOption($option);
			$option = null;
		
//		$option = new Admin_Theme_Element_Select();
//			$option->setName('Select a slideshow effect')
//					->setDescription('Select a slideshow effect')
//					->setId(SHORTNAME.self::EFFECT)
//					->setStd('fade')
//					->setOptions($this->getSlideshowEffectList());
//			$this->addOption($option);
//			$option = null;
			
		$option = new Admin_Theme_Element_Text();
			$option->setName('Slideshow timeout')
					->setDescription('Milliseconds between slide transitions (0 to disable auto advance)')
					->setId(SHORTNAME.self::TIMEOUT)
					->setStd(6000);
			$this->addOption($option);
			$option = null;
		
		$option = new Admin_Theme_Element_Text();
			$option->setName('Slideshow speed')
					->setDescription('Speed of the transition(Milliseconds)')
					->setId(SHORTNAME.self::SPEED)
					->setStd(1000);
			$this->addOption($option);
			$option = null;
			
		$option = new Admin_Theme_Element_Checkbox();
			$option->setName('Next/Prev navigation')
					->setDescription('Check to show Next/Prev navigation for slideshow')
					->setId(SHORTNAME.self::NAVIGATION)
					->setStd('');
			$this->addOption($option);
			$option = null;
			
		$option = new Admin_Theme_Element_Text();
			$option->setName('Slideshow fixed height')
					->setDescription('Set custom fixed slideshow height. Write only number of pixels!')
					->setId(SHORTNAME.self::FIXEDHEIGHT)
					->setStd('');
			$this->addOption($option);
			$option = null;
			
		$option = new Admin_Theme_Element_Checkbox();
			$option->setName('Remove top and bottom paddings from slideshow')
					->setDescription('Check to remove top and bottom paddings from slideshow')
					->setId(SHORTNAME.self::PADDING)
					->setStd('');
			$this->addOption($option);
			$option = null;
			
			
		$option = new Admin_Theme_Element_Checkbox();
			$option->setName('Slideshow pause')
					->setDescription('"On" to pause enable "pause on hover"')
					->setId(SHORTNAME.self::PAUSE)
					->setStd('');
			$this->addOption($option);
			$option = null;
		
		$option = new Admin_Theme_Element_Checkbox();
			$option->setName('Disable autoplay')
					->setDescription('"On" to disable Slideshow autoplay')
					->setId(SHORTNAME.self::AUTOSCROLL)
					->setStd('');
			$this->addOption($option);
			$option = null;
			
		$option = new Admin_Theme_Element_Separator();
			$this->addOption($option);
			$option = null;
			
		$custom_page = new Custom_Posts_Type_Slideshow();
		$option = new Admin_Theme_Element_Text();
		$option->setName('Slideshow Post slug')
				->setDescription('Some description')
				->setId($custom_page->getPostSlugOptionName())
				->setStd($custom_page->getDefaultPostSlug());
		$this->addOption($option);
		$option = null;
		
		$option = new Admin_Theme_Element_Text();
		$option->setName('Slideshow Tax slug')
				->setDescription('Some description')
				->setId($custom_page->getTaxSlugOptionName())
				->setStd($custom_page->getDefaultTaxSlug());
		$this->addOption($option);
		
	}
	
	private function getSlideshowEffectList()
	{
		return self::$effects_list;
	}
	
	/**
	 * Static function for getting jCycle effect list in format arrya('effect name'=>'value', .....)
	 * @return type
	 */
	public static function getMetaSlideshowEffectList()
	{
		$meta_list = array();

		foreach (self::$effects_list as $effect )
		{
			$meta_list[]  = array('name' => $effect, 'value' => $effect);
		}
		return $meta_list;
	}
	
	/**
	 * List of slideshow effects for Custom_MetaBox_Item_Default<br/>
	 * in format array('effect1 name'=>'value', 'effect2'=>'value')
	 * @return type
	 */
	public static function getTaxonomySlideshowEffectList()
	{
		$meta_list = array();

		foreach (self::$effects_list as $effect )
		{
			$meta_list[$effect]  = $effect;
		}
		return $meta_list;
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