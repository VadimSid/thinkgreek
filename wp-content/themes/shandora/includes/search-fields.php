<?php
require_once( 'searches/property.php' );
require_once( 'searches/car.php' );
require_once( 'searches/boat.php' );

/**
 * Used to output title field in search panel
 * 
 * @since 1.2
 * @return string
 * @param string $value
 *
 */

function shandora_search_title_field($value = array(), $class, $is_widget = false) {

	$o = apply_atomic('search_title_field', '', $value, $class, $is_widget );

	if( $o != '' ) {
		return $o;
	}

	global $bon;
	$form = $bon->form();

	$o = $form->form_label(__('Title','bon'), 'title');
	$o .= $form->form_input('title', $value['title'], 'placeholder="'.__('Type listing title here','bon').'" class="'.$class.'"');

	return apply_atomic( 'search_title_field_output', $o );
}

?>