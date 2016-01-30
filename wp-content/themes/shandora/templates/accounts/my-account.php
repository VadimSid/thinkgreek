<?php if ( ! defined( 'ABSPATH' ) ) exit('No direct script access allowed'); // Exit if accessed directly

/**
 *
 * @author      Hermanto Lim
 * @copyright   Copyright (c) Hermanto Lim
 * @link        http://bonfirelab.com
 * @since       Version 1.3
 * @package     BonFramework
 * @subpackage  Template
 * @category    Account
 * 
 *
 */

?>

<?php do_action( 'bon_before_account' ); ?>

<div class="bon-account">
	<p class="bon-account-welcome">
		<?php
		printf(
			__( 'Hello <strong>%1$s</strong> (not %1$s? <a href="%2$s">Sign out</a>).', 'bon' ) . ' ',
			$current_user->display_name,
			wp_logout_url( get_permalink( bon_accounts()->my_account_page_id ) )
		);

		printf( __( 'From your account dashboard you can view your profile or <a href="%s">edit your password and account details</a>.', 'bon' ),
			bon_accounts()->edit_account_url()
		);
		?>
	</p>

	<figure class="bon-account-user">
		<?php echo get_avatar( $current_user->user_email ); ?>
		<figcaption>
			<strong><?php echo $current_user->display_name; ?></strong>
			<p>
				<?php echo $current_user->description; ?>
			</p>
		</figcaption>
	</figure>

	<h4 class="bon-form-title"><?php _e('Contact Info', 'bon'); ?></h4>

	<ul class="bon-account-social-contact">
		<?php foreach ( wp_get_user_contact_methods( $current_user ) as $name => $desc ) { ?>
			<?php if( isset( $current_user->$name ) && !empty( $current_user->$name ) && class_exists( 'Bon_Toolkit_Widget_Social' ) ) : ?>
				<li>
					<a href="<?php echo esc_url( $current_user->$name ); ?>">
						<i class="bt-icon-<?php echo $name; ?>"></i>
					</a>
				</li>
			<?php endif; ?>
		<?php } ?>
	</ul>
	<?php 


	if( function_exists( 'bon_fav_get_users_favorites_by_posttype' ) ) :

		$layout = get_theme_mod('theme_layout');
	    if( empty($layout) ) {
	        $layout = get_post_layout(get_queried_object_id());
	    }

		$mobile = bon_get_option('mobile_layout', '2');

		$block_cls = '4';
		if( $layout == '2c-l' || $layout == '2c-r') {
			$block_cls = '3';
		}

		$ul_class = "small-block-grid-".$mobile." large-block-grid-".$block_cls;
		$compare_page = bon_get_option('compare_page');

		$fav_post_ids = bon_fav_get_users_favorites_by_posttype( $current_user->$name, 'listing' );
			if( !empty( $fav_post_ids ) ) :

				

	 ?> 
		<h4 class="bon-form-title"><?php _e('Favorite Properties', 'bon'); ?></h4>
		<?php

		$loop = array(
				'post_type'      => 'listing',
				'post__in'		=> $fav_post_ids,
				'posts_per_page' => -1,
			);

			query_posts($loop);

			if ( have_posts() ) : ?>

			<div id="listings-container" class="row">

				<div class="<?php echo shandora_column_class('large-12'); ?>">

					<ul class="listings <?php echo $ul_class; ?>" data-compareurl="<?php echo get_permalink($compare_page); ?>">

				<?php while ( have_posts() ) : the_post();
					$status = shandora_get_meta(get_the_ID(), 'listing_status');
				?>
					<li>
					<article id="post-<?php echo get_the_ID(); ?>" class="<?php echo join(' ', get_post_class($status, null, false)); ?>" itemscope itemtype="http://schema.org/RealEstateAgent">
						<?php
						
							bon_get_template_part( 'block', 'listing-header' ); 

							echo '<div class="entry-summary">';

							do_atomic( 'entry_summary' );

							echo '</div>';

							bon_get_template_part( 'block', 'listing-footer' ); 

						?>

					</article></li>

				<?php endwhile; ?>

			</ul></div></div>

			<?php endif; wp_reset_query(); ?>



	<?php endif; ?>


	<?php 
		$car_post_ids = bon_fav_get_users_favorites_by_posttype( $current_user->$name, 'car-listing' );
			if( !empty( $car_post_ids ) ) :
	 ?> 
		<h4 class="bon-form-title"><?php _e('Favorite Cars', 'bon'); ?></h4>
		<?php

		$loop2 = array(
				'post_type'      => 'car-listing',
				'post__in'		=> $car_post_ids,
				'posts_per_page' => -1,
			);

			query_posts($loop2);

			if ( have_posts() ) : ?>

			<div id="listings-container" class="row">

				<div class="<?php echo shandora_column_class('large-12'); ?>">

					<ul class="listings <?php echo $ul_class; ?>" data-compareurl="<?php echo get_permalink($compare_page); ?>">

				<?php while ( have_posts() ) : the_post();
					$status = shandora_get_meta(get_the_ID(), 'listing_status');
				?>
					<li>
					<article id="post-<?php echo get_the_ID(); ?>" class="<?php echo join(' ', get_post_class($status, null, false)); ?>" itemscope itemtype="http://schema.org/RealEstateAgent">
						<?php
						
							bon_get_template_part( 'block', 'listing-header' ); 

							echo '<div class="entry-summary">';

							do_atomic( 'entry_summary' );

							echo '</div>';

							bon_get_template_part( 'block', 'listing-footer' ); 

						?>

					</article></li>

				<?php endwhile; ?>

			</ul></div></div>

			<?php endif; wp_reset_query(); ?>
		
		
	<?php endif; 

	endif; ?>


	<?php do_action('bon_account_detail', $current_user ); ?>

</div>

<?php do_action( 'bon_after_account' ); ?>