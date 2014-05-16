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

angular.module('ximdex.common.service')
    .factory('xMenu', ['$window', '$rootScope', function($window, $rootScope) {
        
    	var createButton = function(options) {
			options = $.extend({
				text: '',
				title: '',
				value: '',
				data: {},
				className: '',
				click: function() {},
				icon: null,
				container: null
			}, options);

			var b = $('<button></button>')
				.addClass('window-toolbar-button '+options.className + ' ' +options.value)
				//.html($('<span></span><span class="tooltip"></span>').html(options.text))
				.val(options.value)
				.data('data', options.data)
//				.attr('title', options.title)
				.click(options.click);

			$(b)
				.append($('<span>Icono</span>'))
				.append($('<span class="tooltip"></span>').html(options.text));

			if (!Object.isEmpty(options.icon)) {

				var icon = options.icon.substr(0, options.icon.length-4);
				b.addClass('icon-'+icon);
			}

			if (options.container !== null) {
				b.appendTo(options.container);
			}
			$(b).wrap('<div class="button-container"/>');
			return b;
		}

    	var createButtonList = function(options) {
    	    options = $.extend({
    	        text: '',
    	        title: '',
    	        value: '',
    	        data: {},
    	        className: '',
    	        click: function() {},
    	        icon: null,
    	        container: null
    	    }, options);


    	    var ndiv = $('<div/>').addClass('button-container-list');
    	    var ndiv = $('<div/>').addClass('button-container-list icon').addClass(options.value).html(options.text);
    	    ndiv.data('data',options.data).click(options.click);

    	    if (options.container !== null) {
    	        ndiv.appendTo(options.container);
    	    }

    	    return ndiv;
    	}
    	var createFloatMenu = function(params) {
    	    var nodeid = params.data.nodeid.value;
    	    var cmenu = $('div.xim-actions-menu').unbind().empty();
    	    var show = true;

    	    if (cmenu.length == 0) {

    	        cmenu = $('<div></div>')
    	            .addClass('xim-actions-menu destroy-on-click')
    	            .attr('id', 'cmenu-'+nodeid);

    	    } else if ($(cmenu).attr('id') == 'cmenu-'+nodeid) {
    	        show = false;
    	    }

    	    show = show & (params.actions.length > 0);

    	    if (!show) {
    	        $(cmenu).unbind().remove();
    	        return;
    	    }

    	    if (Object.isFunction(params.actions.each)) {

    	        params.actions.each(function(index, item) {
    	            item.params = item.params.replace('%3D', '=');
    	            createButton({
    	                text: item.name,
    	                title: item.name,
    	                value: item.command,
    	                data: {
    	                    action: item,
    	                    nodes: params.nodes,
    	                    ids: params.ids
    	                },
    	                className: 'view-action',
    	                icon: item.icon,
    	                click: function(e) {
    	                    var data = $(e.currentTarget).data('data');
    	                    params.result(data.action, params.ids);
    	                }.bind(this),
    	                container: cmenu
    	            });
    	        });

    	        cmenu
    	            .css({
    	                position: 'absolute',
    	                left: params.menuPos.x,
    	                top: params.menuPos.y
    	            })
    	            .appendTo('body');

    	    }
    	}
    	var createFloatMenuList = function(params) {
			var nodeid = params.data.nodeid.value;
			var cmenu = $('div.xim-actions-menu').unbind().empty();
			var show = true;

			if (cmenu.length == 0) {

				cmenu = $('<div></div>')
					.addClass('xim-actions-menu xim-actions-menu-list destroy-on-click')
					.attr('id', 'cmenu-'+nodeid);

			} else if ($(cmenu).attr('id') == 'cmenu-'+nodeid) {
				show = false;
			}

			show = show & (params.actions.length > 0);

			if (!show) {
				$(cmenu).unbind().remove();
				return;
			}

			if (Object.isFunction(params.actions.each)) {

				params.actions.each(function(index, item) {
					item.params = item.params.replace('%3D', '=');
					createButtonList({
						text: item.name,
						title: item.name,
						value: item.command,
						data: {
							action: item,
							nodes: params.nodes,
							ids: params.ids
						},
						className: 'view-action',
						icon: item.icon,
						click: function(e) {
							var data = $(e.currentTarget).data('data');
							params.result(data.action, params.ids);
						}.bind(this),
						container: cmenu
					});
				});




				cmenu
					.css({
						position: 'absolute',
						left: params.menuPos.x,
						top: params.menuPos.y
					})
					.appendTo('body');

				//Detect End Page Collision
				var windowY = window.innerHeight;
				var menuY = $(cmenu).height();
				var finY  = menuY + params.menuPos.y;

				if(finY > windowY){
					params.menuPos.y = windowY - menuY - 20;
					$(cmenu).css({
						top: params.menuPos.y
					});
				}

			}
		}
        return {
            legacyOpen: function(params) {
            	if (params.inline) {
            		createFloatMenu(params);
            	} else {
            		createFloatMenuList(params);
            	}
            }
        }
    }]);