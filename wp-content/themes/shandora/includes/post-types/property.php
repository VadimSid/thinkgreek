<?php

if( !function_exists('shandora_setup_listing_post_type') ) {

	function shandora_setup_listing_post_type() {
		global $bon;

		$prefix = bon_get_prefix();

		$suffix = SHANDORA_MB_SUFFIX;

		$cpt = $bon->cpt();

		$use_rewrite = bon_get_option( 'use_rewrite', 'no' );

		$settings = array();
		$slug = '';

		$settings['rewrite_root'] = bon_get_option( 'rewrite_root' );
		$settings['realestate_root'] = bon_get_option( 'realestate_root', 'real-estate' );


		$settings['realestate_property_type_root'] = bon_get_option( 'realestate_property_type_root', 'manufacturer' );
		$settings['realestate_property_location_root'] = bon_get_option( 'realestate_property_location_root', 'body-type' );
		$settings['realestate_property_feature_root'] = bon_get_option( 'realestate_property_feature_root', 'dealer-location' );


		if( !empty( $settings['rewrite_root'] ) ) {
			$slug = "{$settings['rewrite_root']}/{$settings['realestate_root']}";
		} else {
			$slug = "{$settings['realestate_root']}";
		}

		$property_type_slug = "{$settings['realestate_root']}/{$settings['realestate_property_type_root']}";
		$property_location_slug = "{$settings['realestate_root']}/{$settings['realestate_property_location_root']}";
		$property_feature_slug = "{$settings['realestate_root']}/{$settings['realestate_property_feature_root']}";

		$has_archive = ( $use_rewrite == 'no' ) ? false : $slug;

		$rewrite_var = array(
				'slug'       => $slug,
				'with_front' => false,
				'pages'      => true,
				'feeds'      => true,
				'ep_mask'    => EP_PERMALINK,
			);
		
		$rewrite = ( $use_rewrite == 'no' ) ? true : $rewrite_var;



		$name = __('Listing', 'bon');
		$plural = __('Listings', 'bon');
		$args = array( 
				'has_archive' => $has_archive, 
				'rewrite' => $rewrite, 
				'supports' => array('editor','title', 'excerpt', 'thumbnail', 'front-end-editor'), 
				'menu_position' => 6
				);

		$cpt->create('Listing', $args, array(), $name, $plural );

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
				'label'	=> __('MLS Number', 'bon'),
				'desc'	=> __('The property MLS Number #', 'bon'), 
				'id'	=> $prefix . $suffix .'mls',
				'type'	=> 'text',
			),

			

			array( 
				'label'	=> __('Property Status', 'bon'),
				'desc'	=> __('The status for the property, used for badge, etc.', 'bon'), 
				'id'	=> $prefix . $suffix . 'status',
				'type'	=> 'select',
				'options' => shandora_get_search_option()
			),

			array( 
				'label'	=> __('For Rent Period', 'bon'),
				'desc'	=> __('Choose the period for the rent. Only show if status is for rent.', 'bon'), 
				'id'	=> $prefix . $suffix . 'period',
				'type'	=> 'select',
				'options' => shandora_get_search_option('period'),
			),

			array( 
				'label'	=> __('Show Period on Price', 'bon'),
				'desc'	=> __('Always show the period after price for all property status except sale and sold status.', 'bon'), 
				'id'	=> $prefix . $suffix . 'show_period',
				'type'	=> 'select',
				'options' => array(
					'no' => __('No', 'bon'),
					'yes' => __('Yes', 'bon'),
				),
			),

			array(

				'label'	=> __('Address', 'bon'),
				'desc'	=> __('The Property Address.', 'bon'), 
				'id'	=> $prefix . $suffix .'address',
				'type'	=> 'textarea',

			),

			array(
				'label'	=> __('Zip Postal', 'bon'),
				'desc'	=> __('Address Zip Postal', 'bon'), 
				'id'	=> $prefix . $suffix .'zip',
				'type'	=> 'text',
			),

			array(

				'label'	=> __('Price', 'bon'),
				'desc'	=> __('The Property Price. Fill with numeric only, eq: 123456', 'bon'), 
				'id'	=> $prefix . $suffix .'price',
				'type'	=> 'text',

			),

			array(

				'label'	=> __('Secondary Price', 'bon'),
				'desc'	=> __('The Secondary Price eq. price without tax. Fill with numeric only, eq: 123456', 'bon'), 
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

				'label'	=> __('Bed Rooms', 'bon'),
				'desc'	=> __('How Many Bedroom? Fill with numeric only', 'bon'), 
				'id'	=> $prefix . $suffix .'bed',
				'type'	=> 'text',
				
			),

			array( 

				'label'	=> __('Bath Rooms', 'bon'),
				'desc'	=> __('How Many Bathroom? Fill with numeric only', 'bon'), 
				'id'	=> $prefix . $suffix .'bath',
				'type'	=> 'text',
				
			),

			array( 

				'label'	=> __('Garage', 'bon'),
				'desc'	=> __('How Many Garage? Fill with numeric only', 'bon'), 
				'id'	=> $prefix . $suffix .'garage',
				'type'	=> 'text',
				
			),

			array( 

				'label'	=> __('Basement', 'bon'),
				'desc'	=> __('How many basement?', 'bon'), 
				'id'	=> $prefix . $suffix .'basement',
				'type'	=> 'text',
				
			),

			array( 

				'label'	=> __('Floors / Stories', 'bon'),
				'desc'	=> __('The total floors or stories.', 'bon'), 
				'id'	=> $prefix . $suffix .'floor',
				'type'	=> 'text',
				
			),

			array( 

				'label'	=> __('Total Rooms', 'bon'),
				'desc'	=> __('The total rooms. Fill with numeric only', 'bon'), 
				'id'	=> $prefix . $suffix .'totalroom',
				'type'	=> 'text',
				
			),

			array( 

				'label'	=> __('Lot Size', 'bon'),
				'desc'	=> __('The Lot Size', 'bon'), 
				'id'	=> $prefix . $suffix .'lotsize',
				'type'	=> 'text',
				
			),

			array( 

				'label'	=> __('Building Size', 'bon'),
				'desc'	=> __('The Building Size', 'bon'), 
				'id'	=> $prefix . $suffix .'buildingsize',
				'type'	=> 'text',
				
			),

			array( 

				'label'	=> __('Furnishing', 'bon'),
				'desc'	=> __('The Property is Furnished or unfurnised?', 'bon'), 
				'id'	=> $prefix . $suffix .'furnishing',
				'type'	=> 'select',
				'options' => shandora_get_search_option('furnishing')
			),


			array( 

				'label'	=> __('Mortgage Availability', 'bon'),
				'desc'	=> __('The Property is Available for mortgage or not?', 'bon'), 
				'id'	=> $prefix . $suffix .'mortgage',
				'type'	=> 'select',
				'options' => shandora_get_search_option('mortgage')
			),


			array( 

				'label'	=> __('Date of Availability', 'bon'),
				'desc'	=> __('When is the property available?', 'bon'), 
				'id'	=> $prefix . $suffix .'dateavail',
				'type'	=> 'date',
				
			),

			array( 

				'label'	=> __('Year Built', 'bon'),
				'desc'	=> __('When is the property build? eq: 2013', 'bon'), 
				'id'	=> $prefix . $suffix .'yearbuild',
				'type'	=> 'text',
				
			),



			array( 

				'label'	=> __('Map Latitude', 'bon'),
				'desc'	=> __('The Map Latitude. You can easily find it <a href="http://www.itouchmap.com/latlong.html">here</a>. Copy and paste the latitude value generated there', 'bon'), 
				'id'	=> $prefix . $suffix .'maplatitude',
				'type'	=> 'text',
				
			),

			array( 

				'label'	=> __('Map Longitude', 'bon'),
				'desc'	=> __('The Map Longitude. You can easily find it <a href="http://www.itouchmap.com/latlong.html">here</a>. Copy and paste the longitude value generated there', 'bon'), 
				'id'	=> $prefix . $suffix .'maplongitude',
				'type'	=> 'text',
				
			),

			array( 

				'label'	=> __('Featured Property', 'bon'),
				'desc'	=> __('Make the property featured for featured property widget', 'bon'), 
				'id'	=> $prefix . $suffix .'featured',
				'type'	=> 'checkbox',
				
			),

			array( 

				'label'	=> __('Agent for this listing', 'bon'),
				'desc'	=> __('The agent pointed for this property listing', 'bon'), 
				'id'	=> $prefix . $suffix .'agentpointed',
				'type'	=> 'old_post_select',
				'post_type' => 'agent', 
				
			),



			
		);


		$fr_opt = array();

		if( bon_get_option( 'enable_dpe_ges', false ) == 'yes' ) {

			$fr_opt[] = array( 

						'label'	=> __('DPE', 'bon'),
						'desc'	=> __('Diagnostic de Performance énergétiqueg', 'bon'), 
						'id'	=> $prefix . $suffix .'dpe',
						'type'	=> 'number',
						
					);

			$fr_opt[] = array( 

						'label'	=> __('GES', 'bon'),
						'desc'	=> __('Gaz à effet de serre', 'bon'), 
						'id'	=> $prefix . $suffix .'ges',
						'type'	=> 'number',
						
					);
		}

		$prop_options = array_merge( $fr_opt, $prop_options);


		

		/* The rewrite handles the URL structure. */
		$property_type_rewrite_var = array(
			'slug'         => $property_type_slug,
			'with_front'   => false,
			'hierarchical' => false,
			'ep_mask'      => EP_NONE
		);

		
		/* The rewrite handles the URL structure. */
		$property_location_rewrite_var = array(
			'slug'         => $property_location_slug,
			'with_front'   => false,
			'hierarchical' => true,
			'ep_mask'      => EP_NONE
		);

		/* The rewrite handles the URL structure. */
		$property_feature_rewrite_var = array(
			'slug'         => $property_feature_slug,
			'with_front'   => false,
			'hierarchical' => false,
			'ep_mask'      => EP_NONE
		);

		if( $use_rewrite == 'no' ) {

			$property_feature_rewrite = true;
			$property_location_rewrite = true;
			$property_type_rewrite = true;

		} else {

			$property_feature_rewrite = $property_feature_rewrite_var;
			$property_location_rewrite = $property_location_rewrite_var;
			$property_type_rewrite = $property_type_rewrite_var;

		}

		$cpt->add_taxonomy("Property Type", array( 'rewrite' => $property_type_rewrite, 'hierarchical' => true, 'label' => __('Property Types','bon'), 'labels' => array('menu_name' => __('Types','bon') ) ) );

		$cpt->add_taxonomy("Property Location", array( 'rewrite' => $property_location_rewrite, 'hierarchical' => true, 'label' => __('Property Locations','bon'), 'labels' => array('menu_name' => __('Locations','bon') ) ) );

		$cpt->add_taxonomy("Property Feature", array( 'rewrite' => $property_feature_rewrite, 'label' => __('Property Features','bon'), 'labels' => array('menu_name' => __('Features','bon') ) ) );


		do_action("shandora_property_taxonomy", $cpt );

		$cpt->add_meta_box(   
		    'gallery-options',
		    __('Gallery Options','bon'),
		    $gallery_opts
		);

		$cpt->add_meta_box(   
		    'property-options',
		    __('Property Options','bon'),
		    $prop_options  
		);

		$cpt->add_meta_box(   
		    'video-options',
		    __('Video Options','bon'),
		    shandora_video_metabox_args()  
		);


		do_action("shandora_property_metabox", $cpt );

	}

}

if( !function_exists('shandora_setup_agent_post_type') ) {

	function shandora_setup_agent_post_type() {
		global $bon;

		$prefix = bon_get_prefix();

		$cpt = $bon->cpt();

		$name = __('Agent', 'bon');
		$plural = __('Agents', 'bon');
		

		$cpt->create('Agent', array( 'supports' => array('editor', 'title' ) ,'exclude_from_search' => true, 'menu_position' => 7 ), array(), $name, $plural );


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