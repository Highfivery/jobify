<?php
class Jobify_Widgets {
  public function run() {
    add_action( 'widgets_init', function(){
      register_widget( 'JobsWidget\JobsWidget' );
    });

    add_action('admin_enqueue_scripts', function( $hook )
      {
        if ( $hook != 'widgets.php' )
          return;

        wp_enqueue_style( 'jobify', plugins_url( 'css/widgets.css' , JOBIFY_PLUGIN ) );
      });
  }
}