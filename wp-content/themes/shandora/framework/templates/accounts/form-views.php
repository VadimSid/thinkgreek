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

<?php do_action( 'bon_before_login_form' ); ?>

<div class="bon-account-login <?php echo ( get_option('users_can_register') ) ? 'bon-account-can-register' : ''; ?>" id="bon-account-login">

	<div class="<?php echo apply_filters( 'bon_login_form_wrap', 'bon-login-form-wrap' ); ?>">
		<?php bon_get_template( 'accounts/form-login.php', $args ); ?>
	</div>

	<?php if ( get_option( 'users_can_register' ) ) : ?>

		<div class="<?php echo apply_filters( 'bon_register_form_wrap', 'bon-register-form-wrap' ); ?>">
			<?php bon_get_template( 'accounts/form-register.php' ); ?>
		</div>

	<?php endif; ?>

</div>

<?php do_action( 'bon_after_login_form' ); ?>