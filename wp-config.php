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
define('DB_NAME', 'howard_wp_hfd_site');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', '');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

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
define('AUTH_KEY',         'F#oi(D<^+(nFq@t.5@tMca*&{w;7 Y|1?Hm ]EPCYBV26#r]ml:O$@z`ox*j9Z$u');
define('SECURE_AUTH_KEY',  'sQPkqK}7Kk~ G7cfL,jyyI.iO!CXMwSr_v??ONaM]5[YJ.MtXj|Gf1*[HD6{m9Yx');
define('LOGGED_IN_KEY',    'JZ(T+<WtiM(vm;b^FSTfS,}v0;e{F}w>1-a>m[2M$Zj-wGk]P9P-/9{5rvmwOf_=');
define('NONCE_KEY',        'Mka{g^ )>yI{u_{C_K,71J0*An#oS|)VU{,@%+A%|~ejfxIM1w|h1;9Q,psz5!<Z');
define('AUTH_SALT',        ')7 +l&3pqu<4Sw3|jO+GUOM{JnT6ilh)Np6|rMYqYA9=*J_[-azfe(R%*d^H:aHr');
define('SECURE_AUTH_SALT', 'ST2t|/ppJ*H*vu7{@g+SH`[sw^4tFL;bvdSaL2tj!pd;2a<:k-{qV@#hCJXm|fcV');
define('LOGGED_IN_SALT',   'D>$G,v9&GC@R{u;Df.Ck@w+P?T<bw/.S5:)e7TzH+~XW^N9&Sl3KS>x;O$p5qT5.');
define('NONCE_SALT',       'aD0BW&8R_QF^jVyw#,_jGNZq?@7pW.t5|H?5Y}ujLz2!gAEG_OXykbFUhMaa*GfM');

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
