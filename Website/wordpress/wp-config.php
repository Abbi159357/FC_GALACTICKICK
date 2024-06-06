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
 * @link https://wordpress.org/documentation/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'fcgalactickick' );

/** Database username */
define( 'DB_USER', 'bit_academy' );

/** Database password */
define( 'DB_PASSWORD', 'bit_academy' );

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
define( 'AUTH_KEY',         '9$?,g8SX@_VHJ}2<F<.!XV$>S9hA(&mm5/mQ@TE5V/E8|09L0b4C+g>,vF<jdjqM' );
define( 'SECURE_AUTH_KEY',  'X+H7.#3~q`e3jFEcl2by1CPO?~R$X1C8 c;o _!yFD@M{il2L}H|H|s0#SI<|+!o' );
define( 'LOGGED_IN_KEY',    'dMPPh]J?{XiOU_[>n<}(b8r%L:sZI+n&Op=*!6=_8(soLXat^#$0j`smU*lY2+(s' );
define( 'NONCE_KEY',        '@[l~azP`-h&%$4zUlFcCK[t>VimnKgQ}oeur&aa% PE+t??EXj^i7;44H0y0@6/H' );
define( 'AUTH_SALT',        'hslYbeZ|K^.iS@K9IuUW+3`kjM.i/p.f&S8^Fd:_bR#xr0}O=zBtR.( jQ_XyWgb' );
define( 'SECURE_AUTH_SALT', '`[$RixGwa={Z6O` t{N,_s}#IABW <EHTldQJv*1 :ap}CE_u {@ocDPO.zd4AL6' );
define( 'LOGGED_IN_SALT',   ' xe;Za9wZrET.^Znc?{G^l/*s1Soa2K}.,1?vbbx)mvwXM;JB%y_ XEmw4JZuqD`' );
define( 'NONCE_SALT',       'Dy;mLV-7T%SXBVU%_#;uDf<%>LKMx <XdEudI>(wVcE5jk)u18Lgh`j[$Wm%!qZ=' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

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
 * @link https://wordpress.org/documentation/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
