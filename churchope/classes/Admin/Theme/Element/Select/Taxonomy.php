<?php

/**
 * Class for Select Html element 
 */
class Admin_Theme_Element_Select_Taxonomy extends Admin_Theme_Menu_Element
{
	private $taxonomy = array();
	
	protected $option = array(
						'type' => Admin_Theme_Menu_Element::TYPE_SELECT,
					);
	
	public function render()
	{
		ob_start();
		echo $this->getElementHeader();
		$cur = false;
		?>
		<select  name="<?php echo $this->id; ?>" id="<?php echo $this->id; ?>">
				<?php foreach ($this->getTerms() as $term) { ?>
					<option 
						<?php if ( get_option( $this->id ) == $term->slug) 
						{
							echo ' selected="selected"';
							$cur = true; 
						}
						elseif($term->slug == $this->std && !$cur)
						{
							echo ' selected="selected"'; 
							
						}
						?> value="<?php echo $term->slug?>">
							<?php echo $term->name; ?>
					</option>
				<?php } ?>
		</select>
		<?php
		echo $this->getElementFooter();
		 
		 $html = ob_get_clean();
		 return $html;
	}
	
	private function getTerms()
	{
		$terms_list = array();
		foreach($this->getTaxonomies() as $taxonomy )
		{
			foreach( get_terms($taxonomy, 'hide_empty=0' ) as $term )
			{
				$terms_list[] = $term;
			}
		}
		return $terms_list;
	}
	
	public function setTaxonomy($taxonomy)
	{
		if(is_array($taxonomy))
		{
			$this->taxonomy = $taxonomy;
		}
		else
		{
			$this->taxonomy[] = $taxonomy;
		}
		
		return $this;
	}
	
	private function getTaxonomies()
	{
		return $this->taxonomy;
	}
}
?>
