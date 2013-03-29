<?php

abstract class Admin_Theme_Menu_Item
{
	
	/**
	 * Flag to $this->last_action after form submit
	 */
	const ACTION_SAVE = 'save';
	
	/**
	 * Flag to $this->last_action after form reset
	 */
	const ACTION_RESET = 'reset';
	
	/**
	 * Flag to $this->last_action load page
	 */
	const ACTION_DEFAULT = 'load';
	
	/**
	 * Save action name for wp_nonce_field && check_admin_referer 
	 */
	const NONCE_SAVE_ACTION = 'th-save-action_nonce';
	
	/**
	 * Reset action name for wp_nonce_field && check_admin_referer 
	 */
	const NONCE_RESET_ACTION = 'th-reset-action_nonce';
	/**
	 * List of form elements
	 */
	protected $options = array();
	
	/**
	 * The text to be displayed in the title tags<br/>
	 * of the page when the menu is selected
	 * @var string
	 */
	protected $title = '';
	
	/**
	 * The text to be used for the menu
	 * @var srting
	 */
	protected $menu_title = '';
	
	/**
	 * The capability required for this menu<br/>
	 * to be displayed to the user.
	 * @var string
	 */
	protected $capability = '';
	
	/**
	 * The slug name to refer to this menu by (should be unique for this menu).<br/>
	 * If you want to NOT duplicate the parent menu item,<br/>
	 * you need to set the name of the $menu_slug exactly the same as the parent slug. 
	 * @var string
	 */
	protected $menu_slug = '';
	
	/**
	 * Last form action. example
	 * @var type 
	 */
	protected $last_action = '';
	
	/**
	 * Current theme name
	 * @var string
	 */
	protected $theme_name = '';
	
	/**
	 * The slug name for the parent menu (or the file name of a standard WordPress admin page)
	 * @var string 
	 */
	protected $parent_slug = '';
	
	/**
	 * Initialize all page Elements 
	 */
	abstract public function init();
	
	function __construct($parent_slug = '')
	{
		if(strlen($parent_slug))
		{
			$this->setParentSlug($parent_slug);
			
			add_action( 'customize_register', array($this, 'theme_customize_register'), 20);

			add_submenu_page($this->getParentSlug(),
							$this->getPageTitle(),
							$this->getMenuTitle(),
							$this->getCapability(),
							$this->getMenuSlug(),
							array($this, 'renderPage') );
			
		}
	}
	
	function theme_customize_register($wp_customize)
	{
		$wp_customize->add_section( $this->getMenuSlug(), array(
			'title'          => __( $this->getMenuTitle()),
			'priority'       => 35,
		));
	}
	
	
	/**
	 * Call save method for each form element 
	 */
	public function saveForm()
	{
		foreach($this->getOptionsList() as $element)
		{
			$element->save();
		}
		$this->setLastAction(self::ACTION_SAVE);
		
		$custom_style = new Custom_CSS_Style();
		$custom_style->reinit();
	}
	
	/**
	 * Call reset method for each element 
	 */
	public function resetForm()
	{
		foreach($this->getOptionsList() as $element)
		{
			$element->reset();
		}
		$this->setLastAction(self::ACTION_RESET);
		
		$custom_style = new Custom_CSS_Style();
		$custom_style->reinit();
	}
	
	/**
	 * On theme activation save to DB default settings elements values.
	 */
	public function saveDefaultValues()
	{
		foreach($this->getOptionsList() as $option_page)
		{
			$option_page->setDefaultValue();
		}
	}
	
//	/**
//	 * Return item elements as array<br/>
//	 * @return type 
//	 */
//	public function asArray()
//	{
//		
//		$result = array();
//		
//		foreach($this->getOptionsList() as $element)
//		{
//			$result[] = $element->getOption();
//		}
//		
//		return $result;
//	}
	
	/**
	 * Render header & footer page with all elements 
	 */
	public function renderPage()
	{
		$html = $this->getActionMessage();
		
		$html .= '<div class="th_admin_page">';
		
		if($this->getMenuSlug() != SHORTNAME . '_dummy')
		{
			$html .= '<form method="post"  enctype="multipart/form-data"  action="">';
		}
		
		foreach($this->getOptionsList() as $option)
		{
			$html .= $option->render();
		}
		
		if($this->getMenuSlug() != SHORTNAME.'_dummy')
		{
			$save_nonce = wp_nonce_field(self::NONCE_SAVE_ACTION);
			$reset_nonce = wp_nonce_field(self::NONCE_RESET_ACTION);
			$html .= '<input name="save_options" type="submit" value="Save" class="th_save"  />'.
				$save_nonce	
				.'</form>
			<form method="post" id="th_reset"  action="">'.
				$reset_nonce
				.'<input name="reset_options" type="submit" value="Reset" class="th_reset" />
			</form>';
		}
		
		$html .= '</div>';
		
		if($this->getParentSlug())
		{
			echo $html;
		}
	}
	
	/**
	 * Get message after save\reset
	 * @return string 
	 */
	public function getActionMessage()
	{
		$message = '';
		
		switch($this->getLastAction())
		{
			case self::ACTION_SAVE:
				$message = '<div id="message" class="updated fade" style="width:450px"><p><strong>Settings have been saved.</strong></p></div>';
				break;
			case self::ACTION_RESET:
				$message = '<div id="message" class="updated fade" style="width:450px"><p><strong>Settings have been reset.</strong></p></div>';
				break;
			default :
				$message = '';
				break;
		}
		return $message;
	}
	
	/**
	 * Get array of Admin_Theme_Menu_Element objects<br/>
	 * for tis page(item)
	 * @return array
	 */
	public function getOptionsList()
	{
		return $this->options;
	}

	/**
	 * Add option to list
	 * @param array $option 
	 */
	public function addOption($option)
	{
		$option->setCustomizeSection($this->getMenuSlug());
		
		$this->options[] = $option;
	}
	
	public function setPageTitle($title)
	{
		$this->title = $title;
		return $this;
	}
	
	public function getPageTitle()
	{
		return $this->title;
	}
	
	
	public function setMenuTitle($menu_title)
	{
		$this->menu_title = $menu_title;
		return $this;
	}
	
	public function getMenuTitle()
	{
		return $this->menu_title;
	}
	
	
	public function setCapability($capability)
	{
		$this->capability = $capability;
		return $this;
	}
	
	public function getCapability()
	{
		return $this->capability;
	}
	
	
	public function setMenuSlug($menu_slug)
	{
		$this->menu_slug = $menu_slug; 
		return $this;
	}
	
	public function getMenuSlug()
	{
		return $this->menu_slug;
	}
	
	/**
	 * Set last form action load\reset\save
	 * @param type $action 
	 */
	protected function setLastAction($action = self::ACTION_DEFAULT)
	{
		$this->last_action = $action;
	}
	
	protected function getLastAction()
	{
		return $this->last_action;
	}
	
	protected function getThemeName()
	{
		return $this->theme_name;
	}
	
	public function setThemeName($theme_name)
	{
		$this->theme_name = $theme_name;
	}
	
	public function setParentSlug($parent_slug)
	{
		$this->parent_slug = $parent_slug;
	}
	
	public function getParentSlug()
	{
		return $this->parent_slug;
	}
}
?>