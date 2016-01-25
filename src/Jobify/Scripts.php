<?php
class Jobify_Scripts {
  public function run() {
    add_action( 'wp_enqueue_scripts', function()
    {
      wp_register_script( 'jobify-indeed', 'https://gdc.indeed.com/ads/apiresults.js', array(), false, false );
      wp_register_script( 'jobify-ajax', plugin_dir_url( JOBIFY_PLUGIN ) . 'js' . DIRECTORY_SEPARATOR . 'ajax.js', array( 'jquery' ), '1.1.0', false );

      wp_localize_script( 'jobify-ajax', 'Jobify', array(
        'ajaxurl'  => admin_url( 'admin-ajax.php' ),
        'security' => wp_create_nonce( 'jobify' )
      ));
    });

    add_action( 'wp_ajax_jobify_get_jobs', function() {
      check_ajax_referer( 'jobify', 'security' );

      $param             = $_POST['params'];
      $location          = jobify_get_location( $param['lat'] . ',' .  $param['lng'] );
      $param['location'] = $location[3];

      $jobs = jobify_get_jobs( $param );

      echo json_encode( $jobs );

      die();
    } );
  }
}