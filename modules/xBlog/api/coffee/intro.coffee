class ContentEdit.Intro extends ContentEdit.Text
    constructor: (tagName, attributes, content) ->
        super(tagName, attributes)

        # The content of the text element
        if content instanceof HTMLString.String
            @content = content
        else
            # Strings are trimmed initially to prevent selection issues with
            # whitespaces inside of starting or ending tags
            # (e.g starting <p><a> abc</a>, or ending <a>abc </a></p>).
            @content = new HTMLString.String(content).trim()

    type: () ->
        # Return the type of element (this should be the same as the class name)
        return 'Intro'

    typeName: () ->
        # Return the name of the element type (e.g Image, List item)
        return 'Intro'

    _keyReturn: (ev) ->
        ev.preventDefault()

    # Do not delete this element if isWhitespace
    blur: () ->
# Remove editing focus from this element

# Last chance - check for changes in the content not captured before
# this point.
        if @isMounted()
            @_syncContent()
# Blur the DOM element
            try
                @_domElement.blur()
            catch error
# HACK: Do nothing if this fails, internet explorer doesn't
# allow blur to be triggered against the contentediable element
# programatically and will trigger the following error:
#
# `Unexpected call to method or property access.`

# Stop the element from being editable
            @_domElement.removeAttribute('contenteditable')

        root = ContentEdit.Root.get();
        if @isFocused()
            this._removeCSSClass 'ce-element--focused'
            root._focused = null
            root.trigger 'blur', @


# Register `ContentEdit.Text` the class with associated tag names
ContentEdit.TagNames.get().register(
    ContentEdit.Intro,
    'intro'
)