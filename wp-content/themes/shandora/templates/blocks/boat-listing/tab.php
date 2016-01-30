<section>
	<?php 
		$nav_tab = '';
		$tab_contents = '';
		
		$tabs = shandora_get_boat_tabs();
		$i = 0;
		foreach( $tabs as $_tab_key => $_tab_val ) {

			$nav_tab_class = '';

			if( isset( $_tab_val['show_ui'] ) && $_tab_val['show_ui'] === true ) {

				if( $i == 0 ) {
					$nav_tab_class = 'active';
				}

				$nav_tab .= '<a class="'.$nav_tab_class.'" href="#'.$_tab_key.'">'.$_tab_val['label'].'</a>';

				$tab_contents .= '<div id="'.$_tab_key.'" class="tab-content '.$nav_tab_class.'">';

					$tab_contents .= '<ul class="property-details">';

						if( isset( $_tab_val_['tax'] ) ) {

							$tab_contents .= get_the_term_list( get_the_ID(), $_tab_val['tax'], '<li>', '</li><li>', '</li>' );

						} else {

							$parent_term = '';
							$terms = '';

							if( isset( $_tab_val['options'] ) ) {

								foreach( $_tab_val['options'] as $_tab_option ) {
									
									if( $_tab_option['type'] == 'tax' ) {

										if( $_tab_option['depth'] == 1 ) {

											$terms = get_the_terms( $post->ID, $_tab_option['id'] );
											
											if ( $terms && ! is_wp_error( $terms ) )  {		
												$tab_contents .= '<li><strong>'.$_tab_option['label'].'</strong><span>';												   														   
											    foreach ( $terms as $term ) {				
											   		if( $term->parent == '0' ) {
											   			$parent_term = $term->term_id;
														$tab_contents .= '<a href="'.get_term_link( $term->term_id, $_tab_option['id'] ).'" title="'.$term->name.'">' . $term->name . '</a>';
														break;
													}
											    }
											    $tab_contents .= '</span></li>';														   													   														   
											}

										} else if( $_tab_option['depth'] == 2 ) {

											if ( $terms && ! is_wp_error( $terms ) )  {		
												$tab_contents .= '<li><strong>'.$_tab_option['label'].'</strong><span>';												   														   
											    foreach ( $terms as $term ) {				
											   		if( $term->parent == $parent_term ) {
														$tab_contents .= '<a href="'.get_term_link( $term->term_id, $_tab_option['id'] ).'" title="'.$term->name.'">' . $term->name . '</a>';
														break;
													}
											    }
											    $tab_contents .= '</span></li>';														   													   														   
											}

										} else {

											$value = get_the_term_list( $post->ID, $_tab_option['id'], '', ', ', '' );

											if( !empty( $value ) ) {
												$tab_contents .= '<li><strong>'.$_tab_option['label'].'</strong><span>'.$value.'</span>';
											}
										}

									} else {

										$value = get_post_meta( $post->ID, $_tab_option['id'], true );

										if( !empty( $value ) ) {

											if( $_tab_option['type'] == 'select' ) {
												if(array_key_exists($value, $_tab_option['options'] ) ) {
											    	$value = $_tab_option['options'][$value];
											    }
											}

											if( isset( $_tab_option['measure'] ) ) {
												$value .= ' '. $_tab_option['measure'];
											} else {
												if( isset( $_tab_val['measure'] ) ) {
													$value .= ' '. $_tab_val['measure'];
												}
											}

											$tab_contents .= '<li><strong>'.$_tab_option['label'].'</strong><span>'.$value.'</span>';

											
										}

									}

								}
							}
						}

					$tab_contents .= '</ul>';

				$tab_contents .= '</div>';
				
				$i++;
			}

		}

	?>
	<nav class="tab-nav">
		<?php echo $nav_tab; ?>
	</nav>
	<div class="tab-contents">
		<?php echo $tab_contents; ?>
	</div>
</section>