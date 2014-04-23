
function prepareForm(autoSave, publicate) {

	if(autoSave && autoSave.type)
		autoSave = false;

	var savebutton = getFromSelector('kupu-save-button');

	if(autoSave === false)
		loadingImage.showLoadingImage();

    var drawer = window.document.getElementById('kupu-librarydrawer');
    if (drawer) {
        drawer.parentNode.removeChild(drawer);
    }

    kupu.prepareForm(savebutton.form, 'kupu');
    //savebutton.form.submit();

    // First step is refreshing the XML document content
    if(autoSave === false)
	    kupu.updateEditor();

	var xmlfile = "";
	//var content = savebutton.form.kupu.value;

	var ximdoc = kupu.getXimDocument();
	var content = ximdoc.saveXML({
		asString: true,
		hideXimlets: true,
		resolveXimlinks: true
	});
	var lang = "es";

	var encodedContent = "XML=" + encodeURIComponent(xmlfile) +
						 "&content=" + encodeURIComponent(content) +
						 "&lang=" + encodeURIComponent(lang);
	var encondedObject = {
		"XML": xmlfile,
		"content": content,
		"lang": lang
	};

	// NOTE: XML validation on the server
	var validate = ximdoc.schemaValidatorIsActive();
	ximdoc.setSchemaValidator(false);

	ximdoc.validateXML(function(valid, msg) {

		if(autoSave === false) {
			if (!valid) {
				kupu.alert(msg);
			}
			loadingImage.showLoadingImage();
			ximdoc.setSchemaValidator(validate);
		}

		if(publicate === true) {
			com.ximdex.ximdex.editors.PublicateHandler(kupu.getBaseURL(), encondedObject, {
				onComplete: function(req, json) {
					if(autoSave === false) {
						kupu.alert(_('The document has been saved and sent to be published.'));
						loadingImage.hideLoadingImage();
					} else {
						kupu.logMessage(_('The document has been saved and sent to be published.'));
					}
				},
				onError: function(req) {
					if(autoSave === false) {
						kupu.alert(_('An error occurred while saving and/or sending the document to publish.\n\n' + 'State: ' + req.status + '\nError: ' + req.statusText));
						loadingImage.hideLoadingImage();
					} else {
						kupu.alert(_('An error occurred while saving and/or sending the document to publish.\n\n' + 'State: ' + req.status + '\nError: ' + req.statusText));
					}
				}
			}, autoSave);
		} else {
			com.ximdex.ximdex.editors.SaveHandler(kupu.getBaseURL(), encondedObject, {
				onComplete: function(req, json) {
					if(autoSave === false) {
						kupu.alert(_('The document has been successfully saved.'));
						loadingImage.hideLoadingImage();
						//kupu.reloadXml();
					} else {
						kupu.logMessage(_('The document has been successfully saved.'));
					}
				},
				onError: function(req) {
					if(autoSave === false) {
						kupu.alert(_('An error occurred while saving the document.\n\n' + 'State: ' + req.status + '\nError: ' + req.statusText));
						loadingImage.hideLoadingImage();
					} else {
						kupu.alert(_('An error occurred while saving the document\n\n' + 'State: ' + req.status + '\nError: ' + req.statusText));
					}
				}
			}, autoSave);
		}
	});
}


