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

angular.module('ximdex.common.service')//Abstraction for server communications. TODO: Expose to client a REST like interface
    .factory('xTranslate', ['$window', function($window) {
        return function(input){
        	path = input.split('.');
			dictionary = $window.X.i18nStrings[$window.document.documentElement.lang] || $window.X.i18nStrings['en'];
			try {
				for (var i = 0, len = path.length; i < len; i++) {
					node = path[i];
					dictionary = dictionary[node];
				}
				if (typeof dictionary === "string") {
					return dictionary;
				} else {
					return input;
				}
			} catch (error) {
				return input;
			}
		        }
    }]);