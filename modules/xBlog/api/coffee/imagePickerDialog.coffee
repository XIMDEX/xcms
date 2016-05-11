class ContentTools.Tools.ImagePicker extends ContentTools.Tool

    ContentTools.ToolShelf.stow(@, 'imagePicker')

    @label = 'ImagePicker'
    @icon = 'imagePicker'

    @canApply: (element, selection) ->
        # Return true if the tool can be applied to the current
        # element/selection.
        return element.type() != 'Intro'

    @apply: (element, selection, callback) ->

        # If supported allow store the state for restoring once the dialog is
        # cancelled.
        if element.storeState
            element.storeState()

        # Set-up the dialog
        app = ContentTools.EditorApp.get()

        # Modal
        modal = new ContentTools.ModalUI()

        # Dialog
        dialog = new ContentTools.ImagePickerDialog()

        # Support cancelling the dialog
        dialog.bind 'cancel', () =>
            dialog.unbind('cancel')

            modal.hide()
            dialog.hide()

            if element.restoreState
                element.restoreState()

            callback(false)

        # Support saving the dialog
        dialog.bind 'ok', (imageURL, imageSize, imageAttrs) =>
            dialog.unbind('save')

            if not imageAttrs
                imageAttrs = {}

            imageAttrs.src = imageURL

            imageAttrs.width = if imageSize[0]? then imageSize[0] else 9999999
            imageAttrs.height = if imageSize[1]? then imageSize[1] else 9999999 
            console.log imageAttrs
            
            # Find insert position
            [node, index] = @_insertAt(element)
            parent = node.parent()

            # Che
            parentStyle = window.getComputedStyle(parent._domElement)
            widthParent = parseInt(parentStyle.width);
            widthParent -= parseInt(parentStyle.paddingLeft);
            widthParent -= parseInt(parentStyle.paddingRight);
            if widthParent < imageAttrs.width
                rel = widthParent / imageAttrs.width
                imageAttrs.width =  widthParent
                imageAttrs.height = parseInt(imageAttrs.height * rel)


            # Create the new image
            image = new ContentEdit.Image(imageAttrs)


            parent.attach(image, index)

            # Focus the new image
            image.focus()

            modal.hide()
            dialog.hide()

            callback(true)

        # Show the dialog
        app.attach(modal)
        app.attach(dialog)
        modal.show()
        dialog.show()



class ContentTools.ImagePickerDialog extends ContentTools.DialogUI
    constructor: (data, previewElement = null)->
        super('Selecciona una imagen para el post')
        @_state = 'populated'
        # If the dialog is populated, this is the URL of the image
        @_imageURL = data
        @_previewElement = previewElement


    mount: () ->
        # Mount the widget
        super()

        # Update dialog class
        ContentEdit.addCSSClass(@_domElement, 'ct-imagepicker-dialog')
        ContentEdit.addCSSClass(@_domElement, 'ct-imagepicker-dialog--empty')

        ContentEdit.addCSSClass(@_domView, 'ct-imagepicker-dialog--empty')

        # Update view class

        @_selectImage = document.createElement("select")
        @_selectImage.className += " masonry";
        @_domView.appendChild @_selectImage

        # Actions
        domActions = @constructor.createDiv(
            ['ct-control-group', 'ct-control-group--right'])
        @_domControls.appendChild(domActions)

        # Cancel
        @_domCancel = @constructor.createDiv([
            'ct-control',
            'ct-control--text',
            'ct-control--cancel'
            ])
        @_domCancel.textContent = ContentEdit._('Cancelar')
        domActions.appendChild(@_domCancel)

        # OK button
        @_domOK = @constructor.createDiv([
            'ct-control',
            'ct-control--text',
            'ct-control--upload'
        ])
        @_domOK.textContent = ContentEdit._('Aceptar')
        domActions.appendChild(@_domOK)

        $.getJSON 'getImages', (data) =>
            for d in data
                if d.nodetype == "5040"
                    option = document.createElement("option")
                    option.value = d.nodeid + ',' + d.file + ',' + d.width + ',' + d.height
                    #option.text = d.name
                    option.setAttribute 'data-img-src', d.file
                    @_selectImage.appendChild option
            $(@_selectImage).imagepicker()

        # Add interaction handlers
        @_addDOMEventListeners()

        @trigger('CropImageDialog.mount')

    _addDOMEventListeners: ()->
        super()
        @_domCancel.addEventListener 'click', (ev) =>
            @trigger('cancel')
        @_domOK.addEventListener 'click', (ev) =>
            value = @_selectImage.value
            valueSplitted = value.split(',')
            imageAttrs = 
                'data-xid': valueSplitted[0]
            imageSize =[ 
                valueSplitted[2]
                valueSplitted[3]
            ]
            @trigger('ok', valueSplitted[1], imageSize, imageAttrs)
        