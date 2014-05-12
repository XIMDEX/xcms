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

angular.module('ximdex', ['ximdex.common', 'ximdex.main', 'ximdex.widget', 'ximdex.module', 'ximdex.vendor']);

angular.module('ximdex.vendor', ['ngAnimate', 'blueimp.fileupload', 'flow']);
angular.module('ximdex.module', ['ximdex.module.xlyre', 'ximdex.module.xtags']);

angular.module('ximdex.common', ['ximdex.common.service', 'ximdex.common.directive', 'ximdex.common.filter']);
angular.module('ximdex.main', ['ximdex.main.controller']);
angular.module('ximdex.widget', []);

angular.module('ximdex.common.directive', ['ximdex.common.directive.validator']);
angular.module('ximdex.common.directive.validator', []);

angular.module('ximdex.common.service', []);
angular.module('ximdex.common.filter', []);

angular.module('ximdex.main.controller', []);

angular.module('ximdex.module.xlyre', []);
angular.module('ximdex.module.xtags', []);

//Configure interpolation symbols to work in smarty templates
angular.module('ximdex')
    .config(function($interpolateProvider, $controllerProvider, $compileProvider) {
        $interpolateProvider.startSymbol('#/').endSymbol('/#');
        
        //Store providers in module for controller directive resgistration after bootstraping
        angular.module('ximdex').controllerProvider = $controllerProvider;
        angular.module('ximdex').compileProvider = $compileProvider;

        //Keep track of registered controllers and directives
        var registeredItems = [];
        angular.module('ximdex').registerItem = function(item) {
        	registeredItems.push(item);
        }
        angular.module('ximdex').notRegistred = function(item) {
        	if (registeredItems.indexOf(item) >= 0) {
        		return false
        	} else {
        		return true
        	}
        }
});

//Hacks to deal with actual mixed enviroment

(function(X) {

	X.angularTools = {
		//Initialize compile on a view and manage scope destruction
		initView: function (view, id){
			var $injector = angular.element(document).injector();
			var scope = null;
			$injector.invoke(function($compile, $rootScope) {
			    var destroy = function(event, viewId){
			        if (id == viewId) {
			            scope.$destroy();
			            $(document).off("closeTab.angular", destroy);
			        }
			    };
			    scope = $rootScope.$new();
			    $compile(view[0])(scope);
			    scope.$digest();
			    $(document).on("closeTab.angular", destroy);
			});
			return scope;
		}
	};

})(com.ximdex);