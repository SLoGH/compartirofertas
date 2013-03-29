<?php
class Walker_Nav_Menu_Sub extends Walker_Nav_Menu
{
	function display_element($element, &$children_elements, $max_depth, $depth = 0, $args, &$output)
	{
		if (!$element)
		{
			return;
		}

		$id_field = $this->db_fields['id'];

		//display this element
		if (is_array($args[0]))
			$args[0]['has_children'] = !empty($children_elements[$element->$id_field]);

		//Adds the 'parent' class to the current item if it has children		
		if (!empty($children_elements[$element->$id_field]))
			array_push($element->classes, 'dropdown');

		$cb_args = array_merge(array(&$output, $element, $depth), $args);

		call_user_func_array(array(&$this, 'start_el'), $cb_args);

		$id = $element->$id_field;

		// descend only when the depth is right and there are childrens for this element
		if (($max_depth == 0 || $max_depth > $depth + 1 ) && isset($children_elements[$id]))
		{
			foreach ($children_elements[$id] as $child)
			{

				if (!isset($newlevel))
				{
					$newlevel = true;
					//start the child delimiter
					$cb_args = array_merge(array(&$output, $depth), $args);
					call_user_func_array(array(&$this, 'start_lvl'), $cb_args);
				}
				$this->display_element($child, $children_elements, $max_depth, $depth + 1, $args, $output);
			}
			unset($children_elements[$id]);
		}

		if (isset($newlevel) && $newlevel)
		{
			//end the child delimiter
			$cb_args = array_merge(array(&$output, $depth), $args);
			call_user_func_array(array(&$this, 'end_lvl'), $cb_args);
		}

		//end this element
		$cb_args = array_merge(array(&$output, $element, $depth), $args);
		call_user_func_array(array(&$this, 'end_el'), $cb_args);
	}
}
?>