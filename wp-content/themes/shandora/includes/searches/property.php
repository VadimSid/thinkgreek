<?php

/* PROPERTY SEARCH */



/**
 * Returning the property options based on parameter inputted
 * 
 * @since 1.0.6
 * @return mixed
 * @param string $value
 *
 */
function shandora_get_search_option($option = 'status') {

	$val = array();

	switch ($option) {

		case 'status':

			$val = apply_filters( "shandora_filter_property_status", array(
					'none' => __('None', 'bon'),
					'for-rent' => __('For Rent', 'bon'),
					'for-sale' => __('For Sale', 'bon'),
					'reduced' => __('Reduced', 'bon'),
					'new' => __('New', 'bon'),
					'sold' => __('Sold', 'bon'),
					'rented' => __('Rented', 'bon'),
					'on-show' => __('On Show', 'bon')
				) );

			break;

		case 'type':

			$terms = get_terms('property-type');
			$val['any'] = __('Any', 'bon');
		    if( $terms ) {
			    foreach($terms as $term) {
			    	$val[$term->slug] = $term->name;
			    }
			}

		break;

		case 'location':

			$terms = get_terms('property-location');
			$val['any'] = __('Any', 'bon');
		    if( $terms ) {
			    foreach($terms as $term) {
			    	$val[$term->slug] = $term->name;
			    }
			}

		break;

		case 'location1':

			$terms = get_terms('property-location', array('parent' => 0));
			$val['any'] = __('Any', 'bon');
		    if( $terms ) {
			    foreach($terms as $term) {
			    	$val[$term->slug] = $term->name;
			    }
			}

		break;

		case 'feature':

			$terms = get_terms('property-feature');
			$val['any'] = __('Any', 'bon');
		    if( $terms ) {
			    foreach($terms as $term) {
			    	$val[$term->slug] = $term->name;
			    }
			}

		break;

		case 'furnishing':

			$val =  apply_filters( "shandora_filter_property_furnish", array(
						'any' => __('Any','bon'),
						'unfurnished' => __('Unfurnished', 'bon'),
						'furnished' => __('Furnished', 'bon'),
					) );

		break;

		case 'mortgage':

			$val = apply_filters( "shandora_filter_property_mortgage", array(
				'any' => __('Any','bon'),
				'mortgage' => __('Mortgage', 'bon'),
				'nomortgage' => __('No Mortgage', 'bon'),
			) );

		break;

		case 'agent':
			$val['any'] = __('Any','bon');
			$posts = get_posts( array( 'post_type' => 'agent', 'posts_per_page' => 50, 'orderby' => 'name', 'order' => 'ASC' ) );
			foreach ( $posts as $item )
				$val[$item->ID] = $item->post_title;
					

		break;	

		case 'period':

			$val = array(
				'per-month' => __('Per Month', 'bon'),
				'per-year' => __('Per Year', 'bon'),
				'per-week' => __('Per Week', 'bon'),
				'per-day' => __('Per Day', 'bon'),
			);

		break;
		
	}

	return $val;
}

/**
 * Used to output property feature field in search panel
 * 
 * @since 1.0.6
 * @return string
 * @param string $value
 *
 */
function shandora_search_feature_field($value = array(), $class, $is_widget = false) {

	$o = apply_atomic('search_feature_field', '', $value, $class, $is_widget );

	if( $o != '' ) {
		return $o;
	}

	global $bon;
	$form = $bon->form();

	$o = $form->form_label(__('Feature','bon'), 'property_feature');
	$o .= $form->form_dropdown('property_feature', shandora_get_search_option('feature'), $value['property_feature'], 'class=" '.$class.'"');

	return apply_atomic( 'search_feature_field_output', $o );
}

/**
 * Used to output lot size field in search panel
 * 
 * @since 1.0.6
 * @return string
 * @param string $value
 *
 */
