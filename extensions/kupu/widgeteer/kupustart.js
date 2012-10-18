/*****************************************************************************
 *
 * Copyright (c) 2003-2005 Kupu Contributors. All rights reserved.
 *
 * This software is distributed under the terms of the Kupu
 * License. See LICENSE.txt for license text. For a list of Kupu
 * Contributors see CREDITS.txt.
 *
 *****************************************************************************/

// $Id$

function startKupu() {
    // first let's load the message catalog
    // if there's no global 'i18n_message_catalog' variable available, don't
    // try to load any translations
    if (window.i18n_message_catalog) {
        var request = new XMLHttpRequest();
        // sync request, scary...
        request.open('GET', 'kupu-pox.cgi', false);
        request.send('');
        if (request.status != '200') {
            alert('Error loading translation (status ' + status +
                    '), falling back to english');
        } else {
            // load successful, continue
            var dom = request.responseXML;
            window.i18n_message_catalog.initialize(dom);
        };
    };
    
    // initialize the editor, initKupu groks 1 arg, a reference to the iframe
    var frame = getFromSelector('kupu-editor'); 
    var kupu = initKupu(frame);
    
    // and now we can initialize...
    kupu.initialize();

    return kupu;
};
