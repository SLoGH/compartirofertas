README WP User Frontend Version: 1.1.0-fork-2RRR-4.4 alpha
========================================================

Modified Code: Andy Bruin (professor99)
Original Code: Tareq Hassan (tareq1988)

Introduction
-------------

This is a fork from WP User Frontend Version 1.1. 
It is a major update of WP User Frontend concentrating on useability and customerisation.
It focuses on Add Post and Edit Post functionality.
There are some bug fixes included as well.

This update contains  many bug fixes and some minor upgrades.

- For the add and edit shortcodes the 'current' option has been removed. A url redirect option has been added.

- The category select code has been rewritten and now works for Ajaxified and Checkbox options.
An additional option has been added to restrict the Ajaxfied category select to childless categories.

- The ability to show attachment images in posts has been restored with an option to turn this on/off. 
File mime icons are also shown if this option is enabled.

- If the Post Status field is not displayed then on edit the post status is reset to the default.

- CSS has been hardened to reduce interference in styling from other themes and plugins.

- Javascript and CSS is only loaded where needed. A new wpuf-global.css file has been created for global CSS.

- EditUsers has been updated to redirect edits to the EditProfile page. 
An option to specify the EditProfile page for this has been added.  

Other changes below in changelog.

Some of these changes are outlined by the following items on the WP User Frontend support forum.

http://wordpress.org/support/topic/custom-editors
http://wordpress.org/support/topic/plugin-wp-user-frontend-redirecting-after-posting
http://wordpress.org/support/topic/allow-to-choose-category-filter
http://wordpress.org/support/topic/close-button-and-return-on-post
http://wordpress.org/support/topic/security-problem-doesnt-observe-user-capabilities
http://wordpress.org/support/topic/inserting-media-directly-into-post-with-the-add-media-button
http://wordpress.org/support/topic/edit-the-url-postname-1
http://wordpress.org/support/topic/edit-post-no-content

Status
------

This code is ALPHA! Use it at you own risk!

It currently has only been tested in the following configurations.

WordPress 3.4.2, 3.5.1
Apache Server
PHP 5.2 & 5.3
WordPress 2010 Theme
WordPress 2011 Theme
Firefox 16.0.2
Safari 5.0.533.16 (should work 4.1+)
Chrome 23.0.1271.95m (should work 5.0+)
Internet Explorer 7.0 & 8.0 
IBM PC

This code is a public development fork of WP User Frontend.
It is not written by or supported by the author of WP User Frontend (Tareq Hasan).
It is not an official release of WP User Frontend.
Please be aware this code may not be included in the next official release of WP User Frontend.

Bugs
-----

Please report bugs via this special topic on the WP User FrontEnd forum 

http://wordpress.org/support/topic/frontend-updates-2rrr-fork

Please report only bugs here. 

All suggestions for updates to WP User Frontend need to go to the normal support forum.

http://wordpress.org/support/plugin/wp-user-frontend

Download
--------

http://2rrr.org.au/downloads/wp-user-frontend/2RRR_4_4/wp-user-frontend_1_1_2RRR_4_4_alpha.zip

A Github repository is available

https://github.com/professor99/WP-User-Frontend/tree/2RRR

Examples
---------

Examples of use are provided in the directory /examples.

AddPost Shortcodes
--------------------

Shortcode examples::

	[wpuf_addpost]
	[wpuf_addpost close="false"]
	[wpuf_addpost close="false" redirect="none"]

Shortcode options:

	post_type: post | <otherPostType>
		post: (default)
		<otherPostType>: other post types
	close: true | false 
		true: will display close button and redirect to last page on close (default)
		false: 
	redirect: none | auto | new | last | %url%
		none: do nothing
		auto: If close==true will load last page on post. 
		      Else will reload current page on post. (default)
		new: will load new page on post
		last: will load last page on post 
		%url%: go to given %url%

EditPost Shortcodes
------------------

Shortcode examples::

	[wpuf_editpost]
	[wpuf_editpost close="false"]
	[wpuf_editpost close="false" redirect="none"]

Shortcode options:

	close: true | false 
		true: will display close button and redirect to last page on close (default)
		false: 
	redirect: none | auto | new | last | %url%
		none: do nothing
		auto: If close==true will load last page on post. 
		      Else will reload current page on post. (default)
		new: will load new page on post
		last: will load last page on post 
		%url%: go to given %url%

Installation
------------

If you have WP User FrontEnd version 1.1 or an earlier development version installed move it to
another directory outside the WordPress plugin directory. This enables you to restore it if necessary.