function shandora_search_lotsize_field($value = array(), $class, $is_widget = false) {

	$o = apply_atomic('search_lotsize_field', '', $value, $class, $is_widget );

	if( $o != '' ) {
		return $o;
	}

	global $bon;
	$form = $bon->form();

	$id = 'lotsize-slider-range';
	if( $is_widget ) {
		$id = 'lotsize-slider-range-'.$is_widget;
	}


	$range_opt = shandora_get_size_range('lotsize');

	$slider = '<div class="price-slider-wrapper"><div class="range-slider" data-type="lotsize" data-step="'.$range_opt['step'].'" data-min="'.$range_opt['min'].'" data-max="'.$range_opt['max'].'" id="'.$id.'"></div></div>';

	$o = '<label for="property_lotsize">'.__('Lot Size', 'bon');
		$o .= '<span class="price-text text-min min_lotsize_text"></span>';
		$o .= '<span class="price-text text-max max_lotsize_text"></span>';
	$o .= '</label>';

	$o .= $slider;

	
	$o .= '<div class="row">';
		$o .= '<div class="column large-6"><input class="min_holder_lotsize" type="hidden" name="min_lotsize" value="'.$value['min_lotsize'].'" /></div>';
		$o .= '<div class="column large-6"><input class="max_holder_lotsize" type="hidden" name="max_lotsize" value="'.$value['max_lotsize'].'" /></div>';
		//$o .= '<div class="column large-6">' . $form->form_hidden('min_lotsize', $value['min_lotsize']) . '</div>';
		//$o .= '<div class="column large-6">' . $form->form_hidden('max_lotsize', $value['max_lotsize']). '</div>';
	$o .= '</div>';

	return apply_atomic( 'search_lotsize_field_output', $o );
}

/**
 * Used to output building size field in search panel
 * 
 * @since 1.0.6
 * @return string
 * @param string $value
 *
 */
function shandora_search_buildingsize_field($value = array(), $class, $is_widget = false) {

	$o = apply_atomic('search_buildingsize_field', '', $value, $class, $is_widget );

	if( $o != '' ) {
		return $o;
	}

	global $bon;
	$form = $bon->form();

	$id = 'buildingsize-slider-range';
	if( $is_widget ) {
		$id = 'buildingsize-slider-range-'.$is_widget;
	}

	$range_opt = shandora_get_size_range('buildingsize');

	$slider = '<div class="price-slider-wrapper"><div class="range-slider" data-type="buildingsize" data-step="'.$range_opt['step'].'" data-min="'.$range_opt['min'].'" data-max="'.$range_opt['max'].'" id="'.$id.'"></div></div>';

	$o = '<label for="property_buildingsize">'.__('Building Size', 'bon');
		$o .= '<span class="price-text text-min min_buildingsize_text"></span>';
		$o .= '<span class="price-text text-max max_buildingsize_text"></span>';
	$o .= '</label>';

	$o .= $slider;
	$o .= '<div class="row">';
		$o .= '<div class="column large-6"><input class="min_holder_buildingsize" type="hidden" name="min_buildingsize" value="'.$value['min_buildingsize'].'" /></div>';
		$o .= '<div class="column large-6"><input class="max_holder_buildingsize" type="hidden" name="max_buildingsize" value="'.$value['max_buildingsize'].'" /></div>';
		//$o .= '<div class="column large-6">' . $form->form_hidden('min_buildingsize', $value['min_buildingsize']) . '</div>';
		//$o .= '<div class="column large-6">' . $form->form_hidden('max_buildingsize', $value['max_buildingsize']) . '</div>';
	$o .= '</div>';


	return apply_atomic( 'search_buildingsize_field_output', $o );
}

/**
 * Used to output floor field in search panel
 * 
 * @since 1.0.6
 * @return string
 * @param string $value
 *
 */
function shandora_search_floor_field($value = array(), $class, $is_widget = false) {

	$o = apply_atomic('search_floor_field', '', $value, $class, $is_widget );

	if( $o != '' ) {
		return $o;
	}


	global $bon;
	$form = $bon->form();

	$floor_opt = absint(bon_get_option('maximum_floor', 5));
	$floor_arr = array(  'any'=> __('Any', 'bon') );
	if(!is_int($floor_opt)) {
		$floor_opt = 5;
	}
	for($i = 1; $i <= $floor_opt; $i++) {
		$floor_arr[$i] = $i;
	}

	$id = 'property_floor';
	if( $is_widget ) {
		$id = 'property_floor_' . $is_widget;
	}

	$o = $form->form_label(__('Floor','bon'), 'property_floor');
	$o .= $form->form_dropdown(array( 'name'=> 'property_floor', 'id'=> $id ), $floor_arr, $value['property_floor'], 'class="no-custom select-slider '.$class.'"');

	return apply_atomic( 'search_floor_field_output', $o );
}

