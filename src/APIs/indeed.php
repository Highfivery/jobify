<?php
jobify_addAPI( array(
  'title'   => __( 'Indeed', 'jobify' ),
  'logo'    => plugins_url( 'img/indeed.jpg' , JOBIFY_PLUGIN ),
  'name'    => 'indeed',
  'getJobs' => function( $options ) {
    $settings = jobify_settings();
    $jobs     = array();

    $options['indeed_publisher_number'] = ( ! empty ( $settings['indeed_publisher_number'] ) ) ? $settings['indeed_publisher_number'] : '9769494768160125';

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
      if ( ! empty( $results['error'] ) )
      {
        $jobs[] = array(
          'error'  => __( '<b>Indeed API Error:</b> ', 'jobify' ) . $results['error']
        );
      }
      else
      {
        wp_cache_set( 'indeedresults', $results, 'jobify', 43200 ); // Half a day
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
    }

    //print_r($results);

    return $jobs;
  },
  'options' => array(
    array(
      'title'   => __( 'Keyword', 'jobify' ),
      'name'    => 'indeed_keyword',
      'desc'    => __( 'A search term, such as "ruby" or "java".', 'jobify' ),
      'default' => ''
    ),
    array(
      'title'   => __( 'Location', 'jobify' ),
      'name'    => 'indeed_location',
      'desc'    => __( 'A city name, zip code, or other location search term.', 'jobify' ),
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
          'desc'    => __( 'Distance from search location.', 'jobify' ),
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
          'desc'    => __( 'Max number of results from Indeed (Max. 25).', 'jobify' ),
          'default' => '10',
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
    )
  )
));