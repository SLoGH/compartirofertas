<?php
/**
 * Create group of 3 radiobutton 
 * 
 * @todo No page using this element
 * @uses NO
 * 
 * 
 */
class Admin_Theme_Element_Radio extends Admin_Theme_Menu_Element
{
	protected $option = array(
							'type' => Admin_Theme_Menu_Element::TYPE_RADIO,
						);
	
	
	public function render()
	{
		/**
		 * @todo what is selector ?????
		 */
		$selector = '';
		ob_start();		
		$value = get_option($this->id);
		echo $this->getElementHeader();?>

			<label><input name="<?php echo $this->id; ?>" id="<?php echo $this->id; ?>" type="radio" value="<?php echo $this->value; ?>" <?php echo $selector; ?> <?php if ($value == $this->value || $value == ""){echo 'checked="checked"';}?> /> <?php echo $this->desc; ?></label><br />
			<label><input name="<?php echo $this->id; ?>" id="<?php echo $this->id; ?>_2" type="radio" value="<?php echo $this->value2; ?>" <?php echo $selector; ?> <?php if ($value == $this->value2){echo 'checked="checked"';}?> /> <?php echo $this->desc2; ?></label><br />
			<label><input name="<?php echo $this->id; ?>" id="<?php echo $this->id; ?>_3" type="radio" value="<?php echo $this->value3; ?>" <?php echo $selector; ?> <?php if ($value == $this->value3){echo 'checked="checked"';}?> /> <?php echo $this->desc3; ?></label>
		<?php 	
		echo $this->getElementFooter();
		$html = ob_get_clean();
		return $html;
	}
	
	public function setValue($value)
	{
		$this->option['value'] = $value;
		return $this;
	}
	
	public function setValue2($value)
	{
		$this->option['value2'] = $value;
		return $this;
	}
	public function setValue3($value)
	{
		$this->option['value3'] = $value;
		return $this;
	}
	
	public function setDescription2($description)
	{
		$this->option['desc2'] = $description;
		return $this;
	}
	public function setDescription3($description)
	{
		$this->option['desc3'] = $description;
		return $this;
	}
}
?>
