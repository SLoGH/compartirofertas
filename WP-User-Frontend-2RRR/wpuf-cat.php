<?php

/**
 * Category functions
 *
 * @author Andrew Bruin (professor99) 
 * @package WP User Frontend
 * @version 1.1-fork-2RRR-4.4
 * @since 1.1-fork-2RRR-4.4
 */

/*
== Changelog ==

= 1.1-fork-2RRR-4.4 professor99 =
* Included functions originally in wpuf-ajax.php and wpuf-functions.php
* Also replaces category list code in wpuf-add-post.php and wpuf-edit-post.php
* Lots of bug fixes
* CSS classes and ids use wpuf prefix
*/

/*
 * Category class
 *
 * @author Andrew Bruin (professor99)
 * @package WP User Frontend
 * @subpackage WPUF_Cat
 * @since 1.1-fork-2RRR-4.4
 */

class WPUF_Cat {

	function __construct() {
		add_action( 'wp_ajax_nopriv_wpuf_get_child_cats', array($this, 'get_child_cats') );
		add_action( 'wp_ajax_wpuf_get_child_cats', array($this, 'get_child_cats') );
	}

	/**
	 * @author Tareq Hasan
	 *
	 * @return string Child category dropdown on ajax request
	 */
	function get_child_cats() {
		$parentCat = $_POST['catID'];
		$exclude = wpuf_get_option( 'exclude_cats' );
		$result = '';
		
		if ( $parentCat > 0 && get_categories( 'taxonomy=category&child_of=' . $parentCat . '&hide_empty=0' ) ) {
			$options = 'show_option_none=' . __( '-- Select --', 'wpuf');
			$options .= '&id=wpuf-cat-ajax&class=wpuf-cat-dropdown&name=category[]&orderby=name';
			$options .= '&hierarchical=1&depth=1&hide_empty=0&echo=0';
			$options .= '&exclude=' . $exclude;
			$options .= '&child_of=' . $parentCat;

			$result = wp_dropdown_categories( $options );
		}

		die( $result );
	}

	/**
	 * Create ajax dropdown list for given category with exclusions
	 *
	 * @author Andrew Bruin (professor99)
	 * @since 1.1-fork-2RRR-4.4
	 *	
	 * @param int $selected Selected category
	 * @return array $exclude Excluded categories
	 */
	function ajax_dropdown( $selected, $exclude ) {
		$options = 'show_option_none=' . __( '-- Select --', 'wpuf');
		$options .= '&id=wpuf-cat-ajax&name=category[]&orderby=name';
		$options .= '&hierarchical=1&depth=1&hide_empty=0';
		$options .= '&exclude=' . $exclude;

		if ( $selected ) {
			$ancestors = $this->get_ancestors( $selected );
			$descendents = array_reverse( $ancestors );
			$parent = null;
			$level = 0;

			foreach ( $descendents as $child ) {
				$options1 = $options . '&selected=' . $child;

				if ( $child == $selected && !$this->has_descendents( $child ) )
					echo '<div id="wpuf-cat-lvl' . $level . '" level="' . $level . '">';
				else
					echo '<div id="wpuf-cat-lvl' . $level . '" level="' . $level . '" class="wpuf-cat-has-child">';

				if ( $parent ) {
					$options1 .= '&class=wpuf-cat-dropdown&child_of=' . $parent;
						wp_dropdown_categories( $options1 );
				} else {
					$options1 .= '&class=wpuf-cat-top requiredField';
						wp_dropdown_categories( $options1 );
				}

				echo '</div>';

				$parent = $child;
				$level++;
			}
			
			if ( $this->has_descendents( $selected ) ) {
				echo '<div id="wpuf-cat-lvl' . $level . '" level="' . $level . '">';
				$options1 = $options . '&class=wpuf-cat-dropdown&child_of=' . $parent;
				wp_dropdown_categories( $options1 );
				echo '</div>';
			}
		} else {
			echo '<div id="lvl0" level="0">';
				$options .= '&class=wpuf-cat-top requiredField';
				wp_dropdown_categories( $options );
			echo '</div>';
		}
	}

	/**
	 * Get ancestors of given category
	 *
	 * @author Andrew Bruin
	 * @since 1.1-fork-2RRR-4.4
	 *	
	 * @param int $cat Category ID
	 * @return array Category IDs
	 */
	function get_ancestors( $cat ) {
		$ancestors = array();
		$ancestors[] = $cat;
		$child = $cat;

		while ( true ) {
			$cat_info = get_category( $child );
			$parent = $cat_info->parent;

			if ( ! $parent )
				break;

			$ancestors[] = $parent;
			$child = $parent;	
		}

		return $ancestors;
	}

	/**
	 * Checks if given category has descendents
	 *
	 * @author Andrew Bruin
	 * @since 1.1-fork-2RRR-4.4
	 * @global wpdb $wpdb
	 *	
	 * @param int $cat Category ID
	 * @return int Number of descendents
	 */
	function has_descendents( $cat ) {
		global $wpdb;
		
		return $wpdb->query( "SELECT * FROM $wpdb->term_taxonomy WHERE parent=$cat" );
	}
	

