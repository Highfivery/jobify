<?php
jobify_addAPI( array(
  'title'   => __( 'USAJOBS', 'jobify' ),
  'logo'    => plugins_url( 'img/usajobs.jpg' , JOBIFY_PLUGIN ),
  'name'    => 'usajobs',
  'getJobs' => function( $options ) {
    $jobs = array();

    if ( ! empty( $options['usajobs_email'] ) && ! empty( $options['usajobs_api_key'] ) )
    {
      $results = wp_cache_get( 'usajobsresults', 'jobify' );
      if ( false === $results )
      {

        $link = 'https://data.usajobs.gov/api/search?';

        if ( ! empty( $options['usajobs_keyword'] ) )
        {
          $link .= 'Keyword=' . urlencode( $options['usajobs_keyword'] ) . '&';
        }

        if ( ! empty( $options['usajobs_exclude_keyword'] ) )
        {
          $link .= 'KeywordExclusion=' . urlencode( $options['usajobs_exclude_keyword'] ) . '&';
        }

        if ( ! empty( $options['usajobs_location'] ) )
        {
          $link .= 'LocationName=' . urlencode( $options['usajobs_location'] ) . '&';
        }

         if ( ! empty( $options['usajobs_page'] ) )
        {
          $link .= 'Page=' . urlencode( $options['usajobs_page'] ) . '&';
        }

        if ( ! empty( $options['usajobs_limit'] ) )
        {
          $link .= 'ResultsPerPage=' . urlencode( $options['usajobs_limit'] ) . '&';
        }


        // Get cURL resource
        $curl = curl_init();
        // Set some options - we are passing in a useragent too here
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $link,
            CURLOPT_HTTPHEADER => array(
              'Host: data.usajobs.gov',
              'User-Agent: ' . $options['usajobs_email'],
              'Authorization-Key: ' . $options['usajobs_api_key'],
            )
        ));
        // Send the request & save response to $resp
        $response = curl_exec($curl);
        // Close request to clear up some resources
        curl_close($curl);

        $results = json_decode( $response );

        wp_cache_set( 'usajobsresults', $results, 'jobify', 43200 ); // Half a day
      }

      $search_results = ( ! empty ( $results->SearchResult->SearchResultItems ) ) ? $results->SearchResult->SearchResultItems : false;

      if ( $search_results )
      {
        foreach( $search_results as $key => $obj )
        {
          //print_r($obj);
          $jobs[] = array(
            'title'    => $obj->MatchedObjectDescriptor->PositionTitle,
            'company'  => $obj->MatchedObjectDescriptor->OrganizationName,
            'city'     => $obj->MatchedObjectDescriptor->PositionLocation[0]->CityName,
            'state'    => $obj->MatchedObjectDescriptor->PositionLocation[0]->CountrySubDivisionCode,
            'country'  => $obj->MatchedObjectDescriptor->PositionLocation[0]->CountryCode,
            'desc'     => $obj->MatchedObjectDescriptor->PositionFormattedDescription[0]->Content,
            'url'      => $obj->MatchedObjectDescriptor->PositionURI,
            'location' => $obj->MatchedObjectDescriptor->PositionLocation[0]->LocationName,
          );
        }
      }
    }

    return $jobs;
  },
  'options' => array(
    array(
      'title'   => __( 'API key', 'jobify' ),
      'name'    => 'usajobs_api_key',
      'desc'    => sprintf( __( '<span class="jobify__api__req">Required</span>. If you do not have a API key, you can receive one by heading to the <a href="%s" target="_blank">USAJOBS Developer Site</a>.', 'jobify' ), 'https://developer.usajobs.gov/' ),
      'default' => ''
    ),
    array(
      'title'   => __( 'Email address', 'jobify' ),
      'name'    => 'usajobs_email',
      'desc'    => __( '<span class="jobify__api__req">Required</span>. Enter the email address registered when creating the USAJOBS API key.', 'jobify' ),
      'default' => ''
    ),
    array(
      'title'   => __( 'Keyword', 'jobify' ),
      'name'    => 'usajobs_keyword',
      'desc'    => __( 'A search term, such as "ruby" or "java".', 'jobify' ),
      'default' => ''
    ),
    array(
      'title'   => __( 'Exclude Keywords', 'jobify' ),
      'name'    => 'usajobs_exclude_keyword',
      'desc'    => __( 'Search terms to exclude from the search (comma seperated).', 'jobify' ),
      'default' => ''
    ),
    array(
      'title'   => __( 'Location', 'jobify' ),
      'name'    => 'usajobs_location',
      'desc'    => __( 'A city name, zip code, or other location search term. Multiple values can be semicolon delimited.', 'jobify' ),
      'default' => ''
    ),
    array(
      'group' => array(
        array(
          'title'   => __( 'Page', 'jobify' ),
          'name'    => 'usajobs_page',
          'desc'    => __( 'Begin retrieving specified paged results.', 'jobify' ),
          'default' => '1',
          'type'    => 'number'
        ),
        array(
          'title'   => __( 'Limit', 'jobify' ),
          'name'    => 'usajobs_limit',
          'desc'    => __( 'Max number of results from USAJOBS (Max. 500).', 'jobify' ),
          'default' => '10',
          'type'    => 'number'
        ),
      )
    ),
  )
));