<?php
/**
 * Class for worck with custom CSS data.
 */
class Custom_CSS_Style
{
	const DEFAULT_COLOR = '#00a0c6';
	
	const DEFAULT_FONT = 'Open Sans';
	
	const DEFAULT_FONT_STYLE = 'italic';
		
	/**
	 * File with $values
	 */
	const TEMPLATE_FILE =  'style.template.css';
	
	/**
	 * Ready file 
	 */
	const CUSTOM_STYLESHEET_FILE = 'skin.css';
	
	
	const NEED_REINIT_OPTION = '_need_reinit_custom_style';
	
	/**
	 * get get_option(SHORTNAME . "_preview") value
	 * @var type 
	 */
	private $preview_option = false;
	
	/**
	 * Is writable style file
	 * @var type 
	 */
	private $is_writable = false;
	
	/**
	 * Special list of colorchoosers id<br/>
	 * for converting it values to rgb
	 * @var array
	 */
	private $colorchooser_list = array();
	
	
	function __construct()
	{
		$this->checkPreviewOption();
		$this->checkWritabilityOption();
		if (get_option(SHORTNAME."_preview")){
			add_action('wp_head', array($this, 'sessionJSPreview'));
		}
	}
	
	/**
	 * wp_enqueue_style css OR php file with header
	 */
	public function run()
	{
		if($this->isCustomizeStyle())
		{
			/*
			 * Dinamic styles
			 */
			add_action( 'wp_head', array($this, 'wp_customize_head' ));
		}
		elseif($this->isSessionPreview())
		{
			$uri = get_template_directory_uri() . '/css/style.php';
			wp_enqueue_style('th-custom-style', $uri,array(),false, 'all');
		}
		elseif($this->isWritableOption() && $this->isCustomStylesheetExist())
		{
			$uri = get_template_directory_uri() .'/css/'.self::CUSTOM_STYLESHEET_FILE;
			wp_enqueue_style('th-custom-style', $uri,array(),false, 'all');
		}
		else
		{
			$uri = get_template_directory_uri() . '/css/style.php';
			wp_enqueue_style('th-custom-style', $uri,array(),false, 'all');
		}
	}
	
	public function wp_customize_head()
	{
		?><style><?php echo $this->getStyle();?></style><?php
	}


	/**
	 * Public method for call rewrite file
	 */
	public function reinit()
	{
		$rewrited = $this->rewriteStylesheetFile();
		$this->setWritableOption($rewrited);
		$this->deleteReinitFlag();
	}
	
	/**
	 * Check is directory for custom style file is writable
	 * @return boolean
	 */
	private function isWritableDir()
	{
		if( file_exists($this->getCustomStyleDir()) )
		{
			if(is_writable($this->getCustomStyleDir()))
			{
				return true;
			}
		}
		return false;
	}
	
	/**
	 * is DB writable option 
	 * @return type
	 */
	private function isWritableOption()
	{
		return $this->is_writable;
	}

	/**
	 * Set result of writing custom styleeshet file
	 * @param bool $is_writable 
	 */
	private function setWritableOption($is_writable)
	{
		update_option($this->getWritableOptionName(), $is_writable);
	}
	
	/**
	 * Action on switch theme.
	 */
	public function themeSetup()
	{
		if($this->isWritableDir())
		{
			if(!$this->isWritableOption()) 
			{
				$rewrited = $this->rewriteStylesheetFile();
				$this->setWritableOption($rewrited);
			}
		}
	}
	
	/**
	 * Rewrite stylesheet file
	 */
	private function rewriteStylesheetFile()
	{
		if($this->isWritableDir())
		{
			$handle = fopen($this->getCustomStylesheetPath() , "w+");
			if($handle)
			{
				$style = $this->getStyle();
				fwrite($handle,$style ,strlen($style));
				fclose($handle);
				chmod($this->getCustomStylesheetPath(), 0644);
				return true;
			}
		}
		return false;
	}
	
