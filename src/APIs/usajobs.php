<?php
$settings = jobify_settings();
if ( ! empty( $settings['usajobs_email'] ) && ! empty( $settings['usajobs_api_key'] ) )
{
  jobify_addAPI( array(
    'title'   => __( 'USAJOBS', 'jobify' ),
    'logo'    => plugins_url( 'img/usajobs.jpg' , JOBIFY_PLUGIN ),
    'name'    => 'usajobs',
    'getJobs' => function( $options ) {
      $settings = jobify_settings();
      $jobs     = array();

      $results = wp_cache_get( 'usajobsresults', 'jobify' );
      if ( false === $results )
      {

        $link = 'https://data.usajobs.gov/api/search?';

        if ( ! empty( $options['usajobs_keyword'] ) )
        {
          $link .= 'Keyword=' . urlencode( $options['keyword'] ) . '&';
        }

        if ( ! empty( $options['usajobs_exclude_keyword'] ) )
        {
          $link .= 'KeywordExclusion=' . urlencode( $options['usajobs_exclude_keyword'] ) . '&';
        }

        if ( ! empty( $options['location'] ) )
        {
          $link .= 'LocationName=' . urlencode( $options['location'] ) . '&';
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
              'User-Agent: ' . $settings['usajobs_email'],
              'Authorization-Key: ' . $settings['usajobs_api_key'],
            )
        ));
        // Send the request & save response to $resp
        $response = curl_exec($curl);
        // Close request to clear up some resources
        curl_close($curl);

        $results = json_decode( $response );

        wp_cache_set( 'usajobsresults', $results, 'jobify', 43200 ); // Half a day

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
              'app_url'  => $obj->MatchedObjectDescriptor->PositionURI,
              'location' => $obj->MatchedObjectDescriptor->PositionLocation[0]->LocationName,
            );
          }
        }
      }

      return $jobs;
    },
    'options' => array(
      array(
        'title'   => __( 'Exclude Keywords', 'jobify' ),
        'name'    => 'usajobs_exclude_keyword',
        'desc'    => __( 'Search terms to exclude from the search (comma seperated).', 'jobify' ),
        'default' => ''
      ),
      array(
        'title'   => __( 'Limit', 'jobify' ),
        'name'    => 'usajobs_limit',
        'desc'    => __( 'Max number of results from USAJOBS (Max. 500).', 'jobify' ),
        'default' => '10',
        'type'    => 'number'
      ),
    )
  ));
}