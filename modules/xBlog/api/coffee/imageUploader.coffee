ImageUploader = (dialog) ->
    image = undefined
    xhr = undefined
    xhrComplete = undefined
    xhrProgress = undefined
    dialog.bind 'imageUploader.cancelUpload', (setDialogEmpty = true) ->
# Cancel the current upload
# Stop the upload
        if xhr
            xhr.upload.removeEventListener 'progress', xhrProgress
            xhr.removeEventListener 'readystatechange', xhrComplete
            xhr.abort()
        if setDialogEmpty
            # Set the dialog to empty
            dialog.state 'empty'
        return
    dialog.bind 'imageUploader.clear', ->
# Clear the current image
        dialog.clear()
        image = null
        return
    dialog.bind 'imageUploader.fileReady', (file, data = null) ->
# Upload a file to the server
        formData = undefined
        # Define functions to handle upload progress and completion

        xhrProgress = (ev) ->
# Set the progress for the upload
            dialog.progress ev.loaded / ev.total * 100
            return

        xhrComplete = (ev) ->
            response = undefined
            # Check the request is complete
            if ev.target.readyState != 4
                return
            # Clear the request
            xhr = null
            xhrProgress = null
            xhrComplete = null
            # Handle the result of the upload
            if parseInt(ev.target.status) == 200
# Unpack the response (from JSON)
                response = JSON.parse(ev.target.responseText)
                # Store the image details
                image =
                    size: response.size
                    url: response.url
                    id: response.id
                # Populate the dialog
                dialog.populate image.url, image.size, image.id
            else
# The request failed, notify the user
                new (ContentTools.FlashUI)('no')
                dialog.state 'error'
            return

        # Set the dialog state to uploading and reset the progress bar to 0
        dialog.state 'uploading'
        dialog.progress 0
        # Build the form data to post to the server
        formData = new FormData
        formData.append 'image', file
        if data != null
            formData.append "left", data.left
            formData.append "top", data.top
            formData.append "width", data.width
            formData.append "height", data.height
        # Make the request
        xhr = new XMLHttpRequest
        xhr.upload.addEventListener 'progress', xhrProgress
        xhr.addEventListener 'readystatechange', xhrComplete
        xhr.open 'POST', window.baseUrl + '/upload-image', true
        xhr.send formData
        return
    dialog.bind 'imageUploader.save', ->
        dialog.save image.url, image.size,
            'data-ce-max-width': image.size[0]
            'data-xid': image.id
        return
        crop = undefined
        cropRegion = undefined
        formData = undefined
        # Define a function to handle the request completion

        xhrComplete = (ev) ->
# Check the request is complete
            if ev.target.readyState != 4
                return
            # Clear the request
            xhr = null
            xhrComplete = null
            # Free the dialog from its busy state
            dialog.busy false
            # Handle the result of the rotation
            if parseInt(ev.target.status) == 200
# Unpack the response (from JSON)
                response = JSON.parse(ev.target.responseText)
                # Trigger the save event against the dialog with details of the
                # image to be inserted.
                dialog.save response.url, response.size,
                    'alt': response.alt
                    'data-ce-max-width': image.size[0]
            else
# The request failed, notify the user
                new (ContentTools.FlashUI)('no')
            return

        # Set the dialog to busy while the rotate is performed
        dialog.busy true
        # Build the form data to post to the server
        formData = new FormData
        formData.append 'url', image.url
        # Set the width of the image when it's inserted, this is a default
        # the user will be able to resize the image afterwards.
        formData.append 'width', 600
        # Check if a crop region has been defined by the user
        if dialog.cropRegion()
            formData.append 'crop', dialog.cropRegion()
        # Make the request
        xhr = new XMLHttpRequest
        xhr.addEventListener 'readystatechange', xhrComplete
        xhr.open 'POST', '/insert-image', true
        xhr.send formData
        return
    return

window.ImageUploader = ImageUploader