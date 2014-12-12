(function( $, document ) {
    var timeout = 5 * 60 * 1000,
        lastActive = 0,
        refreshCsrf = function() {
            return $.getJSON(
                '{{ urlFor( "revalidatecsrf") }}',
                function( data ) {
                    var $token = $( '#token' );
                    $token.val( data[ "{{ csrf_param }}" ] );
                    console.log( 'csrf token: ' + $token.val() );
                }
            );
        },
        ping = setInterval(function() {
            if ( new Date().getTime() - lastActive > timeout ) {
                console.log( 'Killing keep-alive job due to inactivity' );
                clearInterval( ping );
            } else {
                console.log( 'Refreshing csrf token' );
                refreshCsrf();
            }
        }, timeout ),
        debounce = function( func, wait ) {
            // Adapted from http://davidwalsh.name/javascript-debounce-function
            var timeout;
            return function() {
                var that = this,
                    args = arguments,
                    later = function() {
                        timeout = null;
                        func.apply( that, args );
                    };
                clearTimeout( timeout );
                timeout = setTimeout( later, wait );
            };
        },
        $document = $( document );

    $document.keydown( debounce(
        function() { lastActive = new Date().getTime(); },
        1000
    ) );
    $document.mousemove( debounce(
        function() { lastActive = new Date().getTime(); },
        1000
    ) );
    // TODO: refresh token on form submit?
})( jQuery, document );
