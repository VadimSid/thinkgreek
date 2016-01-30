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

<h2><?php _e( 'Register', 'bon' ); ?></h2>
<form method="post" class="<?php echo apply_filters( 'bon_register_form_class', 'bon-register-form' ); ?>" id="bon-register-form">

	<?php do_action( 'bon_register_form_start' ); ?>

	<p class="bon-form-row">
		<label for="reg_login"><?php _e( 'Username', 'bon' ); ?> <span class="required">*</span></label>
		<input type="text" name="username" id="reg_login" value="<?php if ( ! empty( $_POST['username'] ) ) echo esc_attr( $_POST['username'] ); ?>" />
	</p>

	<p class="bon-form-row">
		<label for="reg_email"><?php _e( 'Email address', 'bon' ); ?> <span class="required">*</span></label>
		<input type="email" name="email" id="reg_email" value="<?php if ( ! empty( $_POST['email'] ) ) echo esc_attr( $_POST['email'] ); ?>" />
	</p>

	<p class="bon-form-row">
		<label for="reg_pass"><?php _e( 'Password', 'bon' ); ?> <span class="required">*</span></label>
		<input type="password" name="password" id="reg_pass" value="" />
	</p>

	<p class="bon-form-row">
		<label for="reg_pass_confirm"><?php _e( 'Confirm Password', 'bon' ); ?> <span class="required">*</span></label>
		<input type="password" name="password_confirm" id="reg_pass_confirm" value="" />
	</p>

	<!-- Spam Trap -->
	<div style="left:-999em; position:absolute;">
		<label for="trap"><?php _e( 'Anti-spam', 'bon' ); ?></label>
		<input type="text" name="email_2" id="trap" tabindex="-1" />
	</div>

	<?php do_action( 'bon_register_form' ); ?>
	<?php do_action( 'register_form' ); ?>

	<p class="form-row">
		<?php wp_nonce_field( 'bon_register' ); ?>
		<input type="submit" class="button" name="register" value="<?php _e( 'Register', 'bon' ); ?>" />
	</p>

	<?php do_action( 'bon_register_form_end' ); ?>

</form>