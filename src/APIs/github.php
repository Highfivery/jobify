<?php
jobify_addAPI( array(
  'title'   => __( 'GitHub Jobs', 'jobify' ),
  'logo'    => plugins_url( 'img/github-jobs.jpg' , JOBIFY_PLUGIN ),
  'name'    => 'githubjobs',
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