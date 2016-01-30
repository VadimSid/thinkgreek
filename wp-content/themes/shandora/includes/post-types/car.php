<?php

if( !function_exists('shandora_setup_car_dealer_post_type') ) {

	function shandora_setup_car_dealer_post_type() {
		global $bon;

		$prefix = bon_get_prefix();

		$suffix = SHANDORA_MB_SUFFIX;

		$cpt = $bon->cpt();


		$use_rewrite = bon_get_option( 'use_rewrite', 'no' );

		$settings = array();
		$slug = '';

		$settings['rewrite_root'] = bon_get_option( 'rewrite_root' );
		$settings['car_root'] = bon_get_option( 'car_root', 'car' );

		$settings['car_manufacturer_root'] = bon_get_option( 'car_manufacturer_root', 'manufacturer' );
		$settings['car_body_type_root'] = bon_get_option( 'car_body_type_root', 'body-type' );
		$settings['car_dealer_location_root'] = bon_get_option( 'car_dealer_location_root', 'dealer-location' );
		$settings['car_feature_root'] = bon_get_option( 'car_feature_root', 'feature' );


		if( !empty( $settings['rewrite_root'] ) ) {
			$slug = "{$settings['rewrite_root']}/{$settings['car_root']}";
		} else {
			$slug = "{$settings['car_root']}";
		}

		$manufacturer_slug = "{$settings['car_root']}/{$settings['car_manufacturer_root']}";
		$body_type_slug = "{$settings['car_root']}/{$settings['car_body_type_root']}";
		$dealer_location_slug = "{$settings['car_root']}/{$settings['car_dealer_location_root']}";
		$feature_slug = "{$settings['car_root']}/{$settings['car_feature_root']}";

		$has_archive = ( $use_rewrite == 'no' ) ? false : $slug;

		$rewrite_var = array(
			'slug'       => $slug,
			'with_front' => false,
			'pages'      => true,
			'feeds'      => true,
			'ep_mask'    => EP_PERMALINK,
		);

		$rewrite = ( $use_rewrite == 'no' ) ? true : $rewrite_var;

			$name = __('Car Listing', 'bon');
			$plural = __('Car Listings', 'bon');

		$cpt->create('Car Listing', array( 'has_archive' => $has_archive, 'rewrite' => $rewrite, 'supports' => array('editor','title', 'excerpt', 'thumbnail','front-end-editor'), 'menu_position' => 8 ), array(), $name, $plural);

		$gallery_opts = array(

			array( 

				'label'	=> __('Listings Gallery', 'bon'),
				'desc'	=> __('Choose image to use in this listing gallery.', 'bon'), 
				'id'	=> $prefix . $suffix . 'gallery',
				'type'	=> 'gallery',
			),

		);

		$prop_options = array(

			array(
				'label'	=> __('Reg Number', 'bon'),
				'desc'	=> __('The Car Registry Number #', 'bon'), 
				'id'	=> $prefix . $suffix .'reg',
				'type'	=> 'text',
			),

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
				'label'	=> __('Car Status', 'bon'),
				'desc'	=> __('Car sale status', 'bon'), 
				'id'	=> $prefix . $suffix .'status',
				'type'	=> 'select',
				'options' => shandora_get_car_search_option('status')
			),


			array(
				'label'	=> __('Mileage', 'bon'),
				'desc'	=> __('Car mileage', 'bon'), 
				'id'	=> $prefix . $suffix .'mileage',
				'type'	=> 'text',
			),

			array(
				'label'	=> __('Exterior Color', 'bon'),
				'desc'	=> __('Car exterior color', 'bon'), 
				'id'	=> $prefix . $suffix .'extcolor',
				'type'	=> 'text',
			),

			array(
				'label'	=> __('Interior Color', 'bon'),
				'desc'	=> __('Car interior color', 'bon'), 
				'id'	=> $prefix . $suffix .'intcolor',
				'type'	=> 'text',
			),

			array(
				'label'	=> __('Fuel Type', 'bon'),
				'desc'	=> __('Car fuel type', 'bon'), 
				'id'	=> $prefix . $suffix .'fueltype',
				'type'	=> 'select',
				'options' => shandora_get_car_search_option('fuel')
			),

			array(
				'label'	=> __('Transmission', 'bon'),
				'desc'	=> __('Car transmission', 'bon'), 
				'id'	=> $prefix . $suffix .'transmission',
				'type'	=> 'select',
				'options' => shandora_get_car_search_option('transmission')
			),

			array(

				'label'	=> __('Price', 'bon'),
				'desc'	=> __('The Car Price. Fill with numeric only, eq: 123456', 'bon'), 
				'id'	=> $prefix . $suffix .'price',
				'type'	=> 'text',

			),

			array(

				'label'	=> __('Secondary Price', 'bon'),
				'desc'	=> __('The Car Secondary Price eq. price without Tax. Fill with numeric only, eq: 123456', 'bon'), 
				'id'	=> $prefix . $suffix .'price_sec',
				'type'	=> 'text',

			),

			array(

				'label'	=> __('Price as Text', 'bon'),
				'desc'	=> __('Set price to use text. Text Options can be filled in theme Options, Shandora > Listing Settings > Price as Text.', 'bon'), 
				'id'	=> $prefix . $suffix .'pricetext',
				'type'	=> 'checkbox',

			),

			array(
				'label'	=> __('Engine Type', 'bon'),
				'desc'	=> __('Car engine type', 'bon'), 
				'id'	=> $prefix . $suffix .'enginetype',
				'type'	=> 'text',
			),


			array(
				'label'	=> __('Engine Size', 'bon'),
				'desc'	=> __('Car engine size', 'bon'), 
				'id'	=> $prefix . $suffix .'enginesize',
				'type'	=> 'text',
			),

			
			array(

				'label'	=> __('Overall Height', 'bon'),
				'desc'	=> __('The overall car height', 'bon'), 
				'id'	=> $prefix . $suffix .'height',
				'type'	=> 'text',

			),

			array(

				'label'	=> __('Overall Width', 'bon'),
				'desc'	=> __('The overall car width', 'bon'), 
				'id'	=> $prefix . $suffix .'width',
				'type'	=> 'text',

			),

			array(

				'label'	=> __('Overall Length', 'bon'),
				'desc'	=> __('The overall car length', 'bon'), 
				'id'	=> $prefix . $suffix .'length',
				'type'	=> 'text',

			),

			array(

				'label'	=> __('Wheelbase', 'bon'),
				'desc'	=> __('The wheelbase size', 'bon'), 
				'id'	=> $prefix . $suffix .'wheelbase',
				'type'	=> 'text',

			),

			array(

				'label'	=> __('Track Front', 'bon'),
				'desc'	=> __('The track front size', 'bon'), 
				'id'	=> $prefix . $suffix .'trackfront',
				'type'	=> 'text',

			),

			array(

				'label'	=> __('Track Rear', 'bon'),
				'desc'	=> __('The track front size', 'bon'), 
				'id'	=> $prefix . $suffix .'trackrear',
				'type'	=> 'text',

			),

			array(

				'label'	=> __('Ground Clearance', 'bon'),
				'desc'	=> __('The ground clearance size', 'bon'), 
				'id'	=> $prefix . $suffix .'ground',
				'type'	=> 'text',

			),

			array(

				'label'	=> __('Standard Seating', 'bon'),
				'desc'	=> __('How many standard seating available', 'bon'), 
				'id'	=> $prefix . $suffix .'seating',
				'type'	=> 'text',

			),

			array(

				'label'	=> __('Steering Type', 'bon'),
				'desc'	=> __('The car steering type', 'bon'), 
				'id'	=> $prefix . $suffix .'steering',
				'type'	=> 'text',

			),

			array(
				'label'	=> __('ANCAP Rating / Safety Rating', 'bon'),
				'desc'	=> __('Australasian New Car Assessment Program Rating. see http://ancap.com.au', 'bon'), 
				'id'	=> $prefix . $suffix .'ancap',
				'type'	=> 'slider',
				'step' => '1',
				'min' => '0',
				'max' => '5'
			),


			array( 

				'label'	=> __('Year Built', 'bon'),
				'desc'	=> __('When is the car year build? eq: 2013', 'bon'), 
				'id'	=> $prefix . $suffix .'yearbuild',
				'type'	=> 'text',
			),


			array( 

				'label'	=> __('Featured Car', 'bon'),
				'desc'	=> __('Make the listing featured for featured listing widget', 'bon'), 
				'id'	=> $prefix . $suffix .'featured',
				'type'	=> 'checkbox',
				
			),

			array( 

				'label'	=> __('Sales Representative for this listing', 'bon'),
				'desc'	=> __('The sales rep pointed for this car listing', 'bon'), 
				'id'	=> $prefix . $suffix .'agentpointed',
				'type'	=> 'old_post_select',
				'post_type' => 'sales-representative', 
				
			),

			
		);

		
		/* The rewrite handles the URL structure. */
		$manufacturer_rewrite_var = array(
			'slug'         => $manufacturer_slug,
			'with_front'   => false,
			'hierarchical' => true,
			'ep_mask'      => EP_NONE
		);

		
		/* The rewrite handles the URL structure. */
		$body_type_rewrite_var = array(
			'slug'         => $body_type_slug,
			'with_front'   => false,
			'hierarchical' => false,
			'ep_mask'      => EP_NONE
		);

		/* The rewrite handles the URL structure. */
		$dealer_location_rewrite_var = array(
			'slug'         => $dealer_location_slug,
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

		if( $use_rewrite == 'no' ) {

			$feature_rewrite = true;
			$dealer_location_rewrite = true;
			$body_type_rewrite = true;
			$manufacturer_rewrite = true;

		} else {

			$feature_rewrite = $feature_rewrite_var;
			$dealer_location_rewrite = $dealer_location_rewrite_var;
			$body_type_rewrite = $body_type_rewrite_var;
			$manufacturer_rewrite = $manufacturer_rewrite_var;

		}

		$cpt->add_taxonomy("Manufacturer", array( 'rewrite' => $manufacturer_rewrite, 'label' => __('Manufacturers','bon'), 'labels' => array('menu_name' => __('Manufacturers','bon') ), 'hierarchical' => true ) );

		$cpt->add_taxonomy("Body Type", array( 'rewrite' => $body_type_rewrite, 'label' => __('Body Types','bon'), 'labels' => array('menu_name' => __('Body Types','bon') ), 'hierarchical' => true ) );

		$cpt->add_taxonomy("Dealer Location", array( 'rewrite' => $dealer_location_rewrite, 'label' => __('Dealer Locations','bon'), 'labels' => array('menu_name' => __('Dealer Locations','bon') ),  'hierarchical' => true ) );

		$cpt->add_taxonomy("Car Feature", array( 'rewrite' => $feature_rewrite, 'label' => __('Car Features','bon'), 'labels' => array('menu_name' => __('Features','bon') ) ) );

		$cpt->add_meta_box(   
		    'gallery-options',
		    __('Gallery Options', 'bon'),
		    $gallery_opts
		);

		$cpt->add_meta_box(   
		    'car-options',
		    __('Detail Options', 'bon'),
		    $prop_options  
		);

		$cpt->add_meta_box(   
		    'video-options',
		    __('Video Options', 'bon'),
		    shandora_video_metabox_args()  
		);
	}

}

