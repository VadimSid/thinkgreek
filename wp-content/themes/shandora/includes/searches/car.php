<?php

/* CAR SEARCH */

function shandora_get_car_search_option($option = 'status') {
	$val = array();

	switch ($option) {

		case 'transmission':

			$val = apply_filters( "shandora_filter_car_transmission", array(
				'automatic' => __('Automatic','bon'),
				'manual' => __('Manual','bon'),
				'semi-auto' => __('Semi Auto','bon'),
				'other' => __('Other','bon'),
			) );
		break;

		case 'status':

			$val = apply_filters( "shandora_filter_car_status", array(
				'new' => __('New','bon'),
				'used' => __('Used','bon'),
				'certified' => __('Certified Pre-Owned','bon'),
				'sold' => __('Sold', 'bon'),
			) );
		break;

		case 'dealer_location':

			$terms = get_terms('dealer-location');
			$val['any'] = __('Any', 'bon');
		    if( $terms ) {
			    foreach($terms as $term) {
			    	$val[$term->slug] = $term->name;
			    }
			}

		break;

		case 'dealer_location1':

			$terms = get_terms('dealer-location', array('parent' => 0));
			$val['any'] = __('Any', 'bon');
		    if( $terms ) {
			    foreach($terms as $term) {
			    	$val[$term->slug] = $term->name;
			    }
			}

		break;

		case 'car_feature':

			$terms = get_terms('car-feature');
			$val['any'] = __('Any', 'bon');
		    if( $terms ) {
			    foreach($terms as $term) {
			    	$val[$term->slug] = $term->name;
			    }
			}

		break;

		case 'body_type':

			$terms = get_terms('body-type');
			$val['any'] = __('Any', 'bon');
		    if( $terms ) {
			    foreach($terms as $term) {
			    	$val[$term->slug] = $term->name;
			    }
			}

		break;

		case 'manufacturer':

			$terms = get_terms('manufacturer');
			$val['any'] = __('Any', 'bon');
			if( $terms ) {
			    foreach($terms as $term) {
			    	$val[$term->slug] = $term->name;
			    }
			}

		break;

		case 'manufacturer1':

			$terms = get_terms('manufacturer', array('parent' => 0));
			$val['any'] = __('Any', 'bon');
		    if( $terms ) {
			    foreach($terms as $term) {
			    	$val[$term->slug] = $term->name;
			    }
			}
		break;


		case 'fuel':

			$val = apply_filters( "shandora_filter_car_fuel", array(
				'gasoline' => __('Gasoline','bon'),
				'e-85' => __('Flex Fuel (E-85)','bon'),
				'gasoline-hybrid' => __('Gasoline Hybrid','bon'),
				'diesel' => __('Diesel', 'bon'),
				'electric' => __('Electric', 'bon'),
				'natural-gas' => __('Compressed Natural Gas', 'bon'),
			) );
			
		break;
	}

	return $val;
}

/**
 * Used to output reg field in search panel
 * 
 * @since 1.0.7
 * @return string
 * @param string $value
 *
 */
function shandora_search_reg_field($value = array(), $class, $is_widget = false) {

	$o = apply_atomic('search_reg_field', '', $value, $class, $is_widget );

	if( $o != '' ) {
		return $o;
	}

	global $bon;
	$form = $bon->form();

	$o = $form->form_label(__('Reg. Number #','bon'), 'reg_number');
	$o .= $form->form_input('reg_number', $value['reg_number'], 'placeholder="'.__('Type Reg. number here','bon').'" class="'.$class.'"');

	return apply_atomic( 'search_reg_field_output', $o );
}

/**
 * Used to output color field in search panel
 * 
 * @since 1.0.7
 * @return string
 * @param string $value
 *
 */
function shandora_search_exterior_color_field($value = array(), $class, $is_widget = false) {

	$o = apply_atomic('search_exterior_color_field', '', $value, $class, $is_widget );

	if( $o != '' ) {
		return $o;
	}

	global $bon;
	$form = $bon->form();

	$o = $form->form_label(__('Ext. Color','bon'), 'exterior_color');
	$o .= $form->form_input('exterior_color', $value['exterior_color'], 'placeholder="'.__('Type exterior color here','bon').'" class="'.$class.'"');

	return apply_atomic( 'search_exteriour_color_field_output', $o );
}

