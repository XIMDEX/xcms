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

X.actionLoaded(function(event, fn, params) {
		fn("select[name='channellist']").change(function(){
			var str_channel = "&channel="+fn(this).val();
			var container = fn(this).closest('fieldset');
			var nodeid = fn('input[name=node_id]', container).val();
			var link=fn('a', container).attr('href');
			document.getElementById('preview'+nodeid).src = link+str_channel;
		});

		fn('#prevdoc-button').click(function(event) {
			event.preventDefault();
			var link = fn(this).attr('href');
			var container = fn(this).closest('fieldset');
			var nodeid = fn('input[name=node_id]', container).val();
			var selected_channel = fn('#channellist'+nodeid).val();
			var str_channel = "&channel="+selected_channel;
			window.open(link+str_channel);
		});
});
