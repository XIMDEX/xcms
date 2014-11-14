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
ximdexInstallerApp.controller('SettingController', ["$timeout", '$scope', 'installerService', "$q", "$window",
 function($timeout, $scope, installerService, $q, $window) {

    $scope.languages = [{"iso": "en_US",
                         "name": "English"},
                        {"iso": "es_ES",
                         "name": "Spanish"},                         
                         {"iso": "de_DE",
                         "name": "German"},
                         {"iso": "pt_BR",
                         "name": "Portuguese"}];

    $scope.language = "en_US";
    $scope.anonymous_information = "1";
    $scope.minLengthMessage = "Password length minimun: 6";
    $scope.minLenghtFail = false;
    $scope.localhash=false;

    $scope.init = function(){
        installerService.sendAction("setId").then(function(response) {
            $scope.localhash=response.data.localhash;
        });
    }

    $scope.init();

    $scope.checkForm = function(){
        var params = "pass="+$scope.pass;
        params += "&language="+$scope.language;
        params += "&anonymous_information="+$scope.anonymous_information;
        if ($scope.settingForm.$valid){
            installerService.sendAction("initializeSettings", params).then(function(response) {
                    if (response.data.success){
                        location.reload();
                    }
            });
        }else{
            $scope.minLenghtFail = true;
        }

    }
   
}]);