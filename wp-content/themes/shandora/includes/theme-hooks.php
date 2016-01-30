<?php
function shandora_setup_theme_hook() {

	$prefix = bon_get_prefix();
	$show_search = bon_get_option('enable_search_panel', 'yes');
	$show_top_menu = bon_get_option('show_top_menu', 'show');
	$show_footer_widget = bon_get_option('show_footer_widget', 'show');
	$show_footer_copyright = bon_get_option('show_footer_copyright', 'hide');
	$show_listing_meta = bon_get_option('show_listing_meta', 'yes');
	$_overlay_btns = bon_get_option( 'overlay_buttons', array('link' => true, 'gallery' => true, 'compare' => true, 'icon' => true, 'thumbnail' => true ) );

	if(!is_admin()) {

		add_action("{$prefix}head", "shandora_document_info", 1);

		add_action("{$prefix}before_loop", "shandora_get_page_header", 1);

		if( $show_search == 'yes' ) {
			add_action("{$prefix}before_loop", "shandora_search_get_listing", 2);
		}

		add_action("{$prefix}before_loop", "shandora_open_main_content_row", 5);

		add_action("{$prefix}before_loop", "shandora_get_left_sidebar", 10);

		add_action("{$prefix}before_loop", "shandora_open_main_content_column", 15 );

		add_action("{$prefix}before_loop", "shandora_listing_open_ul", 50 );

		add_action("{$prefix}before_pagination", "shandora_listing_close_ul", 1 );

		add_action("{$prefix}after_loop", "shandora_close_main_content_column", 1);

		add_action("{$prefix}after_loop", "shandora_get_right_sidebar", 5);

		add_action("{$prefix}after_loop", "shandora_close_main_content_row", 10);

		if( $show_top_menu == 'show' ) {
			add_action("{$prefix}header_content", "shandora_get_topbar_navigation", 1);
		}

		add_action("{$prefix}header_content", "shandora_get_main_header", 5);

		add_action("{$prefix}header_content", "shandora_get_main_navigation", 10);

		add_action("{$prefix}after_header", "shandora_get_custom_header", 1);

		add_action("{$prefix}footer", "shandora_get_footer", 1);
		
		if( $show_footer_widget != 'hide' || $show_footer_copyright != 'hide' ) {
			add_action("{$prefix}footer_widget", "shandora_get_footer_backtop", 1);
		}

		if( $show_footer_widget == 'show' ) {
			add_action("{$prefix}footer_widget", "shandora_get_footer_widget", 5);
		}

		if( $show_footer_copyright == 'show' ) {
			add_action("{$prefix}footer_widget", "shandora_get_footer_copyright", 10);
		}

		add_action("{$prefix}before_single_entry_content", "shandora_listing_gallery", 5);


		if( $show_listing_meta == 'yes' ) {
			add_action("{$prefix}after_single_entry_content", "shandora_listing_meta", 5);
		}

		add_action("{$prefix}after_single_entry_content", "shandora_listing_spec_open", 10);

		add_action("{$prefix}after_single_entry_content", "shandora_listing_detail_tabs", 15);

		add_action("{$prefix}after_single_entry_content", "shandora_listing_video", 20);
		
		add_action("{$prefix}after_single_entry_content", "shandora_listing_spec_close", 25);

		add_action("{$prefix}after_single_entry_content", "shandora_car_listing_video", 30);

		add_action("{$prefix}after_single_entry_content", "shandora_listing_dpe_ges", 32);

		add_action("{$prefix}after_single_entry_content", "shandora_listing_map", 35);

		add_action("{$prefix}after_single_entry_content", "shandora_listing_related", 45);

		add_action("{$prefix}after_single_entry_content", "shandora_listing_footer", 65);

		add_action("{$prefix}entry_summary", "shandora_listing_entry_title", 5);

		if( isset( $_overlay_btns['icon'] ) && $_overlay_btns['icon'] == true ) {
			add_action("{$prefix}entry_summary", "shandora_listing_entry_meta", 10);
		}

		add_action("{$prefix}entry_summary", "shandora_listing_list_view_summary", 15);

		add_filter( 'posts_where', 'shandora_posts_where', 10, 2 );

		if( bon_get_option( 'exclude_sold_rented', 'no') == 'yes' ) {
			add_filter( 'posts_where', 'shandora_exclude_sold_rented', 50, 2 );
		}

		add_filter('bon_og_meta_title', 'shandora_meta_opengraph_title');

	}

}

add_action( 'after_setup_theme', 'shandora_setup_theme_hook', 100);


function shandora_posts_where( $where, &$wp_query ) {
    global $wpdb;

    if ( $post_title = $wp_query->get( 'post_title' ) ) {
        $where .= ' AND ' . $wpdb->posts . '.post_title LIKE \'%' . esc_sql( like_escape( $post_title ) ) . '%\'';
    }
    return $where;
}

function shandora_exclude_sold_rented( $where, &$wp_query ) {
	global $wpdb;

	$where .= ' AND ' . $wpdb->posts .'.ID NOT IN (SELECT DISTINCT post_id FROM ' . $wpdb->postmeta . ' WHERE meta_key = \'' .  esc_sql( esc_attr( bon_get_prefix() . 'listing_status' ) ) . '\' AND ( meta_value = \'' . esc_sql( esc_attr( 'sold' ) ) . '\'  OR meta_value = \''. esc_sql( esc_attr( 'rented' ) ) .'\' ) )';
	return $where;
}

function shandora_listing_list_view_summary() {
	global $post;

	if( !isset($_GET['view'] ) || $_GET['view'] == 'grid' ) {
		return '';
	}
	
	echo '<div class="hide-for-small">';

	the_excerpt();
	
	echo '</div>';

	$meta = shandora_entry_meta();

	echo apply_atomic( 'listing_list_view_entry_meta' , $meta );
}


