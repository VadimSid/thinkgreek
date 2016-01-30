<?php

/* BOAT SEARCH */

/**
 * Returning the boat options based on parameter inputted
 * 
 * @since 2.4
 * @return mixed
 * @param string $value
 *
 */

function shandora_get_boat_search_option( $option = 'status' ) {
	$val = array();

	switch ($option) {

		case 'status':

			$val = apply_filters( "shandora_filter_boat_status", array(
				'new' => __('New','bon'),
				'used' => __('Used','bon'),
				'sold' => __('Sold', 'bon'),
			) );

		break;

		case 'boat_location':

			$terms = get_terms('boat-location');
			$val['any'] = __('Any', 'bon');
		    if( $terms ) {
			    foreach($terms as $term) {
			    	$val[$term->slug] = $term->name;
			    }
			}

		break;

		case 'boat_location1':

			$terms = get_terms('boat-location', array('parent' => 0));
			$val['any'] = __('Any', 'bon');
		    if( $terms ) {
			    foreach($terms as $term) {
			    	$val[$term->slug] = $term->name;
			    }
			}

		break;

		case 'boat_feature':

			$terms = get_terms('boat-feature');
			$val['any'] = __('Any', 'bon');
		    if( $terms ) {
			    foreach($terms as $term) {
			    	$val[$term->slug] = $term->name;
			    }
			}

		break;

		case 'boat_type':

			$terms = get_terms('boat-type');
			$val['any'] = __('Any', 'bon');
		    if( $terms ) {
			    foreach($terms as $term) {
			    	$val[$term->slug] = $term->name;
			    }
			}

		break;

		case 'boat_manufacturer':

			$terms = get_terms('boat-manufacturer');
			$val['any'] = __('Any', 'bon');
			if( $terms ) {
			    foreach($terms as $term) {
			    	$val[$term->slug] = $term->name;
			    }
			}

		break;

		case 'boat_manufacturer1':

			$terms = get_terms('boat-manufacturer', array('parent' => 0));
			$val['any'] = __('Any', 'bon');
		    if( $terms ) {
			    foreach($terms as $term) {
			    	$val[$term->slug] = $term->name;
			    }
			}
		break;


		case 'boat_hull':

			$terms = get_terms('boat-hull');
			$val['any'] = __('Any', 'bon');
			if( $terms ) {
			    foreach($terms as $term) {
			    	$val[$term->slug] = $term->name;
			    }
			}

		break;

		case 'boat_seat':

			$terms = get_terms('boat-seat');
			$val['any'] = __('Any', 'bon');
			if( $terms ) {
			    foreach($terms as $term) {
			    	$val[$term->slug] = $term->name;
			    }
			}

		break;


		case 'boat_engine':

			$terms = get_terms('boat-engine');
			$val['any'] = __('Any', 'bon');
			if( $terms ) {
			    foreach($terms as $term) {
			    	$val[$term->slug] = $term->name;
			    }
			}

		break;

		case 'boat_engine1':

			$terms = get_terms('boat-engine', array('parent' => 0));
			$val['any'] = __('Any', 'bon');
		    if( $terms ) {
			    foreach($terms as $term) {
			    	$val[$term->slug] = $term->name;
			    }
			}
		break;


		case 'fuel':

			$val = apply_filters( "shandora_filter_boat_fuel", array(
				'gasoline' => __('Gasoline','bon'),
				'diesel' => __('Diesel', 'bon'),
				'electric' => __('Electric', 'bon'),
				'others' => __('Others', 'bon'),
			) );
			
		break;

		/*case 'seating':

			$val = apply_filters( "shandora_filter_boat_seating", array(
				'sport' => __('Sport', 'bon'),
				'bench' => __('Bench', 'bon'),
				'dual-bolsters' => __('Dual Bolsters with Flip Up', 'bon'),
				'swivel' => __('Swivel', 'bon'),
			) );

		break;
		*/

		case 'canvas':

			$val = apply_filters( "shandora_filter_boat_canvas", array(
				'top' => __('Top - Bow Cover', 'bon'),
				'bimini-top' => __('Bimini Top', 'bon'),
				'cockpit-cover' => __('Cockpit Cover', 'bon'),
			) );

		break;
                                            
		case 'hull':

			$val = apply_filters( "shandora_filter_boat_hull", array(
				'aluminum' => __('Aluminum','bon'),
				'composite' => __('Composite','bon'),
				'ferro' => __('Ferro Cement','bon'),
				'fiberglass' => __('Fiberglass', 'bon'),
				'hypalon' => __('Hypalon', 'bon'),
				'pvc' => __('PVC', 'bon'),
				'roplene' => __('Roplene', 'bon'),
				'steel' => __('Steel', 'bon'),
				'wood' => __('Wood', 'bon'),
				'other' => __('Other', 'bon'),
			) );
			
		break;
	}

	return $val;
}

