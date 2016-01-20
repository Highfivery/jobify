<?php
/**
 * Plugin Name: Jobify
 * Plugin URI: https://benmarshall.me/jobify
 * Description: Easily integrate job listings from all the major job posting sites like GitHub and CraigsList.
 * Version: 1.0.0
 * Author: Ben Marshall
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

jobify_addAPI( array(
  'title'   => __( 'GitHub Jobs', 'jobify' ),
  'logo'    => plugins_url( 'img/github-jobs.jpg' , JOBIFY_PLUGIN ),
  'name'    => 'githubjobs',
  'default' => '1',
  'getJobs' => function( $options ) {
    $jobs = array();

    $results = wp_cache_get( 'githubjobresults', 'jobify' );
    if ( false === $results )
    {
      $link = 'https://jobs.github.com/positions.json?';

      if ( ! empty( $options['githubjobs_keyword'] ) ) {
        $link .= 'description=' . urlencode( $options['githubjobs_keyword'] ) . '&';
      }

      if ( ! empty( $options['githubjobs_location'] ) ) {
        $link .= 'location=' . urlencode( $options['githubjobs_location'] ) . '&';
      }

      if ( ! empty( $options['githubjobs_fulltime'] ) ) {
        $link .= 'full_time=' . urlencode( $options['githubjobs_fulltime'] );
      }

      if ( ! empty( $options['githubjobs_lat'] ) ) {
        $link .= 'lat=' . urlencode( $options['githubjobs_lat'] );
      }

      if ( ! empty( $options['githubjobs_long'] ) ) {
        $link .= 'long=' . urlencode( $options['githubjobs_long'] );
      }

      $results = json_decode( file_get_contents( $link ) );
      wp_cache_set( 'githubjobresults', $results, 'jobify', 43200 ); // Half a day
    }

    if ( count( $results ) > 0 )
    {
      foreach ( $results as $key => $obj ) {
        $jobs[] = array(
          'title'    => $obj->title,
          'company'  => $obj->company,
          'desc'     => $obj->description,
          'url'      => $obj->url,
          'location' => $obj->location
        );
      }
    }

    return $jobs;
  },
  'options' => array(
    array(
      'title'   => __( 'Keyword', 'jobify' ),
      'name'    => 'githubjobs_keyword',
      'desc'    => __( 'A search term, such as "ruby" or "java".', 'jobify' ),
      'default' => ''
    ),
    array(
      'title'   => __( 'Location', 'jobify' ),
      'name'    => 'githubjobs_location',
      'desc'    => __( 'A city name, zip code, or other location search term.', 'jobify' ),
      'default' => ''
    ),
    array(
      'group' => array(
        array(
          'title'   => __( 'Latitude', 'jobify' ),
          'name'    => 'githubjobs_lat',
          'desc'    => __( 'A specific latitude.', 'jobify' ),
          'default' => ''
        ),
        array(
          'title'   => __( 'Longitude', 'jobify' ),
          'name'    => 'githubjobs_long',
          'desc'    => __( 'A specific longitude.', 'jobify' ),
          'default' => ''
        ),
      ),
      'desc' => __( 'If latitude and longitude used, do <b>not</b> set location.', 'jobify' )
    ),
    array(
      'title'   => __( 'Full-time', 'jobify' ),
      'name'    => 'githubjobs_fulltime',
      'desc'    => __( 'If you want to limit results to full time positions.', 'jobify' ),
      'type'    => 'checkbox',
      'options' => array(
        'yes' => __( 'Yes', 'jobify' )
      ),
      'default' => ''
    ),
  )
));

jobify_addAPI( array(
  'title'   => __( 'Indeed', 'jobify' ),
  'logo'    => plugins_url( 'img/indeed.jpg' , JOBIFY_PLUGIN ),
  'name'    => 'indeed',
  'default' => '1',
  'getJobs' => function( $options ) {
    $jobs   = array();
    if ( ! empty ( $options['indeed_publisher_number'] ) )
    {
      $results = wp_cache_get( 'indeedresults', 'jobify' );
      if ( false === $results )
      {
        $client = new Indeed( $options['indeed_publisher_number'] );
        $params   = array(
          'userip'    => jobify_get_ip(),
          'useragent' => $_SERVER['HTTP_USER_AGENT'],
          'format'    => 'json',
          'raw'       => false,
          'start'     => 0,
          'highlight' => 0,
          'filter'    => 1,
          'latlong'   => 1,
          //'co'      => 'us'
        );

        $params['q']       = ( ! empty( $options['indeed_keyword'] ) ) ? $options['indeed_keyword'] : '';
        $params['l']       = ( ! empty( $options['indeed_location'] ) ) ? $options['indeed_location'] : '';
        $params['radius']  = ( ! empty( $options['indeed_radius'] ) ) ? $options['indeed_radius'] : '25';
        $params['sort']    = ( ! empty( $options['indeed_sort'] ) ) ? $options['indeed_sort'] : 'relevance';
        $params['limit']   = ( ! empty( $options['indeed_limit'] ) ) ? $options['indeed_limit'] : 10;
        $params['fromage'] = ( ! empty( $options['indeed_fromage'] ) ) ? $options['indeed_fromage'] : '';

        $results = $client->search( $params );
        wp_cache_set( 'indeedresults', $results, 'jobify', 43200 ); // Half a day
      }

      if ( count( $results['results'] ) > 0 ) {
        foreach ( $results['results'] as $key => $ary ) {
          $jobs[] = array(
            'title'    => $ary['jobtitle'],
            'company'  => $ary['company'],
            'city'     => $ary['city'],
            'state'    => $ary['state'],
            'country'  => $ary['country'],
            'desc'     => $ary['snippet'],
            'url'      => $ary['url'],
            'location' => $ary['formattedLocation']
          );
        }
      }
    }

    //print_r($results);

    return $jobs;
  },
  'options' => array(
    array(
      'title'   => __( 'Publisher number', 'jobify' ),
      'name'    => 'indeed_publisher_number',
      'desc'    => __( 'If you do not have a publisher number, you can receive one by heading to the <a href="https://ads.indeed.com/jobroll/xmlfeed" target="_blank">Indeed Publisher Portal</a>.', 'jobify' ),
      'default' => ''
    ),
    array(
      'title'   => __( 'Keyword', 'jobify' ),
      'name'    => 'indeed_keyword',
      'desc'    => __( 'By default terms are ANDed. To see what is possible, use our <a href="http://www.indeed.com/advanced_search" target="_blank">advanced search page</a> to perform a search and then check the url for the q value.', 'jobify' ),
      'default' => ''
    ),
    array(
      'title'   => __( 'Location', 'jobify' ),
      'name'    => 'indeed_location',
      'desc'    => __( 'Use a postal code or a "city, state/province/region" combination.', 'jobify' ),
      'default' => ''
    ),
    array(
      'group' => array(
        array(
          'title'   => __( 'Sort', 'jobify' ),
          'name'    => 'indeed_sort',
          'desc'    => __( 'Sort by relevance or date.', 'jobify' ),
          'default' => 'relevance',
          'type'    => 'select',
          'options' => array(
            'relevance' => __( 'Relevance', 'jobify' ),
            'date'      => __( 'Date', 'jobify' )
          )
        ),
        array(
          'title'   => __( 'Radius', 'jobify' ),
          'name'    => 'indeed_radius',
          'desc'    => __( 'Distance from search location ("as the crow flies").', 'jobify' ),
          'default' => '25',
          'type'    => 'number'
        ),
      )
    ),
    array(
      'group' => array(
        array(
          'title'   => __( 'Limit', 'jobify' ),
          'name'    => 'indeed_limit',
          'desc'    => __( 'Maximum number of results returned per query. Default is 10, max 25.', 'jobify' ),
          'default' => '10',
          'type'    => 'number'
        ),
        array(
          'title'   => __( 'From Age', 'jobify' ),
          'name'    => 'indeed_fromage',
          'desc'    => __( 'Number of days back to search.', 'jobify' ),
          'default' => '30',
          'type'    => 'number'
        ),
      )
    ),
    /*array(
      'title'   => __( 'Full-time', 'jobify' ),
      'name'    => 'githubjobs_fulltime',
      'desc'    => __( 'If you want to limit results to full time positions.', 'jobify' ),
      'type'    => 'checkbox',
      'options' => array(
        'yes' => __( 'Yes', 'jobify' )
      ),
      'default' => ''
    ),*/
  )
));
