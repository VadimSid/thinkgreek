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
<?php do_action( 'bon_before_lost_password_form' ); ?>

<form method="post" class="<?php echo apply_filters( 'bon_lost_password_form_class', 'bon-lost-password-form' ); ?>">

	<?php if( 'lost_password' == $args['form'] ) : ?>

        <p><?php echo apply_filters( 'bon_lost_password_message', __( 'Lost your password? Please enter your username or email address. You will receive a link to create a new password via email.', 'bon' ) ); ?></p>

        <p class="bon-form-row">
            <label for="user_login"><?php _e( 'Username or email', 'bon' ); ?></label> 
            <input class="input-text" type="text" name="user_login" id="user_login" />
        </p>

	<?php else : ?>

        <p><?php echo apply_filters( 'bon_reset_password_message', __( 'Enter a new password below.', 'bon') ); ?></p>

        <p class="bon-form-row">
            <label for="password_1"><?php _e( 'New password', 'bon' ); ?> <span class="required">*</span></label>
            <input type="password" class="input-text" name="password_1" id="password_1" />
        </p>
        <p class="bon-form-row">
            <label for="password_2"><?php _e( 'Re-enter new password', 'bon' ); ?> <span class="required">*</span></label>
            <input type="password" class="input-text" name="password_2" id="password_2" />
        </p>

        <input type="hidden" name="reset_key" value="<?php echo isset( $args['key'] ) ? $args['key'] : ''; ?>" />
        <input type="hidden" name="reset_login" value="<?php echo isset( $args['login'] ) ? $args['login'] : ''; ?>" />

    <?php endif; ?>

    <div class="clear"></div>

    <p class="bon-form-row">
        <input type="submit" class="button" name="reset_password" value="<?php echo 'lost_password' == $args['form'] ? __( 'Reset Password', 'bon' ) : __( 'Save', 'bon' ); ?>" />
    </p>
    
	<?php wp_nonce_field( 'bon_' . $args['form'] ); ?>

</form>

<?php do_action( 'bon_after_lost_password_form' ); ?>