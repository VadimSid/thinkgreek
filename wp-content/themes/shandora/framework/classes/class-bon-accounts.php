<?php if ( ! defined( 'ABSPATH' ) ) exit('No direct script access allowed'); // Exit if accessed directly

/**
 * Class BON Account
 *
 *
 *
 * @author		Hermanto Lim
 * @copyright	Copyright (c) Hermanto Lim
 * @link		http://bonfirelab.com
 * @since		Version 1.0
 * @package 	BonFramework
 * @category 	Core
 *
 *
*/ 

class BON_Accounts {

	/**
	 * @var BON_Front_End_Editor The single instance of the class
	 */
	protected static $_instance = null;

	/** 
	 * @var array Query vars to add to wp 
	 */
	public $query_vars = array();
	
	/** 
	 * @var string myaccount page permalink
	 */
	public $my_account_page_id;

	function __construct() {

		add_action( 'template_redirect', array( $this, 'save_account_details' ) );

		add_action( 'bon_before_login_form', array( $this, 'process_login'          ) );
		add_action( 'bon_before_login_form', array( $this, 'process_registration'   ) );
		
		add_action( 'bon_before_lost_password_form', array( $this, 'process_reset_password' ) );
		
		add_action( 'bon_before_login_form',         'bon_show_error' );
		add_action( 'bon_before_lost_password_form', 'bon_show_error' );
		add_action( 'bon_before_edit_account_form',  'bon_show_error' );
		add_action( 'bon_before_account', 			 'bon_show_error' );

		add_filter( 'bon_shortcode_lists', array( $this, 'shortcode_filter' ) );
		add_filter( 'user_contactmethods', array( $this, 'filter_contact_method' ) );

		add_filter( 'comments_open', array( $this, 'remove_comments' ), 10, 2 );
		add_filter( 'pings_open', array( $this, 'remove_comments' ), 10, 2 );

		$this->my_account_page_id = apply_filters( 'bon_my_account_page_id', bon_get_option( 'my_account_page', '' ) ); 

		add_action( 'init', array( $this, 'add_endpoints') );

		if ( ! is_admin() ) {
			
			add_filter( 'query_vars', array( $this, 'add_query_vars'), 0 );
			add_action( 'parse_request', array( $this, 'parse_request'), 0 );
		}

		$this->init_query_vars();

	}

	/**
	 * Init query vars by loading options.
	 */
	public function init_query_vars() {
		// Query vars to add to WP
		$this->query_vars = array(
			'edit-account'    => apply_filters( 'bon_accounts_edit_account_end_point', _x( 'edit-account', 'edit-account-slug', 'bon' ) ),
			'lost-password'   => apply_filters( 'bon_accounts_lost_password_endpoint', _x( 'lost-password', 'lost-password-slug', 'bon' ) ),
		);
	}

	/**
	 * Add endpoints for query vars
	 */
	public function add_endpoints() {
		foreach ( $this->query_vars as $key => $var )
			add_rewrite_endpoint( $var, EP_PAGES );
	}

	/**
	 * add_query_vars function.
	 *
	 * @access public
	 * @param array $vars
	 * @return array
	 */
	public function add_query_vars( $vars ) {
		foreach ( $this->query_vars as $key => $var )
			$vars[] = $key;

		return $vars;
	}

	/**
	 * Get query vars
	 * @return array()
	 */
	public function get_query_vars() {
		return $this->query_vars;
	}

	/**
	 * Parse the request and look for query vars - endpoints may not be supported
	 */
	public function parse_request() {

		global $wp;

		// Map query vars to their keys, or get them if endpoints are not supported
		foreach ( $this->query_vars as $key => $var ) {
			if ( isset( $_GET[ $var ] ) ) {
				$wp->query_vars[ $key ] = $_GET[ $var ];
			}

			elseif ( isset( $wp->query_vars[ $var ] ) ) {
				$wp->query_vars[ $key ] = $wp->query_vars[ $var ];
			}
		}
	}
	/**
	 * Added shortcode key to the shortcode list array.
	 * @uses bon_shortcode_lists filter
	 * @see Class BON_Shortcodes
	 * @var array shortcodes
	 * @return array shortcodes
	 *
	 */
	public function shortcode_filter( $shortcodes ) {

		$shortcodes['bon-account'] = array( $this, 'render_shortcode' );
		return $shortcodes;
	}

