<?php

/**
 * =====================================================================================================
 *
 * Setting Up theme supports
 * To setup theme supports use a filter to filter the support since there are already some default support
 * initialize after theme setup such as post formats etc, to remove post format unset the post format
 * from $theme_supports array variable and filter it 
 *
 * @since 1.0
 * @return array()
 *
 * ======================================================================================================
 */

add_filter( 'bon_post_format_icons', 'shandora_post_format_icons' );

function shandora_post_format_icons( $icons ) {

	$icons['link'] = 'sha-link';
	$icons['status'] = '';
	$icons['video'] = 'bonicons bi-play';
	$icons['gallery'] = 'sha-polaroid';
	$icons['image'] = 'sha-camera-3';
	$icons['audio'] = 'sha-headset-2';
	$icons['quote'] = 'sha-double-quote';
	$icons['chat'] = 'sha-talk-bubble';
	$icons['aside'] = 'bonicons bi-pencil';
	$icons['standard'] = 'bonicons bi-file';

	return $icons;
} 

add_filter( 'bon_option_pages', 'shandora_option_pages' );

function shandora_option_pages( $option_pages ) {

	require_once( BON_THEME_DIR . '/includes/theme-options.php');

	$shandora_pages = array( 
		'slug' => 'bon_options',
		'parent'=> 'bon_options',
		'title' => __('Theme Settings', 'bon'),
		'role' => 'manage_options',
		'option_key' => 'bon_optionsframework',
		'customizer' => true,
		'option_set' => bon_set_theme_options()
	);

	array_unshift( $option_pages, $shandora_pages );

	return $option_pages;
}

define('SC_CHAT_LICENSE_KEY', '05f8208c-3ac1-4176-8cd4-c62022d8e8ee');

add_action( 'after_setup_theme', 'shandora_setup' );

function shandora_setup() {

	bon_set_content_width( 620 );

	add_editor_style( 'assets/css/editor-styles.css' );

	update_option('sc_chat_validate_license', 1);
}

function shandora_embed_defaults( $args ) {

	if ( current_theme_supports( 'theme-layouts' ) && '1c' == get_theme_mod( 'theme_layout' ) )
		$args['width'] = 1170;

	return $args;

	add_filter( 'embed_defaults', 'shandora_embed_defaults' );
}

if( !function_exists('shandora_setup_theme_supports') ) {

	function shandora_setup_theme_support($theme_supports) {
		$theme_supports['bon-core-widgets'] = '';
		$theme_supports['bon-accounts'] = '';
		$theme_supports['bon-breadcrumb-trail'] = '';
		$theme_supports['post-formats'] = array(
										    'gallery',
										    'link',
										    'image',
										    'quote',
										    'video',
						 					);
		$theme_supports['bon-fee'] = '';
		$theme_supports['bon-fav'] = '';
		$theme_supports['get-the-image'] = '';
		$theme_supports['theme-fonts'] = array( 'callback' => 'shandora_custom_typo','customizer' => true );
		$theme_supports['bon-core-sidebars'] = array( 
												array(
													'name' => __('Sidebar Primary','bon'),
													'id' => 'primary'
												),
												array(
													'name' => __('Sidebar Listing', 'bon'),
													'id' => 'secondary'
												),
												array(
													'name' => __('Listing Details', 'bon'),
													'id' => 'singularlisting'
												),
												array(
													'name' => __('Footer 1', 'bon'),
													'id' => 'footer1'
												),
												array(
													'name' => __('Footer 2', 'bon'),
													'id' => 'footer2'
												),
												array(
													'name' => __('Footer 3', 'bon'),
													'id' => 'footer3'
												),
												array(
													'name' => __('Footer 4', 'bon'),
													'id' => 'footer4'
												),

											);
		$theme_supports['bon-featured-slider'] = '';
		$theme_supports['bon-poll'] = '';
		$theme_supports['bon-page-builder'] = array(
				'post',
				'page'
			);
		$theme_supports['bon-quiz'] = '';
		$theme_supports['bon-core-menus'] = array(
									'menus' => array(
										array(
											'id' => 'primary',
											'name' => __('Primary', 'bon' ),
											'advanced' => true,
										),
										array(
											'id' => 'topbar',
											'name' => __('Top Bar Menu', 'bon' ),
										),
									),
									'advanced_menu' => true,
								);
		$theme_supports['cleaner-gallery'] = '';
		$theme_supports['zurb-foundation'] = array(
				'foundation',
		);

		$theme_supports['dynamic-script'] = apply_filters('shandora_dynamic_script', array(
			

			'calculator' => array(
				'name' => 'calculator',
				'version' => '1.0.0',
				'dep' => array( 'jquery'),
				'in_footer' => true,
				'folder' => 'libs/',
				'filename' => 'calculator'
			),


			'selecttoui' => array(
					'name' => 'selecttoui',
					'version' => '2.0.0',
					'dep' => array( 'jquery', 'jquery-ui-slider'),
					'in_footer' => true,
					'folder' => 'libs/',
					'filename' => 'jquery.selecttoui.min'
				),
			
			/* uncomment the script below to support IE8 ( not fully supporting ) */
			
			'respond' => array(
					'name' => 'respond',
					'version' => '1.4.2',
					'in_footer' => false,
					'folder' => 'libs/',
					'filename' => 'respond.min'
				),
			
			
		));
	

		$color = bon_get_option('main_color_style', 'green');
		
		$theme_supports['dynamic-style'] = apply_filters('shandora_dynamic_style', array(

					
						'app' => array(
								'name' => 'app',
								'version' => '',
								'dep' => '',
								'media' => 'all',
								'folder' => 'colors/',
								'filename' => $color,
							),
						
						
						'all' => array(
								'name' => 'all',
								'version' => '',
								'dep' => '',
								'media' => 'all',
								'folder' => '',
								'filename' => 'all'
							),
						'print' => array(
								'name' => 'print',
								'version' => '',
								'dep' => '',
								'media' => 'print',
								'folder' => '',
								'filename' => 'print'
							),

						));
		
		$theme_supports = apply_filters('shandora_default_theme_supports', $theme_supports);

		foreach($theme_supports as $support_key => $support_args) {
			add_theme_support( $support_key, $support_args );
		}

	}

	add_action('after_setup_theme', 'shandora_setup_theme_support', 5);
}




