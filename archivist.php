<?php



/*
Plugin Name: Archivist
Plugin URI: https://github.com/benhuson/archivist
Description: Hello, I'm an archivist. I'll look after all your old stuff and file it sensibly where people can find it. Not really, I'll just give you a few extra options for the archive widget.
Version: 1.1
Requires at least: 4.3
Requires PHP: 5.6
Author: Ben Huson
Author URI: https://github.com/benhuson
License: GPL v2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: archivist
Domain Path: /languages
*/



/*
Copyright 2010 Ben Huson (http://www.benhuson.co.uk)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as 
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/



class WP_Widget_Archivist extends WP_Widget {

	public function __construct() {

		$widget_ops = array(
			'classname'   => 'widget_archive',
			'description' => __( 'An archive of your site&#8217;s posts')
		);

		parent::__construct( 'archivist_archives', 'Archivist', $widget_ops );

	}

	public function widget( $args, $instance ) {
		
		extract( $args );
		
		$args = wp_parse_args( (array) $args, array( 
			'title'    => '',
			'count'    => 0,
			'dropdown' => '',
			'limit'    => 0,
			'type'     => 'monthly'
		) );
		
		$c = $instance['count'] ? '1' : '0';
		$d = $instance['dropdown'] ? '1' : '0';
		$l = $instance['limit'];
		$t = $instance['type'];
		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? __( 'Archives' ) : $instance['title'], $instance, $this->id_base );

		echo $before_widget;
		if ( $title )
			echo $before_title . $title . $after_title;

		$archive_args = array( 'type' => $t, 'show_post_count' => $c );
		if ( $l > 0 )
			$archive_args['limit'] = $l;
		
		if ( $d ) {
			$archive_args['format'] = 'option';
			?>
			<select name="archive-dropdown" onchange='document.location.href=this.options[this.selectedIndex].value;'> <option value=""><?php echo esc_attr( __( 'Select Month' ) ); ?></option> <?php wp_get_archives( apply_filters( 'widget_archives_dropdown_args', $archive_args ) ); ?> </select>
			<?php
		} else {
			?>
			<ul>
				<?php wp_get_archives( apply_filters( 'widget_archives_args', $archive_args ) ); ?>
			</ul>
			<?php
		}

		echo $after_widget;
		
	}
	
	public function update( $new_instance, $old_instance ) {
	
		$instance = $old_instance;
		
		$new_instance = wp_parse_args( (array) $new_instance, array( 
			'title' => '',
			'count' => 0,
			'dropdown' => '',
			'limit' => 0,
			'type' => 'monthly'
		) );
		
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['count'] = $new_instance['count'] ? 1 : 0;
		$instance['limit'] = $new_instance['limit'];
		$instance['type'] = $new_instance['type'];
		$instance['dropdown'] = $new_instance['dropdown'] ? 1 : 0;

		return $instance;
		
	}

	public function form( $instance ) {
	
		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'count' => 0, 'dropdown' => '', 'limit' => 0 ) );
		
		$type = array(
			'yearly'     => '',
			'monthly'    => '',
			'weekly'     => '',
			'daily'      => '',
			'postbypost' => ''
		);
		
		$title = strip_tags($instance['title']);
		$limit = $instance['limit'] > 0 ? $instance['limit'] : '';
		$type[$instance['type']] = 'selected="selected"';
		$count = $instance['count'] ? 'checked="checked"' : '';
		$dropdown = $instance['dropdown'] ? 'checked="checked"' : '';
		
		?>
		<p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title' ); ?>:</label> <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" /></p>
		<p><label for="<?php echo $this->get_field_id( 'limit' ); ?>"><?php _e( 'Limit' ); ?>:</label> <input class="widefat" id="<?php echo $this->get_field_id( 'limit' ); ?>" name="<?php echo $this->get_field_name( 'limit' ); ?>" type="text" value="<?php echo esc_attr( $limit ); ?>" /></p>
		<p><label for="<?php echo $this->get_field_id( 'type' ); ?>"><?php _e( 'Type' ); ?>:</label>
			<select class="widefat" id="<?php echo $this->get_field_id( 'type' ); ?>" name="<?php echo $this->get_field_name( 'type' ); ?>">
				<option value="yearly" <?php echo $type['yearly']; ?>>Yearly</option>
				<option value="monthly" <?php echo $type['monthly']; ?>>Monthly</option>
				<option value="weekly" <?php echo $type['weekly']; ?>>Weekly</option>
				<option value="daily" <?php echo $type['daily']; ?>>Daily</option>
				<option value="postbypost" <?php echo $type['postbypost']; ?>>Post by Post</option>
			</select></p>
		<p>
			<input class="checkbox" type="checkbox" <?php echo $count; ?> id="<?php echo $this->get_field_id( 'count' ); ?>" name="<?php echo $this->get_field_name('count'); ?>" /> <label for="<?php echo $this->get_field_id('count'); ?>"><?php _e('Show post counts'); ?></label>
			<br />
			<input class="checkbox" type="checkbox" <?php echo $dropdown; ?> id="<?php echo $this->get_field_id( 'dropdown' ); ?>" name="<?php echo $this->get_field_name( 'dropdown' ); ?>" /> <label for="<?php echo $this->get_field_id( 'dropdown' ); ?>"><?php _e( 'Display as a drop down' ); ?></label>
		</p>
		<?php
		
	}
	
}

function archivist_load_widget() {

	register_widget( 'WP_Widget_Archivist' );

}

add_action( 'widgets_init', 'archivist_load_widget' );
