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
class Shandora_Calculator_Widget extends WP_Widget {

	/**
	 * Set up the widget's unique name, ID, class, description, and other options.
	 *
	 * @since 1.2.0
	 */
	function __construct() {

		/* Set up the widget options. */
		$widget_options = array(
			'classname'   => 'mortgage-calculator',
			'description' => esc_html__( 'Show Mortgage Calculator.', 'bon' )
		);

		/* Set up the widget control options. */
		$control_options = array(
			
		);

		/* Create the widget. */
		$this->WP_Widget(
			'shandora-mortgage-calculator',               // $this->id_base
			__( 'Shandora Calculator', 'bon' ), // $this->name
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


		/* Output the theme's $before_widget wrapper. */
		echo $before_widget;

		/* If a title was input by the user, display it. */
		if ( !empty( $instance['title'] ) )
			echo $before_title . apply_filters( 'widget_title',  $instance['title'], $instance, $this->id_base ) . $after_title;

		?>
		
		<div class="row">
			<div class="column large-12">
				<?php if ($instance['type'] == "realestate") { $currency = bon_get_option( 'currency', '$'); ?>


					<script type="text/javascript"> 
					jQuery(document).ready(function($) { $("#bon-calc-<?php echo $this->id_base; ?>").MortgageCalculator({ currency: "<?php echo $currency; ?>", mode: "widget", logo: { path: "", url: "", target: "_blank" } }); });</script>
					<div id="bon-calc-<?php echo $this->id_base; ?>"></div>
				<?php } else { $currencysymbol = bon_get_option('currency'); ?>
				<div id="loancalculator_cars">
					<form action="post">
						<label>
						<?php _e('Loan Amount', 'bon'); ?>
						(<?php echo $currencysymbol ?>)</label>
						<input type="text" id="LoanAmount" size="10" name="LoanAmount" value="30000" />
						
						<label>
						<?php _e('Down Payment', 'bon'); ?>
						(<?php echo $currencysymbol ?>)</label>
						<input type="text" id="DownPayment" size="10" name="DownPayment" value="0" />

						<label>
						<?php _e('Annual Rate','bon'); ?>
						</label>
						<input id="InterestRate" type="text" size="3" name="InterestRate" value="7.0" />

						<label>
						<?php _e('Loan Terms (Years)', 'bon'); ?>
						</label>
						<input id="NumberOfYears" type="text" size="3" name="NumberOfYears" value="4" />

						<button class="button flat radius" id="morgcal" name="morgcal" ><?php _e('Calculate', 'bon'); ?></button><br />

						<label>
						<?php _e('Number of Payments', 'bon'); ?>
						</label>
						<input id="NumberOfPayments" type="text" name="NumberOfPayments" />

						<label>
						<?php _e('Monthly Payment', 'bon'); ?>
						(<?php echo $currencysymbol ?>)</label>
						<input id="MonthlyPayment" type="text" name="MonthlyPayment" />
					</form>
				</div>
				<?php } ?>
			</div>
		</div>

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
			'title'           => esc_attr__( 'Mortgage Calculator', 'bon' ),
			'type'           => 'realestate',
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
				<option value="realestate" <?php selected( $instance['type'], 'realestate' ); ?>><?php _e('Real Estate','bon'); ?></option>
				<option value="car" <?php selected( $instance['type'], 'car' ); ?>><?php _e('Car','bon'); ?></option>
			</select>
		</p>

		
		</div>
	<?php
	}
}

?>