function shandora_entry_meta() {

	global $post;
	
	$suffix = 'listing_';

	$html = apply_atomic( 'entry_meta_icon' , '');

	if( $html != '' ) {
		return $html;
	}

	$view = isset( $_GET['view'] ) ? $_GET['view'] : 'grid';

	if( get_post_type() === 'listing') {

		$sizemeasurement = bon_get_option('measurement');
		$bed = shandora_get_meta($post->ID, 'listing_bed');
	    $bath = shandora_get_meta($post->ID, 'listing_bath');
	    $lotsize = shandora_get_meta($post->ID, 'listing_buildingsize');
	    $rooms = shandora_get_meta( $post->ID, 'listing_totalroom');
	    $garage = shandora_get_meta( $post->ID, 'listing_garage');
	   

		$html = '<div class="entry-meta">';

			if ( $bed ) {

				$html .= '<div class="icon bed"><i class="' . apply_atomic('bed_icon','sha-bed') . '"></i>';
				$html .= '<span>';
				$html .= sprintf( _n('%s Bed','%s Beds', $bed , 'bon'), $bed );
				$html .= '</span>';
				$html .= '</div>';

			}

			if ( $bath ) {
				$html .= '<div class="icon bath"><i class="' . apply_atomic('bath_icon','sha-bath') . '"></i>';
				$html .= '<span>';
				$html .= sprintf( _n('%s Bath','%s Baths', $bath , 'bon'), $bath );
				$html .= '</span>';
				$html .= '</div>';
			}

			if( $view == 'list' ) {

				if ( $garage ) {
					$html .= '<div class="icon garage"><i class="' . apply_atomic('garage_icon','sha-car') . '"></i>';
					$html .= '<span>';
					$html .= sprintf( _n('%s Garage','%s Garages', $garage , 'bon'), $garage );
					$html .= '</span>';
					$html .= '</div>';
				}

				if ( $rooms ) {
					$html .= '<div class="icon room"><i class="' . apply_atomic('room_icon','sha-building') . '"></i>';
					$html .= '<span>';
					$html .= sprintf( _n('%s Room','%s Rooms', $rooms , 'bon'), $rooms );
					$html .= '</span>';
					$html .= '</div>';
				}

			}

			if( $lotsize ) {

				$html .= '<div class="icon size"><i class="' . apply_atomic('size_icon','sha-ruler') . '"></i>';
				$html .= '<span>';
				$html .= ($lotsize) ? $lotsize . ' ' . $sizemeasurement : __('Unspecified','bon');
				$html .= '</span>';
				$html .= '</div>';
				
			}

		$html .= '</div>';


	} else if( get_post_type() == 'car-listing') {

		$transmission = shandora_get_meta($post->ID, $suffix . 'transmission');
    	$engine = shandora_get_meta($post->ID, $suffix . 'enginesize');
    	$mileage = shandora_get_meta($post->ID, $suffix . 'mileage');

    	$trans_opt = shandora_get_car_search_option('transmission');
	    if(array_key_exists($transmission, $trans_opt)) {
	    	$transmission = $trans_opt[$transmission];
	    }

		$html = '<div class="entry-meta">';

			$html .= '<div class="icon engine"><i class="' . apply_atomic('engine_icon','sha-engine') . '"></i>';
			$html .= '<span>';
			$html .= ($engine) ? $engine : __('Unspecified','bon');
			$html .= '</span>';
			$html .= '</div>';

			$html .= '<div class="icon transmission"><i class="' . apply_atomic('transmission_icon','sha-gear-shifter') . '"></i>';
			$html .= '<span>';
			$html .= ($transmission) ? $transmission : __('Unspecified','bon');
			$html .= '</span>';
			$html .= '</div>';

			$html .= '<div class="icon mileage"><i class="' . apply_atomic('mileage_icon','bonicons bi-dashboard') . '"></i>';
			$html .= '<span>';
			$html .= ($mileage) ? $mileage : __('Unspecified','bon');
			$html .= '</span>';
			$html .= '</div>';

		$html .= '</div>';

	} else if( get_post_type() == 'boat-listing' ) {

		$length = shandora_get_meta($post->ID, 'listing_length');
    	$speed = shandora_get_meta($post->ID, 'listing_speed');
    	$fuel = shandora_get_meta( $post->ID, 'listing_fuelcaps');
    	$people = shandora_get_meta( $post->ID, 'listing_people_cap');
		$terms = get_the_terms( $post->ID, 'boat-engine' );
		$engine_types = array();
		$engine_type = '';
		if ( $terms && ! is_wp_error( $terms ) ) 
		{														   														   
			   foreach ( $terms as $term ) {															   
					$engine_types[] = $term->name;
					break;
			   }														   													   														   
		}

		if( count( $engine_types ) > 0 ) {
			$engine_type = join( ' ', $engine_types );
		}

		$html = '<div class="entry-meta">';

			if( $engine_type ) {

				$html .= '<div class="icon engine"><i class="' . apply_atomic('engine_icon','sha-engine') . '"></i> ';
				$html .= '<span>';
				$html .= $engine_type;
				$html .= '</span>';
				$html .= '</div>';
			}
			
			if( $length ) {
				$html .= '<div class="icon transmission"><i class="' . apply_atomic('length_icon','sha-ruler') . '"></i> ';
				$html .= '<span>';
				$html .= $length . ' ' . bon_get_option( 'length_measure' ) ;
				$html .= '</span>';
				$html .= '</div>';
			}
			
			if( $speed ) {

				$html .= '<div class="icon speed"><i class="' . apply_atomic('speed_icon','bonicons bi-dashboard') . '"></i>';
				$html .= '<span>';
				$html .= $speed . ' ' . bon_get_option( 'speed_measure' );
				$html .= '</span>';
				$html .= '</div>';

			}

			if( $view == 'list' ) {

				if ( $people ) {
					$html .= '<div class="icon people"><i class="' . apply_atomic('people_icon','sha-users') . '"></i>';
					$html .= '<span>';
					$html .= $people . ' ' . __('People', 'bon');
					$html .= '</span>';
					$html .= '</div>';
				}

				if ( $fuel ) {
					$html .= '<div class="icon room"><i class="' . apply_atomic('fuel_icon','sha-tint') . '"></i>';
					$html .= '<span>';
					$html .= $fuel . ' ' . bon_get_option( 'volume_measure' );
					$html .= '</span>';
					$html .= '</div>';
				}

			}

		$html .= '</div>';
	}

	return $html;
}
/**
 * Get Entry Title
 *
 * @since 1.3.5
 * @return void
 *
 */
