"use strict";

( function( $ ) {
  var JobifyTracker = function() {

    let methods = {};

    let jobs = $( ".jobifyJobs" );
    jobs.each( function() {
      let element = this;

    });

    $( "body" ).on( "click", ".jobifyJob a", function( e ) {
      let portal  = $( this ).closest( '.jobifyJob' ).data( 'portal' ),
          id      = $( this ).closest( '.jobifyJob' ).data( 'id' );

      if ( "indeed" === portal ) {
        indeed_clk( this, id );
      }
    });
  };

  var JobifyTracker = new JobifyTracker();
})( jQuery );