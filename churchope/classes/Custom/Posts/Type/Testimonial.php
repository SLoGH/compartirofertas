<?php

class Custom_Posts_Type_Testimonial extends Custom_Posts_Type_Default
{
	const POST_TYPE = 'th_testimonials';
	const TAXONOMY = 'th_testimonials_cat';
	
	protected $post_slug_option	= '_slug_testimonial';
	protected $tax_slug_option	= '_slug_testimonial_cat';
	protected $post_type_name	= self::POST_TYPE;
	
	protected $taxonomy_name = self::TAXONOMY;

	const DEFAULT_TAX_SLUG = 'th_testimonial_cat';
	
	const DEFAULT_POST_SLUG = 'th_testimonial';
	
	
	
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
					'menu_icon'				=> get_template_directory_uri() . '/backend/img/i_testimonials.png',
					'query_var'				=> true,
					'publicly_queryable'	=> true,
					'exclude_from_search'	=> false,
					'supports'				=> array('title', 'editor')
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
							add_filter('wp_insert_post_data', array($this, 'default_comments_off'));
							add_action("manage_posts_custom_column", array(&$this, "th_post_type_custom_columns"));
							add_action('restrict_manage_posts', array(&$this, 'th_post_type_restrict_manage_posts'));
							add_action('request', array(&$this, 'thtestimonials_request'));
							add_action('init', array(&$this, "thTestimonialsInit"));
							$this->addCustomMetaBox(new Custom_MetaBox_Item_Testimonial($this->getTaxonomyName()));

						}

						function thTestimonialsInit()
						{
							global $thtestimonials;
							$thtestimonials = $this;
						}

						function thtestimonials_request($request)
						{
							if (is_admin()
									&& $GLOBALS['PHP_SELF'] == '/wp-admin/edit.php'
									&& isset($request['post_type'])
									&& $request['post_type'] == $this->getPostTypeName())
							{
								$th_portoflios_cat = (isset($request[$this->getTaxonomyName()]) ? $request[$this->getTaxonomyName()] : NULL);
								$term = get_term($th_portoflios_cat, $this->getTaxonomyName());
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
	////////////////////////////////////////////
	//
	function th_post_type_columns($columns)
	{
		$columns = array(
			"cb" => "<input type=\"checkbox\" />",
			"title" => __("Testimonial Item Title", 'churchope'),
			"thtestimonials_preview" => __("Image preview", 'churchope'),
			"thtestimonials_categories" => __("Assign to Testimonials Category(s)", 'churchope')
		);

		return $columns;
	}

	function th_post_type_custom_columns($column)
	{
		global $post;
		switch ($column)
		{
			case "thtestimonials_preview":
				?>
				<?php if (has_post_thumbnail()) : ?>
					<a href="post.php?post=<?php echo $post->ID ?>&action=edit"><?php the_post_thumbnail('testimonial_widget'); ?></a>
					<?php
				endif;
				break;

			case "thtestimonials_categories":
				$kgcs = get_the_terms(0, $this->getTaxonomyName());
				if (!empty($kgcs))
				{
					$kgcs_html = array();
					foreach ($kgcs as $kgc)
						array_push($kgcs_html, $kgc->name);

					echo implode($kgcs_html, ", ");
				}
				break;
		}
	}

	protected function getPostLabeles()
	{
		$labels = array(
			'name'				=> _x('Testimonials', 'post type general name', 'churchope'),
			'all_items'			=> _x('Testimonial Posts', 'post type general name', 'churchope'),
			'singular_name'		=> _x('Testimonial', 'post type singular name', 'churchope'),
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
			'name'					=> _x('Testimonial Categories', 'taxonomy general name', 'churchope'),
			'singular_name'			=> _x('Testimonial Category', 'taxonomy singular name', 'churchope'),
			'search_items'			=> __('Search Testimonials Categories', 'churchope'),
			'popular_items'			=> __('Popular Testimonials Categories', 'churchope'),
			'all_items'				=> __('All Testimonials Categories', 'churchope'),
			'parent_item'			=> null,
			'parent_item_colon'		=> null,
			'edit_item'				=> __('Edit Testimonials Category', 'churchope'),
			'update_item'			=> __('Update Testimonials Category', 'churchope'),
			'add_new_item'			=> __('Add New Testimonials Category', 'churchope'),
			'new_item_name'			=> __('New Testimonials Category Name', 'churchope'),
			'add_or_remove_items'	=> __('Add or remove Testimonials Categories', 'churchope'),
			'choose_from_most_used' => __('Choose from the most used Testimonials Categories', 'churchope'),
			'separate_items_with_commas' => __('Separate Testimonials Categories with commas', 'churchope'),
		);
		return $labels;
	}
}
?>
