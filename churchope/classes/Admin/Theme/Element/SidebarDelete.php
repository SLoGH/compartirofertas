<?php
/**
 * Class for SidebarDelete Html element
 */
class Admin_Theme_Element_SidebarDelete extends Admin_Theme_Menu_Element
{
	protected $option = array(
							'type' => Admin_Theme_Menu_Element::TYPE_SIDEBAR_DELETE,
						);
	
	public function render()
	{
		ob_start();
		echo $this->getElementHeader();
		?>
		<ul class="sidebars">
		<?php
		$get_sidebar_options = Sidebar_Generator::get_sidebars();
		if($get_sidebar_options != "") {
		$i=1;

		foreach ($get_sidebar_options as $sidebar_gen) { ?>

		<li id="sidebar_cell_<?php echo $i; ?>">

		<strong><?php echo $sidebar_gen; ?></strong>
		<input type="submit" name="sidebar_rm_<?php echo $i; ?>" id="<?php echo $i; ?>" class="button" value="Delete" />
		<img class="sidebar_rm_<?php echo $i; ?>" style="display:none;" src="images/wpspin_light.gif" alt="Loading" />

		<input id="<?php echo 'sidebar_generator_'.$i ?>" type="hidden" name="<?php echo 'sidebar_generator_'.$i ?>" value="<?php echo $sidebar_gen; ?>" />
		</li>
		<?php $i++;  
		} 
		}?>
		</ul>
		<?php
		echo $this->getElementFooter();
								
		
		$html = ob_get_clean();
		return $html;
		
	}
}
?>
