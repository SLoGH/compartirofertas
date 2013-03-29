<?php
/**
 * Class for File Html element
 */
class Admin_Theme_Element_File extends Admin_Theme_Menu_Element
{
	
	protected $is_customized = Admin_Theme_Menu_Element::CUSTOMIZED; // file element is customized by default
	
	protected $option = array(
							'type' => Admin_Theme_Menu_Element::TYPE_FILE,
						);
	
	public function render()
	{
		ob_start();
		echo $this->getElementHeader();
			if (get_option($this->id) != "")
		{
			$meta_image = get_option($this->id);
			?>

			<img src="<?php echo $meta_image ?>" alt=""   id="image_file_rm_<?php echo $this->id; ?>" class="th_img" title="<img src='<?php echo get_option($this->id) ?>' alt='' />"  />

			<div id="file_deleted_file_rm_<?php echo $this->id; ?>">
					<div style="float:left;margin:0 0 20px 0"><input type="button" name="file_rm_<?php echo $this->id; ?>" id="file_rm_<?php echo $this->id; ?>" class="button" value="Delete" /></div>
			<div style="margin:3px 0 24px 8px;float:left;"><img class="file_rm_<?php echo $value['id']; ?>" style="display:none;" src="images/wpspin_light.gif" alt="Loading" /></div></div>
			<div class="th_img_frame" id="th_img_frame_file_rm_<?php echo $this->id; ?>" style="display:none"></div>
		<?php 
		}
		else {?>
			<div class="th_img_frame"></div>
		<?php } ?>
			
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
					if(isset($file['url']))
					{
						update_option($keys[$i], $file['url']);
					}
				}
				else
				{
					wp_die('No image was uploaded.');
				}
			}
			$i++;
		}
	}
	
	public function add_customize_control($wp_customize)
	{
		$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, $this->getId(), array(
			'label'    => __( $this->getName()),
			'section'  => $this->getCustomizeSection(),
			'settings' => $this->getId(),
		) ) );
	}
}
?>