/**
 * Used to output color field in search panel
 * 
 * @since 1.0.7
 * @return string
 * @param string $value
 *
 */
function shandora_search_interior_color_field($value = array(), $class, $is_widget = false) {

	$o = apply_atomic('search_interior_color_field', '', $value, $class, $is_widget );

	if( $o != '' ) {
		return $o;
	}

	global $bon;
	$form = $bon->form();

	$o = $form->form_label(__('Int. Color','bon'), 'interior_color');
	$o .= $form->form_input('interior_color', $value['interior_color'], 'placeholder="'.__('Type interior color here','bon').'"  class="'.$class.'"');

	return apply_atomic( 'search_interior_color_field_output', $o );
}

/**
 * Used to output fuel type field in search panel
 * 
 * @since 1.0.7
 * @return string
 * @param string $value
 *
 */
function shandora_search_fuel_type_field($value = array(), $class, $is_widget = false) {

	$o = apply_atomic('search_fuel_type_field', '', $value, $class, $is_widget );

	if( $o != '' ) {
		return $o;
	}

	global $bon;
	$form = $bon->form();

	//$o = $form->form_label(__('Fuel Type','bon'), 'fuel_type');
	//$o .= $form->form_input('fuel_type', $value['fuel_type'], 'placeholder="'.__('Type fuel type here','bon').'"  class="'.$class.'"');

	$pstat = array(
		'' => __('Any', 'bon')
	);
	$stat = wp_parse_args( shandora_get_car_search_option('fuel') , $pstat );

	$o = $form->form_label(__('Fuel Type','bon'), 'fuel_type');
	$o .= $form->form_dropdown('fuel_type', $stat, $value['fuel_type'], 'class=" '.$class.'"');

	return apply_atomic( 'search_fuel_type_field_output', $o );
}

/**
 * Used to output status field in search panel
 * 
 * @since 1.0.7
 * @return string
 * @param string $value
 *
 */
function shandora_search_car_status_field($value = array(), $class, $is_widget = false) {

	$o = apply_atomic('search_car_status_field', '', $value, $class, $is_widget );

	if( $o != '' ) {
		return $o;
	}

	global $bon;
	$form = $bon->form();

	$pstat = array(
		'any' => __('Any', 'bon')
	);
	$stat = wp_parse_args( shandora_get_car_search_option('status') , $pstat );

	$o = $form->form_label(__('Status','bon'), 'car_status');
	$o .= $form->form_dropdown('car_status', $stat, $value['car_status'], 'class=" '.$class.'"');

	return apply_atomic( 'search_car_status_field_output', $o );
}

/**
 * Used to output car dealer location field in search panel
 * 
 * @since 1.0.7
 * @return string
 * @param string $value
 *
 */
function shandora_search_dealer_location_field($value = array(), $class, $is_widget = false) {

	$o = apply_atomic('search_dealer_location_field', '', $value, $class, $is_widget );

	if( $o != '' ) {
		return $o;
	}

	global $bon;
	$form = $bon->form();

	$o = $form->form_label(__('Dealer Location','bon'), 'dealer_location');
	$o .= $form->form_dropdown('dealer_location', shandora_get_car_search_option('dealer_location'), $value['dealer_location'], 'class=" '.$class.'"');

	return apply_atomic( 'search_dealer_location_field_output', $o );

}

/**
 * Used to output car dealer location field in search panel
 * 
 * @since 1.0.7
 * @return string
 * @param string $value
 *
 */
function shandora_search_body_type_field($value = array(), $class, $is_widget = false) {

	$o = apply_atomic('search_body_type_field', '', $value, $class, $is_widget );

	if( $o != '' ) {
		return $o;
	}

	global $bon;
	$form = $bon->form();

	$o = $form->form_label(__('Body Type','bon'), 'body_type');
	$o .= $form->form_dropdown('body_type', shandora_get_car_search_option('body_type'), $value['body_type'], 'class=" '.$class.'"');

	return apply_atomic( 'search_body_type_field_output', $o );

}

