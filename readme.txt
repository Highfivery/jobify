=== Jobify ===
Contributors: bmarshall511
Donate link: https://www.gittip.com/bmarshall511/
Tags: jobs, widgets, github jobs,job postings
Requires at least: 4.4.1
Tested up to: 4.4.1
Stable tag: 1.1.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Jobify allows easy site integration of job postings from all of the major sites like GitHub Jobs.

== Description ==

Jobify allows easy site integration of job postings from all of the major sites like GitHub Jobs. In addition to a highly configurable widget, Jobify opens up an API for developers to easily pull jobs into their themes, plugins and applications.

Supports the following job listing sites:

* <a href="https://jobs.github.com/" target="_blank">GitHub Jobs</a>
* <a href="http://www.indeed.com/" target="_blank">Indeed</a>

Works with <a href="https://codex.wordpress.org/Class_Reference/WP_Object_Cache#Persistent_Caching" target="_blank">persistent cache plugins</a> like <a href="http://wordpress.org/extend/plugins/w3-total-cache/" target="_blank">W3 Total Cache</a>.

**Languages:** English

If you have suggestions for a new add-on, feel free to email me at me@benmarshall.me. Want regular updates? <a href="https://twitter.com/bmarshall0511">Follow me on Twitter</a> or <a href="http://www.benmarshall.me" target="_blank">visit my blog</a>.

== Installation ==

= Plugin Installation =

1. Upload the `jobify` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==

= Can I add additional job listing sites? =

Yes! In your theme or plugin, use the following helper function to add new job APIs:

    <?php
    jobify_addAPI( array(
      'title'   => __( 'Job API Title', 'textdomain' ),     // Enter the job API name
      'logo'    => 'http://myimage.com/image.jpg',          // Image URL to logo (153x50px)
      'name'    => 'job_api_key',                           // Unique API key name
      'default' => '1',                                     // Enabled/disabled by default
      'getJobs' => function( $options ) {                   // Function to return jobs from the API
        $jobs = array();

        $jobs[] = array(
          'title'    => '',
          'company'  => '',
          'city'     => '',
          'state'    => '',
          'country'  => '',
          'desc'     => '',
          'url'      => '',
          'location' => ''
        );

        return $jobs;
      },
      'options' => array(                                   // API options
        array(
          'title'   => __( 'Option', 'textdomain' ),
          'name'    => 'unique_option_key',
          'desc'    => __( 'Enter a description here.', 'textdomain' ),
          'default' => 'I love Jobify!'
        ),
        array(                                              // Split fields with a 'group' array
          'group' => array(
            array(                                          // Number field
              'title'   => __( 'Option 2', 'jobify' ),
              'name'    => 'unique_option_2_key',
              'desc'    => __( 'Enter a description here.', 'textdomain' ),
              'default' => 10
              'type'    => 'number'
            ),
            array(                                          // Select field
              'title'   => __( 'Option 3', 'jobify' ),
              'name'    => 'unique_option32_key',
              'desc'    => __( 'Enter a description here.', 'textdomain' ),
              'default' => 'option1'
              'type'    => 'select',
              'options' => array(
                'option1' => __( 'Option 1', 'textdomain' ),
                'option2'      => __( 'Option 2', 'textdomain' )
              )
            ),
          ),
          'desc' => __( 'Group description', 'textdomain' ) // Group description
        ),
        array(                                              // Checkbox field
          'title'   => __( 'Option 4', 'textdomain' ),
          'name'    => 'unique_option_4_key',
          'desc'    => __( 'Enter a description here.', 'textdomain' ),
          'default' => 10
          'type'    => 'checkbox',
          'options' => array(
            10 => __( 'Yes', 'jobify' )
          ),
          'default' => ''
        ),
      )
    ));

== Screenshots ==

== Changelog ==

= 1.1.0 =
* Added <code>jobify_addAPI</code> function to allow themes and plugins to add additional job APIs.
* Added support for Indeed.
* Updated readme file.

= 1.0.0 =
* Initial release.

== Contributors ==
* [Ben Marshall](https://github.com/bmarshall511)