	/**
	 * get header('Content-type: text/css') ....
	 */
	public function getHeaderStyle()
	{
		header('Content-type: text/css');
		echo $this->getStyle();
	}
	
	/**
	 * Get template style file content.
	 * @return string
	 */
	private function getTemplateStyle()
	{
		if(file_exists($this->getTemplateStyleFilePath()))
		{
			if(is_readable($this->getTemplateStyleFilePath()))
			{
				return file_get_contents($this->getTemplateStyleFilePath());
			}
		}
		return '';
	}
	
	/**
	 * Get full css content from template file and from custom option
	 * @return string
	 */
	private function getStyle()
	{
		// These variables are involved in the replacement
		if(isset( $_POST['customized']))
		{
			$customazie_values = json_decode( stripslashes( $_POST['customized'] ), true );
		}
		foreach ($this->getMacrosesList() as $macros)
		{
			if(is_string($macros) && strlen($macros))
			{
				if($this->isCustomizeStyle())
				{
						if($customazie_values && is_array($customazie_values))
						{
							if(isset($customazie_values[$macros]))
							{
								$$macros = $customazie_values[$macros];
							}
							else
							{
								$$macros = $this->getMacrosValue($macros);
							}
						}
						else
						{
							$$macros = $this->getMacrosValue($macros);
						}
				}
				elseif($this->isSessionPreview())
				{
					if(isset($_SESSION[$macros]))
					{
						$$macros = $_SESSION[$macros];
					}
					else
					{
						$$macros = $this->getMacrosValue($macros);
					}
				}
				else
				{
					$$macros = $this->getMacrosValue($macros);
				}
				
				if($this->isColorchooser($macros))
				{
					$macros_rbg = $macros . '_rgb';
					$$macros_rbg = $this->hex2rgb($$macros);
				}
			}
		}
		$content = preg_replace('/\$([\w]+)/e', '$0', $this->getTemplateStyle());
		$custom_css = $this->getCustomCSS();
		return $content.$custom_css;
	}
	
	/**
	 * Get custom color value
	 * @return string
	 */
	private function getCustomColor()
	{
		if($this->isPreviewOption())
		{
			if(isset($_SESSION['customcolor']))
			{
				return $_SESSION['customcolor'];
			}
			else
			{
				return self::DEFAULT_COLOR;
			}
		}
		else
		{
			$customcolor =  get_option(SHORTNAME . "_customcolor");
			if($customcolor)
			{
				return $customcolor;
			}
			else
			{
				return self::DEFAULT_COLOR;
			}
		}
	}
	
	/**
	 * Get customcolor_light
	 * @param string $default  -default color
	 * @return string
	 */
	private function getCustomColorLight($default)
	{
		if($this->isPreviewOption())
		{
			if(isset($_SESSION['customcolor_light']))
			{
				return $_SESSION['customcolor_light'];
			}
			else
			{
				return $default;
			}
		}
		else
		{
			$customcolor_light =  get_option(SHORTNAME . "_customcolor_light");
			if($customcolor_light)
			{
				return $customcolor_light;
			}
			else
			{
				return $default;
			}
		}
	}
	
	/**
	 * Get customcolor_dark
	 * @param string $default - default color.
	 * @return string
	 */
	private function getCustomColorDark($default)
	{
		if($this->isPreviewOption())
		{
			if(isset($_SESSION['customcolor_dark']))
			{
				return $_SESSION['customcolor_dark'];
			}
			else
			{
				return $default;
			}
		}
		else
		{
			$customcolor_dark =  get_option(SHORTNAME . "_customcolor_dark");
			if($customcolor_dark)
			{
				return $customcolor_dark;
			}
			else
			{
				return $default;
			}
		}
	}
	
	/**
	 * Get font style from DB or default
	 * @return type
	 */
	private function getFontStyle()
	{
		$fontstyle = get_option(SHORTNAME . "_fontstyle");
		if($fontstyle != '')
		{
			return $fontstyle;
		}
		else
		{
			return self::DEFAULT_FONT_STYLE;
		}
	}
	
