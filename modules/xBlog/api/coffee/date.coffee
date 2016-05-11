class ContentTools.Tools.Date extends ContentTools.Tools.Bold

    # Insert/Remove a date.

    ContentTools.ToolShelf.stow(@, 'date')

    @label = 'Date'
    @icon = 'date'
    @tagName = 'time'

    @getAttr: (attrName, element, selection) ->
        # Find the first character in the selected text that has an `a` tag
        # and return the named attributes value.
        [from, to] = selection.get()
        selectedContent = element.content.slice(from, to)
        for c in selectedContent.characters
            if not c.hasTags('time')
                continue

            for tag in c.tags()
                if tag.name() == 'time'
                    return tag.attr(attrName)

        return ''

    @canApply: (element, selection) ->
# Return true if the tool can be applied to the current
# element/selection.
        return element.type() isnt 'Image' and selection? and (selection.isCollapsed() or @isApplied(element, selection))

    @isApplied: (element, selection) ->
# Return true if the tool is currently applied to the current
# element/selection.
        return super(element, selection)

    @apply: (element, selection, callback) ->
        applied = false

        # Prepare text elements for adding a date

        # Text elements
        element.storeState()

        # Add a fake selection wrapper to the selected text so that it
        # appears to be selected when the focus is lost by the element.
        selectTag = new HTMLString.Tag('span', {'class': 'ct--puesdo-select'})
        [from, to] = selection.get()

        if selection.isCollapsed()
            element.content = element.content.insert(from, '&nbsp;')
            element.content = element.content.format(from, to + 1, selectTag)
        else
            element.content = element.content.format(from, to, selectTag)

        element.updateInnerHTML()

        # Measure a rectangle of the content selected so we can position the
        # dialog centrally.
        domElement = element.domElement()
        measureSpan = domElement.getElementsByClassName('ct--puesdo-select')
        rect = measureSpan[0].getBoundingClientRect()

        if selection.isCollapsed()
            element.content = element.content.unformat(from, to + 1, selectTag)
            element.content.characters.splice(to, 1)
        else
            element.content = element.content.unformat(from, to, selectTag)

        element.updateInnerHTML()
        element.restoreState()

        # Set-up the dialog
        app = ContentTools.EditorApp.get()

        # Modal
        modal = new ContentTools.ModalUI(transparent=true, allowScrolling=true)

        # When the modal is clicked on the dialog should close
        modal.bind 'click', () ->
            @unmount()
            dialog.hide()

            callback(applied)

        # Dialog
        dialog = new ContentTools.DateDialog(
            @getAttr('datetime', element, selection)
        )
        dialog.position([
            rect.left + (rect.width / 2) + window.scrollX,
            rect.top + (rect.height / 2) + window.scrollY
        ])

        dialog.bind 'save', (dateAttr) ->
            dialog.unbind('save')

            applied = true

            # Text elements

            # Clear any existing date
            element.content = element.content.unformat(from, to, 'time')
            if !selection.isCollapsed()
                element.content.characters.splice(from, to - from)

            # If specified add the new date
            if dateAttr.datetime
                time = new HTMLString.Tag('time', dateAttr)

                localDate = moment(dateAttr.datetime).format('LL')
                element.content = element.content.insert(from, localDate)
                element.content = element.content.format(from, from + localDate.length, time)
                element.content.optimize()

            element.updateInnerHTML()

            # Make sure the element is marked as tainted
            element.taint()

            # Close the modal and dialog
            modal.trigger('click')

        app.attach(modal)
        app.attach(dialog)
        modal.show()
        dialog.show()

ContentTools.DEFAULT_TOOLS.push ["imagePicker"]

class ContentTools.DateDialog extends ContentTools.AnchoredDialogUI

    # An anchored dialog to support inserting/modifying a date

    # The target that will be set by the date tool if the open in new window
    # option is selected.
    NEW_WINDOW_TARGET = '_blank'

    constructor: (datetime='') ->
        super()
        moment.locale('es')
        # The initial value to set the href and target attribute
        # of the date as (e.g if we're editing a date).
        @_datetime = datetime

    mount: () ->
# Mount the widget
        super()
        @_domElement.classList.add('ct-anchored-dialog__date')
        @_domInput = @constructor.createDiv(['ct-anchored-dialog__calendar'])
        @_domInput.setAttribute('name', 'datetime')
        @_domElement.appendChild(@_domInput)

        # Add interaction handlers
        @_addDOMEventListeners()

    save: (date) ->
# Save the date. This method triggers the save method against the dialog
# allowing the calling code to listen for the `save` event and manage
# the outcome.

        if not @isMounted
            return @trigger('save', '')

        dateAttr = {}
        dateAttr.datetime = date

        @trigger('save', dateAttr)

    show: () ->
# Show the widget
        super()

        # Once visible automatically give focus to the date input
        @_domInput.focus()

    unmount: () ->
# Unmount the component from the DOM

# Unselect any content
        if @isMounted()
            @_domInput.blur()

        super()

        @_domButton = null
        @_domInput = null

# Private methods

    _addDOMEventListeners: () ->
# Add event listeners for the widget

# Add support for saving the date whenever the `return` key is pressed
# or the button is selected.

# Input
        rome(@_domInput,
            time: false
            initialValue: @_datetime
        ).on('data', (value) =>
            @save(value)
        )