<?php
/**
 * Class for Pagelist Html element
 * @todo No page using this element
 * @uses NO
 */
class Admin_Theme_Element_Pagelist extends Admin_Theme_Menu_Element
{
	protected $option = array(
							'type' => Admin_Theme_Menu_Element::TYPE_PAGELIST,
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
				<select  name="<?php echo $this->id; ?>" id="<?php echo $this->id; ?>">
					<option value="">None</option>  
					<?php 	foreach ($this->options as $page)
							{
								if (get_option( $this->id ) == $page->ID)
								{
									$selected = "selected='selected'";
								}
								else
								{
									$selected = "";		
								}
								echo"<option $selected value='". $page->ID."'>". $page->post_title ."</option>";
							}?>
				</select>
			<?php
			echo $this->getElementFooter();
		$html = ob_get_clean();

		return $html;
	}
}
?>
