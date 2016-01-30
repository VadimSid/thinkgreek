<?php if ( ! defined( 'ABSPATH' ) ) exit('No direct script access allowed'); // Exit if accessed directly

/**
 *
 * @author      Hermanto Lim
 * @copyright   Copyright (c) Hermanto Lim
 * @link        http://bonfirelab.com
 * @since       Version 1.3
 * @package     BonFramework
 * @subpackage  Template
 * @category    Account
 * 
 *
 */

?>
<?php do_action( 'bon_before_edit_account_form' ); ?>

<form action="" method="post" class="<?php echo apply_filters( 'bon_edit_account_form_class', 'bon-edit-account-form' ); ?>">

	<h4 class="bon-form-title"><?php _e('Name', 'bon'); ?></h4>

	<p class="bon-form-row">
		<label for="account_first_name"><?php _e( 'First name', 'bon' ); ?> <span class="required">*</span></label>
		<input type="text" class="input-text" name="account_first_name" id="account_first_name" value="<?php esc_attr_e( $user->first_name ); ?>" />
	</p>
	<p class="bon-form-row">
		<label for="account_last_name"><?php _e( 'Last name', 'bon' ); ?> <span class="required">*</span></label>
		<input type="text" class="input-text" name="account_last_name" id="account_last_name" value="<?php esc_attr_e( $user->last_name ); ?>" />
	</p>
	<p class="bon-form-row">
		<label for="account_nickname"><?php _e( 'Nickname', 'bon' ); ?> <span class="required">*</span></label>
		<input type="text" class="input-text" name="account_nickname" id="account_nickname" value="<?php esc_attr_e( $user->nickname ); ?>" />
	</p>
	
	<p class="bon-form-row">
		<label for="account_display_name"><?php _e( 'Display name publicly as', 'bon' ); ?></label>
		<select name="account_display_name" id="account_display_name">
			<?php
				$public_display = array();
				$public_display['display_nickname']  = $user->nickname;
				$public_display['display_username']  = $user->user_login;

				if ( !empty($user->first_name) )
					$public_display['display_firstname'] = $user->first_name;

				if ( !empty($user->last_name) )
					$public_display['display_lastname'] = $user->last_name;

				if ( !empty($user->first_name) && !empty($user->last_name) ) {
					$public_display['display_firstlast'] = $user->first_name . ' ' . $user->last_name;
					$public_display['display_lastfirst'] = $user->last_name . ' ' . $user->first_name;
				}

				if ( !in_array( $user->display_name, $public_display ) ) // Only add this if it isn't duplicated elsewhere
					$public_display = array( 'display_displayname' => $user->display_name ) + $public_display;

				$public_display = array_map( 'trim', $public_display );
				$public_display = array_unique( $public_display );

				foreach ( $public_display as $id => $item ) {
			?>
				<option <?php selected( $user->display_name, $item ); ?>><?php echo $item; ?></option>
			<?php
				}
			?>
		</select>
	</p>

	<h4 class="bon-form-title"><?php _e('Contact Info', 'bon'); ?></h4>

	<p class="bon-form-row">
		<label for="account_email"><?php _e( 'Email address', 'bon' ); ?> <span class="required">*</span></label>
		<input type="email" class="input-text" name="account_email" id="account_email" value="<?php esc_attr_e( $user->user_email ); ?>" />
	</p>
	<p class="bon-form-row">
		<label for="account_url"><?php _e( 'Website', 'bon' ); ?></label>
		<input type="text" class="input-text" name="account_url" id="account_url" value="<?php echo esc_url( $user->user_url ); ?>" />
	</p>

	<?php
		foreach ( wp_get_user_contact_methods( $user ) as $name => $desc ) {
	?>
	<p class="bon-form-row">
		<?php
		/**
		 * Filter a user contactmethod label.
		 *
		 * The dynamic portion of the filter hook, $name, refers to
		 * each of the keys in the contactmethods array.
		 *
		 * @since 2.9.0
		 *
		 * @param string $desc The translatable label for the contactmethod.
		 */
		?>
		<label for="<?php echo $name; ?>"><?php echo apply_filters( "user_{$name}_label", $desc ); ?></label>
		<input type="text" class="input-text" name="<?php echo $name; ?>" id="<?php echo $name; ?>" value="<?php esc_attr_e($user->$name) ?>" />
	</p>
	<?php
		}
	?>

	<h4 class="bon-form-title"><?php _e('About Yourself', 'bon'); ?></h4>

	<p class="bon-form-row">
		<label for="account_description"><?php _e('Biographical Info', 'bon'); ?></label>
		<textarea name="account_description" id="account_description" rows="5" cols="30"><?php echo $user->description; // textarea_escaped ?></textarea>
	</p>

<?php
/** This filter is documented in wp-admin/user-new.php */
$show_password_fields = apply_filters( 'show_password_fields', true, $user );
if ( $show_password_fields ) :
?> 
	<p class="bon-form-row">
		<label for="password_1"><?php _e( 'Password (leave blank to leave unchanged)', 'bon' ); ?></label>
		<input type="password" class="input-text" name="password_1" id="password_1" />
	</p>
	<p class="bon-form-row">
		<label for="password_2"><?php _e( 'Repeat new password', 'bon' ); ?></label>
		<input type="password" class="input-text" name="password_2" id="password_2" />
	</p>
<?php endif; ?>

	<div class="clear"></div>

	<p><input type="submit" class="button" name="save_account_details" value="<?php _e( 'Save changes', 'bon' ); ?>" /></p>

	<?php wp_nonce_field( 'bon_save_account_details' ); ?>
	<input type="hidden" name="action" value="save_account_details" />
</form>

<?php do_action( 'bon_after_edit_account_form' ); ?>