/**
 * Used to output car feature field in search panel
 * 
 * @since 1.0.7
 * @return string
 * @param string $value
 *
 */
function shandora_search_car_feature_field($value = array(), $class, $is_widget = false) {

	$o = apply_atomic('search_car_feature_field', '', $value, $class, $is_widget );

	if( $o != '' ) {
		return $o;
	}

	global $bon;
	$form = $bon->form();

	$o = $form->form_label(__('Feature','bon'), 'car_feature');
	$o .= $form->form_dropdown('car_feature', shandora_get_car_search_option('car_feature'), $value['car_feature'], 'class=" '.$class.'"');

	return apply_atomic( 'search_car_feature_field_output', $o );

}

/**
 * Used to output car manufacturer field in search panel
 * 
 * @since 1.0.7
 * @return string
 * @param string $value
 *
 */
function shandora_search_manufacturer_field($value = array(), $class, $is_widget = false) {

	$o = apply_atomic('search_manufacturer_field', '', $value, $class, $is_widget );

	if( $o != '' ) {
		return $o;
	}

	global $bon;
	$form = $bon->form();

	$o = $form->form_label(__('Manufacturer','bon'), 'manufacturer');
	$o .= $form->form_dropdown('manufacturer', shandora_get_car_search_option('manufacturer'), $value['manufacturer'], 'class=" '.$class.'"');

	return apply_atomic( 'search_manufacturer_field_output', $o );
}

/**
 * Used to output car transmission field in search panel
 * 
 * @since 1.0.7
 * @return string
 * @param string $value
 *
 */
function shandora_search_transmission_field($value = array(), $class) {

	$o = apply_atomic('search_transmission_field', '', $value, $class );

	if( $o != '' ) {
		return $o;
	}

	global $bon;
	$form = $bon->form();

	$pstat = array(
		'any' => __('Any', 'bon')
	);
	$trans = wp_parse_args( shandora_get_car_search_option('transmission') , $pstat );

	$o = $form->form_label(__('Transmission','bon'), 'transmission');
	$o .= $form->form_dropdown('transmission', $trans, $value['transmission'], 'class=" '.$class.'"');

	return apply_atomic( 'search_transmission_field_output', $o );
}

/**
 * Used to output ancap field in search panel
 * 
 * @since 1.0.7
 * @return string
 * @param string $value
 *
 */
function shandora_search_ancap_field($value = array(), $class, $is_widget = false) {

	$o = apply_atomic('search_ancap_field', '', $value, $class, $is_widget );

	if( $o != '' ) {
		return $o;
	}

	global $bon;
	$form = $bon->form();

	
	$ancap_arr = array(  'any'=> __('Any', 'bon')  );
	
	for($i = 1; $i <= 5; $i++) {
		$ancap_arr[$i] = $i;
	}

	
	$id = 'ancap';
	if( $is_widget ) {
		$id = 'ancap_' . $is_widget;
	}

	$o = $form->form_label(__('ANCAP / Safety','bon'), 'ancap');
	$o .= $form->form_dropdown(array( 'name'=> 'ancap', 'id'=> $id ), $ancap_arr, $value['ancap'], 'class="no-custom select-slider '.$class.'"');

	return apply_atomic( 'search_ancap_field_output', $o );
}


/**
 * Used to output price field in search panel
 * 
 * @since 1.0.7
 * @return string
 * @param string $min_val
 * @param string $max_val
 *
 */
function shandora_search_car_price_field($value = array(), $class, $is_widget = false) {

	$o = apply_atomic('search_car_price_field', '', $value, $class, $is_widget );

	if( $o != '' ) {
		return $o;
	}

	global $bon;
	$form = $bon->form();

	$o = shandora_search_price_field($value, $class);

	return apply_atomic( 'search_car_price_field_output', $o );
}

