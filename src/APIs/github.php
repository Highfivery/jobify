<?php
jobify_addAPI( array(
  'title'   => __( 'GitHub Jobs', 'jobify' ),
  'logo'    => plugins_url( 'img/github-jobs.jpg' , JOBIFY_PLUGIN ),
  'name'    => 'githubjobs',
  //'desc'    => __( 'A keyword is required to search on GitHub Jobs.', 'jobify' ),
  'getJobs' => function( $options ) {
    $jobs = array();

    $results = wp_cache_get( 'githubjobresults', 'jobify' );
    if ( false === $results )
    {
      $link = 'https://jobs.github.com/positions.json?';

      if ( ! empty( $options['keyword'] ) ) {
        $link .= 'description=' . urlencode( $options['keyword'] ) . '&';
      }

      if ( ! empty( $options['location'] ) ) {
        $link .= 'location=' . urlencode( $options['location'] ) . '&';
      }

      if ( ! empty( $options['githubjobs_fulltime'] ) ) {
        $link .= 'full_time=' . urlencode( $options['githubjobs_fulltime'] );
      }

      /*if ( ! empty( $options['lat'] ) ) {
        $link .= 'lat=' . urlencode( $options['lat'] );
      }

      if ( ! empty( $options['lng'] ) ) {
        $link .= 'long=' . urlencode( $options['lng'] );
      }*/

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
          'app_url'  => $obj->url,
          'location' => $obj->location
        );
      }
    }

    return $jobs;
  },
  'options' => array(
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