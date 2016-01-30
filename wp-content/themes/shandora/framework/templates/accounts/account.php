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
<?php 
	
	if( isset( $args['form'] ) ) {

		if ( $args['form'] == 'lost_password' || $args['form'] == 'reset_password' )
			bon_get_template( 'accounts/form-lost-password.php', $args );

		if ( $args['form'] == 'edit_account' )
			bon_get_template( 'accounts/form-edit-account.php', $args );

		if ( $args['form'] == 'login' )
			bon_get_template( 'accounts/form-views.php', $args );
	
	} else {
		bon_get_template( 'accounts/my-account.php', $args );
	}

?>
