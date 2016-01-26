<?php
/**
 * Include the Indeed PHP API
 */
require_once JOBIFY_ROOT . 'src' . DIRECTORY_SEPARATOR . 'indeed.php';

jobify_addAPI( array(
  'key'         => 'indeed',
  'title'       => __( 'Indeed', 'jobify' ),
  'logo'        => plugins_url( 'img/indeed.jpg' , JOBIFY_PLUGIN ),
  'getJobs'     => function( $args )
  {
    // Create the returned jobs array
    $jobs = array();

    // Get Jobify settings
    $settings = jobify_settings();

    // Set the Indeed publisher number
    $indeed_publisher_number = ( ! empty ( $args['indeed_publisher_number'] ) ) ? $args['indeed_publisher_number'] : '9769494768160125';

    // Check cache for results
    $results = wp_cache_get( 'jobs-indeed-' . jobify_string( $args ), 'jobify' );
    if ( false === $results )
    {
      // Query the Indeed PHP API
      $client = new Indeed( $indeed_publisher_number );
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

      $params['q']       = ( ! empty( $args['keyword'] ) ) ? $args['keyword'] : '';
      $params['l']       = ( ! empty( $args['location'] ) ) ? $args['location'] : false;
      $params['radius']  = ( ! empty( $args['indeed_radius'] ) ) ? $args['indeed_radius'] : '25';
      $params['sort']    = ( ! empty( $args['indeed_sort'] ) ) ? $args['indeed_sort'] : 'relevance';
      $params['limit']   = ( ! empty( $args['indeed_limit'] ) ) ? $args['indeed_limit'] : 10;
      $params['fromage'] = ( ! empty( $args['indeed_fromage'] ) ) ? $args['indeed_fromage'] : '';

      $results = $client->search( $params );
      if ( ! empty( $results['error'] ) )
      {
        // API error
        $jobs[] = array(
          'error'  => __( '<b>Indeed API Error:</b> ', 'jobify' ) . $results['error']
        );
      }
      else
      {
        // Save results to cache
        wp_cache_set( 'jobs-indeed-' . jobify_string( $args ), $results, 'jobify', 43200 ); // Half a day
        if ( count( $results['results'] ) > 0 ) {
          foreach ( $results['results'] as $key => $ary ) {
            // Add job to array
            $jobs[] = array(
              'title'    => $ary['jobtitle'],
              'company'  => $ary['company'],
              'city'     => $ary['city'],
              'state'    => $ary['state'],
              'country'  => $ary['country'],
              'desc'     => $ary['snippet'],
              'app_url'  => $ary['url'],
              'location' => $ary['formattedLocation']
            );
          }
        }
      }
    }

    return $jobs;
  },
  'options' => array(
    array(
      'group' => array(
        array(
          'title'   => __( 'Radius', 'jobify' ),
          'name'    => 'indeed_radius',
          'desc'    => __( 'Distance from search location.', 'jobify' ),
          'default' => '25',
          'type'    => 'number'
        ),
        array(
          'title'   => __( 'Number of Days', 'jobify' ),
          'name'    => 'indeed_fromage',
          'desc'    => __( 'Number of days back to search.', 'jobify' ),
          'default' => '30',
          'type'    => 'number'
        ),
      )
    ),
    array(
      'title'   => __( 'Limit', 'jobify' ),
      'name'    => 'indeed_limit',
      'desc'    => __( 'Max number of results from Indeed (Max. 25).', 'jobify' ),
      'default' => '10',
      'type'    => 'number'
    ),
  )
));