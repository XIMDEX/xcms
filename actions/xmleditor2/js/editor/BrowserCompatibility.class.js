/** ########################################## BrowserCompatibility ############################## */
/**
 *	Maintains compatibility between different browsers.
 *	This class is oriented to XML documents.
 */
XimdocEditor.prototype.BrowserCompatibility = {

	parseXMLString: function(xmlString) {
		/**
		 *	NOTE: Cleanup for MSIE!!!!
		 *	MSIE will fail if detect the following:
		 *		- a <xml/> tag.
		 *		- a line break.
		 *		- a DTD.
		 */
		if (IS_IE) {
			xmlString = xmlString.replace(/<\?xml(.*)\?>/ig, '');
			xmlString = xmlString.replace(/\n/ig, '');
			xmlString = xmlString.replace(/<!DOCTYPE(.*)\]>/g, '');
		}
		return xmlString;
	},

	parseXMLDocument: function(xmldoc) {
		/**
		 *	NOTE: sarissa implements parsererror detection
		 *	We use getParseErrorText sarissa method for IE
		 *       but not for FF, because parseerror attribute of documentElement is always setted to 0. (Sarissa emulates IE's parseError attribute)
		 */
		if (IS_IE) {
			this._parse_with_sarissa(xmldoc);
		} else {
			try {
				if(xmldoc.documentElement.tagName == "parsererror" ){
					console.error(xmldoc.documentElement.firstChild.nodeValue);
					throw(xmldoc.documentElement.firstChild.nodeValue);
				}
			}catch(e) {
					console.log("parserXMLDocument error ");
			}
		}

		return xmldoc;
	},

parseXSLString: function(xslString) {
	return xslString;
},

parseXSLDocument: function(xsldoc, view, noRenderizableElements) {


	$('output', xsldoc).remove(); 		// Being sure that the output is HTML for Firefox
	xsldoc = this.extendXSLDocument(xsldoc, view, noRenderizableElements);

	/**
	 *	NOTE:
	 *		MSIE needs a XML as a result of the XSL transformation, so it is included here the <xsl:output/> tag.
	 *		Firefox will crash if detect a <xsl:output/> tag.
	 */
	if (IS_IE) {
		return  this._create_xslOutput(xsldoc);
	}

	return xsldoc;
},

// TODO: Maintain a specific xsldoc for view, due to make xsldoc manipulation only once.
// NOTE: Using Jquery for xsldoc-dom manipulation is OK in FF, but not in IE
// NOTE: Jquery selectors fail for xsldoc-dom in IE
// NOTE: Creating namespaced elements in FF with createElement make xsltprocessor crash, not in IE
//       Correct way in FF is to use createElementNS
extendXSLDocument: function(xsldoc, view, noRenderizableElements) {

	if(view == 'tree' || noRenderizableElements.length == 0)
		return xsldoc;

	console.info(_('Found no renderizable elements: '), noRenderizableElements);

	var minusButton = this._create_minusButton(xsldoc);
	var folderIcon = this._create_folderIcon(xsldoc);
	var blankImage = this._create_blankImage(xsldoc);
	var spanTittle = this._create_spanTittle(xsldoc);
	var uidDiv = this._create_uidDiv(xsldoc);
	var containerDiv = this._create_containerDiv(xsldoc, minusButton, folderIcon, blankImage, spanTittle, uidDiv );

	return this._create_newTemplate(xsldoc, containerDiv, noRenderizableElements);
},

_create_xslOutput: function(xsldoc) {
	var xslOutput = xsldoc.createElement('xsl:output');
	xslOutput.setAttribute('method', 'xml');
	xslOutput.setAttribute('version', '1.0');
	xslOutput.setAttribute('encoding', 'UTF-8');
	xslOutput.setAttribute('indent', 'no');
	xslOutput.setAttribute('omit-xml-declaration', 'yes');	// MSIE will crash if detect a <xml/> tag.
	xsldoc.firstChild.insertBefore(xslOutput, xsldoc.firstChild.firstChild);

	return xsldoc;
},

