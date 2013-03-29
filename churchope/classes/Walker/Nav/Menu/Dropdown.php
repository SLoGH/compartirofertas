<?php

// Menu walker -> select
class Walker_Nav_Menu_Dropdown extends Walker_Nav_Menu
{

	function start_lvl(&$output, $depth)
	{
		$indent = str_repeat("\t", $depth); // don't output children opening tag (`<ul>`)
	}

	function end_lvl(&$output, $depth)
	{
		$indent = str_repeat("\t", $depth); // don't output children closing tag
	}

	function start_el(&$output, $item, $depth, $args)
	{
		// add spacing to the title based on the depth
		$item->title = str_repeat("&nbsp; ", $depth) . $item->title;

		parent::start_el($output, $item, $depth, $args);

		// select current menu item.
		$selected = '';
		if (isset($item->classes) && is_array($item->classes) && in_array('current-menu-item', $item->classes))
		{
			$selected = 'selected="selected"';
		}
		// no point redefining this method too, we just replace the li tag...
		$output =  strip_tags(str_replace('<li', '<option value="' . $item->url . '" ' . $selected, $output),'<option>');
		$output .= "</option>\n";
	}

	function end_el(&$output, $item, $depth)
	{
		return;
//		$output .= "</option>\n"; // replace closing </li> with the option tag
	}
}
?>