<?php
/**
 * Creates a widget that allows users to list users of their site.
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
 * Users widget archive class.
 *
 * @since  2.0.0
 * @access public
 */
class Widget_Users extends \WP_Widget {

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
			'classname'   => 'users',
			'description' => esc_html__( 'Provides the ability to list the users of the site.', 'members' )
		);

		// Set up the widget control options.
		$control_options = array(
			'width'   => 525,
			'height'  => 350,
			'id_base' => 'members-widget-users'
		);

		// Create the widget.
		parent::__construct( 'members-widget-users', esc_html__( 'Members: Users', 'members' ), $widget_options, $control_options );

		// Set up the defaults.
		$this->defaults = array(
			'title'      => esc_attr__( 'Users', 'members' ),
			'order'      => 'ASC',
			'orderby'    => 'login',
			'role'       => '',
			'meta_key'   => '',
			'meta_value' => '',
			'include'    => '',
			'exclude'    => '',
			'search'     => '',
			'offset'     => '',
			'number'     => ''
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

		$instance = wp_parse_args( $instance, $this->defaults );

		// Set up the arguments for get_users().
		$args = array(
			'role'       => $instance['role'],
			'meta_key'   => $instance['meta_key'],
			'meta_value' => $instance['meta_value'],
			'include'    => ! empty( $instance['include'] ) ? explode( ',', $instance['include'] ) : '',
			'exclude'    => ! empty( $instance['exclude'] ) ? explode( ',', $instance['exclude'] ) : '',
			'search'     => $instance['search'],
			'orderby'    => $instance['orderby'],
			'order'      => $instance['order'],
			'offset'     => ! empty( $instance['offset'] ) ? intval( $instance['offset'] ) : '',
			'number'     => ! empty( $instance['number'] ) ? intval( $instance['number'] ) : '',
		);

		// Output the theme's $before_widget wrapper.
		echo $sidebar['before_widget'];

		// If a title was input by the user, display it.
		if ( $instance['title'] )
			echo $sidebar['before_title'] . apply_filters( 'widget_title',  $instance['title'], $instance, $this->id_base ) . $sidebar['after_title'];

		// Get users.
		$users = get_users( $args );

		// If users were found.
		if ( ! empty( $users ) ) {

			echo '<ul class="xoxo users">';

			// Loop through each available user, creating a list item with a link to the user's archive.
			foreach ( $users as $user ) {

				$class = sanitize_html_class( "user-{$user->ID}" );

				if ( is_author( $user->ID ) )
					$class .= ' current-user';

				printf(
					'<li class="%s"><a href="%s">%s</a>',
					esc_attr( $class ),
					esc_url( get_author_posts_url( $user->ID, $user->user_nicename ) ),
					esc_html( $user->display_name )
				);
			}

			echo '</ul>';
		}

		// Close the theme's widget wrapper.
		echo $sidebar['after_widget'];
	}

	/**
	 * Sanitizes/Validates widget options before being saved.
	 *
	 * @since  2.0.0
	 * @access public
	 * @param  array   $new_instance
	 * @param  array   $old_instance
	 * @return array
	 */
	function update( $new_instance, $old_instance ) {

		// Text fields.
		$instance['title']      = sanitize_text_field( $new_instance['title']      );
		$instance['order']      = sanitize_text_field( $new_instance['order']      );
		$instance['orderby']    = sanitize_text_field( $new_instance['orderby']    );
		$instance['meta_key']   = sanitize_text_field( $new_instance['meta_key']   );
		$instance['meta_value'] = sanitize_text_field( $new_instance['meta_value'] );
		$instance['search']     = sanitize_text_field( $new_instance['search']     );

		// Roles.
		$instance['role'] = members_role_exists( $new_instance['role'] ) ? $new_instance['role'] : '';

		// ID lists.
		$instance['include'] = $new_instance['include'] ? join( ',', wp_parse_id_list( $new_instance['include'] ) ) : '';
		$instance['exclude'] = $new_instance['exclude'] ? join( ',', wp_parse_id_list( $new_instance['exclude'] ) ) : '';

		// Integers.
		$instance['offset'] = absint( $new_instance['offset'] );
		$instance['number'] = absint( $new_instance['number'] ) > 0 ? absint( $new_instance['number'] ) : '';

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
		$instance = wp_parse_args( (array) $instance, $this->defaults );

		$order = array(
			'ASC'  => esc_attr__( 'Ascending',  'members' ),
			'DESC' => esc_attr__( 'Descending', 'members' )
		);

		$orderby = array(
			'display_name' => esc_attr__( 'Display Name', 'members' ),
			'email'        => esc_attr__( 'Email',        'members' ),
			'ID'           => esc_attr__( 'ID',           'members' ),
			'nicename'     => esc_attr__( 'Nice Name',    'members' ),
			'post_count'   => esc_attr__( 'Post Count',   'members' ),
			'registered'   => esc_attr__( 'Registered',   'members' ),
			'url'          => esc_attr__( 'URL',          'members' ),
			'user_login'   => esc_attr__( 'Login',        'members' )
		);

		$meta_key = array_merge( array( '' ), (array) members_get_user_meta_keys() );

		$roles = members_get_roles();
		asort( $roles ); ?>

		<div style="float: left;width: 48%;">

		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php esc_html_e( 'Title:', 'members' ); ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'orderby' ); ?>"><?php esc_html_e( 'Order By:', 'members' ); ?></label>
			<select class="widefat" id="<?php echo $this->get_field_id( 'orderby' ); ?>" name="<?php echo $this->get_field_name( 'orderby' ); ?>">
				<?php foreach ( $orderby as $option_value => $option_label ) : ?>
					<option value="<?php echo esc_attr( $option_value ); ?>" <?php selected( $instance['orderby'], $option_value ); ?>><?php echo esc_html( $option_label ); ?></option>
				<?php endforeach; ?>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'order' ); ?>"><?php esc_html_e( 'Order:', 'members' ); ?></label>
			<select class="widefat" id="<?php echo $this->get_field_id( 'order' ); ?>" name="<?php echo $this->get_field_name( 'order' ); ?>">
				<?php foreach ( $order as $option_value => $option_label ) : ?>
					<option value="<?php echo esc_attr( $option_value ); ?>" <?php selected( $instance['order'], $option_value ); ?>><?php echo esc_html( $option_label ); ?></option>
				<?php endforeach; ?>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'role' ); ?>"><?php esc_html_e( 'Role:', 'members' ); ?></label>
			<select class="widefat" id="<?php echo $this->get_field_id( 'role' ); ?>" name="<?php echo $this->get_field_name( 'role' ); ?>">
				<option value="" <?php selected( $instance['role'], '' ); ?>></option>
				<?php foreach ( $roles as $name => $role ) : ?>
					<option value="<?php echo esc_attr( $name ); ?>" <?php selected( $instance['role'], $name ); ?>><?php echo esc_html( $role->get( 'label' ) ); ?></option>
				<?php endforeach; ?>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php esc_html_e( 'Limit:', 'members' ); ?></label>
			<input type="number" min="0" class="widefat code" id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" value="<?php echo esc_attr( $instance['number'] ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'offset' ); ?>"><?php esc_html_e( 'Offset:', 'members' ); ?></label>
			<input type="number" min="1" class="widefat code" id="<?php echo $this->get_field_id( 'offset' ); ?>" name="<?php echo $this->get_field_name( 'offset' ); ?>" value="<?php echo esc_attr( $instance['offset'] ); ?>" />
		</p>

		</div>
		<div style="float: right; width: 48%;">

		<p>
			<label for="<?php echo $this->get_field_id( 'include' ); ?>"><?php esc_html_e( 'Include:', 'members' ); ?></label>
			<input type="text" class="widefat code" id="<?php echo $this->get_field_id( 'include' ); ?>" name="<?php echo $this->get_field_name( 'include' ); ?>" value="<?php echo esc_attr( $instance['include'] ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'exclude' ); ?>"><?php esc_html_e( 'Exclude:', 'members' ); ?></label>
			<input type="text" class="widefat code" id="<?php echo $this->get_field_id( 'exclude' ); ?>" name="<?php echo $this->get_field_name( 'exclude' ); ?>" value="<?php echo esc_attr( $instance['exclude'] ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'search' ); ?>"><?php esc_html_e( 'Search:', 'members' ); ?></label>
			<input type="text" class="widefat code" id="<?php echo $this->get_field_id( 'search' ); ?>" name="<?php echo $this->get_field_name( 'search' ); ?>" value="<?php echo esc_attr( $instance['search'] ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'meta_key' ); ?>"><?php esc_html_e( 'Meta Key:', 'members' ); ?></label>
			<select class="widefat" id="<?php echo $this->get_field_id( 'meta_key' ); ?>" name="<?php echo $this->get_field_name( 'meta_key' ); ?>">
				<?php foreach ( $meta_key as $meta ) { ?>
					<option value="<?php echo esc_attr( $meta ); ?>" <?php selected( $instance['meta_key'], $meta ); ?>><?php echo esc_html( $meta ); ?></option>
				<?php } ?>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'meta_value' ); ?>"><?php esc_html_e( 'Meta Value:', 'members' ); ?></label>
			<input type="text" class="widefat code" id="<?php echo $this->get_field_id( 'meta_value' ); ?>" name="<?php echo $this->get_field_name( 'meta_value' ); ?>" value="<?php echo esc_attr( $instance['meta_value'] ); ?>" />
		</p>

		</div>

		<div style="clear:both;">&nbsp;</div>
	<?php
	}
}
