<?php
/**
 * Security Note: Blocks direct access to the plugin PHP files.
 */
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

function jobify_settings() {
  /*if ( is_plugin_active_for_network( plugin_basename( JOBIFY_PLUGIN ) ) ) {
    // Network plugin settings.
    return (array) get_site_option( 'jobify_general_settings' );
  }

  // Site plugin settings.
  return (array) get_option( 'jobify_general_settings' );*/
}


function jobify_get_url() {
  $pageURL = 'http';

  if ( isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] == 'on' ) {
    $pageURL .= 's';
  }

  $pageURL .= '://';

  if ( '80' != $_SERVER['SERVER_PORT'] ) {
    $pageURL .= $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] . $_SERVER['REQUEST_URI'];
  } else {
    $pageURL .= $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
  }

  return $pageURL;
}

function jobify_admin_url() {
  if ( is_plugin_active_for_network( plugin_basename( JOBIFY_PLUGIN ) ) ) {
    $settings_url = network_admin_url( 'settings.php' );
  } else if ( home_url() != site_url() ) {
    $settings_url = home_url( '/wp-admin/' . 'options-general.php' );
  } else {
    $settings_url = admin_url( 'options-general.php' );
  }

  return $settings_url;
}