<?php
/**
 * Plugin Name: List Custom Taxonomy Widget
 * Plugin URI: http://celloexpressions.com/dev/list-custom-taxonomy-widget
 * Description: Multi-widget for displaying category listings for custom post types (custom taxonomies).
 * Version: 1.0
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
		$taxonomy = $instance['taxonomy']; // Taxonomy to show
		
        	// Output
		echo $before_widget;

		if ( $title ) echo $before_title . $title . $after_title;

		$tax = $taxonomy;
		$args = array(
				'child_of'                 => 0,
				'parent'                   => '',
				'orderby'                  => 'count',
				'order'                    => 'desc',
				'hide_empty'               => true,
				'hierarchical'             => true,
				'exclude'                  => '',
				'include'                  => '',
				'number'                   => '',
				'taxonomy'                 => $tax,
				'pad_counts'               => false );
		$categories=get_categories($args);
		echo '<ul>';
		foreach($categories as $category) 
		{
			echo '<li><a href="' . get_term_link($category->slug, $tax) . '" title="' . sprintf( __( "View all posts in %s" ), $category->name ) . '" ' . '>' . $category->name.'</a> '; 
			echo '('. $category->count . ')</li>'; 
		}//foreach	
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
		return $instance;
	}
	
	/**
	* Widget settings
	**/
	function form( $instance ) {	
	
		    // instance exist? if not set defaults
		    if ( $instance ) {
				$title  = $instance['title'];
		        	$taxonomy = $instance['taxonomy'];
		    } else {
			    //These are our defaults
				$title  = '';
		        	$taxonomy = 'category';//this will display the category taxonomy, which is used for normal, built-in posts
		    }
			

		// The widget form ?>
			<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php echo __( 'Title:' ); ?></label>
			<input id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" class="widefat" />
			</p>
			<p>
			<label for="<?php echo $this->get_field_id('taxonomy'); ?>"><?php echo __( 'Taxonomy to List:' ); ?></label>
			<input id="<?php echo $this->get_field_id('taxonomy'); ?>" name="<?php echo $this->get_field_name('taxonomy'); ?>" type="text" value="<?php echo $taxonomy; ?>" size="20" />
			</p>
	<?php 
	}

} // class lc_taxonomy

?>