	/**
	 * Displays checklist of a taxonomy
	 *
	 * @author Tareq Hasan
	 * @since 0.8
	 *	
	 * @param int $post_id Post ID
	 * @param array $selected_cats Selected category IDs
	 * @param string $tax Taxonomy name
	 */
	function checklist( $post_id = 0, $selected_cats = false, $tax = 'category' ) {
		require_once ABSPATH . '/wp-admin/includes/template.php';

		$walker = new WPUF_Walker_Category_Checklist();

		echo '<ul class="wpuf-category-checklist">';

		wp_terms_checklist( $post_id, array(
			'taxonomy' => $tax,
			'descendants_and_self' => 0,
			'selected_cats' => $selected_cats,
			'popular_cats' => false,
			'walker' => $walker,
			'checked_ontop' => false
		) );

		echo '</ul>';
	}

	/**
	 * Creates category select list
	 *
	 * @author Andrew Bruin
	 * @since 1.1-fork-2RRR-4.4
	 *	
	 * @param int $post_id Post ID
	 */
	function select( $post_id = 0 ) {
		$exclude = wpuf_get_option( 'exclude_cats' );
		$cat_type = wpuf_get_option( 'cat_type' );
		
		if ( $post_id )
			$cats = get_the_category( $post_id );
		else
			$cats = false;
?>
		<div id="wpuf-category" style="float:left;">
			<?php
			if ( $cat_type == 'normal' ) {
			?>
				<div id="wpuf-cat-lvl0">
				<?php
					
					if ( $cats )
						$selected = $cats[0]->term_id;
					else
						$selected = '';

					wp_dropdown_categories( 'show_option_none=' . __( '-- Select --', 'wpuf' ) . '&hierarchical=1&hide_empty=0&orderby=name&name=category[]&show_count=0&title_li=&use_desc_for_title=1&class=wpuf-cat requiredField&exclude=' . $exclude . '&selected=' . $selected );
				?>
				</div>
			<?php
			} else if ( $cat_type == 'ajax' ) {
				
				if ( $cats )
					$selected = $cats[0]->term_id;
				else
					$selected = '';
					
				$this->ajax_dropdown( $selected, $exclude );
			} else {
			?>
				<div id="wpuf-cat-lvl0">
				<?php
					$this->checklist( $post_id );
				?>
				</div>
			<?php
			}
			?>
		</div>
<?php
	}	

	/**
	 * Validates categories
	 *
	 * @author Andrew Bruin
	 * @since 1.1-fork-2RRR-4.4
	 *	
	 * @param array $cats Category IDs
	 * @param array &$errors Reference to error string array
	 */
	function validate( $cats, &$errors ) {
		if ( wpuf_get_option( 'allow_cats' ) == 'on' ) {
			$cat_type = wpuf_get_option( 'cat_type' );

			if ( !isset( $cats ) || $cats[0] == '-1' ) {
				$errors[] = __( 'Please choose a category', 'wpuf' );
			} else {
				if ( $cat_type == 'ajax' ) {
					$post_category = array();
				
					//strip parents
					$category = end( $cats );

					if ( $category == -1 ) {
						$category = prev( $cats );
					}

					//If option cat_ajax_childless set only allow categories that don't have children
					if ( wpuf_get_option( 'cat_ajax_childless' ) == 'on' && $this->has_descendents( $category ) ) {
						$errors[] = __( 'Selected category must be childless', 'wpuf' );
					} else {
						$post_category[] = $category;
					}
				}
				else {
					$post_category = $cats;
				}
			}
		} else {
			//Set category to default if users aren't allowed to choose category
			$post_category = array( get_option( 'wpuf_default_cat' ) );
		}

		return $post_category;
	}

}

$wpuf_cat = new WPUF_cat();

/**
 * Category checklist walker
 *
 * @author Tareq Hasan
 * @since 0.8
 */
class WPUF_Walker_Category_Checklist extends Walker {

	var $tree_type = 'category';
	var $db_fields = array('parent' => 'parent', 'id' => 'term_id'); //TODO: decouple this

	function start_lvl( &$output, $depth, $args ) {
		$indent = str_repeat( "\t", $depth );
		$output .= "$indent<ul class='wpuf-children'>\n";
	}

	function end_lvl( &$output, $depth, $args ) {
		$indent = str_repeat( "\t", $depth );
		$output .= "$indent</ul>\n";
	}

	function start_el( &$output, $category, $depth, $args ) {
		extract( $args );
		if ( empty( $taxonomy ) )
			$taxonomy = 'category';

		if ( $taxonomy == 'category' )
			$name = 'category';
		else
			$name = 'tax_input[' . $taxonomy . ']';

		$class = in_array( $category->term_id, $popular_cats ) ? ' class="wpuf-popular-category"' : '';
		$output .= "\n<li id='wpuf-{$taxonomy}-{$category->term_id}'$class>" . '<label class="wpuf-selectit"><input value="' . $category->term_id . '" type="checkbox" name="' . $name . '[]" id="in-' . $taxonomy . '-' . $category->term_id . '"' . checked( in_array( $category->term_id, $selected_cats ), true, false ) . disabled( empty( $args['disabled'] ), false, false ) . ' /> ' . esc_html( apply_filters( 'the_category', $category->name ) ) . '</label>';
	}

	function end_el( &$output, $category, $depth, $args ) {
		$output .= "</li>\n";
	}

}