/**
 * Used to output boat price field in search panel
 * 
 * @since 2.4
 * @return string
 * @param string $min_val
 * @param string $max_val
 *
 */
function shandora_search_boat_price_field($value = array(), $class, $is_widget = false) {

	$o = apply_atomic('search_boat_price_field', '', $value, $class, $is_widget );

	if( $o != '' ) {
		return $o;
	}

	global $bon;
	$form = $bon->form();

	$o = shandora_search_price_field($value, $class);

	return apply_atomic( 'search_boat_price_field_output', $o );
}

/**
 * Used to output boat reg field in search panel
 * 
 * @since 2.4
 * @return string
 * @param string $value
 *
 */
function shandora_search_boat_reg_field($value = array(), $class, $is_widget = false) {

	$o = apply_atomic('search_boat_reg_field', '', $value, $class, $is_widget );

	if( $o != '' ) {
		return $o;
	}

	$o = shandora_search_reg_field($value, $class);

	return apply_atomic( 'search_boat_reg_field_output', $o );
}

/**
 * Used to output boat status field in search panel
 * 
 * @since 2.4
 * @return string
 * @param string $value
 *
 */
function shandora_search_boat_status_field($value = array(), $class, $is_widget = false) {

	$o = apply_atomic('search_boat_status_field', '', $value, $class, $is_widget );

	if( $o != '' ) {
		return $o;
	}

	global $bon;
	$form = $bon->form();

	$pstat = array(
		'any' => __('Any', 'bon')
	);
	$stat = wp_parse_args( shandora_get_boat_search_option('status') , $pstat );

	$o = $form->form_label(__('Status','bon'), 'boat_status');
	$o .= $form->form_dropdown('boat_status', $stat, $value['boat_status'], 'class=" '.$class.'"');

	return apply_atomic( 'search_boat_status_field_output', $o );
}

/**
 * Used to output boat location field in search panel
 * 
 * @since 2.4
 * @return string
 * @param string $value
 *
 */
function shandora_search_boat_location_field($value = array(), $class, $is_widget = false) {

	$o = apply_atomic('search_boat_location_field', '', $value, $class, $is_widget );

	if( $o != '' ) {
		return $o;
	}

	global $bon;
	$form = $bon->form();

	$o = $form->form_label(__('Location','bon'), 'boat_location');
	$o .= $form->form_dropdown('boat_location', shandora_get_boat_search_option('boat_location'), $value['boat_location'], 'class=" '.$class.'"');

	return apply_atomic( 'search_boat_location_field_output', $o );

}

/**
 * Used to output boat hull field in search panel
 * 
 * @since 2.4
 * @return string
 * @param string $value
 *
 */
function shandora_search_boat_hull_field($value = array(), $class, $is_widget = false) {

	$o = apply_atomic('search_boat_hull_field', '', $value, $class, $is_widget );

	if( $o != '' ) {
		return $o;
	}

	global $bon;
	$form = $bon->form();

	$o = $form->form_label(__('Hull Material','bon'), 'boat_hull');
	$o .= $form->form_dropdown('boat_hull', shandora_get_boat_search_option('boat_hull'), $value['boat_hull'], 'class=" '.$class.'"');

	return apply_atomic( 'search_boat_hull_field_output', $o );

}

/**
 * Used to output boat status field in search panel
 * 
 * @since 2.4
 * @return string
 * @param string $value
 *
 */
function shandora_search_boat_fuel_field($value = array(), $class, $is_widget = false) {

	$o = apply_atomic('search_boat_fuel_field', '', $value, $class, $is_widget );

	if( $o != '' ) {
		return $o;
	}

	global $bon;
	$form = $bon->form();

	$pstat = array(
		'any' => __('Any', 'bon')
	);
	$stat = wp_parse_args( shandora_get_boat_search_option('fuel') , $pstat );

	$o = $form->form_label(__('Fuel','bon'), 'boat_fuel');
	$o .= $form->form_dropdown('boat_fuel', $stat, $value['boat_fuel'], 'class=" '.$class.'"');

	return apply_atomic( 'search_boat_fuel_field_output', $o );
}

function shandora_search_boat_yearbuilt_field($value = array(), $class, $is_widget = false) {

	$o = apply_atomic('search_boat_yearbuilt_field', '', $value, $class, $is_widget );

	if( $o != '' ) {
		return $o;
	}

	$o = shandora_search_yearbuilt_field($value, $class);
    
	return apply_atomic( 'search_boat_yearbuilt_field_output', $o );
}


