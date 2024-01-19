<?php
define( 'WP_CACHE', true );
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
define( 'DB_NAME', 'u305902363_DXeh7' );

/** Database username */
define( 'DB_USER', 'u305902363_TP3bC' );

/** Database password */
define( 'DB_PASSWORD', '8Cc5YKuJFB' );

/** Database hostname */
define( 'DB_HOST', '127.0.0.1' );

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
define( 'AUTH_KEY',          'C7lgQk-6o)0Dd!^h)i.vU[*M,1$j!Ie>WK]I<~KGvQ8c-13=;;e~^*)#ZKZ]fzbY' );
define( 'SECURE_AUTH_KEY',   'RdS_&^?J^`9,P2W2cLq,X=yHwxGZ|FVKWYYq5.ix:MeY0[V4MO%t<|8;mw#pBjcY' );
define( 'LOGGED_IN_KEY',     'MuT9f_ T?ZtGjZXKgyc;}NBRR^jdrrE-tsytnMTVNOl{|j(RXr(ftbyBERiAqoAR' );
define( 'NONCE_KEY',         'R9k+Nw`VIV)x2)<Nv[kCjHCb5eJ50weLnD* GaH+(:S|$a<|v<m6m4{;@k/op0t3' );
define( 'AUTH_SALT',         '#t6W ?kh)~(8e=M+x&v^^Jx#pu(;FD2XJC71TXay+m^oB`Aqma1oXd+uNeUwiNG8' );
define( 'SECURE_AUTH_SALT',  'Kg4ACOLdPdAe$zp^;z`cB]r*Iaw!}^G8/KXBSp32J}+)$d[%7v>b28?vBa~Q1bhE' );
define( 'LOGGED_IN_SALT',    '$x1f7Nu!14Z!/)Xpo,S; qmYDEKm_E)#x7puF>`V;lSL@B&ZDkpt;CMokWwX[<YB' );
define( 'NONCE_SALT',        '`hN4pXF^$m>(?[B_O^!S40dE!6nn1{d=Q*LKlF(zvZ}?1c~`0^0-s<d-jWMLEZ]q' );
define( 'WP_CACHE_KEY_SALT', 'SWaT89aHDE](/}dvg?slq{o}EbfB27G.%RSNve{D5qv~80tY.YS}v6w$Ln i|T[6' );


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
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );


/* Add any custom values between this line and the "stop editing" line. */



define( 'FS_METHOD', 'direct' );
define( 'WP_AUTO_UPDATE_CORE', 'minor' );
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
