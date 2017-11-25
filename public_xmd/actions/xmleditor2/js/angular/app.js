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

angular.module('ximdex', ['ximdex.common', 'ximdex.main', 'ximdex.widget', 'ximdex.module',
    'ximdex.vendor']);

angular.module('ximdex.vendor', ['hmTouchEvents','ui.bootstrap','angularLoad', 'react']);

angular.module('ximdex.module', ['ximdex.module.xtags']);

angular.module('ximdex.common', ['ximdex.common.service', 'ximdex.common.directive', 'ximdex.common.filter']);
angular.module('ximdex.main', ['ximdex.main.controller']);
angular.module('ximdex.widget', []);

angular.module('ximdex.common.directive', ['ximdex.common.directive.validator']);
angular.module('ximdex.common.directive.validator', []);

angular.module('ximdex.common.service', []);
angular.module('ximdex.common.filter', []);

angular.module('ximdex.main.controller', []);
angular.module('ximdex.module.xtags', []);


//Configure interpolation symbols to work in smarty templates
angular.module('ximdex')
    .config(function($interpolateProvider, $controllerProvider, $compileProvider) {
        $interpolateProvider.startSymbol('#/').endSymbol('/#');
        
});
