<?php 
	$status = shandora_get_meta($post->ID, 'listing_status'); 
    $bed = shandora_get_meta($post->ID, 'listing_bed');
    $bath = shandora_get_meta($post->ID, 'listing_bath');
    $lotsize = shandora_get_meta($post->ID, 'listing_lotsize');
    $sizemeasurement = bon_get_option('measurement');
    $agent_pic = shandora_get_meta($post->ID, 'agentpic');
    $agent_fb = shandora_get_meta($post->ID, 'agentfb');
    $agent_tw = shandora_get_meta($post->ID, 'agenttw');
    $agent_li = shandora_get_meta($post->ID,'agentlinkedin');
    $agent_mobile = shandora_get_meta($post->ID,'agentmobilephone');
    $agent_office = shandora_get_meta( $post->ID, 'agentofficephone');
    $agent_fax = shandora_get_meta($post->ID,'agentfax');
    $agent_email = shandora_get_meta($post->ID, 'agentemail');
    $agent_job = shandora_get_meta($post->ID, 'agentjob');
if( is_singular( get_post_type() ) ) { 


?>
<article id="post-<?php the_ID(); ?>" <?php post_class( $status ); ?> itemscope itemtype="http://schema.org/AutoDealer">
	<header class="entry-header clear">
		<h1 class="entry-title" itemprop="name"><?php the_title(); ?>
			<?php if( !empty($agent_job) ) { ?> 
			<span class="agent-job"><?php echo isset( $agent_job ) ? $agent_job : ''; ?></span>
			<?php } ?>
		</h1>
	</header><!-- .entry-header -->

	<div class="entry-content clear" itemprop="description">
		<div class="row">
		<?php 
		echo '<div class="column large-4 small-4">' . wp_get_attachment_image( $agent_pic, 'listing_small_box' ) . '</div>';
		?>
			<div class="column large-8 small-8">
				
				<div class="row">
					<div class="contact-info column large-6">
						<?php if(!empty($agent_mobile)) : ?>
						<strong><?php _e('Mobile','bon'); ?>: </strong><?php echo $agent_mobile; ?><br/>
						<?php endif; ?>
						<?php if(!empty($agent_office)) : ?>
						<strong><?php _e('Office','bon'); ?>: </strong><?php echo $agent_office; ?><br/>
						<?php endif; ?>
						<?php if(!empty($agent_fax)) : ?>
						<strong><?php _e('Fax','bon'); ?>: </strong><?php echo $agent_fax; ?><br/>
						<?php endif; ?>
					</div>

					<div class="social-media column large-6">
						<strong><?php _e('Follow on','bon'); ?>: </strong>
						<?php if(!empty($agent_fb)) : ?>
						<a title="<?php _e('Facebook','bon'); ?>" href="<?php echo $agent_fb; ?>" class="flat round button small"><i class="bonicons bi-facebook"></i></a>
						<?php endif; ?>
						<?php if(!empty($agent_li)) : ?>
						<a title="<?php _e('LinkedIn','bon'); ?>" href="<?php echo $agent_li; ?>" class="flat round button small"><i class="bonicons bi-linkedin"></i></a>
						<?php endif; ?>
						<?php if(!empty($agent_tw)) : ?>
						<a title="<?php _e('Twitter','bon'); ?>"href="<?php echo $agent_tw; ?>" class="flat round button small"><i class="bonicons bi-twitter"></i></a>
						<?php endif; ?>
					</div>
				</div>
				<hr />
				<?php the_content(); ?>
			</div>
		
		</div>
	<?php wp_link_pages( array( 'before' => '<p class="page-links">' . '<span class="before">' . __( 'Pages:', 'bon' ) . '</span>', 'after' => '</p>' ) ); ?>
	</div><!-- .entry-content -->

	<div class="listing-contact row">
		<div class="column large-12">
			<form action="" method="post" id="agent-contactform">
				<div class="row collapse input-container">
					<!--<div class="column large-2 small-1"><span class="attached-label prefix"><i class="sha-user"></i></span></div>-->
					<div class='column large-8 small-11 input-container-inner name'>
						<input class="attached-input required" type="text" placeholder="<?php _e('Full Name','bon'); ?>"  name="name" id="name" value="" size="22" tabindex="1" />
						<div class="contact-form-error" ><?php _e('Please enter your name.','bon'); ?></div>
					</div>
				</div>
				<div class="row collapse input-container">
					<!--<div class="column large-2 small-1"><span class="attached-label prefix"><i class="sha-mail-2"></i></span></div>-->
					<div class='column large-8 small-11 input-container-inner mail'>
						<input class="attached-input required email" type="email" placeholder="<?php _e('Email Address','bon'); ?>"  name="email" id="email" value="" size="22" tabindex="2" />
						<div class="contact-form-error" ><?php _e('Please enter your email.','bon'); ?></div>
					</div>
				</div>
				<div class="row collapse input-container">
					<!--<div class="column large-2 small-1"><span class="attached-label prefix"><i class="sha-phone-2"></i></span></div>-->
					<div class='column large-8 small-11 input-container-inner phone'>
						<input class="attached-input" type="text" placeholder="<?php _e('Phone Number','bon'); ?>"  name="phone" id="phone" value="" size="22" tabindex="1" />
						<div class="contact-form-error" ><?php _e('Please enter your phone number.','bon'); ?></div>
					</div>
				</div>
				<div class="row collapse textarea-container input-container" data-match-height>
					<!--<div class="column large-2 small-1"><span class="attached-label prefix"><i class="sha-pencil"></i></span></div>-->
					<div class='column large-12 small-11 input-container-inner pencil'>
						<textarea name="messages" class="attached-input required" id="messages" cols="58" rows="10" placeholder="<?php _e('Message','bon'); ?>"  tabindex="4"></textarea>
						<div class="contact-form-error" ><?php _e('Please enter your messages.','bon'); ?></div>
					</div>
				</div>
				<div>
					<input type="hidden" name="subject" value="<?php printf(__('Send from: Agent %s Page','bon'), get_the_title( $post->ID )); ?>" />
					<input type="hidden" name="listing_id" value="<?php echo $post->ID; ?>" />
					<input type="hidden" name="receiver" value="<?php echo $agent_email; ?>" />
					<input class="flat button red radius" name="submit" type="submit" id="submit" tabindex="5" value="<?php _e('Submit', 'bon') ?>" />
					<span class="contact-loader"><img src="<?php echo trailingslashit(BON_THEME_URI); ?>assets/images/loader.gif" alt="loading..." />
				</div>
				<div class="sending-result"><div class="green bon-toolkit-alert"></div></div>
			</form>
		</div>
	</div>
</article>

<div id="agent-listings">
	<h3><?php printf(__('Latest Listing by %s','bon'), get_the_title(get_the_ID())); ?></h3>
	<?php bon_get_template_part('block', 'salesreplisting'); ?>
</div>

<?php } else {
?>

<li>
<article id="post-<?php the_ID(); ?>" <?php post_class( $status ); ?> itemscope itemtype="http://schema.org/AutoDealer">

		<header class="entry-header">
			<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
			<?php 
			echo wp_get_attachment_image( $agent_pic, 'listing_small_box');
			?>
			</a>
		</header><!-- .entry-header -->

		<div class="entry-summary">

			<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php echo apply_atomic_shortcode( 'entry_title', the_title( '<h3 class="entry-title" itemprop="name">', '</h3>', false ) ); ?></a>
			<div class="entry-meta">
				<?php if ( $agent_mobile ) { ?>
				<div>
					<strong><?php _e('Mobile:','bon'); ?></strong>
					<span><?php echo $agent_mobile; ?></span>
				</div>
				<?php } ?>
				<?php if( $agent_office ) { ?>
				<div>	
					<strong><?php _e('Office:','bon'); ?></strong>
					<span><?php echo $agent_office; ?></span>
				</div>
				<?php } ?>
				<?php if( $agent_fax ) { ?> 
				<div>			
					<strong><?php _e('Fax:','bon'); ?></strong>
					<span><?php echo $agent_fax; ?></span>
				</div>
				<?php } ?>
			</div>
		</div><!-- .entry-summary -->

</article>
</li>
<?php } ?>