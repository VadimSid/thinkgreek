<?php if ( ! defined( 'ABSPATH' ) ) exit('No direct script access allowed'); // Exit if accessed directly

/**
 * Class BON Front End Editor
 *
 *
 *
 * @author		Hermanto Lim
 * @copyright	Copyright (c) Hermanto Lim
 * @link		http://bonfirelab.com
 * @since		Version 1.0
 * @package 	BonFramework
 * @category 	Extension
 *
 *
*/ 
if( !class_exists( 'BON_Front_End_Editor' ) ) {

	class BON_Front_End_Editor {


		/**
		 * @var BON_Front_End_Editor The single instance of the class
		 */
		protected static $_instance = null;

		/** 
		 * @var array Query vars to add to wp 
		 */
		public $query_vars = array();

		/** 
		 * @var array Query vars to add to wp 
		 */
		public $fee_page;

		public $meta_boxes;


		public static function instance() {
			if ( is_null( self::$_instance ) )
				self::$_instance = new self();
			return self::$_instance;
		}
		
		function __construct() {
			
			global $wp_version;

			if ( empty( $wp_version ) || version_compare( $wp_version, '3.9', '<' ) ) {
				add_action( 'admin_notices', array( $this, 'admin_notices' ) );
				return;
			}

			add_action( 'init', array( $this, 'add_endpoints') );

			if ( ! is_admin() ) {
				add_filter( 'query_vars', array( $this, 'add_query_vars'), 0 );
				add_action( 'parse_request', array( $this, 'parse_request'), 0 );
			}

			add_action( 'init', array( $this, 'init' ) );

			add_filter( 'bon_shortcode_lists', array( $this, 'filter_shortcode' ) );

			if ( isset( $_POST['bon_fee_redirect'] ) && $_POST['bon_fee_redirect'] == '1' )
				add_filter( 'redirect_post_location', array( $this, 'redirect_post_location' ), 10, 2 );

			$this->init_query_vars();

			add_action( 'wp_ajax_get_metabox', array( $this, 'do_ajax') );

			add_action( 'before_bon_fee_post_form', 'bon_show_error' );

		}

		public function the_title( $title ) {
			global $wp;

			if( $this->is_edit() && $this->get_post_to_edit() && is_main_query() && in_the_loop() ) {
				$post = get_post( $wp->query_vars[$this->query_vars['edit-post']] );
				if( $post ) {
					return sprintf( __('Editing %s', 'bon' ),  $post->post_title );
				} else {
					return $title;
				}
				
			}

			return $title;
		}

		public function layout( $layout ) {

			if( $this->is_edit() )
				$layout = '1c';
  			
  			return $layout;
		}
		/**
		 * Init query vars by loading options.
		 */
		public function init_query_vars() {
			// Query vars to add to WP
			$this->query_vars = array(
				// Checkout actions
				'edit-post'    => apply_filters( 'bon_fee_edit_post_endpoint', _x( 'edit-post', 'edit-post-slug', 'bon' ) ),
				'add-post'     => apply_filters( 'bon_fee_add_post_endpoint', _x( 'add-post', 'add-post-slug', 'bon' ) ),
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
		 * Init query vars by loading options.
		 */
		public function set_post_page() {

			$this->fee_page = apply_filters( 'bon_fee_post_page', bon_get_option( 'fee_post_page' ) );
		}

		public function admin_notices() {
			echo '<div class="error"><p><strong>Front-end Editor</strong> only works on WordPress versions 3.9+</p></div>';
		}

		public function init() {

			$this->set_post_page();

			global $wp_post_statuses;

			if ( !is_admin() && !empty( $_GET['trashed'] ) && $_GET['trashed'] === '1' && !empty( $_GET['ids'] ) ) {
				wp_redirect( admin_url( 'edit.php?post_type=' . get_post_type( $_GET['ids'] ) . '&trashed=1&ids=' . $_GET['ids'] ) );
				die;
			}

			// Lets auto-drafts pass as drafts by WP_Query.
			$wp_post_statuses['auto-draft']->protected = true;

			add_action( 'wp', array( $this, 'wp' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ) );

			add_filter( 'theme_mod_theme_layout', array( $this, 'layout' ), 50 );
			add_filter( 'the_title', array( $this, 'the_title' ), 20 );
			add_filter( 'get_edit_post_link', array( $this, 'get_edit_post_link' ), 10, 3 );
			add_filter( 'edit_post_link', array( $this, 'edit_post_link' ), 10, 2 );
			add_filter( 'admin_url', array( $this, 'admin_url' ) );
			add_action( 'template_redirect', array( $this, 'form_handler') );
			

		}


		public function get_post_to_edit() {
			global $wp;
			if( !isset( $wp->query_vars[ $this->query_vars['edit-post'] ] ) )
				return false;

			return $wp->query_vars[ $this->query_vars['edit-post'] ];
		}

		public function wp() {

			if ( ! $this->is_edit() )
				return;

			if ( force_ssl_admin() && ! is_ssl() ) {
				wp_redirect( set_url_scheme( get_permalink( $post->ID ), 'https' ) );
				die();
			}

			require_once( ABSPATH . '/wp-admin/includes/admin.php' );
			require_once( ABSPATH . '/wp-admin/includes/post.php' );
			require_once( ABSPATH . '/wp-admin/includes/meta-boxes.php' );

			add_action( 'wp_head', array( $this, 'wp_head' ) );
			add_action( 'bon_fee_meta_section', array( $this, 'meta_section' ) );
			
			add_action( 'wp_print_footer_scripts', 'wp_auth_check_html' );

			$check_users = get_users( array( 'fields' => 'ID', 'number' => 2 ) );

			if ( count( $check_users ) > 1 )
				add_action( 'wp_print_footer_scripts', '_admin_notice_post_locked' );

			unset( $check_users );

		}

		public function form_handler() {

			if( isset( $_POST['post_ID'] ) ) {

				require_once( ABSPATH . '/wp-admin/includes/post.php' );

				$action = isset( $_POST['action'] ) ? $_POST['action'] : '';

				if ( isset( $_GET['post'] ) )
				 	$post_id = $post_ID = (int) $_GET['post'];
				elseif ( isset( $_POST['post_ID'] ) )
				 	$post_id = $post_ID = (int) $_POST['post_ID'];
				else
				 	$post_id = $post_ID = 0;

				$post = $post_type = $post_type_object = null;

				if ( $post_id )
					$post = get_post( $post_id );

				if ( $post ) {
					$post_type = $post->post_type;
					$post_type_object = get_post_type_object( $post_type );
				}

				if ( isset( $_POST['deletepost'] ) )
					$action = 'delete';
				elseif ( isset($_POST['wp-preview']) && 'dopreview' == $_POST['wp-preview'] )
					$action = 'preview';

				$sendback = wp_get_referer();

				if ( ! $sendback ||
				     strpos( $sendback, 'post.php' ) !== false ||
				     strpos( $sendback, 'post-new.php' ) !== false ) {
					if ( 'attachment' == $post_type ) {
						$sendback = admin_url( 'upload.php' );
					} else {
						$sendback = admin_url( 'edit.php' );
						$sendback .= ( ! empty( $post_type ) ) ? '?post_type=' . $post_type : '';
					}
				} else {
					$sendback = remove_query_arg( array('trashed', 'untrashed', 'deleted', 'ids'), $sendback );
				}


				switch($action) {
					
					case 'postajaxpost':
					case 'post':

						check_admin_referer( 'add-' . $post_type );
						$post_id = 'postajaxpost' == $action ? edit_post() : write_post();
						$this->redirect_post( $post_id );
						exit();
						break;

					case 'editpost':
						wp_verify_nonce('update-post_' . $post_id);

						$post_id = edit_post();

						// Session cookie flag that the post was saved
						if ( isset( $_COOKIE['wp-saving-post-' . $post_id] ) )
							setcookie( 'wp-saving-post-' . $post_id, 'saved' );

						$this->redirect_post( $post_id ); // Send user on their way while we keep working

						exit();
					break;

					case 'trash':
						check_admin_referer('trash-post_' . $post_id);

						if ( ! $post )
							wp_die( __( 'The item you are trying to move to the Trash no longer exists.' ) );

						if ( ! $post_type_object )
							wp_die( __( 'Unknown post type.' ) );

						if ( ! current_user_can( 'delete_post', $post_id ) )
							wp_die( __( 'You are not allowed to move this item to the Trash.' ) );

						if ( $user_id = wp_check_post_lock( $post_id ) ) {
							$user = get_userdata( $user_id );
							wp_die( sprintf( __( 'You cannot move this item to the Trash. %s is currently editing.' ), $user->display_name ) );
						}

						if ( ! wp_trash_post( $post_id ) )
							wp_die( __( 'Error in moving to Trash.' ) );

						wp_redirect( add_query_arg( array('trashed' => 1, 'ids' => $post_id), $sendback ) );
						exit();
					break;

					case 'preview':
						check_admin_referer( 'update-post_' . $post_id );
						$url = post_preview();
						wp_redirect($url);
						exit();
					break;

				} // end switch
			}
		}

		/**
		 * Redirect to previous page.
		 *
		 * @param int $post_id Optional. Post ID.
		 */
		public function redirect_post($post_id = '') {
			if ( isset($_POST['save']) || isset($_POST['publish']) ) {
				$status = get_post_status( $post_id );

				if ( isset( $_POST['publish'] ) ) {
					switch ( $status ) {
						case 'pending':
							$message = 8;
							break;
						case 'future':
							$message = 9;
							break;
						default:
							$message = 6;
					}
				} else {
						$message = 'draft' == $status ? 10 : 1;
				}

				$location = add_query_arg( 'message', $message, $this->edit_link( $post_id ) );
			} elseif ( isset($_POST['addmeta']) && $_POST['addmeta'] ) {
				$location = add_query_arg( 'message', 2, wp_get_referer() );
				$location = explode('#', $location);
				$location = $location[0] . '#postcustom';
			} elseif ( isset($_POST['deletemeta']) && $_POST['deletemeta'] ) {
				$location = add_query_arg( 'message', 3, wp_get_referer() );
				$location = explode('#', $location);
				$location = $location[0] . '#postcustom';
			} else {
				$location = add_query_arg( 'message', 4, get_edit_post_link( $post_id, 'url' ) );
			}

			/**
			 * Filter the post redirect destination URL.
			 *
			 * @since 2.9.0
			 *
			 * @param string $location The destination URL.
			 * @param int    $post_id  The post ID.
			 */
			wp_redirect( apply_filters( 'redirect_post_location', $location, $post_id ) );
			exit;
		}

		public function redirect_post_location( $location, $post_id ) {

			return $location;
		}

		public function get_edit_post_link( $link, $id, $context ) {

			if( empty( $this->fee_page ) || $id == $this->fee_page )
				return $link;

			global $pagenow;

			$post = get_post( $id );

			if ( $post->post_type === 'revision' )
				return add_query_arg( 'redirect', 'front', $link );

			if ( $this->is_edit() )
				return get_permalink( $id );

			if ( post_type_supports( $post->post_type, 'front-end-editor' ) && ( !is_admin() || ( $pagenow === 'revision.php' && isset( $_GET['redirect'] ) && $_GET['redirect'] === 'front' ) ) )
				return $this->edit_link( $id );

			return $link;

		}

		public function edit_post_link( $link, $id ) {

			if( empty( $this->fee_page ) )
				return $link;

			require_once( ABSPATH . '/wp-admin/includes/post.php' );

			if ( $this->is_edit() )
				return '<a class="post-edit-link" href="' . get_permalink( $id ) . '">' . __( 'Cancel' ) . '</a>';

			if ( wp_check_post_lock( $id ) )
				return '<a class="post-edit-link" href="' . $this->edit_link( $id ) . '">' . __( 'LOCKED' ) . '</a>';

			return $link;

		}

		public function admin_url( $url ) {

			global $pagenow;

			if ( $pagenow === 'revision.php' && isset( $_GET['redirect'] ) && $_GET['redirect'] === 'front' )
				return add_query_arg( 'redirect', 'front', $url );

			return $url;

		}

		public function is_edit() {

			global $wp_query;

			$object_id = get_queried_object_id();
			$post_type = get_post_type( $object_id );



			if( isset( $_POST['wp-preview'] ) && $_POST['wp-preview'] === 'dopreview' && isset( $_POST['bon_fee_post_nonce'] ) && wp_verify_nonce( $_POST['bon_fee_post_nonce'], 'bon_fee_post_nonce' ) ) {
				if( !is_singular() ) {
					return false;
				} else {
					return true;
				}
			}

			if ( isset( $wp_query->query_vars[$this->query_vars['edit-post']] ) && $this->fee_page !== $object_id )
				return true;

			return false;
		}

		public function edit_link( $id ) {

			$post = get_post( $id );

			if ( !$post || empty( $this->fee_page ) )
				return;

			$permalink = get_permalink( $this->fee_page );

			if ( strpos( $permalink, '?' ) !== false )
				$link = add_query_arg( array( $this->query_vars['edit-post'] => $id ), '', $permalink );

			if ( trailingslashit( $permalink ) === $permalink )
				$link = trailingslashit( $permalink . trailingslashit( $this->query_vars['edit-post'] ) . $id );

			if ( !isset( $link ) )
				$link = trailingslashit( $permalink ) . trailingslashit( $this->query_vars['edit-post'] ) . $id;

			if ( force_ssl_admin() )
				$link = set_url_scheme( $link, 'https' );

			return $link;

		}

		public function filter_shortcode( $shortcodes ) {
			$shortcodes['bon-fee'] = array( $this, 'render_shortcode' );
			return $shortcodes;
		}

		public function render_shortcode( $attr ){
			return BON_Shortcodes::render( array( $this, 'front_end_editor_shortcodes' ), $attr, true );
		}

		public function wp_head() {
			
			global $wp_locale, $hook_suffix, $current_screen, $wp;

			$object_id = $this->get_post_to_edit();

			if( ! $object_id ) {
				return;
			}

			$post_type = get_post_type( $object_id );

			set_current_screen( $post_type );

			$admin_body_class = preg_replace( '/[^a-z0-9_-]+/i', '-', $hook_suffix );

			?><script type="text/javascript">
			addLoadEvent = function(func){if(typeof jQuery!="undefined")jQuery(document).ready(func);else if(typeof wpOnload!='function'){wpOnload=func;}else{var oldonload=wpOnload;wpOnload=function(){oldonload();func();}}};
			var ajaxurl = '<?php echo admin_url( 'admin-ajax.php' ); ?>',
				pagenow = '<?php echo $current_screen->id; ?>',
				typenow = '<?php echo $current_screen->post_type; ?>',
				adminpage = '<?php echo $admin_body_class; ?>',
				thousandsSeparator = '<?php echo addslashes( $wp_locale->number_format['thousands_sep'] ); ?>',
				decimalPoint = '<?php echo addslashes( $wp_locale->number_format['decimal_point'] ); ?>',
				isRtl = <?php echo (int) is_rtl(); ?>;
			</script><?php
			unset( $GLOBALS['current_screen'] );
		}

		public function wp_enqueue_scripts() {

			global $concatenate_scripts, $compress_scripts;

			$object_id = $this->get_post_to_edit();


			if( !$object_id )
				return;

			if ( ! isset($concatenate_scripts) )
				script_concat_settings();

			$compressed = $compress_scripts && $concatenate_scripts && isset( $_SERVER['HTTP_ACCEPT_ENCODING'] )
				&& false !== stripos( $_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip' );

			$suffix = ( defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ) ? '' : '.min';

			if ( $this->is_edit() ) {
				
				wp_enqueue_style( 'wp-auth-check' );
				wp_enqueue_style( 'bon-fee', trailingslashit( BON_CSS ) . 'frontend/fee.css' , '', null, null );
				wp_enqueue_style( 'dashicons' );
				//wp_enqueue_style( 'wpadmin', admin_url( 'css/wp-admin.css' ) , '', null, null );

				wp_enqueue_style( 'bon-meta-box', BON_CSS . '/metabox.css', '', '1.0', 'all' ); 
				wp_enqueue_script( 'jquery' );
				wp_enqueue_script( 'jquery-ui-selectable' );
				wp_enqueue_script( 'heartbeat' );
				wp_enqueue_script( 'wp-auth-check' );
				wp_enqueue_script( 'autosave' );
				wp_enqueue_script( 'schedule' );
				wp_enqueue_script( 'wp-ajax-response' );
			
				wp_enqueue_script( 'bon-meta-box', BON_JS . '/metabox.js', array( 'jquery', 'jquery-ui-slider', 'jquery-ui-draggable', 'jquery-ui-datepicker'), null, true );
				wp_enqueue_script( 'bon-fee', trailingslashit( BON_JS ) . 'frontend/fee.js', array( 'jquery', 'wp-lists', 'utils', 'suggest', 'heartbeat' ), null, true );

				$vars = array(
					'ok' => __( 'OK' ),
					'cancel' => __( 'Cancel' ),
					'publishOn' => __( 'Publish on:' ),
					'publishOnFuture' =>  __( 'Schedule for:' ),
					'publishOnPast' => __( 'Published on:' ),
					'dateFormat' => __( '%1$s %2$s, %3$s @ %4$s : %5$s' ),
					'showcomm' => __( 'Show more comments' ),
					'endcomm' => __( 'No more comments found.' ),
					'publish' => __( 'Publish' ),
					'schedule' => __( 'Schedule' ),
					'update' => __( 'Update' ),
					'savePending' => __( 'Save as Pending' ),
					'saveDraft' => __( 'Save Draft' ),
					'private' => __( 'Private' ),
					'public' => __( 'Public' ),
					'publicSticky' => __( 'Public, Sticky' ),
					'password' => __( 'Password Protected' ),
					'privatelyPublished' => __( 'Privately Published' ),
					'published' => __( 'Published' ),
					'comma' => _x( ',', 'tag delimiter' )
				);

				wp_localize_script( 'autosave', 'autosaveL10n', array(
					'autosaveInterval' => AUTOSAVE_INTERVAL,
					'blog_id' => get_current_blog_id()
				) );

				wp_localize_script( 'bon-fee', 'autosaveL10n', array(
					'autosaveInterval' => AUTOSAVE_INTERVAL,
					'savingText' => __( 'Saving Draft&#8230;' ),
					'saveAlert' => __( 'The changes you made will be lost if you navigate away from this page.' ),
					'blog_id' => get_current_blog_id()
				) );

				wp_localize_script( 'bon-fee', 'postL10n', $vars );
				
				wp_localize_script( 'wp-link', 'wpLinkL10n', array(
					'title' => __('Insert/edit link'),
					'update' => __('Update'),
					'save' => __('Add Link'),
					'noTitle' => __('(no title)'),
					'noMatchesFound' => __('No matches found.')
				) );

				//wp_enqueue_media( array( 'post' => get_post( $object_id ) ) );

			}

		}


		public function front_end_editor_shortcodes( $attr ) {

			global $wp, $current_screen, $wp_meta_boxes, $post;

			$is_bac = $this->is_bac();
			$output = '';

			/**
			  * Start Checking the Conditional needed to render editor
			  * Define Variable needed for use in whole function
			  *  
			  *
			  */

			if ( !is_user_logged_in() ) {

				if( $is_bac === true ) {
					wp_safe_redirect( bon_accounts()->my_account_url() );
				} else if( is_woocommerce_activated() ) {
					wp_safe_redirect( get_permalink( wc_get_page_id( 'myaccount' ) ) );
				}

			} else { 

				if( !$this->is_edit() ) {
					return;
				}

				$object_id = $this->get_post_to_edit();

				if( !$object_id ) {
					bon_error_notice()->add('invalid_post', __( 'You attempted to edit an item that doesn&#8217;t exist. Perhaps it was deleted?' ), 'error' );
					return;
				}


				$post_object = get_post( $this->get_post_to_edit() );
				setup_postdata( $GLOBALS['post'] =& $post_object );

				$current_post_type = get_post_type( $object_id );
				

				if( !$post_object ) {
					bon_error_notice()->add('invalid_post', __( 'You attempted to edit an item that doesn&#8217;t exist. Perhaps it was deleted?' ), 'error' );
					return;
				}

				if ( !current_user_can( 'edit_post', $object_id ) ) {
					bon_error_notice()->add('permission_denied', __( 'You are not allowed to edit this item.' ), 'error' );
					return;
				}
				
				if ( !post_type_supports( $post_object->post_type, 'front-end-editor' ) ) {
					bon_error_notice()->add( 'unsupported_posttype', __('The post type assigned is not supporting front end post', 'bon'), 'error' );
				}

				$form_extra = '';
				$notice = false;

				if ( $post_object->post_status === 'auto-draft' ) {

					$post_object->post_title = '';
					$post_object->comment_status = get_option( 'default_comment_status' );
					$post_object->ping_status = get_option( 'default_ping_status' );
					$autosave = false;
					$form_extra .= "<input type='hidden' id='auto_draft' name='auto_draft' value='1' />";

				} else {
					$autosave = wp_get_post_autosave( $object_id );
				}

				$form_action = 'editpost';
				$nonce_action = 'update-post_' . $object_id;
				$form_extra .= "<input type='hidden' id='post_ID' name='post_ID' value='" . esc_attr( $object_id ) . "' />";


				$content_css = array(
					trailingslashit( get_stylesheet_directory_uri() ) . 'assets/css/editor-styles.css',
					trailingslashit( includes_url() ) . 'css/dashicons.min.css',
					trailingslashit( includes_url() ) . 'js/mediaelement/mediaelementplayer.min.css',
					trailingslashit( includes_url() ) . 'js/mediaelement/wp-mediaelement.css',
					trailingslashit( includes_url() ) . 'js/tinymce/skins/wordpress/wp-content.css',
					trailingslashit( includes_url() ) . 'css/editor.min.css',
				);

				$content_css = join( ',', array_map( 'esc_url', array_unique( $content_css ) ) );

				$args = array(
					'post_ID' => $object_id,
					'post_type' => $current_post_type,
					'user_ID' => get_current_user_id(),
					'post' => $post_object,
					'post_type_object' => get_post_type_object( $current_post_type ),
					'autosave' => $autosave,
					'form_extra' => $form_extra,
					'form_action' => $form_action,
					'nonce_action' => $nonce_action,
					'editor_settings' => array(
						'dfw' => true,
						'drag_drop_upload' => true,
						'tabfocus_elements' => 'insert-media-button, save-post',
						'editor_height' => 360,
						'tinymce' => array(
							'resize' => false,
							'add_unload_trigger' => false,
							'content_css' => $content_css,
						),
					)
				);

				ob_start();

				bon_get_template( 'posts/editor.php', $args );

				$args['editor'] = ob_get_clean();

				unset( $args['editor_settings']);

				set_current_screen( $current_post_type );

				$current_screen->set_parentage( 'edit.php?post_type='.$current_post_type );
				
				if ( ! wp_check_post_lock( $object_id ) ) {
					$args['active_post_lock'] = wp_set_post_lock( $object_id );
				}
				
				$messages = $this->get_wp_messages( $post_object );

				$message = false;
				if ( isset($_GET['message']) ) {
					$_GET['message'] = absint( $_GET['message'] );
					if ( isset($messages[$current_post_type][$_GET['message']]) )
						$message = $messages[$current_post_type][$_GET['message']];
					elseif ( !isset($messages[$current_post_type]) && isset($messages['post'][$_GET['message']]) )
						$message = $messages['post'][$_GET['message']];
				}

				// Detect if there exists an autosave newer than the post and if that autosave is different than the post
				if ( $autosave && mysql2date( 'U', $autosave->post_modified_gmt, false ) > mysql2date( 'U', $post_object->post_modified_gmt, false ) ) {
					foreach ( _wp_post_revision_fields() as $autosave_field => $_autosave_field ) {
						if ( normalize_whitespace( $autosave->$autosave_field ) != normalize_whitespace( $post_object->$autosave_field ) ) {
							bon_error_notice()->add( 'autosave_exists', sprintf( __( 'There is an autosave of this post that is more recent than the version below. <a href="%s">View the autosave</a>' ), get_edit_post_link( $autosave->ID ) ) , 'notice' );
							break;
						}
					}
					// If this autosave isn't different from the current post, begone.
					if ( ! $notice )
						wp_delete_post_revision( $autosave->ID );

					unset($autosave_field, $_autosave_field);
				}


				bon_get_template( 'posts/post.php', $args );

				unset( $GLOBALS['current_screen'] );

				wp_reset_postdata();

			}

		}

		public function get_wp_messages( $post ) {
			/*
				 * @todo Document the $messages array(s).
				 */
				$messages = array();
				$messages['post'] = array(
					 0 => '', // Unused. Messages start at index 1.
					 1 => sprintf( __('Post updated. <a href="%s">View post</a>'), esc_url( get_permalink($post->ID) ) ),
					 2 => __('Custom field updated.'),
					 3 => __('Custom field deleted.'),
					 4 => __('Post updated.'),
					/* translators: %s: date and time of the revision */
					 5 => isset($_GET['revision']) ? sprintf( __('Post restored to revision from %s'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
					 6 => sprintf( __('Post published. <a href="%s">View post</a>'), esc_url( get_permalink($post->ID) ) ),
					 7 => __('Post saved.'),
					 8 => sprintf( __('Post submitted. <a target="_blank" href="%s">Preview post</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post->ID) ) ) ),
					 9 => sprintf( __('Post scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview post</a>'),
						/* translators: Publish box date format, see http://php.net/date */
						date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink($post->ID) ) ),
					10 => sprintf( __('Post draft updated. <a target="_blank" href="%s">Preview post</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post->ID) ) ) ),
				);
				$messages['page'] = array(
					 0 => '', // Unused. Messages start at index 1.
					 1 => sprintf( __('Page updated. <a href="%s">View page</a>'), esc_url( get_permalink($post->ID) ) ),
					 2 => __('Custom field updated.'),
					 3 => __('Custom field deleted.'),
					 4 => __('Page updated.'),
					 5 => isset($_GET['revision']) ? sprintf( __('Page restored to revision from %s'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
					 6 => sprintf( __('Page published. <a href="%s">View page</a>'), esc_url( get_permalink($post->ID) ) ),
					 7 => __('Page saved.'),
					 8 => sprintf( __('Page submitted. <a target="_blank" href="%s">Preview page</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post->ID) ) ) ),
					 9 => sprintf( __('Page scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview page</a>'), date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink($post->ID) ) ),
					10 => sprintf( __('Page draft updated. <a target="_blank" href="%s">Preview page</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post->ID) ) ) ),
				);

				$messages['attachment'] = array_fill( 1, 10, __( 'Media attachment updated.' ) ); // Hack, for now.

				/**
				 * Filter the post updated messages.
				 *
				 * @since 3.0.0
				 *
				 * @param array $messages Post updated messages. For defaults @see $messages declarations above.
				 */
				$messages = apply_filters( 'post_updated_messages', $messages );

				return $messages;
		}

		public function meta_section() {

			require_once( trailingslashit( BON_CLASSES ) . 'class-bon-machine.php' );

			global $post, $current_screen;
			
			$object_id = $this->get_post_to_edit();
			$post_type = get_post_type( $post );

			$thumbnail_support = current_theme_supports( 'post-thumbnails', $post_type ) && post_type_supports( $post_type, 'thumbnail' );
		
			$publish_callback_args = null;
			if ( post_type_supports($post_type, 'revisions') && 'auto-draft' != $post->post_status ) {
				$revisions = wp_get_post_revisions( $post_ID );

				// We should aim to show the revisions metabox only when there are revisions.
				if ( count( $revisions ) > 1 ) {
					reset( $revisions ); // Reset pointer for key()
					$publish_callback_args = array( 'revisions_count' => count( $revisions ), 'revision_id' => key( $revisions ) );
					add_meta_box('revisionsdiv', __('Revisions'), 'post_revisions_meta_box', null, 'normal', 'core');
				}
			}

			if ( 'attachment' == $post_type ) {
				wp_enqueue_script( 'image-edit' );
				wp_enqueue_style( 'imgareaselect' );
				add_meta_box( 'submitdiv', __('Save'), 'attachment_submit_meta_box', null, 'side', 'core' );
				add_action( 'edit_form_after_title', 'edit_form_image_editor' );

				if ( 0 === strpos( $post->post_mime_type, 'audio/' ) ) {
					add_meta_box( 'attachment-id3', __( 'Metadata' ), 'attachment_id3_data_meta_box', null, 'normal', 'core' );
				}
			} else {
				add_meta_box( 'submitdiv', __( 'Publish' ), 'post_submit_meta_box', null, 'side', 'core', $publish_callback_args );
			}

			if ( current_theme_supports( 'post-formats' ) && post_type_supports( $post_type, 'post-formats' ) )
				add_meta_box( 'formatdiv', _x( 'Format', 'post format' ), 'post_format_meta_box', null, 'side', 'core' );

			// all taxonomies
			foreach ( get_object_taxonomies( $post ) as $tax_name ) {
				$taxonomy = get_taxonomy( $tax_name );
				if ( ! $taxonomy->show_ui || false === $taxonomy->meta_box_cb )
					continue;

				$label = $taxonomy->labels->name;

				if ( ! is_taxonomy_hierarchical( $tax_name ) )
					$tax_meta_box_id = 'tagsdiv-' . $tax_name;
				else
					$tax_meta_box_id = $tax_name . 'div';

				add_meta_box( $tax_meta_box_id, $label, $taxonomy->meta_box_cb, null, 'side', 'core', array( 'taxonomy' => $tax_name ) );
			}

			if ( post_type_supports($post_type, 'page-attributes') )
				add_meta_box('pageparentdiv', 'page' == $post_type ? __('Page Attributes') : __('Attributes'), 'page_attributes_meta_box', null, 'side', 'core');

			if ( $thumbnail_support && current_user_can( 'upload_files' ) )
				add_meta_box('postimagediv', __('Featured Image'), 'post_thumbnail_meta_box', null, 'side', 'low');

			if ( post_type_supports($post_type, 'excerpt') )
				add_meta_box('postexcerpt', __('Excerpt'), 'post_excerpt_meta_box', null, 'normal', 'core');

			if ( post_type_supports($post_type, 'trackbacks') )
				add_meta_box('trackbacksdiv', __('Send Trackbacks'), 'post_trackback_meta_box', null, 'normal', 'core');

			if ( post_type_supports($post_type, 'custom-fields') )
				add_meta_box('postcustom', __('Custom Fields'), 'post_custom_meta_box', null, 'normal', 'core');

			if ( post_type_supports($post_type, 'comments') )
				add_meta_box('commentstatusdiv', __('Discussion'), 'post_comment_status_meta_box', null, 'normal', 'core');

			if ( ( 'publish' == get_post_status( $post ) || 'private' == get_post_status( $post ) ) && post_type_supports($post_type, 'comments') )
				add_meta_box('commentsdiv', __('Comments'), 'post_comment_meta_box', null, 'normal', 'core');

			if ( ! ( 'pending' == get_post_status( $post ) && ! current_user_can( $post_type_object->cap->publish_posts ) ) )
				add_meta_box('slugdiv', __('Slug'), 'post_slug_meta_box', null, 'normal', 'core');

			if ( post_type_supports($post_type, 'author') ) {
				if ( is_super_admin() || current_user_can( $post_type_object->cap->edit_others_posts ) )
					add_meta_box('authordiv', __('Author'), 'post_author_meta_box', null, 'normal', 'core');
			}

			/**
			 * Fires after all built-in meta boxes have been added.
			 *
			 * @since 3.0.0
			 *
			 * @param string  $post_type Post type.
			 * @param WP_Post $post      Post object.
			 */
			do_action( 'add_meta_boxes', $post_type, $post );

			/**
			 * Fires after all built-in meta boxes have been added, contextually for the given post type.
			 *
			 * The dynamic portion of the hook, $post_type, refers to the post type of the post.
			 *
			 * @since 3.0.0
			 *
			 * @param WP_Post $post Post object.
			 */
			do_action( 'add_meta_boxes_' . $post_type, $post );

			/**
			 * Fires after meta boxes have been added.
			 *
			 * Fires once for each of the default meta box contexts: normal, advanced, and side.
			 *
			 * @since 3.0.0
			 *
			 * @param string  $post_type Post type of the post.
			 * @param string  $context   string  Meta box context.
			 * @param WP_Post $post      Post object.
			 */
			do_action( 'do_meta_boxes', $post_type, 'normal', $post );
			/** This action is documented in wp-admin/edit-form-advanced.php */
			do_action( 'do_meta_boxes', $post_type, 'advanced', $post );
			/** This action is documented in wp-admin/edit-form-advanced.php */
			do_action( 'do_meta_boxes', $post_type, 'side', $post );

			/*
			if ( post_type_supports($post_type, 'revisions') && 'auto-draft' != $post->post_status ) {
				$revisions = wp_get_post_revisions( $object_id );
				// We should aim to show the revisions metabox only when there are revisions.
				if ( count( $revisions ) > 1 ) {
					reset( $revisions ); // Reset pointer for key()
					$publish_callback_args = array( 'revisions_count' => count( $revisions ), 'revision_id' => key( $revisions ) );
					$this->add_meta_section('revisionsdiv', __('Revisions'), 'post_revisions_meta_box','advanced', 50 );
				}
			}


			$this->add_meta_section( 'submitdiv', __( 'Publish' ), 'post_submit_meta_box', 'side', 10, $publish_callback_args );

			if ( current_theme_supports( 'post-formats' ) && post_type_supports( $post_type, 'post-formats' ) )
				$this->add_meta_section( 'formatdiv', _x( 'Format', 'post format' ), 'post_format_meta_box', 'side', 10 );

			foreach ( get_object_taxonomies( $post ) as $tax_name ) {
				$taxonomy = get_taxonomy( $tax_name );
				if ( ! $taxonomy->show_ui || false === $taxonomy->meta_box_cb )
					continue;

				$label = $taxonomy->labels->name;
				if ( ! is_taxonomy_hierarchical( $tax_name ) ) {
					$tax_meta_box_id = 'tagsdiv-' . $tax_name;
				} else {
					$tax_meta_box_id = $tax_name . 'div';
				}
				$this->add_meta_section( $tax_meta_box_id, $label, $taxonomy->meta_box_cb, 'side', 20, array( 'taxonomy' => $tax_name ) );
			}

			
			if ( post_type_supports( $post_type, 'page-attributes' ) )
				$this->add_meta_section( 'pageparentdiv', 'page' == $post_type ? __( 'Page Attributes' ) : __( 'Attributes' ), 'page_attributes_meta_box', 'side', 10 );

			if ( post_type_supports( $post_type, 'excerpt' ) )
				$this->add_meta_section( 'postexcerpt', __( 'Excerpt' ), 'post_excerpt_meta_box', 'advanced', 10 );

			if ( post_type_supports( $post_type, 'trackbacks' ) )
				$this->add_meta_section( 'trackbacksdiv', __( 'Send Trackbacks' ), 'post_trackback_meta_box', 'advanced', 20 );

			if ( post_type_supports( $post_type, 'custom-fields' ) && current_user_can( 'edit_others_posts' ) )
				$this->add_meta_section( 'postcustom', __( 'Custom Fields' ), 'post_custom_meta_box', 'advanced', 30 );

			if ( post_type_supports( $post_type, 'comments' ) )
				$this->add_meta_section( 'commentstatusdiv', __( 'Discussion' ), 'post_comment_status_meta_box', 'advanced', 40 );

			if ( $thumbnail_support )
				$this->add_meta_section('postimagediv', __('Featured Image'), 'post_thumbnail_meta_box', 'side', 50 );
			*/

		}

		public function add_meta_section( $id, $title, $callback, $context = 'side' , $priority = 10, $args = null ) {

			global $wp_meta_sections;

			if ( ! isset( $wp_meta_sections ) )
				$wp_meta_sections = array();

			if ( ! isset( $wp_meta_sections[$context] ) )
				$wp_meta_sections[$context] = array();

			foreach ( array_keys( $wp_meta_sections ) as $a_context ) {

				foreach ( array_keys( $wp_meta_sections[$a_context] ) as $a_priority ) {

					if ( ! isset( $wp_meta_sections[$a_context][$a_priority][$id] ) )
						continue;

					if ( false === $wp_meta_sections[$a_context][$a_priority][$id] )
						return;

					if ( $priority != $a_priority || $context != $a_context )
						unset( $wp_meta_sections[$a_context][$a_priority][$id] );

				}

			}

			if ( ! isset( $wp_meta_sections[$context][$priority]) )
				$wp_meta_sections[$context][$priority] = array();

			$wp_meta_sections[$context][$priority][$id] = array(
				'id' => $id,
				'title' => $title,
				'callback' => $callback,
				'args' => $args
			);

		}

		public function handle_post_submit() {

		}

		public function supported_post_types() {

			$post_types = get_post_types( array( 'show_ui' => true, 'public' => true ) );

			foreach( $post_types as $post_type ) {
				if( post_type_supports( $post_type, 'front-end-editor' ) && $post_type != 'attachment' ) {
					$supported_post_types[] = $post_type;
				}
			}

			return $supported_post_types;
		}

		public function check_post_type( $post_type ) {

			if( !in_array( $post_type, $this->supported_post_types() ) ) {
				return false;
			}
		}

		/**
		 * This function check whether bon account is active or not
		 *
		 */
		public function is_bac() {

			if( class_exists( 'BON_Accounts' ) && function_exists( 'bon_accounts' ) ) {
				return true;
			} else {
				return false;
			}

		}


	}


	/**
	 * Returns the main instance of BON_Accounts Class to prevent the need to use globals.
	 *
	 * @return BON_Accounts
	 */
	function bon_fee() {
		return BON_Front_End_Editor::instance();
	}

	// Global for backwards compatibility.
	$GLOBALS['bon_fee'] = bon_fee();
}