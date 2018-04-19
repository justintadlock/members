<?php
/**
 * Creates a widget that allows users to add a login form to a widget area.
 *
 * @package    Members
 * @subpackage Includes
 * @author     Justin Tadlock <justintadlock@gmail.com>
 * @copyright  Copyright (c) 2009 - 2018, Justin Tadlock
 * @link       https://themehybrid.com/plugins/members
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace Members;

/**
 * Login form widget class.
 *
 * @since  2.0.0
 * @access public
 */
class Widget_Login extends \WP_Widget {

	/**
	 * Default arguments for the widget settings.
	 *
	 * @since  2.0.0
	 * @access public
	 * @var    array
	 */
	public $defaults = array();

	/**
	 * Set up the widget's unique name, ID, class, description, and other options.
	 *
	 * @since  2.0.0
	 * @access public
	 * @return void
	 */
	public function __construct() {

		// Set up the widget options.
		$widget_options = array(
			'classname'   => 'login',
			'description' => esc_html__( 'A widget that allows users to log into your site.', 'members' )
		);

		// Set up the widget control options.
		$control_options = array(
			'width'   => 800,
			'height'  => 350,
			'id_base' => 'members-widget-login'
		);

		// Create the widget.
		parent::__construct( 'members-widget-login', esc_attr__( 'Members: Login Form', 'members' ), $widget_options, $control_options );

		// Set up the defaults.
		$this->defaults = array(
			'title'           => esc_attr__( 'Log In',      'members' ),
			'label_username'  => esc_attr__( 'Username',    'members' ),
			'label_password'  => esc_attr__( 'Password',    'members' ),
			'label_log_in'    => esc_attr__( 'Log In',      'members' ),
			'label_remember'  => esc_attr__( 'Remember Me', 'members' ),
			'form_id'         => 'loginform',
			'id_username'     => 'user_login',
			'id_password'     => 'user_pass',
			'id_remember'     => 'rememberme',
			'id_submit'       => 'wp-submit',
			'remember'        => true,
			'value_remember'  => false,
			'value_username'  => '',
			'show_avatar'     => true,
			'logged_out_text' => esc_html__( 'Please log into the site.',   'members' ),
			'logged_in_text'  => esc_html__( 'You are currently logged in.', 'members' )
		);
	}

	/**
	 * Outputs the widget based on the arguments input through the widget controls.
	 *
	 * @since  2.0.0
	 * @access public
	 * @param  array  $sidebar
	 * @param  array  $instance
	 * @return void
	 */
	function widget( $sidebar, $instance ) {
		global $user_identity, $user_ID;

		$instance = wp_parse_args( $instance, $this->defaults );

		// Set up the arguments for wp_login_form().
		$args = array(
	 		'form_id'        => ! empty( $instance['form_id'] ) ? esc_attr( $instance['form_id'] ) : 'loginform',

			'label_username' => esc_html( $instance['label_username'] ),
			'label_password' => esc_html( $instance['label_password'] ),
			'label_remember' => esc_html( $instance['label_remember'] ),
			'label_log_in'   => esc_html( $instance['label_log_in']   ),
			'id_username'    => esc_attr( $instance['id_username']    ),
			'id_password'    => esc_attr( $instance['id_password']    ),
			'id_remember'    => esc_attr( $instance['id_remember']    ),
			'id_submit'      => esc_attr( $instance['id_submit']      ),
			'value_username' => esc_attr( $instance['value_username'] ),

			'remember'       => ! empty( $instance['remember']       ) ? true : false,
			'value_remember' => ! empty( $instance['value_remember'] ) ? true : false,
			'echo'           => false,
		);

		if ( ! empty( $instance['redirect'] ) )
			$args['redirect'] = esc_url( $instance['redirect'] );

		// Get the logged in/out text.
		$logged_in_text  = apply_filters( 'widget_text', $instance['logged_in_text'] );
		$logged_out_text = apply_filters( 'widget_text', $instance['logged_out_text'] );

		// Output the theme's $before_widget wrapper.
		echo $sidebar['before_widget'];

		// If a title was input by the user, display it.
		if ( $instance['title'] )
			echo $sidebar['before_title'] . apply_filters( 'widget_title',  $instance['title'], $instance, $this->id_base ) . $sidebar['after_title'];

		// If the current user is logged in.
		if ( is_user_logged_in() ) {

			// Show avatar if enabled.
			if ( ! empty( $instance['show_avatar'] ) )
				echo get_avatar( $user_ID );

			// Show logged in text if any is written.
			if ( $logged_in_text )
				echo do_shortcode( shortcode_unautop( wpautop( $logged_in_text ) ) );
		}

		// If the current user is not logged in.
		else {

			// Show avatar if enabled.
			if ( ! empty( $instance['show_avatar'] ) )
				echo get_avatar( $user_ID );

			// Show logged out text if any is written.
			if ( $logged_out_text )
				echo do_shortcode( shortcode_unautop( wpautop( $logged_out_text ) ) );

			// Output the login form.
			echo '<div class="members-login-form">' . wp_login_form( $args ) . '</div>';
		}

		// Close the theme's widget wrapper.
		echo $sidebar['after_widget'];
	}

