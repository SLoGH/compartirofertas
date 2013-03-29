<?php

abstract class Custom_MetaBox_Item_Default
{
	/**
	 * meta box id, unique per meta box
	 * @var string
	 */
	protected $id = '';
	
	/**
	 * Meta box title
	 * @var string
	 */
	protected $title  = '';
	/**
	 * Taxonomy name, accept categories, post_tag and custom taxonomies
	 * @var array 
	 */
	protected $pages = array();
	/**
	 * Where the meta box appear: normal (default), advanced, side; optional
	 * @var string
	 */
	protected $context = 'normal';
	
	/**
	 * List of meta fields (can be added by field arrays)
	 * @var array
	 */
	protected $fields = array();
	
	/**
	 * Use local or hosted images (meta box images for add/remove)
	 * @var boolean
	 */
	protected $local_images = false;
	
	/**
	 * Change path if used with theme set to true, false for a plugin or anything else for a custom path(default false).
	 * @var boolean
	 */
	protected $use_with_theme = null;
	
	/**
	 * Object
	 * @var Custom_MetaBox_Taxonomy 
	 */
	protected $metaTaxObj;
	
	
	function __construct($taxonomy)
	{
		$this->setPageTaxonomy($taxonomy);
	}
	
	/**
	 * Set taxonomy<br/>
	 * @param string $taxonomy Taxonomy name, accept categories, post_tag and custom taxonomies
	 */
	private function setPageTaxonomy($taxonomy)
	{
		$this->pages[] = $taxonomy;
		
	}
	
	/**
	 * Pages which is available metabox 
	 * @return array
	 */
	public function getPageTaxonomy()
	{
		return $this->pages;
	}
	
	/**
	 * Set unique meta box id
	 * @param string $id 
	 */
	protected function setId($id)
	{
		$this->id = $id;
		return $this;
	}
	
	/**
	 * Get unique meta box id
	 * @return string 
	 */
	public function getId()
	{
		return $this->id;
	}
	
	/**
	 * Set Metabox title
	 * @param string $title 
	 */
	protected function setTitle($title)
	{
		$this->title = $title;
		return $this;
	}
	
	/**
	 * Get Metabox title
	 * @return string
	 */
	public function getTitlte()
	{
		return $this->title;
	}
	
	/**
	 * Set where the meta box appear: normal (default), advanced, side;
	 * @param string $context 
	 */
	protected function setContext($context)
	{
		if(in_array($context, array('normal', 'advanced', 'side')))
		{
			$this->context = $context;
		}
		else
		{
			$this->context = 'normal';
		}
		return $this;
	}
	
	/**
	 * Get where the meta box appear
	 * @return string 
	 */
	public function getContext()
	{
		return $this->context;
	}
	
	/**
	 * 
	 */
	public function getFields()
	{
		return $this->fields;
	}
	
	/**
	 * Set use local or hosted images
	 * @param boolean $local_images 
	 */
	protected function setLocalImages($local_images)
	{
		$this->local_images = (boolean) $local_images;
		return $this;
	}
	
	/**
	 * 
	 */
	public function getLocalImages()
	{
		return (boolean) $this->local_images;
	}
	
	/**
	 * Change path if used with theme set to true, false for a plugin or anything else for a custom path(default false).
	 * @param mixed $use 
	 */
	protected function setUseWithTheme($use)
	{
		$this->use_with_theme =  $use;
		return $this;
	}
	
	/**
	 * 
	 * @return boolean 
	 */
	public function getUseWithTheme()
	{
		if(is_null($this->use_with_theme))
		{
			return get_template_directory_uri();
		}
		return $this->use_with_theme;
	}
	
	
	protected function getConfig()
	{
		return array(
				'id'			=> $this->getId(),
				'title'			=> $this->getTitlte(),
				'pages'			=> $this->getPageTaxonomy(),
				'context'		=> $this->getContext(),
				'fields'		=> $this->getFields(),
				'local_images'	=> $this->getLocalImages(),
				'use_with_theme'=> $this->getUseWithTheme(),
		);
	}
	
	/**
	 * create instanse of  Custom_MetaBox_Taxonomy and set it.
	 */
	private function setMetaTaxInstance()
	{
		$this->metaTaxObj = new Custom_MetaBox_Taxonomy($this->getConfig());
	}
	
	/**
	 * Get saved instanse of Custom_MetaBox_Taxonomy
	 * @return Custom_MetaBox_Taxonomy 
	 */
	public function getMetaTaxInstance()
	{
		return $this->metaTaxObj;
	}
	
	
	/**
	 * Add custom elements
	 */
	protected function addFields()
	{
		$this->setMetaTaxInstance();
	}
	
	/**
	 * get Sitebar list
	 * @return array
	 */
	protected function getSidebars()
	{
		return Sidebar_Generator::get_sidebars();
	}

	/**
	 * Finish Declaration of Meta Box and add it.
	 */
	public function run()
	{
		$this->getMetaTaxInstance()->Finish();
	}
	
	protected function getCategoriesList($taxonomy)
	{
		$list = array();
		
		if(taxonomy_exists($taxonomy))
		{
			if($terms = get_terms($taxonomy))
			{
				if(is_array($terms))
				{
					foreach ($terms as $term)
					{
						$list[$term->slug] = $term->name;
					}
				}
			}
		}
		return $list;
	}
}

?>
