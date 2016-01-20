<?php
jobify_addAPI( array(
  'title'   => __( 'Indeed', 'jobify' ),
  'logo'    => plugins_url( 'img/indeed.jpg' , JOBIFY_PLUGIN ),
  'name'    => 'indeed',
  'getJobs' => function( $options ) {
    $jobs   = array();

    $options['indeed_publisher_number'] = ( ! empty ( $options['indeed_publisher_number'] ) ) ? $options['indeed_publisher_number'] : '9769494768160125';

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