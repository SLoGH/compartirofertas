<?php
/**
 * Class for Sidebar Html element
 */
class Admin_Theme_Element_Sidebar extends Admin_Theme_Menu_Element
{
	protected $option = array(
							'type' => Admin_Theme_Menu_Element::TYPE_SIDEBAR,
						);
	
	public function render()
	{
		$checked = "";
		ob_start();
		echo $this->getElementHeader();
			?>
			<input size="<?php echo $this->size; ?>" name="<?php echo $this->id; ?>" id="<?php echo $this->id; ?>" type="text" value="" /><br><br>

			<input name="save_options" type="submit" value="Add sidebar" class="button" />
			<?php 	
			echo $this->getElementFooter();
		
		$html = ob_get_clean();
		return $html;
		
	}
	
	public function save()
	{
		$get_sidebar_options = Sidebar_Generator::get_sidebars();
		$sidebar_name = (isset($_POST['sidebar_generator_0']) ? $_POST['sidebar_generator_0'] : NULL);
		$sidebar_name = str_replace(array("\n", "\r", "\t"), '', $sidebar_name);

		$sidebar_id = Sidebar_Generator::name_to_class($sidebar_name);
		if ($sidebar_id == '')
		{
			$options_sidebar = $get_sidebar_options;
		}
		else
		{
			if (is_array($get_sidebar_options))
			{
				$new_sidebar_gen[$sidebar_id] = $sidebar_id;
				$options_sidebar = array_merge($get_sidebar_options, (array) $new_sidebar_gen);
			}
			else
			{
				$options_sidebar[$sidebar_id] = $sidebar_id;
			}
		}

		update_option(SHORTNAME . '_sidebar_generator', $options_sidebar);
	}
}
?>
