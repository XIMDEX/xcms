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
 *  @version $Revision: 8529 $
 */


(function(X) {

	var B = X.browser;
	B.panels.leftTabs[4] = 'ModulesView';

	var idCount = 0;

	B.ModulesView = Object.xo_create(B.AbstractView, {

		container_modules: null,

		_init: function(options) {

			X.widgetsVars.setWidgetType(this, "modulestab");

			B.ModulesView._construct(this, options);
			this.id = 'browser-modules-view-' + (++idCount);
			this.className = 'browser-modules-view';
			this.label = _("Modules");
			this._tabId = 4;
			this.content = $('<div></div>')
				.addClass('browser-modules-view-content')
				.attr({id: this.id});
				this.setViewTitle(_("Modules"));

			this.container_modules = $('<div></div>').addClass('browser-modules-view-list-container').appendTo(this.content);

			this.loadModulesList();
		},

		loadModulesList: function() {


			var url = '%s?action=%s&method=%s'.printf(X.restUrl, 'moduleslist', 'readModules');

			//Get all modules
			$.ajax({
				type: "GET",
				url: url,
				success:  function(data) {
					this.container_modules.html(data);
					this.assignTabs();
				}.bind(this)
			});


		},

		assignTabs: function() {
			$("li", this.container_modules).each(function(i, module) {
				$(module).click(function() {
						this.openTab(module, $(module).text() );
				}.bind(this) );
			}.bind(this) ); 
		},

		openTab: function(link, module) {
				var lbl_module = _("Module");

				 parent.$('#bw1').browserwindow('openAction', {
				   label: lbl_module+" "+module,
					name: lbl_module+" "+module,
					command: 'moduleslist',
					params: 'method=opentab&modsel='+module,
					nodes: 10000,
					url: X.restUrl + '?action=moduleslist&nodes[]=10000&nodeid=10000&modsel='+module,
					bulk: '10000'
				},10000);
		},

		getter: ['loadModulesList']
	});

})(com.ximdex);
