<?php

// BOAT POST TYPE //
if( !function_exists( 'shandora_setup_boat_dealer_post_type') )  {

	function shandora_setup_boat_dealer_post_type() {
		global $bon;

		$prefix = bon_get_prefix();

		$suffix = SHANDORA_MB_SUFFIX;

		$cpt = $bon->cpt();


		$use_rewrite = bon_get_option( 'use_rewrite', 'no' );

		$settings = array();
		$slug = '';

		$settings['rewrite_root'] = bon_get_option( 'rewrite_root' );
		$settings['boat_root'] = bon_get_option( 'boat_root', 'car' );

		$settings['boat_manufacturer_root'] = bon_get_option( 'boat_manufacturer_root', 'boat-manufacturer' );
		$settings['boat_type_root'] = bon_get_option( 'boat_type_root', 'boat-type' );
		$settings['boat_location_root'] = bon_get_option( 'boat_location_root', 'boat-location' );
		$settings['boat_feature_root'] = bon_get_option( 'boat_feature_root', 'boat-feature' );
		$settings['boat_engine_root'] = bon_get_option( 'boat_engine_root', 'boat-engine' );
		$settings['boat_hull_root'] = bon_get_option( 'boat_hull_root', 'boat-hull' );
		$settings['boat_seat_root'] = bon_get_option( 'boat_seat_root', 'boat-seat' );


		if( !empty( $settings['rewrite_root'] ) ) {
			$slug = "{$settings['rewrite_root']}/{$settings['boat_root']}";
		} else {
			$slug = "{$settings['boat_root']}";
		}

		$manufacturer_slug = "{$settings['boat_root']}/{$settings['boat_manufacturer_root']}";
		$boat_type_slug = "{$settings['boat_root']}/{$settings['boat_type_root']}";
		$boat_location_slug = "{$settings['boat_root']}/{$settings['boat_location_root']}";
		$feature_slug = "{$settings['boat_root']}/{$settings['boat_feature_root']}";
		$engine_slug = "{$settings['boat_root']}/{$settings['boat_engine_root']}";
		$hull_slug = "{$settings['boat_root']}/{$settings['boat_hull_root']}";
		$seat_slug = "{$settings['boat_root']}/{$settings['boat_seat_root']}";

		$has_archive = ( $use_rewrite == 'no' ) ? false : $slug;

		$rewrite_var = array(
			'slug'       => $slug,
			'with_front' => false,
			'pages'      => true,
			'feeds'      => true,
			'ep_mask'    => EP_PERMALINK,
		);

		$rewrite = ( $use_rewrite == 'no' ) ? true : $rewrite_var;

			$name = __('Boat Listing', 'bon');
			$plural = __('Boat Listings', 'bon');

		$cpt->create('Boat Listing', array( 'has_archive' => $has_archive, 'rewrite' => $rewrite, 'supports' => array('editor','title', 'excerpt', 'thumbnail'), 'menu_position' => 8 ), array(), $name, $plural);

		/* The rewrite handles the URL structure. */
		$manufacturer_rewrite_var = array(
			'slug'         => $manufacturer_slug,
			'with_front'   => false,
			'hierarchical' => true,
			'ep_mask'      => EP_NONE
		);


		/* The rewrite handles the URL structure. */
		$engine_rewrite_var = array(
			'slug'         => $engine_slug,
			'with_front'   => false,
			'hierarchical' => true,
			'ep_mask'      => EP_NONE
		);

		
		/* The rewrite handles the URL structure. */
		$boat_type_rewrite_var = array(
			'slug'         => $boat_type_slug,
			'with_front'   => false,
			'hierarchical' => false,
			'ep_mask'      => EP_NONE
		);

		/* The rewrite handles the URL structure. */
		$dealer_location_rewrite_var = array(
			'slug'         => $boat_location_slug,
			'with_front'   => false,
			'hierarchical' => true,
			'ep_mask'      => EP_NONE
		);

		/* The rewrite handles the URL structure. */
		$feature_rewrite_var = array(
			'slug'         => $feature_slug,
			'with_front'   => false,
			'hierarchical' => false,
			'ep_mask'      => EP_NONE
		);

		$hull_rewrite_var = array(
			'slug'         => $hull_slug,
			'with_front'   => false,
			'hierarchical' => false,
			'ep_mask'      => EP_NONE
		);

		$seat_rewrite_var = array(
			'slug'         => $seat_slug,
			'with_front'   => false,
			'hierarchical' => false,
			'ep_mask'      => EP_NONE
		);


		if( $use_rewrite == 'no' ) {

			$feature_rewrite = true;
			$dealer_location_rewrite = true;
			$boat_type_rewrite = true;
			$manufacturer_rewrite = true;
			$engine_rewrite = true;
			$hull_rewrite = true;
			$seat_rewrite = true;

		} else {

			$feature_rewrite = $feature_rewrite_var;
			$dealer_location_rewrite = $dealer_location_rewrite_var;
			$boat_type_rewrite = $boat_type_rewrite_var;
			$manufacturer_rewrite = $manufacturer_rewrite_var;
			$engine_rewrite = $engine_rewrite_var;
			$hull_rewrite = $hull_rewrite_var;
			$seat_rewrite = $seat_rewrite_var;
		}

		$cpt->add_taxonomy("Boat Manufacturer", array( 'rewrite' => $manufacturer_rewrite, 'label' => __('Manufacturers','bon'), 'labels' => array('menu_name' => __('Manufacturers','bon') ), 'hierarchical' => true ) );

		$cpt->add_taxonomy("Boat Engine", array( 'rewrite' => $feature_rewrite, 'label' => __('Engine','bon'), 'labels' => array('menu_name' => __('Engines','bon') ), 'hierarchical' => true ) );

		$cpt->add_taxonomy("Boat Type", array( 'rewrite' => $boat_type_rewrite, 'label' => __('Types','bon'), 'labels' => array('menu_name' => __('Types','bon') ), 'hierarchical' => true ) );

		$cpt->add_taxonomy("Boat Location", array( 'rewrite' => $dealer_location_rewrite, 'label' => __('Dealer Locations','bon'), 'labels' => array('menu_name' => __('Dealer Locations','bon') ),  'hierarchical' => true ) );

		$cpt->add_taxonomy("Boat Feature", array( 'rewrite' => $feature_rewrite, 'label' => __('Features','bon'), 'labels' => array('menu_name' => __('Features','bon') ) ) );

		$cpt->add_taxonomy("Boat Hull", array( 'rewrite' => $hull_rewrite, 'label' => __('Hull Materials','bon'), 'labels' => array('menu_name' => __('Hull Materials','bon') ),  'hierarchical' => true ) );

		$cpt->add_taxonomy("Boat Seat", array( 'rewrite' => $seat_rewrite, 'label' => __('Seatings','bon'), 'labels' => array('menu_name' => __('Seatings','bon') ) ) );


		$opts = shandora_boat_meta_box_options();

		foreach( $opts as $_opt_key => $_opt_val ) {
			$cpt->add_meta_box( $_opt_key, $_opt_val['label'] . ' ' . __('Options', 'bon' ), $_opt_val['options'] );
		}

	}
}


