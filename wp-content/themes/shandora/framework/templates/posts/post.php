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

do_action( 'before_bon_fee_post_form' );
?>

<form id="bon-fee-post" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" name="bon_fee__post">

	<?php wp_nonce_field( $nonce_action ); ?>
	<?php wp_nonce_field( 'bon_fee_post_nonce', 'bon_fee_post_nonce' ); ?>
	<input type="hidden" id="user-id" name="user_ID" value="<?php echo (int) $user_ID ?>" />
	<input type="hidden" id="hiddenaction" name="action" value="<?php echo esc_attr( $form_action ) ?>" />
	<input type="hidden" id="originalaction" name="originalaction" value="<?php echo esc_attr( $form_action ) ?>" />
	<input type="hidden" id="post_author" name="post_author" value="<?php echo esc_attr( $post->post_author ); ?>" />
	<input type="hidden" id="post_type" name="post_type" value="<?php echo esc_attr( $post_type ) ?>" />
	<input type="hidden" id="original_post_status" name="original_post_status" value="<?php echo esc_attr( $post->post_status) ?>" />
	<input type="hidden" id="referredby" name="referredby" value="<?php echo esc_url( wp_get_referer() ); ?>" />
	<input type="hidden" id="bon_fee_redirect" name="bon_fee_redirect" value="1" />
	<?php if ( ! empty( $active_post_lock ) ) { ?>
		<input type="hidden" id="active_post_lock" value="<?php echo esc_attr( implode( ':', $active_post_lock ) ); ?>" />
	<?php
	}
	if ( 'draft' != get_post_status( $post ) )
		wp_original_referer_field(true, 'previous');

	echo $form_extra;

		wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false );
	?>
	<?php wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false ); ?>


	<div id="bon-fee-poststuff">
		<div id="bon-fee-post-body" class="bon-fee-metabox-holder">
			<div id="bon-fee-post-body-content">

				<?php if ( post_type_supports( $post_type, 'title' ) ) { ?>

					<div id="bon-fee-titlewrap">
						<?php
						/**
						 * Filter the title field placeholder text.
						 *
						 * @since 3.1.0
						 *
						 * @param string  $text Placeholder text. Default 'Enter title here'.
						 * @param WP_Post $post Post object.
						 */
						?>
						<input type="text" name="post_title" size="30" placeholder="<?php echo apply_filters( 'enter_title_here', __( 'Enter title here', 'bon' ), $post ); ?>" value="<?php echo esc_attr( htmlspecialchars( $post->post_title ) ); ?>" id="title" autocomplete="off" />
					</div> <!-- end /bon-fee-titlewrap -->

				<?php } ?>

				<?php 

					do_action( 'edit_form_after_title', $post );

					if ( post_type_supports( $post_type, 'editor' ) ) { 
						echo $editor; 
					} 

					/**
					 * Fires after the content editor.
					 *
					 * @since 3.5.0
					 *
					 * @param WP_Post $post Post object.
					 */
					do_action( 'edit_form_after_editor', $post ); 

				?>
	
			</div><!-- end bon-fee-post-body-content -->


			<?php

				do_action( 'bon_fee_meta_section' );

				//global $wp_meta_sections;
				
				bon_get_template( 'posts/meta-section.php', $args );


			?>
			<br class="clear">

		</div><!-- end bon-fee-post-body -->

	</div><!-- end bon-fee-poststuff -->
		
</form>

<?php if ( post_type_supports( $post_type, 'title' ) && '' === $post->post_title ) : ?>
	<script type="text/javascript">
		try{document.getElementById('bon-fee-title').focus();}catch(e){}
	</script>
<?php endif; 

do_action( 'after_bon_fee_post_form' );

?>