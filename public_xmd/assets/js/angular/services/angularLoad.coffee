angular.module('angularLoad', []).service 'angularLoad', [
    '$document'
    '$q'
    '$timeout'
    ($document, $q, $timeout) ->
        ###*
        # Dynamically loads the given script
        # @param src The url of the script to load dynamically
        # @returns {*} Promise that will be resolved once the script has been loaded.
        ###

        loader = (createElement) ->
            promises = {}
            (url) ->
                ext = url.split('.').pop()
                if typeof promises[url] == 'undefined' || ext != "css"
                    #removejscssfile url, ext if ext != "css"
                    deferred = $q.defer()
                    element = createElement(url)
                    element.onload =
                        element.onreadystatechange = (e) ->
                            $timeout ->
                                deferred.resolve e
                                return
                            return

                    element.onerror = (e) ->
                        $timeout ->
                            deferred.reject e
                            return
                        return
                    if ext == "css"
                        promises[url] = deferred.promise
                    else
                        return deferred.promise
                promises[url]

        @loadScript = loader((src) ->
            script = $document[0].createElement('script')
            script.src = src
            $document[0].body.appendChild script
            script
        )

        ###*
        # Dynamically loads the given CSS file
        # @param href The url of the CSS to load dynamically
        # @returns {*} Promise that will be resolved once the CSS file has been loaded.
        ###

        @loadCSS = loader((href) ->
            style = $document[0].createElement('link')
            style.rel = 'stylesheet'
            style.type = 'text/css'
            style.href = href
            $document[0].head.appendChild style
            style
        )
        return
]