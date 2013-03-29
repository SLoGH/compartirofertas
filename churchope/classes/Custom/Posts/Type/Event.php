<?php

class Custom_Posts_Type_Event extends Custom_Posts_Type_Default
{
	const POST_TYPE = 'th_events';
	const TAXONOMY = 'th_events_cat';
	
	protected $post_slug_option	= '_slug_event';
	protected $tax_slug_option	= '_slug_event_cat';

	protected $post_type_name	= self::POST_TYPE;
	
	protected $taxonomy_name = self::TAXONOMY;

	const DEFAULT_TAX_SLUG = 'th_event_cat';
	
	const DEFAULT_POST_SLUG = 'th_event';
	
	
	
	function __construct()
	{
		$this->setDefaultPostSlug(self::DEFAULT_POST_SLUG);
		$this->setDefaultTaxSlug(self::DEFAULT_TAX_SLUG);
		parent::__construct();
	}
	
	protected function init()
	{
		register_post_type($this->getPostTypeName(), array(
					'labels'				=> $this->getPostLabeles(),
					'public'				=> true,
					'show_ui'				=> true,
					'_builtin'				=> false,
					'capability_type'		=> 'post',
					'_edit_link'			=> 'post.php?post=%d',
					'rewrite'				=> array("slug" =>  $this->getPostSlug()), 
					'hierarchical'			=> false,
					'menu_icon'				=> get_template_directory_uri() . '/backend/img/i_events.png',
					'query_var'				=> true,
					'publicly_queryable'	=> true,
					'exclude_from_search'	=> false,
					'supports'				=> array('title', 'editor', 'thumbnail', 'excerpt', 'comments')
		));


		register_taxonomy($this->getTaxonomyName(),$this->getPostTypeName(),
					array(
					'hierarchical'			=> true,
					'labels'				=> $this->getTaxLabels(),
					'show_ui'				=> true,
					'query_var'				=> true,
					'rewrite'				=> array('slug' => $this->getTaxSlug()),
		));
		
		
		
	}
	////////////////////////////////////////////
	public function run()
	{
		add_filter("manage_edit-{$this->getPostTypeName()}_columns", array(&$this, "th_post_type_columns"));
		add_filter('wp_insert_post_data', array(&$this, 'default_comments_off'));
		add_action("manage_posts_custom_column", array(&$this, "th_post_type_custom_columns"));
		add_action('restrict_manage_posts', array(&$this, 'th_post_type_restrict_manage_posts'));
		add_action('request', array(&$this, 'th_request'));
		add_action('init', array(&$this, "thInit"));
		add_action( 'init', array(&$this,'event_rewrites_init' ));
		add_filter( 'query_vars', array(&$this, 'event_query_vars'));
		add_action('template_redirect', array(&$this,'template_redirect_file'));

		
		$this->addCustomMetaBox( new Custom_MetaBox_Item_Event($this->getTaxonomyName()) );
		
	}
	
	function event_rewrites_init(){
		add_rewrite_rule(
			"{$this->getTaxSlug()}/([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/?$",
			'index.php?pagename=customeventslist&event_year=$matches[1]&event_month=$matches[2]&event_day=$matches[3]',
			'top' );
			
		add_rewrite_rule(
			"{$this->getTaxSlug()}/([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/page/(\d+)/?$",
			'index.php?pagename=customeventslist&event_year=$matches[1]&event_month=$matches[2]&event_day=$matches[3]&page=$matches[4]',
			'top' );
	}


	function event_query_vars( $query_vars ){
		$query_vars[] = 'event_year';
		$query_vars[] = 'event_month';
		$query_vars[] = 'event_day';
//		$query_vars[] = 'event_pagination';
		
		return $query_vars;
	}
	
	function template_redirect_file()
	{
		if (get_query_var('pagename') == 'customeventslist')
		{
			locate_template(array('page-customeventslist.php'), true, true);
			exit();
		}
	}


	function thInit()
	{
		global $thevent;
		$thevent = $this;
	}

	function th_request($request)
	{
		if (is_admin()
				&& $GLOBALS['PHP_SELF'] == '/wp-admin/edit.php'
				&& isset($request['post_type'])
				&& $request['post_type'] == $this->getPostTypeName())
		{
			$th_events_cat = (isset($request[$this->getTaxonomyName()]) ? $request[$this->getTaxonomyName()] : NULL);
			$term = get_term($th_events_cat, $this->getTaxonomyName());
			$request['term'] = isset($term->slug);
		}
		return $request;
	}

