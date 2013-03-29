<?php

class Admin_Theme_Element_Group_Toggle_Open extends Admin_Theme_Menu_Group
{
	function __construct()
	{
		$this->setGroupClass('toggle');
		$this->setImgClass('down');
	}
	
	public function render()
	{
		return $this->getElementHeader();
	}
}
?>
