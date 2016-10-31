<?php
class Jobify_Admin extends Jobify_Plugin {
  public $tabs = array();

  public function run() {
    // Merge and update new changes
    if ( isset( $_POST['jobify_settings'] ) ) {
      $saved_settings = array();
      foreach ( $this->default_settings as $key => $val ) {
        if ( isset( $_POST['jobify_settings'][$key] ) ) {
          $saved_settings[$key] = $_POST['jobify_settings'][$key];
        } else {
          $saved_settings[$key] = 0;
        }
      }

      if ( is_plugin_active_for_network( plugin_basename( JOBIFY_PLUGIN ) ) ) {
        update_site_option( 'jobify_settings', $saved_settings );
      } else {
        update_option( 'jobify_settings', $saved_settings );
      }

      $this->load_settings();
    }

    $this->tabs['jobify_settings'] = __( 'Settings', 'jobify' );
    //$this->tabs['jobify_style'] = __( 'Style', 'jobify' );

    if ( is_plugin_active_for_network( plugin_basename( JOBIFY_PLUGIN ) ) ) {
      add_action( 'network_admin_menu', array( $this, 'admin_menu' ) );
      add_action( 'network_admin_edit_jobify', array( $this, 'update_network_setting' ) );
    }

    add_action( 'admin_init', array( $this, 'admin_init' ) );
    add_action( 'admin_menu', array( $this, 'admin_menu' ) );

    add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
  }

  /**
   * Add admin scripts.
   *
   * Adds the CSS & JS for the settings page.
   *
   * @since 1.1.0
   *
   * @link http://codex.wordpress.org/Plugin_API/Action_Reference/admin_enqueue_scripts
   *
   * @param string $hook Used to target a specific admin page.
   * @return void
   */
  public function admin_enqueue_scripts( $hook ) {
    if ( 'settings_page_jobify' != $hook ) {
          return;
    }
  }

  /**
   * Uses admin_init.
   *
   * Triggered before any other hook when a user accesses the admin area.
   *
   * @since 1.1.0
   *
   * @link http://codex.wordpress.org/Plugin_API/Action_Reference/admin_init
   */
  public function admin_init()
  {
    register_setting( 'jobify_settings', 'jobify_settings' );

    add_settings_section( 'section_general', __( 'General Settings', 'jobify' ), false, 'jobify_settings' );
    add_settings_section( 'section_indeed', __( 'Indeed Settings', 'jobify' ), false, 'jobify_settings' );
    add_settings_section( 'section_usajobs', __( 'USAJOBS Settings', 'jobify' ), false, 'jobify_settings' );
    add_settings_section( 'section_careerjet', __( 'Careerjet Settings', 'jobify' ), false, 'jobify_settings' );

    add_settings_field( 'job_post_type', __( 'Disable Job post type', 'jobify' ), array( $this, 'field_job_post_type' ), 'jobify_settings', 'section_general' );

    if ( ! $this->settings['job_post_type'] )
    {
      add_settings_field( 'job_post_slug', __( 'Job post type slug', 'jobify' ), array( $this, 'field_job_post_slug' ), 'jobify_settings', 'section_general' );
    }

    add_settings_field( 'indeed_publisher_number', __( 'Publisher number', 'jobify' ), array( $this, 'field_indeed_publisher_number' ), 'jobify_settings', 'section_indeed' );

    add_settings_field( 'usajobs_api_key', __( 'API key', 'jobify' ), array( $this, 'field_usajobs_api_key' ), 'jobify_settings', 'section_usajobs' );
    add_settings_field( 'usajobs_email', __( 'Email address', 'jobify' ), array( $this, 'field_usajobs_email' ), 'jobify_settings', 'section_usajobs' );

    add_settings_field( 'careerjet_api_key', __( 'API Key', 'jobify' ), array( $this, 'field_careerjet_api_key' ), 'jobify_settings', 'section_careerjet' );
  }

  public function field_job_post_type()
  {
    ?>
    <label>
      <input type="checkbox" name="jobify_settings[job_post_type]" value="1"<?php if ( 1 == $this->settings['job_post_type'] ): ?>checked="checked"<?php endif; ?>>
      <p class="description"><?php _e( 'Disables the creation of job postings on the site.' ); ?></p>
    </label>
    <?php
  }

  public function field_job_post_slug()
  {
    ?>
    <label>
      <input type="text" class="regular-text" name="jobify_settings[job_post_slug]" value="<?php echo esc_attr( $this->settings['job_post_slug'] ); ?>">
      <p class="description"><?php printf( __( 'Enter the job post type slug. After updating, re-save the <a href="%s">site permalink settings</a>.', 'jobify' ), admin_url( 'options-permalink.php' ) ); ?></p>
    </label>
    <?php
  }

  public function field_indeed_publisher_number() {
    ?>
    <label>
      <input type="text" class="regular-text" name="jobify_settings[indeed_publisher_number]" value="<?php echo esc_attr( $this->settings['indeed_publisher_number'] ); ?>">
      <p class="description"><?php printf( __( 'If you do not have a publisher number, you can receive one by heading to the <a href="%s" target="_blank">Indeed Publisher Portal</a>.', 'jobify' ), 'https://ads.indeed.com/jobroll/xmlfeed' ); ?></p>
    </label>
    <?php
  }