/**
 * Used to output boat feature field in search panel
 * 
 * @since 2.4
 * @return string
 * @param string $value
 *
 */
function shandora_search_boat_feature_field($value = array(), $class, $is_widget = false) {

	$o = apply_atomic('search_boat_feature_field', '', $value, $class, $is_widget );

	if( $o != '' ) {
		return $o;
	}

	global $bon;
	$form = $bon->form();

	$o = $form->form_label(__('Feature','bon'), 'boat_feature');
	$o .= $form->form_dropdown('boat_feature', shandora_get_boat_search_option('boat_feature'), $value['boat_feature'], 'class=" '.$class.'"');

	return apply_atomic( 'search_boat_feature_field_output', $o );
}

/**
 * Used to output boat make / boat manufacturer level 1 field in search panel
 * 
 * @since 2.4
 * @return string
 * @param string $value
 *
 */
function shandora_search_boat_make_field($value = array(), $class, $is_widget = false) {

	$o = apply_atomic('search_boat_make_field', '', $value, $class, $is_widget );

	if( $o != '' ) {
		return $o;
	}

	global $bon;
	$form = $bon->form();

	$o = $form->form_label(bon_get_option('boat_make_label'), 'boat_make');
	$o .= $form->form_dropdown('boat_make', shandora_get_boat_search_option('boat_manufacturer1'), $value['boat_make'], 'class=" '.$class.'"');

	return apply_atomic( 'search_boat_make_field_output', $o );

}

/**
 * Used to output boat model / boat manufacturer level 2 field in search panel
 * 
 * @since 2.4
 * @return string
 * @param string $value
 *
 */
function shandora_search_boat_model_field($value = array(), $class, $is_widget = false) {

	$o = apply_atomic('search_boat_model_field', '', $value, $class, $is_widget );

	if( $o != '' ) {
		return $o;
	}

	global $bon;
	$form = $bon->form();

	$loc_opt = array('any' => __('Any','bon'));

	if($value['boat_make'] != '') {
		$parent = get_term_by('slug', $value['boat_make'], 'boat-manufacturer');
		if($parent) {
			$terms = get_terms('boat-manufacturer', array('parent' => $parent->term_id, 'hide_empty' => true ) );
		    if($terms) {
		    	foreach($terms as $term) {
			    	$loc_opt[$term->slug] = $term->name;
			    }
		    }
		}
	}

	$o = $form->form_label(bon_get_option('boat_model_label'), 'boat_model');
	$o .= $form->form_dropdown('boat_model', $loc_opt, $value['boat_model'], 'class=" '.$class.'"');

	return apply_atomic( 'search_boat_model_field_output', $o );

}


/**
 * Used to output boat submodel / boat manufacturer level 3 field in search panel
 * 
 * @since 2.4
 * @return string
 * @param string $value
 *
 */
function shandora_search_boat_submodel_field($value = array(), $class, $is_widget = false) {

	$o = apply_atomic('search_boat_submodel_field', '', $value, $class, $is_widget );

	if( $o != '' ) {
		return $o;
	}

	global $bon;
	$form = $bon->form();

	$loc_opt = array('any' => __('Any','bon'));

	if($value['boat_model'] != '') {
		$parent = get_term_by('slug', $value['boat_model'], 'boat-manufacturer');
		if($parent) {
			$terms = get_terms('boat-manufacturer', array('parent' => $parent->term_id, 'hide_empty' => true) );
		    if($terms) {
		    	foreach($terms as $term) {
			    	$loc_opt[$term->slug] = $term->name;
			    }
		    }
		}
	}

	$o = $form->form_label(bon_get_option('boat_submodel_label'), 'boat_submodel');
	$o .= $form->form_dropdown('boat_submodel', $loc_opt, $value['boat_submodel'], 'class=" '.$class.'"');

	return apply_atomic( 'search_boat_submodel_field_output', $o );

}



/**
 * Used to output boat engine make / engine level 1 field in search panel
 * 
 * @since 2.4
 * @return string
 * @param string $value
 *
 */
function shandora_search_boat_engine_make_field($value = array(), $class, $is_widget = false) {

	$o = apply_atomic('search_boat_engine_make_field', '', $value, $class, $is_widget );

	if( $o != '' ) {
		return $o;
	}

	global $bon;
	$form = $bon->form();

	$o = $form->form_label(bon_get_option('boat_engine_make_label'), 'boat_engine_make');
	$o .= $form->form_dropdown('boat_engine_make', shandora_get_boat_search_option('boat_engine1'), $value['boat_engine_make'], 'class=" '.$class.'"');

	return apply_atomic( 'search_boat_engine_make_field_output', $o );

}