/**
 * Used to output garage field in search panel
 * 
 * @since 1.0.6
 * @return string
 * @param string $value
 *
 */
function shandora_search_garage_field($value = array(), $class, $is_widget = false) {

	$o = apply_atomic('search_garage_field', '', $value, $class, $is_widget );

	if( $o != '' ) {
		return $o;
	}

	global $bon;
	$form = $bon->form();

	$garage_opt = absint(bon_get_option('maximum_garage', 5));
	$garage_arr = array(  'any'=> __('Any', 'bon')  );
	if(!is_int($garage_opt)) {
		$garage_opt = 5;
	}
	for($i = 1; $i <= $garage_opt; $i++) {
		$garage_arr[$i] = $i;
	}

	
	$id = 'property_garage';
	if( $is_widget ) {
		$id = 'property_garage_' . $is_widget;
	}

	$o = $form->form_label(__('Garage','bon'), 'property_garage');
	$o .= $form->form_dropdown(array( 'name'=> 'property_garage', 'id'=> $id ), $garage_arr, $value['property_garage'], 'class="no-custom select-slider '.$class.'"');

	return apply_atomic( 'search_garage_field_output', $o );
}

/**
 * Used to output basement field in search panel
 * 
 * @since 1.0.6
 * @return string
 * @param string $value
 *
 */
function shandora_search_basement_field($value = array(), $class, $is_widget = false) {

	$o = apply_atomic('search_basement_field', $value, $class, $is_widget );

	if( $o != '' ) {
		return $o;
	}

	global $bon;
	$form = $bon->form();

	$basement_opt = absint(bon_get_option('maximum_basement', 5));
	$basement_arr = array(  'any'=> __('Any', 'bon')  );
	if(!is_int($basement_opt)) {
		$basement_opt = 5;
	}
	for($i = 1; $i <= $basement_opt; $i++) {
		$basement_arr[$i] = $i;
	}

	
	$id = 'property_basement';
	if( $is_widget ) {
		$id = 'property_basement_' . $is_widget;
	}

	$o = $form->form_label(__('Basement','bon'), 'property_basement');
	$o .= $form->form_dropdown(array( 'name'=> 'property_basement', 'id'=> $id ), $basement_arr, $value['property_basement'], 'class="no-custom select-slider '.$class.'"');

	return apply_atomic( 'search_basement_field_output', $o );
}

/**
 * Used to output mortgage field in search panel
 * 
 * @since 1.0.6
 * @return string
 * @param string $value
 *
 */
function shandora_search_mortgage_field($value = array(), $class, $is_widget = false) {

	$o = apply_atomic('search_mortgage_field', '', $value, $class, $is_widget );

	if( $o != '' ) {
		return $o;
	}

	global $bon;
	$form = $bon->form();

	$o = $form->form_label(__('Mortgage','bon'), 'property_mortgage');
	$o .= $form->form_dropdown('property_mortgage', shandora_get_search_option('mortgage'), $value['property_mortgage'], 'class=" '.$class.'"');

	return apply_atomic( 'search_mortgage_field_output', $o );
}

/**
 * Used to output property type field in search panel
 * 
 * @since 1.0.6
 * @return string
 * @param string $value
 *
 */
function shandora_search_type_field($value = array(), $class, $is_widget = false) {

	$o = apply_atomic('search_type_field', '', $value, $class, $is_widget );

	if( $o != '' ) {
		return $o;
	}

	global $bon;
	$form = $bon->form();

	$o = $form->form_label(__('Type','bon'), 'property_type');
	$o .= $form->form_dropdown('property_type', shandora_get_search_option('type'), $value['property_type'], 'class=" '.$class.'"');

	return apply_atomic( 'search_type_field_output', $o );
}


/**
 * Used to output price field in search panel
 * 
 * @since 1.0.6
 * @return string
 * @param string $min_val
 * @param string $max_val
 *
 */
