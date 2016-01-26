<?php
$settings = jobify_settings();
if ( ! empty( $settings['usajobs_email'] ) && ! empty( $settings['usajobs_api_key'] ) )
{
  jobify_addAPI( array(
    'key'     => 'usajobs',
    'title'   => __( 'USAJOBS', 'jobify' ),
    'logo'    => plugins_url( 'img/usajobs.jpg' , JOBIFY_PLUGIN ),
    'getJobs' => function( $options ) {
      $settings = jobify_settings();
      $jobs     = array();

      $results = wp_cache_get( 'jobs-usajobs-' . jobify_string( $options ), 'jobify' );
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

        wp_cache_set( 'jobs-usajobs-' . jobify_string( $options ), $results, 'jobify', 43200 ); // Half a day

        $search_results = ( ! empty ( $results->SearchResult->SearchResultItems ) ) ? $results->SearchResult->SearchResultItems : false;

        if ( $search_results )
        {
          foreach( $search_results as $key => $obj )
          {
            //print_r($obj);
            $jobs[] = array(
              'portal'   => 'usajobs',
              'title'    => ( ! empty( $obj->MatchedObjectDescriptor->PositionTitle ) ) ? $obj->MatchedObjectDescriptor->PositionTitle : false,
              'company'  => ( ! empty( $obj->MatchedObjectDescriptor->OrganizationName ) ) ? $obj->MatchedObjectDescriptor->OrganizationName : false,
              //'company_logo' => ( ! empty( $obj->company_logo ) ) ? $obj->company_logo : false,
              //'company_url'   => ( ! empty( $obj->company_url ) ) ? $obj->company_url : false,
              'city'     => ( ! empty( $obj->MatchedObjectDescriptor->PositionLocation[0]->CityName ) ) ? $obj->MatchedObjectDescriptor->PositionLocation[0]->CityName : false,
              'state'    => ( ! empty( $obj->MatchedObjectDescriptor->PositionLocation[0]->CountrySubDivisionCode ) ) ? $obj->MatchedObjectDescriptor->PositionLocation[0]->CountrySubDivisionCode : false,
              'country'  => ( ! empty( $obj->MatchedObjectDescriptor->PositionLocation[0]->CountryCode ) ) ? $obj->MatchedObjectDescriptor->PositionLocation[0]->CountryCode : false,
              'desc'     => ( ! empty( $obj->MatchedObjectDescriptor->PositionFormattedDescription[0]->Content ) ) ? $obj->MatchedObjectDescriptor->PositionFormattedDescription[0]->Content : false,
              'app_url'  => ( ! empty( $obj->MatchedObjectDescriptor->PositionURI ) ) ? $obj->MatchedObjectDescriptor->PositionURI : false,
              //'lat'      => ( ! empty( $ary['latitude'] ) ) ? $ary['latitude'] : false,
              //'long'     => ( ! empty( $ary['longitude'] ) ) ? $ary['longitude'] : false,
              //'date'     => ( ! empty( $ary['date'] ) ) ? $ary['date'] : false,
              'location' => ( ! empty( $obj->MatchedObjectDescriptor->PositionLocation[0]->LocationName ) ) ? $obj->MatchedObjectDescriptor->PositionLocation[0]->LocationName : false,
              /*'custom'   => array(
                'onmousedown'           => ( ! empty( $ary['onmousedown'] ) ) ? $ary['onmousedown'] : false,
                'source'                => ( ! empty( $ary['source'] ) ) ? $ary['source'] : false,
                'sponsored'             => ( ! empty( $ary['sponsored'] ) ) ? $ary['sponsored'] : false,
                'expired'               => ( ! empty( $ary['expired'] ) ) ? $ary['expired'] : false,
                'indeedApply'           => ( ! empty( $ary['indeedApply'] ) ) ? $ary['indeedApply'] : false,
                'formattedRelativeTime' => ( ! empty( $ary['formattedRelativeTime'] ) ) ? $ary['formattedRelativeTime'] : false,
                'noUniqueUrl'           => ( ! empty( $ary['noUniqueUrl'] ) ) ? $ary['noUniqueUrl'] : false,
              )*/
              //'address'  => ( ! empty( $ary['address'] ) ) ? $ary['address'] : false,
              //'phone'  => ( ! empty( $ary['phone'] ) ) ? $ary['phone'] : false,
              //'email'  => ( ! empty( $ary['email'] ) ) ? $ary['email'] : false,
              //'type'  => ( ! empty( $ary['type'] ) ) ? $ary['type'] : false,
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