/**
 * Used to output property location field in search panel
 * 
 * @since 1.0.6
 * @return string
 * @param string $value
 *
 */
function shandora_search_dealer_location_level1_field($value = array(), $class, $is_widget = false) {

	$o = apply_atomic('search_dealer_location_level1_field', '', $value, $class, $is_widget );

	if( $o != '' ) {
		return $o;
	}

	global $bon;
	$form = $bon->form();

	$o = $form->form_label( bon_get_option('dealer_location_level1_label'), 'dealer_location_level1');
	$o .= $form->form_dropdown('dealer_location_level1', shandora_get_car_search_option('dealer_location1'), $value['dealer_location_level1'], 'class=" '.$class.'"');

	return apply_atomic( 'search_dealer_location_level1_field_output', $o );

}

/**
 * Used to output property location field in search panel
 * 
 * @since 1.0.6
 * @return string
 * @param string $value
 *
 */
function shandora_search_dealer_location_level2_field($value = array(), $class, $is_widget = false) {

	$o = apply_atomic('search_dealer_location_level2_field', '', $value, $class, $is_widget );

	if( $o != '' ) {
		return $o;
	}

	global $bon;
	$form = $bon->form();

	$loc_opt = array('any' => __('Any','bon'));

	if($value['dealer_location_level1'] != '') {
		$parent = get_term_by('slug', $value['dealer_location_level1'], 'dealer-location');
		if($parent) {
			$terms = get_terms('dealer-location', array('parent' => $parent->term_id));
		    if($terms) {
		    	foreach($terms as $term) {
			    	$loc_opt[$term->slug] = $term->name;
			    }
		    }
		}
	}

	$o = $form->form_label(bon_get_option('dealer_location_level2_label'), 'dealer_location_level2');
	$o .= $form->form_dropdown('dealer_location_level2', $loc_opt, $value['dealer_location_level2'], 'class=" '.$class.'"');

	return apply_atomic( 'search_dealer_location_level2_field_output', $o );

}


/**
 * Used to output property location field in search panel
 * 
 * @since 1.0.6
 * @return string
 * @param string $value
 *
 */
function shandora_search_dealer_location_level3_field($value = array(), $class, $is_widget = false) {

	$o = apply_atomic('search_dealer_location_level3_field', '', $value, $class, $is_widget );

	if( $o != '' ) {
		return $o;
	}

	global $bon;
	$form = $bon->form();

	$loc_opt = array('any' => __('Any','bon'));

	if($value['dealer_location_level2'] != '') {
		$parent = get_term_by('slug', $value['dealer_location_level2'], 'dealer-location');
		if($parent) {
			$terms = get_terms('dealer-location', array('parent' => $parent->term_id));
		    if($terms) {
		    	foreach($terms as $term) {
			    	$loc_opt[$term->slug] = $term->name;
			    }
		    }
		}
	}

	$o = $form->form_label(bon_get_option('dealer_location_level3_label'), 'dealer_location_level3');
	$o .= $form->form_dropdown('dealer_location_level3', $loc_opt, $value['dealer_location_level3'], 'class=" '.$class.'"');

	return apply_atomic( 'search_dealer_location_level3_field_output', $o );

}

/**
 * Used to output car manufacturer field in search panel
 * 
 * @since 1.2.4
 * @return string
 * @param string $value
 *
 */
function shandora_search_manufacturer_level1_field($value = array(), $class, $is_widget = false) {

	$o = apply_atomic('search_manufacturer_level1_field', '', $value, $class, $is_widget );

	if( $o != '' ) {
		return $o;
	}

	global $bon;
	$form = $bon->form();

	$o = $form->form_label(bon_get_option('manufacturer_level1_label'), 'manufacturer_level1');
	$o .= $form->form_dropdown('manufacturer_level1', shandora_get_car_search_option('manufacturer1'), $value['manufacturer_level1'], 'class=" '.$class.'"');

	return apply_atomic( 'search_manufacturer_level1_field_output', $o );

}

