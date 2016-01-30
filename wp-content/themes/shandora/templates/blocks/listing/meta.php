<?php
	$status = shandora_get_meta($post->ID, 'listing_status'); 
    $bed = shandora_get_meta($post->ID, 'listing_bed');
    $bath = shandora_get_meta($post->ID, 'listing_bath');
    $lotsize = shandora_get_meta($post->ID, 'listing_buildingsize');
    $sizemeasurement = bon_get_option('measurement');
	$furnish = shandora_get_meta($post->ID, 'listing_furnishing');
	$garage = shandora_get_meta($post->ID, 'listing_garage');
	$rooms = shandora_get_meta($post->ID, 'listing_totalroom');
?>
<ul class="large-custom-grid-5 small-custom-grid-3">
	<?php if( !empty( $bed ) ) : ?>
	<li class="bed"><div class="meta-wrap">
		<i class="sha-bed"></i>
		<span class="meta-value">
			<?php 
				($bed > 0) ? printf(_n( '1 Bed', '%s Beds', $bed, 'bon' ), $bed) : _e('No Bath','bon'); 
			?>
		</span></div>
	</li>
	<?php endif; ?>
	<?php if( !empty( $bath ) ) : ?>
	<li class="bath"><div class="meta-wrap">
		<i class="sha-bath"></i>
		<span class="meta-value">
			<?php 
				($bath > 0) ? printf(_n( '1 Bath', '%s Baths', $bath, 'bon' ), $bath) : _e('No Bath','bon'); 
			?>
		</span></div>
	</li>
	<?php endif; ?>
	<?php if( !empty( $lotsize ) ) : ?>
	<li class="lotsize"><div class="meta-wrap">
		<i class="sha-ruler-2"></i>
		<span class="meta-value">
			<?php echo $lotsize . ' ' . strtolower($sizemeasurement); ?>
		</span></div>
	</li>
	<?php endif; ?>
	<?php if( !empty( $garage ) ) : ?>
	<li class="garage"><div class="meta-wrap">
		<i class="sha-car"></i>
		<span class="meta-value">
			<?php 
				($garage > 0) ? printf(_n( '1 Garage', '%s Garages', $garage, 'bon' ), $garage) : _e('No Garage','bon'); 
			?>
		</span></div>
	</li>
	<?php endif; ?>
	<?php if( !empty( $rooms ) ) : ?>
	<li class="furnish"><div class="meta-wrap">
		<i class="sha-building"></i>
		<span class="meta-value">
			<?php 
				($rooms > 0) ? printf(_n( '1 Room', '%s Rooms', $rooms, 'bon' ), $rooms) : _e('No Room','bon'); 
			?>
		</span>
		</div>
	</li>
	<?php endif; ?>
</ul>