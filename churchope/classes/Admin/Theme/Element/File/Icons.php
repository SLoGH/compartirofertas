<?php
/**
 * Class for Custom Icons uploader element with preview
 */
class Admin_Theme_Element_File_Icons extends Admin_Theme_Element_File
{
	protected $is_customized = Admin_Theme_Menu_Element::NOT_CUSTOMIZED; // Icons element is NOT customized by default
	
	protected $option = array(
							'type' => Admin_Theme_Menu_Element::TYPE_FILE,
						);
	
	public function render()
	{
		ob_start();
		echo $this->getElementHeader();
		$icons_list = $this->getIcons();
		
		if($icons_list && count($icons_list)):
			$counter = 1;
			$active_icon = get_option($this->id.'_active', '');
			?>
			<div style="clear:both;margin-bottom: 20px">
				<div style="display: inline;padding-right: 20px">
					<input type="radio" name="<?php echo 'list_'.$this->id; ?>" value="" id="<?php echo $this->id.'_0'; ?>" <?php if (!$active_icon) { echo 'checked="checked"';} ?> />
					<label for="<?php echo $this->id.'_0' ?>" style="display:inline;float:none;font-size: 14px;">
						None
					</label>
				</div>
			<?php foreach($icons_list as $icon): 
				$checked = '';
				if($icon == $active_icon)
				{
					$checked = 'checked="checked"';
				}
				?>
				<div style="display: inline;padding-right: 20px">
					<input type="radio" name="<?php echo 'list_'.$this->id; ?>" value="<?php echo $icon?>" id="<?php echo $this->id.'_'.$counter; ?>"  <?php echo $checked?> />
					<label for="<?php echo $this->id.'_'.$counter; ?>" style="display:inline;float:none">
						<img src="<?php echo $icon ?>" alt="" class="th_img" />
					</label>
				</div>
			<?php 
			$counter++;
			endforeach;?>
			</div>
		<?php else:?>
			<div class="th_img_frame"></div>
		<?php endif ?>
			
			<input  name="<?php echo $this->id; ?>" type="file"  />
		<?php
		echo $this->getElementFooter();
		$html = ob_get_clean();
		return $html;
		
	}
	
	
	public function save()
	{
		$keys = array_keys($_FILES);
		
		$i = 0;
		foreach ($_FILES as $image)
		{
			if ($image['size'])
			{
				if (preg_match('/(jpg|jpeg|png|gif)$/', $image['type']))
				{
					$override = array('test_form' => false);
					$file = wp_handle_upload($image, $override);
					
					$this->addCustomIcon($file['url']);
					
				}
				else
				{
					wp_die('No image was uploaded.');
				}
			}
			$i++;
		}
		if(isset($_REQUEST['list_'.$this->id]))
		{
			$active_icon = $_REQUEST['list_'.$this->id];
			update_option($this->id.'_active', $active_icon);
		}
		
	}
	
	/**
	 * Custom icons
	 * @return array
	 */
	private function getIcons()
	{
		$option = get_option($this->id);
		if($option) 
		{
			if(is_array($option))
			{
				$icon_list = $option;
			}
			else
			{
				$icon_list = unserialize($option);
				
			}
			if(is_array($icon_list))
			{
				return $icon_list;
			}
			else
			{
				return array();
			}
		}
		else
		{
			return array();
		}
		
	}
	
	/**
	 * add icon url to serialized icon list
	 * @param type $icon
	 */
	private function addCustomIcon($icon)
	{
		$icon_list = $this->getIcons();
		$icon_list[] = $icon;
		update_option($this->id, serialize($icon_list));
	}
	
	public function reset()
	{
		update_option($this->id, '');
		update_option(SHORTNAME . Admin_Theme_Item_Galleries::CUSTOM_GALLERY_ICONS.'_active', '');
	}
}
?>