	/**
	 * Define class instance
	 * @var self::$_instance
	 * @return object self
	 *
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) )
			self::$_instance = new self();
		return self::$_instance;
	}

	/**
	* Get endpoint URL
	*
	* Gets the URL for an endpoint, which varies depending on permalink settings.
	*
	* @param string $page
	* @return string
	*/
	public function get_endpoint_url( $endpoint, $value = '', $permalink = '' ) {
		if ( ! $permalink )
			$permalink = get_permalink();

		// Map endpoint to options
		$endpoint = isset( $this->query_vars[ $endpoint ] ) ? $this->query_vars[ $endpoint ] : $endpoint;

		if ( get_option( 'permalink_structure' ) ) {
			if ( strstr( $permalink, '?' ) ) {
				$query_string = '?' . parse_url( $permalink, PHP_URL_QUERY );
				$permalink = current( explode( '?', $permalink ) );
			} else {
				$query_string = '';
			}

			$url = trailingslashit( $permalink ) . $endpoint . '/' . $value . $query_string;

		} else {
			$url = add_query_arg( $endpoint, $value, $permalink );

		}

		return apply_filters( 'bon_account_get_endpoint_url', $url );
	}
	
	public function my_account_url() {
		return get_permalink( $this->my_account_page_id );
	}
	public function lost_password_url() {
		return $this->get_endpoint_url( 'lost-password', '', $this->my_account_url() );
	}

	public function edit_account_url() {
		return $this->get_endpoint_url( 'edit-account', '', $this->my_account_url() );
	}

	public function remove_comments( $open, $post_id ) {
		global $post;

		if( !$post_id )
			$post_id = $post->ID;

		if( $post_id == $this->my_account_page_id ) {
			$open = false;
		}

		return $open;
	}
	/**
	 * Save the password/account details and redirect back to the my account page.
	 */
	public function save_account_details() {

		if ( 'POST' !== strtoupper( $_SERVER[ 'REQUEST_METHOD' ] ) ) {
			return;
		}

		if ( empty( $_POST[ 'action' ] ) || ( 'save_account_details' !== $_POST[ 'action' ] ) || empty( $_POST['_wpnonce'] ) ) {
			return;
		}

		wp_verify_nonce( $_POST['_wpnonce'], 'bon_save_account_details' );

		$update       = true;
		$user         = new stdClass();

		$user->ID     = (int) get_current_user_id();
		$current_user = get_user_by( 'id', $user->ID );

		if ( $user->ID <= 0 ) {
			return;
		}

		/* Name */
		$account_first_name     = ! empty( $_POST[ 'account_first_name' ] )  ? sanitize_text_field( $_POST[ 'account_first_name' ] ) : '';
		$account_last_name      = ! empty( $_POST[ 'account_last_name' ] )   ? sanitize_text_field( $_POST[ 'account_last_name' ] )  : '';
		$account_nickname       = ! empty( $_POST[ 'account_nickname'] )     ? sanitize_text_field( $_POST['account_nickname'] )     : '';
		$account_display_name   = ! empty( $_POST[ 'account_display_name'] ) ? sanitize_text_field( $_POST['account_display_name'] ) : '';
		
		/* Contact Info */
		$account_email          = ! empty( $_POST[ 'account_email' ] )       ? sanitize_email( $_POST[ 'account_email' ] )      : '';
		$account_url            = ! empty( $_POST[ 'account_url' ] )         ? esc_url( $_POST[ 'account_url' ] )               : '';
		$account_description    = ! empty( $_POST[ 'account_description' ] ) ? esc_textarea( $_POST[ 'account_description' ] )  : '';

		$pass1              = ! empty( $_POST[ 'password_1' ] ) ? $_POST[ 'password_1' ] : '';
		$pass2              = ! empty( $_POST[ 'password_2' ] ) ? $_POST[ 'password_2' ] : '';

		$user->first_name   = $account_first_name;
		$user->last_name    = $account_last_name;
		$user->nickname     = $account_nickname;
		$user->display_name = $account_display_name;

		$user->user_email   = $account_email;
		$user->user_url     = $account_url;
		$user->description  = $account_description;

		foreach ( wp_get_user_contact_methods( $user ) as $name => $desc ) {
			if( isset( $_POST[$name] ) ) {
				$user->$name = $_POST[$name];
			}
		}

		if ( $pass1 ) {
			$user->user_pass = $pass1;
		}

		if ( empty( $account_first_name ) || empty( $account_last_name ) ) {
			bon_error_notice()->add( 'name_empty', __( 'Please enter your name.', 'bon' ), 'error' );
		}

		if ( empty( $account_email ) || ! is_email( $account_email ) ) {
			bon_error_notice()->add( 'invalid_email', __( 'Please provide a valid email address.', 'bon' ), 'error' );
		} elseif ( email_exists( $account_email ) && $account_email !== $current_user->user_email ) {
			bon_error_notice()->add( 'email_exists', __( 'This email address is already registered.', 'bon' ), 'error' );
		}

		if ( ! empty( $pass1 ) && empty( $pass2 ) ) {
			bon_error_notice()->add( 'password_mismatch', __( 'Please re-enter your password.', 'bon' ), 'error' );
		} elseif ( ! empty( $pass1 ) && $pass1 !== $pass2 ) {
			bon_error_notice()->add( 'password_mismatch', __( 'Passwords do not match.', 'bon' ), 'error' );
		}

		$errors = bon_error_notice();
		$errors_message = $errors->get_error_messages();
		// Allow plugins to return their own errors.
		do_action_ref_array( 'user_profile_update_errors', array( &$errors, $update, &$user ) );

		if ( empty( $errors_message ) ) {

			wp_update_user( $user ) ;

			do_action( 'bon_save_account_details', $user->ID );

			wp_safe_redirect( add_query_arg( 'updated', 'true', $this->my_account_url() ) );

			exit;
		}
	}