function shandora_boat_meta_box_options() {

	$prefix = bon_get_prefix();
	$suffix = SHANDORA_MB_SUFFIX;
	$measure_w = bon_get_option( 'width_measure' );
	$measure_h = bon_get_option( 'height_measure' );
	$measure_l = bon_get_option( 'length_measure' );
	

	$detail_options = array(
		array(
			'label'	=> __('Reg Number', 'bon'),
			'desc'	=> __('The Boat Registry Number #', 'bon'), 
			'id'	=> $prefix . $suffix .'reg',
			'type'	=> 'text',
		),

		array(
			'label'	=> __('Boat Condition', 'bon'),
			'desc'	=> __('Boat sale condition', 'bon'), 
			'id'	=> $prefix . $suffix .'status',
			'type'	=> 'select',
			'options' => shandora_get_boat_search_option('status')
		),


		array(
			'label'	=> __('Exterior Color', 'bon'),
			'desc'	=> __('Exterior Color', 'bon'), 
			'id'	=> $prefix . $suffix .'extcolor',
			'type'	=> 'text',
		),

		array(
			'label'	=> __('Interior Color', 'bon'),
			'desc'	=> __('Interior Color', 'bon'), 
			'id'	=> $prefix . $suffix .'intcolor',
			'type'	=> 'text',
		),
		
		array(
			'label'	=> __('Hours', 'bon'),
			'desc'	=> __('The boat used hours.', 'bon'), 
			'id'	=> $prefix . $suffix .'hours',
			'type'	=> 'text',
		),

		array(
			'label'	=> __('Fuel Type', 'bon'),
			'desc'	=> __('Fuel Type', 'bon'), 
			'id'	=> $prefix . $suffix .'fueltype',
			'type'	=> 'select',
			'options' => shandora_get_boat_search_option('fuel')
		),

		array(
				'label'	=> __('Max Speed', 'bon'),
				'desc'	=> __('Max Speed in knots.', 'bon'), 
				'id'	=> $prefix . $suffix .'speed',
				'type'	=> 'text',
				'measure' => bon_get_option( 'speed_measure' ),
		),

		array(
			'label'	=> __('People Capacity', 'bon'),
			'desc'	=> __('How many people can be hold on the boat capacity.', 'bon'), 
			'id'	=> $prefix . $suffix .'people_cap',
			'type'	=> 'text',
			'measure' => __('People', 'bon'),
		),

		array( 
			'label'	=> __('Year', 'bon'),
			'desc'	=> __('When is the car year build? eq: 2013', 'bon'), 
			'id'	=> $prefix . $suffix .'yearbuild',
			'type'	=> 'text',
		),

		/*array(
			'label'	=> __('Seating', 'bon'),
			'desc'	=> __('Seating', 'bon'), 
			'id'	=> $prefix . $suffix .'seating',
			'type'	=> 'select',
			'options' => shandora_get_boat_search_option( 'seating'),
		),*/

		array(
			'label'	=> __('Canvas', 'bon'),
			'desc'	=> __('Canvas', 'bon'), 
			'id'	=> $prefix . $suffix .'canvas',
			'type'	=> 'select',
			'options' => shandora_get_boat_search_option( 'canvas'),
		),

		/*array(
			'label'	=> __('Hull Material', 'bon'),
			'desc'	=> __('Hull Material', 'bon'), 
			'id'	=> $prefix . $suffix .'hull',
			'type'	=> 'select',
			'options' => shandora_get_boat_search_option( 'hull' ),
		),*/

		array(
			'label'	=> __('Carpet', 'bon'),
			'desc'	=> __('Carpet', 'bon'), 
			'id'	=> $prefix . $suffix .'carpet',
			'type'	=> 'text',
		),
		
		array(
			'label'	=> __('Steering Type', 'bon'),
			'desc'	=> __('The boat steering type', 'bon'), 
			'id'	=> $prefix . $suffix .'steering',
			'type'	=> 'text',
		),

	);

	$dimension_options = array(
		array(
			'label'	=> __('Overall Height', 'bon'),
			'desc'	=> __('The overall boat height', 'bon'), 
			'id'	=> $prefix . $suffix .'height',
			'type'	=> 'text',
			'measure' => $measure_h,
		),

		array(
			'label'	=> __('Overall Width', 'bon'),
			'desc'	=> __('The overall boat width', 'bon'), 
			'id'	=> $prefix . $suffix .'width',
			'type'	=> 'text',
			'measure' => $measure_w,
		),

		array(
			'label'	=> __('Overall Length', 'bon'),
			'desc'	=> __('The overall boat length', 'bon'), 
			'id'	=> $prefix . $suffix .'length',
			'type'	=> 'text',
			'measure' => $measure_l,
		),

		array(
			'label'	=> __('Waterline Length', 'bon'),
			'desc'	=> __('The boat waterline length', 'bon'), 
			'id'	=> $prefix . $suffix .'waterline_length',
			'type'	=> 'text',
			'measure' => $measure_l,
		),

		array(
			'label'	=> __('Max Beam', 'bon'),
			'desc'	=> __('The boat beam width', 'bon'), 
			'id'	=> $prefix . $suffix .'beam',
			'type'	=> 'text',
			'measure' => $measure_w,
		),

		array(
			'label'	=> __('Max Draft', 'bon'),
			'desc'	=> __('The boat draft height', 'bon'), 
			'id'	=> $prefix . $suffix .'draft',
			'type'	=> 'text',
			'measure' => $measure_h,
		),
	);

	$capacity_options = array(
		array(
			'label'	=> __('Fuel Tanks', 'bon'),
			'desc'	=> __('Fuel Tanks', 'bon'), 
			'id'	=> $prefix . $suffix .'fuelcaps',
			'type'	=> 'text',
		),

		array(
			'label'	=> __('Fresh Water', 'bon'),
			'desc'	=> __('Fresh Water', 'bon'), 
			'id'	=> $prefix . $suffix .'freshwater',
			'type'	=> 'text',
		),

		array(
			'label'	=> __('Black Water', 'bon'),
			'desc'	=> __('Black Water', 'bon'), 
			'id'	=> $prefix . $suffix .'blackwater',
			'type'	=> 'text',
		),

		array(
			'label'	=> __('Bilge Water', 'bon'),
			'desc'	=> __('Bilge Water', 'bon'), 
			'id'	=> $prefix . $suffix .'bilgewater',
			'type'	=> 'text',
		),

		array(
			'label'	=> __('Grey Water', 'bon'),
			'desc'	=> __('Grey Water', 'bon'), 
			'id'	=> $prefix . $suffix .'greywater',
			'type'	=> 'text',
		),

		array(
			'label'	=> __('Fuel Overflow', 'bon'),
			'desc'	=> __('Fuel Overflow', 'bon'), 
			'id'	=> $prefix . $suffix .'fueloverflow',
			'type'	=> 'text',
		),

		array(
			'label'	=> __('Lube Oil', 'bon'),
			'desc'	=> __('Lube Oil', 'bon'), 
			'id'	=> $prefix . $suffix .'lubeoil',
			'type'	=> 'text',
		),

		array(
			'label'	=> __('Dirty Oil', 'bon'),
			'desc'	=> __('Dirty Oil', 'bon'), 
			'id'	=> $prefix . $suffix .'dirtyoil',
			'type'	=> 'text',
		),

		array(
			'label'	=> __('Sludge', 'bon'),
			'desc'	=> __('Sludge', 'bon'), 
			'id'	=> $prefix . $suffix .'sludge',
			'type'	=> 'text',
		),

		array(
			'label'	=> __('Watermist', 'bon'),
			'desc'	=> __('Watermist', 'bon'), 
			'id'	=> $prefix . $suffix .'watermist',
			'type'	=> 'text',
		),
	);

	$misc_options = array(

		array(
			'label'	=> __('Badge', 'bon'),
			'desc'	=> __('badge text to show in listings view', 'bon'), 
			'id'	=> $prefix . $suffix .'badge',
			'type'	=> 'text',
		),

		array(
			'label'	=> __('Badge color', 'bon'),
			'desc'	=> __('badge text to show in listings view', 'bon'), 
			'id'	=> $prefix . $suffix .'badge_color',
			'type'	=> 'select',
			'options' => array(
					'none' => __('None','bon'),
					'badge-red' => __('Red','bon'),
					'badge-orange' => __('Orange','bon'),
					'badge-green' => __('Green','bon'),
					'badge-blue' => __('Blue','bon'),
					'badge-purple' => __('Purple','bon'),
					'badge-gray' => __('Gray','bon'),
				)
		),

		array(
			'label'	=> __('Price', 'bon'),
			'desc'	=> __('The Property Price. Fill with numeric only, eq: 123456', 'bon'), 
			'id'	=> $prefix . $suffix .'price',
			'type'	=> 'text',
		),

		array(
			'label'	=> __('Price as Text', 'bon'),
			'desc'	=> __('Set price to use text. Text Options can be filled in theme Options, Shandora > Listing Settings > Price as Text.', 'bon'), 
			'id'	=> $prefix . $suffix .'pricetext',
			'type'	=> 'checkbox',
		),
				
		array( 

			'label'	=> __('Featured Boat', 'bon'),
			'desc'	=> __('Make the listing featured for featured listing widget', 'bon'), 
			'id'	=> $prefix . $suffix .'featured',
			'type'	=> 'checkbox',
			
		),

		array( 

			'label'	=> __('Agent for this listing', 'bon'),
			'desc'	=> __('The sales rep pointed for this boat listing', 'bon'), 
			'id'	=> $prefix . $suffix .'agentpointed',
			'type'	=> 'old_post_select',
			'post_type' => 'agent', 
			
		),
	);

	$opts = array(
		'gallery-options' => array(
			'label' => __('Gallery', 'bon'),
			'options' => shandora_gallery_metabox_args(),
			'show_ui' => false,
		),
		'boat-misc-options' => array(
			'label' => __('Miscellaneous', 'bon'),
			'options' => $misc_options,
			'show_ui' => false,
		),
		'boat-detail-options' => array(
			'label' => __('Detail', 'bon'),
			'options' => $detail_options,
			'show_ui' => true,
		),
		'boat-dimension-options' => array(
			'label' => __('Dimension', 'bon'),
			'options' => $dimension_options,
			'show_ui' => true,
		),
		'boat-capacity-options' => array(
			'label' => __('Capacity', 'bon'),
			'options' => $capacity_options,
			'show_ui' => true,
			'measure' => bon_get_option( 'volume_measure', 'litres'),
		),
		'video-options' => array(
			'label' => __('Video', 'bon'),
			'options' => shandora_video_metabox_args(),
			'show_ui' => false,
		),
	);

	return $opts;
}