if( !function_exists('shandora_setup_sales_rep_post_type') ) {

	function shandora_setup_sales_rep_post_type() {
		global $bon;

		$prefix = bon_get_prefix();

		$cpt = $bon->cpt();

		$name = __('Sales Representative', 'bon');
		$plural = __('Sales Representatives', 'bon');

		$cpt->create('Sales Representative', array( 'supports' => array('editor', 'title') , 'exclude_from_search' => true, 'menu_position' => 9 ), array(), $name, $plural );


		$agent_opt1 = array(

			array( 
				'label'	=> __('Job Title', 'bon'),
				'desc'	=> '', 
				'id'	=> $prefix.'agentjob',
				'type'	=> 'text',
			),

			array( 
				'label'	=> __('Facebook Username', 'bon'),
				'desc'	=> '', 
				'id'	=> $prefix.'agentfb',
				'type'	=> 'text',
			),

			array( 
				'label'	=> __('Twitter Username', 'bon'),
				'desc'	=> '', 
				'id'	=> $prefix.'agenttw',
				'type'	=> 'text',
			),

			array( 
				'label'	=> __('LinkedIn Username', 'bon'),
				'desc'	=> '', 
				'id'	=> $prefix.'agentlinkedin',
				'type'	=> 'text',
			),

			array( 
				'label'	=> __('Agent Profile Photo', 'bon'),
				'desc'	=> '', 
				'id'	=> $prefix.'agentpic',
				'type'	=> 'image',
			),

			array( 
				'label'	=> __('Email Address', 'bon'),
				'desc'	=> '', 
				'id'	=> $prefix.'agentemail',
				'type'	=> 'text',
			),

			array( 
				'label'	=> __('Office Phone Number', 'bon'),
				'desc'	=> '', 
				'id'	=> $prefix.'agentofficephone',
				'type'	=> 'text',
			),

			array( 
				'label'	=> __('Mobile Phone Number', 'bon'),
				'desc'	=> '', 
				'id'	=> $prefix.'agentmobilephone',
				'type'	=> 'text',
			),

			array( 
				'label'	=> __('Fax Number', 'bon'),
				'desc'	=> '', 
				'id'	=> $prefix.'agentfax',
				'type'	=> 'text',
			),

			
			
		);


		$cpt->add_meta_box(   
		    'agent-options',
		    'Agent Options',
		    $agent_opt1  
		);

	
	}

}
?>