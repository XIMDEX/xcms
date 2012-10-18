$.extend({
    parseJSON: function( data ) {
        if ( typeof data !== "string" || !data ) {
            return null;
        }    
        data = jQuery.trim( data );    
        if ( /^[\],:{}\s]*$/.test(data.replace(/\\(?:["\\\/bfnrt]|u[0-9a-fA-F]{4})/g, "@")
            .replace(/"[^"\\\n\r]*"|true|false|null|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?/g, "]")
            .replace(/(?:^|:|,)(?:\s*\[)+/g, "")) ) {    
            return window.JSON && window.JSON.parse ?
                window.JSON.parse( data ) :
                (new Function("return " + data))();    
        } else {
            jQuery.error( _("Invalid JSON: ") + data );
        }
    }
});

