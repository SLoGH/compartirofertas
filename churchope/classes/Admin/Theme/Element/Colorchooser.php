<?php
/**
 * Class for Colorchooser Html element
 */
class Admin_Theme_Element_Colorchooser extends Admin_Theme_Menu_Element
{
	
	protected $is_customized = Admin_Theme_Menu_Element::CUSTOMIZED; // Colorchooser element is customized by default
	
	protected $option = array(
							'type' => Admin_Theme_Menu_Element::TYPE_COLORCHOOSER,
						);
	
	public function render()
	{
		$checked = "";
		ob_start();
			echo $this->getElementHeader();
			?>
				<input style="width:100px;clear:both" name="<?php echo $this->id; ?>" id="<?php echo $this->id; ?>" type="color" data-hex="true" value="<?php if ( get_option( $this->id ) != "") { echo get_option( $this->id ); } else { echo $this->std; } ?>" />
			<?php
			echo $this->getElementFooter();
		$html = ob_get_clean();
		return $html;
	}
	
	
	public function add_customize_control($wp_customize)
	{
		$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, $this->getId(), array(
			'label'    => __( $this->getName()),
			'section'  => $this->getCustomizeSection(),
			'settings' => $this->getId(),
		) ) );

	}
}
?>