function shandora_listing_entry_title() {

	global $post;
	
	$price = '';

	if( isset( $_GET['view'] ) && $_GET['view'] == 'list' ) {
		$price = '<a href="'.get_permalink( $post->ID ).'" title="'.the_title_attribute( array('before' => __('Permalink to ','bon'), 'echo' => false) ).'"><span class="price">'. shandora_get_listing_price( false ) .'</span></a>';
	}

	echo apply_atomic_shortcode( 'entry_title', the_title( '<h3 class="entry-title" itemprop="name"><a href="'.get_permalink().'" title="'.the_title_attribute( array('before' => __('Permalink to ','bon'), 'echo' => false) ).'">', '</a>'.$price.'</h3>', false ) );

	
}

/**
 * Get Entry Meta
 *
 * @since 1.3.5
 * @return void
 *
 */
function shandora_listing_entry_meta() {
	if( isset( $_GET['view'] ) && $_GET['view'] == 'list' ) {
		return '';
	}

	$meta = shandora_entry_meta();


	echo apply_atomic( 'listing_entry_meta', $meta );
}


/**
 * Get Gallery Template
 *
 * @since 1.3.5
 * @return void
 *
 */
function shandora_listing_gallery() {
	bon_get_template_part('block','listinggallery');
}

/**
 * Get Listing Meta Icons
 *
 * @since 1.3.5
 * @return void
 *
 */
function shandora_listing_meta() { ?>
	
	<div class="entry-meta" itemprop="description">
		<?php 
			//bon_get_template_part('block',  ( is_singular( 'car-listing' ) ? 'carlistingmeta' : 'listingmeta' ) ); 
			bon_get_template_part( 'block', trailingslashit( get_post_type() ) . 'meta' );
		?>
	</div>

<?php }

/**
 * Get Listing Video
 *
 * @since 1.3.5
 * @return void
 *
 */
function shandora_listing_video() { $vid = shandora_get_video(); ?>
	
	<?php if( is_singular('listing') ) { ?>
			<div id="listing-video"  class="column large-6">
				<?php echo $vid; ?>
			</div>
	<?php } ?>

<?php
}


/**
 * Get Listing Video
 *
 * @since 1.3.5
 * @return void
 *
 */
function shandora_car_listing_video() { ?>
	
	<?php  if( get_post_type() == 'car-listing' || get_post_type() == 'boat-listing' ) { ?>
		
		<div class="row">
			<?php $vid = shandora_get_video(); ?>
			<div id="listing-video"  class="column large-12">
				<?php echo $vid; ?>
			</div>
		</div>

	<?php } ?>

<?php
}

/**
 * Get Details Tab
 *
 * @since 1.3.5
 * @return void
 *
 */
function shandora_listing_detail_tabs() {
	
			$vid = shandora_get_video();
			$detail_class = 'large-6';
		if ( empty($vid) || is_singular( 'car-listing' ) || is_singular('boat-listing') ) {
			$detail_class = "large-12";
		} ?>
		<div id="detail-tab" class="column <?php echo $detail_class; ?>">
			<?php 
				//bon_get_template_part('block', ( is_singular( 'car-listing' ) ? 'carlistingtab' : 'listingtab' ) ); 
				bon_get_template_part( 'block', trailingslashit( get_post_type() ) . 'tab' );
			?>
		</div>
<?php		
}


/**
 * Get Before Specification open div
 *
 * @since 1.3.5
 * @return void
 *
 */
function shandora_listing_spec_open() {
	echo '<div class="row entry-specification">';
}

/**
 * Close Specification div
 *
 * @since 1.3.5
 * @return void
 *
 */
function shandora_listing_spec_close() {
	echo '</div>';
}


