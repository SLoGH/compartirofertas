<?php
/**
 * Class collection with all theme widgets 
 */
final class Widget 
{
	/**
	 * Unrigister widgets exception AND Register all theme widgets.
	 */
	public function run()
	{
		unregister_widget('WP_Widget_Recent_Posts' );
		
		register_widget('Widget_Flickr');
		register_widget('Widget_FeedburnerEmail');
		register_widget('Widget_ContactForm');
		register_widget('Widget_Gallery');
		register_widget('Widget_RecentPosts');
		register_widget('Widget_Twitter');
		register_widget('Widget_SocialLinks');
		register_widget('Widget_PopularPosts');
		register_widget('Widget_Event');
		register_widget('Widget_Event_Upcoming');
		register_widget('Widget_Testimonial');
	}
}
?>