<?php
    $engine = shandora_get_meta($post->ID, 'listing_enginesize');
    $transmission = shandora_get_meta($post->ID, 'listing_transmission');
    $ancap = shandora_get_meta($post->ID, 'listing_ancap');
    $mileage = shandora_get_meta($post->ID, 'listing_mileage');

    $trans_opt = shandora_get_car_search_option('transmission');
    if(array_key_exists($transmission, $trans_opt)) {
    	$transmission = $trans_opt[$transmission];
    }
	$terms = get_the_terms( $post->ID, 'body-type' );
						
	if ( $terms && ! is_wp_error( $terms ) ) 
	{														   														   
		   foreach ( $terms as $term ) {															   
				$bodytype = $term->name;
				break; // to display only one body type
		   }														   													   														   
	}
    
?>
<ul class="large-custom-grid-5 small-custom-grid-3">
	<?php if( !empty( $engine ) ) { ?>
	<li class="bed"><div class="meta-wrap">
		<i class="sha-engine"></i>
		<span class="meta-value">
			<?php 
				echo $engine;
			?>
		</span></div>
	</li>
	<?php } ?>
	<?php if(!empty($mileage)) { ?>
	<li class="bath"><div class="meta-wrap">
		<i class="sha-dashboard"></i>
		<span class="meta-value">
			<?php
				$mm = bon_get_option('mileage_measure');
				if( $mm == '' || strtolower($mm) == 'mile' || strtolower($mm) == 'miles' ) {
					($mileage > 0) ? printf(_n( '1 Mile', '%s Miles', $mileage, 'bon' ), $mileage) : _e('Unspecified','bon'); 
				} else {
					echo $mileage . ' ' . $mm;
				}
			?>
		</span></div>
	</li>
	<?php } ?>
	<?php if( !empty( $transmission ) ) { ?>
	<li class="lotsize"><div class="meta-wrap">
		<i class="sha-gear-shifter"></i>
		<span class="meta-value">
			<?php echo $transmission; ?>
		</span></div>
	</li>
	<?php } ?>
	<?php if( !empty( $bodytype ) ) { ?>
	<li class="garage"><div class="meta-wrap">
		<i class="sha-car-front"></i>
		<span class="meta-value">
			<?php echo $bodytype; ?>
		</span></div>
	</li>
	<?php } ?>
	<?php if(!empty($ancap) && $ancap <= 5 && $ancap != '0') { ?>
	<li class="furnish"><div class="meta-wrap">
		<i class="sha-airbag"></i>
		<span class="meta-value">
			<div class="ancap-rating">
			<?php for($i = 0; $i < $ancap; $i++) {
				echo '<i class="icon bonicons bi-star"></i>';
			} ?>
			</div>
		</span>
		</div>
	</li>
	<?php } ?>
</ul>