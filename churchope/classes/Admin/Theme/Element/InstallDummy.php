<?php
/**
 * Class for Info Html element
 * @todo No page using this element
 * @uses NO
 */
class Admin_Theme_Element_InstallDummy extends Admin_Theme_Menu_Element
{
//	private $option_name
	
	protected $option = array(
							'type' => Admin_Theme_Menu_Element::TYPE_INSTALL_DUMMY,
						);
	
	public function render()
	{
		ob_start();
		?>
		<p><strong>Warning!</strong><br> DO NOT INSTALL dummy data on your live website. <br>It will corrupt your existing data.
		</p>
		<p>We suggest you install dummy data only on clean WordPress setup.
		</p>
		<p><small>We do not hold any responsibility if you lost existing data.</small></p>
		<br>
		<?php
		if ($this->isDummyInstalled(SHORTNAME.'_dummy_install')): ?>
			<div id="message" class="updated fade" style="width:450px;margin:15px 0 !important"><p><strong>Dummy content already installed.</strong></p></div>
		<?php endif; ?>
		<input name="install_dummy" type="submit" value=" Install dummy content " class="th_save" style="width:auto !important;margin:0 !important" /> 
		<br>
		<br>
		<img class="install_dummy_loading" style="display:none;" src="images/wpspin_light.gif" alt="Loading" />
		<p class="install_dummy_result"></p>
		<?php
		$html = ob_get_clean();
		return $html;
	}
	
	private function isDummyInstalled($name)
	{
		return get_option( $name ) != "";
	}
}
?>
