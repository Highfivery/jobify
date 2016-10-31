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
        'keyword'     => ( ! empty( $_POST['params']['keyword'] ) ) ? $_POST['params']['keyword'] : false,
        'geolocation' => ( ! empty( $_POST['params']['geolocation'] ) ) ? $_POST['params']['geolocation'] : false,
        'powered_by'  => ( ! empty( $_POST['params']['powered_by'] ) ) ? $_POST['params']['powered_by'] : true,
        'portals'     => ( ! empty( $_POST['params']['portals'] ) ) ?  $_POST['params']['portals'] : array()
        // @TODO - Add custom API fields
      );

      $location = jobify_get_location( $_POST['params']['lat'] . ',' .  $_POST['params']['lng'] );
      if ( count( $location ) > 0 )
      {
        $jobArgs['location'] = $location[3];
      }

      $jobs = jobify_get_jobs( $jobArgs );

      echo json_encode( $jobs );

      die();
    } );
  }
}