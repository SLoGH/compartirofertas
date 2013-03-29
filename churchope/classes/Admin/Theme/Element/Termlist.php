<?php
/**
 * Class for Termlist Html element
 * @todo No page using this element
 * @uses NO
 */
class Admin_Theme_Element_Termlist extends Admin_Theme_Menu_Element
{
	protected $option = array(
							'type' => Admin_Theme_Menu_Element::TYPE_TERMLIST,
						);
	
	/**
	 * Options !! array of object.
	 * @return string 
	 */
	public function render()
	{
		ob_start();
			echo $this->getElementHeader();
			?>
				<select name="<?php echo $this->id; ?>" id="<?php echo $this->id; ?>">
					<option value="">None</option>  
					<?php foreach ($this->options as $cat)
							{
								if (get_option( $this->id ) == $cat->slug)
								{
									$selected = "selected='selected'";
								}
								else
								{
									$selected = "";		
								}
								echo"<option $selected value='". $cat->slug."'>". $cat->name ."</option>";
							}?>
				</select>
			<?php
			echo $this->getElementFooter();
		$html = ob_get_clean();
		
		return $html;
	}
}
?>
