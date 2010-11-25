<?php
/**
 * Creates a widget that allows users to add a login form to a widget area.
 *
 * @package Members
 * @subpackage Components
 */

class Members_Widget_Login extends WP_Widget {

	function Members_Widget_Login() {
		$widget_ops = array( 'classname' => 'login', 'description' => __('A widget that allows users to log into your site.', 'widgets-reloaded') );
		$control_ops = array( 'width' => 700, 'height' => 350, 'id_base' => 'members-widget-login' );
		$this->WP_Widget( 'members-widget-login', __('Login Form', 'widgets-reloaded'), $widget_ops, $control_ops );
	}

	function widget( $args, $instance ) {
		global $user_identity, $user_ID;

		extract( $args );

		$title = apply_filters( 'widget_title', $instance['title'] );

		$username_label = $instance['username_label'];
		$password_label = $instance['password_label'];
		$submit_text = $instance['submit_text'];
		$remember_text = $instance['remember_text'];

		$logged_in_text = apply_filters( 'widget_text', $instance['logged_in_text'] );
		$logged_out_text = apply_filters( 'widget_text', $instance['logged_out_text'] );

		$show_avatar = isset( $instance['show_avatar'] ) ? $instance['show_avatar'] : false;

		echo $before_widget;

		if ( $title )
			echo "\n\t\t\t" . $before_title . $title . $after_title;

		if ( is_user_logged_in() ) {

			if ( $show_avatar )
				echo get_avatar( $user_ID );

			if ( $logged_in_text )
				echo $logged_in_text;

		}
		else {
			if ( $show_avatar )
				echo get_avatar( $user_ID );

			if ( $logged_out_text )
				echo $logged_out_text;

		$login = '<div class="clear log-in login-form">';

			$login .= '<form class="log-in" action="' . get_bloginfo( 'wpurl' ) . '/wp-login.php" method="post">';

				$login .= '<p class="text-input">';
					$login .= '<label class="text" for="log">' . $username_label . '</label>';
					$login .= '<input class="field" type="text" name="log" id="log" value="' . esc_attr( $user_login ) . '" size="23" />';
				$login .= '</p>';

				$login .= '<p class="text-input">';
					$login .= '<label class="text" for="pwd">' . $password_label . '</label>';
					$login .= '<input class="field" type="password" name="pwd" id="pwd" size="23" />';
				$login .= '</p>';

				$login .= '<div class="clear">';
					$login .= '<input type="submit" name="submit" value="' . $submit_text . '" class="log-in" />';
					$login .= '<label class="remember"><input name="rememberme" id="rememberme" type="checkbox" checked="checked" value="forever" /> ' . $remember_text . '</label>';
					$login .= '<input type="hidden" name="redirect_to" value="' . $_SERVER['REQUEST_URI'] . '"/>';
				$login .= '</div>';

			$login .= '</form>';

		$login .= '</div>';

			echo $login;
		}

		echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['username_label'] = strip_tags( $new_instance['username_label'] );
		$instance['password_label'] = strip_tags( $new_instance['password_label'] );
		$instance['submit_text'] = strip_tags( $new_instance['submit_text'] );
		$instance['remember_text'] = strip_tags( $new_instance['remember_text'] );
		$instance['show_avatar'] = $new_instance['show_avatar'];

		if ( current_user_can('unfiltered_html') ) {
			$instance['logged_in_text'] =  $new_instance['logged_in_text'];
			$instance['logged_out_text'] =  $new_instance['logged_out_text'];
		}
		else {
			$instance['logged_in_text'] = wp_filter_post_kses( $new_instance['logged_in_text'] );
			$instance['logged_out_text'] = wp_filter_post_kses( $new_instance['logged_out_text'] );
		}

		return $instance;
	}

	function form( $instance ) {

		//Defaults
		$defaults = array(
			'title' => __('Log In', 'widgets-reloaded'), 
			'username_label' => __('Username:', 'members'),
			'password_label' => __('Password', 'members'),
			'submit_text' => __('Log In', 'members'),
			'remember_text' => __('Remember me?', 'members'),
			'show_avatar' => true,
			'logged_out_text' => __('Please log into the site.', 'members'),
			'logged_in_text' => __('You are currently logged in.', 'members')
		);

		$instance = wp_parse_args( (array) $instance, $defaults );
		$logged_in_text = format_to_edit( $instance['logged_in_text'] );
		$logged_out_text = format_to_edit( $instance['logged_out_text'] ); ?>

		<div style="float: left; width: 48%;">
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:', 'widgets-reloaded'); ?></label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:100%;" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'logged_out_text' ); ?>"><?php _e('Logged out text:', 'members'); ?></label>
			<textarea class="widefat" rows="3" cols="20" id="<?php echo $this->get_field_id('logged_out_text'); ?>" name="<?php echo $this->get_field_name('logged_out_text'); ?>" style="width:100%;"><?php echo $logged_out_text; ?></textarea>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'logged_in_text' ); ?>"><?php _e('Logged in text:', 'members'); ?></label>
			<textarea class="widefat" rows="3" cols="20" id="<?php echo $this->get_field_id('logged_in_text'); ?>" name="<?php echo $this->get_field_name('logged_in_text'); ?>" style="width:100%;"><?php echo $logged_in_text; ?></textarea>
		</p>
		</div>

		<div style="float: right; width: 48%;">

		<p>
			<label for="<?php echo $this->get_field_id( 'show_avatar' ); ?>">
			<input class="checkbox" type="checkbox" <?php checked( $instance['show_avatar'], true ); ?> id="<?php echo $this->get_field_id( 'show_avatar' ); ?>" name="<?php echo $this->get_field_name( 'show_avatar' ); ?>" /> <?php _e('Display avatar?', 'widgets-reloaded'); ?></label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'username_label' ); ?>"><?php _e('Username Label:', 'widgets-reloaded'); ?></label>
			<input id="<?php echo $this->get_field_id( 'username_label' ); ?>" name="<?php echo $this->get_field_name( 'username_label' ); ?>" value="<?php echo $instance['username_label']; ?>" style="width:100%;" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'password_label' ); ?>"><?php _e('Password Label:', 'widgets-reloaded'); ?></label>
			<input id="<?php echo $this->get_field_id( 'password_label' ); ?>" name="<?php echo $this->get_field_name( 'password_label' ); ?>" value="<?php echo $instance['password_label']; ?>" style="width:100%;" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'submit_text' ); ?>"><?php _e('Submit Text:', 'widgets-reloaded'); ?></label>
			<input id="<?php echo $this->get_field_id( 'submit_text' ); ?>" name="<?php echo $this->get_field_name( 'submit_text' ); ?>" value="<?php echo $instance['submit_text']; ?>" style="width:100%;" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'remember_text' ); ?>"><?php _e('Remember User Text:', 'widgets-reloaded'); ?></label>
			<input id="<?php echo $this->get_field_id( 'remember_text' ); ?>" name="<?php echo $this->get_field_name( 'remember_text' ); ?>" value="<?php echo $instance['remember_text']; ?>" style="width:100%;" />
		</p>
		</div>

		<div>

		</div>
		<div style="clear:both;">&nbsp;</div>
	<?php
	}
}

?>