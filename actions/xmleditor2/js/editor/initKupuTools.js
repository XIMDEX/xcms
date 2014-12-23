
function initKupuTools(kupu) {

    var contextmenu = new ContextMenu();
    kupu.setContextMenu(contextmenu);

    // now we can create a UI object which we can use from the UI
    // var ui = new KupuUI('kupu-tb-styles');

    // the ui must be registered to the editor like a tool so it can be notified
    // of state changes
    // kupu.registerTool('ui', ui); // XXX Should this be a different method?

    // function that returns a function to execute a button command
    var execCommand = function(cmd) {
        return function(button, editor) {
            editor.execCommand(cmd);
        };
    };

    var removeelementbutton = new XimdocRemoveElementButton('kupu-remove-button');
	kupu.registerTool('removebutton', removeelementbutton);

//    var proptool = new PropertyTool('kupu-properties-title', 'kupu-properties-description');
//    kupu.registerTool('proptool', proptool);

    var showpathtool = new ShowPathTool();
    kupu.registerTool('showpathtool', showpathtool);

	// ximdoc tools

	// Base tool
	var ximdoctool = new XimdocTool();
	kupu.registerTool('ximdoctool', ximdoctool);

	var imagestool = new ImagesTool();
	kupu.registerTool('imagestool', imagestool);

	var editorviewtool = new EditorViewTool();
	kupu.registerTool('editorviewtool', editorviewtool);

	var hovertool = new HoverTool();
	kupu.registerTool('hovertool', hovertool);

	if (kupu.getXimDocument().expertModeIsAllowed()) {
	    var schemavalidatorbutton = new SchemaValidatorButton('kupu-schemavalidator-button');
	    kupu.registerTool('schemavalidatorbutton', schemavalidatorbutton);
    } else {
    	$('#kupu-schemavalidator-button').remove();
    }

	var ximdoceditablecontenttool = new XimdocEditableContentTool();
	kupu.registerTool('ximdoceditablecontenttool', ximdoceditablecontenttool);

	var ximdoccontextmenutool = new XimdocContextMenuTool();
	kupu.registerTool('ximdoccontextmenutool', ximdoccontextmenutool);

	var ximdocspellcheckertool = new XimdocSpellCheckerTool();
	kupu.registerTool('ximdocspellcheckertool', ximdocspellcheckertool);

    var ximdocpreviewtool = new XimdocPreviewTool();
    kupu.registerTool('ximdocpreviewtool', ximdocpreviewtool);

	var tablemanagertool = new TableManagerTool();
	kupu.registerTool('tablemanagertool', tablemanagertool);

	var listmanagertool = new ListManagerTool();
	kupu.registerTool('listmanagertool', listmanagertool);

	var attributestool = new AttributesTool();
	kupu.registerTool('attributestool', attributestool);

	var toolcontainertoolbox = new ToolContainerToolBox({});
	ximdoctool.registerToolBox('toolcontainertoolbox', toolcontainertoolbox);


	/********* TOOLBOX FOR THE RIGHT PANEL ************/

	/**Tools and toolbox declaration**/

	// Annotation tool
	var ximdocannotationtool = new XimdocAnnotationTool();
	kupu.registerTool('ximdocannotationtool', ximdocannotationtool);

	// Annotation toolbox
    var annotationstoolbox = new AnnotationsToolBox({
		toolboxId: 'xedit-annotations-toolbox',
		ctrlButtonId: 'xedit-annotations-toolbox-button',
		buttonActiveClass: 'xedit-annotations-toolbox-button',
		buttonInactiveClass: 'xedit-annotations-toolbox-button-closed',
		visible: new Boolean(kupu.config.toolboxes.annotations)
	});

	//Availables elements for the selected element toolbox
	var rngelementstoolbox = new RNGElementsToolBox({
		toolboxId: 'xedit-rngelements-toolbox',
		ctrlButtonId: 'xedit-rngelements-toolbox-button',
		buttonActiveClass: 'xedit-rngelements-toolbox-button',
		buttonInactiveClass: 'xedit-rngelements-toolbox-button-closed',
		visible: new Boolean(kupu.config.toolboxes.rngelements)
	});

	//Selected element attributes toolbox
	var attributestoolbox = new AttributesToolBox({
		toolboxId: 'xedit-attributes-toolbox',
		ctrlButtonId: 'xedit-attributes-toolbox-button',
		buttonActiveClass: 'xedit-attributes-toolbox-button',
		buttonInactiveClass: 'xedit-attributes-toolbox-button-closed',
		visible: new Boolean(kupu.config.toolboxes.attributes)
	});

	//Changeset in the current doc
	var changesettoolbox = new ChangesetToolBox({	
		toolboxId: 'xedit-changeset-toolbox',
		ctrlButtonId: 'kupu-toolbox-undolog-button',
		buttonActiveClass: 'kupu-toolbox-undolog-button',
		buttonInactiveClass: 'kupu-toolbox-undolog-button-closed',
		maxlength: kupu.config.history_max_changes,
		visible: new Boolean(kupu.config.toolboxes.history)
	});
	
	//Current document's info toolbox
	var infotoolbox = new InfoToolBox({
		toolboxId: 'xedit-info-toolbox',
		ctrlButtonId: 'xedit-info-toolbox-button',
		buttonActiveClass: 'xedit-info-toolbox-button',
		buttonInactiveClass: 'xedit-info-toolbox-button-closed',
		visible: new Boolean(kupu.config.toolboxes.info)
	});
	/**END OF Tools and toolbox declaration**/

	/**TOOLBOX REGISTER. In the reverse order*/

	/**
		1. Tag (fixed, in Smarty)
		2. Annotation Tool Box
		3. Availables Element.
		4. Attributes for the current element
		5. Info
		6. Channels
		7. Change set
		8. Log.
	*/
	ximdoctool.registerToolBox('changesettoolbox', changesettoolbox);	
	ximdoctool.registerToolBox('infotoolbox', infotoolbox);
	ximdoctool.registerToolBox('rngelementstoolbox', rngelementstoolbox);
	attributestool.registerToolBox('attributestoolbox', attributestoolbox);
	ximdocannotationtool.registerToolBox('annotationstoolbox', annotationstoolbox);
	
	/**END OF TOOLBOX REGISTER.**/

    var highlighttoolbox = new HighlightToolBox();
    hovertool.registerToolBox('highlighttoolbox', highlighttoolbox);
    
    var formViewToolBox = new FormViewToolBox();
    hovertool.registerToolBox('formViewToolBox', formViewToolBox);

    var draggabletoolbox = new DraggablesToolBox();
    hovertool.registerToolBox('draggabletoolbox', draggabletoolbox);

    // Drawers...

    // Function that returns function to open a drawer
    var opendrawer = function(drawerid) {
        return function(button, editor) {
            drawertool.openDrawer(drawerid);
        };
    };

    // Function that returns function to open a ximdocdrawer
    var openximdocdrawer = function(drawerid) {
        return function(button, editor) {
            ximdocdrawertool.openDrawer(drawerid);
        };
    };

    var spellchecker = new KupuButton('kupu-spellchecker-button', function() {
    	$(this.button).toggleClass('kupu-spellchecker-pressed').toggleClass('kupu-spellchecker');
    	ximdocspellcheckertool.doCheck();
    });
    kupu.registerTool('spellchecker', spellchecker);

    var annotation = new KupuButton('kupu-annotation-button', function() {ximdocannotationtool.doAnnotate();});
    kupu.registerTool('annotation', annotation);

    var previewmenu = new KupuMenu('kupu-prevdoc-menu', function(channel) {ximdocpreviewtool.preview(channel);});
    kupu.registerTool('previewmenu', previewmenu);

    // create some drawers, drawers are some sort of popups that appear when a
    // toolbar button is clicked
    var drawertool = new DrawerTool();
    kupu.registerTool('drawertool', drawertool);

    var ximdocdrawertool = new XimdocDrawerTool();
    kupu.registerTool('ximdocdrawertool', ximdocdrawertool);

    var toolbartool = new ToolbarTool();
    kupu.registerTool('toolbartool', toolbartool);

	var rmximdextool = new RMXimdexTool();
	kupu.registerTool('rmximdextool', rmximdextool);

	// MAIN TOOLBOXES
    kupu.maintoolboxes['xedit-attributes-toolbox'] 	= attributestoolbox;

//    kupu.maintoolboxes['xedit-toolbar-toolbox'] = toolbartoolbox;
    	kupu.maintoolboxes['xedit-rngelements-toolbox'] 	= rngelementstoolbox;
    	kupu.maintoolboxes['xedit-annotations-toolbox'] 	= annotationstoolbox;
    	kupu.maintoolboxes['xedit-changeset-toolbox'] 		= changesettoolbox;
	kupu.maintoolboxes['xedit-info-toolbox'] 		= infotoolbox;
    	kupu.maintoolboxes['xedit-ximdexlogger-toolbox'] 	= kupu.log;


    // XIMLETS

    // Ximlet Tool
    var ximlettool = new ximletTool();
    kupu.registerTool('ximlettool', ximlettool);

    // Ximlet Drawer
    var ximletdrawer = new ximletDrawer('kupu-ximletdrawer', ximlettool);
    ximdocdrawertool.registerDrawer('ximletdrawer', ximletdrawer);

    // Ximlet Drawer Button
    var ximletdrawerbutton = new KupuButton('kupu-ximletdrawer-button',
                                          openximdocdrawer('ximletdrawer'));
    kupu.registerTool('ximletdrawerbutton', ximletdrawerbutton);

	var ximlinkdrawer = new XimlinkDrawer('kupu-ximlinkdrawer', attributestool);
	ximdocdrawertool.registerDrawer('ximlinkdrawer', ximlinkdrawer);

	var tabledrawer = new TableDrawer('kupu-tabledrawer', attributestool);
	ximdocdrawertool.registerDrawer('tabledrawer', tabledrawer);

    // Disabling ximlet drawer button
    var button = getFromSelector('kupu-ximletdrawer-button');
	KupuButtonDisable(button);


    //Navbar Tools
    var navbartagtool = new NavBarTool({});
	kupu.registerTool('navbartagtool',navbartagtool);

	var navbartagtoolbox = new NavBarToolBox({});
	navbartagtool.registerToolBox("navbartagtoolbox",navbartagtoolbox);    

    // making the prepareForm method get called on form submit
    // some bug in IE makes it crash on saving the form when a lib drawer was added to the page at some point, remove it on form submit
    var savebutton = getFromSelector('kupu-save-button');
	addEventHandler(savebutton, 'click', prepareForm, kupu);

    // making the prepareForm method get called on publicated button clicked with extra publication options
    var publicatebutton = getFromSelector('kupu-publicate-button');
	addEventHandler(publicatebutton, 'click', function () {prepareForm(false, true);}, kupu);

    // Button: Open Xedit on new window
    var newwindowbutton = getFromSelector('kupu-newwindow-button');
	addEventHandler(newwindowbutton, 'click', function () {
		if (kupu.config.confirm_on_new_window == 1) {
			kupu.confirm(_('You have applied to open the editor in a new window. Changes not saved will be lost. Do you want to continue?'), {
				'yes': function() {
					window.open(document.URL);
				},
				'no': function() {
				}
			});
		} else {
			window.open(document.URL);
		}
	}, kupu);

	// making the prepareForm method get called every minute for saving content
	// on ximdex temp directory
	if (kupu.config.autosave_time > 0) {
		var xTimer = XimTimer.getInstance();
		xTimer.addObserver(function () {prepareForm(true);}, kupu.config.autosave_time);
		xTimer.start();
	}

    // registering some cleanup filter
    // removing tags that aren't in the XHTML DTD
    var nonxhtmltagfilter = new NonXHTMLTagFilter();
    kupu.registerFilter(nonxhtmltagfilter);

//    if (window.kuputoolcollapser) {
//        var collapser = new window.kuputoolcollapser.Collapser('kupu-toolboxes');
//        collapser.initialize();
//    };


	/*for (var i=0, l=window.kupuToolHandlers.length; i<l; i++) {
		window.kupuToolHandlers[i].handler(window.kupuToolHandlers[i]);
	}*/

	$('#kupu-toolboxes').unbind().remove();

	try {
		var tagslist = $('.xim-tagsinput-container');
		$(tagslist).tagsinput();
	}catch(e) {
		//ximTAGS module needed
	}

    return kupu;
};

function continueStartKupu(kupu) {

    if (kupu.getXimDocument().expertModeIsAllowed()) {
	    var schemavalidatorbutton = kupu.getTool('schemavalidatorbutton');
    	schemavalidatorbutton.setSchemaValidator(!kupu.config.expert_mode_active);
    } else {
    	kupu.setSchemaValidator(true);
   	}

   	if(!kupu.getXimDocument().publicationIsAllowed())
	   	KupuButtonDisable(getFromSelector('kupu-publicate-button'));

   	if(!kupu.getXimDocument().checkSpellingIsAllowed())
	   	KupuButtonDisable(getFromSelector('kupu-spellchecker-button'));

	/*
	Validate the document on init
	kupu.getXimDocument().validateXML(function(valid, msg) {
    		if (!valid) kupu.alert(msg);
    });
    */

	loadingImage.hideLoadingImage();

    return kupu;
};


