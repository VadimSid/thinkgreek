<?php if ( ! defined( 'ABSPATH' ) ) exit('No direct script access allowed'); // Exit if accessed directly
/**
 * Meta Functions
 *
 *
 *
 * @author		Hermanto Lim
 * @copyright	Copyright (c) Hermanto Lim
 * @link		http://bonfirelab.com
 * @since		Version 1.0
 * @package 	BonFramework
 * @category 	Fuctions
 *
 *
*/

function bon_error_notice() {
	static $wp_error; // Will hold global variable safely
	return isset($wp_error) ? $wp_error : ( $wp_error = new WP_Error( null, null, null ) );
}

// displays error messages from form submissions
function bon_show_error( $echo = true ) {

	if( $codes = bon_error_notice()->get_error_codes() ) {

		if( !isset( $echo ) || $echo == null )
			$echo = true;

		$output = '';

		foreach( $codes as $code ) {
			$data = bon_error_notice()->get_error_data( $code );
			$message = bon_error_notice()->get_error_message( $code );
			$error_classes = array( 'bon-wp-error' );
			$error_class = '';
			$error_text = '';
			if( $data ) {
				$error_class = 'bon-message-'.$data;

				if( $data == 'error' )
					$error_text = '<strong>'.__('ERROR','bon').'</strong>: ';
				elseif( $data =='success' )
					$error_text = '<strong>'.__('SUCCESS','bon').'</strong>: ';
				elseif( $data == 'notice' )
					$error_text = '<strong>'.__('NOTICE','bon').'</strong>: ';
				elseif( $data == 'error-user')
					$error_class = 'bon-message-error';
			}

			$error_classes[] = $error_class;
			$error_classes = apply_filters( 'bon_error_classes', $error_classes, $data );
			$error_text = apply_filters( 'bon_error_text', $error_text, $data );

			/* Sanitize and join all classes. */
			$class = join( ' ', array_map( 'sanitize_html_class', array_unique( $error_classes ) ) );

			if( $message ) {
				$output .= '<div class="'.$class.' ">' . $error_text . $message . '</div>';
			}
		}

		if( !empty( $output ) ) {
			if( $echo == true ) {
				echo $output;
			} else {
				return $output;
			}
		}
	}
}

?>