Install as a normal WordPress plugin. Simply unzip it and copy the wp-user-frontend directory to the WordPress plugin directory.

Downgrade
---------

To downgrade back to Version 1.1 go the the WP User Frontend admin tab "Downgrade" and click on the 
"Downgrade to version 1.1" button. This will set options back to version 1.1 equivalents. 
Restore version 1.1 files directly after this.

Changelog
---------

= 1.1-fork-2RRR-4.4 professor99 =
* Fixed redirect bug.
* Added redirect url option
* Dropped redirect current option
* If no Post Status field then on edit reset the post's status to the default.
* Category select and validate code moved to wpuf-cat.php and wpuf-cat.js
* Fixed category ajaxfied and checkbox selection
* Added Category Childless option to allow selection of only Ajaxified childless category 
* Removed wpuf-ajax.php
* Removed wpuf.js
* EditUsers now redirects to EditProfile when editing user.
* Added Profile Page option 
* Created editprofile.js
* Only load javascript where needed.
* Only load CSS where needed
* Created wpuf-global.css for global CSS
* Added wpuf prefix to most CSS ids and classes to harden style
* Changed wpuf-post-area CSS id to wpuf. Extended to cover all forms including buttons.
* Added wpuf selector to most CSS to harden style.
* Added more language constructs


= 1.1.0-fork-2RRR-4.3 professor99 = 
* Added post status field
* Added slug
* Fixed suppress_edit_post_link bug
* Fixed wpuf_referer
* Fixed expiration date
* Fixed excerpt
* Added login message label
* wpuf_settings_field() now caches
* Fix Insert Media Bug
* Auto Draft added 
* Fixed permalink references

= 1.1.0-fork-2RRR-4.2 professor99 = 
* Bugfix: Changed version to 1.1.0-fork-2RRR-4.2 (eliminates update prompt)
* Fixed wpuf_edit shortcode typo.
* Fixed 'required' message opacity bug
* Fixed Jquery $ conflict bug

= 1.1-fork-2RRR-4.1 professor99 = 
* Adds a "Post Format" field to the edit/add post forms.

= 1.1-fork-2RRR-4.0 professor99 = 
* Added "Users can post?" option.
* Added "default" parameter for "Users can edit post?" option.
* Added "default" parameter for "Users can delete post?" option.
* Added "default" parameter for "Post Status" option.
* Added version to support option page.
* Added Downgrade menu
* Better language support for info div
* Enhanced security
* Added $post_type parameter to wpuf_can_post filter.
* Added $post_id parameter to wpuf_can_edit filter.
* Added wpuf_can_delete filter
* Updated user mapping
* Fixed Description alignment for all users
* Redirect to dashboard on login
* Bugfix: Changed wpuf_user_edit_profile_form() to show_form()

= 1.1-fork-2RRR-3.0 professor99 = 
* Optionally add Excerpts to the Add/Edit Post forms.
* Add/Edit Post forms re-styled to suit excerpts.
* Publish and expiration times can now be edited on the Edit Post form.
* A Delete button can be optionally added to the Edit Post Form.
* Attachment calls now direct (was actions).
* Featured Image code moved to lib/featured_image.php and js/featured_image.js.
* Redirects now filtered by wpuf_post_redirect. 
* Form actions consolidated under wpuf_post_form.
* Attachment/Featured Image buttons fixed for Flash/Silverlight.
* CSS has been strengthened to avoid being messed up by themes.
* CSS has been rearranged and formatted for readability.
* CSS has been tested and fixed for popular browsers using 2010/2011 WordPress themes.

= 1.1-fork-2RRR-2.1 professor99 = 
* Replaced anonymous function with suppress_edit_post_link()

= 1.1-fork-2RRR-2.0 professor99 = 
* Now uses jquery.form to do Ajax style updates.
* Post redirect shortcut option added.
* Better info and error messages.
* Suppress "edit_post_link" on WP User Frontend pages
* Added wpuf_get_option filter
* Removed wpuf_allow_cats filter
* Re-styled buttons
* Re-styled attachment display
* Added wpuf prefix to some css classes
 
= 1.1-fork-2RRR-1.0 professor99 =
* Custom editor option added.
* Editors use max availiable width.
* Close button added as shortcut option and redirects set to suit.
* wpuf_allow_cats filter added.
* Security checks updated.
* Code updated to allow use of wpuf_can_post filter for non logged in users.
		
Last word
---------

Hope you find this useful. Please report bugs as mentioned above.

Big thanks to Tareq Hasan for his work putting together WP User Frontend.

Cheers
TheProfessor




