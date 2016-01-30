<?php if ( ! defined( 'ABSPATH' ) ) exit('No direct script access allowed'); // Exit if accessed directly

/**
 * Admin page Class
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
 
if( ! class_exists( 'BON_Admin_Bar' ) ) {

	add_action( "init", "bon_admin_bar_menu_init" );

	function bon_admin_bar_menu_init() {

		global $bon_admin_bar_menu;

		if( !isset( $bon_admin_bar_menu ) ) {
	    	$bon_admin_bar_menu = new BON_Admin_Bar();
		}

	}

	class BON_Admin_Bar {

		function __construct() {

			add_action( 'admin_bar_menu', array( $this, 'add_menu' ), 100 );

			if( !is_admin() ) {
				add_action( 'admin_bar_menu', array( $this, 'add_front_menu' ), 100 );
			} else {
				add_action( 'admin_bar_menu', array( $this, 'add_back_menu'), 100 );
			}

			add_action( 'admin_bar_menu', array( $this, 'remove_wp_logo'), 100 );
			
			do_action( 'bon_admin_bar_menu', $this );

		}

		function remove_wp_logo( $wp_admin_bar ) {
			$wp_admin_bar->remove_node( 'wp-logo' );
		}

		function add_node( $name, $href = '', $parent = '', $custom_meta = array() ) {

			global $wp_admin_bar;

			if (!is_super_admin()
		            || !is_admin_bar_showing()
		            || !is_object($wp_admin_bar)
		            || !function_exists('is_admin_bar_showing')) {
		        return;
		    }

		    // Generate ID based on the current filename and the name supplied.
		    $id = str_replace('.php', '', basename(__FILE__)) . '-' . $name;
		    $id = preg_replace('#[^\w-]#si', '-', $id);
		    $id = strtolower($id);
		    $id = trim($id, '-');

		    $parent = trim($parent);

		    // Generate the ID of the parent.
		    if (!empty($parent)) {
		        $parent = str_replace('.php', '', basename(__FILE__)) . '-' . $parent;
		        $parent = preg_replace('#[^\w-]#si', '-', $parent);
		        $parent = strtolower($parent);
		        $parent = trim($parent, '-');
		    }

		    // links from the current host will open in the current window
		    $site_url = site_url();

		    $meta_default = array();
		    $meta_ext = array( 'target' => '_blank' ); // external links open in new tab/window

		    $meta = (strpos($href, $site_url) !== false) ? $meta_default : $meta_ext;
		    $meta = array_merge($meta, $custom_meta);

		    $wp_admin_bar->add_node(array(
		        'parent' => $parent,
		        'id' => $id,
		        'title' => $name,
		        'href' => $href,
		        'meta' => $meta,
		    ));
		}

		function add_front_menu() {
			do_action('bon_admin_bar_add_front_menu', $this);
		}

		function add_back_menu() {
			do_action('bon_admin_bar_add_back_menu', $this);
		}

		function add_menu() {
			do_action('bon_admin_bar_add_menu', $this);
		}
    }

}