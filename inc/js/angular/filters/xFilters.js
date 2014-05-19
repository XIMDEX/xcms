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

//Format 
angular.module('ximdex.common.filter')
    .filter('xBytes', function(){
        return function(bytes){
            if (isNaN(parseFloat(bytes)) || !isFinite(bytes))
                return ''
            if (parseFloat(bytes) == 0)
            	return '0 bytes'
            var units = ['bytes', 'kB', 'MB', 'GB', 'TB', 'PB'];
            var number = Math.floor(Math.log(bytes) / Math.log(1024));
            var size = (bytes / Math.pow(1024, Math.floor(number))).toFixed(2);
            var unit =  units[number];
            return size+' '+unit;
        }
});

//Translate
angular.module('ximdex.common.filter')
    .filter('xI18n', ['xTranslate', function(xTranslate){
        return function(string){
            return xTranslate(string);
        }
}]);

angular.module('ximdex.common.filter')
    .filter('xNormalize', ['xTranslate', function(xTranslate){
        return function(string){
            //Basic normalization
            return string.replace(' ', '_');
        }
}]);
