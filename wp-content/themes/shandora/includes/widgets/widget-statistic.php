<?php if ( ! defined( 'ABSPATH' ) ) exit('No direct script access allowed'); // Exit if accessed directly

/**
 * Widget Archive
 *
 *
 *
 * @author		Hermanto Lim
 * @copyright	Copyright (c) Hermanto Lim
 * @link		http://bonfirelab.com
 * @since		Version 1.0
 * @package 	BonFramework
 * @category 	Widgets
 *
 *
*/ 

/**
 * Archives widget class.
 *
 * @since 1.0
 */
class Shandora_Statistic_Widget extends WP_Widget {

	/**
	 * Set up the widget's unique name, ID, class, description, and other options.
	 *
	 * @since 1.2.0
	 */
	function __construct() {

		/* Set up the widget options. */
		$widget_options = array(
			'classname'   => 'listing-statistic',
			'description' => esc_html__( 'Show Listing Statistic.', 'bon' )
		);

		/* Set up the widget control options. */
		$control_options = array(
			
		);

		/* Create the widget. */
		$this->WP_Widget(
			'shandora-listing-statistic',               // $this->id_base
			__( 'Shandora Listing Statistic', 'bon' ), // $this->name
			$widget_options,                 // $this->widget_options
			$control_options                 // $this->control_options
		);
	}