function shandora_listing_dpe_ges() {
	global $post;
	if ( bon_get_option('enable_dpe_ges', false) == 'yes' ) { 

			$dpe = shandora_get_meta( $post->ID, 'listing_dpe');
			$ges = shandora_get_meta( $post->ID, 'listing_ges');

			$dpe_output = '<div class="dpe-ges-val"><span class="val">'. $dpe .'</span><span class="val-desc">kWh/m<sup>2</sup>/an</span></div>';
			$ges_output = '<div class="dpe-ges-val"><span class="val">'. $ges .'</span><span class="val-desc">KgeqCO2/m<sup>2</sup>.an</span></div>';
		
		?>

		<div class="row entry-dpe-ges">
			<?php if( $dpe ) { ?>
			<div class="column large-6">
				
				<h4 class="subheader"><?php _e('Logement économe', 'bon' ); ?></h4>
				
					<div class="dpe-container dpe-ges-container">
						<div class="base-val-container clear <?php if( $dpe <= 50 ) { echo 'active'; } ?>">
							<div class="dpe-a dpe-ges-grade">
								<span class="base-val">&le; 50</span>
								<span class="base-grade">A</span>
							</div>
							<?php if( $dpe <= 50 ) {
								echo $dpe_output;
							} ?>
						</div>
						<div class="base-val-container clear <?php if( $dpe >= 51 && $dpe <= 90 ) { echo 'active'; } ?>">
							<div class="dpe-b dpe-ges-grade">
								<span class="base-val">51 &aacute; 90</span>
								<span class="base-grade">B</span>
							</div>
							<?php if( $dpe >= 51 && $dpe <= 90 ) {
								echo $dpe_output;
							} ?>
						</div>
						<div class="base-val-container clear <?php if( $dpe >= 91 && $dpe <= 150 ) { echo 'active'; } ?>">
							<div class="dpe-c dpe-ges-grade">
								<span class="base-val">91 &aacute; 150</span>
								<span class="base-grade">C</span>
							</div>	
							<?php if( $dpe >= 91 && $dpe <= 150 ) {
								echo $dpe_output;
							} ?>
						</div>
						<div class="base-val-container clear <?php if( $dpe >= 151 && $dpe <= 230 ) { echo 'active'; } ?>">
							<div class="dpe-d dpe-ges-grade">
								<span class="base-val">151 &aacute; 230</span>
								<span class="base-grade">D</span>
							</div>
							<?php if( $dpe >= 151 && $dpe <= 230 ) {
								echo $dpe_output;
							} ?>	
						</div>
						<div class="base-val-container clear <?php if( $dpe >= 231 && $dpe <= 330 ) { echo 'active'; } ?>">
							<div class="dpe-e dpe-ges-grade">
								<span class="base-val">231 &aacute; 330</span>
								<span class="base-grade">E</span>
							</div>
							<?php if( $dpe >= 231 && $dpe <= 330 ) {
								echo $dpe_output;
							} ?>	
						</div>
						<div class="base-val-container clear <?php if( $dpe >=331 && $dpe <=450 ) { echo 'active'; } ?>">
							<div class="dpe-f dpe-ges-grade">
								<span class="base-val">331 &aacute; 450</span>
								<span class="base-grade">F</span>
							</div>
							<?php if( $dpe >=331 && $dpe <=450 ) {
								echo $dpe_output;
							} ?>
						</div>
						<div class="base-val-container clear <?php if( $dpe > 451 ) { echo 'active'; } ?>">
							<div class="dpe-g dpe-ges-grade">
								<span class="base-val">&gt; 450</span>
								<span class="base-grade">G</span>
							</div>
							<?php if( $dpe >= 451 ) {
								echo $dpe_output;
							} ?>
						</div>
					</div>

				<h4 class="subheader"><?php _e('Logement énergivore', 'bon' ); ?></h4>

			</div>
			<?php } ?>
			<?php if( $ges ) { ?>
			<div class="column large-6">

				<h4 class="subheader"><?php _e('Faible émission de GES', 'bon' ); ?></h4>
				
					<div class="ges-container dpe-ges-container">
						<div class="base-val-container clear <?php if( $ges <= 5 ) { echo 'active'; } ?>">
							<div class="ges-a dpe-ges-grade">
								<span class="base-val">&le; 5</span>
								<span class="base-grade">A</span>
							</div>
							<?php if( $ges <= 5 ) {
								echo $ges_output;
							} ?>
						</div>
						<div class="base-val-container clear <?php if( $ges >= 6 && $ges <= 10 ) { echo 'active'; } ?>">
							<div class="ges-b dpe-ges-grade">
								<span class="base-val">6 &aacute; 10</span>
								<span class="base-grade">B</span>
							</div>
							<?php if( $ges >= 6 && $ges <= 10 ) {
								echo $ges_output;
							} ?>
						</div>
						<div class="base-val-container clear <?php if( $ges >= 11 && $ges <= 20 ) { echo 'active'; } ?>">
							<div class="ges-c dpe-ges-grade">
								<span class="base-val">11 &aacute; 20</span>
								<span class="base-grade">C</span>
							</div>	
							<?php if( $ges >= 11 && $ges <= 20 ) {
								echo $ges_output;
							} ?>
						</div>
						<div class="base-val-container clear <?php if( $ges >= 21 && $ges <= 35 ) { echo 'active'; } ?>">
							<div class="ges-d dpe-ges-grade">
								<span class="base-val">21 &aacute; 35</span>
								<span class="base-grade">D</span>
							</div>
							<?php if( $ges >= 21 && $ges <= 35 ) {
								echo $ges_output;
							} ?>	
						</div>
						<div class="base-val-container clear <?php if( $ges >= 36 && $ges <= 55 ) { echo 'active'; } ?>">
							<div class="ges-e dpe-ges-grade">
								<span class="base-val">36 &aacute; 55</span>
								<span class="base-grade">E</span>
							</div>
							<?php if( $ges >= 36 && $ges <= 55 ) {
								echo $ges_output;
							} ?>	
						</div>
						<div class="base-val-container clear <?php if( $ges >=56 && $ges <=80 ) { echo 'active'; } ?>">
							<div class="ges-f dpe-ges-grade">
								<span class="base-val">56 &aacute; 80</span>
								<span class="base-grade">F</span>
							</div>
							<?php if( $ges >=56 && $ges <=80 ) {
								echo $ges_output;
							} ?>
						</div>
						<div class="base-val-container clear <?php if( $ges > 80 ) { echo 'active'; } ?>">
							<div class="ges-g dpe-ges-grade">
								<span class="base-val">&gt; 80</span>
								<span class="base-grade">G</span>
							</div>
							<?php if( $ges >= 80 ) {
								echo $ges_output;
							} ?>
						</div>
					</div>

				<h4 class="subheader"><?php _e('Forte émission de GES', 'bon' ); ?></h4>

			</div>
			<?php } ?>
		</div>
	<?php }
}
/**
 * Get Listing Map
 *
 * @since 1.3.5
 * @return void
 *
 */
function shandora_listing_map() { 
	if ( is_singular( 'listing' ) ) {

	global $post; ?>
	<div class="listing-map">
		<?php 
		$latitude = shandora_get_meta($post->ID, 'listing_maplatitude');
		$longitude = shandora_get_meta($post->ID, 'listing_maplongitude');

		if( !empty($latitude) && !empty($longitude) ) {
			echo apply_atomic_shortcode('listing_map','[bt-map color="blue" latitude="'.$latitude.'" longitude="'.$longitude.'" zoom="16" width="100%" height="400px"]');
		}
		?>
	</div>
<?php 
	}
}

/**
 * Get Related Listing
 *
 * @since 1.3.5
 * @return void
 *
 */
function shandora_listing_related() {
	if( is_singular( 'listing') && bon_get_option('show_related', 'yes') == 'yes' ) {
		bon_get_template_part('block', 'related'); 
	}
}

/**
 * Get Listing Footer
 *
 * @since 1.3.5
 * @return void
 *
 */
function shandora_listing_footer() {

	$show_agent_details = bon_get_option('show_agent_details', 'yes');
	$show_contact_form = bon_get_option('show_contact_form', 'yes');

	if( $show_agent_details == 'no' && $show_contact_form == 'no' )
		return;

	bon_get_template_part( 'block', trailingslashit( get_post_type() ) . 'footer' );
}