	public function render_shortcode( $attr ){
		return BON_Shortcodes::render( array( $this, 'my_account_page_shortcode' ), $attr, true );
	}

	public function my_account_page_shortcode( $attr ) {
		global $wp;

		$args = array();

		if ( ! is_user_logged_in() ) {

			if ( isset( $wp->query_vars['lost-password'] ) ) {
				
				$args['form'] = 'lost_password';

				// process reset key / login from email confirmation link
				if ( isset( $_GET['key'] ) && isset( $_GET['login'] ) ) {

					$user = self::_check_password_reset_key( $_GET['key'], $_GET['login'] );

					// reset key / login is correct, display reset password form with hidden key / login values
					if( is_object( $user ) ) {
						$args['form'] = 'reset_password';
						$args['key'] = esc_attr( $_GET['key'] );
						$args['login'] = esc_attr( $_GET['login'] );
					}
				} elseif ( isset( $_GET['reset'] ) ) {
					bon_error_notice()->add( 'password_reset_success', __( 'Your password has been reset.', 'bon' ) . ' <a href="' . $this->my_account_url() . '">' . __( 'Log in', 'bon' ) . '</a>', 'success' );
				}

			} else {
				$args['form'] = 'login';
				$args['lost_password_url'] = $this->lost_password_url();
			}

		} else {

			if ( isset( $wp->query_vars['edit-account'] ) ) {

				$args['form'] = 'edit_account';
				$args['user'] = get_user_by( 'id', get_current_user_id() );

				if( $args['user'] )
					$args['user']->filter = 'edit';

			} else {

				if( isset( $_GET['updated'] ) ) {
					bon_error_notice()->add( 'edit_success', __( 'Account details changed successfully.', 'bon' ), 'success' );
				}

				$args['current_user'] = get_user_by( 'id', get_current_user_id() );
				$args['edit_account_url'] = $this->edit_account_url();

			}
		}

		bon_get_template( 'accounts/account.php', $args );

	}


