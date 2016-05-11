class ContentTools.CropImageDialog extends ContentTools.DialogUI
    constructor: (imageData, file)->
        super('Establece la imagen para el post')
        @_state = 'populated'
        # If the dialog is populated, this is the URL of the image
        @_imageData = imageData
        @_data = null
        @_file = file
        @_state = 'empty'
        # If an image uploader factory is defined create a new uploader for the
        # dialog.
        if ContentTools.IMAGE_UPLOADER
            ContentTools.IMAGE_UPLOADER(this)

    progress: (progress) ->
        # Get/Set upload progress
        if progress is undefined
            return @_progress

        @_progress = progress

        # Update progress bar width
        if not @isMounted()
            return

        @_domProgress.style.width = "#{ @_progress }%"

    populate: (url, size, id) ->
        @trigger('save', url, id)

    state: (state) ->
        # Set/get the state of the dialog (empty, uploading, populated)

        if state is undefined
            return @_state

        # Check that we need to change the current state of the dialog
        if @_state == state
            return

        # Modify the state
        prevState = @_state
        @_state = state

        # Update state modifier class for the dialog
        if not @isMounted()
            return

        ContentEdit.addCSSClass(@_domElement, "ct-image-dialog--#{ @_state }")
        ContentEdit.removeCSSClass(
            @_domElement,
            "ct-image-dialog--#{ prevState }"
        )

    mount: () ->
        # Mount the widget
        super()

        # Update dialog class
        ContentEdit.addCSSClass(@_domElement, 'ct-image-dialog')
        ContentEdit.addCSSClass(@_domElement, 'ct-image-dialog--empty')

        # Update view class
        ContentEdit.addCSSClass(@_domView, 'ct-image-dialog__view')

        # Add controls

        # Image tools & progress bar
        domTools = @constructor.createDiv(
            ['ct-control-group', 'ct-control-group--left'])
        @_domControls.appendChild(domTools)


        @_domError = @constructor.createDiv([
            'ct-error'
            ])
        @_domError.textContent = ContentEdit._('An error was found, please try again.')
        domTools.appendChild(@_domError)

        # Progress bar
        domProgressBar = @constructor.createDiv(['ct-progress-bar'])
        domTools.appendChild(domProgressBar)

        @_domProgress = @constructor.createDiv(['ct-progress-bar__progress'])
        domProgressBar.appendChild(@_domProgress)

        # Actions
        domActions = @constructor.createDiv(
            ['ct-control-group', 'ct-control-group--right'])
        @_domControls.appendChild(domActions)

        # Upload button
        @_domUpload = @constructor.createDiv([
            'ct-control',
            'ct-control--text',
            'ct-control--upload'
            ])
        @_domUpload.textContent = ContentEdit._('Subir')
        domActions.appendChild(@_domUpload)

        # Cancel
        @_domCancelUpload = @constructor.createDiv([
            'ct-control',
            'ct-control--text',
            'ct-control--cancel'
        ])
        @_domCancelUpload.textContent = ContentEdit._('Cancel')
        domActions.appendChild(@_domCancelUpload)

        # Clear
        @_domClear = @constructor.createDiv([
            'ct-control',
            'ct-control--text',
            'ct-control--clear'
            ])
        @_domClear.textContent = ContentEdit._('Clear')
        domActions.appendChild(@_domClear)

        @_img = document.createElement("img")
        @_img.src = @_imageData
        @_img.classList.add 'ct-image-dialog__image'
        @_domView.appendChild(@_img)

        # Add interaction handlers
        @_addDOMEventListeners()

        @trigger('CropImageDialog.mount')

    _addDOMEventListeners: ()->
        super()
        @_domUpload.addEventListener 'click', (ev) =>
            @trigger('imageUploader.fileReady', @_file, @_data)

        # Cancel upload
        @_domCancelUpload.addEventListener 'click', (ev) =>
            @trigger('imageUploader.cancelUpload', false)
            @trigger('cancel')


        $(@_img).cropper
            aspectRatio: 3.0/4.0
            scalable: false
            rotatable: false
            movable: false
            viewMode: 1
            crop: (e) =>
                @_data =
                    left: e.x
                    top: e.y
                    width: e.width
                    height: e.height
                return
