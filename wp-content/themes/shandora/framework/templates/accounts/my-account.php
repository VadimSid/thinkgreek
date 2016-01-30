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
	
	<h4 class="bon-form-title"><?php _e('Recent Posts', 'bon'); ?></h4>
		
	<div class="bon-account-recent-posts">
		<ul>
		<?php 
			$posts_query = get_posts( array( 'post_status' => 'publish', 'numberposts' => 5, 'author' => $current_user->ID ) ); 

			if( $posts_query ) {
				foreach( $posts_query as $post_by_current_user ) { ?>

				<li>
					<a href="<?php echo get_permalink( $post_by_current_user->ID ); ?>" title="<?php echo $post_by_current_user->post_title; ?>">
						<?php echo $post_by_current_user->post_title; ?>
					</a>
				</li>
			<?php }
			}

		?>
		</ul>
	</div>

	<?php do_action('bon_account_detail', $current_user ); ?>

</div>

<?php do_action( 'bon_after_account' ); ?>