if( !function_exists('shandora_document_info') ) {
	
	function shandora_document_info() {
		?>
		<!DOCTYPE html>
		<!--[if IE 7 ]><html class="ie ie7" lang="en"> <![endif]-->
		<!--[if IE 8 ]><html class="ie ie8" lang="en"> <![endif]-->
		<!--[if (gte IE 9)|!(IE)]><!-->
		<html <?php language_attributes(); ?>>
		<head>
			
			
			<?php bon_doctitle(); ?>
			<link rel="profile" href="http://gmpg.org/xfn/11" />
			<?php $favico = bon_get_option('favicon', trailingslashit( BON_THEME_URI ) . 'assets/images/icon.png'); ?>
			<link rel="shortcut icon" href="<?php echo $favico; ?>" type="image/x-icon" />

			<?php wp_head(); // wp_head ?>

		</head>
		<?php
	}

}


if( !function_exists('shandora_get_page_header') ) {

	function shandora_get_page_header() {
		if(!shandora_is_home() ) {
			$show_page_header = bon_get_option('show_page_header');

			if($show_page_header == 'show') {
				bon_get_template_part('block', 'pageheader');
			}
		} else if( shandora_is_home() && bon_get_option( 'show_slider', 'show') == 'hide' ) {
			$show_page_header = bon_get_option('show_page_header');

			if($show_page_header == 'show') {
				bon_get_template_part('block', 'pageheader');
			}
		}
	}

}

if( !function_exists('shandora_search_get_listing') ) {

	function shandora_search_get_listing() {

		if( shandora_is_home() ||
			is_singular('listing') || is_singular('agent') || is_singular('car-listing') || is_singular( 'boat-listing' ) ||
			is_page_template('page-templates/page-template-all-agent.php') ||
			is_page_template('page-templates/page-template-all-listings.php') ||
			is_page_template('page-templates/page-template-all-car-listings.php') ||
			is_page_template('page-templates/page-template-compare-car-listings.php') ||
			is_page_template('page-templates/page-template-search-car-listings.php') ||
			is_page_template('page-templates/page-template-idx.php' ) ||
			is_page_template('page-templates/page-template-idx-details.php' ) ||
			is_page_template('page-templates/page-template-search-listings.php') ||
			is_page_template('page-templates/page-template-compare-listings.php') ||
			is_page_template('page-templates/page-template-property-status.php') ||
			is_page_template('page-templates/page-template-car-status.php') ||
			is_page_template('page-templates/page-template-all-boats.php') ||
			is_page_template('page-templates/page-template-search-boat.php') ||
			is_page_template('page-templates/page-template-compare-boat.php') ||
		 	is_tax( get_object_taxonomies('listing') ) ||
		 	is_tax( get_object_taxonomies('car-listing' ) ) ||
		 	is_tax( get_object_taxonomies('boat-listing') ) ) {

			bon_get_template_part('block','searchlisting'); 
		}
	}

}

if( !function_exists('shandora_open_main_content_row') ) {


	function shandora_open_main_content_row() {

		echo '<div id="main-content" class="row">';
	}

}

if( !function_exists('shandora_get_left_sidebar') ) {


	function shandora_get_left_sidebar() {
		
		$layout = get_theme_mod('theme_layout');
		if(empty($layout)) {
			$layout = get_post_layout(get_queried_object_id());
		}
		if( $layout == '2c-r') {
			if( is_singular( 'listing' ) || is_singular( 'car-listing' ) || is_singular( 'boat-listing' ) ) {
				get_sidebar('singularlisting');
			} else if( get_post_type() == 'listing' || get_post_type() == 'car-listing' || 
				is_page_template('page-templates/page-template-all-listings.php') ||
				is_page_template('page-templates/page-template-all-car-listings.php') || 
				is_page_template('page-templates/page-template-search-car-listings.php') ||
				is_page_template('page-templates/page-template-property-status.php') ||
				is_page_template('page-templates/page-template-car-status.php') ||
				is_page_template('page-templates/page-template-all-boats.php') ||
				is_page_template('page-templates/page-template-search-boat.php') ||
				is_page_template('page-templates/page-template-search-listings.php') ) {
				get_sidebar('secondary');
			}  else {
				get_sidebar('primary');
			}
		}
	}
}

if( !function_exists('shandora_open_main_content_column') ) {
	
	
	function shandora_open_main_content_column() {

		if(is_page_template( 'page-templates/page-template-home.php' ) ) {
			echo '<div class="column large-12">';
		} else {

			$layout = get_theme_mod('theme_layout');
			if(empty($layout)) {
				$layout = get_post_layout(get_queried_object_id());
			}
			if( $layout == '1c') {
				echo '<div class="'.shandora_column_class().'">';
			} else {				
				echo '<div class="'.shandora_column_class('large-8').'">';
			}
		}
	}
}

