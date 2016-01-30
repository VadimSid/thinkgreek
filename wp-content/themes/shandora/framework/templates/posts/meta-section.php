<!-- This is the side post box -->
<div id="bon-fee-postbox-container-1" class="bon-fee-postbox-container">

	<div id="metabox-side">
		<?php
			

			/*$i = 0;

			global $wp_meta_sections;

			if( $wp_meta_sections ) {
				foreach ( $wp_meta_sections as $context => $priorities ) {

					if ( is_array( $priorities ) && $context == 'side' ) {

						ksort( $priorities );

						foreach ( $priorities as $priority => $sections ) {

							if ( is_array( $sections ) ) {

								foreach ( $sections as $section ) {

									$i++;

									?>
									<div id="bon-fee-meta-box-<?php echo $section['id']; ?>" class="bon-fee-postbox">
										<div id="<?php echo $section['id']; ?>" class="postbox <?php echo postbox_classes( $section['id'], $post_type ); ?>">
											<div title="<?php _e( 'Click to toggle' ); ?>" class="handlediv"><br></div>
											<h3 class="hndle"><span><?php echo $section['title']; ?></span></h3>
											<div class="inside">
												<?php call_user_func( $section['callback'], $post, $section ); ?>
											</div>
										</div>
									</div>
									<?php
								}
							}
						}
					}
				}
			}*/

		?>

		<?php

			if ( 'page' == $post_type ) {
				/**
				 * Fires before meta boxes with 'side' context are output for the 'page' post type.
				 *
				 * The submitpage box is a meta box with 'side' context, so this hook fires just before it is output.
				 *
				 * @since 2.5.0
				 *
				 * @param WP_Post $post Post object.
				 */
				do_action( 'submitpage_box', $post );
			}
			else {
				/**
				 * Fires before meta boxes with 'side' context are output for all post types other than 'page'.
				 *
				 * The submitpost box is a meta box with 'side' context, so this hook fires just before it is output.
				 *
				 * @since 2.5.0
				 *
				 * @param WP_Post $post Post object.
				 */
				do_action( 'submitpost_box', $post );
			}

			do_meta_boxes($post_type, 'side', $post);

		?>
	</div>

</div><!-- end bon-fee-postbox-container-1 -->

<div id="bon-fee-postbox-container-2" class="bon-fee-postbox-container">

	<div id="metabox-advanced">

	<?php
		
		/*do_action( 'bon_fee_meta_section', 'advanced' );

		$i = 0;

		if( $wp_meta_sections ) {
			
			foreach ( $wp_meta_sections as $context => $priorities ) {

				if ( is_array( $priorities ) && $context == 'advanced' ) {

					ksort( $priorities );

					foreach ( $priorities as $priority => $sections ) {

						if ( is_array( $sections ) ) {

							foreach ( $sections as $section ) {

								$i++;

								?>
								<div id="bon-fee-meta-box-<?php echo $section['id']; ?>" class="bon-fee-postbox">
									<div id="<?php echo $section['id']; ?>" class="postbox">
										<div title="<?php _e( 'Click to toggle' ); ?>" class="handlediv"><br></div>
										<h3 class="hndle"><span><?php echo $section['title']; ?></span></h3>
										<div class="inside">
											<?php call_user_func( $section['callback'], $post, $section ); ?>
										</div>
									</div>
								</div>
								<?php
							}
						}
					}
				}
			}
		}*/
	?>

	<?php

		do_meta_boxes(null, 'normal', $post);

		if ( 'page' == $post_type ) {
			/**
			 * Fires after 'normal' context meta boxes have been output for the 'page' post type.
			 *
			 * @since 1.5.0
			 *
			 * @param WP_Post $post Post object.
			 */
			do_action( 'edit_page_form', $post );
		}
		else {
			/**
			 * Fires after 'normal' context meta boxes have been output for all post types other than 'page'.
			 *
			 * @since 1.5.0
			 *
			 * @param WP_Post $post Post object.
			 */
			do_action( 'edit_form_advanced', $post );
		}


		do_meta_boxes(null, 'advanced', $post);

	?>
	</div>

</div><!-- end bon-fee-postbox-container-2 -->