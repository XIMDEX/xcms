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

// constructor method
var query_manager = function(url) {
	this.params = new Object();
	this.url = url;
	if (url == null || url == 'undefined') {
		return false;
	}
	arr_query_string = this.url.split('?');
	var search = null;
	if (arr_query_string.length == 2) {
		search = arr_query_string[1];
	}
	if (search) {
		search_elements = search.split("&");
		for(var i=0; i < search_elements.length; i++) {
			splitted_element = search_elements[i].split('=');
			if (splitted_element.length == 2) {
				this.params[splitted_element[0]] = splitted_element[1];
			}
		}
	}
}

// aditional methods
query_manager.prototype = {
		params: null,
		url: null,
		get_value: function(needle) {
			for (key in this.params) {
				if (key == needle) {
					return (this.params[key]);
				}
			}
			return false;
		}

}