/**
 * Used to output boat engine model / boat engine level 2 field in search panel
 * 
 * @since 2.4
 * @return string
 * @param string $value
 *
 */
function shandora_search_boat_engine_model_field($value = array(), $class, $is_widget = false) {

	$o = apply_atomic('search_boat_engine_model_field', '', $value, $class, $is_widget );

	if( $o != '' ) {
		return $o;
	}

	global $bon;
	$form = $bon->form();

	$loc_opt = array('any' => __('Any','bon'));

	if($value['boat_engine_make'] != '') {
		$parent = get_term_by('slug', $value['boat_engine_make'], 'boat-engine');
		if($parent) {
			$terms = get_terms('boat-engine', array('parent' => $parent->term_id, 'hide_empty' => true ) );
		    if($terms) {
		    	foreach($terms as $term) {
			    	$loc_opt[$term->slug] = $term->name;
			    }
		    }
		}
	}

	$o = $form->form_label(bon_get_option('boat_engine_model_label'), 'boat_engine_model');
	$o .= $form->form_dropdown('boat_engine_model', $loc_opt, $value['boat_engine_model'], 'class=" '.$class.'"');

	return apply_atomic( 'search_boat_engine_model_field_output', $o );

}


/**
 * Used to output boat engine submodel / boat engine level 3 field in search panel
 * 
 * @since 2.4
 * @return string
 * @param string $value
 *
 */
function shandora_search_boat_engine_submodel_field($value = array(), $class, $is_widget = false) {

	$o = apply_atomic('search_boat_engine_submodel_field', '', $value, $class, $is_widget );

	if( $o != '' ) {
		return $o;
	}

	global $bon;
	$form = $bon->form();

	$loc_opt = array('any' => __('Any','bon'));

	if($value['boat_engine_model'] != '') {
		$parent = get_term_by('slug', $value['boat_engine_model'], 'boat-engine');
		if($parent) {
			$terms = get_terms('boat-engine', array('parent' => $parent->term_id, 'hide_empty' => true) );
		    if($terms) {
		    	foreach($terms as $term) {
			    	$loc_opt[$term->slug] = $term->name;
			    }
		    }
		}
	}

	$o = $form->form_label(bon_get_option('boat_engine_submodel_label'), 'boat_engine_submodel');
	$o .= $form->form_dropdown('boat_engine_submodel', $loc_opt, $value['boat_engine_submodel'], 'class=" '.$class.'"');

	return apply_atomic( 'search_boat_engine_submodel_field_output', $o );

}


/**
 * Used to output boat location level 1 field in search panel
 * 
 * @since 1.2.4
 * @return string
 * @param string $value
 *
 */
function shandora_search_boat_location_level1_field($value = array(), $class, $is_widget = false) {

	$o = apply_atomic('search_boat_location_level1_field', '', $value, $class, $is_widget );

	if( $o != '' ) {
		return $o;
	}

	global $bon;
	$form = $bon->form();

	$o = $form->form_label(bon_get_option('boat_location_level1_label'), 'boat_location_level1');
	$o .= $form->form_dropdown('boat_location_level1', shandora_get_boat_search_option('boat_location1'), $value['boat_location_level1'], 'class=" '.$class.'"');

	return apply_atomic( 'search_boat_location_level1_field_output', $o );

}

/**
 * Used to output boat location level 2 field in search panel
 * 
 * @since 2.4
 * @return string
 * @param string $value
 *
 */
function shandora_search_boat_location_level2_field($value = array(), $class, $is_widget = false) {

	$o = apply_atomic('search_boat_location_level2_field', '', $value, $class, $is_widget );

	if( $o != '' ) {
		return $o;
	}

	global $bon;
	$form = $bon->form();

	$loc_opt = array('any' => __('Any','bon'));

	if($value['boat_location_level1'] != '') {
		$parent = get_term_by('slug', $value['boat_location_level1'], 'boat-location');
		if($parent) {
			$terms = get_terms('boat-location', array('parent' => $parent->term_id, 'hide_empty' => true ) );
		    if($terms) {
		    	foreach($terms as $term) {
			    	$loc_opt[$term->slug] = $term->name;
			    }
		    }
		}
	}

	$o = $form->form_label(bon_get_option('boat_location_level2_label'), 'boat_location_level2');
	$o .= $form->form_dropdown('boat_location_level2', $loc_opt, $value['boat_location_level2'], 'class=" '.$class.'"');

	return apply_atomic( 'search_boat_location_level2_field_output', $o );

}


