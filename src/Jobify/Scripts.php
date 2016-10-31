<?php
class Jobify_Scripts {
  public function run() {
    add_action( 'wp_enqueue_scripts', function()
    {
      wp_register_script( 'jobify-indeed', 'https://gdc.indeed.com/ads/apiresults.js', array(), false, false );
      wp_register_script( 'jobify-geolocation', plugin_dir_url( JOBIFY_PLUGIN ) . 'js' . DIRECTORY_SEPARATOR . 'geolocation.js', array( 'jquery' ), '1.1.0', false );
      wp_register_script( 'jobify-tracker', plugin_dir_url( JOBIFY_PLUGIN ) . 'js' . DIRECTORY_SEPARATOR . 'tracker.js', array( 'jquery' ), '1.3.3', false );

      wp_localize_script( 'jobify-geolocation', 'Jobify', array(
        'ajaxurl'  => admin_url( 'admin-ajax.php' ),
        'security' => wp_create_nonce( 'jobify' )
      ));
    });

    add_action( 'wp_ajax_jobify_get_jobs', function() {
      check_ajax_referer( 'jobify', 'security' );

      // Get the jobs
      $jobArgs = array(
        'keyword'                 => ( ! empty( $_POST['params']['keyword'] ) ) ? $_POST['params']['keyword'] : false,
        'geolocation'             => ( ! empty( $_POST['params']['geolocation'] ) ) ? $_POST['params']['geolocation'] : false,
        'powered_by'              => ( ! empty( $_POST['params']['powered_by'] ) ) ? $_POST['params']['powered_by'] : true,
        'portals'                 => ( ! empty( $_POST['params']['portals'] ) ) ?  $_POST['params']['portals'] : array(),

        'careerjet_locale'        => ( ! empty( $_POST['params']['careerjet_locale'] ) ) ?  $_POST['params']['careerjet_locale'] : 'en_US',
        'githubjobs_fulltime'     => ( ! empty( $_POST['params']['githubjobs_fulltime'] ) ) ?  $_POST['params']['githubjobs_fulltime'] : false,
        'indeed_radius'           => ( ! empty( $_POST['params']['indeed_radius'] ) ) ?  $_POST['params']['indeed_radius'] : 25,
        'indeed_fromage'          => ( ! empty( $_POST['params']['indeed_fromage'] ) ) ?  $_POST['params']['indeed_fromage'] : 30,
        'indeed_limit'            => ( ! empty( $_POST['params']['indeed_limit'] ) ) ?  $_POST['params']['indeed_limit'] : 10,
        'usajobs_exclude_keyword' => ( ! empty( $_POST['params']['usajobs_exclude_keyword'] ) ) ?  $_POST['params']['usajobs_exclude_keyword'] : '',
        'usajobs_limit'           => ( ! empty( $_POST['params']['usajobs_limit'] ) ) ?  $_POST['params']['usajobs_limit'] : '',
      );

      // Location
      if ( ! empty( $_POST['params']['lat'] ) && ! empty( $_POST['params']['lng'] ) ) {
        $jobArgs['lat'] = $_POST['params']['lat'];
        $jobArgs['lng'] = $_POST['params']['lng'];
      } elseif ( ! empty( $_POST['params']['location'] ) ) {
        $jobArgs['location'] = $args['location'];
      }

      $jobs = jobify_get_jobs( $jobArgs );

      if ( count( $jobs ) > 0 )
      {
        shuffle( $jobs );
      }

      echo json_encode( $jobs );

      die();
    } );
  }
}