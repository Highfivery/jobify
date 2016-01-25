<?php
class Jobify_JobPostType extends Jobify_Plugin {
  public function run() {
    add_action( 'init', function(){
      $labels = array(
        'name'                  => _x( 'Jobs', 'Post Type General Name', 'jobify' ),
        'singular_name'         => _x( 'Job', 'Post Type Singular Name', 'jobify' ),
        'menu_name'             => __( 'Jobs', 'jobify' ),
        'name_admin_bar'        => __( 'Jobs', 'jobify' ),
        'archives'              => __( 'Job Archives', 'jobify' ),
        'parent_item_colon'     => __( 'Parent Job:', 'jobify' ),
        'all_items'             => __( 'All Jobs', 'jobify' ),
        'add_new_item'          => __( 'Add New Job', 'jobify' ),
        'add_new'               => __( 'Add New', 'jobify' ),
        'new_item'              => __( 'New Job', 'jobify' ),
        'edit_item'             => __( 'Edit Job', 'jobify' ),
        'update_item'           => __( 'Update Job', 'jobify' ),
        'view_item'             => __( 'View Job', 'jobify' ),
        'search_items'          => __( 'Search Job', 'jobify' ),
        'not_found'             => __( 'Not found', 'jobify' ),
        'not_found_in_trash'    => __( 'Not found in Trash', 'jobify' ),
        'featured_image'        => __( 'Featured Image', 'jobify' ),
        'set_featured_image'    => __( 'Set featured image', 'jobify' ),
        'remove_featured_image' => __( 'Remove featured image', 'jobify' ),
        'use_featured_image'    => __( 'Use as featured image', 'jobify' ),
        'insert_into_item'      => __( 'Insert into job', 'jobify' ),
        'uploaded_to_this_item' => __( 'Uploaded to this job', 'jobify' ),
        'items_list'            => __( 'Jobs list', 'jobify' ),
        'items_list_navigation' => __( 'Jobs list navigation', 'jobify' ),
        'filter_items_list'     => __( 'Filter jobs list', 'jobify' ),
      );
      $args = array(
        'label'                 => __( 'Job', 'jobify' ),
        'description'           => __( 'Create job postings.', 'jobify' ),
        'labels'                => $labels,
        'supports'              => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'comments', 'trackbacks', 'revisions', 'custom-fields', ),
        'hierarchical'          => false,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 5,
        'menu_icon'             => 'dashicons-megaphone',
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => true,
        'exclude_from_search'   => false,
        'publicly_queryable'    => true,
        'capability_type'       => 'page',
        'rewrite'               => array( 'slug' => $this->settings['job_post_slug'] ),
      );
      register_post_type( 'jobify_posting', $args );
    });