function shandora_search_price_field($value = array(), $class, $is_widget = false) {

	$o = apply_atomic('search_price_field', '', $value, $class, $is_widget );

	if( $o != '' ) {
		return $o;
	}

	global $bon;
	$form = $bon->form();


	$id = 'price-slider-range';
	if( $is_widget ) {
		$id = 'price-slider-range-'.$is_widget;
	}


	$range_opt = shandora_get_price_range();
	$range_rent_opt = shandora_get_price_range('rent');


	$price_slider = '<div class="price-slider-wrapper"><div data-type="price" data-step-r="'.$range_rent_opt['step'].'" data-min-r="'.$range_rent_opt['min'].'" data-max-r="'.$range_rent_opt['max'].'" data-step="'.$range_opt['step'].'" data-min="'.$range_opt['min'].'" data-max="'.$range_opt['max'].'" id="'.$id.'" class="range-slider"></div></div>';

	$o = '<label for="property_price">'.__('Price Range', 'bon');
		$o .= '<span class="price-text text-min min_price_text"></span>';
		$o .= '<span class="price-text text-max max_price_text"></span>';
	$o .= '</label>';

	$o .= $price_slider;
	$o .= '<div class="row">';
	
		//$o .= '<div class="column large-6">' . $form->form_hidden('min_price', $value['min_price']) . '</div>';
		$o .= '<div class="column large-6"><input class="min_holder_price" type="hidden" name="min_price" value="'.$value['min_price'].'" /></div>';
		$o .= '<div class="column large-6"><input class="max_holder_price" type="hidden" name="max_price" value="'.$value['max_price'].'" /></div>';
		//$o .= '<div class="column large-6">' . $form->form_hidden('max_price', $value['max_price']) . '</div>';
	$o .= '</div>';


	return apply_atomic( 'search_price_field_output', $o );
}

/**
 * Used to output bed field in search panel
 * 
 * @since 1.0.6
 * @return string
 * @param string $value
 *
 */
function shandora_search_bed_field($value = array(), $class, $is_widget = false) {

	$o = apply_atomic('search_bed_field', '', $value, $class, $is_widget );

	if( $o != '' ) {
		return $o;
	}

	global $bon;
	$form = $bon->form();

	$bed_opt = absint(bon_get_option('maximum_bed', 5));
	$bed_arr = array(  'any'=> __('Any', 'bon') );
	if(!is_int($bed_opt)) {
		$bed_opt = 5;
	}
	for($i = 1; $i <= $bed_opt; $i++) {
		$bed_arr[$i] = $i;
	}
	$id = 'property_bed';
	if( $is_widget ) {
		$id = 'property_bed_' . $is_widget;
	}
	$o = $form->form_label(__('Bed Room','bon'), 'property_bed');
	$o .= $form->form_dropdown(array( 'name'=> 'property_bed', 'id'=> $id ), $bed_arr, $value['property_bed'], 'class="no-custom select-slider '.$class.'"');

	return apply_atomic( 'search_bed_field_output', $o );
}

/**
 * Used to output bath field in search panel
 * 
 * @since 1.0.6
 * @return string
 * @param string $value
 *
 */
function shandora_search_bath_field($value = array(), $class, $is_widget = false) {
	
	$o = apply_atomic('search_bath_field', '', $value, $class, $is_widget );

	if( $o != '' ) {
		return $o;
	}

	global $bon;
	$form = $bon->form();

	$bath_opt = absint(bon_get_option('maximum_bath', 5));
	$bath_arr = array(  'any'=> __('Any', 'bon') );
	if(!is_int($bath_opt)) {
		$bath_opt = 5;
	}
	for($i = 1; $i <= $bath_opt; $i++) {
		$bath_arr[$i] = $i;
	}
	$id = 'property_bath';
	if( $is_widget ) {
		$id = 'property_bath_' . $is_widget;
	}
	$o = $form->form_label(__('Bath Room','bon'), 'property_bath');
	$o .= $form->form_dropdown(array( 'name'=> 'property_bath', 'id'=> $id ), $bath_arr, $value['property_bath'], 'class="no-custom select-slider '.$class.'"');

	return apply_atomic( 'search_bath_field_output', $o );

}



