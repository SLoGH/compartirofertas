<?php
/**
 * Wpuf Settings Options
 *
 *@author Tareq Hasan 
 *@package WP User Frontend
 *@version 1.1.0-fork-2RRR-4.4
 */

/*
== Changelog ==

= 1.1.0-fork-2RRR-4.4 professor99 =
* Added show attachment thumbnails option.
* Added cat_ajax_childless option
* Updated language constructs
* Added profile_page_id option

= 1.1.0-fork-2RRR-4.3 professor99 =
* Added private post status.
* Added post status field
* Added slug
* Prepared_fields now cached
* Added login message label

= 1.1.0-fork-2RRR-4.2 professor99 =
* Fixed wpuf_edit shortcode typo.

= 1.1-fork-2RRR-4.1 professor99 =
* Added "allow_format" option.
* Added "format_label" option.
* Added "format_help" option.

= 1.1-fork-2RRR-4.0 professor99 =
* Added "Users can post?" option.
* Added "default" parameter for "Users can edit post?" option.
* Added "default" parameter for "Users can delete post?" option.
* Added "default" parameter for "Post Status" option.
* Added version to support option page.

= 1.1-fork-2RRR-3.0 professor99 =
* Added delete button label and option
* Changed post expiration description
* Added excerpt options
* Added wpuf_get_option filter
* Removed exclamation from Update Post label
* Changed attachment upload button display name

= 1.1-fork-2RRR-2.0 professor99 =
* Added wpuf_get_option filter
* Attachment label changed to 'Add attachment'
* Removed ! from 'Update Post!'

= 1.1-fork-2RRR-1.0 professor99 =
* Added Close label for Close button
*/

//Cached version of $prepared_fields to reduce SQL Queries
$wpuf_prepared_fields = '';

/**
 * Get the value of a settings field
 *
 * @param string $option option field name
 * @return mixed
 */
function wpuf_get_option( $option ) {
	global $wpuf_prepared_fields;

	if ( !empty( $wpuf_prepared_fields ) )
	{
		$prepared_fields = $wpuf_prepared_fields;
	} 
	else
	{
		$fields = wpuf_settings_fields();
			
		$prepared_fields = array();

		//prepare the array with the field as key
		//and set the section name on each field
		foreach ($fields as $section => $field) {
			foreach ($field as $fld) {
				$prepared_fields[$fld['name']] = $fld;
				$prepared_fields[$fld['name']]['section'] = $section;
			}
		}
		
		$wpuf_prepared_fields = $prepared_fields;
	}
	
    //get the value of the section where the option exists
    $opt = get_option( $prepared_fields[$option]['section'] );
    $opt = is_array( $opt ) ? $opt : array();

    //return the value if found, otherwise default
    if ( array_key_exists( $option, $opt ) ) {
        $val = $opt[$option];
    } else {
        $val = isset( $prepared_fields[$option]['default'] ) ? $prepared_fields[$option]['default'] : '';
    }
	
	return apply_filters( 'wpuf_get_option', $val, $option );
}

/**
 * Settings Sections
 *
 * @since 1.0
 * @return array
 */
function wpuf_settings_sections() {
    $sections = array(
        array(
            'id' => 'wpuf_labels',
            'title' => __( 'Labels', 'wpuf' )
        ),
        array(
            'id' => 'wpuf_frontend_posting',
            'title' => __( 'Frontend Posting', 'wpuf' )
        ),
        array(
            'id' => 'wpuf_dashboard',
            'title' => __( 'Dashboard', 'wpuf' )
        ),
        array(
            'id' => 'wpuf_others',
            'title' => __( 'Others', 'wpuf' )
        ),
        array(
            'id' => 'wpuf_payment',
            'title' => __( 'Payments', 'wpuf' )
        ),
        array(
            'id' => 'wpuf_support',
            'title' => __( 'Support', 'wpuf' )
        ),
    );

    return apply_filters( 'wpuf_settings_sections', $sections );
}

