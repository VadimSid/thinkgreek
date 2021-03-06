<?php 

	$suffix = 'listing_';
	$status = shandora_get_meta($post->ID, $suffix . 'status'); 
	$status_key = shandora_get_meta($post->ID, $suffix . 'status');
    $badge = shandora_get_meta($post->ID, $suffix . 'badge');
    $badgeclr = shandora_get_meta($post->ID, $suffix . 'badge_color');
    
    $status_opt = shandora_get_car_search_option('status');
    if(array_key_exists($status, $status_opt)) {
    	$status = $status_opt[$status];
    }
if( is_singular( get_post_type() ) ) { 


?>
<article id="post-<?php the_ID(); ?>" <?php post_class( $status_key . ' listing'  ); ?> itemscope itemtype="http://schema.org/AutoDealer">
	<header class="entry-header clear">
		<?php echo apply_atomic_shortcode( 'entry_title', the_title( '<h1 class="entry-title" itemprop="name">', '</h1>', false ) ); ?>
		
		<a class="print" href="javascript:window.print()"><i class="sha-printer"></i></a>
		<?php echo apply_atomic_shortcode( 'entry_fav', '[bon-fav]' ); ?>
		<?php echo apply_atomic_shortcode('listing_published', '[entry-published before="<div class=\'show-for-large\'>" after="</div>" text="'.__('Published on ','bon').'"]'); ?>
		<h4 class="price"><?php shandora_get_listing_price(); ?></h4>
		<?php $price2 = shandora_get_listing_price( false, true ); ?>
		<?php if( !empty( $price2 ) ) { ?>
		<h4 class="price secondary"><?php echo $price2; ?></h4>
		<?php } ?>
	</header><!-- .entry-header -->

	<?php do_atomic( 'before_single_entry_content' ); ?>

	<div class="entry-content clear" itemprop="description">
		<?php the_content(); ?>
		<?php wp_link_pages( array( 'before' => '<p class="page-links">' . '<span class="before">' . __( 'Pages:', 'bon' ) . '</span>', 'after' => '</p>' ) ); ?>
	</div><!-- .entry-content -->

	<?php do_atomic( 'after_single_entry_content' ); ?>


</article>
<?php } else {

	$view = isset( $_GET['view'] ) ? $_GET['view'] : 'grid';

	$li_class = '';
    if( ($wp_query->current_post + 1) == ($wp_query->post_count) ) {  
        $li_class = 'last'; 
    }  
?>

<li class="<?php echo $li_class; ?>">
	<article id="post-<?php the_ID(); ?>" <?php post_class( $badgeclr ); ?> itemscope itemtype="http://schema.org/AutoDealer">

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
<?php } ?>