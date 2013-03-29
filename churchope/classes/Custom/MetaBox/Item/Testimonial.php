<?php

class Custom_MetaBox_Item_Testimonial extends Custom_MetaBox_Item_Default
{
	
	function __construct($taxonomy)
	{
		parent::__construct($taxonomy);
		$this->setId('testimonial_meta_box')
			->setTitle('Testimonial Taxonomy Meta Box');
		$this->addFields();
	}
	
	protected function addFields()
	{
		parent::addFields();
		$this->getMetaTaxInstance()->addSelect( SHORTNAME . '_post_listing_layout', array('' => 'Use global', 'layout_none_sidebar' => 'Full width','layout_left_sidebar' => 'Left sidebar', 'layout_right_sidebar' => 'Right sidebar'), array('name' => 'Template', 'std' => ''));
		$this->getMetaTaxInstance()->addSelect( SHORTNAME . '_post_listing_sidebar', $this->getSidebars(), array('name' => 'Sidebar', 'std' => ''));
		$this->getMetaTaxInstance()->addText( SHORTNAME . '_post_listing_number', array('name' => 'Items per page'));
		
		/**
		 * @todo add paragraph text
		 */
		$this->getMetaTaxInstance()->addParagraph( SHORTNAME . '_slider_option_type_description', array('value'=>'Slideshow option description'));
		$this->getMetaTaxInstance()->addSelect( SHORTNAME . '_tax_slider', array(''=>'Use global', 'jCycle'=>'jCycle', 'Disable'=>'Disable'), array('name' => 'Slideshow type', 'std' => '', 'desc'=>'Select a slideshow type for current taxonomy'));
		$this->getMetaTaxInstance()->addSelect( SHORTNAME . '_tax_slider_cat', $this->getCategoriesList(Custom_Posts_Type_Slideshow::TAXONOMY), array('name' => 'Select a slider category', 'std' => ''));
		$this->getMetaTaxInstance()->addText( SHORTNAME . '_tax_slider_count', array('name' => 'How many slides to display:', 'std'=>4, 'desc'=>'Set a number of how many slides you want to use at current taxonomy'));
		
		/**
		 * @todo description
		 */
		$this->getMetaTaxInstance()->addParagraph( SHORTNAME . '_slider_effect_description', array('value'=>'Slideshow effect option description'));
//		$this->getMetaTaxInstance()->addSelect( SHORTNAME . '_tax_slider_effect', Admin_Theme_Item_Slideshow::getTaxonomySlideshowEffectList() , array('name' => 'Slideshow effect', 'std' => 'fade', 'desc'=>'Choose slideshow effect for this category'));
		$this->getMetaTaxInstance()->addText( SHORTNAME . '_tax_slider_timeout', array('name' => 'Slideshow timeout:', 'std'=>6000, 'desc'=>'Milliseconds between slide transitions (0 to disable auto advance)'));
		$this->getMetaTaxInstance()->addCheckbox( SHORTNAME . '_tax_slider_autoscroll', array('name' => 'Disable autoscroll', 'desc'=>'"On" to disable Slideshow autoscroll'));
		$this->getMetaTaxInstance()->addCheckbox( SHORTNAME . '_tax_slider_navigation', array('name' => 'Next/Prev navigation', 'desc'=>'Check to show Next/Prev navigation for slideshow'));
		$this->getMetaTaxInstance()->addText( SHORTNAME . '_tax_slider_fixedheight', array('name' => 'Slideshow fixed height', 'desc'=>'Set custom fixed slideshow height. Write only number of pixels!'));
		$this->getMetaTaxInstance()->addCheckbox( SHORTNAME . '_tax_slider_padding', array('name' => 'Remove top and bottom paddings from slideshow', 'desc'=>'Check to remove top and bottom paddings from slideshow'));
		$this->getMetaTaxInstance()->addCheckbox( SHORTNAME . '_tax_slider_pause', array('name' => 'Slideshow pause', 'desc'=>'On to Slideshow pause enable "pause on hover"'));
		$this->getMetaTaxInstance()->addText( SHORTNAME . '_tax_slider_speed', array('name' => 'Slideshow speed:', 'std'=>1000, 'desc'=>'Speed of the transition(Milliseconds)'));
	}
}
?>
