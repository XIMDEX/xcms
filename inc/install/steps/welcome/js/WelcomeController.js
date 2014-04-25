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
ximdexInstallerApp.controller('WelcomeController', ["$timeout", '$scope', 'installerService', "$q", "$window",
 function($timeout, $scope, installerService, $q, $window) {

    $scope.cosa="";
    $scope.submit = false;
    $scope.root_user="root";
    $scope.name="ximdex";

    installerService.sendAction("checkHost").then(function(response) {
        if (response.data.success){
            $scope.host=response.data.host;
            $scope.port=response.data.port;
            $scope.hostCheck = false;
        }

    });

    $scope.processForm = function(){
        $scope.loading = true;
        var index = 0;
        $scope.checkRootUser();
    };

    $scope.checkRootUser = function(){

        var params = "root_user="+$scope.root_user;
        params += "&root_pass="+$scope.root_pass;
        params += "&host="+$scope.host;
        params += "&port="+$scope.port;
        installerService.sendAction("checkUser",params).then(function(response) {
        if (response.data.success){

        }

    });
    };

    $scope.installDataBase = function(){

    }

}]);