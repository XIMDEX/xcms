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

		//console.info(fn('.prevdoc-button'));

		fn('.prevdoc-button').click(function(event) {
			event.preventDefault();
			var link = fn(this).attr('href');

			//¿prevdoc or filepreview?
			if(link.indexOf("prevdoc") != -1)
				var link_params = link.split('prevdoc&');
			else
				var link_params = link.split('filepreview&');

			var container_button = fn(this).closest('tr');

			//get nodeid
			var nodeid = fn('input[name=node_id]', container_button).val();
			var str_node = "&nodeid="+nodeid;

			//get channel
			if(-1 == link.indexOf("channel") ) {
				var selected_channel = fn('.prevdoc [name="channellist"]').val();
			}else {
				var selected_channel = 1;
			}

			var str_channel = "&channel="+selected_channel;

			//join all
			var query_string = str_channel+str_node;

			//¿in new window?
			var newwindow = fn('input:checkbox:checked', container_button).val();
			if("on" == newwindow) {
			 	window.open(link+query_string);
				return ;
			}

			//open action
			//console.log(link_params);
			var action = {
					bulk: 0,
					command: 'prevdoc',
				name: 'Previo',
				params: link_params[1]+query_string
			};
			$(params.browser).browserwindow('openAction', action, [nodeid]);
		})

});
