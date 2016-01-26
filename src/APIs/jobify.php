<?php
jobify_addAPI( array(
  'key'     => 'jobify',
  'title'   => __( 'Jobify', 'jobify' ),
  'logo'    => plugins_url( 'img/jobify.jpg' , JOBIFY_PLUGIN ),
  'getJobs' => function( $options ) {
    $settings = jobify_settings();
    $jobs     = array();

    $results = wp_cache_get( 'jobs-jobify', 'jobify' );
    if ( false === $results )
    {
      $args = array(
        'post_type' => 'jobify_posting'
      );
      $query = new WP_Query( $args );
      if ( $query->have_posts() )
      {
        $query->the_post();
        $city    = get_field( 'jobify_city' );
        $state   = get_field( 'jobify_state' );
        $zip     = get_field( 'jobify_zip' );
        $country = get_field( 'jobify_country' );

        $jobs[] = array(
          'title'    => get_the_title(),
          'company'  => get_field( 'jobify_company' ),
          'city'     => $city,
          'state'    => $state,
          'zip'      => $zip,
          'country'  => $country,
          'desc'     => get_the_excerpt(),
          'url'      => get_field( 'jobify_app_url' ),
          'location' => $city . ', ' . $state . ' ' . $zip
        );
      }
      wp_reset_postdata();

      wp_cache_set( 'jobs-jobify', $results, 'jobify', 43200 ); // Half a day
    }


    return $jobs;
  },
  'options' => array(
  )
));