	/**
	 * Get font from DB or default
	 * @return type
	 */
	private function getFont()
	{
		if($this->isPreviewOption())
		{
			if(isset($_SESSION['customfont']))
			{
				return $_SESSION['customfont'];
			}
			else
			{
				return self::DEFAULT_FONT;
			}
		}
		else
		{
			$font =  get_option(SHORTNAME . "_gfont");
			if($font)
			{
				return $font;
			}
			else
			{
				return self::DEFAULT_FONT;
			}
		}
	}
	
	/**
	 * Get writability option from DB and set value to $this->is_writable
	 */
	private function checkWritabilityOption()
	{
		$value = get_option($this->getWritableOptionName()) != '';
		$this->setWritability($value);
	}
	
	private function setWritability($value)
	{
		$this->is_writable = $value;
	}
	
	/**
	 * Check _preview option and set it to preview_option
	 */
	private function checkPreviewOption()
	{
		$value = get_option($this->getPreviewOptionName()) != '';
		$this->setPreviewOption($value);
	}
	
	private function setPreviewOption($value)
	{
		$this->preview_option = $value;
	}
	
	private function isPreviewOption()
	{
		return $this->preview_option;
	}
	
	private function getPreviewOptionName()
	{
		return SHORTNAME . "_preview";
	}
	
	private function getWritableOptionName()
	{
		return SHORTNAME . '_is_writable_style_file';
	}
	
	/**
	 * Path to Template stylesheet file with $values
	 * @return 
	 */
	private function getTemplateStyleFilePath()
	{
		if(isset($_GET['styles.css']))
		{
			if(is_file($_GET['styles.css']))
			{
				if(pathinfo($filename, PATHINFO_EXTENSION) == 'css')
				{
					return $_GET['styles.css'];
				}
			}
		}
		
		return get_template_directory().DIRECTORY_SEPARATOR.'css'.DIRECTORY_SEPARATOR.self::TEMPLATE_FILE;
	}
	
	/**
	 * Path to dir with custom stylesheet file
	 * @return string
	 */
	private function getCustomStyleDir()
	{
		
		return get_template_directory().DIRECTORY_SEPARATOR.'css';
	}
	
	/**
	 * Path to custom stylesheet file
	 * @return string
	 */
	private function getCustomStylesheetPath()
	{
		$path = $this->getCustomStyleDir() . DIRECTORY_SEPARATOR . self::CUSTOM_STYLESHEET_FILE;
		return $path;
	}
	
	/**
	 * Check is custom stylessheet file exist
	 * @return bool
	 */
	private function isCustomStylesheetExist()
	{
		$path = $this->getCustomStylesheetPath();
		return file_exists($path) && is_readable($path);
	}
	
	/**
	 * Custom style value from DB
	 * @return string
	 */
	private function getCustomCSS()
	{
		$custom = get_option(SHORTNAME . "_customcss");
		if($custom)
		{
			return $custom;
		}
		return '';
	}
	
	/**
	 * Macroses list consist of ID(option name) of admin theme settings page
	 * @example array('ch_customcolor', 'ch_customcolor_light', 'ch_customcolor_dark', 'ch_gfont')
	 * @return array
	 */
	function getMacrosesList()
	{
		global $admin_menu;
		
		$list = array();
		
		if($admin_menu && $admin_menu instanceof Admin_Theme_Menu)
		{
			foreach($admin_menu->getMenuPageList() as $adminPage)
			{
				if($adminPage && $adminPage instanceof Admin_Theme_Menu_Item)
				{
					foreach($adminPage->getOptionsList() as $option)
					{
						if($option && $option instanceof Admin_Theme_Menu_Element)
						{
							$id = $option->getId();
							// some have id false, i.e. 'separator', 'pagetitle'...
							if($id)
							{
								$list[] = $id;
								
								/**
								* Create list of colorchooser for converting it values to RGB
								*/
								if($option instanceof Admin_Theme_Element_Colorchooser)
								{
									$this->addColorchooser($id);
								}
							}
						}
					}
				}
			}
		}
		return $list;
	}
	
