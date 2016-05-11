class ContentTools.ConfirmPublishDialog extends ContentTools.DialogUI
    constructor: ()->
        super('Se va a proceder a publicar los cambios.')
        @_state = 'populated'



    mount: () ->
        # Mount the widget
        super()

        # Update dialog class
        ContentEdit.addCSSClass(@_domElement, 'ct-confirm-dialog')

        # Update view class
        ContentEdit.addCSSClass(@_domView, 'ct-confirm-dialog__view')

        # Add controls

        # Image tools & progress bar
        domTools = @constructor.createDiv(
            ['ct-control-group', 'ct-control-group--left'])
        @_domControls.appendChild(domTools)


        @_domInfo = @constructor.createDiv([
            'ct-info'
            ])
        @_domInfo.textContent = ContentEdit._('¿Desea continuar?')
        @_domView.appendChild(@_domInfo)


        # Actions
        domActions = @constructor.createDiv(
            ['ct-control-group', 'ct-control-group--right'])
        @_domControls.appendChild(domActions)

        # OK button
        @_domOK = @constructor.createDiv([
            'ct-control',
            'ct-control--text',
            'ct-control--upload'
            ])
        @_domOK.textContent = ContentEdit._('Continuar')
        domActions.appendChild(@_domOK)

        # Cancel
        @_domCancel = @constructor.createDiv([
            'ct-control',
            'ct-control--text',
            'ct-control--cancel'
            ])
        @_domCancel.textContent = ContentEdit._('Cancelar')
        domActions.appendChild(@_domCancel)

        # Add interaction handlers
        @_addDOMEventListeners()

        @trigger('ConfirmPublishDialog.mount')

    _addDOMEventListeners: ()->
        super()
        @_domCancel.addEventListener 'click', (ev) =>
            @trigger('cancel')
        @_domOK.addEventListener 'click', (ev) =>
            @trigger('ok')