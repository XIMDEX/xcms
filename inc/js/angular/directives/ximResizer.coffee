angular.module("ximdex.common.directive").directive "ximResizer", ($document) ->

    #TODO: Make it jquery independet
    ($scope, $element, $attrs) ->
        size = 300
        expanded = $($attrs.ximResizerToggle).hasClass("hide")
        listenHidePanel = true
        mousemove = (event) ->
            # Handle vertical resizer
            x = event.pageX
            x = $document.width()-17  if  x > $document.width()-17
            x = parseInt($attrs.ximResizerMin)  if $attrs.ximResizerMin and x < $attrs.ximResizerMin
            $element.css left: x + "px"
            $($attrs.ximResizerLeft).css width: x + "px"
            $($attrs.ximResizerRight).css left: (x + parseInt($attrs.ximResizerWidth)) + "px"
            return
        mouseup = ->
            $document.unbind "mousemove", mousemove
            $document.unbind "mouseup", mouseup
            return
        $element.on "mousedown", (event) ->
            if(expanded)
                event.preventDefault()
                $document.on "mousemove", mousemove
                $document.on "mouseup", mouseup
            return

        hidePanel = () ->
            if(!expanded && listenHidePanel)
                a=7
                b=parseInt($attrs.ximResizerWidth)+a
                $($attrs.ximResizerLeft).animate
                    width: a+"px"
                ,
                    400
                ,
                    () ->
                $($attrs.ximResizerRight).animate
                    left: b+"px"
                ,
                    400
                ,
                    () ->
                $element.animate
                    left: a+"px"
                ,
                    400
                ,
                    () ->
                        listenHidePanel = false
            return

        showPanel = () ->
            if(!expanded && !listenHidePanel)
                $($attrs.ximResizerLeft).animate
                    width: size+"px"
                ,
                    400
                ,
                    () ->
                $($attrs.ximResizerRight).animate
                    left: parseInt($attrs.ximResizerWidth)+size+"px"
                ,
                    400
                ,
                    () ->
                $element.animate
                    left: size+"px"
                ,
                    400
                ,
                    () ->
                        listenHidePanel = true
            return

        $element.on "mouseenter", showPanel

        $($attrs.ximResizerLeft).on "mouseleave", hidePanel

        togglePanel = (event) ->
            $(this).toggleClass "hide"
            $(this).toggleClass "tie"

            expanded = !expanded
            if(!expanded)
                size = $($attrs.ximResizerLeft).width()
                hidePanel()
            return

        $($attrs.ximResizerToggle).on "click", togglePanel


        return