	/**
	 * Process the login form.
	 * @uses wp_verify_nonce()
	 * @uses wp_signon()
	 * @uses wp_safe_redirect()
	 * @uses wp_validate_redirect()
	 * @uses wp_get_referer()
	 * @uses Class WP_Error
	 * @uses Class Exception
	 *
	 */
	public function process_login() {

		if ( ! empty( $_POST['login'] ) && ! empty( $_POST['_wpnonce'] ) ) {

			wp_verify_nonce( $_POST['_wpnonce'], 'bon_login' );

			$creds = array();

			if ( empty( $_POST['username'] ) && empty( $_POST['password'] ) ) {
				bon_error_notice()->add( 'empty_username', __( 'Username is required.', 'bon' ), 'error' );
				bon_error_notice()->add( 'empty_password', __( 'Password is required.', 'bon' ), 'error' );
			}
			
			if ( is_email( $_POST['username'] ) ) {

				$user = get_user_by( 'email', $_POST['username'] );

				if ( isset( $user->user_login ) ) {
					$creds['user_login'] 	= $user->user_login;
				}

			} else {
				$creds['user_login'] 	= $_POST['username'];
			}

			$creds['user_password'] = $_POST['password'];
			$creds['remember']      = isset( $_POST['rememberme'] );
			$secure_cookie          = is_ssl() ? true : false;
			$user 					= wp_signon( $creds, $secure_cookie );
			
			if ( is_wp_error($user) ) {

				bon_error_notice()->add( 'user_error', $user->get_error_message(), 'error-user' );

			} else {

				if ( wp_get_referer() ) {

					$redirect = esc_url( wp_get_referer() );

				} else {

					$redirect = wp_validate_redirect( $_SERVER['REQUEST_URI'] );

				}

				wp_safe_redirect( $redirect );
			}

		}
	}


	public function process_registration() {

		if ( ! get_option( 'users_can_register' ) )
			return;

		if ( ! empty( $_POST['register'] ) ) {

			wp_verify_nonce( $_POST['register'], 'bon_register' );

			$username     = ! empty( $_POST['username'] ) ? sanitize_text_field( $_POST['username'] ) : '';
			$email        = ! empty( $_POST['email'] ) ? sanitize_email( $_POST['email'] ) : '';
			$password     = $_POST['password'];
			$pass_confirm = $_POST['password_confirm'];

			if( username_exists( $username ) ) {
				bon_error_notice()->add( 'username_unavailable', __( 'Username already taken' ), 'error' );
			}
			if( !validate_username( $username ) ) {
				bon_error_notice()->add( 'username_invalid', __( 'Invalid username' ), 'error' );
			}
			if( $username == '' ) {
				bon_error_notice()->add('username_empty', __( 'Please enter a username' ), 'error' );
			}
			if( !is_email( $email ) ) {
				bon_error_notice()->add('email_invalid', __( 'Invalid email' ), 'error' );
			}
			if( email_exists( $email ) ) {
				bon_error_notice()->add('email_used', __( 'Email already registered' ), 'error' );
			}
			if( $password == '' ) {
				bon_error_notice()->add('password_empty', __( 'Please enter a password' ), 'error' );
			}
			if( $password != $pass_confirm ) {
				bon_error_notice()->add('password_mismatch', __( 'Passwords do not match' ), 'error' );
			}

			// Anti-spam trap
			if ( ! empty( $_POST['email_2'] ) ) {
				bon_error_notice()->add( 'anti_spam', __( 'Cheatin&#8217; uh?' ), 'error' );
			}

			$errors = bon_error_notice()->get_error_messages();

			if( empty( $errors ) ) {
				$new_user_id = wp_insert_user( array(
						'user_login'		=> $username,
						'user_pass'	 		=> $password,
						'user_email'		=> $email,
						'user_registered'	=> date('Y-m-d H:i:s'),
						'role'				=> 'subscriber'
				));

				if( $new_user_id ) {
					// send an email to the admin alerting them of the registration
					wp_new_user_notification( $new_user_id, __( 'Your chosen password', 'bon' ) );
					
					// log the new user in
					wp_setcookie( $username, $password, true );
					wp_set_current_user( $new_user_id, $username );	
					do_action( 'wp_login', $username );
					
					// send the newly created user to the home page after logging them in
					wp_redirect( home_url() ); exit;
				}
			}

		}
	}
	

