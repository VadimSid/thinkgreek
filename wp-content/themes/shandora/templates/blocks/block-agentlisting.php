<?php 

$args = array(
	'posts_per_page'	=> 48,
	'post_type'			=> 'listing',
	'post_status'		=> 'publish',
	'paged'				=> $paged,
	'meta_query'		=> array(
		array(
			'key' => bon_get_prefix() . 'listing_agentpointed',
            'value' => serialize(array(strval($post->ID))),
            'compare' => '=',
		)
	)
);

$agent_query = new WP_Query($args);

if($agent_query->have_posts()) : $compare_page = bon_get_option('compare_page'); ?>

<ul class="listings <?php shandora_block_grid_column_class(); ?>" data-compareurl="<?php echo get_permalink($compare_page); ?>">
			
<?php
	while($agent_query->have_posts()) : $agent_query->the_post();

		$status = shandora_get_meta($post->ID, 'listing_status'); 
	    $bed = shandora_get_meta($post->ID, 'listing_bed');
	    $bath = shandora_get_meta($post->ID, 'listing_bath');
	    $lotsize = shandora_get_meta($post->ID, 'listing_lotsize');
	 	$sizemeasurement = bon_get_option('measurement');

	 	$view = isset( $_GET['view'] ) ? $_GET['view'] : 'grid';

		$li_class = '';
	    if( ($agent_query->current_post + 1) == ($agent_query->post_count) ) {  
	        $li_class = 'last'; 
	    }  

?>
<li class="<?php echo $li_class; ?>">
<article id="post-<?php the_ID(); ?>" <?php post_class( $status ); ?> itemscope itemtype="http://schema.org/RealEstateAgent">
	<?php 

			if( $view == 'list') {
				echo '<div class="row"><div class="column large-3 small-4">';
			}

			bon_get_template_part( 'block', 'listing-header' ); 

			if( $view == 'list') {
				echo '</div>';
				echo '<div class="column large-9 small-8">';
			}
		?>

		<div class="entry-summary">

			<?php do_atomic('entry_summary'); ?>

		</div><!-- .entry-summary -->

		<?php 

			if( $view == 'list') { 

				echo '</div></div>';

			}
		?>

		<?php 
			if( $view == 'grid' ) {
				bon_get_template_part( 'block', 'listing-footer' ); 
			}
		?>

</article>
</li>
<?php
endwhile; 
?>
	</ul>
<?php
endif; wp_reset_query();
?>