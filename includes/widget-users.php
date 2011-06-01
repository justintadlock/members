<?php
/**
 * Creates a widget that allows users to list users of their site.
 *
 * @package Members
 * @subpackage Includes
 */

/**
 * Users widget archive class.
 *
 * @since 0.1.0
 */
class Members_Widget_Users extends WP_Widget {

	/**
	 * Set up the widget's unique name, ID, class, description, and other options.
	 *
	 * @since 0.1.0
	 */
	function Members_Widget_Users() {

		/* Set up the widget options. */
		$widget_options = array(
			'classname' => 'users',
			'description' => esc_html__( 'Provides the ability to list the users of the site.', 'members' )
		);

		/* Set up the widget control options. */
		$control_options = array(
			'width' => 525,
			'height' => 350,
			'id_base' => 'members-widget-users'
		);

		/* Create the widget. */
		$this->WP_Widget( 'members-widget-users', esc_attr__( 'Users', 'members' ), $widget_options, $control_options );
	}

	/**
	 * Outputs the widget based on the arguments input through the widget controls.
	 *
	 * @since 0.1.0
	 */
	function widget( $args, $instance ) {

		extract( $args, EXTR_SKIP );

		/* Set up the arguments for get_users(). */
		$args = array(
			'role' => $instance['role'],
			'meta_key' => $instance['meta_key'],
			'meta_value' => $instance['meta_value'],
			'include' => ( !empty( $instance['include'] ) ? explode( ',', $instance['include'] ) : '' ),
			'exclude' => ( !empty( $instance['exclude'] ) ? explode( ',', $instance['exclude'] ) : '' ),
			'search' => $instance['search'],
			'orderby' => $instance['orderby'],
			'order' => $instance['order'],
			'offset' => ( !empty( $instance['offset'] ) ? intval( $instance['offset'] ) : '' ),
			'number' => ( !empty( $instance['number'] ) ? intval( $instance['number'] ) : '' ),
		);

		/* Output the theme's $before_widget wrapper. */
		echo $before_widget;

		/* If a title was input by the user, display it. */
		if ( !empty( $instance['title'] ) )
			echo $before_title . apply_filters( 'widget_title',  $instance['title'], $instance, $this->id_base ) . $after_title;

		/* Get users. */
		$users = get_users( $args );

		/* If users were found. */
		if ( !empty( $users ) ) {

			echo '<ul class="xoxo users">';

			/* Loop through each available user, creating a list item with a link to the user's archive. */
			foreach ( $users as $user ) {
				$url = get_author_posts_url( $user->ID, $user->user_nicename );

				$class = "user-{$user->ID}";
				if ( is_author( $user->ID ) )
					$class .= ' current-user';

				echo "<li class='{$class}'><a href='{$url}' title='" . esc_attr( $user->display_name ) . "'>{$user->display_name}</a></li>\n";
			}

			echo '</ul>';
		}

		/* Close the theme's widget wrapper. */
		echo $after_widget;
	}

	/**
	 * Updates the widget control options for the particular instance of the widget.
	 *
	 * @since 0.1.0
	 */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['order'] = strip_tags( $new_instance['order'] );
		$instance['orderby'] = strip_tags( $new_instance['orderby'] );
		$instance['number'] = strip_tags( $new_instance['number'] );
		$instance['offset'] = strip_tags( $new_instance['offset'] );
		$instance['meta_key'] = strip_tags( $new_instance['meta_key'] );
		$instance['meta_value'] = strip_tags( $new_instance['meta_value'] );
		$instance['role'] = strip_tags( $new_instance['role'] );
		$instance['include'] = strip_tags( $new_instance['include'] );
		$instance['exclude'] = strip_tags( $new_instance['exclude'] );
		$instance['search'] = strip_tags( $new_instance['search'] );

