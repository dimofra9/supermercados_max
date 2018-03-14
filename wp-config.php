<?php
/** 
 * Configuración básica de WordPress.
 *
 * Este archivo contiene las siguientes configuraciones: ajustes de MySQL, prefijo de tablas,
 * claves secretas, idioma de WordPress y ABSPATH. Para obtener más información,
 * visita la página del Codex{@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} . Los ajustes de MySQL te los proporcionará tu proveedor de alojamiento web.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** Ajustes de MySQL. Solicita estos datos a tu proveedor de alojamiento web. ** //
/** El nombre de tu base de datos de WordPress */
define('DB_NAME', 'supermercadosmax_db');

/** Tu nombre de usuario de MySQL */
define('DB_USER', 'nextu');

/** Tu contraseña de MySQL */
define('DB_PASSWORD', '12345');

/** Host de MySQL (es muy probable que no necesites cambiarlo) */
define('DB_HOST', 'localhost');

/** Codificación de caracteres para la base de datos. */
define('DB_CHARSET', 'utf8mb4');

/** Cotejamiento de la base de datos. No lo modifiques si tienes dudas. */
define('DB_COLLATE', '');

/**#@+
 * Claves únicas de autentificación.
 *
 * Define cada clave secreta con una frase aleatoria distinta.
 * Puedes generarlas usando el {@link https://api.wordpress.org/secret-key/1.1/salt/ servicio de claves secretas de WordPress}
 * Puedes cambiar las claves en cualquier momento para invalidar todas las cookies existentes. Esto forzará a todos los usuarios a volver a hacer login.
 *
 * @since 2.6.0
 */
define('AUTH_KEY', 'DTG6F%m3;:0d_(d~eD3Qn.kZ2pnFd+:<{MUTWvCa0aQ>c0LF:8Q^{qc<LEm+C<-@');
define('SECURE_AUTH_KEY', 'K)w5IG&f?st~s|3Uu3=8Fp?uAPVnug3<=r:$H1GQ]:C@N:[MT^xgPsk=`@DkP9yk');
define('LOGGED_IN_KEY', 'I?m{Kjz=c2Y9hxC<#Z!N?To9.wB:IH823,N|*;MJ/B1ysru$n$r<,m}xan/{Xr#.');
define('NONCE_KEY', '^XZ{3g ]<j:>SKHNTMj@~c/F+iEF<ius1uQC0#pXu$<JAy}vCr`*lKEZkuGl.mw.');
define('AUTH_SALT', '1HS)q*B9z,RL2[3s/j$-n+Bp1/4pk&]E7<%dCgdm70n&e&(nyXl#3eaVC/3-:d6j');
define('SECURE_AUTH_SALT', 'aDs4I2*MKrGhlVG&&8rNS;_[io@UjO@kdWv=7CHs=>1KDc0@AC+4jRxcCFSWVUSJ');
define('LOGGED_IN_SALT', 'ErVq+BR>mmO=BShA*:3!e%nxsJ?R|2J$D!Mt4o$``Z}SDrTF1r0LjOFkraI@<Odk');
define('NONCE_SALT', 'Fr US,DxHA-)bN&p,U,.)xI{N=qOSSz.,fmy@-@2TU3T/c-* P*qs##+A_fE1yUn');

/**#@-*/

/**
 * Prefijo de la base de datos de WordPress.
 *
 * Cambia el prefijo si deseas instalar multiples blogs en una sola base de datos.
 * Emplea solo números, letras y guión bajo.
 */
$table_prefix  = 'wp_';


/**
 * Para desarrolladores: modo debug de WordPress.
 *
 * Cambia esto a true para activar la muestra de avisos durante el desarrollo.
 * Se recomienda encarecidamente a los desarrolladores de temas y plugins que usen WP_DEBUG
 * en sus entornos de desarrollo.
 */
define('WP_DEBUG', false);

/* ¡Eso es todo, deja de editar! Feliz blogging */

/** WordPress absolute path to the Wordpress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');

