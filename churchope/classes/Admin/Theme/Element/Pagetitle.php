<?php

/**
 * Class for Pagetitle Html element
 * @todo $discussionurl,  $faqurl;
 */
class Admin_Theme_Element_Pagetitle extends Admin_Theme_Menu_Element
{

	protected $option = array(
		'type' => Admin_Theme_Menu_Element::TYPE_PAGETITLE,
	);

	public function render()
	{
		ob_start();
		?>
		<a name="thtop" id="th_top"></a>
		<h2><?php echo $this->name; ?></h2>     

		<div class="th_admin_wrap">
			<ul>
				<?php
				$html = ob_get_clean();
				return $html;
			}

		}
		?>