function shandora_get_site_url() {

    $protocol = ( !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    $domain = $_SERVER['HTTP_HOST'];

    return  $protocol . $domain;
}

if( !function_exists('shandora_listing_open_ul') ) {

	
	function shandora_listing_open_ul() {
		$compare_page = bon_get_option('compare_page');
		
		if( ( is_page_template('page-templates/page-template-property-status.php') ||  
			  is_page_template('page-templates/page-template-car-status.php') || get_post_type() == 'listing' || get_post_type() == 'boat-listing' ||
			  get_post_type() == 'car-listing' || is_page_template('page-templates/page-template-all-listings.php') ||
			  is_page_template('page-templates/page-template-all-car-listings.php') || 
			  is_page_template('page-templates/page-template-search-listings.php') ||
			  is_page_template('page-templates/page-template-search-boat.php') || 
			  is_page_template('page-templates/page-template-all-boats.php') ||
			  is_page_template('page-templates/page-template-search-car-listings.php')) 
			  && !is_singular('listing') && !is_singular( 'car-listing' ) && !is_singular( 'boat-listing' ) && !is_search() ) {
			
			$show_map = 'no';
			$show_listing_count = bon_get_option('show_listing_count', 'no');

			if( ( is_page_template('page-templates/page-template-property-status.php') || get_post_type() == 'listing' || is_page_template('page-templates/page-template-all-listings.php')
			|| is_page_template('page-templates/page-template-search-listings.php')) && !is_singular('listing') &&  !is_singular( 'car-listing' ) && !is_singular('boat-listing') ) {
				$show_map = bon_get_option('show_listings_map');
			}
		?>
		<div class="listing-header">
		<div class="row">
		
		<?php
		if($show_listing_count) {
			echo '<div class="column large-6"><h3 id="listed-property"></h3></div>';
		}
		?>
		
		<?php 
		$search_order = isset($_GET['search_order']) ? $_GET['search_order'] : bon_get_option('listing_order', 'DESC');
		$search_orderby = isset($_GET['search_orderby']) ? $_GET['search_orderby'] : bon_get_option('listing_orderby', 'date');

		?>

			<div class="column large-6 right">

				<div class="row">
					<div class="column large-3">
						<?php
							$view = isset( $_GET['view'] ) ? $_GET['view'] : 'grid';
							$newurl = '';
							foreach ($_GET as $variable => $value ) {
								if( $variable != 'view' ) {
						           $newurl .= $variable.'='.$value.'&';
						        }
							}
							$newurl = rtrim($newurl,'&');
							if( empty( $newurl) ) {
								$uri = shandora_get_site_url() . strtok($_SERVER["REQUEST_URI"],'?');
								$newurl =  $uri . '?view=';
							} else {
								$uri = shandora_get_site_url() . strtok($_SERVER["REQUEST_URI"],'?');
								$newurl = $uri . '?' . $newurl . '&view=';
							}
						?>
						<a class="view-button button blue flat view-grid <?php echo ( $view == 'grid' ) ? 'selected' : ''; ?> " href="<?php echo $newurl . 'grid'; ?>"><i class="bonicons bi-th"></i></a>
						<a class="view-button button blue flat view-list <?php echo ( $view == 'list' ) ? 'selected' : ''; ?>" href="<?php echo $newurl . 'list'; ?>"><i class="bonicons bi-list"></i></a>
					</div>
					<div class="column large-9">
						<form class="custom" action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="get" id="orderform" name="orderform">
				            
				            <div class="row">
				                <div class="column large-6 search-order">
				                    <select class="no-mbot" name="search_order" onChange="document.forms['orderform'].submit()">
				                        <option value="ASC" <?php selected( $search_order, 'ASC' );?> ><?php _e('Ascending','bon'); ?></option>
				                        <option value="DESC" <?php selected( $search_order, 'DESC' );?> ><?php _e('Descending','bon'); ?></option>
				                    </select>
				                </div>
				                <div class="column large-6 search-order">
				                    <select class="no-mbot" name="search_orderby" onChange="document.forms['orderform'].submit()">
				                        <option value="price" <?php selected( $search_orderby, 'price' );?> ><?php _e('Price','bon'); ?></option>
				                        <option value="date" <?php selected( $search_orderby, 'date' );?> ><?php _e('Date','bon'); ?></option>
				                        <option value="title" <?php selected( $search_orderby, 'title' );?> ><?php _e('Title','bon'); ?></option>
				                        <option value="size" <?php selected( $search_orderby, 'size' );?> >
				                        	<?php 
				                        		if( get_post_type() == 'listing' || is_page_template( 'page-templates/page-template-search-listings.php' ) || is_page_template( 'page-templates/page-template-all-listings.php') ) {
				                        			echo __('Size', 'bon');
				                        		} else if( get_post_type() == 'car-listing' || is_page_template( 'page-templates/page-template-search-car-listings.php' ) || is_page_template( 'page-templates/page-template-all-car-listings.php') ) {
				                        			echo __('Mileage', 'bon');
				                        		} else if( get_post_type() == 'boat-listing' || is_page_template( 'page-templates/page-template-search-boat.php' ) || is_page_template( 'page-templates/page-template-all-boats.php') ) {
				                        			echo __('Length', 'bon');
				                        		}
				                        	?>
				                        </option>
				                    </select>
				                </div>
					                <?php 

						                foreach($_GET as $name => $value) {
									  	  if($name != 'search_order' && $name != 'search_orderby') {
									  	  	$name = htmlspecialchars($name);
											  $value = htmlspecialchars($value);
											  echo '<input type="hidden" name="'. $name .'" value="'. $value .'">';
									  	  }
										}
									?>
				            </div>
				        </form>
				    </div>
				</div>
			</div>
    	</div>
    	</div>
		<?php	

		if($show_map == 'show' ) {
			$show_zoom = bon_get_option('show_listings_map_zoom', 'false' );
			if( $show_zoom == 'show' ) { $show_zoom = 'true'; }

			$show_type = bon_get_option('show_listings_map_type', 'false');
			if( $show_type == 'show' ) { $show_type = 'true'; }

	        echo '<div id="listings-map" data-show-zoom="'.$show_zoom.'" data-show-map-type="'.$show_type.'"></div>';
	    }
	    ?>
		<ul class="listings <?php echo ( isset( $_GET['view'] ) && $_GET['view'] == 'list' ) ? 'list-view' : shandora_block_grid_column_class( false ); ?>" data-compareurl="<?php echo trailingslashit( get_permalink($compare_page) ); ?>">
		<?php
		}
	}
}

if( !function_exists('shandora_listing_close_ul') ) {


	
	function shandora_listing_close_ul() {

		if( (get_post_type() == 'listing' || get_post_type() == 'car-listing' || get_post_type() == 'boat-listing' || is_page_template('page-templates/page-template-all-listings.php') || 
			is_page_template('page-templates/page-template-search-boat.php') || is_page_template('page-templates/page-template-all-boats.php') ||
			is_page_template('page-templates/page-template-all-car-listings.php') || is_page_template('page-templates/page-template-car-status.php') || is_page_template('page-templates/page-template-property-status.php') ||
			is_page_template('page-templates/page-template-search-car-listings.php') 
		|| is_page_template('page-templates/page-template-search-listings.php')) && !is_singular('listing') && !is_singular( 'car-listing' ) && !is_singular( 'boat-listing' ) && !is_search() ) {
		
		
		?>
		</ul>

		<?php
		
		}
	}
}


if( !function_exists('shandora_close_main_content_column') ) {



	function shandora_close_main_content_column() {
		echo '</div><!-- close column -->';
	}
}

if( !function_exists('shandora_get_right_sidebar') ) {

	function shandora_get_right_sidebar() {
		$layout = get_theme_mod('theme_layout');
		if(empty($layout)) {
			$layout = get_post_layout(get_queried_object_id());
		}
		if( $layout == '2c-l' ) {
			if( is_singular( 'listing' ) || is_singular( 'car-listing' ) || is_singular( 'boat-listing' ) ) {
				get_sidebar('singularlisting');
			} else if( get_post_type() == 'listing' || get_post_type() == 'car-listing' || 
				is_page_template('page-templates/page-template-all-listings.php') ||
				is_page_template('page-templates/page-template-all-car-listings.php') || 
				is_page_template('page-templates/page-template-search-car-listings.php') ||
				is_page_template('page-templates/page-template-property-status.php') ||
				is_page_template('page-templates/page-template-car-status.php') ||
				is_page_template('page-templates/page-template-search-listings.php')  ) {
				get_sidebar('secondary');
			}  else {
				get_sidebar('primary');
			}
		}
	}
}

if( !function_exists('shandora_close_main_content_row') ) {




	function shandora_close_main_content_row() {
		
		echo '</div><!-- close row -->';
	}

}


if( !function_exists('shandora_get_topbar_navigation') ) {


	function shandora_get_topbar_navigation() {
		?>

		<hgroup id="topbar-navigation" class="hide-for-small">
			<div class="row">
				<?php bon_get_template_part( 'menu', 'topbar' ); // Loads the menu-primary.php template. ?>
				<?php 
					$enable_header_social = bon_get_option('enable_header_social', 'yes');

					if($enable_header_social == 'yes') {
						shandora_get_social_icons();
					} else if ( $enable_header_social != 'yes' && function_exists( 'icl_get_languages') ) {
						echo '<nav class="large-6 column right">';
						echo '<ul id="top-social-icons" class="social-icons right">'. shandora_get_country_selection() . '</ul>';
						echo '</nav>';
					}
					 
				?>
				
			</div>
		</hgroup>

		<?php
	}
}

if( !function_exists('shandora_get_main_header') ) {

	function shandora_get_main_header() {
		$header_style = bon_get_option('main_header_style', 'dark');
		$state = bon_get_option('show_main_header', 'show');
		$header_col_1 = bon_get_option('enable_header_col_1');
		$header_col_2 = bon_get_option('enable_header_col_2');
		$center_logo = bon_get_option('centering_logo');

		$header_col_class = 'large-9';
		$col_class = 'large-6';
		$logo_class = 'uncentered';
		$logo_col_class = 'large-3';
		if( $header_col_1 == true && $header_col_2 == true ) {
			$header_col_class = 'large-9';
			$col_class = 'large-6';
		} else if( $header_col_1 == true && $header_col_2 == false ) {
			$header_col_class = 'large-5';
			$col_class = 'large-12';
		} else if( $header_col_1 == false && $header_col_2 == true ) {
			$header_col_class = 'large-5';
			$col_class = 'large-12';
		} else {

			$logo_class = 'full';
			$logo_col_class = 'large-12';
			if( $center_logo == true ) {
				$logo_class = 'centered';
			}
		}

		if( isset( $_COOKIE['header_state']) ) {
			$state = $_COOKIE['header_state'];
		}
	?>
		<hgroup id="main-header" class="<?php echo $header_style; ?> slide <?php echo $state; ?>">
			<div class="row">
				<?php $is_text = ((bon_get_option('logo') != '') ? false : true) ; ?>
				<div class="<?php echo $logo_col_class; ?> column small-centered large-<?php echo $logo_class; ?> <?php echo ($is_text) ? 'text-logo' : ''; ?>" id="logo">
					<div id="nav-toggle" class="navbar-handle show-for-small"></div>
					<?php
						$tag = 'h1';
						if( is_singular() && !is_home() && !is_front_page() ) {
							$tag = 'h3';
						}
					?>
					<<?php echo $tag; ?> itemprop="name" class="<?php echo $logo_class; ?>"><a href="<?php echo home_url(); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>"><?php if( bon_get_option('logo') ) { ?><img itemprop="image" src="<?php echo bon_get_option('logo', get_template_directory_uri() . '/assets/images/logo.png'); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>"/><?php } else { echo esc_attr( get_bloginfo( 'name', 'display' ) ); } ?></a></<?php echo $tag; ?>>
					<?php if( $is_text ) { ?>
						<span class="site-description <?php echo $logo_class; ?> hide-for-desktop hide-for-small"><?php echo get_bloginfo( 'description', 'display'); ?></span>
					<?php } ?>
				</div>
				
				<?php if( $header_col_1 == true || $header_col_2 == true ) : ?>
				<div class="<?php echo $header_col_class; ?> column hide-for-desktop hide-for-small" id="company-info">
					<div class="row">
						<?php if( $header_col_1 ) : ?>
							<div class="<?php echo $col_class; ?> column">
								<div class="icon">
									<span class="<?php echo apply_filters('shandora_head_phone_icon', 'sha-phone'); ?>"></span>
								</div>
								<span class="info-title"><?php echo esc_attr(bon_get_option('hgroup1_title')); ?></span>
								<?php 
								    $phone_html = '';
									$phone = explode( ',', esc_attr( bon_get_option('hgroup1_content') ) );
									$phone_count = count($phone);
									if( $phone_count  > 1 ) {
										foreach( $phone as $number ) {
											$phone_html .= '<strong>' . $number . '</strong>';
										}
									} else {
										$phone_html = '<strong>' . esc_attr( bon_get_option('hgroup1_content') ) . '</strong>';
									}
								?>
								<span class="phone phone-<?php echo $phone_count; ?>"><?php echo $phone_html; ?></span>
							</div>
						<?php endif; ?>
						<?php if( $header_col_2 ) : ?>
							<div class="<?php echo $col_class; ?> column">
								<div class="icon">
									<span class="<?php echo apply_filters('shandora_head_map_icon', 'sha-map'); ?>"></span>
								</div>
								<span class="info-title"><?php echo bon_get_option('hgroup2_title'); ?></span>
								<address>
									<p><span class="bonicons bi-home"></span><?php echo esc_attr(bon_get_option('hgroup2_line1')); ?></p>
									<p><span class="bonicons bi-clock-o"></span><?php echo esc_attr(bon_get_option('hgroup2_line2')); ?></p>
								</address>
							</div>
						<?php endif; ?>
					</div>
				</div>
				<?php endif; ?>

			</div>
		</hgroup> 
	<?php
	}
}

if( !function_exists('shandora_get_main_navigation') ) {



	function shandora_get_main_navigation() {

		$nav_style = bon_get_option('main_header_nav_style', 'dark');
		$no_search = 'no-search';
		?>
			<hgroup id="main-navigation" class="<?php echo $nav_style; ?>">
				<?php if( bon_get_option('show_header_search', 'yes') == 'yes' ) { $no_search = '';?>
					<div class="searchform-container">
						<?php shandora_get_searchform('header'); ?>
					</div>
				<?php } ?>
				<div class="nav-block <?php echo $no_search; ?>">
					<?php bon_get_template_part( 'menu', 'primary' ); // Loads the menu-primary.php template. ?>
				</div>
				<div class="header-toggler hide-for-small"><div class="toggler-button"></div></div>
			</hgroup>

		<?php
	}

}

if( !function_exists('shandora_get_custom_header') ) {

	function shandora_get_custom_header() {
		if(!shandora_is_home() ) :
		?>
			<div id="header-background" class="show-for-medium-up"></div>
		<?php
		elseif( shandora_is_home() && bon_get_option('show_slider','show') == 'hide' ) : ?>
			<div id="header-background" class="show-for-medium-up"></div>
		<?php 
		endif;
	}

}

if( !function_exists('shandora_get_footer') ) {


	function shandora_get_footer() {
		$lang = '';

		if( function_exists( 'icl_get_languages') ) {
			$lang = ICL_LANGUAGE_CODE;
		}
		?>
		<div id="action-compare" class="action-compare" data-lang="<?php echo $lang; ?>" data-count="0" data-compare=""></div>

		<?php shandora_scroll_top_button(); wp_footer(); ?>

		</body>
		</html>
		<?php
	}

}

if( !function_exists('shandora_get_footer_backtop') ) {


	function shandora_get_footer_backtop() {
		?>

		<a href="#totop" class="backtop" id="backtop" title="<?php _e('Back to Top', 'bon'); ?>"><i class="icon bonicons bi-chevron-up"></i></a>

		<?php
	}

}

if( !function_exists('shandora_get_footer_widget') ) {



	function shandora_get_footer_widget() {

		?>
		<div class="footer-widgets footer-inner">

			<div class="row">

				<?php for($i = 1; $i <= 4; $i++ ) { ?>

					<div id="footer-widget-<?php echo $i; ?>" class="<?php echo shandora_column_class("large-3"); ?>">
						
					<?php if ( is_active_sidebar( 'footer'.$i ) ) : ?>

						<?php dynamic_sidebar( 'footer'.$i ); ?>

					<?php else : ?>

						<!-- This content shows up if there are no widgets defined in the backend. -->
						
						<p><?php _e("Please activate some Widgets.", "framework");  ?></p>

					<?php endif; ?>

					</div>

				<?php } ?>

			</div>

		</div>

		<?php
	}

}

if( !function_exists('shandora_get_footer_copyright') ) {


	function shandora_get_footer_copyright() {
		?>
		<div class="footer-copyright footer-inner">

			<div class="row">
				<div class="column large-12 footer-column"><div class="row">
					<div id="social-icon-footer" class="large-4 column large-uncentered small-11 small-centered">
						<?php 

							$enable_footer_social = bon_get_option('enable_footer_social', 'yes');
							
							if($enable_footer_social == 'yes') {
								shandora_get_social_icons(false);
							} else if( $enable_footer_social != 'yes' && function_exists( 'icl_get_languages') ) {
								echo '<nav><ul id="footer-social-icons" class="social-icons">'. shandora_get_country_selection() . '</ul></nav>';
							} else {
								echo "&nbsp;";
							}


						?>
						
					</div>

					<div id="copyright-text" class="large-8 column large-uncentered small-11 small-centered">
						<div><?php echo bon_get_option('footer_copyright', apply_atomic_shortcode( 'footer_content', '<div class="credit">' . __( 'Copyright &copy; [the-year] [site-link]. Powered by [wp-link] and [theme-link].', 'bon' ) . '</div>') ); ?></div>
					</div>
				</div></div>
			</div>

		</div>
		<?php
	}
}

add_filter( 'body_class', 'shandora_filter_body_class' );

function shandora_filter_body_class( $classes ) {

	global $post;

	if( !isset( $post->ID) ) {
		return $classes;
	}
	$id = $post->ID;

	$class = shandora_get_meta( $id, 'slideshow_type' );

	if( !empty( $class ) && shandora_is_home() && bon_get_option('show_slider', 'show') == 'show' ) {
		$class = 'slider-' . $class;
		$classes[] = $class;
	} else {
		if( shandora_is_home() && bon_get_option('show_slider', 'show') == 'show' ) {
			$classes[] = 'slider-full';
		}
	}
	
	return $classes;
}

function shandora_is_home() {
	if( is_page_template( 'page-templates/page-template-home.php' ) || is_front_page() ) {
		return true;
	}

	return false;
}

function shandora_scroll_top_button() {
	echo '<a id="scroll-top" href="#totop"><i class="bonicons bi-chevron-up"></i></a>';
}

function shandora_meta_opengraph_title( $title ) {
	global $post;
	if( $post->post_type == 'listing' ) {
            $price = shandora_get_price_meta($post->ID);
            $title = $title . ' - ' . $price;
    }
    return $title;
}

?>