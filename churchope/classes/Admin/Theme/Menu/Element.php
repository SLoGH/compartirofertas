<?php

abstract class Admin_Theme_Menu_Element
{
	/**
	 * Form elements types
	 */
	const TYPE_CATLIST			= 'catlist';
	
	const TYPE_CHECKBOX			= 'checkbox';
	
	const TYPE_COLORCHOOSER		= 'colorchooser';
	
	const TYPE_FILE				= 'file';
	
	const TYPE_INFO				= 'info';
	
	const TYPE_INSTALL_DUMMY	= 'install_dummy';
	
	const TYPE_PAGETITLE		= 'pagetitle';
	
	const TYPE_PAGELIST			= 'pagelist';
	
	const TYPE_RADIO			= 'radio';
	
	const TYPE_SELECT			= 'select';
	
	const TYPE_SELECT_SIDEBAR	= 'select_sidebar';
	
	const TYPE_SEPARATOR		= 'separator';
	
	const TYPE_SIDEBAR			= 'sidebar';
	
	const TYPE_SIDEBAR_DELETE	= 'sidebar_delete';
	
	const TYPE_TERMLIST			= 'termlist';
	
	const TYPE_TEXT				= 'text';
	
	const TYPE_TEXTAREA			= 'textarea';
	
	/**
	 * For WP 3.4 is element customized 
	 */
	const CUSTOMIZED = true;
	
	const NOT_CUSTOMIZED = false;
	
	protected $option = array();
	
	protected $customize_section = '';
	
	protected $is_customized = self::NOT_CUSTOMIZED;


	/**
	 * Rendering element 
	 */
	abstract public function render();

	/**
	 * Save element value.
	 */
	public function save()
	{
		if($this->getId())
		{
			 update_option($this->getId(), stripslashes($this->getRequestValue()));
		}
	}
	
	/**
	 * Delete element value
	 */
	public function reset()
	{
		if($this->getId())
		{
			if($this->getStdValue())
			{
				update_option($this->getId(), $this->getStdValue());
			}
			else
			{
				delete_option($this->getId());
			}
		}
		
	}
	
	public function setCustomized($customized = self::CUSTOMIZED)
	{
		$this->is_customized = $customized;
		return $this;
	}
	
	protected function isCustomized()
	{
		return $this->is_customized;
	}


	/**
	 * 3.4
	 * @todo comment need
	 * @param string $section
	 */
	public function setCustomizeSection($section)
	{
		$this->customize_section = $section;
		
		if($this->isCustomized())
		{
			add_action( 'customize_register', array($this, 'element_customize_register' ), 30);
			add_action( 'customize_register', array($this, 'add_customize_control' ), 40);
		}
		
	}
	
	function element_customize_register($wp_customize)
	{
		if($this->getId())
		{
			$wp_customize->add_setting( $this->getId(), array(
				'default'        => $this->getStdValue(),
				'type'           => 'option',				// @todo wtf option
				'capability'     => 'edit_theme_options',
			) );
		}
	}
	
	public function add_customize_control($wp_customize)
	{
		return;
	}


	/**
	 * 
	 * @todo comment need
	 * @return string
	 */
	public function getCustomizeSection()
	{
		return $this->customize_section;
	}
	
	/**
	 * Get value after form submit 
	 */
	public function getRequestValue()
	{
		
		if(isset($_REQUEST[$this->getId()]))
		{
			return $_REQUEST[$this->getId()];
		}
		return '';
	}
	
	/**
	 * Save to DB element default value if not exist.<br/>
	 */
	public function setDefaultValue()
	{
		if(get_option($this->getId()) === false)
		{
			update_option($this->getId(), $this->getStdValue());
		}
	}
			
	
	/**
	 * Return option values
	 * @return array
	 */
	public function getOption()
	{
		return $this->option;
	}
	
	public function setName($name)
	{
		$this->option['name'] = $name;
		return $this;
	}
	
	protected function getName()
	{
		if(isset($this->option['name']))
		{
			return $this->option['name'];
		}
		return false;
	}
	
	public function setDescription($description)
	{
		$this->option['desc'] = $description;
		return $this;
	}
	public function setId($id)
	{
		$this->option['id'] = $id;
		return $this;
	}
	
	public function getId()
	{
		if(isset($this->option['id']))
		{
			return $this->option['id'];
		}
		return false;
	}
	
	public function getStdValue()
	{
		return $this->std;
	}
	
	public function setStd($std)
	{
		$this->option['std'] = $std;
		return $this;
	}
	public function setOptions($options)
	{
		$this->option['options'] = $options;
		return $this;
	}
	public function setType($type)
	{
		$this->option['type'] = $type;
		return $this;
	}
	public function setSize($size)
	{
		$this->option['size'] = $size;
		return $this;
	}
	
	function __get($name)
	{
		if(isset($this->option[$name]))
		{
			return $this->option[$name];
		}
		return '';
	}
	
	/**
	 * Get element HTML header
	 * @return string
	 */
	protected function getElementHeader()
	{
		ob_start();
		?>
		<li>
			<label for="<?php echo $this->id; ?>">
				<?php echo $this->name; ?>
			</label>
				<a href="#" title="<?php echo $this->desc; ?>" class="th_help">
					<img src="<?php echo get_template_directory_uri() . '/backend/img/help.png'; ?>"  width="15" height="16"  alt="" />
				</a><br /><br />
		<?php
		$html = ob_get_clean();
		
		return $html;
	}
	
	/**
	 * Get Element HTML footer
	 * @return string 
	 */
	protected function getElementFooter()
	{
		return '</li>';
	}
}
?>