/**
 * Used to output bath field in search panel
 * 
 * @since 1.0.6
 * @return string
 * @param string $value
 *
 */
function shandora_search_room_field($value = array(), $class, $is_widget = false) {
	
	$o = apply_atomic('search_bath_field', '', $value, $class, $is_widget );

	if( $o != '' ) {
		return $o;
	}

	global $bon;
	$form = $bon->form();

	$room_opt = absint(bon_get_option('maximum_room', 5));
	$room_arr = array(  'any'=> __('Any', 'bon') );
	if(!is_int($room_opt)) {
		$room_opt = 5;
	}
	for($i = 1; $i <= $room_opt; $i++) {
		$room_arr[$i] = $i;
	}
	$id = 'property_room';
	if( $is_widget ) {
		$id = 'property_room' . $is_widget;
	}
	$o = $form->form_label(__('Total Room','bon'), 'property_room');
	$o .= $form->form_dropdown(array( 'name'=> 'property_room', 'id'=> $id ), $room_arr, $value['property_room'], 'class="no-custom select-slider '.$class.'"');

	return apply_atomic( 'search_room_field_output', $o );

}

/**
 * Used to output agent field in search panel
 * 
 * @since 1.0.6
 * @return string
 * @param string $value
 *
 */
function shandora_search_agent_field($value = array(), $class, $is_widget = false) {

	$o = apply_atomic('search_agent_field', '', $value, $class, $is_widget );

	if( $o != '' ) {
		return $o;
	}

	global $bon;
	$form = $bon->form();

	$o = $form->form_label(__('Agent','bon'), 'property_agent');
	$o .= $form->form_dropdown('property_agent', shandora_get_search_option('agent'), $value['property_agent'], 'class=" '.$class.'"');

	return apply_atomic( 'search_agent_field_output', $o );
}


/**
 * Used to output mls field in search panel
 * 
 * @since 1.0.6
 * @return string
 * @param string $value
 *
 */
function shandora_search_mls_field($value = array(), $class, $is_widget = false) {

	$o = apply_atomic('search_mls_field', '', $value, $class, $is_widget );

	if( $o != '' ) {
		return $o;
	}

	global $bon;
	$form = $bon->form();

	$o = $form->form_label(__('MLS #','bon'), 'property_mls');
	$o .= $form->form_input('property_mls', $value['property_mls'], 'placeholder="'.__('Type MLS ID here','bon').'" class="'.$class.'"');

	return apply_atomic( 'search_mls_field_output', $o );
}

/**
 * Used to output zip field in search panel
 * 
 * @since 1.0.6
 * @return string
 * @param string $value
 *
 */
function shandora_search_zip_field($value = array(), $class, $is_widget = false) {

	$o = apply_atomic('search_zip_field', '', $value, $class, $is_widget );

	if( $o != '' ) {
		return $o;
	}

	global $bon;
	$form = $bon->form();

	$o = $form->form_label(__('Zip Postal','bon'), 'property_zip');
	$o .= $form->form_input('property_zip', $value['property_zip'], 'placeholder="'.__('Type Zip Code here','bon').'" class="'.$class.'"');
	
	return apply_atomic( 'search_zip_field_output', $o );
}

/**
 * Used to output status field in search panel
 * 
 * @since 1.0.6
 * @return string
 * @param string $value
 *
 */
function shandora_search_status_field($value = array(), $class, $is_widget = false) {

	$o = apply_atomic('search_status_field', '', $value, $class, $is_widget );

	if( $o != '' ) {
		return $o;
	}

	global $bon;
	$form = $bon->form();

	$pstat = array(
		'any' => __('Any', 'bon')
	);
	$stat = wp_parse_args( shandora_get_search_option('status') , $pstat );

	$o = $form->form_label(__('Status','bon'), 'property_status');
	$o .= $form->form_dropdown('property_status', $stat, $value['property_status'], 'class=" '.$class.'"');

	return apply_atomic( 'search_status_field_output', $o );
}

/**
 * Used to output property location field in search panel
 * 
 * @since 1.0.6
 * @return string
 * @param string $value
 *
 */
