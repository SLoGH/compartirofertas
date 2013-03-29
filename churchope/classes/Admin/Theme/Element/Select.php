<?php

/**
 * Class for Select Html element 
 */
class Admin_Theme_Element_Select extends Admin_Theme_Menu_Element
{
	protected $option = array(
						'type' => Admin_Theme_Menu_Element::TYPE_SELECT,
					);
	
	public function render()
	{
		ob_start();
		echo $this->getElementHeader();
		$cur = false;
		?>
		<select  name="<?php echo $this->id; ?>" id="<?php echo $this->id; ?>">
				<?php foreach ($this->options as $option) { ?>
					<option 
						<?php if ( get_option( $this->id ) == $option) 
						{
							echo ' selected="selected"';
							$cur = true; 
						}
						elseif($option == $this->std && !$cur)
						{
							echo ' selected="selected"'; 
							
						}
						?>>
							<?php echo $option; ?>
					</option>
				<?php } ?>
		</select>
		<?php
		echo $this->getElementFooter();
		 
		 $html = ob_get_clean();
		 return $html;
	}
	
	public function add_customize_control($wp_customize)
	{
		$wp_customize->add_control($this->getId(), array(
			'label'		=> __( $this->getName()),
			'section'	=> $this->getCustomizeSection(),
			'settings'	=> $this->getId(),
			'type'		=> 'select',
			'choices'    => $this->getSelectOptionForCustomizing(),
		) );
	}
	
	/**
	 * array of values for WP customizing Select 'value'=>'value'
	 * @return array
	 */
	private function getSelectOptionForCustomizing()
	{
		$result = array();
		if($this->options && is_array($this->options))
		{
			foreach($this->options as $option)
			{
				$result[$option] = $option;
			}
		}
		return $result;
	}
	
}
?>
