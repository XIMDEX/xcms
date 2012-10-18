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

var XimMenuHooks = {

	execute: function(hookName, params) {
		var method = XimMenuHooks['hook_' + hookName];
		if (typeof(method) == 'function') {
//			console.info(params);
			method(params);
		}
	},

	hook_mnuMsg: function(params) {
		$('#tabs').tabs('add', window.url_root + '/mesg/index.php', params.data.text);
	},

	hook_mnuUserData: function(params) {
		$('#tabs').tabs('add', window.url_root + '/xmd/chkpass.php', params.data.text);
	},

	hook_mnuQuit: function(params) {
		location.href = window.url_root+'/xmd/loadaction=logout';
	},

	hook_mnuShowTree: function(params) {
		$('#tree').toggle();
	},

	hook_mnuShowStatusbar: function(params) {
		$('#footer').toggle();
	},

	hook_mnuTotalActions: function(params) {
		var url = window.url_root + '/xmd/loadaction.php?nodeid=NULL&action=actionsstats&method=index';
		$('#tabs').tabs('add', url, params.data.text);
	},

	hook_mnuTempAvarage: function(params) {
		var url = window.url_root + '/xmd/loadaction.php?nodeid=NULL&action=actionsstats&method=avarage';
		$('#tabs').tabs('add', url, params.data.text);
	},

	hook_mnuView1: function(params) {
		var url = window.url_root + '/xmd/loadaction.php?actionid=7300&nodeid=NULL&method=batchlist';
		$('#tabs').tabs('add', url, params.data.text);
	},

	hook_mnuView2: function(params) {
		var url = window.url_root + '/modules/ximSYNC/actions/managebatchs/init.php';
		$('#tabs').tabs('add', url, params.data.text);
	},

	hook_mnuShowLogs: function(params) {
		var url = window.url_root + '/xmd/logger/logger.php';
		$('#tabs').tabs('add', url, params.data.text);
	},

	hook_mnuActionsInWindows: function(params) {
	},

	hook_mnuSmartyConsole: function(params) {
	},

	hook_mnuNodeFilter: function(params) {
	},

	hook_mnuGrep: function(params) {
	},

	hook_mnuCloseTabs: function(params) {
	},

	hook_mnuActiveModules: function(params) {
	},

	hook_mnuNodeInfo: function(params) {
	},

	hook_mnuPHPInfo: function(params) {
	},

	hook_mnuShowNodesId: function(params) {
	},

	hook_mnuRenders: function(params) {
	},

	hook_mnuSmartyRender: function(params) {
	},

	hook_mnuJSONRender: function(params) {
	},

	hook_mnuDebugRender: function(params) {
	},

	hook_mnuSpanishLang: function(params) {
        if(confirm("Es necesario reiniciar su ximdex. ¿Está seguro que quiere proceder?")) {
        	window.location = 'menu/change_lang.php?lang=es';
        }
	},

	hook_mnuEnglishLang: function(params) {
        if(confirm("Es necesario reiniciar su ximdex. ¿Está seguro que quiere proceder?")) {
        	window.location = 'menu/change_lang.php?lang=en';
        }
	},

	hook_mnuDeutchLang: function(params) {
        if(confirm("Es necesario reiniciar su ximdex. ¿Está seguro que quiere proceder?")) {
        	window.location = 'menu/change_lang.php?lang=de';
        }
	},

	hook_mnuXimdexManual: function(params) {
		$('#tabs').tabs('add', window.url_root + '/help/index.html', params.data.text);
	},

	hook_mnuAbout: function(params) {
		// Ximdex Version must be a variable
		alert('ximDEX 2.5 (c) Open Ximdex Evolution SL 2006');
	},

	hook_mnuXimdexClose: function(params) {
		var url = window.url_root + '/xmd/loadaction.php?action=ximdexclose&method=index';
		$('#tabs').tabs('add', url, params.data.text.value);
	},
        hook_mnuManageThemes: function(params) {
                var url = window.url_root + '/xmd/loadaction.php?action=templatecreator&mod=ximTHEMES';
                $('#tabs').tabs('add', url, 'Definir nuevo layout');
        }

}

function addworkspace(titlebar, url, unclosable) {

}
