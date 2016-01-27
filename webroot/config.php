<?php
/**
 * Config-file for Master. Change settings here to affect installation.
 *
 */

/**
 * Set the error reporting.
 *
 */
error_reporting(-1);              // Report all type of errors
ini_set('display_errors', 1);     // Display all errors 
ini_set('output_buffering', 0);   // Do not buffer outputs, write directly


/**
 * Define Master paths.
 *
 */
define('MASTER_INSTALL_PATH', __DIR__ . '/..');
define('MASTER_THEME_PATH', MASTER_INSTALL_PATH . '/theme/render.php');


/**
 * Include bootstrapping functions.
 *
 */
include(MASTER_INSTALL_PATH . '/src/bootstrap.php');


/**
 * Start the session.
 *
 */
session_name(preg_replace('/[^a-z\d]/i', '', __DIR__));
session_start();

/**
 * Create the Master variable.
 *
 */
$master = array();


/**
 * Site wide settings.
 *
 */
$master['lang']         = 'sv';
$master['title_append'] = ' | Master - en webbtemplate';

$master['header'] = <<<EOD
</form>
EOD;

$master['footer'] = <<<EOD

EOD;

/**
 * Theme related settings.
 *
 */
$master['stylesheets'] = array('css/style.css', 'css/calendar.css', 'css/gallery.css', 'css/breadcrumb.css', 'css/figure.css', 'css/dice.css');
$master['favicon']    = 'favicon.ico';

/**
 * The navbar
 *
 */
//$master['navbar'] = null; // To skip the navbar
$master['menu'] = array(
        'filmer' => array('text'=>'Filmer', 'url'=>'filmer.php'),
        'nyheter' => array('text'=>'Nyheter', 'url'=>'content_blog.php'),
		'om' => array('text'=>'Om RM Rental Movies', 'url'=>'om.php'),
		'tävling' => array('text'=>'Tävling', 'url'=>'dice.php'),
		'filmkalender' => array('text'=>'Filmkalender', 'url'=>'filmkalender.php?month=01&year=2016'),
		'my_account' => array('text'=>'Mitt konto', 'url'=>'my_account.php')
		);

/**
 * Settings for JavaScript.
 *
 */
$master['modernizr'] = 'js/modernizr.js';
$master['jquery'] = '//ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js';
//$master['jquery'] = null; // To disable jQuery
$master['javascript_include'] = array();
//$master['javascript_include'] = array('js/main.js'); // To add extra javascript files


/**
 * Google analytics.
 *
 */
$master['google_analytics'] = 'UA-22093351-1'; // Set to null to disable google analytics

/**
 * Settings for the database.
 *
 */
$master['database']['dsn']            = 'insert the dsn';
$master['database']['username']       = DB_USER;
$master['database']['password']       = DB_PASSWORD;
$master['database']['driver_options'] = array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'");