	function th_post_type_restrict_manage_posts()
	{
		global $typenow;

		if ($typenow == $this->getPostTypeName())
		{
			$filters = array($this->getTaxonomyName());

			foreach ($filters as $tax_slug)
			{
				// retrieve the taxonomy object
				$tax_obj = get_taxonomy($tax_slug);
				$tax_name = $tax_obj->labels->name;
				// retrieve array of term objects per taxonomy
				$terms = get_terms($tax_slug);

				// output html for taxonomy dropdown filter
				echo "<select name='$tax_slug' id='$tax_slug' class='postform'>";
				echo "<option value=''>Show All $tax_name</option>";
				$th_slider_tax_slug = (isset($_GET[$tax_slug]) ? $_GET[$tax_slug] : NULL);
				foreach ($terms as $term)
				{
					// output each select option line, check against the last $_GET to show the current option selected
					echo '<option value=' . $term->slug, $th_slider_tax_slug == $term->slug ? ' selected="selected"' : '', '>' . $term->name . ' (' . $term->count . ')</option>';
				}
				echo "</select>";
			}
		}
	}

	function th_post_type_columns($columns)
	{

		$columns = array(
			"cb" => "<input type=\"checkbox\" />",
			"title" => __("Event Item Title", 'churchope'),
			"thevents_preview" => __("Image preview", 'churchope'),
			"thevents_categories" => __("Assign to Events Category(s)", 'churchope'),
			"thevents_event_start" => __("Event start at:", 'churchope'),
			"thevents_event_is_repetition" => __("Is repetition:", 'churchope'),
			"thevents_event_period" =>__("Repeat every:", 'churchope'),
		);

		return $columns;
	}

	function th_post_type_custom_columns($column)
	{
		global $post;
		switch ($column)
		{

			case "thevents_preview":
				?>
				<?php if (has_post_thumbnail()) : ?>
					<a href="post.php?post=<?php echo $post->ID ?>&action=edit"><?php the_post_thumbnail('event_widget'); ?></a>
					<?php
				endif;
				break;

			case "thevents_categories":
				$kgcs = get_the_terms(0, $this->getTaxonomyName());
				if (!empty($kgcs))
				{
					$kgcs_html = array();
					foreach ($kgcs as $kgc)
						array_push($kgcs_html, $kgc->name);

					echo implode($kgcs_html, ", ");
				}
				break;
			case 'thevents_event_start':
				echo get_post_meta($post->ID,SHORTNAME . Widget_Event::EVENT_DATE_META_KEY , true);
				break;
			case 'thevents_event_is_repetition':
				$repeat = get_post_meta($post->ID,SHORTNAME . Widget_Event::EVENT_REPEATING_META_KEY, true );
				if($repeat)
				{
					echo __('Yes', 'churchope');
				}
				else
				{
					echo __('No', 'churchope');
				}
				break;
			case 'thevents_event_period':
				$repeat = get_post_meta($post->ID,SHORTNAME . Widget_Event::EVENT_REPEATING_META_KEY, true );
				if($repeat)
				{
					$period = get_post_meta($post->ID,SHORTNAME . Widget_Event::EVENT_INTERVAL_META_KEY , true);
					if($period)
					{
						echo __(ucfirst($period), 'churchope');
					}
				}
				break;
		}
	}

	protected function getPostLabeles()
	{

		$labels = array(
			'name'				=> _x('Events', 'post type general name', 'churchope'),
			'all_items'			=> _x('Event Posts', 'post type general name', 'churchope'),
			'singular_name'		=> _x('Event', 'post type singular name', 'churchope'),
			'add_new'			=> _x('Add New', 'item', 'churchope'),
			'add_new_item'		=> __('Add New Item', 'churchope'),
			'edit_item'			=> __('Edit Item', 'churchope'),
			'new_item'			=> __('New Item', 'churchope'),
			'view_item'			=> __('View Item', 'churchope'),
			'search_items'		=> __('Search Items', 'churchope'),
			'not_found'			=> __('No items found', 'churchope'),
			'not_found_in_trash' => __('No items found in Trash', 'churchope'),
			'parent_item_colon'	=> ''
		);
		
		return $labels;
	}

	protected function getTaxLabels()
	{
		$labels = array(
			'name'					=> _x('Event Categories', 'taxonomy general name', 'churchope'),
			'singular_name'			=> _x('Event Category', 'taxonomy singular name', 'churchope'),
			'search_items'			=> __('Search Events Categories', 'churchope'),
			'popular_items'			=> __('Popular Events Categories', 'churchope'),
			'all_items'				=> __('All Events Categories', 'churchope'),
			'parent_item'			=> null,
			'parent_item_colon'		=> null,
			'edit_item'				=> __('Edit Events Category', 'churchope'),
			'update_item'			=> __('Update Events Category', 'churchope'),
			'add_new_item'			=> __('Add New Events Category', 'churchope'),
			'new_item_name'			=> __('New Events Category Name', 'churchope'),
			'add_or_remove_items'	=> __('Add or remove Events Categories', 'churchope'),
			'choose_from_most_used' => __('Choose from the most used Events Categories', 'churchope'),
			'separate_items_with_commas' => __('Separate Events Categories with commas', 'churchope'),
		);
		return $labels;
	}
}
?>
