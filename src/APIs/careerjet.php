<?php
/**
 * Include the Careerjet's PHP API
 *
 * @since 1.4.0
 */
require_once JOBIFY_ROOT . 'src' . DIRECTORY_SEPARATOR . 'Careerjet_API.php';

jobify_addAPI( array(
  'key'          => 'careerjet',
  'title'        => __( 'Careerjet', 'jobify' ),
  'logo'         => plugins_url( 'img/careerjet.jpg' , JOBIFY_PLUGIN ),
  'requirements' => array(),
  'getJobs'      => function( $args )
  {
    // Create the returned jobs array
    $jobs = array();

    // Get Jobify settings
    $settings = jobify_settings();

    // Set the Careerjet affiliate ID
    $careerjet_api_key = ( ! empty ( $settings['careerjet_api_key'] ) ) ? $settings['careerjet_api_key'] : 'b4a44bbbcaa7fe6bfd6039d1e864294e';

    // Set the Careerjet locale
    $careerjet_locale = ( ! empty ( $args['careerjet_locale'] ) ) ? $args['careerjet_locale'] : 'en_US';

    // Check cache for results
    $results = wp_cache_get( 'jobs-careerjet-' . jobify_string( $args ), 'jobify' );
    if ( false === $results )
    {
      // Query the Careerjet PHP API
      $careerjet = new Careerjet_API( $careerjet_locale );

      $params = array(
        'page'  => 1,
        'affid' => $careerjet_api_key
      );

      $params['keywords']       = ( ! empty( $args['keyword'] ) ) ? $args['keyword'] : '';
      $params['location']       = ( ! empty( $args['location'] ) ) ? $args['location'] : false;

      $results = $careerjet->search( $params );
      if ( ! $results->type == 'JOBS' )
      {
        // API error
        $jobs[] = array(
          'error'  => __( '<b>Careerjet API Error:</b> ', 'jobify' ) . ' Invalid result type: ' . $results->type
        );
      }
      else
      {
        // Save results to cache
        wp_cache_set( 'jobs-careerjet-' . jobify_string( $args ), $results, 'jobify', 43200 ); // Half a day
        if ( ! empty( $results->jobs ) && count( $results->jobs ) > 0 ) {
          foreach ( $results->jobs as $key => $obj ) {
            // Add job to array
            $jobs[] = array(
              'portal'   => 'careerjet',
              'title'    => ( ! empty( $obj->title ) ) ? $obj->title : false,
              'company'  => ( ! empty( $obj->company ) ) ? $obj->company : false,
              //'company_logo' => ( ! empty( $obj->company_logo ) ) ? $obj->company_logo : false,
              //'company_url'   => ( ! empty( $obj->company_url ) ) ? $obj->company_url : false,
              //'city'     => ( ! empty( $ary['city'] ) ) ? $ary['city'] : false,
              //'state'    => ( ! empty( $ary['state'] ) ) ? $ary['state'] : false,
              //'country'  => ( ! empty( $ary['country'] ) ) ? $ary['country'] : false,
              'desc'     => ( ! empty( $obj->description ) ) ? $obj->description : false,
              'app_url'  => ( ! empty( $obj->url ) ) ? $obj->url : false,
              //'lat'      => ( ! empty( $ary['latitude'] ) ) ? $ary['latitude'] : false,
              //'long'     => ( ! empty( $ary['longitude'] ) ) ? $ary['longitude'] : false,
              'date'     => ( ! empty( $obj->date ) ) ? $obj->date : false,
              'location' => ( ! empty( $obj->locations ) ) ? $obj->locations : false,
              'custom'   => array(
                //'onmousedown'           => ( ! empty( $ary['onmousedown'] ) ) ? $ary['onmousedown'] : false,
                //'source'                => ( ! empty( $ary['source'] ) ) ? $ary['source'] : false,
                //'sponsored'             => ( ! empty( $ary['sponsored'] ) ) ? $ary['sponsored'] : false,
                //'expired'               => ( ! empty( $ary['expired'] ) ) ? $ary['expired'] : false,
                //'indeedApply'           => ( ! empty( $ary['indeedApply'] ) ) ? $ary['indeedApply'] : false,
                //'formattedRelativeTime' => ( ! empty( $ary['formattedRelativeTime'] ) ) ? $ary['formattedRelativeTime'] : false,
                //'noUniqueUrl'           => ( ! empty( $ary['noUniqueUrl'] ) ) ? $ary['noUniqueUrl'] : false,
              )
              //'address'  => ( ! empty( $ary['address'] ) ) ? $ary['address'] : false,
              //'phone'  => ( ! empty( $ary['phone'] ) ) ? $ary['phone'] : false,
              //'email'  => ( ! empty( $ary['email'] ) ) ? $ary['email'] : false,
              //'type'  => ( ! empty( $ary['type'] ) ) ? $ary['type'] : false,
            );
          }
        }
      }
    }

    return $jobs;
  },
  'options' => array(
    array(
      'title'   => __( 'Careerjet Locale', 'jobify' ),
      'name'    => 'careerjet_locale',
      'desc'    => __( 'Select your Careerjet locale.', 'jobify' ),
      'default' => 'en_US',
      'type'    => 'select',
      'options' => array(
        'cs_CZ' => __( 'Czech Republic', 'jobify' ),
        'da_DK' => __( 'Denmark', 'jobify' ),
        'de_AT' => __( 'Austria', 'jobify' ),
        'de_CH' => __( 'Switzerland (DE)', 'jobify' ),
        'de_DE' => __( 'Germany', 'jobify' ),
        'en_AE' => __( 'United Arab Emirates', 'jobify' ),
        'en_AU' => __( 'Australia', 'jobify' ),
        'en_CA' => __( 'Canada (EN)', 'jobify' ),
        'en_CN' => __( 'China (EN)', 'jobify' ),
        'en_HK' => __( 'Hong Kong', 'jobify' ),
        'en_IE' => __( 'Ireland', 'jobify' ),
        'en_IN' => __( 'India', 'jobify' ),
        'en_MY' => __( 'Malaysia', 'jobify' ),
        'en_NZ' => __( 'New Zealand', 'jobify' ),
        'en_OM' => __( 'Oman', 'jobify' ),
        'en_PH' => __( 'Philippines', 'jobify' ),
        'en_PK' => __( 'Pakistan', 'jobify' ),
        'en_QA' => __( 'Qatar', 'jobify' ),
        'en_SG' => __( 'Singapore', 'jobify' ),
        'en_GB' => __( 'United Kingdom', 'jobify' ),
        'en_US' => __( 'United States', 'jobify' ),
        'en_ZA' => __( 'South Africa', 'jobify' ),
        'en_TW' => __( 'Taiwan', 'jobify' ),
        'en_VN' => __( 'Vietnam (EN)', 'jobify' ),
        'es_AR' => __( 'Argentina', 'jobify' ),
        'es_BO' => __( 'Bolivia', 'jobify' ),
        'es_CL' => __( 'Chile', 'jobify' ),
        'es_CR' => __( 'Costa Rica', 'jobify' ),
        'es_DO' => __( 'Dominican Republic', 'jobify' ),
        'es_EC' => __( 'Ecuador', 'jobify' ),
        'es_ES' => __( 'Spain', 'jobify' ),
        'es_GT' => __( 'Guatemala', 'jobify' ),
        'es_MX' => __( 'Mexico', 'jobify' ),
        'es_PA' => __( 'Panama', 'jobify' ),
        'es_PE' => __( 'Peru', 'jobify' ),
        'es_PR' => __( 'Puerto Rico', 'jobify' ),
        'es_PY' => __( 'Paraguay', 'jobify' ),
        'es_UY' => __( 'Uruguay', 'jobify' ),
        'es_VE' => __( 'Venezuela', 'jobify' ),
        'fi_FI' => __( 'Finland', 'jobify' ),
        'fr_CA' => __( 'Canada (FR)', 'jobify' ),
        'fr_BE' => __( 'Belgium (FR)', 'jobify' ),
        'fr_CH' => __( 'Switzerland (FR)', 'jobify' ),
        'fr_FR' => __( 'France', 'jobify' ),
        'fr_LU' => __( 'Luxembourg', 'jobify' ),
        'fr_MA' => __( 'Morocco', 'jobify' ),
        'hu_HU' => __( 'Hungary', 'jobify' ),
        'it_IT' => __( 'Italy', 'jobify' ),
        'ja_JP' => __( 'Japan', 'jobify' ),
        'ko_KR' => __( 'Korea', 'jobify' ),
        'nl_BE' => __( 'Belgium (NL)', 'jobify' ),
        'nl_NL' => __( 'Netherlands', 'jobify' ),
        'no_NO' => __( 'Norway', 'jobify' ),
        'pl_PL' => __( 'Poland', 'jobify' ),
        'pt_PT' => __( 'Portugal', 'jobify' ),
        'pt_BR' => __( 'Brazil', 'jobify' ),
        'ru_RU' => __( 'Russia', 'jobify' ),
        'ru_UA' => __( 'Ukraine (RU)', 'jobify' ),
        'sv_SE' => __( 'Sweden', 'jobify' ),
        'sk_SK' => __( 'Slovakia', 'jobify' ),
        'tr_TR' => __( 'Turkey', 'jobify' ),
        'uk_UA' => __( 'Ukraine (UK)', 'jobify' ),
        'vi_VN' => __( 'Vietnam (VI)', 'jobify' ),
        'zh_CN' => __( 'China (ZH)', 'jobify' )
      ),
    ),
  )
));