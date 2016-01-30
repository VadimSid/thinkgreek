<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'think_greek_en');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', '');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '!we~cm1:~$H(=lNJT42^$|#Xj>l7i/j<4~1Zj]?*|b+Hm;d[TjH{m./(ya|.g5ss');
define('SECURE_AUTH_KEY',  'E7<%@+KnO-6%S8WO2QFJ;sU~Tw;`Saek[v%o5(rZ*_kPBs{j#>[20AOBmsuN5I1u');
define('LOGGED_IN_KEY',    '94wr+_rjzUoq<TVI.CzPAnRC+Po6{=*D^e*H,hQBIsw2dt+0}Ao#[c:b)w9*`XaX');
define('NONCE_KEY',        '~1c++u5PG3tbkEx|>:?QtZLoCGg13h=-# &FXBW`yb^L0!7Y9LXz@<&zR5aMu%*i');
define('AUTH_SALT',        '$@|TuLg?|9o(3RI+&g6~!u,+wSysU6_2x(hbS-NrC|s?H%e9()Jeloda(FG[3EXo');
define('SECURE_AUTH_SALT', '0<+e8:Mp|?cH~+L3ssnniVteQ-h|9ku?<QON_Zei_<Uijc5w 8 &8x^54cQ<z*IY');
define('LOGGED_IN_SALT',   'i|s!V0Hf}&vLAjj|aprdrdGOG&^7*Jr#jZo8p6^2 zlqyl^wm+OU+/amF`yc2v]?');
define('NONCE_SALT',       '+~5$>Y?zgRB}YA=VkWdLq4AW_0zrF-{m BV5Cwg6(w3bO4b1RqdRAW9;lr!xvmOU');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
