<?php
jobify_addAPI( array(
  'key'    => 'github_jobs',
  'title'   => __( 'GitHub Jobs', 'jobify' ),
  'logo'    => plugins_url( 'img/github-jobs.jpg' , JOBIFY_PLUGIN ),
  //'desc'    => __( 'A keyword is required to search on GitHub Jobs.', 'jobify' ),
  'getJobs' => function( $args ) {
    $jobs = array();

    $results = wp_cache_get( 'jobs-github-jobs-' . jobify_string( $args ), 'jobify' );
    if ( false === $results )
    {
      $link = 'https://jobs.github.com/positions.json?';

      if ( ! empty( $args['keyword'] ) ) {
        $link .= 'description=' . urlencode( $args['keyword'] ) . '&';
      }

      if ( ! empty( $args['location'] ) ) {
        $link .= 'location=' . urlencode( $args['location'] ) . '&';
      }

      if ( ! empty( $args['githubjobs_fulltime'] ) ) {
        $link .= 'full_time=' . urlencode( $args['githubjobs_fulltime'] );
      }

      /*if ( ! empty( $args['lat'] ) ) {
        $link .= 'lat=' . urlencode( $args['lat'] );
      }

      if ( ! empty( $args['lng'] ) ) {
        $link .= 'long=' . urlencode( $args['lng'] );
      }*/

      $results = json_decode( file_get_contents( $link ) );
      wp_cache_set( 'jobs-github-jobs-' . jobify_string( $args ), $results, 'jobify', 43200 ); // Half a day
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