<?php 
	$prefix = bon_get_prefix();
	$show_agent_details = bon_get_option('show_agent_details', 'yes');
	$show_contact_form = bon_get_option('show_contact_form', 'yes');

	$agent_ids = get_post_meta($post->ID, $prefix . 'listing_agentpointed', true);
	$agent_id = '';
    if(is_array($agent_ids) && !empty($agent_ids)) {
      $agent_id = $agent_ids[0];
    } else {
    	$agent_id = bon_get_option( 'global_agent' );
    }
if( !empty($agent_id) ) {
	$agent_email = shandora_get_meta($agent_id, 'agentemail');
	if( empty( $agent_email ) ) {
		$agent_email = get_option( 'admin_email' );
	}
?>

<footer class="listing-contact row">
	<?php if( $show_agent_details == 'yes' ) : 

			$col_class = 'large-6';

			if( $show_contact_form == 'no' ) { $col_class = 'large-12'; } ?>
		<div class="column <?php echo $col_class; ?>">
			<div class="agent-detail row">

	            <figure class="column large-6">
					<?php
						$img_id = shandora_get_meta($agent_id, 'agentpic');
						echo wp_get_attachment_image( $img_id, 'listing_small_box' );
					?>
				</figure>
				<div class="column large-6">
					<strong class="agent-title"><?php _e('Agent for this listing','bon'); ?></strong>
					<h3 class="subheader agent-name"><?php echo get_the_title($agent_id); ?></h3>
					<?php if( shandora_get_meta( $agent_id, 'agentmobilephone') ) { ?> 
					<div class="agent-info">
						<strong><?php _e('Mobile:','bon'); ?></strong>
						<span><?php echo shandora_get_meta( $agent_id, 'agentmobilephone'); ?></span>
					</div>
					<?php } ?>
					<?php if ( shandora_get_meta( $agent_id, 'agentofficephone' ) ) { ?>
					<div class="agent-info">	
						<strong><?php _e('Offce:','bon'); ?></strong>
						<span><?php echo shandora_get_meta( $agent_id, 'agentofficephone'); ?></span>
					</div>
					<?php } ?>
					<?php if ( shandora_get_meta( $agent_id, 'agentfax') ) { ?>
					<div class="agent-info">			
						<strong><?php _e('Fax:','bon'); ?></strong>
						<span><?php echo shandora_get_meta( $agent_id, 'agentfax'); ?></span>
					</div>
					<?php } ?>
				</div>
				
			</div>
			<div class="related-post">
				<div class="related-header"><strong><?php printf(__('Other Listing by %s','bon'), get_the_title($agent_id)); ?></strong><a class="more-related" href="<?php echo get_permalink($agent_id); ?>" title="<?php _e('More post','bon'); ?>"><?php _e('more','bon'); ?></a></div>
				<ul>
				<?php 
				
					$related_posts = get_posts(  array(
						'numberposts'		=>	5,
						'post_type'			=>	'listing',
						'post_status'		=>	'publish',
						'post__not_in'		=> array($post->ID),
						'meta_query'		=> array(
							array(
								'key' => $prefix . 'listing_agentpointed',
		                        'value' => serialize($agent_ids),
		                        'compare' => '=',
							)
						)
					));

					if( $related_posts ) {
						foreach($related_posts as $related_post) {
							echo '<li><a href="'.get_permalink( $related_post->ID ).'" title="'.sprintf(__('Permalink to %s','bon'), $related_post->post_title).'">'.$related_post->post_title.'</a></li>';
						}
					}
				?>
			</ul>
			</div>
		</div>
	<?php endif; ?>
	<?php if( $show_contact_form == 'yes' ) :

			$col_class = 'large-6';

			if( $show_agent_details == 'no' ) { $col_class = 'large-12'; } ?>
		<div class="column <?php echo $col_class; ?>">
			<div class="row">
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
							<input type="hidden" name="subject" value="<?php printf(__('Contact For: %s Property','bon'), get_the_title( $post->ID )); ?>" />
							<input type="hidden" name="listing_id" value="<?php echo $post->ID; ?>" />
							<input type="hidden" name="receiver" value="<?php echo $agent_email; ?>" />
							<input class="flat button red radius" name="submit" type="submit" id="submit" tabindex="5" value="<?php _e('Submit', 'bon') ?>" />
							<span class="contact-loader"><img src="<?php echo trailingslashit(BON_THEME_URI); ?>assets/images/loader.gif" alt="loading..." />
						</div>
						<div class="sending-result"><div class="green bon-toolkit-alert"></div></div>
					</form>
				</div>
			</div>
		</div>
	<?php endif; ?>
</footer>
<?php } ?>