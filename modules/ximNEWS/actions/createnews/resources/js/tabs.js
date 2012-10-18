/**
 *  \details &copy; 2011  Open Ximdex Evolution SL [http://www.ximdex.org]
 *
 *  Ximdex a Semantic Content Management System (CMS)
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as published
 *  by the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  See the Affero GNU General Public License for more details.
 *  You should have received a copy of the Affero GNU General Public License
 *  version 3 along with Ximdex (see LICENSE file).
 *
 *  If not, visit http://gnu.org/licenses/agpl-3.0.html.
 *
 *  @author Ximdex DevTeam <dev@ximdex.com>
 *  @version $Revision$
 */


X.actionLoaded(function(event, fn, params){

	var tabsContainer = fn('.languages-news').closest('fieldset');
	var contextWidth = $(tabsContainer).width();
	fn('.languages-news').width(contextWidth);


    fn(".datepickerhandler").datepicker({
        dateFormat: 'dd/mm/yy',
        changeYear: true,
        changeMonth: true
    });

    //Put the datepicker over the tabs
    fn(".ui-datepicker").css({
        zIndex:101
    });


    // getting links

    $.getJSON(X.restUrl + '?action=createnews&mod=ximNEWS&method=get_links', function(data) {

        fn('.linksuggest').autocomplete({
            source: data
        }).bind('autocompleteselect', function(event, ui) {
            fn('input:hidden[name="a_enlaceid_noticia_enlace_asociado"]').val(ui['item'].id);
        });
    });


    // getting images

    var loteid = fn('#lote').val();

    $.getJSON(X.restUrl + '?action=createnews&mod=ximNEWS&method=get_images&lote=' + loteid, function(data) {

        fn('.imagesuggest').autocomplete({
            source: data
        }).bind('autocompleteselect', function(event, ui) {
            fn('input:hidden[name="a_enlaceid_noticia_imagen_asociada"]').val(ui['item'].id);
        });
    });

    tab_handler();

    function tab_handler() {


        var load_url = X.restUrl + '?action=' + params.action.command+ '&nodeid=' + params.nodes[0]
        + '&mod=ximNEWS&method=load_language_tab&idtemplate=' + $('#id_template').val();
        var languages = fn('input:hidden[name^=langs_lst]');


        $('<div><ul class="ul"></ul></div>').appendTo(fn('.language-tabs'));
        fn('.language-tabs').tabs({
            tabTemplate: '<li><div class="ui-tab-close"></div><a href="#{href}"><span>#{label}</span></a></li>',
            panelTemplate: '<div class="createnews-tab" style="top:0px;position:relative;"></div>',
				spinner: _('Loading...'),
            cache: true,
            select: function(event, ui) {
                $('.destroy-on-click').unbind().remove();
            }
        });

        var tabId = 0;
        var langs = [];

        $(languages).each(function(id, lang) {
            langs.push({
                id: $(lang).attr('id'),
                text: $(lang).val()
            });
        });

        function addTab(lang) {
            fn('.language-tabs')
            	.one('tabsload', function(event, ui) {
            		$(ui.panel).labelWidth({
            			fieldset: 'ol'
            		});
	                if (langs.length > 0) {
	                    var lang = langs.shift();
	                    addTab(lang);
	                }
	            })
            	.tabs('add', load_url + '&idlang=' + lang.id, lang.text, tabId++)
            	.tabs('adjustTabClasses');
        }

        addTab(langs.shift());
    }

});