function wpuf_settings_fields() {
    global $wpuf;

    $settings_fields = array(
        'wpuf_labels' => apply_filters( 'wpuf_options_label', array(
            array(
                'name' => 'title_label',
                'label' => __( 'Post title label', 'wpuf' ),
                'default' => __( 'Title', 'wpuf' )
            ),
            array(
                'name' => 'title_help',
                'label' => __( 'Post title help text', 'wpuf' )
            ),
            array(
                'name' => 'slug_label',
                'label' => __( 'Slug label', 'wpuf' ),
                'default' =>  __( 'Slug', 'wpuf' )
            ),
            array(
                'name' => 'slug_help',
                'label' => __( 'Slug help text', 'wpuf' ),
				'default' => __( 'Leave blank for default', 'wpuf' )
            ),
            array(
                'name' => 'format_label',
                'label' => __( 'Post Format label', 'wpuf' ),
                'default' => __( 'Post Format', 'wpuf' )
            ),
            array(
                'name' => 'format_help',
                'label' => __( 'Post Format help text', 'wpuf' )
            ),
            array(
                'name' => 'cat_label',
                'label' => __( 'Post category label', 'wpuf' ),
                'default' => __( 'Category', 'wpuf' )
            ),
            array(
                'name' => 'cat_help',
                'label' => __( 'Post category help text', 'wpuf' )
            ),
           array(
                'name' => 'status_label',
                'label' => __( 'Post Status label', 'wpuf' ),
                'default' => __( 'Post Status', 'wpuf' )
            ),
            array(
                'name' => 'status_help',
                'label' => __( 'Post Status help text', 'wpuf' ),
            ),
            array(
                'name' => 'desc_label',
                'label' => __( 'Post description label', 'wpuf' ),
                'default' => __( 'Description', 'wpuf' )
            ),
            array(
                'name' => 'desc_help',
                'label' => __( 'Post description help text', 'wpuf' ),
            ),
            array(
                'name' => 'excerpt_label',
                'label' => __( 'Excerpt tag label', 'wpuf' ),
                'default' => __( 'Excerpt', 'wpuf' )
            ),
            array(
                'name' => 'excerpt_help',
                'label' => __( 'Excerpt tag help text', 'wpuf' ),
            ),
            array(
                'name' => 'tag_label',
                'label' => __( 'Post tag label', 'wpuf' ),
                'default' => __( 'Tags', 'wpuf' )
            ),
            array(
                'name' => 'tag_help',
                'label' => __( 'Post tag help text', 'wpuf' ),
            ),
            array(
                'name' => 'submit_label',
                'label' => __( 'Post submit button label', 'wpuf' ),
                'default' => __( 'Submit Post', 'wpuf' )
            ),
            array(
                'name' => 'delete_label',
                'label' => __( 'Delete button label', 'wpuf' ),
                'default' => __( 'Delete Post', 'wpuf' )
            ),
            array(
                'name' => 'close_label',
                'label' => __( 'Close button label', 'wpuf' ),
                'default' => __( 'Close', 'wpuf' )
            ),
            array(
                'name' => 'update_label',
                'label' => __( 'Post update button label', 'wpuf' ),
                'default' => __( 'Update Post', 'wpuf' )
            ),
            array(
                'name' => 'updating_label',
                'label' => __( 'Post updating button label', 'wpuf' ),
                'desc' => __( 'the text will be used when the submit button is pressed', 'wpuf' ),
                'default' => __( 'Please wait...', 'wpuf' )
            ),
            array(
                'name' => 'ft_image_label',
                'label' => __( 'Featured image label', 'wpuf' ),
                'default' => __( 'Featured Image', 'wpuf' )
            ),
            array(
                'name' => 'ft_image_btn_label',
                'label' => __( 'Featured Button image label', 'wpuf' ),
                'default' => __( 'Upload Image', 'wpuf' )
            ),
            array(
                'name' => 'attachment_label',
                'label' => __( 'Attachment Label', 'wpuf' ),
                'default' => __( 'Attachments', 'wpuf' )
            ),
            array(
                'name' => 'attachment_btn_label',
                'label' => __( 'Attachment upload button', 'wpuf' ),
                'default' => __( 'Add attachment', 'wpuf' )
            ),
            array(
                'name' => 'login_label',
                'label' => __( 'Login label', 'wpuf' ),
                'default' => __( 'This page is restricted. Please %s to view this page.', 'wpuf' )
            ),
        ) ),
        'wpuf_frontend_posting' => apply_filters( 'wpuf_options_frontend', array(
            array(
                'name' => 'post_status',
                'label' => __( 'Post Status', 'wpuf' ),
                'desc' => __( 'Post status after user submits a post (Default = WordPress default)', 'wpuf' ),
                'type' => 'select',
                'default' => 'default',
                'options' => array(
                    'default' => __( 'Default', 'wpuf' ),
                    'draft' => __( 'Draft', 'wpuf' ),
                    'pending' => __( 'Pending', 'wpuf' ),
                    'publish' => __( 'Publish', 'wpuf' ),
                    'private' => __( 'Private', 'wpuf' )
                )
            ),
            array(
                'name' => 'allow_status',
                'label' => __( 'Allow post status', 'wpuf' ),
                'desc' => __( 'Users will be able to specify post status', 'wpuf' ),
                'type' => 'checkbox',
                'default' => 'off'
            ),
            array(
                'name' => 'post_author',
                'label' => __( 'Post Author', 'wpuf' ),
                'desc' => __( 'Set the new post\'s post author by default', 'wpuf' ),
                'type' => 'select',
                'default' => 'original',
                'options' => array(
                    'original' => __( 'Original Author', 'wpuf' ),
                    'to_other' => __( 'Map to other user', 'wpuf' )
                )
            ),
            array(
                'name' => 'map_author',
                'label' => __( 'Map posts to poster', 'wpuf' ),
                'desc' => __( 'If <b>Map to other user</b> selected, new post\'s post author will be this user by default', 'wpuf' ),
                'type' => 'select',
                'options' => wpuf_list_users()
            ),
            array(
                'name' => 'allow_cats',
                'label' => __( 'Allow to choose category?', 'wpuf' ),
                'desc' => __( 'Allow users to choose category while posting?', 'wpuf' ),
                'type' => 'checkbox',
                'default' => 'on'
            ),
            array(
                'name' => 'exclude_cats',
                'label' => __( 'Exclude category ID\'s', 'wpuf' ),
                'desc' => __( 'Exclude categories from the dropdown', 'wpuf' ),
                'type' => 'text'
            ),
            array(
                'name' => 'default_cat',
                'label' => __( 'Default post category', 'wpuf' ),
                'desc' => __( 'If users are not allowed to choose any category, this category will be used instead', 'wpuf' ),
                'type' => 'select',
                'options' => wpuf_get_cats()
            ),
            array(
                'name' => 'cat_type',
                'label' => __( 'Category Selection type', 'wpuf' ),
                'type' => 'radio',
                'default' => 'normal',
                'options' => array(
                    'normal' => __( 'Normal', 'wpuf' ),
                    'ajax' => __( 'Ajaxified', 'wpuf' ),
                    'checkbox' => __( 'Checkbox', 'wpuf' )
                )
            ),
            array(
                'name' => 'cat_ajax_childless',
                'label' => __( 'Category childless', 'wpuf' ),
                'desc' => __( 'Selected category must be childless (Ajaxified)', 'wpuf' ),
                'type' => 'checkbox',
                'default' => 'on'
            ),
            array(
                'name' => 'enable_featured_image',
                'label' => __( 'Featured Image upload', 'wpuf' ),
                'desc' => __( 'Gives ability to upload an image as featured image', 'wpuf' ),
                'type' => 'radio',
                'default' => 'no',
                'options' => array(
                    'yes' => __( 'Enable', 'wpuf' ),
                    'no' => __( 'Disable', 'wpuf' )
                )
            ),
            array(
                'name' => 'allow_attachment',
                'label' => __( 'Allow attachments', 'wpuf' ),
                'desc' => __( 'Will the users be able to add attachments on posts?', 'wpuf' ),
                'type' => 'radio',
                'default' => 'no',
                'options' => array(
                    'yes' => __( 'Enable', 'wpuf' ),
                    'no' => __( 'Disable', 'wpuf' )
                )
            ),
            array(
                'name' => 'attachment_num',
                'label' => __( 'Number of attachments', 'wpuf' ),
                'desc' => __( 'How many attachments can be attached on a post. Put <b>0</b> for unlimited attachment', 'wpuf' ),
                'type' => 'text',
                'default' => '0'
            ),
            array(
                'name' => 'attachment_max_size',
                'label' => __( 'Attachment max size', 'wpuf' ),
                'desc' => __( 'Enter the maximum file size in <b>KILOBYTE</b> that is allowed to attach', 'wpuf' ),
                'type' => 'text',
                'default' => '2048'
            ),
            array(
                'name' => 'editor_type',
                'label' => __( 'Content editor type', 'wpuf' ),
                'type' => 'select',
                'default' => 'plain',
                'options' => array(
                    'rich' => __( 'Rich Text (tiny)', 'wpuf' ),
                    'full' => __( 'Rich Text (full)', 'wpuf' ),
                    'plain' => __( 'Plain Text', 'wpuf' )
                )
            ),
            array(
                'name' => 'allow_excerpt',
                'label' => __( 'Allow excerpt', 'wpuf' ),
                'desc' => __( 'Users will be able to add excerpt', 'wpuf' ),
                'type' => 'checkbox',
           ),
            array(
                'name' => 'require_excerpt',
                'label' => __( 'Require excerpt', 'wpuf' ),
                'desc' => __( 'Users will be required to add excerpt', 'wpuf' ),
                'type' => 'checkbox',
            ),
            array(
                'name' => 'excerpt_max_chars',
                'label' => __( 'Excerpt Max Characters', 'wpuf' ),
                'desc' => __( 'Excerpt character limit (0=unlimited)', 'wpuf' ),
                'type' => 'text',
                'default' => '0'
            ),
            array(
                'name' => 'allow_slug',
                'label' => __( 'Allow post slug', 'wpuf' ),
                'desc' => __( 'Users will be able to specify post slug', 'wpuf' ),
                'type' => 'checkbox',
                'default' => 'off'
            ),
            array(
                'name' => 'allow_format',
                'label' => __( 'Allow post format', 'wpuf' ),
                'desc' => __( 'Users will be able to specify post format', 'wpuf' ),
                'type' => 'checkbox',
                'default' => 'off'
            ),
            array(
                'name' => 'allow_tags',
                'label' => __( 'Allow post tags', 'wpuf' ),
                'desc' => __( 'Users will be able to add post tags', 'wpuf' ),
                'type' => 'checkbox',
                'default' => 'on'
            ),
            array(
                'name' => 'enable_custom_field',
                'label' => __( 'Enable custom fields', 'wpuf' ),
                'desc' => __( 'You can use additional fields on your post submission form. Add new fields by going <b>Custom Fields</b> option page.', 'wpuf' ),
                'type' => 'checkbox'
            ),
            array(
                'name' => 'enable_post_date',
                'label' => __( 'Enable post date input', 'wpuf' ),
                'desc' => __( 'This will enable users to input the post published date', 'wpuf' ),
                'type' => 'checkbox'
            ),
            array(
                'name' => 'enable_post_expiry',
                'label' => __( 'Enable Post expiration', 'wpuf' ),
                'desc' => __( 'This will enable users to input the post expiration date. This feature depends on <strong>Post Expirator</strong> plugin. ', 'wpuf' ),
                'type' => 'checkbox'
            ),
        ) ),
        'wpuf_dashboard' => apply_filters( 'wpuf_options_dashboard', array(
            array(
                'name' => 'post_type',
                'label' => __( 'Show post type', 'wpuf' ),
                'desc' => __( 'Select the post type that the user will see', 'wpuf' ),
                'type' => 'select',
                'options' => wpuf_get_post_types()
            ),
            array(
                'name' => 'per_page',
                'label' => __( 'Posts per page', 'wpuf' ),
                'desc' => __( 'How many posts will be listed in a page', 'wpuf' ),
                'type' => 'text',
                'default' => '10'
            ),
            array(
                'name' => 'show_user_bio',
                'label' => __( 'Show user bio', 'wpuf' ),
                'desc' => __( 'Users biographical info will be shown', 'wpuf' ),
                'type' => 'checkbox',
                'default' => 'on'
            ),
            array(
                'name' => 'show_post_count',
                'label' => __( 'Show post count', 'wpuf' ),
                'desc' => __( 'Show how many posts are created by the user', 'wpuf' ),
                'type' => 'checkbox',
                'default' => 'on'
            ),
            array(
                'name' => 'show_ft_image',
                'label' => __( 'Show Featured Image', 'wpuf' ),
                'desc' => __( 'Show featured image of the post', 'wpuf' ),
                'type' => 'checkbox'
            ),
            array(
                'name' => 'ft_img_size',
                'label' => __( 'Featured Image size', 'wpuf' ),
                'type' => 'select',
                'options' => wpuf_get_image_sizes()
            ),
        ) ),
        'wpuf_others' => apply_filters( 'wpuf_options_others', array(
            array(
                'name' => 'post_notification',
                'label' => __( 'New post notification', 'wpuf' ),
                'desc' => __( 'A mail will be sent to admin when a new post is created', 'wpuf' ),
                'type' => 'select',
                'default' => 'yes',
                'options' => array(
                    'yes' => __( 'Yes', 'wpuf' ),
                    'no' => __( 'No', 'wpuf' )
                )
            ),
            array(
                'name' => 'enable_post_add',
                'label' => __( 'Users can post?', 'wpuf' ),
                'desc' => __( 'Default = WordPress default', 'wpuf' ),
                'type' => 'select',
                'default' => 'default',
                'options' => array(
                    'default' => __( 'Default', 'wpuf' ),
                    'yes' => __( 'Yes', 'wpuf' ),
                    'no' => __( 'No', 'wpuf' )
                )
            ),
            array(
                'name' => 'enable_post_edit',
                'label' => __( 'Users can edit their posts?', 'wpuf' ),
                'desc' => __( 'Default = WordPress default', 'wpuf' ),
                'type' => 'select',
                'default' => 'default',
                'options' => array(
                    'default' => __( 'Default', 'wpuf' ),
                    'yes' => __( 'Yes', 'wpuf' ),
                    'no' => __( 'No', 'wpuf' )
                )
            ),
            array(
                'name' => 'enable_post_del',
                'label' => __( 'User can delete their posts?', 'wpuf' ),
                'desc' => __( 'Default = WordPress default', 'wpuf' ),
                'type' => 'select',
                'default' => 'default',
                'options' => array(
                    'default' => __( 'Default', 'wpuf' ),
                    'yes' => __( 'Yes', 'wpuf' ),
                    'no' => __( 'No', 'wpuf' )
                )
            ),
            array(
                'name' => 'enable_delete_button',
                'label' => __( 'Add delete button?', 'wpuf' ),
                'desc' => __( 'Adds delete button to edit page', 'wpuf' ),
                'type' => 'select',
                'default' => 'no',
                'options' => array(
                    'yes' => __( 'Yes', 'wpuf' ),
                    'no' => __( 'No', 'wpuf' )
                )
            ),
            array(
                'name' => 'edit_page_id',
                'label' => __( 'Edit Page', 'wpuf' ),
                'desc' => __( 'Select the page where [wpuf_edit] is located', 'wpuf' ),
                'type' => 'select',
                'options' => wpuf_get_pages()
            ),
            array(
                'name' => 'profile_page_id',
                'label' => __( 'Profile Page', 'wpuf' ),
                'desc' => __( 'Select the page where [wpuf_editprofile] is located', 'wpuf' ),
                'type' => 'select',
                'options' => wpuf_get_pages()
            ),
            array(
                'name' => 'admin_access',
                'label' => __( 'Admin area access', 'wpuf' ),
                'desc' => __( 'Allow you to block specific user role to WordPress admin area.', 'wpuf' ),
                'type' => 'select',
                'default' => 'read',
                'options' => array(
                    'install_themes' => __( 'Admin Only', 'wpuf' ),
                    'edit_others_posts' => __( 'Admins, Editors', 'wpuf' ),
                    'publish_posts' => __( 'Admins, Editors, Authors', 'wpuf' ),
                    'edit_posts' => __( 'Admins, Editors, Authors, Contributors', 'wpuf' ),
                    'read' => __( 'Default', 'wpuf' )
                )
            ),
            array(
                'name' => 'cf_show_front',
                'label' => __( 'Show custom fields in the post', 'wpuf' ),
                'desc' => __( 'If you want to show the custom field data in the post added by the plugin.', 'wpuf' ),
                'type' => 'checkbox',
                'default' => 'on'
            ),
            array(
                'name' => 'att_show_front',
                'label' => __( 'Show attachments in the post', 'wpuf' ),
                'desc' => __( 'If you want to show the uploaded attachments in the post', 'wpuf' ),
                'type' => 'checkbox',
                'default' => 'on'
            ),
            array(
                'name' => 'att_show_front_thumb',
                'label' => __( 'Show attachment thumbnails in the post', 'wpuf' ),
                'desc' => __( 'If you want to show the uploaded attachment thumbnails in the post', 'wpuf' ),
                'type' => 'checkbox',
                'default' => 'on'
            ),
            array(
                'name' => 'override_editlink',
                'label' => __( 'Override the post edit link', 'wpuf' ),
                'desc' => __( 'Users see the edit link in post if s/he is capable to edit the post/page. Selecting <strong>Yes</strong> will override the default WordPress link', 'wpuf' ),
                'type' => 'select',
                'default' => 'no',
                'options' => array(
                    'yes' => __( 'Yes', 'wpuf' ),
                    'no' => __( 'No', 'wpuf' )
                )
            ),
            array(
                'name' => 'custom_css',
                'label' => __( 'Custom CSS codes', 'wpuf' ),
                'desc' => __( 'If you want to add your custom CSS code, it will be added on page header wrapped with style tag', 'wpuf' ),
                'type' => 'textarea'
            ),
        ) ),
        'wpuf_payment' => apply_filters( 'wpuf_options_payment', array(
            array(
                'name' => 'charge_posting',
                'label' => __( 'Charge for posting', 'wpuf' ),
                'desc' => __( 'Charge user for submitting a post', 'wpuf' ),
                'type' => 'select',
                'default' => 'no',
                'options' => array(
                    'yes' => __( 'Yes', 'wpuf' ),
                    'no' => __( 'No', 'wpuf' )
                )
            ),
            array(
                'name' => 'force_pack',
                'label' => __( 'Force pack purchase', 'wpuf' ),
                'desc' => __( 'When active, users must have to buy a pack for posting', 'wpuf' ),
                'type' => 'select',
                'default' => 'no',
                'options' => array(
                    'no' => __( 'Disable', 'wpuf' ),
                    'yes' => __( 'Enable', 'wpuf' )
                )
            ),
            array(
                'name' => 'currency',
                'label' => __( 'Currency', 'wpuf' ),
                'type' => 'select',
                'default' => 'USD',
                'options' => array(
                    'AUD' => __( 'Australian Dollar', 'wpuf' ),
                    'CAD' => __( 'Canadian Dollar', 'wpuf' ),
                    'EUR' => __( 'Euro', 'wpuf' ),
                    'GBP' => __( 'British Pound', 'wpuf' ),
                    'JPY' => __( 'Japanese Yen', 'wpuf' ),
                    'USD' => __( 'U.S. Dollar', 'wpuf' ),
                    'NZD' => __( 'New Zealand Dollar', 'wpuf' ),
                    'CHF' => __( 'Swiss Franc', 'wpuf' ),
                    'HKD' => __( 'Hong Kong Dollar', 'wpuf' ),
                    'SGD' => __( 'Singapore Dollar', 'wpuf' ),
                    'SEK' => __( 'Swedish Krona', 'wpuf' ),
                    'DKK' => __( 'Danish Krone', 'wpuf' ),
                    'PLN' => __( 'Polish Zloty', 'wpuf' ),
                    'NOK' => __( 'Norwegian Krone', 'wpuf' ),
                    'HUF' => __( 'Hungarian Forint', 'wpuf' ),
                    'CZK' => __( 'Czech Koruna', 'wpuf' ),
                    'ILS' => __( 'Israeli New Shekel', 'wpuf' ),
                    'MXN' => __( 'Mexican Peso', 'wpuf' ),
                    'BRL' => __( 'Brazilian Real', 'wpuf' ),
                    'MYR' => __( 'Malaysian Ringgit', 'wpuf' ),
                    'PHP' => __( 'Philippine Peso', 'wpuf' ),
                    'TWD' => __( 'New Taiwan Dollar', 'wpuf' ),
                    'THB' => __( 'Thai Baht', 'wpuf' ),
                    'TRY' => __( 'Turkish Lira', 'wpuf' )
                )
            ),
            array(
                'name' => 'currency_symbol',
                'label' => __( 'Currency Symbol', 'wpuf' ),
                'type' => 'text',
                'default' => '$'
            ),
            array(
                'name' => 'cost_per_post',
                'label' => __( 'Cost', 'wpuf' ),
                'desc' => __( 'Cost per post', 'wpuf' ),
                'type' => 'text',
                'default' => '2'
            ),
            array(
                'name' => 'sandbox_mode',
                'label' => __( 'Enable demo/sandbox mode', 'wpuf' ),
                'desc' => __( 'When sandbox mode is active, all payment gateway will be used in demo mode', 'wpuf' ),
                'type' => 'checkbox',
                'default' => 'on'
            ),
            array(
                'name' => 'payment_page',
                'label' => __( 'Payment Page', 'wpuf' ),
                'desc' => __( 'This page will be used to process payment options', 'wpuf' ),
                'type' => 'select',
                'options' => wpuf_get_pages()
            ),
            array(
                'name' => 'payment_success',
                'label' => __( 'Payment Success Page', 'wpuf' ),
                'desc' => __( 'After payment users will be redirected here', 'wpuf' ),
                'type' => 'select',
                'options' => wpuf_get_pages()
            ),
            array(
                'name' => 'active_gateways',
                'label' => __( 'Payment Gateways', 'wpuf' ),
                'desc' => __( 'Active payment gateways', 'wpuf' ),
                'type' => 'multicheck',
                'options' => wpuf_get_gateways()
            ),
        ) ),
        'wpuf_support' => apply_filters( 'wpuf_options_support', array(
            array(
                'name' => 'version',
                'label' => __( 'Version', 'wpuf' ),
                'type' => 'html',
                'desc' => $wpuf->version
            ),		
            array(
                'name' => 'support',
                'label' => __( 'Need Help?', 'wpuf' ),
                'type' => 'html',
                'desc' => __( '
                        <ol>
                            <li>
                                <strong>Check the FAQ and the documentation</strong>
                                <p>First of all, check the <strong><a href="http://wordpress.org/extend/plugins/wp-user-frontend/faq/">FAQ</a></strong> before contacting! Most of the questions you might need answers to have already been asked and the answers are in the FAQ. Checking the FAQ is the easiest and quickest way to solve your problem.</p>
                            </li>
                            <li>
                                <strong>Use the Support Forum</strong>
                                <p>If you were unable to find the answer to your question on the FAQ page, you should check the <strong><a href="http://wordpress.org/tags/wp-user-frontend?forum_id=10">support forum on WordPress.org</a></strong>. If you can’t locate any topics that pertain to your particular issue, post a new topic for it.</p>
                                <p>But, remember that this is a free support forum and no one is obligated to help you. Every person who offers information to help you is a volunteer, so be polite. And, I would suggest that you read the <a href="http://wordpress.org/support/topic/68664">“Forum Rules”</a> before posting anything on this page.</p>
                            </li>
                            <li>
                                <strong>Got an idea?</strong>
                                <p>I would love to hear about your ideas and suggestions about the plugin. Please post them on the <strong><a href="http://wordpress.org/tags/wp-user-frontend?forum_id=10">support forum on WordPress.org</a></strong> and I will look into it</p>
                            </li>
                            <li>
                                <strong>Gettings no response?</strong>
                                <p>I try to answer all the question in the forum. I created the plugin without any charge and I am usually very busy with my other works. As this is a free plugin, I am not bound answer all of your questions.</p>
                            </li>
                            <li>
                                I spent countless hours to build this plugin, <strong><a target="_blank" href="https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=tareq%40wedevs%2ecom&lc=US&item_name=WP%20User%20Frontend&item_number=Tareq%27s%20Planet&currency_code=USD&bn=PP%2dDonationsBF%3abtn_donate_SM%2egif%3aNonHosted">support</a></strong> me if you like this plugin and <a href="http://wordpress.org/extend/plugins/wp-user-frontend/">rate</a> the plugin.
                            </li>
                        </ol>', 'wpuf' )
            )
        ) ),
    );

    return apply_filters( 'wpuf_settings_fields', $settings_fields );
}