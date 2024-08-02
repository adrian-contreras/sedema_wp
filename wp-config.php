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
define( 'DB_NAME', 'ecsedemacdmx' );

/** Database username */
define( 'DB_USER', 'cdmxsedemaec' );

/** Database password */
define( 'DB_PASSWORD', 'B453d4t0SDGkw4as' );

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
define('AUTH_KEY',         'BY]Al.az+M}B4{t;CE/iW^|kh1+]q X|oA4|&LPb!2}%35zX0,Z:8sDgS9,06Zs5');
define('SECURE_AUTH_KEY',  '2T;5`e}@4`4 Vzx,{0|!`f$0Rz>]++sK[WT5mX6l)Ll7PQg[P@_UwYl[<TQ1ic4}');
define('LOGGED_IN_KEY',    'N2|Ei`ljD^1(|[TsDMC+$o@YrxbFpOG4!6#++E{GSa(l}TxukoO4{-*ACscK}2So');
define('NONCE_KEY',        '<BM%#/6$0OJkd<cfV$k+Y//LJcyv/}r;Tr%~l5>`K.OqpoMjo&o*{^+S4GJ (>)$');
define('AUTH_SALT',        '~!uz8+Vn}!z!~q7=ZWXDphQKnOh?B_*40D.szNi/x/X7nnQ~Mz+:bd9IjCC?7ASZ');
define('SECURE_AUTH_SALT', 'vTU+6JB-(Y{9+o)f*TjMQ|+CQar.|1H{;B0%K}Rk<Dl0k{xs:BX|/ln:X(J]!L__');
define('LOGGED_IN_SALT',   't_nI,x%dOaEu2zQ+5Veyej#kzlNJ7agA[m=/zr+/>9$/cFU1yH)L=JT).K6Qdghh');
define('NONCE_SALT',       'BU<U8I8AH (oCy&jtFM[C1h5( HhieI>&Uuny+AK!XfJWZ6g[%cPdOUs>}NwqV@l');

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
// Habilitar el modo WP_DEBUG
define( 'WP_DEBUG', true );
// Habilite el registro de depuración en el archivo /wp-content/debug.log
define( 'WP_DEBUG_LOG', true );
// Desactiva la visualización de errores y advertencias
define( 'WP_DEBUG_DISPLAY', false );
// Desactiva las actualizaciones de WordPres, las de los plugins, las de los themes
define('AUTOMATIC_UPDATER_DISABLED', true);
// Desactiva las actualziaciones solo de WordPress
define( 'WP_AUTO_UPDATE_CORE', false );

@ini_set( 'display_errors', 0 );

/* Add any custom values between this line and the "stop editing" line. */

define('FFMPEG_PATH', '/usr/local/bin/ffmpeg');
define( "BB_FFMPEG_BINARY_PATH", "/usr/local/bin/ffmpeg" );
define( "BB_FFPROBE_BINARY_PATH", "/usr/local/bin/ffmpeg" );

define('WP_MEMORY_LIMIT', '256M');  // Ajusta según tus necesidades
define('WP_MAX_UPLOAD_SIZE', 52428800);  // 50MB en bytes

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