  public function field_careerjet_api_key() {
    ?>
    <label>
      <input type="text" class="regular-text" name="jobify_settings[careerjet_api_key]" value="<?php echo esc_attr( $this->settings['careerjet_api_key'] ); ?>">
      <p class="description"><?php printf( __( 'If you do not have an API key, you can receive one by heading to the <a href="%s" target="_blank">Careerjet Partners</a>.', 'jobify' ), 'http://www.careerjet.com/partners/?ak=b4a44bbbcaa7fe6bfd6039d1e864294e' ); ?></p>
    </label>
    <?php
  }

  public function field_usajobs_api_key() {
    ?>
    <label>
      <input type="text" class="regular-text" name="jobify_settings[usajobs_api_key]" value="<?php echo esc_attr( $this->settings['usajobs_api_key'] ); ?>">
      <p class="description"><?php printf( __( 'If you do not have a API key, you can receive one by heading to the <a href="%s" target="_blank">USAJOBS Developer Site</a>.', 'jobify' ), 'https://developer.usajobs.gov/' ); ?></p>
    </label>
    <?php
  }

  public function field_usajobs_email() {
    ?>
    <label>
      <input type="email" class="regular-text" name="jobify_settings[usajobs_email]" value="<?php echo esc_attr( $this->settings['usajobs_email'] ); ?>">
      <p class="description"><?php _e( 'Enter the email address registered when creating the USAJOBS API key.', 'jobify' ); ?></p>
    </label>
    <?php
  }

  /**
   * Update network settings.
   *
   * Used when plugin is network activated to save settings.
   *
   * @since 1.1.0
   *
   * @link http://wordpress.stackexchange.com/questions/64968/settings-api-in-multisite-missing-update-message
   * @link http://benohead.com/wordpress-network-wide-plugin-settings/
   */
  public function update_network_setting() {
    update_site_option( 'jobify_settings', $_POST['jobify_settings'] );
    wp_redirect( add_query_arg(
      array(
        'page'    => 'jobify',
        'updated' => 'true',
        ),
      network_admin_url( 'settings.php' )
    ) );
    exit;
  }

  /**
   * Uses admin_menu.
   *
   * Used to add extra submenus and menu options to the admin panel's menu
   * structure.
   *
   * @since 1.1.0
   *
   * @link http://codex.wordpress.org/Plugin_API/Action_Reference/admin_menu
   *
   * @return void
   */
  public function admin_menu() {

    if ( is_plugin_active_for_network( plugin_basename( JOBIFY_PLUGIN ) ) ) {
      $hook_suffix = add_submenu_page(
        'settings.php',
        __( 'Jobify Settings', 'jobify' ),
        __( 'Jobify', 'jobify' ),
        'manage_network',
        'jobify',
        array( $this, 'settings_page' )
      );
    } else {
      // Register plugin settings page.
      $hook_suffix = add_options_page(
        __( 'Jobify Settings', 'jobify' ),
        __( 'Jobify', 'jobify' ),
        'manage_options',
        'jobify',
        array( $this, 'settings_page' )
      );
    }

    // Load settings from the database.
    add_action( "load-{$hook_suffix}", array( $this, 'load_jobify_settings' ) );
  }

  /**
   * Admin Scripts
   *
   * Adds CSS and JS files to the admin pages.
   *
   * @since 1.1.0
   *
   * @return void | boolean
   */
  public function load_jobify_settings() {
    if ( 'options-general.php' !== $GLOBALS['pagenow'] ) {
      return false;
    }

    wp_enqueue_style( 'jobify-admin', plugins_url( 'css/style.css', JOBIFY_PLUGIN ) );
  }

  /**
   * Plugin options page.
   *
   * Rendering goes here, checks for active tab and replaces key with the related
   * settings key. Uses the _options_tabs method to render the tabs.
   *
   * @since 1.1.0
   */
  public function settings_page() {
    $plugin = get_plugin_data( JOBIFY_PLUGIN );
    $tab    = isset( $_GET['tab'] ) ? $_GET['tab'] : 'jobify_settings';
    $page   = isset( $_GET['p'] ) ? $_GET['p'] : 1;
    $action = is_plugin_active_for_network( plugin_basename( JOBIFY_PLUGIN ) ) ? 'edit.php?action=jobify' : 'options.php';
    ?>
    <div class="wrap">
      <h2><?php echo __( 'Jobify', 'jobify' ); ?></h2>
      <?php $this->option_tabs(); ?>
      <?php
      if ( $tab == 'jobify_style' ) {
        //require_once JOBIFY_ROOT . 'inc/settings.php';
      } else {
        require_once JOBIFY_ROOT . 'inc/settings.php';
      } ?>
    </div>
    <?php
  }

  /**
   * Renders setting tabs.
   *
   * Walks through the object's tabs array and prints them one by one.
   * Provides the heading for the settings_page method.
   *
   * @since 2.0.0
   */
  public function option_tabs() {
    $current_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : 'jobify_settings';
    echo '<h2 class="nav-tab-wrapper">';
    foreach ( $this->tabs as $key => $name ) {
      $active = $current_tab == $key ? 'nav-tab-active' : '';
      echo '<a class="nav-tab ' . $active . '" href="?page=jobify&tab=' . $key . '">' . $name . '</a>';
    }
    echo '</h2>';
  }
}