	/**
	 * Outputs the widget based on the arguments input through the widget controls.
	 *
	 * @since 1.0
	 */
	function widget( $sidebar, $instance ) {
		extract( $sidebar );

		/* Set the $args for wp_get_archives() to the $instance array. */
		$args = $instance;

		/* Overwrite the $echo argument and set it to false. */
		$args['echo'] = false;

		/* Output the theme's $before_widget wrapper. */
		echo $before_widget;

		/* If a title was input by the user, display it. */
		if ( !empty( $instance['title'] ) )
			echo $before_title . apply_filters( 'widget_title',  $instance['title'], $instance, $this->id_base ) . $after_title;


		$term = '';
		$tax_query = array();
		$date_query = '';

			
		if( isset( $instance['type'] ) && isset( $instance['location'] ) ) {
			if( $instance['type'] = 'listing' ) {
				$term = get_term_by( 'slug', $instance['location'], 'property-location');
			} else if ( $instance['type'] = 'car-listing' ) {
				$term = get_term_by( 'slug', $instance['location'], 'dealer-location');
			}
		} 

		?>

		<?php if( !empty( $term ) ) { ?>

		<?php

			if( $instance['scope'] == 'weekly' ) {
				$date_query = date('Y-m-d', strtotime('-7 days'));
			} else {
				$date_query = date('Y-m-d', strtotime('-30 days'));
			}


		    global $wpdb;
			$sold_results = $wpdb->get_results( $wpdb->prepare( 
				"SELECT status.meta_value lstatus, count(DISTINCT posts.ID) cpost, 
						AVG(DISTINCT price.meta_value) avgvalue 
						FROM $wpdb->posts posts 
						INNER JOIN $wpdb->term_relationships tr 
						ON posts.ID = tr.object_id 
						INNER JOIN $wpdb->term_taxonomy t 
						ON tr.term_taxonomy_id = t.term_taxonomy_id 
						INNER JOIN $wpdb->postmeta price 
						ON posts.ID = price.post_id 
						AND price.meta_key = %s 
						INNER JOIN $wpdb->postmeta status 
						ON posts.ID = status.post_id 
						WHERE tr.term_taxonomy_id IN (%d) 
						AND posts.post_type = %s 
						AND (posts.post_status = 'publish' OR posts.post_status = 'private') 
						AND status.meta_key = %s 
						AND status.meta_value = 'sold' 
						AND posts.post_modified > %s 
						GROUP BY status.meta_value 
						ORDER BY posts.post_modified DESC
", bon_get_prefix() . 'listing_price', $term->term_taxonomy_id, $instance['type'], bon_get_prefix() . 'listing_status', $date_query ) );

			$other_results = $wpdb->get_results( $wpdb->prepare( 
				"SELECT count(DISTINCT posts.ID) cpost, 
						AVG(DISTINCT price.meta_value) avgvalue 
						FROM $wpdb->posts posts 
						INNER JOIN $wpdb->term_relationships tr 
						ON posts.ID = tr.object_id 
						INNER JOIN $wpdb->term_taxonomy t 
						ON tr.term_taxonomy_id = t.term_taxonomy_id 
						INNER JOIN $wpdb->postmeta price 
						ON posts.ID = price.post_id 
						AND price.meta_key = %s 
						INNER JOIN $wpdb->postmeta status 
						ON posts.ID = status.post_id 
						WHERE tr.term_taxonomy_id IN (%d) 
						AND posts.post_type = %s 
						AND (posts.post_status = 'publish' OR posts.post_status = 'private') 
						AND status.meta_key = %s 
						AND ( status.meta_value != 'sold' AND status.meta_value != 'rented' )
						AND posts.post_modified > %s 
						ORDER BY posts.post_modified DESC
", bon_get_prefix() . 'listing_price', $term->term_taxonomy_id, $instance['type'], bon_get_prefix() . 'listing_status', $date_query ) );
			
		?>
		<div class="row">
			<div class="column large-12">

				<div class="shandora-sales-stat">
					<div class="shandora-sales-stat-header">
						<h4 class="shandora-sales-stat-location">
							<?php echo $term->name; ?>
						</h4>
						<?php if( isset( $instance['scope'] ) ) { ?>
							<div class="shandora-sales-stat-title">
								<?php if( $instance['scope'] == 'weekly' ) {
									_e('Listing over last 7 days', 'bon');
								} else if( $instance['scope'] == 'monthly' ) {
									_e('Listing over last 30 days', 'bon');
								} ?>
							</div>
						<?php } ?>
					</div>
					<div class="shandora-sales-stat-content">

						<div class="shandora-sales-stat-avg-price shandora-stat-container">
							<div class="shandora-stat-icon pull-left">
								<i class="bonicons bi-calculator bi-2x bi-fw"></i>
							</div>
							<div class="shandora-stat-value-container">
								<div class="shandora-stat-value"><?php echo shandora_format_price( $other_results[0]->avgvalue ); ?></div>
								<div class="shandora-stat-title">
									<?php _e('Listings Avg. Price', 'bon'); ?>
								</div>
							</div>
						</div>

						<div class="shandora-sales-stat-avg-sold shandora-stat-container">
							<div class="shandora-stat-icon pull-left">
								<i class="bonicons bi-flag bi-2x bi-fw"></i>
							</div>
							<div class="shandora-stat-value-container">
								<div class="shandora-stat-value"><?php echo shandora_format_price( $sold_results[0]->avgvalue ); ?></div>
								<div class="shandora-stat-title">
									<?php _e('Listings Avg. Sold Price', 'bon'); ?>
								</div>
							</div>
						</div>

						<div class="shandora-sales-stat-for-sale shandora-stat-container">
							<div class="shandora-stat-icon pull-left">
								<i class="bonicons bi-home bi-2x bi-fw"></i>
							</div>
							<div class="shandora-stat-value-container">
								<div class="shandora-stat-value"><?php echo $other_results[0]->cpost; ?></div>
								<div class="shandora-stat-title">
									<?php _e('Listings For Sale', 'bon'); ?>
								</div>
							</div>
						</div>

						<div class="shandora-sales-stat-sold shandora-stat-container">
							<div class="shandora-stat-icon pull-left">
								<i class="bonicons bi-legal bi-2x bi-fw"></i>
							</div>
							<div class="shandora-stat-value-container">
								<div class="shandora-stat-value"><?php echo $sold_results[0]->cpost; ?></div>
								<div class="shandora-stat-title">
									<?php _e('Listings Sold', 'bon'); ?>
								</div>
							</div>
						</div>
					</div>
				</div>

			</div>
		</div>
		<?php } ?>

	<?php

		/* Close the theme's widget wrapper. */
		echo $after_widget;
	}

	/**
	 * Updates the widget control options for the particular instance of the widget.
	 *
	 * @since 0.6.0
	 */
	function update( $new_instance, $old_instance ) {

		$instance = $new_instance;

		$instance['title']  = strip_tags( $new_instance['title'] );
		$instance['type']  = strip_tags( $new_instance['type'] );
		$instance['location']  = strip_tags( $new_instance['location'] );
		$instance['scope']  = strip_tags( $new_instance['scope'] );

		return $instance;
	}

	/**
	 * Displays the widget control options in the Widgets admin screen.
	 *
	 * @since 0.6.0
	 */
	function form( $instance ) {

		/* Set up the default form values. */
		$defaults = array(
			'title'         => esc_attr__( 'Listing Statistic', 'bon' ),
			'type'          => 'listing',
			'location'		=> '',
			'scope'			=> '',
		);

		/* Merge the user-selected arguments with the defaults. */
		$instance = wp_parse_args( (array) $instance, $defaults );
		?>

		<div class="bon-widget-controls">
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><code><?php _e( 'Title:', 'bon' ); ?></code></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'type' ); ?>"><code><?php _e('Related to','bon'); ?></code></label>
			<select class="widefat" id="<?php echo $this->get_field_id( 'type' ); ?>" name="<?php echo $this->get_field_name( 'type' ); ?>">
				<option value="listing" <?php selected( $instance['type'], 'listing' ); ?>><?php _e('Real Estate','bon'); ?></option>
				<option value="car-listing" <?php selected( $instance['type'], 'car-listing' ); ?>><?php _e('Car','bon'); ?></option>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'location' ); ?>"><code><?php _e('Location Slug','bon'); ?></code></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'location' ); ?>" name="<?php echo $this->get_field_name( 'location' ); ?>" value="<?php echo esc_attr( $instance['location'] ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'scope' ); ?>"><code><?php _e('Time Scope','bon'); ?></code></label>
			<select class="widefat" id="<?php echo $this->get_field_id( 'scope' ); ?>" name="<?php echo $this->get_field_name( 'scope' ); ?>">
				<option value="monthly" <?php selected( $instance['scope'], 'lastthirty' ); ?>><?php _e('Last 30 Days','bon'); ?></option>
				<option value="weekly" <?php selected( $instance['scope'], 'weekly' ); ?>><?php _e('Last 7 Days','bon'); ?></option>
			</select>
		</p>

		
		</div>
	<?php
	}

	function post_date_filter_where_thirty($where = '') {
		 //only show posts published within the last 30 days
		 $where .= " AND post_date > '" . date('Y-m-d', strtotime('-30 days')) . "'";
		 return $where;
	}

	function post_date_filter_where_seven($where = '') {
		 //only show posts published within the last 7 days
		 $where .= " AND post_date > '" . date('Y-m-d', strtotime('-7 days')) . "'";
		 return $where;
	}

	function post_mod_filter_where_thirty($where = '') {
		 //only show posts published within the last 30 days
		 $where .= " AND post_modified > '" . date('Y-m-d', strtotime('-30 days')) . "'";
		 return $where;
	}

	function post_mod_filter_where_seven($where = '') {
		 //only show posts published within the last 7 days
		 $where .= " AND post_modified > '" . date('Y-m-d', strtotime('-7 days')) . "'";
		 return $where;
	}
}

?>