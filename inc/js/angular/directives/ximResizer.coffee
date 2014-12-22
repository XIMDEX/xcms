angular.module("ximdex.common.directive").directive "ximResizer", ($document,$timeout) ->

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
                $($attrs.ximResizerLeft).css left: (-size-7) + "px"
                $($attrs.ximResizerRight).css left: (b-7) + "px"
                $timeout(
                    () ->
                        listenHidePanel = false
                ,
                    500
                )
            return

        showPanel = () ->
            if(!expanded && !listenHidePanel)
                $($attrs.ximResizerLeft).css left: 0 + "px"
                $($attrs.ximResizerRight).css left: (size+parseInt($attrs.ximResizerWidth)+7) + "px"
                $timeout(
                    () ->
                        listenHidePanel = true
                ,
                    500
                )
            return

        $element.on "mouseenter", showPanel

        $($attrs.ximResizerLeft).on "mouseleave", hidePanel

        togglePanel = (event) ->
            $(this).toggleClass "hide"
            $(this).toggleClass "tie"
            $($attrs.ximResizerLeft).toggleClass "hideable"
            $($attrs.ximResizerRight).toggleClass "hideable"
            $element.toggleClass "hideable"

            expanded = !expanded
            size = $($attrs.ximResizerLeft).width()
            if(!expanded)
                hidePanel()
            return

        $($attrs.ximResizerToggle).on "click", togglePanel


        return
