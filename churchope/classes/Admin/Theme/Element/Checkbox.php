<?php
/**
 * Class for Checkbox Html element
 */
class Admin_Theme_Element_Checkbox extends Admin_Theme_Menu_Element
{
	protected $option = array(
							'type' => Admin_Theme_Menu_Element::TYPE_CHECKBOX,
						);
	
	public function render()
	{
		$checked = "";
		ob_start();
		echo $this->getElementHeader();
		if(get_option($this->id))
		{
			$checked = "checked=\"checked\"";
		}
		?>                   
		<input type="checkbox" name="<?php echo $this->id; ?>" id="<?php echo $this->id; ?>" value="true" <?php echo $checked; ?>  />
		
		<?php
		echo $this->getElementFooter();
		
		$html = ob_get_clean();
		return $html;
		
	}
	
	public function save()
	{
		if($this->getId())
		{
			$checked_on = isset($_REQUEST[$this->getId()]);
			update_option($this->getId(), $checked_on);
		}
	}
	
	public function add_customize_control($wp_customize)
	{
		$wp_customize->add_control($this->getId(), array(
			'label'		=> __( $this->getName()),
			'section'	=> $this->getCustomizeSection(),
			'settings'	=> $this->getId(),
			'type'		=> 'checkbox',
		) );
	}
}
?>
