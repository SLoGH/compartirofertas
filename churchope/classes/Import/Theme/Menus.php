<?php

class Import_Theme_Menus implements Import_Theme_Item
{
	
	public function import()
	{
		global $wpdb;
		
		$table_db_name = $wpdb->prefix . "terms";
		$rows = $wpdb->get_results("SELECT * FROM $table_db_name where  name='Main' OR name='Footer Menu'", ARRAY_A);
		$menu_ids = array();
		
		foreach ($rows as $row)
		{
			$menu_ids[$row["name"]] = $row["term_id"];
		}

		$items = wp_get_nav_menu_items($menu_ids['Main']);

		foreach ($items as $item)
		{
			if ($item->title == "Home")
			{
				update_post_meta($item->ID, '_menu_item_url', home_url());
			}
		}
		
		set_theme_mod('nav_menu_locations', array_map('absint', array('header-menu' => $menu_ids['Main'], 'footer-menu' => $menu_ids['Footer Menu'])));
	}
}
?>
