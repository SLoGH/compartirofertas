<?php


abstract class Custom_Posts_Type_Default
{
	/**
	 * post option name in DB
	 * @var string 
	 */
	protected $post_slug_option	= '';
	/**
	 * Tax option name in DB
	 * @var string
	 */
	protected $tax_slug_option	= '';
	/**
	 * Default post slug.
	 * @var string
	 */
	protected $default_post_slug = '';
	/**
	 * Default Tax slug
	 * @var string
	 */
	protected $default_tax_slug = '';
	/**
	 * Text domain for __() ...
	 * @var string
	 */
	protected $text_domain = '';

	/**
	 * Post type.
	 * @uses register_post_type
	 * @var string 
	 */
	protected $post_type_name	= '';
	
	/**
	 * The name of the taxonomy.
	 * @uses register_taxonomy
	 * @var string
	 */
	protected $taxonomy_name = '';
	
	function __construct()
	{
		$this->setTextDomain(strtolower(THEMENAME));
		$this->init();
//		flush_rewrite_rules(false);
	}
	
	/**
	 * The name of the taxonomy.
	 * @return string
	 */
	public function getTaxonomyName()
	{
		return $this->taxonomy_name;
	}
	
	/**
	 * Post type.
	 * @return string. 
	 */
	protected function getPostTypeName()
	{
		return $this->post_type_name;
	}

		/**
	 * set textdomain for translation
	 * @param string $text_domain 
	 */
	protected function setTextDomain($text_domain)
	{
		$this->text_domain = $text_domain;
	}
	
	/**
	 * Get textdomain for translation
	 * @return string
	 */
	protected function getTextDomain()
	{
		return $this->text_domain;
	}
	
	/**
	 * Get default custom post slug
	 * @return string
	 */
	public function getDefaultPostSlug()
	{
		return $this->default_post_slug;
	}
	
	/**
	 * Set default custom post slug
	 * @param string $slug 
	 */
	protected function setDefaultPostSlug($slug)
	{
		$this->default_post_slug = $slug;
	}
	
	/**
	 * Get default custom page tax
	 * @return type 
	 */
	public function getDefaultTaxSlug()
	{
		return $this->default_tax_slug;
	}
	
	/**
	 * set default custom page tax slug
	 * @param type $slug 
	 */
	protected function setDefaultTaxSlug($slug)
	{
		$this->default_tax_slug = $slug;
	}
	
	/**
	 * Get name of option under which value saved in DB
	 * @return string
	 */
	public function getTaxSlugOptionName()
	{
		return SHORTNAME.$this->tax_slug_option;
	}
	
	/**
	 * Get name of option under which value saved in DB
	 * @return string 
	 */
	public function getPostSlugOptionName()
	{
		return SHORTNAME.$this->post_slug_option;
	}
	
	/**
	 * Get custom post type slug
	 * @return type 
	 */
	protected function getPostSlug()
	{
		$post_slug = get_option( $this->getPostSlugOptionName() );

		if ($post_slug == '')
		{
			$post_slug = $this->getDefaultPostSlug(); // post item
		}
		
		return $post_slug;
	}
	
	/**
	 * Get custom post tax slug
	 * @return string 
	 */
	public function getTaxSlug()
	{
		$tax_slug = get_option( $this->getTaxSlugOptionName() );
		
		if ($tax_slug == '')
		{
			$tax_slug = $this->getDefaultTaxSlug(); // TAX
		}
		
		return $tax_slug;
	}
	
	/**
	 * A plural descriptive name for the post type marked for translation.
	 *  
	 */
	protected abstract function getPostLabeles();
	
	/**
	 * An array of labels for this taxonomy. By default tag labels are used for non-hierarchical types and category labels for hierarchical ones.  
	 */
	protected abstract function getTaxLabels();
	
	/**
	 * register_post_type
	 * register_taxonomy 
	 */
	protected abstract function init();
	
	/**
	 * Adding all action & filters
	 */
	public abstract function run();
	
	/**
	 * Off default comments
	 */
	function default_comments_off($data)
	{
		if ($data['post_type'] == $this->getPostTypeName() && $data['post_status'] == 'auto-draft')
		{
			$data['comment_status'] = 0;
			$data['ping_status'] = 0;
		}
		return $data;
	}

	/**
	 * Add custom fields to custom type category admin page.
	 * @param Custom_MetaBox_Item_Default $meta 
	 */
	protected function addCustomMetaBox($meta)
	{
		if($meta instanceof Custom_MetaBox_Item_Default)
		{
			$meta->run();
		}
	}
	
}
?>