	/**
	 * Handle reset password form
	 */
	public function process_reset_password() {

		if ( ! isset( $_POST['reset_password'] ) ) {
			return;
		}

		// process lost password form
		if ( isset( $_POST['user_login'] ) && isset( $_POST['_wpnonce'] ) ) {

			wp_verify_nonce( $_POST['_wpnonce'], 'bon_lost_password' );

			$this->_retrieve_password();
		}

		// process reset password form
		if ( isset( $_POST['password_1'] ) && isset( $_POST['password_2'] ) && isset( $_POST['reset_key'] ) && isset( $_POST['reset_login'] ) && isset( $_POST['_wpnonce'] ) ) {

			// verify reset key again
			$user = self::_check_password_reset_key( $_POST['reset_key'], $_POST['reset_login'] );

			if ( is_object( $user ) ) {

				// save these values into the form again in case of errors
				$args['key']   = sanitize_text_field( $_POST['reset_key'] );
				$args['login'] = sanitize_text_field( $_POST['reset_login'] );

				wp_verify_nonce( $_POST['_wpnonce'], 'bon_reset_password' );

				if ( empty( $_POST['password_1'] ) || empty( $_POST['password_2'] ) ) {
					bon_error_notice()->add('password_empty', __( 'Please enter your password.', 'bon' ), 'error' );
					$args['form'] = 'reset_password';
				}

				if ( $_POST[ 'password_1' ] !== $_POST[ 'password_2' ] ) {
					bon_error_notice()->add('password_mismatch', __( 'Passwords do not match.', 'bon' ), 'error' );
					$args['form'] = 'reset_password';
				}

				do_action( 'validate_password_reset', $errors, $user );
				
				$errors = bon_error_notice()->get_error_messages();

				if ( empty( $errors ) ) {

					self::_reset_password( $user, $_POST['password_1'] );

					do_action( 'bon_user_reset_password', $user );

					wp_redirect( add_query_arg( 'reset', 'true', remove_query_arg( array( 'key', 'login' ) ) ) );

					exit;
				}
			}

		}
	}

	/**
	 * Handles sending password retrieval email to customer.
	 *
	 * @access public
	 * @uses $wpdb WordPress Database object
	 * @return bool True: when finish. False: on error
	 */
	public function _retrieve_password() {
		global $wpdb;

		if ( empty( $_POST['user_login'] ) ) {
			bon_error_notice()->add('username_empty', __( 'Please enter a username or e-mail address.', 'bon' ), 'error' );

		} elseif ( is_email( $_POST['user_login'] ) ) {

			$user_data = get_user_by( 'email', trim( $_POST['user_login'] ) );

			if ( empty( $user_data ) )
				bon_error_notice()->add('email_invalid', __( 'There is no user registered with that email address.', 'bon' ), 'error' );

		} else {

			$login = trim( $_POST['user_login'] );
			$user_data = get_user_by( 'login', $login );
		}

		do_action('lostpassword_post');

		$errors = bon_error_notice()->get_error_messages();

		if( !empty( $errors ) )
			return false;

		if ( ! $user_data ) {
			bon_error_notice()->add('username_email_invalid', __( 'Invalid username or e-mail.', 'bon' ), 'error' );
			return false;
		}

		// redefining user_login ensures we return the right case in the email
		$user_login = $user_data->user_login;
		$user_email = $user_data->user_email;

		do_action('retrieve_password', $user_login);

		$allow = apply_filters('allow_password_reset', true, $user_data->ID);

		if ( ! $allow ) {
			bon_error_notice()->add('no_password_reset', __( 'Password reset is not allowed for this user' ), 'error' );
			return false;
		} elseif ( is_wp_error( $allow ) ) {
			bon_error_notice()->add('allow_password_reset', $allow->get_error_message(), 'error' );
			return false;
		}

		$key = $wpdb->get_var( $wpdb->prepare( "SELECT user_activation_key FROM $wpdb->users WHERE user_login = %s", $user_login ) );

		if ( empty( $key ) ) {

			// Generate something random for a key...
			$key = wp_generate_password( 20, false );

			do_action('retrieve_password_key', $user_login, $key);

			// Now insert the new md5 key into the db
			$wpdb->update( $wpdb->users, array( 'user_activation_key' => $key ), array( 'user_login' => $user_login ) );
		}

		do_action( 'bon_reset_password_notification', $user_login, $key );

		$message = __('Someone requested that the password be reset for the following account:') . "\r\n\r\n";
		$message .= network_home_url( '/' ) . "\r\n\r\n";
		$message .= sprintf( __( 'Username: %s' ), $user_login ) . "\r\n\r\n";
		$message .= __( 'If this was a mistake, just ignore this email and nothing will happen.' ) . "\r\n\r\n";
		$message .= __( 'To reset your password, visit the following address:' ) . "\r\n\r\n";
		$message .= '<' . trailingslashit( $this->lost_password_url() ) . "?key=$key&login=" . rawurlencode( $user_login ) . ">\r\n";
		//$message .= '<' . network_site_url("wp-login.php?action=rp&key=$key&login=" . rawurlencode($user_login), 'login') . ">\r\n";

		if ( is_multisite() )
			$blogname = $GLOBALS['current_site']->site_name;
		else
			// The blogname option is escaped with esc_html on the way into the database in sanitize_option
			// we want to reverse this for the plain text arena of emails.
			$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

		$title = sprintf( __('[%s] Password Reset'), $blogname );

		/**
		 * Filter the subject of the password reset email.
		 *
		 * @since 2.8.0
		 *
		 * @param string $title Default email title.
		 */
		$title = apply_filters( 'retrieve_password_title', $title );
		/**
		 * Filter the message body of the password reset mail.
		 *
		 * @since 2.8.0
		 *
		 * @param string $message Default mail message.
		 * @param string $key     The activation key.
		 */
		$message = apply_filters( 'retrieve_password_message', $message, $key );

		if ( $message && !wp_mail( $user_email, wp_specialchars_decode( $title ), $message ) ) {

			bon_error_notice()->add( 'send_failed', __('The e-mail could not be sent.') . "<br />\n" . __('Possible reason: your host may have disabled the mail() function.'), 'error' );
			return false;
		}

		bon_error_notice()->add( 'notice', __( 'Check your e-mail for the confirmation link.', 'bon' ), 'notice' );
		return true;
	}

