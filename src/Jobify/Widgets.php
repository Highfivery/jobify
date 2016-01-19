<?php
class Jobify_Widgets {
  public function run() {
    add_action( 'widgets_init', function(){
      register_widget( 'JobsWidget\JobsWidget' );
    });
  }
}