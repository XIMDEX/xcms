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
ximdexInstallerApp.controller('InstallDatabaseController', ["$timeout", '$scope', 'installerService', "$q", "$window",
 function($timeout, $scope, installerService, $q, $window) {

    $scope.error=false;
    $scope.submit = false;
    $scope.root_user="root";
    $scope.name="ximdex";
    $scope.installed=false;
    $scope.overwrite = false;
    
    installerService.sendAction("checkHost").then(function(response) {
        if (response.data.success){
            $scope.host=response.data.host;
            $scope.port=response.data.port;
            $scope.hostCheck = true;
        }else{
            $scope.hostCheck = 'host';
        }

    });

    $scope.processForm = function(){
        $scope.genericErrors="";
        $scope.dbErrors="";
        $scope.loading = true;
        var index = 0;
        $scope.checkRootUser();
    };

    $scope.checkRootUser = function(){
        
        var params = "user="+$scope.root_user;
        params += "&pass="+$scope.root_pass;
        params += "&host="+$scope.host;
        params += "&port="+$scope.port;
        installerService.sendAction("checkUser",params).then(function(response) {
        if (response.data.success){
            $scope.checkExistDataBase();

        }else{
            $scope.hostCheck = "root_user";
            $scope.loading = false;
            $scope.genericErrors = response.data.errors;
        }

    });
    };

    $scope.checkExistDataBase = function(){
        var params = "user="+$scope.root_user;
        params += "&pass="+$scope.root_pass;
        params += "&host="+$scope.host;
        params += "&port="+$scope.port;
        params += "&name="+$scope.name;
        installerService.sendAction("checkExistDataBase",params).then(function(response) {        
        if (response.data.success){
            $scope.installDataBase();            
        }else{
            $scope.dbErrors = $scope.name+" database already exists. Overwrite it?";
            $scope.hostCheck = "exist_db";
            $scope.overwrite = true;
            $scope.loading=false;
        }
     });
    };

    $scope.installDataBase = function(){
        if ($scope.overwrite)
            $scope.loadingOverwrite = true;
        var params = "user="+$scope.root_user;
        params += "&pass="+$scope.root_pass;
        params += "&host="+$scope.host;
        params += "&port="+$scope.port;
        params += "&name="+$scope.name;
        installerService.sendAction("createDataBase",params).then(function(response) {
        if ($scope.overwrite)
            $scope.loadingOverwrite = false;
        else
            $scope.loading=false;
        if (response.data.success){
            $scope.installed = true;
        }else{
            $scope.error = response.data.errors;
        }
    });
   
    }
    $scope.$watch("name", function(){
        $scope.overwrite = false;
        $scope.dbErrors = false;
    })
    $scope.addUser = function(){
        $scope.loadingAddUser = true;
        var params = "user="+$scope.user;
        params += "&pass="+$scope.pass;
        params += "&host="+$scope.host;
        params += "&port="+$scope.port;
        params += "&name="+$scope.name;
        params += "&root_user="+$scope.root_user;
        params += "&root_pass="+$scope.root_pass;
        installerService.sendAction("addUser",params).then(function(response) {
            $scope.loadingAddUser = false;
            location.reload();
    });
   
    }
   
}]);