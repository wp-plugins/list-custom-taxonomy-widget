<?php
/**
 * Plugin Name: List Custom Taxonomy Widget
 * Plugin URI: http://celloexpressions.com/dev/list-custom-taxonomy-widget
 * Description: Multi-widget for displaying category listings for custom post types (custom taxonomies).
 * Version: 2.0
 * Author: Nick Halsey
 * Author URI: http://celloexpressions.com/
 * Tags: custom taxonomy, custom tax, widget, sidebar, category, categories, custom category, custom categories, post types, custom post types
 * License: GPL
 
=====================================================================================
Copyright (C) 2012 Nick Halsey

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
=====================================================================================
*/
  
// Register 'List Custom Taxonomy' widget
add_action( 'widgets_init', 'init_lc_taxonomy' );
function init_lc_taxonomy() { return register_widget('lc_taxonomy'); }

class lc_taxonomy extends WP_Widget {
	/** constructor */
	function lc_taxonomy() {
		parent::WP_Widget( 'lc_taxonomy', $name = 'List Custom Taxonomy' );
	}

	/**
	* This is the Widget
	**/
	function widget( $args, $instance ) {
		global $post;
		extract($args);

		// Widget options
		$title 	 = apply_filters('widget_title', $instance['title'] ); // Title		
		$this_taxonomy = $instance['taxonomy']; // Taxonomy to show
		$hierarchical = !empty( $instance['hierarchical'] ) ? '1' : '0';
		$showcount = !empty( $instance['count'] ) ? '1' : '0';
		
        // Output
		echo $before_widget;

		if ( $title ) echo $before_title . $title . $after_title;
		
		$tax = $this_taxonomy;
		$args = array(
				'show_option_all'    => '',
				'orderby'            => 'count',
				'order'              => 'desc',
				'style'              => 'list',
				'show_count'         => $showcount,
				'hide_empty'         => 1,
				'use_desc_for_title' => 1,
				'child_of'           => 0,
				'feed'               => '',
				'feed_type'          => '',
				'feed_image'         => '',
				'exclude'            => '',
				'exclude_tree'       => '',
				'include'            => '',
				'hierarchical'       => $hierarchical,
				'title_li'           => '',
				'show_option_none'   => __('No categories'),
				'number'             => null,
				'echo'               => 1,
				'depth'              => 0,
				'current_category'   => 0,
				'pad_counts'         => 0,
				'taxonomy'           => $tax,
				'walker'             => null
/*				'child_of'                 => 0,
				'parent'                   => '',
				'orderby'                  => 'count',
				'order'                    => 'desc',
				'hide_empty'               => true,
				'hierarchical'             => $hierarchical,
				'exclude'                  => '',
				'include'                  => '',
				'number'                   => '',
				'taxonomy'                 => $tax,
				'pad_counts'               => false */);
		//$categories=get_categories($args);
		echo '<ul>';
		wp_list_categories($args);
		//foreach($categories as $category){
		//	echo '<li><a href="' . get_term_link($category->slug, $tax) . '" title="' . sprintf( __( "View all posts in %s" ), $category->name ) . '" ' . '>' . $category->name.'</a> '; 
		//	if( $showcount ) { echo '('. $category->count . ')'; }
		//	echo '</li>'; 
		//}
		echo '</ul>';
		// echo widget closing tag
		echo $after_widget;
	}
//get_category_link( $category->term_id ) . 
	/** Widget control update */
	function update( $new_instance, $old_instance ) {
		$instance    = $old_instance;
		
		$instance['title']  = strip_tags( $new_instance['title'] );
		$instance['taxonomy'] = strip_tags( $new_instance['taxonomy'] );
		$instance['hierarchical'] = !empty($new_instance['hierarchical']) ? 1 : 0;
        $instance['count'] = !empty($new_instance['count']) ? 1 : 0;

		return $instance;
	}
	
	/**
	* Widget settings
	**/
	function form( $instance ) {	
	
		    // instance exist? if not set defaults
		    if ( $instance ) {
				$title  = $instance['title'];
				$this_taxonomy = $instance['taxonomy'];
                $showcount = isset($instance['count']) ? (bool) $instance['count'] :false;
                $hierarchical = isset( $instance['hierarchical'] ) ? (bool) $instance['hierarchical'] : false;
		    } else {
			    //These are our defaults
				$title  = '';
				$this_taxonomy = 'category';//this will display the category taxonomy, which is used for normal, built-in posts
				$hierarchical = true;
				$showcount = true;
		    }
			
			//Count number of post types for select box sizing
			$args=array(
			  'public'   => true,
			  '_builtin' => true
			); 
			$output = 'names'; // or objects
			$operator = 'and'; // 'and' or 'or'
			$taxonomies=get_taxonomies($args,$output,$operator); 
			foreach ($taxonomies as $tax ) {
			   $taxonomies_ar[] = $tax;
			}

		// The widget form ?>
			<p>
				<label for="<?php echo $this->get_field_id('title'); ?>"><?php echo __( 'Title:' ); ?></label>
				<input id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" class="widefat" />
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('taxonomy'); ?>"><?php echo __( 'Select Taxonomy:' ); ?></label>
				<select name="<?php echo $this->get_field_name('taxonomy'); ?>" id="<?php echo $this->get_field_id('taxonomy'); ?>" class="widefat" style="height: auto;" size="4">
			<?php 
			$args=array(
			  'public'   => true,
			  '_builtin' => false //these are manually added to the array later
			); 
			$output = 'names'; // or objects
			$operator = 'and'; // 'and' or 'or'
			$taxonomies=get_taxonomies($args,$output,$operator); 
			$taxonomies[] = 'category';
			$taxonomies[] = 'post_tag';
			$taxonomies[] = 'post_format';
			foreach ($taxonomies as $taxonomy ) { ?>
				<option value="<?php echo $taxonomy; ?>" <?php if( $taxonomy == $this_taxonomy ) { echo 'selected="selected"'; } ?>><?php echo $taxonomy;?></option>
			<?php }	?>
			</select>
			</p>
			
            <input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('count'); ?>" name="<?php echo $this->get_field_name('count'); ?>"<?php checked( $showcount ); ?> />
            <label for="<?php echo $this->get_field_id('count'); ?>"><?php _e( 'Show post counts' ); ?></label><br />
            <input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('hierarchical'); ?>" name="<?php echo $this->get_field_name('hierarchical'); ?>"<?php checked( $hierarchical ); ?> />
            <label for="<?php echo $this->get_field_id('hierarchical'); ?>"><?php _e( 'Show hierarchy' ); ?></label></p>
			
			
<?php 
	}

} // class lc_taxonomy

?>