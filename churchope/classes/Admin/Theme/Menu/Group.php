<?php

abstract class Admin_Theme_Menu_Group extends Admin_Theme_Menu_Element
{

	public function setGroupClass($class)
	{
		$this->option['group_class'] = $class;
		return $this;
	}
	
	public function getGroupClass()
	{
		return $this->group_class;
	}
	
	public function setImgClass($class)
	{
		$this->option['img_class'] = $class;
		return $this;
	}
	
	public function getImgClass()
	{
		return $this->img_class;
	}
	
	protected function getElementHeader()
	{
		ob_start();
		?>
		<li>
			<label class="<?php echo $this->getGroupClass() . ' ' . $this->getImgClass()?>">
				<?php echo $this->name; ?>
			</label>
				<a href="#" title="<?php echo $this->desc; ?>" class="th_help">
					<img src="<?php echo get_template_directory_uri() . '/backend/img/help.png'; ?>"  width="15" height="16"  alt="" />
				</a><br/><br/>
				<ul class="toggled">
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
		return '</ul></li>';
	}
	
	/**
	 * Group doesn't save 
	 */
	public function save()
	{
		return;
	}
	
	/**
	 * Group doesn't reset 
	 */
	public function reset()
	{
		return;
	}
	
	
}
?>