	/**
	 * Retrieves a user row based on password reset key and login
	 *
	 * @uses $wpdb WordPress Database object
	 *
	 * @access public
	 * @param string $key Hash to validate sending user's password
	 * @param string $login The user login
	 * @return object|bool User's database row on success, false for invalid keys
	 */
	public static function _check_password_reset_key( $key, $login ) {

		global $wpdb;

		$key = preg_replace( '/[^a-z0-9]/i', '', $key );

		if ( empty( $key ) || ! is_string( $key ) ) {
			bon_error_notice()->add('invalid_key', __( 'Invalid Key.', 'bon' ), 'error' );
			return false;
		}

		if ( empty( $login ) || ! is_string( $login ) ) {
			bon_error_notice()->add('invalid_key', __( 'Invalid Key.', 'bon' ), 'error' );
			return false;
		}

		$user = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $wpdb->users WHERE user_activation_key = %s AND user_login = %s", $key, $login ) );

		if ( empty( $user ) ) {
			bon_accounts()->error_notice()->add('invalid_key', __( 'Invalid Key.', 'bon' ), 'error' );
			return false;
		}

		return $user;
	}

	/**
	 * Handles resetting the user's password.
	 *
	 * @access public
	 * @param object $user The user
	 * @param string $new_pass New password for the user in plaintext
	 * @return void
	 */
	public static function _reset_password( $user, $new_pass ) {
		do_action( 'password_reset', $user, $new_pass );
		wp_set_password( $new_pass, $user->ID );
		wp_password_change_notification( $user );
	}


	public function filter_contact_method( $user_contact ) {

		/* Add user contact methods */
		$user_contact['skype'] = __( 'Skype Username', 'bon' ); 
		$user_contact['twitter'] = __( 'Twitter URL', 'bon' );
		$user_contact['linkedin'] = __( 'LinkedIn', 'bon' ); 
		$user_contact['google'] = __( 'Google Plus', 'bon' );
		$user_contact['facebook'] = __( 'Facebook URL', 'bon' ); 
		$user_contact['youtube'] = __( 'YouTube URL', 'bon' );
		$user_contact['pinterest'] = __( 'Pinterest URL', 'bon' );
		$user_contact['instagram'] = __( 'Instagram URL', 'bon' );
		$user_contact['dribbble'] = __( 'Dribbble URL', 'bon' );
		$user_contact['github'] = __( 'Github URL', 'bon' );
		$user_contact['wordpress'] = __( 'WordPress URL', 'bon' );

		return $user_contact;
	}

}
/**
 * Returns the main instance of BON_Accounts Class to prevent the need to use globals.
 *
 * @return BON_Accounts
 */
function bon_accounts() {
	return BON_Accounts::instance();
}

// Global for backwards compatibility.
$GLOBALS['bon_accounts'] = bon_accounts();