if( !function_exists('shandora_layout_setup') ) {

	function shandora_layout_setup() {
		add_theme_support( 'theme-layouts', array( '1c', '2c-l', '2c-r' ), array( 'default' => '2c-l' ) );
	}

	add_action('after_setup_theme', 'shandora_layout_setup');
}

/**
 * =====================================================================================================
 *
 * Setting Up theme post thumbnails
 *
 * @since 1.0
 * @return array()
 *
 * ======================================================================================================
 */

if( !function_exists('shandora_setup_theme_thumbnails') ) {

	function shandora_setup_theme_thumbnails( $theme_thumbnails ) {
		
		$theme_thumbnails = array(
			'listing_small' => array('width'=>270, 'height'=>220, 'crop' => true ),							
			'listing_small_box'	=> array('width'=>300, 'height'=>300, 'crop' => true),
			'listing_list' => array( 'width' => 420, 'height' => 420, 'crop' => true ),
			'blog_small' => array('width' => 285, 'height' => 285, 'crop' => true),
			'listing_large' => array('width' => 800, 'height' => 400, 'crop' => true),
			'listing_medium' => array('width' => 400, 'height' => 200, 'crop' => true),
			'featured_slider' => array('width'=>1920, 'height'=> 1090, 'crop' => true),
		);

		foreach($theme_thumbnails as $key => $args) {
			add_image_size( $key, $args['width'], $args['height'], $args['crop'] );
		}
	}


	add_action('init', 'shandora_setup_theme_thumbnails');
}


function shandora_custom_typo( $theme_fonts ) {

	/* Register font settings. */

	$theme_fonts->add_setting(
		array(
			'id'        => 'primary',
			'label'     => __( 'Primary Font', 'example' ),
			'default'   => 'titilium-stack',
			'selectors' => 'body, p, ul, ol, dl, .subheader, #slider-container .slider-inner-container .flex-caption .secondary-title, #slider-container .slider-inner-container .flex-caption .caption-content, .featured-listing-carousel h2, #main-navigation nav ul > li > ul li a,
							.bon-toolkit-posts-widget .item-title,
							article.listing .price,
							footer .widget-title,
							.bon-builder-element-calltoaction .panel.callaction h1,
							.bon-builder-element-calltoaction .panel.callaction h2,
							.bon-builder-element-calltoaction .panel.callaction h3,
							.bon-builder-element-calltoaction .panel.callaction h4',
		)
	);

	$theme_fonts->add_setting(
		array(
			'id'        => 'heading',
			'label'     => __( 'Heading & Menu Font', 'bon' ),
			'default'   => 'bebeas-neue-stack',
			'selectors' => 'h1, h2, h3, h4, h5, h6, #main-navigation nav > ul, .listings .entry-header .badge, #comparison-table td.title nav > ul > li > a',
		)
	);


	$theme_fonts->add_font(
		array(
			'handle' => 'titilium-stack',
			'label'  => __( 'Titilium Web (font stack)', 'bon' ),
			'stack'  => '"Titillium Web", "HelveticaNeue-Light", "Helvetica Neue Light", "Helvetica Neue", Helvetica, Arial, "Lucida Grande", sans-serif',
		)
	);

	$theme_fonts->add_font(
		array(
			'handle' => 'helvetica-neue-stack',
			'label'  => __( 'Helvetica Neue (font stack)', 'bon' ),
			'stack'  => '"HelveticaNeue-Light", "Helvetica Neue Light", "Helvetica Neue", Helvetica, Arial, "Lucida Grande", sans-serif',
		)
	);

	$theme_fonts->add_font(
		array(
			'handle' => 'muli',
			'label'  => __( 'Muli', 'bon' ),
			'family' => 'Muli',
			'stack'  => "Muli, sans-serif",
			'type'   => 'google'
		)
	);

	$theme_fonts->add_font(
		array(
			'handle' => 'bebeas-neue-stack',
			'label' => 'Bebas Neue',
			'stack' => '"BebasNeue", sans-serif',

		)
	);


}

add_action('wp_enqueue_scripts', 'shandora_enqueue_scripts' );

function shandora_enqueue_scripts() {

	if( !is_page_template( 'page-templates/page-template-idx-details.php' ) ) {
		wp_register_script('googlemap3', 'http://maps.googleapis.com/maps/api/js?sensor=false', false, false, false);

		if( !wp_script_is('googlemap3', 'enqueued' ) ) {
			wp_enqueue_script( 'googlemap3' );
		}
	}
	
}


add_filter( 'bon_advanced_menu_color_mod', 'shandora_menu_color_mod' );

function shandora_menu_color_mod( $mod ) {

	return bon_get_option('main_header_nav_style', 'dark');
}
?>