function shandora_get_boat_tabs() {
	$tabs = shandora_boat_meta_box_options();

	$details_tab = array( 
		array(
			'label'	=> __('Make', 'bon'),
			'id'	=> 'boat-manufacturer',
			'depth' => 1,
			'type'	=> 'tax',
		),
		array(
			'label'	=> __('Model', 'bon'),
			'id'	=> 'boat-manufacturer',
			'depth' => 2,
			'type'	=> 'tax',
		),
		array(
			'label'	=> __('Engine Make', 'bon'),
			'id'	=> 'boat-engine',
			'depth' => 1,
			'type'	=> 'tax',
		),
		array(
			'label'	=> __('Engine Model', 'bon'),
			'id'	=> 'boat-engine',
			'depth' => 2,
			'type'	=> 'tax',
		),
		array(
			'label'	=> __('Dealer Location', 'bon'),
			'id'	=> 'boat-location',
			'depth' => 0,
			'type'	=> 'tax',
		),

		array(
			'label'	=> __('Hull Material', 'bon'),
			'id'	=> 'boat-hull',
			'depth' => 0,
			'type'	=> 'tax',
		),

		array(
			'label'	=> __('Seating', 'bon'),
			'id'	=> 'boat-seat',
			'depth' => 0,
			'type'	=> 'tax',
		),

	);

	$tabs['boat-detail-options']['options'] = array_merge( $details_tab, $tabs['boat-detail-options']['options'] );

	$tabs['boat-features'] = array(
		'label' => __('Features', 'bon'),
		'tax' => 'boat-feature',
		'show_ui' => true,
	);

	return apply_atomic( 'boat_tabs', $tabs );
}
?>