	/**
	 * Sanitizes/Validates widget options before being saved.
	 *
	 * @since  2.0.0
	 * @access public
	 * @param  array  $new_instance
	 * @param  array  $old_instance
	 * @return array
	 */
	function update( $new_instance, $old_instance ) {

		$instance['title']          = strip_tags( $new_instance['title'] );
		$instance['label_username'] = strip_tags( $new_instance['label_username'] );
		$instance['label_password'] = strip_tags( $new_instance['label_password'] );
		$instance['label_remember'] = strip_tags( $new_instance['label_remember'] );
		$instance['label_log_in']   = strip_tags( $new_instance['label_log_in'] );
		$instance['id_username']    = strip_tags( $new_instance['id_username'] );
		$instance['id_password']    = strip_tags( $new_instance['id_password'] );
		$instance['id_remember']    = strip_tags( $new_instance['id_remember'] );
		$instance['id_submit']      = strip_tags( $new_instance['id_submit'] );
		$instance['value_username'] = strip_tags( $new_instance['value_username'] );

		$instance['remember']       = isset( $new_instance['remember'] )       ? 1 : 0;
		$instance['value_remember'] = isset( $new_instance['value_remember'] ) ? 1 : 0;
		$instance['show_avatar']    = isset( $new_instance['show_avatar'] )    ? 1 : 0;

		if ( current_user_can( 'unfiltered_html' ) ) {
			$instance['logged_in_text']  = $new_instance['logged_in_text'];
			$instance['logged_out_text'] = $new_instance['logged_out_text'];
		} else {
			$instance['logged_in_text']  = wp_kses_post( stripslashes( $new_instance['logged_in_text']  ) );
			$instance['logged_out_text'] = wp_kses_post( stripslashes( $new_instance['logged_out_text'] ) );
		}

		return $instance;
	}

	/**
	 * Displays the widget control options in the Widgets admin screen.
	 *
	 * @since  2.0.0
	 * @access public
	 * @param  array  $instance
	 * @return void
	 */
	function form( $instance ) {

		// Merge the user-selected arguments with the defaults.
		$instance = wp_parse_args( (array) $instance, $this->defaults ); ?>

		<div style="float: left; width: 31%; margin-right: 3.5%;">

		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php esc_html_e( 'Title:', 'members' ); ?></label>
			<input type="text" class="widefat code" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'label_username' ); ?>"><?php esc_html_e( 'Username Label:', 'members' ); ?></label>
			<input type="text" class="widefat code" id="<?php echo $this->get_field_id( 'label_username' ); ?>" name="<?php echo $this->get_field_name( 'label_username' ); ?>" value="<?php echo esc_attr( $instance['label_username'] ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'label_password' ); ?>"><?php esc_html_e( 'Password Label:', 'members' ); ?></label>
			<input type="text" class="widefat code" id="<?php echo $this->get_field_id( 'label_password' ); ?>" name="<?php echo $this->get_field_name( 'label_password' ); ?>" value="<?php echo esc_attr( $instance['label_password'] ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'label_log_in' ); ?>"><?php esc_html_e( 'Log In Label:', 'members' ); ?></label>
			<input type="text" class="widefat code" id="<?php echo $this->get_field_id( 'label_log_in' ); ?>" name="<?php echo $this->get_field_name( 'label_log_in' ); ?>" value="<?php echo esc_attr( $instance['label_log_in'] ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'label_remember' ); ?>"><?php esc_html_e( 'Remember Me Label:', 'members' ); ?></label>
			<input type="text" class="widefat code" id="<?php echo $this->get_field_id( 'label_remember' ); ?>" name="<?php echo $this->get_field_name( 'label_remember' ); ?>" value="<?php echo esc_attr( $instance['label_remember'] ); ?>" />
		</p>