/**
 * Used to output boat location level 3 field in search panel
 * 
 * @since 2.4
 * @return string
 * @param string $value
 *
 */
function shandora_search_boat_location_level3_field($value = array(), $class, $is_widget = false) {

	$o = apply_atomic('search_boat_location_level3_field', '', $value, $class, $is_widget );

	if( $o != '' ) {
		return $o;
	}

	global $bon;
	$form = $bon->form();

	$loc_opt = array('any' => __('Any','bon'));

	if($value['boat_location_level2'] != '') {
		$parent = get_term_by('slug', $value['boat_location_level2'], 'boat-location');
		if($parent) {
			$terms = get_terms('boat-location', array('parent' => $parent->term_id, 'hide_empty' => true) );
		    if($terms) {
		    	foreach($terms as $term) {
			    	$loc_opt[$term->slug] = $term->name;
			    }
		    }
		}
	}

	$o = $form->form_label(bon_get_option('boat_location_level3_label'), 'boat_location_level3');
	$o .= $form->form_dropdown('boat_location_level3', $loc_opt, $value['boat_location_level3'], 'class=" '.$class.'"');

	return apply_atomic( 'search_boat_location_level3_field_output', $o );

}

/**
 * Used to update ajax request callback for boat manufacturer
 * 
 * @since 2.4
 * @return string
 * @param string $value
 *
 */
function shandora_ajax_update_boat_manufacturer_level($slug) {
	$loc_opt = array('any' => __('Any', 'bon'));

	if ( function_exists( 'check_ajax_referer' ) ) {				
		check_ajax_referer( 'search-panel-submit', 'nonce' );
	}

	$slug = $_POST['term_slug'];

	if(!empty($slug)) {
		$parent = get_term_by('slug', $slug, 'boat-manufacturer');
		if($parent) {
			$terms = get_terms('boat-manufacturer', array( 'hide_empty' => true, 'parent' => $parent->term_id));
		    if($terms) {
		    	foreach($terms as $term) {
			    	$loc_opt[$term->slug] = $term->name;
			    }
		    }
		}
	}

	wp_send_json($loc_opt);
}

add_action( 'wp_ajax_boat-manufacturer-level', 'shandora_ajax_update_boat_manufacturer_level' );
add_action( 'wp_ajax_nopriv_boat-manufacturer-level', 'shandora_ajax_update_boat_manufacturer_level' );


/**
 * Used to update ajax request callback for boat location
 * 
 * @since 2.4
 * @return string
 * @param string $value
 *
 */
function shandora_ajax_update_boat_location_level($slug) {
	$loc_opt = array('any' => __('Any', 'bon'));

	if ( function_exists( 'check_ajax_referer' ) ) {				
		check_ajax_referer( 'search-panel-submit', 'nonce' );
	}

	$slug = $_POST['term_slug'];

	if(!empty($slug)) {
		$parent = get_term_by('slug', $slug, 'boat-location');
		if($parent) {
			$terms = get_terms('boat-location', array( 'hide_empty' => true, 'parent' => $parent->term_id));
		    if($terms) {
		    	foreach($terms as $term) {
			    	$loc_opt[$term->slug] = $term->name;
			    }
		    }
		}
	}

	wp_send_json($loc_opt);
}

add_action( 'wp_ajax_boat-location-level', 'shandora_ajax_update_boat_location_level' );
add_action( 'wp_ajax_nopriv_boat-location-level', 'shandora_ajax_update_boat_location_level' );


/**
 * Used to update ajax request callback for boat engine
 * 
 * @since 2.4
 * @return string
 * @param string $value
 *
 */
function shandora_ajax_update_boat_engine_level($slug) {
	$loc_opt = array('any' => __('Any', 'bon'));

	if ( function_exists( 'check_ajax_referer' ) ) {				
		check_ajax_referer( 'search-panel-submit', 'nonce' );
	}

	$slug = $_POST['term_slug'];

	if(!empty($slug)) {
		$parent = get_term_by('slug', $slug, 'boat-engine');
		if($parent) {
			$terms = get_terms('boat-engine', array( 'hide_empty' => true, 'parent' => $parent->term_id));
		    if($terms) {
		    	foreach($terms as $term) {
			    	$loc_opt[$term->slug] = $term->name;
			    }
		    }
		}
	}

	wp_send_json($loc_opt);
}

add_action( 'wp_ajax_boat-engine-level', 'shandora_ajax_update_boat_engine_level' );
add_action( 'wp_ajax_nopriv_boat-engine-level', 'shandora_ajax_update_boat_engine_level' );
?>