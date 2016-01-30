<?php if ( ! defined( 'ABSPATH' ) ) exit('No direct script access allowed'); // Exit if accessed directly

/**
 * Entry Favorite
 *
 *
 * @author      Hermanto Lim
 * @copyright   Copyright (c) Hermanto Lim
 * @link        http://bonfirelab.com
 * @since       Version 1.0
 * @package     BonFramework
 * @category    Extensions
 *
 *
*/ 


if( !class_exists( 'BON_Favorite' ) ) {

	class BON_Favorite {
		/**
		 * @var string
		 */
		public $prefix;

		/**
		 * @var string
		 */
		public $shortcode_tag = 'bon-fav';

		/**
		 * @var string
		 */
		public $metakey = 'bon_favorites';

		/**
		 * @var string
		 */
		public $action = 'bon_fav_action';

		/**
		 * @var BON_Favorite The single instance of the class
		 */
		protected static $_instance = null;

		/*
		 * Class Constructor
		 */
		function __construct() {

			add_action('wp_loaded', array($this, 'load'));
			add_filter('bon_shortcode_lists', array( $this, 'filter_shortcode' ) );

		}

		public static function instance() {
			if ( is_null( self::$_instance ) )
				self::$_instance = new self();
			return self::$_instance;
		}

		function load() {

			if ( isset( $_REQUEST[$this->action] ) ):

		        if ($_REQUEST[$this->action] == 'add') {

		            $this->add_favorite();

		        } else if ( $_REQUEST[$this->action] == 'remove' ) {

		            $this->remove_favorite();

		        } else if ( $_REQUEST[$this->action] == 'clear' ) {

		            if ( $this->clear_favorites()) {
		            	wpfp_die_or_go(wpfp_get_option('cleared'));
		            } else {
		            	wpfp_die_or_go("ERROR");
		            }
		        }

		    endif;

		}


		function add_favorite( $post_id = "" ) {

			if ( !is_user_logged_in() ) {
		        return false;
		    }

		    if ( empty($post_id) ) $post_id = intval( $_REQUEST['postid'] );

		    if ( $this->do_add_to_list( $post_id ) ) {
		        // added, now?
		        do_action( 'bon_fav_after_add', $post_id );

		        $this->update_post_meta($post_id, 1);

		        wp_redirect($_SERVER['HTTP_REFERER']);
		    }

		    else {
		    	return false;
		    }
		}

		function do_add_to_list( $post_id ) {
		    if ( $this->check_favorited( $post_id ) ) {
		        return false;
		    }

		    if ( is_user_logged_in() ) {
		        return $this->add_to_usermeta( $post_id );
		    }
		}

		function check_favorited( $cid ) {
		    if ( is_user_logged_in() ) {
		        $favorite_post_ids = $this->get_user_meta();
		        if ( $favorite_post_ids && is_array( $favorite_post_ids ) ) {
		        	if( in_array($cid, $favorite_post_ids) ) {
		        		return true;
		        	}
		            //foreach ($favorite_post_ids as $fpost_id) {
		                //if ($fpost_id == $cid) return true;
		            //}
		        }
			}

		    return false;
		}

		/* =========================== Shortcode Related ==================== */

		function filter_shortcode( $shortcodes ) {
			$shortcodes[$this->shortcode_tag] = array( $this, 'render_shortcode' );
			return $shortcodes;
		}

		function render_shortcode( $attr ){
			return BON_Shortcodes::render( array( $this, 'shortcode' ), $attr, true );
		}

		function shortcode( $attr ) {

			if ( !is_user_logged_in() ) {
				return '';
			}


			global $post;

			$post_id = $post->ID;

			$output = '';

			if( $this->check_favorited( $post_id ) ) {
				$output .= $this->link( true, 'remove' );
			} else {
				$output .= $this->link( true, 'add' );
			}

			echo $output;
		}


		/* ================== Interface ====================== */

		function link( $return = false, $action = "" ) {
		    global $post;
		    $post_id = &$post->ID;


		    $str = apply_filters( 'bon_fav_link', '', $action );


		    if( !empty( $str ) ) {
		    	if ( $return ) { return $str; } else { echo $str; }
		    }
		    
		    if ( $action == "remove" || $this->check_favorited( $post_id ) == true ) :
		        $str .= $this->link_html( $post_id, __('Remove from Favorite', 'bon'), "remove");
		    else :
		        $str .= $this->link_html( $post_id, __('Add to Favorite', 'bon'), "add");
		    endif;

		    if ( $return ) { 
		    	return $str; 
		    } else { 
		    	echo $str; 
		    }
		}

		function link_html($post_id, $opt, $action) {

			$link = apply_filters( 'bon_fav_link_html', '', $post_id, $opt, $action );

			if( !empty( $link ) ) {
				return $link;
			}


			if( $action == 'add' ) {
				$icon = 'bonicons bi-heart-o';
			} else if(  $action == 'remove' ) {
				$icon = 'bonicons bi-heart';
			}

			$icon = apply_filters('bon_fav_icon', $icon, $action );

		    $link = "<a class='bon-fav-link' href='?".$this->action."=".$action."&amp;postid=". $post_id . "' title='". $opt ."' rel='nofollow'><i class='". $icon ."'></i></a>";

		    return $link;
		}

		/* =========================== User Related ==================== */

		function add_to_usermeta( $post_id ) {
		    $favorites = $this->get_user_meta();
		    $favorites[] = $post_id;
		    $this->update_user_meta($favorites);
		    return true;
		}

		function update_user_meta( $arr ) {
		    return update_user_meta($this->get_user_id(), $this->metakey, $arr);
		}

		function get_user_id() {
		    global $current_user;
		    get_currentuserinfo();
		    return $current_user->ID;
		}

		function get_user_meta( $user = "" ) {
		    if ( !empty($user) ):
		        $userdata = get_user_by( 'login', $user );
		        $user_id = $userdata->ID;
		        return get_user_meta($user_id, $this->metakey, true);
		    else:
		        return get_user_meta( $this->get_user_id(), $this->metakey, true);
		    endif;
		}

		/* =========================== Post Related ==================== */

		function update_post_meta($post_id, $val) {
			$oldval = $this->get_post_meta($post_id);
			if ($val == -1 && $oldval == 0) {
		    	$val = 0;
			} else {
				$val = $oldval + $val;
			}
		    return add_post_meta($post_id, $this->metakey, $val, true) or update_post_meta($post_id, $this->metakey, $val);
		}

		function delete_post_meta($post_id) {
		    return delete_post_meta($post_id, $this->metakey);
		}

		function get_post_meta($post_id) {
		    $val = get_post_meta($post_id, $this->metakey, true);
		    if ($val < 0) $val = 0;
		    return $val;
		}

		function remove_favorite($post_id = "") {

		    if ( !is_user_logged_in() ) {
		        return false;
		    }

		    if ( empty($post_id) ) $post_id = intval( $_REQUEST['postid'] );

		    if ($this->do_remove_favorite($post_id)) {
		        // removed, now?
		        do_action('bon_fav_after_remove', $post_id);
		        $this->update_post_meta($post_id, -1);
		        wp_redirect($_SERVER['HTTP_REFERER']);

		    }
		    else return false;
		}


		function do_remove_favorite($post_id) {
		    if ( !$this->check_favorited($post_id) )
		        return true;

		    $a = true;
		    if (is_user_logged_in()) {
		        $user_favorites = $this->get_user_meta();
		        $user_favorites = array_diff($user_favorites, array($post_id));
		        $user_favorites = array_values($user_favorites);
		        $a = $this->update_user_meta($user_favorites);
		    }

		    return $a;
		}

	}


	function bon_fav_get_users_favorites($user = "") {
	    $favorite_post_ids = array();

	    if (!empty($user)):
	        return bon_fav()->get_user_meta($user);
	    endif;

	    # collect favorites from cookie and if user is logged in from database.
	    if (is_user_logged_in()):
	        $favorite_post_ids = bon_fav()->get_user_meta();
		endif;

	    return $favorite_post_ids;
	}

	function bon_fav_get_users_favorites_by_posttype($user = "", $post_type) {
	    $favorite_post_ids = array();

	    if (!empty($user)):
	        return bon_fav()->get_user_meta($user);
	    endif;

	    # collect favorites from cookie and if user is logged in from database.
	    if (is_user_logged_in()):
	        $favorite_post_ids = bon_fav()->get_user_meta();
		endif;


		if( $favorite_post_ids ) {
			$new_favorite_ids = array();
			foreach( $favorite_post_ids as $favid ) {
				if( get_post_type( $favid ) == $post_type ) {
					$new_favorite_ids[] = $favid;
				}
			}
			return $new_favorite_ids;
		} else {
			return false;
		}

	}


	function bon_fav() {
		return BON_Favorite::instance();
	}

	bon_fav();

}