		</div>
		<div style="float: left; width: 31%; margin-right: 3.5%;">

		<p>
			<label for="<?php echo $this->get_field_id( 'value_username' ); ?>"><?php esc_html_e( 'Username Value:', 'members' ); ?></label>
			<input type="text" class="widefat code" id="<?php echo $this->get_field_id( 'value_username' ); ?>" name="<?php echo $this->get_field_name( 'value_username' ); ?>" value="<?php echo esc_attr( $instance['value_username'] ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'id_username' ); ?>"><?php esc_html_e( 'Username Field ID:', 'members' ); ?></label>
			<input type="text" class="widefat code" id="<?php echo $this->get_field_id( 'id_username' ); ?>" name="<?php echo $this->get_field_name( 'id_username' ); ?>" value="<?php echo esc_attr( $instance['id_username'] ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'id_remember' ); ?>"><?php esc_html_e( 'Remember Me Field ID:', 'members' ); ?></label>
			<input type="text" class="widefat code" id="<?php echo $this->get_field_id( 'id_remember' ); ?>" name="<?php echo $this->get_field_name( 'id_remember' ); ?>" value="<?php echo esc_attr( $instance['id_remember'] ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'id_password' ); ?>"><?php esc_html_e( 'Password Field ID:', 'members' ); ?></label>
			<input type="text" class="widefat code" id="<?php echo $this->get_field_id( 'id_password' ); ?>" name="<?php echo $this->get_field_name( 'id_password' ); ?>" value="<?php echo esc_attr( $instance['id_password'] ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'id_submit' ); ?>"><?php esc_html_e( 'Submit Button ID:', 'members' ); ?></label>
			<input type="text" class="widefat code" id="<?php echo $this->get_field_id( 'id_submit' ); ?>" name="<?php echo $this->get_field_name( 'id_submit' ); ?>" value="<?php echo esc_attr( $instance['id_submit'] ); ?>" />
		</p>

		</div>

		<div style="float: right; width: 31%;">

		<p>
			<label>
				<input class="checkbox" type="checkbox" <?php checked( $instance['remember'] ); ?> name="<?php echo $this->get_field_name( 'remember' ); ?>" />
				<?php esc_html_e( '"Remember me" checkbox?', 'members' ); ?>
			</label>
		</p>
		<p>
			<label>
				<input class="checkbox" type="checkbox" <?php checked( $instance['value_remember'] ); ?> name="<?php echo $this->get_field_name( 'value_remember' ); ?>" />
				<?php esc_html_e( 'Check "remember me"?', 'members' ); ?>
			</label>
		</p>
		<p>
			<label>
				<input class="checkbox" type="checkbox" <?php checked( $instance['show_avatar'], true ); ?> name="<?php echo $this->get_field_name( 'show_avatar' ); ?>" />
				<?php esc_html_e( 'Display avatar?', 'members' ); ?>
			</label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'logged_out_text' ); ?>"><?php esc_html_e( 'Logged out text:', 'members' ); ?></label>
			<textarea class="widefat" rows="4" cols="20" id="<?php echo $this->get_field_id( 'logged_out_text' ); ?>" name="<?php echo $this->get_field_name( 'logged_out_text' ); ?>" style="width:100%;"><?php echo esc_textarea( $instance['logged_out_text'] ); ?></textarea>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'logged_in_text' ); ?>"><?php esc_html_e( 'Logged in text:', 'members' ); ?></label>
			<textarea class="widefat" rows="4" cols="20" id="<?php echo $this->get_field_id( 'logged_in_text' ); ?>" name="<?php echo $this->get_field_name( 'logged_in_text' ); ?>" style="width:100%;"><?php echo esc_textarea( $instance['logged_in_text'] ); ?></textarea>
		</p>

		</div>

		<div style="clear:both;">&nbsp;</div>
	<?php
	}
}
