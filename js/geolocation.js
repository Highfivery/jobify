"use strict";

( function( $ ) {
  var JobifyGeolocation = function() {

    let methods = {
      tplParse: function( tpl, obj ) {
        for( var key in obj ) {
          let search = "\\[" + key.trim().toLowerCase() + "\\]";
          tpl = tpl.replace( new RegExp( search, "g" ), obj[key] );
        };

        return tpl;
      },
      getJobs: function( params, callback ) {
        let data = {
          action:   'jobify_get_jobs',
          security: Jobify.security,
          params: params
        };

        $.post( Jobify.ajaxurl, data, function( response ) {
          response = $.parseJSON( response );
          callback( response );
        });
      },
      getLocation: function( callback ) {
        if ( navigator.geolocation ) {
          navigator.geolocation.getCurrentPosition( function( position ) {
            callback( position );
          }, function() {
            //handleNoGeolocation(browserSupportFlag);
          });
        }
      }
    };

    let jobs = $( ".jobifyJobs" );
    jobs.each( function() {
      let element                 = this,
          geolocation             = $( this ).data( 'geolocation' ),
          template                = $( this ).data( 'template' ),
          tpl                     = $( "#" + template ).html(),
          keyword                 = $( this ).data( 'keyword' ),
          apis                    = $( this ).data( 'apis' ),
          limit                   = $( this ).data( 'limit' ),

          careerjet_locale        = $( this ).data( 'careerjet-locale' ),
          githubjobs_fulltime     = $( this ).data( 'githubjobs-fulltime' ),
          indeed_radius           = $( this ).data( 'indeed-radius' ),
          indeed_fromage          = $( this ).data( 'indeed-fromage' ),
          indeed_limit            = $( this ).data( 'indeed-limit' ),
          usajobs_exclude_keyword = $( this ).data( 'usajobs-exclude-keyword' );

      if ( geolocation === 'on' ) {
        methods.getLocation( function( loc ) {
          let getJobsParam = {
            lat                 : loc.coords.latitude,
            lng                 : loc.coords.longitude,
            keyword             : keyword,
            limit               : limit,
            portals             : [],

            careerjet_locale        : careerjet_locale,
            githubjobs_fulltime     : githubjobs_fulltime,
            indeed_radius           : indeed_radius,
            indeed_fromage          : indeed_fromage,
            indeed_limit            : indeed_limit,
            usajobs_exclude_keyword : usajobs_exclude_keyword,
            usajobs_limit           : usajobs_limit
          };

          let enabledAPIs = apis.split( "|" );
          for( var i = 0; enabledAPIs.length > i; i++ ) {
            getJobsParam['portals'].push( enabledAPIs[i] );
          }

          methods.getJobs( getJobsParam, function( jobs ) {
            let html = '',
                cnt  = 0;

            if ( jobs.length > 0 ) {
              $( jobs ).each( function() {
                html += "<div class='jobifyJob' data-portal='" + this.portal + "'>" + methods.tplParse( tpl, this ) + "</div>";
                cnt++;
                if ( cnt >= limit ) return false;
              });

              $( element ).html( html );
            }
          });
        });
      }
    });
  };

  var JobifyGeolocation = new JobifyGeolocation();
})( jQuery );