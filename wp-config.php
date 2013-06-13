<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information
 * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'hmr');

/** MySQL database username */
define('DB_USER', 'hmr');

/** MySQL database password */
define('DB_PASSWORD', 'nd7gwpea');

/** MySQL hostname */
define('DB_HOST', 'andwcst.cxvefgi4j12x.us-east-1.rds.amazonaws.com');

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
define('AUTH_KEY',         'Iq^3MEX.x;H6Piau<A2Smax]*2PDWqiu<AMfTq.y{LEXq.y;I6Pibn^3<EYMf$r,');
define('SECURE_AUTH_KEY',  'BJc$v>F3QJczn,70F4RJczs|@0NGZRr>!4}FcVogz0|4:GdVoh-1[C5Osg@w[G4RK');
define('LOGGED_IN_KEY',    '8o@0|CcRk@0>BUJgzo@1|8VKdwo!5VNgzo|8:GdRkWOh-:#9SGdxl[C1KhVp_5:ia');
define('NONCE_KEY',        '3BQj@0,7bQjMcv!z}F4NkYs|80QkYr>@0JBUoZRk@s|C1KdVo[C4NgZs|z:G8Kdwp');
define('AUTH_SALT',        'Zhw[KdRk~1[CVKhth-:#9Slax]~9Sldw[~1Oexp_9;LeWqi+9;LeWti~2]eTm*2{E');
define('SECURE_AUTH_SALT', '2bn^v>F3NgYr,F7Qjcv>$MBUkYr|80JcRo!v0NBUngz}F4RdRk!w[CVOh-o[G4Nk');
define('LOGGED_IN_SALT',   'px#9Sleq.x{Eu<*ATmex]LeTf$q.6QIbuj^ALfTm*3{EXMj$IBUncv<AUMfy{^');
define('NONCE_SALT',       '@FVJn^v>BUNgzn,0|8RJdw[!4NCgzo!8}GZRk@gZs|z:G8Rkdw0JCVocv}!4NZOl~');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * WordPress Localized Language, defaults to English.
 *
 * Change this to localize WordPress. A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de_DE.mo to wp-content/languages and set WPLANG to 'de_DE' to enable German
 * language support.
 */
define('WPLANG', '');

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);
define('FS_METHOD', 'direct');

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
