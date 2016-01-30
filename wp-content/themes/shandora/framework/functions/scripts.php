<?php if ( ! defined( 'ABSPATH' ) ) exit('No direct script access allowed'); // Exit if accessed directly

/**
 * Script Functions
 *
 *
 *
 * @author      Hermanto Lim
 * @copyright   Copyright (c) Hermanto Lim
 * @link        http://bonfirelab.com
 * @since       Version 1.0
 * @package     BonFramework
 * @category    Fuctions
 *
 *
*/ 

function bon_scripts() {
  if (!is_admin()) {

    $bon_frontend_scripts = apply_filters( 'bon_frontend_scripts', array(
      'modernizr'        => array(
                              'src' => 'frontend/modernizr.js',
                              'version' => '2.6.2',
                              'footer' => false,
                            ),
      'easing'           => array(
                              'src' => 'frontend/jquery.easing.js',
                              'dep' => array( 'jquery' ),
                              'footer' => false,
                            ),
      'fitvids'          => array(
                              'src' => 'frontend/jquery.fitvids.js',
                              'dep' => array( 'jquery' ),
                            ),
      'gallery-carousel' => array(
                              'src' => 'frontend/jquery.fitvids.js',
                              'dep' => array( 'jquery' ),
                              'enqueue' => false,
                            ),
      'selectivizr'      => array(
                              'src' => 'frontend/selectivizr.js',
                            ),

    ));


    if( current_theme_supports( 'bootstrap' ) ) {
       wp_register_script( 'bootstrap', BON_JS . '/frontend/bootstrap.js', array('jquery'), '3.1', true );
       wp_enqueue_script('bootstrap');
    }

    if( current_theme_supports('zurb-foundation') ) {
      $zurb = get_theme_support('zurb-foundation');
      if(isset($zurb[0]) && is_array($zurb[0])) {
        foreach($zurb[0] as $zurb_part) {
          if($zurb_part == 'foundation') {
            wp_register_script( 'zf-'.$zurb_part, BON_THEME_URI . '/assets/js/foundation/'.$zurb_part.'.js', array('jquery'), '5', true );
          }
          else {
            wp_register_script( 'zf-'.$zurb_part, BON_THEME_URI . '/assets/js/foundation/foundation.'.$zurb_part.'.js', array('jquery'), '5', true );
          }
          wp_enqueue_script('zf-'.$zurb_part);
        }
      }
    }

    // comment reply script for threaded comments
    if ( is_singular() AND !is_front_page() AND comments_open() AND (get_option('thread_comments') == 1)) {
      wp_enqueue_script( 'comment-reply' );
      global $post;
    }

    if( current_theme_supports('dynamic-script') ) {
      $ds = get_theme_support('dynamic-script');
      if( isset($ds[0]) && is_array($ds[0]) ) {
        foreach( $ds[0] as $dynamic_array ) {
          if( is_array($dynamic_array) ) {
            $name = (isset($dynamic_array['name'])) ? $dynamic_array['name'] : '';
            $folder = (isset($dynamic_array['folder'])) ? $dynamic_array['folder'] : '';
            $filename = (isset($dynamic_array['filename'])) ? $dynamic_array['filename'] : '';
            $version = (isset($dynamic_array['version'])) ? $dynamic_array['version'] : '';
            $dep = (isset($dynamic_array['dep'])) ? $dynamic_array['dep'] : '';
            $in_footer = (isset($dynamic_array['in_footer'])) ? $dynamic_array['in_footer'] : true;
            $condition = (isset($dynamic_array['condition'])) ? $dynamic_array['condition'] : '';
            if( !empty( $condition ) && is_array( $condition ) ) {
              if( count( $condition['value'] ) < 2 ) {
                if( $condition['value'][0]['key']($condition['value'][0]['param'] ) ) {
                   wp_register_script( 'bon-'.$name, BON_THEME_URI . '/assets/js/' . $folder . $filename . '.js', $dep, $version, $in_footer );
                   wp_enqueue_script( 'bon-'.$name);
                }
              } else {
                  if(isset($condition['operator']) && !empty($condition['operator'])) {
                    switch ($condition['operator']) {
                      case 'AND':
                       if( $condition['value'][0]['key']($condition['value'][0]['param']) && $condition['value'][1]['key']($condition['value'][1]['param']) ) {
                          wp_register_script( 'bon-'.$name, BON_THEME_URI . '/assets/js/' . $folder . $filename . '.js', $dep, $version, $in_footer );
                          wp_enqueue_script( 'bon-'.$name);
                        }
                        break;
                      case 'OR':
                        if( $condition['value'][0]['key']($condition['value'][0]['param']) || $condition['value'][1]['key']($condition['value'][1]['param']) ) {
                          wp_register_script( 'bon-'.$name, BON_THEME_URI . '/assets/js/' . $folder . $filename . '.js', $dep, $version, $in_footer );
                          wp_enqueue_script( 'bon-'.$name);
                        }
                      break;
                    }
                  }
              }
            } else {
              wp_register_script( 'bon-'.$name, BON_THEME_URI . '/assets/js/' . $folder . $filename . '.js', $dep, $version, $in_footer );
              wp_enqueue_script( 'bon-'.$name);
            }
          }
        }
      }
    }

    foreach( $bon_frontend_scripts as $_handle => $_params ) {

      $default_params = array(
          'footer' => true,
          'dep' => array(),
          'version' => '',
          'src' => '',
          'enqueue' => true,
      );

      $_params = wp_parse_args( $_params, $default_params );
      wp_register_script( $_handle, trailingslashit( BON_JS ) . $_params['src'], $_params['dep'], $_params['version'], $_params['footer'] );
      
      if( $_params['enqueue'] === true )
        wp_enqueue_script( $_handle );
    }


    wp_register_script( 'bon-js', BON_THEME_URI . '/assets/js/custom.js', array( 'jquery'), '1.0', true );
    wp_enqueue_script( 'bon-js' );
    wp_localize_script( 'bon-js', 'bon_ajax', apply_filters( 'bon_ajax_param' , array('url' => admin_url('admin-ajax.php'), 'toolkit_url' => plugins_url() . '/bon-toolkit/') ) );
  }
}

// adding the conditional wrapper around ie stylesheet
// source: http://code.garyjones.co.uk/ie-conditional-style-sheets-wordpress/
function bon_ie_conditional( $tag, $handle ) {
    if ( 'bon-ie-only' == $handle )
        $tag = '<!--[if lt IE 9]>' . "\n" . $tag . '<![endif]-->' . "\n";
    return $tag;
}
add_action('wp_enqueue_scripts', 'bon_scripts', 1);

?>