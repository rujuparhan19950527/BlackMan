<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'sql_manga_neko_c' );

/** Database username */
define( 'DB_USER', 'sql_manga_neko_c' );

/** Database password */
define( 'DB_PASSWORD', '44087e9ceb05d8' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'dxL!T|RBPj>}H`&glno?8iR{mLdd,Jes-bFeHD15 g |f%H$tg4esLr%wo4Vr7R=' );
define( 'SECURE_AUTH_KEY',  '`APgBtGNEn2<wo1}`8)a][U^w?~#^0mY5u]PRx|RbhAi,JEX~DrD#fCdzxEvQ.T@' );
define( 'LOGGED_IN_KEY',    ' K EC~LQv{ d<<n9)H}+4645SwXlIGv}3_|k%ruFF_vA>!e<3*UOq|@1`wYgL;tm' );
define( 'NONCE_KEY',        'ZICL/U~G9:`7TM9MvGCe-$AwBTHjAM#1N0DE>|Ul)PO4Yh!g.[vH-R9*tcJbFcZc' );
define( 'AUTH_SALT',        'h-P+E@9u-UJjbp`VPdTc}_R.pL>=(3UgPq`x +Q6detW(q u/)sDE0Br|a4N<s=6' );
define( 'SECURE_AUTH_SALT', 'id|4^yGi6eaJ+2],{#l4|@J>P=:9^bx}4 vzS0KO[*`i1x^qD;Y=lFSW!NaT[.Jx' );
define( 'LOGGED_IN_SALT',   '4;w|)vJ>]Pr7iN]IM+):x$y75@M(,Jbg~Z<fyl?Fqqqb!YM{xjH<;XvlH@jx2bl1' );
define( 'NONCE_SALT',       'U0raJXLrD!G^|j$jDqp+jDkQj8.xi$Hph`uU00EDY|4G6Eu`3po<q)@fdM84PM5}' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 *
 * At the installation time, database tables are created with the specified prefix.
 * Changing this value after WordPress is installed will make your site think
 * it has not been installed.
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/#table-prefix
 */
$table_prefix = 'wp_247737_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
 */
define( 'WP_DEBUG', true );

/* Add any custom values between this line and the "stop editing" line. */

define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
@ini_set('display_errors', 0);


/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
