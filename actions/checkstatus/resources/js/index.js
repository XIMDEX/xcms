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
 if (angular.module('ximdex').notRegistred('XPublishStatus')){
    angular.module('ximdex')
        .controllerProvider.register('XPublishStatus', ['$scope', '$attrs', 'xBackend', '$timeout', '$http', 'xUrlHelper', 'xTranslate', function($scope, $attrs, xBackend, $timeout, $http, xUrlHelper, xTranslate){
            $scope.publications = {};
            $scope.publications.published = ['hola'];
            $scope.publications.unpublished = ['adios'];
            $scope.translate = xTranslate;
            $scope.backend = xBackend.subscribe({id: $attrs.ximNodeid, action:'checkstatus', method:'getPublicationQueue'}, function(data){
            	if (data && data.publications) {
            		$scope.publications.published = []
            		$scope.publications.unpublished = []
            		angular.element.each(data.publications, function(key, pub){
            			if (pub.state == 'In' || pub.state == 'Out') {
            				$scope.publications.published.push(pub);
            			} else {
            				$scope.publications.unpublished.push(pub);
            			}
            		});
            	}
            });
            $scope.$on('$destroy', function(){
            	if ($scope.backend) {
            		$scope.backend.unsubscribe();
            	}
            });
        }]);
    angular.module('ximdex').registerItem('XPublishStatus');
}


X.actionLoaded(function(event, fn, params) {
	X.angularTools.initView(params.context, params.tabId);

	fn('.state-info .documents-info').addClass("hide-toggle");

        fn('.state-info').click(function() {
                $(this).toggleClass("opened");
                $(this).children(".documents-info").toggleClass("hide-toggle");

        });
        fn('.state-info .documents-info').click(function(e){
			e.stopPropagation();
        });
});
