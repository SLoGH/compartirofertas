<?php


class Admin_Theme_Menu
{
	/**
	 * List of submenu(items)
	 * @var array
	 */
	protected $items = array();
	
	/**
	 * Theme name
	 * @var string
	 */
	protected $theme_name = '';
	
	/**
	 * The on-screen name text for the menu
	 * @var string 
	 */
	protected $adminmenuname = '';
	
	/**
	 * The url to the icon to be used for this menu.
	 * @var string
	 */
	protected $icon_url = '';
	
	/**
	 *  The capability required for this menu to be displayed to the user.<br/>
	 *  User levels are deprecated and should not be used here!
	 * @var string
	 */
	protected $capability = 'administrator';
	
	/**
	 * The slug name to refer to this menu by (should be unique for this menu).
	 * @var string
	 */
	protected $menu_slug = '';
	
	/**
	 * Theme name
	 * @param string $theme_name 
	 */
	function __construct($theme_name = '')
	{
		$this->setThemeName($theme_name);
	}
	
	/**
	 * Init all items(submenu)
	 * Add WP menu-submenu list
	 */
	public function init()
	{
			add_menu_page($this->getThemeName(),
						$this->getAdminMenuName(),
						$this->getCapability(),
						$this->getMenuSlug(),
						array(new Admin_Theme_Item_General(), 'renderPage'),
						$this->getIconUrl());
			
			foreach($this->getMenuPageList($this->getMenuSlug()) as $page)
			{
				$this->addItem($page);
			}
	}
	
	/**
	 * Array of theme settings page in admin.
	 * @param string $menu_slug parent slug
	 * @return type 
	 */
	public function getMenuPageList($menu_slug = '')
	{
		return array(
			new Admin_Theme_Item_General($menu_slug),
			new Admin_Theme_Item_Header($menu_slug),
			new Admin_Theme_Item_Slideshow($menu_slug),
			new Admin_Theme_Item_Blog($menu_slug),
			new Admin_Theme_Item_Sidebar($menu_slug),
			new Admin_Theme_Item_Galleries($menu_slug),
			new Admin_Theme_Item_Events($menu_slug),
			new Admin_Theme_Item_Testimonials($menu_slug),
			new Admin_Theme_Item_Footer($menu_slug),			
			new Admin_Theme_Item_CustomStyles($menu_slug),
			new Admin_Theme_Item_Dummy($menu_slug),					
		);
		
	}
	
	/**
	 * Set to global $wp_admin_bar by link(&) menu list.
	 * @param WP_Admin_Bar $wp_admin_bar 
	 */
	public function setAdminBar(&$wp_admin_bar)
	{
		$wp_admin_bar->add_menu( array(
									'id' =>$this->getMenuSlug(),
									'title'=>__($this->getAdminMenuName(), 'churchope'),
									'href'=>admin_url('admin.php?page=' . $this->getMenuSlug()),
								));
		
		foreach($this->getItemsList() as $item)
		{
			$id = $item->getMenuSlug(). '_sub';
			
			if($item->getMenuSlug() == $this->getMenuSlug())
			{
				$id .= '_sub';
			}
			
			$wp_admin_bar->add_menu( array(
										'id' =>$id,
										'parent'=>$this->getMenuSlug(),
										'title'=>__($item->getMenuTitle(), 'churchope'),
										'href'=>admin_url('admin.php?page=' . $item->getMenuSlug()),
										));
		}
	}
	
	/**
	 * Save to DB values of theme menu pages.
	 */
	public function themeActivation()
	{
		if($this->getMenuPageList())
		{
			foreach ($this->getMenuPageList() as $item)
			{
				if($item instanceof Admin_Theme_Menu_Item)
				{
					$item->saveDefaultValues();
				}
			}
		}
	}
	
	/**
	 *  
	 */
	public function run()
	{
		
		$this->init();
		if($this->isEditThemeSubmenu())
		{
			$submenu = $this->getItemBySlug();

			if($submenu) 
			{
				if($this->isFormSubmit())
				{
					if(check_admin_referer(Admin_Theme_Menu_Item::NONCE_SAVE_ACTION))
					{
						$submenu->saveForm();
					}
					else
					{
						die("Security check");
					}
				}
				elseif($this->isFormReset())
				{
					if(check_admin_referer(Admin_Theme_Menu_Item::NONCE_RESET_ACTION))
					{
						$submenu->resetForm();
					}else
					{
						die("Security check");
					}
				}
			}
		}
	}
	
	/**
	 * Check is current theme submenu is active and edited in admin page.
	 * @return boolean 
	 */
	public function isEditThemeSubmenu()
	{
		$slug = $this->getPage();
		if($slug)
		{
			return in_array($slug, array_keys($this->getItemsList()));
		}
		return false;
	}
	
	/**
	 * Check is form Submit button press
	 * @return bool
	 */
	private function isFormSubmit()
	{
		return isset($_POST['save_options']);
	}
	/**
	 * Check is form reset button press
	 * @return bool
	 */
	private function isFormReset()
	{
		return isset($_POST['reset_options']);
	}
	
	/**
	 *  wp_enqueue_style all theme CSS list
	 */
	public function getCSS()
	{
			wp_enqueue_style('th-admin-css', get_template_directory_uri() .'/backend/css/admin.css','','1.0.0', 'all');
			wp_enqueue_style('th-uniform', get_template_directory_uri() .'/backend/js/uniform/css/uniform.default.css','','1.0.0', 'all');	
			wp_enqueue_style('th-ibutton', get_template_directory_uri() .'/backend/js/switcher/css/jquery.ibutton.min.css','','1.0.0', 'all');
	}
	
