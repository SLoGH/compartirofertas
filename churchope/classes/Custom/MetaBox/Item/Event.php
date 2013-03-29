<?php

class Custom_MetaBox_Item_Event extends Custom_MetaBox_Item_Default
{
	
	function __construct($taxonomy)
	{
		parent::__construct($taxonomy);
		$this->setId('etax_meta_box')
			->setTitle('Event Taxonomy Meta Box');
		
		$this->addFields();
	}
	
	protected function addFields()
	{
		parent::addFields();
		
		$this->getMetaTaxInstance()->addSelect( SHORTNAME . '_post_listing_layout', array('' => 'Use global', 'layout_none_sidebar' => 'Full width','layout_left_sidebar' => 'Left sidebar', 'layout_right_sidebar' => 'Right sidebar'), array('name' => 'Template', 'std' => ''));
		$this->getMetaTaxInstance()->addSelect( SHORTNAME . '_post_listing_sidebar', $this->getSidebars(), array('name' => 'Sidebar', 'std' => ''));
		$this->getMetaTaxInstance()->addText( SHORTNAME . '_post_listing_number', array('name' => 'Items per page'));
	}
}
?>
