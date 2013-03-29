<?php
/**
 * Class for Textarea Html element 
 */
class Admin_Theme_Element_Textarea extends Admin_Theme_Menu_Element
{
	protected $option = array(
							'type' => Admin_Theme_Menu_Element::TYPE_TEXTAREA,
						);
	
	public function render()
	{
		ob_start();
		echo $this->getElementHeader();
		?>
				<textarea name="<?php echo $this->id; ?>" id="<?php echo $this->id; ?>" style="height:100px;"><?php 
						if( get_option($this->id) != "") {
								echo stripslashes(get_option($this->id));
						}else{
								echo $this->std;
						}?></textarea>
		<?php
		echo $this->getElementFooter();
		$html = ob_get_clean();
		return $html;
		
	}
}
?>
