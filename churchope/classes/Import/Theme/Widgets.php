<?php

class Import_Theme_Widgets implements Import_Theme_Item
{

	public function import()
	{
		$sidebars = get_option("sidebars_widgets");
		$sidebars["default-sidebar"] = array();
		$sidebars["header"] = array("churchope-nextevent-5");
		$sidebars["footer-1"] = array("text-2");
		$sidebars["footer-2"] = array("churchope-social-links-2");
		$sidebars["footer-3"] = array("nav_menu-7");
		$sidebars["footer-4"] = array("nav_menu-6");
		$sidebars["th_sidebar-1"] = array("text-5");
		$sidebars["th_sidebar-2"] = array("nav_menu-2", "churchope-testimonials-7");
		$sidebars["th_sidebar-3"] = array("search-5", "churchope-popular-posts-2", "churchope-twitter-3", "tag_cloud-2");
		$sidebars["th_sidebar-4"] = array("churchope-feedburner-3", "text-9", "text-10");
		$sidebars["th_sidebar-5"] = array("churchope-nextevent-3", "churchope-recent-posts-4", "churchope-testimonials-5");
		$sidebars["th_sidebar-6"] = array("search-6", "churchope-flickr-3", "churchope-upcomingevent-2", "churchope-contactform-3");
		$sidebars["th_sidebar-7"] = array("text-13", "churchope-twitter-2", "text-12");
		$sidebars["th_sidebar-8"] = array("churchope-recent-posts-2", "churchope-testimonials-4");
		$sidebars["th_sidebar-9"] = array("text-11", "calendar-3", "churchope-feedburner-6");
		$sidebars["th_sidebar-10"] = array("nav_menu-3", "churchope-testimonials-6");
		$sidebars["th_sidebar-11"] = array("text-4", "text-8", "text-7", "churchope-gallery-3", "churchope-feedburner-4");
		$sidebars["th_sidebar-12"] = array("churchope-flickr-2", "churchope-upcomingevent-3", "text-6", "calendar-2", "churchope-contactform-4");
		$sidebars["th_sidebar-13"] = array("text-15");
		$sidebars["th_sidebar-14"] = array("text-17", "text-16");
		$sidebars["th_sidebar-15"] = array("text-18");
		$sidebars["th_sidebar-16"] = array("text-20", "text-19");
		update_option("sidebars_widgets", $sidebars);


		// Widget Churchope nextevent
		$churchope_nextevent= get_option("widget_churchope-nextevent");
		$churchope_nextevent[3] = array("specific_event" => "next", "hide-expired" => "", "title" => "Next event in:", "days" => "DAYS", "hr" => "HR", "min" => "MIN", "sec" => "SEC", );
		$churchope_nextevent[5] = array("specific_event" => "next", "hide-expired" => "", "title" => "Next event in:", "days" => "DAYS", "hr" => "HR", "min" => "MIN", "sec" => "SEC", );
		$churchope_nextevent["_multiwidget"] = 1;
		update_option("widget_churchope-nextevent", $churchope_nextevent);



		// Widget Text
		$text= get_option("widget_text");
		$text[2] = array("title" => "When and Where?", "text" => "Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, <br/><br/> Coffee Club &ndash; 10:00a<br/> Worship &ndash; 10:30a<br/> AM Exchange &ndash; 11:30<br/> <br/> [button type=\"simple_button_link\" url=\"http://themoholics.com/\" target=\"\" ]more info[/button]", "filter" => "", );
		$text[4] = array("title" => "SOCIAL ICONS", "text" => "[social_link type=\"facebook_account\" url=\"http://www.facebook.com\" target=\"on\" ] [social_link type=\"rss_feed\" url=\"#\" target=\"\" ] [social_link type=\"twitter\" url=\"#\" target=\"on\" ] [social_link type=\"dribble_account\" url=\"#\" target=\"on\" ] [social_link type=\"email_to\" url=\"#\" target=\"\" ] [social_link type=\"google_plus_account\" url=\"#\" target=\"\" ] [social_link type=\"flicker_account\" url=\"#\" target=\"\" ] [social_link type=\"vimeo_account\" url=\"#\" target=\"\" ]", "filter" => "", );
		$text[5] = array("title" => "", "text" => " [three_fourth]<h2>Latest Sermon: Not a Fan: The Open Invitation - February 12, 2012</h2> [/three_fourth] [one_fourth last=last] [button type=\"churchope_button\" url=\"http://themeforest.net/item/churchope-responsive-wordpress-theme/2708562\" target=\"\" ]Download Latest Sermon[/button] [/one_fourth] ", "filter" => "", );
		$text[6] = array("title" => "", "text" => "<iframe width=\"100%\" height=\"120\" frameborder=\"0\" src=\"http://player.vimeo.com/video/35108500?title=0&amp;byline=0&amp;portrait=0\"></iframe>", "filter" => "", );
		$text[7] = array("title" => "Toggles", "text" => "[toggle type=\"white\" title=\"At vero eos et accusamus\"]But I must explain to you how all this mistaken idea of denouncing pleasure and praising pain was born and I will give you a complete account of the system, and expound the actual teachings of the great explorer of the truth[/toggle] [toggle type=\"white\" title=\"Sed ut perspiciatis\"]But I must explain to you how all this mistaken idea of denouncing pleasure and praising pain was born and I will give you a complete account of the system, and expound the actual teachings of the great explorer of the truth[/toggle] [toggle type=\"white\" title=\"On the other hand\"]But I must explain to you how all this mistaken idea of denouncing pleasure and praising pain was born and I will give you a complete account of the system, and expound the actual teachings of the great explorer of the truth, the master-builder of human happiness. No one rejects, dislikes, or avoids pleasure itself, because it is pleasure, but because those who do not know how to pursue pleasure rationally encounter pleasure[/toggle]", "filter" => "", );
		$text[8] = array("title" => "Tabs", "text" => "[tabgroup] [tab title=\"Nullam\"]Cras at nisi nec purus tincidunt suscipit. Proin vitae dui erat. Morbi at sodales orci. Nullam id massa ornare libero luctus molestie eu quis arcu. Pellentesque interdum, nunc ut consequat posuere, turpis risus lobortis ligula, nec tempor enim velit vel augue.[/tab] [tab title=\"Morbi\"] Nam feugiat felis id ipsum blandit sodales. Aenean vitae tortor ultricies est molestie euismod sit amet non leo. Ut velit ipsum, ullamcorper eu condimentum eget, aliquam ut lorem. Integer non velit nulla, in ornare lectus. Cras eget nunc in eros mollis posuere in eu dui. Donec facilisis, orci at sagittis pretium, libero urna consectetur elit, ac ornare odio mi sed odio. Aliquam ut risus neque, eu fringilla neque. Suspendisse potenti. Integer in diam congue diam fermentum cursus at eu lorem. [/tab] [tab title=\"DonecÂ \"]Aenean iaculis turpis non neque molestie cursus. Donec convallis tincidunt erat, sed euismod mauris varius in. Cras ultrices sodales iaculis. Ut sodales nunc ut lectus mattis vitae sagittis lectus aliquet. Ut accumsan ligula ut augue pharetra vulputate. Fusce sit amet ligula at urna tristique cursus.Â [/tab] [/tabgroup]", "filter" => "", );
		$text[9] = array("title" => "", "text" => "[tabgroup] [tab title=\"Nullam\"]Cras at nisi nec purus tincidunt suscipit. Proin vitae dui erat. Morbi at sodales orci. Nullam id massa ornare libero luctus molestie eu quis arcu. Pellentesque interdum, nunc ut consequat posuere, turpis risus lobortis ligula, nec tempor enim velit vel augue.[/tab] [tab title=\"Morbi\"] Nam feugiat felis id ipsum blandit sodales. Aenean vitae tortor ultricies est molestie euismod sit amet non leo. Ut velit ipsum, ullamcorper eu condimentum eget, aliquam ut lorem. Integer non velit nulla, in ornare lectus. Cras eget nunc in eros mollis posuere in eu dui. Donec facilisis, orci at sagittis pretium, libero urna consectetur elit, ac ornare odio mi sed odio. Aliquam ut risus neque, eu fringilla neque. Suspendisse potenti. Integer in diam congue diam fermentum cursus at eu lorem. [/tab] [tab title=\"DonecÂ \"]Aenean iaculis turpis non neque molestie cursus. Donec convallis tincidunt erat, sed euismod mauris varius in. Cras ultrices sodales iaculis. Ut sodales nunc ut lectus mattis vitae sagittis lectus aliquet. Ut accumsan ligula ut augue pharetra vulputate. Fusce sit amet ligula at urna tristique cursus.Â [/tab] [/tabgroup]", "filter" => "", );
		$text[10] = array("title" => "", "text" => "[social_link type=\"facebook_account\" url=\"http://www.facebook.com\" target=\"on\" ] [social_link type=\"rss_feed\" url=\"#\" target=\"\" ] [social_link type=\"twitter\" url=\"#\" target=\"on\" ] [social_link type=\"dribble_account\" url=\"#\" target=\"on\" ] [social_link type=\"email_to\" url=\"#\" target=\"\" ] [social_link type=\"google_plus_account\" url=\"#\" target=\"\" ] [social_link type=\"flicker_account\" url=\"#\" target=\"\" ] [social_link type=\"vimeo_account\" url=\"#\" target=\"\" ]", "filter" => "", );
		$text[11] = array("title" => "Sound shortcode", "text" => "[thaudio href='http://churchope.themoholics.com/wp-content/uploads/2012/07/Sunny-Morning-2.mp3']Sunny Morning[/thaudio] \"But I must explain to you how all this mistaken idea of denouncing pleasure and praising pain was born and I will give you a complete account of the system...", "filter" => "", );
		$text[12] = array("title" => "Lets Get Social!", "text" => "[social_link type=\"facebook_account\" url=\"http://www.facebook.com\" target=\"on\" ] [social_link type=\"rss_feed\" url=\"#\" target=\"\" ] [social_link type=\"twitter\" url=\"#\" target=\"on\" ] [social_link type=\"dribble_account\" url=\"#\" target=\"on\" ] [social_link type=\"email_to\" url=\"#\" target=\"\" ] [social_link type=\"google_plus_account\" url=\"#\" target=\"\" ] [social_link type=\"flicker_account\" url=\"#\" target=\"\" ] [social_link type=\"vimeo_account\" url=\"#\" target=\"\" ]", "filter" => "", );
		$text[13] = array("title" => "Video From our Latest Events", "text" => "<iframe width=\"100%\" height=\"120\" frameborder=\"0\" src=\"http://player.vimeo.com/video/35108500?title=0&amp;byline=0&amp;portrait=0\"></iframe> <br/> <iframe width=\"100%\" height=\"120\" frameborder=\"0\" src=\"http://player.vimeo.com/video/35439665?color=fedf91\"></iframe> ", "filter" => "", );
		$text[15] = array("title" => "", "text" => " [three_fourth]<h2>Don't you think that this theme is awesome?!</h2> [/three_fourth] [one_fourth last=last] [button type=\"churchope_button\" url=\"http://themeforest.net/item/churchope-responsive-wordpress-theme/2708562?ref=themoholics\" target=\"\" ]Get this theme now![/button] [/one_fourth] ", "filter" => "", );
		$text[16] = array("title" => "Follow Us", "text" => "[social_link type=\"facebook_account\" url=\"http://www.facebook.com\" target=\"on\" ] [social_link type=\"rss_feed\" url=\"#\" target=\"\" ] [social_link type=\"twitter\" url=\"#\" target=\"on\" ] [social_link type=\"dribble_account\" url=\"#\" target=\"on\" ] [social_link type=\"email_to\" url=\"#\" target=\"\" ] [social_link type=\"google_plus_account\" url=\"#\" target=\"\" ] [social_link type=\"flicker_account\" url=\"#\" target=\"\" ] [social_link type=\"vimeo_account\" url=\"#\" target=\"\" ]", "filter" => "", );
		$text[17] = array("title" => "Address", "text" => "270 Potrero Avenue<br/> San Francisco, CA 94103,<br/> United States<br/> <br/> <a href=\"#\">mail@churchope.com</a><br/> Phone: 800-321-6543", "filter" => "", );
		$text[18] = array("title" => "", "text" => " [two_third]<h2>YOU CAN ADD SOCIAL ICONS OR OTHER CONTENT HERE</h2> [/two_third] [one_third last=last] [social_link type=\"facebook_account\" url=\"http://www.facebook.com\" target=\"on\" ] [social_link type=\"rss_feed\" url=\"#\" target=\"\" ] [social_link type=\"twitter\" url=\"#\" target=\"on\" ] [social_link type=\"dribble_account\" url=\"#\" target=\"on\" ] [social_link type=\"email_to\" url=\"#\" target=\"\" ] [social_link type=\"google_plus_account\" url=\"#\" target=\"\" ][/one_third] ", "filter" => "", );
		$text[19] = array("title" => "Or here!", "text" => "[social_link type=\"facebook_account\" url=\"http://www.facebook.com\" target=\"on\" ] [social_link type=\"rss_feed\" url=\"#\" target=\"\" ] [social_link type=\"twitter\" url=\"#\" target=\"on\" ] [social_link type=\"dribble_account\" url=\"#\" target=\"on\" ] [social_link type=\"email_to\" url=\"#\" target=\"\" ] [social_link type=\"google_plus_account\" url=\"#\" target=\"\" ] [social_link type=\"flicker_account\" url=\"#\" target=\"\" ] [social_link type=\"vimeo_account\" url=\"#\" target=\"\" ]", "filter" => "", );
		$text[20] = array("title" => "Address", "text" => "270 Potrero Avenue<br/> San Francisco, CA 94103,<br/> United States<br/> <br/> <a href=\"#\">mail@churchope.com</a><br/> Phone: 800-321-6543", "filter" => "", );
		$text["_multiwidget"] = 1;
		update_option("widget_text", $text);



		// Widget Churchope social links
		$churchope_social_links= get_option("widget_churchope-social-links");
		$churchope_social_links[2] = array("hide_icon" => "", "title" => "Follow us", "twitter_account" => "themoholics", "twitter_account_title" => "Twitter", "facebook_account" => "themoholics", "facebook_account_title" => "Facebook", "google_plus_account" => "themoholics", "google_plus_account_title" => "Google Plus", "rss_feed" => "", "rss_feed_title" => "", "email_to" => "themoholics", "email_to_title" => "Email", "flicker_account" => "", "flicker_account_title" => "", "vimeo_account" => "", "vimeo_account_title" => "", "youtube_account" => "", "youtube_account_title" => "", "dribble_account" => "themoholics", "dribble_account_title" => "Dribbble", "linked_in_account" => "", "linked_in_account_title" => "", "pinterest_account" => "", "pinterest_account_title" => "", );
		$churchope_social_links["_multiwidget"] = 1;
		update_option("widget_churchope-social-links", $churchope_social_links);



		// Widget Nav_menu
		$nav_menu= get_option("widget_nav_menu");
		global $wpdb;
		$table_db_name = $wpdb->prefix . "terms";
		$rows = $wpdb->get_results("SELECT * FROM ".$table_db_name." where name='Features' OR name='Shortcodes' OR name='FooterWidgetMenu' OR name='FooterWidgetMenu2'", ARRAY_A);


		$menu_ids = array();
		foreach ($rows as $row)
			$menu_ids[$row["name"]] = $row["term_id"];

		

		$nav_menu[2] = array("title" => "", "nav_menu" => $menu_ids["Features"] );
		$nav_menu[3] = array("title" => "", "nav_menu" => $menu_ids["Shortcodes"] );
		$nav_menu[6] = array("title" => "Unlimited Flexibilty", "nav_menu" => $menu_ids["FooterWidgetMenu"] );
		$nav_menu[7] = array("title" => "Lots of Shortcodes", "nav_menu" => $menu_ids["FooterWidgetMenu2"] );
		$nav_menu["_multiwidget"] = 1;
		update_option("widget_nav_menu", $nav_menu);



		// Widget Churchope testimonials
		$churchope_testimonials= get_option("widget_churchope-testimonials");
		$churchope_testimonials[4] = array("category" => "testimonials", "effect" => "fade", "randomize" => "on", "time" => "10", "title" => "Testimonials", );
		$churchope_testimonials[5] = array("category" => "all", "effect" => "fade", "randomize" => "", "time" => "10", "title" => "Testimonials", );
		$churchope_testimonials[6] = array("category" => "all", "effect" => "fade", "randomize" => "", "time" => "10", "title" => "What people say", );
		$churchope_testimonials[7] = array("category" => "all", "effect" => "fade", "randomize" => "", "time" => "10", "title" => "What people say", );
		$churchope_testimonials["_multiwidget"] = 1;
		update_option("widget_churchope-testimonials", $churchope_testimonials);



		// Widget Search
		$search= get_option("widget_search");
		$search[5] = array("title" => "", );
		$search[6] = array("title" => "", );
		$search["_multiwidget"] = 1;
		update_option("widget_search", $search);



		// Widget Churchope popular posts
		$churchope_popular_posts= get_option("widget_churchope-popular-posts");
		$churchope_popular_posts[2] = array("title" => "Popular post", "number" => "3", );
		$churchope_popular_posts["_multiwidget"] = 1;
		update_option("widget_churchope-popular-posts", $churchope_popular_posts);



		// Widget Churchope twitter
		$churchope_twitter= get_option("widget_churchope-twitter");
		$churchope_twitter[2] = array("title" => "Twitter", "username" => "themoholics", "num" => "3", "update" => "on", "linked" => "", "hyperlinks" => "on", "twitter_users" => "on", "encode_utf8" => "on", );
		$churchope_twitter[3] = array("title" => "Twitter", "username" => "themoholics", "num" => "3", "update" => "on", "linked" => "", "hyperlinks" => "on", "twitter_users" => "on", "encode_utf8" => "on", );
		$churchope_twitter["_multiwidget"] = 1;
		update_option("widget_churchope-twitter", $churchope_twitter);



		// Widget Tag_cloud
		$tag_cloud= get_option("widget_tag_cloud");
		$tag_cloud[2] = array("title" => "Tags", "taxonomy" => "post_tag", );
		$tag_cloud["_multiwidget"] = 1;
		update_option("widget_tag_cloud", $tag_cloud);



		// Widget Churchope feedburner
		$churchope_feedburner= get_option("widget_churchope-feedburner");
		$churchope_feedburner[3] = array("title" => "Sign up for our Newsletter", "description" => "Keep up with the latest news and events.", "feedname" => "themoholics", );
		$churchope_feedburner[4] = array("title" => "Sign up for our Newsletter", "description" => "Keep up with the latest news and events.", "feedname" => "themoholics", );
		$churchope_feedburner[6] = array("title" => "Sign up for our Newsletter", "description" => "Keep up with the latest news and events.", "feedname" => "themoholics", );
		$churchope_feedburner["_multiwidget"] = 1;
		update_option("widget_churchope-feedburner", $churchope_feedburner);



		// Widget Churchope recent posts
		$churchope_recent_posts= get_option("widget_churchope-recent-posts");
		$churchope_recent_posts[2] = array("title" => "Recent posts", "number" => "5", "category" => "", );
		$churchope_recent_posts[4] = array("title" => "Recent posts", "number" => "3", "category" => "", );
		$churchope_recent_posts["_multiwidget"] = 1;
		update_option("widget_churchope-recent-posts", $churchope_recent_posts);



		// Widget Churchope flickr
		$churchope_flickr= get_option("widget_churchope-flickr");
		$churchope_flickr[2] = array("title" => "Flickr", "number" => "6", "user" => "36587311@N08", );
		$churchope_flickr[3] = array("title" => "From Flickr", "number" => "6", "user" => "36587311@N08", );
		$churchope_flickr["_multiwidget"] = 1;
		update_option("widget_churchope-flickr", $churchope_flickr);



		// Widget Churchope upcomingevent
		$churchope_upcomingevent= get_option("widget_churchope-upcomingevent");
		$churchope_upcomingevent[2] = array("title" => "Upcoming events", "count" => "3", "category" => "all", "phone" => "on", "time" => "on", "place" => "on", "email" => "", );
		$churchope_upcomingevent[3] = array("title" => "Upcoming events", "count" => "3", "category" => "all", "phone" => "on", "time" => "on", "place" => "on", "email" => "", );
		$churchope_upcomingevent["_multiwidget"] = 1;
		update_option("widget_churchope-upcomingevent", $churchope_upcomingevent);



		// Widget Churchope contactform
		$churchope_contactform= get_option("widget_churchope-contactform");
		$churchope_contactform[3] = array("title" => "Contact us", );
		$churchope_contactform[4] = array("title" => "Contact us", );
		$churchope_contactform["_multiwidget"] = 1;
		update_option("widget_churchope-contactform", $churchope_contactform);



		// Widget Calendar
		$calendar= get_option("widget_calendar");
		$calendar[2] = array("title" => "", );
		$calendar[3] = array("title" => "", );
		$calendar["_multiwidget"] = 1;
		update_option("widget_calendar", $calendar);



		// Widget Churchope gallery
		$churchope_gallery= get_option("widget_churchope-gallery");
		$churchope_gallery[3] = array("title" => "From gallery", "number" => "4", "category" => "biggallery", );
		$churchope_gallery["_multiwidget"] = 1;
		update_option("widget_churchope-gallery", $churchope_gallery);
	}

}

?>