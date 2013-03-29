<?php

/**
 * Default widget class
 * @abstract 
 */
abstract class Widget_Default extends WP_Widget
{
	/**
	 * Widget Prefix
	 * @var string
	 */
	protected $prefix;
	/**
	 * Textdomain for translation
	 * @var string 
	 */
	protected $textdomain;
	
	/**
	 * 
	 * @var string
	 */
	protected $classname = '';
	
	/**
	 * required if more than 250px
	 * @var int 
	 */
	protected $width = 200;
	
	/**
	 * currently not used but may be needed in the future
	 * @var int 
	 */
	protected $height = 350;
	/**
	 * shown on the configuration page.
	 * @var string
	 */
	protected $description = '';
	
	/**
	 * Name
	 * @var string
	 */
	protected $__name = '';
	
	/**
	 * Part of base_id. THEMENAME-{__id}
	 * @var type 
	 */
	protected $__id = '';
	
	/**
	 * Delimiter between the name of the THEME and the name of the widget <br/>
	 * displayed on the configuration page
	 * @var string
	 */
	protected $name_delimiter = ' &rarr; ';
	/**
	 * Wiget constructor
	 */
	function __construct()
	{
		$this->setPrefix(strtolower(THEMENAME));
		$this->setTextdomain(strtolower(THEMENAME));
		parent::__construct($this->getBaseId(), $this->getTranslatedName(), $this->getWidgetOption(), $this->getWidgetControlOption());
	}
	
	/**
	 * Translated name for the widget displayed on the configuration page/
	 * @return string
	 */
	protected function getTranslatedName()
	{
		return __(THEMENAME . $this->getNameDelimiter().$this->getName(), $this->getTextdomain());
	}
	
	/**
	 * Get classname and translated description
	 * @return array Optional Passed to wp_register_sidebar_widget()
	 *  - classname:
	 *  - description: shown on the configuration page
	 */
	protected function getWidgetOption()
	{
		$widget_ops = array( 'classname' => $this->getClassName(),
							 'description' => __( $this->getDescription(), $this->getTextdomain() ));
		
		return $widget_ops;
	}
	
	/**
	 * Get  Base ID for the widget, lower case,
	 * if left empty a portion of the widget's class name will be used. Has to be unique.
	 * @return string 
	 */
	protected function  getBaseId()
	{
		$base_id = "{$this->getPrefix()}-{$this->getIdSuffix()}";
		return strtolower($base_id);
	}

	
	/**
	 * Get wodget control data
	 * @return array Passed to wp_register_widget_control()
	 *	 - width: required if more than 250px
	 *	 - height: currently not used but may be needed in the future
	 *	 - id_base:
	 */
	protected function getWidgetControlOption()
	{
		$control_ops = array('width'	=> $this->getWidth(),
							 'height'	=> $this->getHeight(),
							 'id_base'	=> $this->getBaseId());
		return $control_ops;
	}
	
	/**
	 * Get wigget prefix( lowercase THEMNAME) 
	 * @return string
	 */
	public function getPrefix()
	{
		return $this->prefix;
	}
	
	protected function setPrefix($prefix)
	{
		$this->prefix = $prefix;
	}
	
	/**
	 * Get textdomain for translation
	 * @return string
	 */
	public function getTextdomain()
	{
		return $this->textdomain;
	}
	
	protected function setTextdomain($textdomain)
	{
		$this->textdomain = $textdomain;
	}
	
	/**
	 * Get widget classname
	 * @return string 
	 */
	public function getClassName()
	{
		return $this->classname;
	}
	
	/**
	 * Set widget classname 
	 * @param string $classname 
	 */
	public function setClassName($classname)
	{
		$this->classname = $classname;
	}
	
	public function getWidth()
	{
		return $this->width;
	}
	
	public function setWidth($width)
	{
		$this->width = $width;
	}
	
	public function getHeight()
	{
		
		return $this->height;
	}
	
	public function setHeight($height)
	{
		$this->height = $height;
	}
	
	/**
	 * Get widget description for shown on the configuration page
	 * @return string 
	 */
	public function getDescription()
	{
		
		return $this->description;
	}
	
	/**
	 * Set widget description for shown on the configuration page
	 * @param string $description shown on the configuration page
	 */
	public function setDescription($description)
	{
		$this->description = $description;
	}
	
	/**
	 * Set Widget name<br/>
	 * for the widget displayed on the configuration page
	 * @param string $name 
	 */
	protected function setName($name)
	{
		$this->__name = $name;
	}
	
	/**
	 * Get widget name<br/>
	 * for the widget displayed on the configuration page
	 * @return string
	 */
	public function getName()
	{
		return $this->__name;
	}
	
	/**
	 * Set suffix part of id_base (THEMENAME-{suffix})
	 * @param string $suffix 
	 */
	protected function setIdSuffix($suffix)
	{
		$this->__id  = $suffix;
	}
	
	/**
	 * Get suffix part of id_base (THEMNAME-{suffix})
	 * @return string
	 */
	protected function getIdSuffix()
	{
		return $this->__id;
	}
	
	/**
	 * Set Delimetr for the THEMANAME and widget name displayed on the configuration page
	 * @param string $name_delimiter 
	 */
	protected function setNameDelimiter($name_delimiter)
	{
		$this->name_delimiter = $name_delimiter;
	}
	
	/**
	 * Get Delimetr for the THEMANAME and widget name displayed on the configuration page
	 * @return string
	 */
	protected function getNameDelimiter()
	{
		return $this->name_delimiter;
	}
	
	
	/**
	 * Check is plugin wpml is active
	 * @return boolean
	 */
	protected function isWPML_PluginActive()
	{
		return defined('ICL_LANGUAGE_CODE');
	}
}
?>