	/**
	 *  wp_enqueue_script & redister JavaScript file list
	 */
	public function getJSIncludes()
	{
		wp_enqueue_script('th-colorpicker', get_template_directory_uri() .'/backend/js/mColorPicker/javascripts/mColorPicker.js', array('jquery'), '1.0.0');
		wp_enqueue_script('th-uniform', get_template_directory_uri() .'/backend/js/uniform/jquery.uniform.min.js', array('jquery'), '1.0.0');
		wp_enqueue_script('th-ibutton', get_template_directory_uri() .'/backend/js/switcher/lib/jquery.ibutton.min.js', array('jquery'), '1.0.0');
		wp_enqueue_script('th-qtip', get_template_directory_uri() .'/backend/js/qtip/jquery.qtip.pack.js', array('jquery'), '1.0.0');
		$this->registerAdminScript();
	}
	
	/**
	 * do wp_register_script('th-admin' .....
	 */
	private function registerAdminScript()
	{
		wp_register_script('th-admin', get_template_directory_uri() .'/backend/js/admin.js', array('jquery'), '1.0.0');
	}
	
	/**
	 * do  wp_print_scripts('th-admin)
	 */
	public function printAdminScript()
	{
		wp_print_scripts( 'th-admin' );
	}
	
	/**
	 * Return javascript code.<br/>
	 * with initialized variables  in script<br/>
	 * <b>$AjaxUrl & $ajaxNonce</b>
	 * @return string 
	 */
	public function getJSCode()
	{
		$nonce = wp_create_nonce( 'th_nonce' );
		$script = '<script type="text/javascript" charset="utf-8">
						jQuery(function(){
							jQuery("input:text, input:file,  textarea, select, button").uniform();
						});
							var $AjaxUrl = "' .admin_url('admin-ajax.php'). '";
							var $ajaxNonce = "' .$nonce. '";		
					</script>';
		
		return $script;
	}
	
	public function removeFile()
	{
		$file_id = isset($_POST['file_id'])? $_POST['file_id'] : false;
		if($file_id)
		{
			$file_id = substr($file_id, 8);   // ugly hardcode
			delete_option( $file_id );
		}
	}
	/**
	 * 
	 * @param object $wpdb 
	 */
	public function removeSidebar($wpdb)
	{
		$options_sidebar_rm = array();

		$sidebar		= $_POST['sidebar'];
		$sidebar_id		= $_POST['sidebar_id'];
		$sidebar_name	= $_POST['sidebar_name'];
//		if($sidebar)
		{
			$pieces	= explode(",", $sidebar);

//			if(is_array($pieces))
			{
				foreach ($pieces as $key => $value)
				{
					if($value != '')
					{
						$options_sidebar_rm[ $value ] = $value;
					}
				}
				
				update_option( SHORTNAME.'_sidebar_generator', $options_sidebar_rm);

				$sidebar_meta = $wpdb->get_results("SELECT post_id FROM $wpdb->postmeta WHERE meta_value = '$sidebar_name'", ARRAY_A);

				if ( is_array($sidebar_meta) )
				{
					foreach ($sidebar_meta as $key => $value)
					{
						delete_post_meta($value['post_id'], 'selected_sidebar');
					}
				}
			}
		}
	}
	
	/**
	 * Add item to Menu items list
	 * @param type $item 
	 */
	public function addItem($item)
	{
		$item->setParentSlug($this->getMenuSlug());
		$item->setThemeName($this->getThemeName());
		$this->items[$item->getMenuSlug()] = $item;
	}
	/**
	 * Return menu items(submenu) list
	 */
	public function getItemsList()
	{
		return $this->items;
	}
	
	/**
	 * Get Submenu by slug
	 * @param string $slug
	 * @return Admin_Theme_Menu_Item||false
	 */
	public function getItemBySlug($slug = '')
	{
		$slug = $this->getPage();
		if($slug)
		{
			if(isset($this->items[$slug]))
			{
				return $this->items[$slug];
			}
		}
		elseif(isset($this->items[$this->getMenuSlug()]))
		{
			return $this->items[$this->getMenuSlug()];
		}
		return false;
	}
	
	/**
	 * Return $_GET['page'] value or false
	 * @return mix 
	 */
	private function getPage()
	{
		return (isset($_GET['page'])) ? $_GET['page'] : false;
	}
	
	
	public function setThemeName($theme_name)
	{
		$this->theme_name = $theme_name;
		return $this;
	}
	
	protected function getThemeName()
	{
		return $this->theme_name;
	}
	
	/**
	 * Set on-screen name text for the menu
	 * @param string $adminmenuname 
	 */
	public function setAdminMenuName($adminmenuname)
	{
		$this->adminmenuname = $adminmenuname;
		return $this;
	}
	
	protected function getAdminMenuName()
	{
		return $this->adminmenuname;
	}
	
	
	/**
	 * Set menu icon url
	 * @param string $icon_url 
	 */
	public function setIconUrl($icon_url)
	{
		$this->icon_url = $icon_url;
		return $this;
	}
	
	protected function getIconUrl()
	{
		return $this->icon_url;
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
	
	public function setCapability($capability)
	{
		$this->capability = $capability;
		return $this;
	}
	
	protected function getCapability()
	{
		return $this->capability;
	}
}
?>