    add_action( 'tgmpa_register', function()
    {
      /*
       * Array of plugin arrays. Required keys are name and slug.
       * If the source is NOT from the .org repo, then source is also required.
       */
      $plugins = array(
        // This is an example of how to include a plugin from the WordPress Plugin Repository.
        array(
          'name'      => 'Advanced Custom Fields',
          'slug'      => 'advanced-custom-fields',
          'required'  => true,
        ),
      );

      /*
       * Array of configuration settings. Amend each line as needed.
       *
       * TGMPA will start providing localized text strings soon. If you already have translations of our standard
       * strings available, please help us make TGMPA even better by giving us access to these translations or by
       * sending in a pull-request with .po file(s) with the translations.
       *
       * Only uncomment the strings in the config array if you want to customize the strings.
       */
      $config = array(
        'id'           => 'tgmpa',                 // Unique ID for hashing notices for multiple instances of TGMPA.
        'default_path' => '',                      // Default absolute path to bundled plugins.
        'menu'         => 'tgmpa-install-plugins', // Menu slug.
        'parent_slug'  => 'themes.php',            // Parent menu slug.
        'capability'   => 'edit_theme_options',    // Capability needed to view plugin install page, should be a capability associated with the parent menu used.
        'has_notices'  => true,                    // Show admin notices or not.
        'dismissable'  => true,                    // If false, a user cannot dismiss the nag message.
        'dismiss_msg'  => '',                      // If 'dismissable' is false, this message will be output at top of nag.
        'is_automatic' => false,                   // Automatically activate plugins after installation or not.
        'message'      => '',                      // Message to output right before the plugins table.

        /*
        'strings'      => array(
          'page_title'                      => __( 'Install Required Plugins', 'theme-slug' ),
          'menu_title'                      => __( 'Install Plugins', 'theme-slug' ),
          'installing'                      => __( 'Installing Plugin: %s', 'theme-slug' ), // %s = plugin name.
          'oops'                            => __( 'Something went wrong with the plugin API.', 'theme-slug' ),
          'notice_can_install_required'     => _n_noop(
            'This theme requires the following plugin: %1$s.',
            'This theme requires the following plugins: %1$s.',
            'theme-slug'
          ), // %1$s = plugin name(s).
          'notice_can_install_recommended'  => _n_noop(
            'This theme recommends the following plugin: %1$s.',
            'This theme recommends the following plugins: %1$s.',
            'theme-slug'
          ), // %1$s = plugin name(s).
          'notice_cannot_install'           => _n_noop(
            'Sorry, but you do not have the correct permissions to install the %1$s plugin.',
            'Sorry, but you do not have the correct permissions to install the %1$s plugins.',
            'theme-slug'
          ), // %1$s = plugin name(s).
          'notice_ask_to_update'            => _n_noop(
            'The following plugin needs to be updated to its latest version to ensure maximum compatibility with this theme: %1$s.',
            'The following plugins need to be updated to their latest version to ensure maximum compatibility with this theme: %1$s.',
            'theme-slug'
          ), // %1$s = plugin name(s).
          'notice_ask_to_update_maybe'      => _n_noop(
            'There is an update available for: %1$s.',
            'There are updates available for the following plugins: %1$s.',
            'theme-slug'
          ), // %1$s = plugin name(s).
          'notice_cannot_update'            => _n_noop(
            'Sorry, but you do not have the correct permissions to update the %1$s plugin.',
            'Sorry, but you do not have the correct permissions to update the %1$s plugins.',
            'theme-slug'
          ), // %1$s = plugin name(s).
          'notice_can_activate_required'    => _n_noop(
            'The following required plugin is currently inactive: %1$s.',
            'The following required plugins are currently inactive: %1$s.',
            'theme-slug'
          ), // %1$s = plugin name(s).
          'notice_can_activate_recommended' => _n_noop(
            'The following recommended plugin is currently inactive: %1$s.',
            'The following recommended plugins are currently inactive: %1$s.',
            'theme-slug'
          ), // %1$s = plugin name(s).
          'notice_cannot_activate'          => _n_noop(
            'Sorry, but you do not have the correct permissions to activate the %1$s plugin.',
            'Sorry, but you do not have the correct permissions to activate the %1$s plugins.',
            'theme-slug'
          ), // %1$s = plugin name(s).
          'install_link'                    => _n_noop(
            'Begin installing plugin',
            'Begin installing plugins',
            'theme-slug'
          ),
          'update_link'             => _n_noop(
            'Begin updating plugin',
            'Begin updating plugins',
            'theme-slug'
          ),
          'activate_link'                   => _n_noop(
            'Begin activating plugin',
            'Begin activating plugins',
            'theme-slug'
          ),
          'return'                          => __( 'Return to Required Plugins Installer', 'theme-slug' ),
          'plugin_activated'                => __( 'Plugin activated successfully.', 'theme-slug' ),
          'activated_successfully'          => __( 'The following plugin was activated successfully:', 'theme-slug' ),
          'plugin_already_active'           => __( 'No action taken. Plugin %1$s was already active.', 'theme-slug' ),  // %1$s = plugin name(s).
          'plugin_needs_higher_version'     => __( 'Plugin not activated. A higher version of %s is needed for this theme. Please update the plugin.', 'theme-slug' ),  // %1$s = plugin name(s).
          'complete'                        => __( 'All plugins installed and activated successfully. %1$s', 'theme-slug' ), // %s = dashboard link.
          'contact_admin'                   => __( 'Please contact the administrator of this site for help.', 'tgmpa' ),

          'nag_type'                        => 'updated', // Determines admin notice type - can only be 'updated', 'update-nag' or 'error'.
        ),
        */
      );

      tgmpa( $plugins, $config );
    } );

    add_action( 'widgets_init', function()
    {
      register_sidebar( array(
        'name' => __( 'Jobify Sidebar', 'jobify' ),
        'id' => 'sidebar-jobify',
        'description' => __( 'Widgets in this area will be shown on all job posts.', 'jobify' ),
        'before_widget' => '<li id="%1$s" class="widget %2$s">',
        'after_widget'  => '</li>',
        'before_title'  => '<h2 class="widgettitle">',
        'after_title'   => '</h2>',
      ) );
    });
  }
}