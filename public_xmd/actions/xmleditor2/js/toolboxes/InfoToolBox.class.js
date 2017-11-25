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
 *  @version $Revision: 7876 $
 */

var InfoToolBox = Object.xo_create(FloatingToolBox, {

	initialize: function(tool, editor) {
		InfoToolBox._super(this, 'initialize', tool, editor);
		this.setTitle(_('Information'));
		this.getInfo();
		setInterval(function() {
			this.getInfo();
		}.bind(this), 60000);
	},

	_setInformation: function(_label, _value)  {
		this._addHtml( "<div><strong>"+_label+"</strong>: "+_value+"</div>");
	},

	getInfo: function() {
		this._insertHtml(_("Updating data")+"...");

		var url = this.editor._baseURL+"&method=getInfo";

		new AjaxRequest(url, {
			method: 'GET',
			onComplete: function(req, json) {
				if(null != json) {
					this.setInfo(json);
				}
			}.bind(this),

			onError: function(req) {

			}.bind(this)
		});
	},

	setInfo: function(data) {
		this._insertHtml("");
		this._setInformation(_("NodeId"), this.editor.nodeId);
		this._setInformation(_("Name"), data.name);
		this._setInformation(_("Path"),data.path) ;
		this._setInformation(_("Last version"),data.version+"."+data.subversion);
		/** Last modified date" */
		var info_date = new Date(data.date*1000)
		this._setInformation(_("Last modified"),info_date);
		this._setInformation(_("Last modified by"),data.lastusername);
		var state = _("Published");
		if(0 == data.published) {
			state = _("Not published");
		}
		if(2 == data.published){
			state = _("Published (*)");
		}
		this._setInformation(_("State"),state);

	}
});