/**
 * Used to output property location field in search panel
 * 
 * @since 1.0.6
 * @return string
 * @param string $value
 *
 */
function shandora_search_manufacturer_level2_field($value = array(), $class, $is_widget = false) {

	$o = apply_atomic('search_manufacturer_level2_field', '', $value, $class, $is_widget );

	if( $o != '' ) {
		return $o;
	}

	global $bon;
	$form = $bon->form();

	$loc_opt = array('any' => __('Any','bon'));

	if($value['manufacturer_level1'] != '') {
		$parent = get_term_by('slug', $value['manufacturer_level1'], 'manufacturer');
		if($parent) {
			$terms = get_terms('manufacturer', array('parent' => $parent->term_id, 'hide_empty' => true ) );
		    if($terms) {
		    	foreach($terms as $term) {
			    	$loc_opt[$term->slug] = $term->name;
			    }
		    }
		}
	}

	$o = $form->form_label(bon_get_option('manufacturer_level2_label'), 'manufacturer_level2');
	$o .= $form->form_dropdown('manufacturer_level2', $loc_opt, $value['manufacturer_level2'], 'class=" '.$class.'"');

	return apply_atomic( 'search_manufacturer_level2_field_output', $o );

}


/**
 * Used to output property location field in search panel
 * 
 * @since 1.0.6
 * @return string
 * @param string $value
 *
 */
function shandora_search_manufacturer_level3_field($value = array(), $class, $is_widget = false) {

	$o = apply_atomic('search_manufacturer_level3_field', '', $value, $class, $is_widget );

	if( $o != '' ) {
		return $o;
	}

	global $bon;
	$form = $bon->form();

	$loc_opt = array('any' => __('Any','bon'));

	if($value['manufacturer_level2'] != '') {
		$parent = get_term_by('slug', $value['manufacturer_level2'], 'manufacturer');
		if($parent) {
			$terms = get_terms('manufacturer', array('parent' => $parent->term_id, 'hide_empty' => true) );
		    if($terms) {
		    	foreach($terms as $term) {
			    	$loc_opt[$term->slug] = $term->name;
			    }
		    }
		}
	}

	$o = $form->form_label(bon_get_option('manufacturer_level3_label'), 'manufacturer_level3');
	$o .= $form->form_dropdown('manufacturer_level3', $loc_opt, $value['manufacturer_level3'], 'class=" '.$class.'"');

	return apply_atomic( 'search_manufacturer_level3_field_output', $o );

}

/**
 * Used to output mileage field in search panel
 * 
 * @since 1.0.7
 * @return string
 * @param string $value
 *
 */
function shandora_search_mileage_field($value = array(), $class, $is_widget = false) {

	$o = apply_atomic('search_mileage_field', '', $value, $class, $is_widget );

	if( $o != '' ) {
		return $o;
	}

	global $bon;
	$form = $bon->form();

	$id = 'mileage-slider-range';
	if( $is_widget ) {
		$id = 'mileage-slider-range-'.$is_widget;
	}

	$range_opt = shandora_get_size_range('mileage');

	$slider = '<div class="price-slider-wrapper"><div class="range-slider" data-type="mileage" data-step="'.$range_opt['step'].'" data-min="'.$range_opt['min'].'" data-max="'.$range_opt['max'].'" id="'.$id.'"></div></div>';

	$o = '<label for="mileage">'.__('Mileage', 'bon');
		$o .= '<span class="price-text text-min min_mileage_text"></span>';
		$o .= '<span class="price-text text-max max_mileage_text"></span>';
	$o .= '</label>';

	$o .= $slider;
	$o .= '<div class="row">';
		$o .= '<div class="column large-6"><input class="min_holder_mileage" type="hidden" name="min_mileage" value="'.$value['min_mileage'].'" /></div>';
		$o .= '<div class="column large-6"><input class="max_holder_mileage" type="hidden" name="max_mileage" value="'.$value['max_mileage'].'" /></div>';
		//$o .= '<div class="column large-6">' . $form->form_hidden('min_mileage', $value['min_mileage']) . '</div>';
		//$o .= '<div class="column large-6">' . $form->form_hidden('max_mileage', $value['max_mileage']). '</div>';
	$o .= '</div>';

	return apply_atomic( 'search_mileage_field_output', $o );
}

