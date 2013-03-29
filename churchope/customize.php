<?php
add_action('customize_register', 'admin_menu_run');

function admin_menu_run()
{
	include( ABSPATH . 'wp-admin/includes/plugin.php'  );
	global $admin_menu;
	$admin_menu->run();
}

add_action('customize_save', array('Custom_CSS_Style', 'setNeedReinitFlag'));

add_action('init', 'th_reinit_custom_style_if_need');

function th_reinit_custom_style_if_need()
{
	if(Custom_CSS_Style::needReinit())
	{
		$custom_stylesheet = new Custom_CSS_Style();
		$custom_stylesheet->reinit();
	}
}
?>
