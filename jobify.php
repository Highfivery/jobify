<?php
/**
 * Plugin Name: Jobify
 * Plugin URI: https://benmarshall.me/jobify
 * Description: Easily integrate job listings from all the major job posting sites like GitHub and Indeed.
 * Version: 1.0.0
 * Author: Ben Marshall
 * Text Domain: jobify
 * Domain Path: /languages
 * Author URI: https://benmarshall.me
 * License: GPL2
 */

/*  Copyright 2015  Ben Marshall  (email : me@benmarshall.me)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License, version 2, as
  published by the Free Software Foundation.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/**
 * Security Note: Blocks direct access to the plugin PHP files.
 */
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

// Define constants.
if( ! defined( 'JOBIFY_ROOT ' ) ) {
  define( 'JOBIFY_ROOT', plugin_dir_path( __FILE__ ) );
}

if( ! defined( 'JOBIFY_PLUGIN ' ) ) {
  define( 'JOBIFY_PLUGIN', __FILE__ );
}

// Define globals.
$jobifyAPIs = array();

/**
 * Include the plugin helpers.
 */
require_once JOBIFY_ROOT . 'src' . DIRECTORY_SEPARATOR . 'helpers.php';

/**
 * Include widgets.
 */
require_once JOBIFY_ROOT . 'src' . DIRECTORY_SEPARATOR . 'Jobify/JobsWidget.class.php';

/**
 * Include add-ons.
 */
require_once JOBIFY_ROOT . 'src' . DIRECTORY_SEPARATOR . 'indeed.php';

spl_autoload_register( 'jobify_autoloader' );
function jobify_autoloader( $class_name ) {
  if ( false !== strpos( $class_name, 'Jobify' ) ) {
    $classes_dir = JOBIFY_ROOT . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR;
    $class_file = str_replace( '_', DIRECTORY_SEPARATOR, $class_name ) . '.php';
    require_once $classes_dir . $class_file;
  }
}

function jobify_githubjobs_api()
{
  global $jobifyAPIs;

  // Load the plugin features.
  $plugin            = new Jobify_Plugin();
  $plugin['widgets'] = new Jobify_Widgets();

  // Initialize the plugin.
  $plugin->run();
}
add_action( 'plugins_loaded', 'jobify_githubjobs_api' );

/**
 * Include APIs.
 */
require_once JOBIFY_ROOT . 'src' . DIRECTORY_SEPARATOR . 'APIs' . DIRECTORY_SEPARATOR . 'github.php';
require_once JOBIFY_ROOT . 'src' . DIRECTORY_SEPARATOR . 'APIs' . DIRECTORY_SEPARATOR . 'indeed.php';
require_once JOBIFY_ROOT . 'src' . DIRECTORY_SEPARATOR . 'APIs' . DIRECTORY_SEPARATOR . 'usajobs.php';