function shandora_search_location_field($value = array(), $class, $is_widget = false) {

	$o = apply_atomic('search_location_field', '', $value, $class, $is_widget );

	if( $o != '' ) {
		return $o;
	}

	global $bon;
	$form = $bon->form();

	$o = $form->form_label(__('Location','bon'), 'property_location');
	$o .= $form->form_dropdown('property_location', shandora_get_search_option('location'), $value['property_location'], 'class=" '.$class.'"');

	return apply_atomic( 'search_location_field_output', $o );

}

/**
 * Used to output property location field in search panel
 * 
 * @since 1.0.6
 * @return string
 * @param string $value
 *
 */
function shandora_search_location_level1_field($value = array(), $class, $is_widget = false) {

	$o = apply_atomic('search_location_level1_field', '', $value, $class, $is_widget );

	if( $o != '' ) {
		return $o;
	}

	global $bon;
	$form = $bon->form();

	$o = $form->form_label(bon_get_option('location_level1_label'), 'property_location');
	$o .= $form->form_dropdown('property_location_level1', shandora_get_search_option('location1'), $value['property_location_level1'], 'class=" '.$class.'"');

	return apply_atomic( 'search_location_level1_field_output', $o );

}

/**
 * Used to output property location field in search panel
 * 
 * @since 1.0.6
 * @return string
 * @param string $value
 *
 */
function shandora_search_location_level2_field($value = array(), $class, $is_widget = false) {

	$o = apply_atomic('search_location_level2_field', '', $value, $class, $is_widget );

	if( $o != '' ) {
		return $o;
	}

	global $bon;
	$form = $bon->form();

	$loc_opt = array('any' => __('Any','bon'));

	if($value['property_location_level1'] != '') {
		$parent = get_term_by('slug', $value['property_location_level1'], 'property-location');
		if($parent) {
			$terms = get_terms('property-location', array('parent' => $parent->term_id));
		    if($terms) {
		    	foreach($terms as $term) {
			    	$loc_opt[$term->slug] = $term->name;
			    }
		    }
		}
	}

	$o = $form->form_label(bon_get_option('location_level2_label'), 'property_location');
	$o .= $form->form_dropdown('property_location_level2', $loc_opt, $value['property_location_level2'], 'class=" '.$class.'"');

	return apply_atomic( 'search_location_level2_field_output', $o );

}


/**
 * Used to output property location field in search panel
 * 
 * @since 1.0.6
 * @return string
 * @param string $value
 *
 */
function shandora_search_location_level3_field($value = array(), $class, $is_widget = false) {

	$o = apply_atomic('search_location_level3_field', '', $value, $class, $is_widget );

	if( $o != '' ) {
		return $o;
	}

	global $bon;
	$form = $bon->form();

	$loc_opt = array('any' => __('Any','bon'));

	if($value['property_location_level2'] != '') {
		$parent = get_term_by('slug', $value['property_location_level2'], 'property-location');
		if($parent) {
			$terms = get_terms('property-location', array('parent' => $parent->term_id));
		    if($terms) {
		    	foreach($terms as $term) {
			    	$loc_opt[$term->slug] = $term->name;
			    }
		    }
		}
	}

	$o = $form->form_label(bon_get_option('location_level3_label'), 'property_location');
	$o .= $form->form_dropdown('property_location_level3', $loc_opt, $value['property_location_level3'], 'class=" '.$class.'"');

	return apply_atomic( 'search_location_level3_field_output', $o );

}

function shandora_ajax_update_location_level($slug) {
	$loc_opt = array('any' => __('Any', 'bon'));

	if ( function_exists( 'check_ajax_referer' ) ) {				
		check_ajax_referer( 'search-panel-submit', 'nonce' );
	}

	$slug = $_POST['term_slug'];

	if(!empty($slug)) {
		$parent = get_term_by('slug', $slug, 'property-location');
		if($parent) {
			$terms = get_terms('property-location', array( 'hide_empty' => true, 'parent' => $parent->term_id));
		    if($terms) {
		    	foreach($terms as $term) {
			    	$loc_opt[$term->slug] = $term->name;
			    }
		    }
		}
	}

	wp_send_json($loc_opt);
}

add_action( 'wp_ajax_location-level', 'shandora_ajax_update_location_level' );
add_action( 'wp_ajax_nopriv_location-level', 'shandora_ajax_update_location_level' );



?>