		return $instance;
	}

	/**
	 * Displays the widget control options in the Widgets admin screen.
	 *
	 * @since 0.1.0
	 */
	function form( $instance ) {
		global $wp_roles;

		/* Set up the default form values. */
		$defaults = array(
			'title' => esc_attr__( 'Users', 'members' ),
			'order' => 'ASC',
			'orderby' => 'login',
			'role' => '',
			'meta_key' => '',
			'meta_value' => '',
			'include' => '',
			'exclude' => '',
			'search' => '',
			'offset' => '',
			'number' => ''
		);

		/* Merge the user-selected arguments with the defaults. */
		$instance = wp_parse_args( (array) $instance, $defaults );

		$order = array( 'ASC' => esc_attr__( 'Ascending', 'members' ), 'DESC' => esc_attr__( 'Descending', 'members' ) );
		$orderby = array( 'display_name' => esc_attr__( 'Display Name', 'members' ), 'email' => esc_attr__( 'Email', 'members' ), 'ID' => esc_attr__( 'ID', 'members' ), 'nicename' => esc_attr__( 'Nice Name', 'members' ), 'post_count' => esc_attr__( 'Post Count', 'members' ), 'registered' => esc_attr__( 'Registered', 'members' ), 'url' => esc_attr__( 'URL', 'members' ), 'user_login' => esc_attr__( 'Login', 'members' ) );
		$meta_key = array_merge( array( '' ), (array) members_get_user_meta_keys() );
		$roles = array( '' => '' );

		foreach ( $wp_roles->role_names as $role => $name )
			$roles[$role] = $name;
		?>

		<div style="float: left;width: 48%;">

		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'members' ); ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'orderby' ); ?>"><code>orderby</code></label> 
			<select class="widefat" id="<?php echo $this->get_field_id( 'orderby' ); ?>" name="<?php echo $this->get_field_name( 'orderby' ); ?>">
				<?php foreach ( $orderby as $option_value => $option_label ) { ?>
					<option value="<?php echo esc_attr( $option_value ); ?>" <?php selected( $instance['orderby'], $option_value ); ?>><?php echo esc_html( $option_label ); ?></option>
				<?php } ?>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'order' ); ?>"><code>order</code></label> 
			<select class="widefat" id="<?php echo $this->get_field_id( 'order' ); ?>" name="<?php echo $this->get_field_name( 'order' ); ?>">
				<?php foreach ( $order as $option_value => $option_label ) { ?>
					<option value="<?php echo esc_attr( $option_value ); ?>" <?php selected( $instance['order'], $option_value ); ?>><?php echo esc_html( $option_label ); ?></option>
				<?php } ?>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'role' ); ?>"><code>role</code></label> 
			<select class="widefat" id="<?php echo $this->get_field_id( 'role' ); ?>" name="<?php echo $this->get_field_name( 'role' ); ?>">
				<?php foreach ( $roles as $role => $name ) { ?>
					<option value="<?php echo esc_attr( $role ); ?>" <?php selected( $instance['role'], $role ); ?>><?php echo esc_html( $name ); ?></option>
				<?php } ?>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'number' ); ?>"><code>number</code></label>
			<input type="text" class="widefat code" id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" value="<?php echo esc_attr( $instance['number'] ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'offset' ); ?>"><code>offset</code></label>
			<input type="text" class="widefat code" id="<?php echo $this->get_field_id( 'offset' ); ?>" name="<?php echo $this->get_field_name( 'offset' ); ?>" value="<?php echo esc_attr( $instance['offset'] ); ?>" />
		</p>

		</div>
		<div style="float: right; width: 48%;">

		<p>
			<label for="<?php echo $this->get_field_id( 'include' ); ?>"><code>include</code></label>
			<input type="text" class="widefat code" id="<?php echo $this->get_field_id( 'include' ); ?>" name="<?php echo $this->get_field_name( 'include' ); ?>" value="<?php echo esc_attr( $instance['include'] ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'exclude' ); ?>"><code>exclude</code></label>
			<input type="text" class="widefat code" id="<?php echo $this->get_field_id( 'exclude' ); ?>" name="<?php echo $this->get_field_name( 'exclude' ); ?>" value="<?php echo esc_attr( $instance['exclude'] ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'search' ); ?>"><code>search</code></label>
			<input type="text" class="widefat code" id="<?php echo $this->get_field_id( 'search' ); ?>" name="<?php echo $this->get_field_name( 'search' ); ?>" value="<?php echo esc_attr( $instance['search'] ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'meta_key' ); ?>"><code>meta_key</code></label> 
			<select class="widefat" id="<?php echo $this->get_field_id( 'meta_key' ); ?>" name="<?php echo $this->get_field_name( 'meta_key' ); ?>">
				<?php foreach ( $meta_key as $meta ) { ?>
					<option value="<?php echo esc_attr( $meta ); ?>" <?php selected( $instance['meta_key'], $meta ); ?>><?php echo esc_html( $meta ); ?></option>
				<?php } ?>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'meta_value' ); ?>"><code>meta_value</code></label>
			<input type="text" class="widefat code" id="<?php echo $this->get_field_id( 'meta_value' ); ?>" name="<?php echo $this->get_field_name( 'meta_value' ); ?>" value="<?php echo esc_attr( $instance['meta_value'] ); ?>" />
		</p>

		</div>

		<div style="clear:both;">&nbsp;</div>
	<?php
	}
}

?>