	/**
	 * Get macros value 
	 * @param string $macros
	 * @return string
	 */
	function getMacrosValue($macros)
	{
		if($this->isPreviewOption())
		{
			if(isset($_SESSION[$macros]))
			{
				return $_SESSION[$macros];
			}
		}
		
		$value =  get_option($macros);
		
		if($value !== false)
		{
			return $value;
		}
		
		return '';
	}
	
	
	/**
	 * Add $id to colorchooser list.
	 * @param string $id
	 */
	private function addColorchooser($id)
	{
		$this->colorchooser_list[] = $id;
	}
	
	/**
	 * Get list of colorchooser elements id
	 * @return array
	 */
	private function getColorchooserList()
	{
		return $this->colorchooser_list;
	}
	
	/**
	 * Check is this macros id in colorchooser list
	 * @param string $id
	 * @return boolean
	 */
	private function isColorchooser($id)
	{
		return in_array($id, $this->getColorchooserList());
	}
	
	/**
	 * Convert HEX color value to RGB
	 * @param string $hex - HEX color code ie. #A80000 
	 * @return string - rgb(168,0,0)
	 */
	private function hex2rgb($hex)
	{
		$hex = str_replace("#", "", $hex);

		if (strlen($hex) == 3)
		{
			$r = hexdec(substr($hex, 0, 1) . substr($hex, 0, 1));
			$g = hexdec(substr($hex, 1, 1) . substr($hex, 1, 1));
			$b = hexdec(substr($hex, 2, 1) . substr($hex, 2, 1));
		}
		else
		{
			$r = hexdec(substr($hex, 0, 2));
			$g = hexdec(substr($hex, 2, 2));
			$b = hexdec(substr($hex, 4, 2));
		}
		$rgb = array($r, $g, $b);
		
		return implode(",", $rgb);
	}
	
	/**
	 * 
	 * @return type
	 */
	public static function needReinit()
	{
		
		return get_option(SHORTNAME.Custom_CSS_Style::NEED_REINIT_OPTION)=='1';
	}
	
	private function deleteReinitFlag()
	{
		delete_option(SHORTNAME.Custom_CSS_Style::NEED_REINIT_OPTION);
	}
	
	public static function setNeedReinitFlag()
	{
		update_option(SHORTNAME.Custom_CSS_Style::NEED_REINIT_OPTION, 1);
	}
	
	/**
	 * is now theme customize menu
	 * @return type
	 */
	private function isCustomizeStyle()
	{
		return isset( $_POST['customized'] ) && isset($_POST['wp_customize']) && !is_admin();
	}
	
	private function isSessionPreview()
	{
		return get_option(SHORTNAME."_preview")  && session_id();
	}
	
	
	public function sessionJSPreview()
	{
		
		global $admin_menu;
		
		$list = array();
		
		if($admin_menu && $admin_menu instanceof Admin_Theme_Menu)
		{
			echo "<script>\n";
			foreach($admin_menu->getMenuPageList() as $adminPage)
			{
				if($adminPage && $adminPage instanceof Admin_Theme_Menu_Item)
				{
					foreach($adminPage->getOptionsList() as $option)
					{
						if($option && ($option instanceof Admin_Theme_Element_Colorchooser || $option->getId() == SHORTNAME.'_gfont'))
						{
							$value = (isset($_SESSION[$option->getId()]))? $_SESSION[$option->getId()] : get_option($option->getId());
							echo "\tvar {$option->getId()} = '{$value}';\n";
						}
					}
				}
			}
			echo "\tvar google_font_list = '".Admin_Theme_Item_General::FONT_LIST."';\n";
			echo "</script>\n";
		}
	}
}
?>