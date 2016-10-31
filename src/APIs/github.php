<?php
jobify_addAPI( array(
  'key'    => 'github_jobs',
  'title'   => __( 'GitHub Jobs', 'jobify' ),
  'logo'    => plugins_url( 'img/github-jobs.jpg' , JOBIFY_PLUGIN ),
  // Since 1.4.0
  'requirements' => array(
    'geolocation' => __( 'Supports geolocation if enabled.', 'jobify' )
  ),
  'getJobs' => function( $args ) {
    $jobs = array();

    $results = wp_cache_get( 'jobs-github-jobs-' . jobify_string( $args ), 'jobify' );
    if ( false === $results )
    {
      $link = 'https://jobs.github.com/positions.json?';

      if ( ! empty( $args['keyword'] ) ) {
        $link .= 'description=' . urlencode( $args['keyword'] ) . '&';
      }

      // Location
      if ( ! empty( $args['lat'] ) && ! empty( $args['lng'] ) ) {
        $link .= 'lat=' . urlencode( $args['lat'] ) . '&';
        $link .= 'long=' . urlencode( $args['lng'] ) . '&';
      } elseif ( ! empty( $args['location'] ) ) {
        $link .= 'location=' . urlencode( $args['location'] ) . '&';
      }

      if ( ! empty( $args['githubjobs_fulltime'] ) ) {
        $link .= 'full_time=' . urlencode( $args['githubjobs_fulltime'] );
      }

      $results = json_decode( file_get_contents( $link ) );
      wp_cache_set( 'jobs-github-jobs-' . jobify_string( $args ), $results, 'jobify', 43200 ); // Half a day
    }

    if ( count( $results ) > 0 )
    {
      foreach ( $results as $key => $obj ) {
        $jobs[] = array(
          'portal'       => 'github_jobs',
          'title'        => ( ! empty( $obj->title ) ) ? $obj->title : false,
          'company'      => ( ! empty( $obj->company ) ) ? $obj->company : false,
          'company_logo' => ( ! empty( $obj->company_logo ) ) ? $obj->company_logo : false,
          'company_url'  => ( ! empty( $obj->company_url ) ) ? $obj->company_url : false,
          //'city'     => ( ! empty( $ary['city'] ) ) ? $ary['city'] : false,
          //'state'    => ( ! empty( $ary['state'] ) ) ? $ary['state'] : false,
          //'country'  => ( ! empty( $ary['country'] ) ) ? $ary['country'] : false,
          'desc'     => ( ! empty( $obj->description ) ) ? $obj->description : false,
          'app_url'  => ( ! empty( $obj->url ) ) ? $obj->url : false,
          //'lat'      => ( ! empty( $ary['latitude'] ) ) ? $ary['latitude'] : false,
          //'long'     => ( ! empty( $ary['longitude'] ) ) ? $ary['longitude'] : false,
          'date'    => ( ! empty( $obj->created_at ) ) ? $obj->created_at : false,
          'location' => ( ! empty( $obj->location ) ) ? $obj->location : false,
          'custom'   => array(
            'how_to_apply' => ( ! empty( $obj->how_to_apply ) ) ? $obj->how_to_apply : false,
          ),
          //'address'  => ( ! empty( $ary['address'] ) ) ? $ary['address'] : false,
          //'phone'  => ( ! empty( $ary['phone'] ) ) ? $ary['phone'] : false,
          //'email'  => ( ! empty( $ary['email'] ) ) ? $ary['email'] : false,
          'type'  => ( ! empty( $obj->type ) ) ? $obj->type : false,
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