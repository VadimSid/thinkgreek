<?php if ( ! defined( 'ABSPATH' ) ) exit('No direct script access allowed'); // Exit if accessed directly

/**
 *
 * @author		Hermanto Lim
 * @copyright	Copyright (c) Hermanto Lim
 * @link		http://bonfirelab.com
 * @since		Version 1.3
 * @package 	BonFramework
 * @subpackage  Template
 * @category 	Account
 * 
 *
 */ 
?>

<h2><?php _e( 'Login', 'bon' ); ?></h2>

<form method="post" class="<?php echo apply_filters( 'bon_login_form_class', 'bon-login-form' ); ?>" id="bon-login-form">

	<?php do_action( 'bon_login_form_start' ); ?>

	<p class="bon-form-row">
		<label for="log_username"><?php _e( 'Username or email address', 'bon' ); ?> <span class="required">*</span></label>
		<input type="text" name="username" id="log_username" />
	</p>
	<p class="bon-form-row">
		<label for="log_password"><?php _e( 'Password', 'bon' ); ?> <span class="required">*</span></label>
		<input type="password" name="password" id="log_password" />
	</p>

	<?php do_action( 'bon_login_form' ); ?>

	<p class="bon-form-row">
		<?php wp_nonce_field( 'bon_login' ); ?>
		<input type="submit" class="button" name="login" value="<?php _e( 'Login', 'bon' ); ?>" />
		<label for="rememberme" class="inline">
			<input name="rememberme" type="checkbox" id="rememberme" value="forever" /> <?php _e( 'Remember me', 'bon' ); ?>
		</label>
	</p>
	<p class="lost_password">
		<a href="<?php echo esc_url( $args['lost_password_url'] ); ?>"><?php _e( 'Lost your password?', 'bon' ); ?></a>
	</p>

	<?php do_action( 'bon_login_form_end' ); ?>

</form>