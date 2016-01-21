<?php
/**
 * Security Note: Blocks direct access to the plugin PHP files.
 */
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

function jobify_settings() {
  if ( is_plugin_active_for_network( plugin_basename( JOBIFY_PLUGIN ) ) ) {
    // Network plugin settings.
    return (array) get_site_option( 'jobify_settings' );
  }

  // Site plugin settings.
  return (array) get_option( 'jobify_settings' );
}

function jobify_addAPI( $args )
{
  global $jobifyAPIs;

  $jobifyAPIs[] = $args;
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

function jobify_get_ip() {
  $ipaddress = '';

  if ( getenv('HTTP_CLIENT_IP') ) {
    $ipaddress = getenv('HTTP_CLIENT_IP');
  } else if ( getenv('HTTP_X_FORWARDED_FOR') ) {
    $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
  } else if ( getenv('HTTP_X_FORWARDED') ) {
    $ipaddress = getenv('HTTP_X_FORWARDED');
  } else if ( getenv('HTTP_FORWARDED_FOR') ) {
    $ipaddress = getenv('HTTP_FORWARDED_FOR');
  } else if ( getenv('HTTP_FORWARDED') ) {
    $ipaddress = getenv('HTTP_FORWARDED');
  } else if ( getenv('REMOTE_ADDR') ) {
    $ipaddress = getenv('REMOTE_ADDR');
  } else {
    $ipaddress = 'UNKNOWN';
  }

  return $ipaddress;
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