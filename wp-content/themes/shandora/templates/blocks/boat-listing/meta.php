<?php
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
    
?>
<ul class="large-custom-grid-5 small-custom-grid-3">
	<?php if( !empty( $engine_type ) ) { ?>
	<li class="bed"><div class="meta-wrap">
		<i class="sha-engine"></i>
		<span class="meta-value">
			<?php 
				echo $engine_type;
			?>
		</span></div>
	</li>
	<?php } ?>
	<?php if(!empty($speed)) { ?>
	<li class="bath"><div class="meta-wrap">
		<i class="sha-dashboard"></i>
		<span class="meta-value">
			<?php echo $speed . ' ' . bon_get_option( 'speed_measure' ); ?>
		</span></div>
	</li>
	<?php } ?>
	<?php if( !empty( $people ) ) { ?>
	<li class="lotsize"><div class="meta-wrap">
		<i class="sha-users"></i>
		<span class="meta-value">
			<?php echo $people . ' ' . __( 'People','bon' ); ?>
		</span></div>
	</li>
	<?php } ?>
	<?php if( !empty( $length ) ) { ?>
	<li class="garage"><div class="meta-wrap">
		<i class="sha-ruler"></i>
		<span class="meta-value">
			<?php echo $length . ' ' . bon_get_option( 'length_measure' ); ?>
		</span></div>
	</li>
	<?php } ?>
	<?php if( !empty($fuel) ) { ?>
	<li class="furnish"><div class="meta-wrap">
		<i class="sha-tint"></i>
		<span class="meta-value">
			<?php echo $fuel . ' ' . bon_get_option( 'volume_measure' ); ?>
		</span>
		</div>
	</li>
	<?php } ?>
</ul>