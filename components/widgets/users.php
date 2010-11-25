<?php
/**
 * Creates a widget that allows users to list users of their site.
 *
 * @package Members
 * @subpackage Components
 */

class Members_Widget_Users extends WP_Widget {

	function Members_Widget_Users() {
		$widget_ops = array( 'classname' => 'users', 'description' => __('An advanced widget that gives you total control over the output of your user lists.','members') );
		$control_ops = array( 'width' => 250, 'height' => 350, 'id_base' => 'members-widget-users' );
		$this->WP_Widget( 'members-widget-users', __('Users', 'members'), $widget_ops, $control_ops );
	}

	function widget( $args, $instance ) {

		extract( $args, EXTR_SKIP );

		$title = apply_filters('widget_title', $instance['title'] );
		$limit = $instance['limit'];
		$order = $instance['order'];
		$orderby = $instance['orderby'];
		$exclude = $instance['exclude'];
		$include = $instance['include'];
		$show_fullname = isset( $instance['show_fullname'] ) ? $instance['show_fullname'] : false;

		$users = array(
			'limit' => $limit,
			'order' => $order,
			'orderby' => $orderby,
			'include' => $include,
			'exclude' => $exclude,
			'show_fullname' => $show_fullname,
			'echo' => 0,
		);

		echo $before_widget;

		if ( $title )
			echo "\n\t\t\t" . $before_title . $title . $after_title;

		echo "\n\t\t\t" . '<ul class="xoxo users">';

		echo "\n\t\t\t\t" . str_replace( array( "\r", "\n", "\t" ), '', members_list_users( $users ) );

		echo "\n\t\t\t" . '</ul><!-- .xoxo .users -->';

		echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['limit'] = strip_tags( $new_instance['limit'] );
		$instance['include'] = strip_tags( $new_instance['include'] );
		$instance['exclude'] = strip_tags( $new_instance['exclude'] );
		$instance['order'] = $new_instance['order'];
		$instance['orderby'] = $new_instance['orderby'];
		$instance['show_fullname'] = $new_instance['show_fullname'];

		return $instance;
	}

	function form( $instance ) {

		//Defaults
		$defaults = array( 'title' => __('Users', 'members'), 'show_fullname' => true, 'order' => 'ASC', 'orderby' => 'display_name' );
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:', 'members'); ?></label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:99%;" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'limit' ); ?>"><?php _e('Limit:', 'members'); ?> <code>limit</code></label>
			<input id="<?php echo $this->get_field_id( 'limit' ); ?>" name="<?php echo $this->get_field_name( 'limit' ); ?>" value="<?php echo $instance['limit']; ?>" style="width:99%;" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'order' ); ?>"><?php _e('Order:', 'widgets-reloaded'); ?> <code>order</code></label> 
			<select id="<?php echo $this->get_field_id( 'order' ); ?>" name="<?php echo $this->get_field_name( 'order' ); ?>" class="widefat" style="width:100%;">
				<option <?php if ( 'ASC' == $instance['order'] ) echo 'selected="selected"'; ?>>ASC</option>
				<option <?php if ( 'DESC' == $instance['order'] ) echo 'selected="selected"'; ?>>DESC</option>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'orderby' ); ?>"><?php _e('Order By:', 'widgets-reloaded'); ?> <code>orderby</code></label> 
			<select id="<?php echo $this->get_field_id( 'orderby' ); ?>" name="<?php echo $this->get_field_name( 'orderby' ); ?>" class="widefat" style="width:100%;">
				<option <?php if ( 'display_name' == $instance['orderby'] ) echo 'selected="selected"'; ?>>display_name</option>
				<option <?php if ( 'ID' == $instance['orderby'] ) echo 'selected="selected"'; ?>>ID</option>
				<option <?php if ( 'user_login' == $instance['orderby'] ) echo 'selected="selected"'; ?>>user_login</option>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'include' ); ?>"><?php _e('Include:', 'members'); ?> <code>include</code></label>
			<input id="<?php echo $this->get_field_id( 'include' ); ?>" name="<?php echo $this->get_field_name( 'include' ); ?>" value="<?php echo $instance['include']; ?>" style="width:99%;" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'exclude' ); ?>"><?php _e('Exclude:', 'members'); ?> <code>exclude</code></label>
			<input id="<?php echo $this->get_field_id( 'exclude' ); ?>" name="<?php echo $this->get_field_name( 'exclude' ); ?>" value="<?php echo $instance['exclude']; ?>" style="width:99%;" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'show_fullname' ); ?>">
			<input class="checkbox" type="checkbox" <?php checked( $instance['show_fullname'], true ); ?> id="<?php echo $this->get_field_id( 'show_fullname' ); ?>" name="<?php echo $this->get_field_name( 'show_fullname' ); ?>" /> <?php _e('Show full name?', 'members'); ?> <code>show_fullname</code></label>
		</p>

		<div style="clear:both;">&nbsp;</div>
	<?php
	}
}

?>