function shandora_ajax_update_manufacturer_level($slug) {
	$loc_opt = array('any' => __('Any', 'bon'));

	if ( function_exists( 'check_ajax_referer' ) ) {				
		check_ajax_referer( 'search-panel-submit', 'nonce' );
	}

	$slug = $_POST['term_slug'];

	if(!empty($slug)) {
		$parent = get_term_by('slug', $slug, 'manufacturer');
		if($parent) {
			$terms = get_terms('manufacturer', array( 'hide_empty' => true, 'parent' => $parent->term_id));
		    if($terms) {
		    	foreach($terms as $term) {
			    	$loc_opt[$term->slug] = $term->name;
			    }
		    }
		}
	}

	wp_send_json($loc_opt);
}

add_action( 'wp_ajax_manufacturer-level', 'shandora_ajax_update_manufacturer_level' );
add_action( 'wp_ajax_nopriv_manufacturer-level', 'shandora_ajax_update_manufacturer_level' );

function shandora_ajax_update_dealer_location_level($slug) {
	$loc_opt = array('any' => __('Any', 'bon'));

	if ( function_exists( 'check_ajax_referer' ) ) {				
		check_ajax_referer( 'search-panel-submit', 'nonce' );
	}

	$slug = $_POST['term_slug'];

	if(!empty($slug)) {
		$parent = get_term_by('slug', $slug, 'dealer-location');
		if($parent) {
			$terms = get_terms('dealer-location', array( 'hide_empty' => true, 'parent' => $parent->term_id));
		    if($terms) {
		    	foreach($terms as $term) {
			    	$loc_opt[$term->slug] = $term->name;
			    }
		    }
		}
	}

	wp_send_json($loc_opt);
}

add_action( 'wp_ajax_dealer-location-level', 'shandora_ajax_update_dealer_location_level' );
add_action( 'wp_ajax_nopriv_dealer-location-level', 'shandora_ajax_update_dealer_location_level' );

function shandora_search_yearbuilt_field($value = array(), $class, $is_widget = false) {

	$o = apply_atomic('search_yearbuilt_field', '', $value, $class, $is_widget );

	if( $o != '' ) {
		return $o;
	}

	global $bon;
	$form = $bon->form();


	$o = '<label for="property_yearbuilt">'.__('Year Range', 'bon');
    $o .= '</label>';

    $cur_year = absint( date("Y") );
    $year_base = bon_get_option( 'min_year_range', '2011');
    $year_base = intval($year_base);
    for( $i = $year_base; $i <= $cur_year; $i++ ) {
        $options[$i] = $i;
    }

    $options1 = array('' => __('From', 'bon') ) + $options ;
    $options2 = array('' => __('To', 'bon') ) + $options;

    $id = 'min_yearbuilt';
    $id2 = 'max_yearbuilt';
	if( $is_widget ) {
		$id = 'min_yearbuilt_' . $is_widget;
		$id2 = 'max_yearbuilt_' . $is_widget;
	}

    $o .= '<div class="row">';
        $o .= '<div class="column large-6">' . $form->form_dropdown( array( 'name' => 'min_yearbuilt', 'id' => $id ), $options1, $value['min_yearbuilt'], 'class="'.$class.'"') . '</div>';
        $o .= '<div class="column large-6">' . $form->form_dropdown( array( 'name' => 'max_yearbuilt', 'id' => $id2 ), $options2, $value['max_yearbuilt'], 'class="'.$class.'"') . '</div>';
    $o .= '</div>';
    
	//$o = $form->form_label(__('Year Built','bon'), 'yearbuilt');
	//$o .= $form->form_input('yearbuilt', $value['yearbuilt'], 'placeholder="'.__('Type year built here','bon').'" class="'.$class.'"');

	return apply_atomic( 'search_yearbuilt_field_output', $o );
}
?>