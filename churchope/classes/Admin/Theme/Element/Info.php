<?php
/**
 * Class for Info Html element
 * @todo No page using this element
 * @uses NO
 */
class Admin_Theme_Element_Info extends Admin_Theme_Menu_Element
{
	protected $option = array(
							'type' => Admin_Theme_Menu_Element::TYPE_INFO,
						);
	
	public function render()
	{
		$html = "<p>{$this->name}</p>";
		return $html;
	}
}
?>