_create_minusButton: function(xsldoc) {
	var minusButton = xsldoc.createElement('img');
	minusButton.setAttribute('src', url_root + '/xmd/images/tree/Lminus.png');
	minusButton.setAttribute('align', 'absmiddle');
	minusButton.setAttribute('class', 'minus folding');

	return minusButton;
},

_create_folderIcon: function(xsldoc) {
	var folderIcon = xsldoc.createElement('img');
	folderIcon.setAttribute('src', url_root + '/xmd/images/tree/openfolder.png');
	folderIcon.setAttribute('align', 'absmiddle');
	folderIcon.setAttribute('class', 'folder folding');

	return folderIcon;
},

_create_blankImage: function(xsldoc) {
	var blankImage = xsldoc.createElement('img');
	blankImage.setAttribute('src', url_root + '/xmd/images/tree/blank.png');
	blankImage.setAttribute('align', 'absmiddle');
	blankImage.setAttribute('width', '10px');

	return blankImage;
},

_create_spanTittle: function(xsldoc) {
	var spanTittle = xsldoc.createElement('span');
	spanTittle.setAttribute('uid', '{@uid}');
	spanTittle.setAttribute('editable', 'no');
	spanTittle.setAttribute('width', '10px');
	spanTittle.setAttribute('class', 'rngeditor_title folding');

	if (typeof xsldoc.createElementNS == 'undefined')
		var valueOf = xsldoc.createElement('xsl:value-of');
	else
		var valueOf = xsldoc.createElementNS('http://www.w3.org/1999/XSL/Transform', 'value-of');

	valueOf.setAttribute('select', 'local-name(.)');

	spanTittle.appendChild(valueOf);

	return spanTittle;
},

_create_uidDiv: function(xsldoc) {
	var uidDiv = xsldoc.createElement('div');
	uidDiv.setAttribute('uid', '{@uid}');
	uidDiv.setAttribute('id', 'tg_{@uid}');

	if (typeof xsldoc.createElementNS == 'undefined')
		var applyTemplates = xsldoc.createElement('xsl:apply-templates');
	else
		var applyTemplates = xsldoc.createElementNS('http://www.w3.org/1999/XSL/Transform', 'apply-templates');

	uidDiv.appendChild(applyTemplates);

	return uidDiv;
},

_create_containerDiv: function(xsldoc,minusButton, folderIcon, blankImage, spanTittle, uidDiv ) {
	var containerDiv = xsldoc.createElement('div');
	containerDiv.setAttribute('class', 'rngeditor_block');

	containerDiv.appendChild(minusButton);
	containerDiv.appendChild(folderIcon);
	containerDiv.appendChild(blankImage);
	containerDiv.appendChild(spanTittle);
	containerDiv.appendChild(uidDiv);

	return containerDiv;
},

_create_newTemplate: function(xsldoc, containerDiv, noRenderizableElements) {

	if (typeof xsldoc.createElementNS == 'undefined')
		var newTemplate = xsldoc.createElement('xsl:template');
	else
		var newTemplate = xsldoc.createElementNS('http://www.w3.org/1999/XSL/Transform', 'template');
	newTemplate.setAttribute('name', 'no_ptd_element');
	newTemplate.appendChild(containerDiv);

	var attrMatchValue = '';
	var numElements = noRenderizableElements.length;
	for(var i = 0; i < numElements; i++) {
		if(i != 0)
			attrMatchValue += ' | ';
		attrMatchValue += noRenderizableElements[i] + ' | ' + noRenderizableElements[i] + '//*';
	}
	newTemplate.setAttribute('match', attrMatchValue);

	xsldoc.firstChild.appendChild(newTemplate);

	return xsldoc;
},

_parse_with_sarissa: function(xmldoc) {
	var parserErrors = Sarissa.getParseErrorText(xmldoc);
	if(parserErrors != Sarissa.PARSED_OK) {
		console.error(parserErrors);
		throw(parserErrors);
	}

}

};
