<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * Localized language
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'local' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', 'root' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

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
define( 'AUTH_KEY',          'nqOdu*du8adMf%>{dt;n|(M$m76wV|I}c7<Xo-n=4Wp2Ru RYBdV]GGcP$k3u+25' );
define( 'SECURE_AUTH_KEY',   'L|fJn85R<Eho.PfQx>qA0UwTM<#!1Iem$&pDZDr2;!s Sm.<htvlTJuZb9D=^GP9' );
define( 'LOGGED_IN_KEY',     '16O8z~{,U=Joe~j8I?,ko1p<(cWIjA234-EkyCjpn65shf12jlounEUg&S#/[C*b' );
define( 'NONCE_KEY',         'r?Pi|J*SKD>Mj,TH/;F@rzNg[l_h(l2tBtH;/J;_ #Vz>GB=8Xk3X~Xa?XBD%#i%' );
define( 'AUTH_SALT',         ' @d^/FHEHh|VX)-;0vqtE&gksXqE$DOO^x$]C)|DWX^[d|rh!6[4t1c6L#<^L{!.' );
define( 'SECURE_AUTH_SALT',  'F9Js<:rISZHLi~rFbe-A^y8h2P4#S!43t=^WkJ;?[S*>g kHlYk/oizka*]HIh7t' );
define( 'LOGGED_IN_SALT',    'c;Ux5P9(l6)_E6FzV{:xR)xsUq{;Kfgm_(6 Gy#Y$+)kc.m=2^#(!k)S3e? FA#^' );
define( 'NONCE_SALT',        'xK>~5!xuDYb:1)0obtX+_I!F%minpm2X;+g9Rgf_b5nnNtk z(}==:kD%?uGhDql' );
define( 'WP_CACHE_KEY_SALT', '(q%6S)ivHngP3=KlX%; m=nIGH:hq$H_y{e>UPENOa:2/2`>(u~j(6|HK[D=P%)X' );
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';


/* Add any custom values between this line and the "stop editing" line. */



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
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
if ( ! defined( 'WP_DEBUG' ) ) {
	define( 'WP_DEBUG', false );
}

define( 'WP_ENVIRONMENT_TYPE', 'local' );
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
