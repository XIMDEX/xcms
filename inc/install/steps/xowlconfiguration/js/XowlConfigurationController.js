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
ximdexInstallerApp.controller('XowlConfigurationController', ['$scope', 'installerService',
    function ($scope, installerService) {
        $scope.serviceurl = "";
        $scope.apikey = "";
        $scope.error = false;
        $scope.success = false;
        $scope.processForm = function () {
            $scope.loading = true;
            if ($scope.serviceurl == "" && $scope.apikey == "") {


                installerService.sendAction("loadNextAction").then(function (response) {
                    location.reload();
                });
            } else {
                installerService
                    .sendAction("configure", "apikey=" + $scope.apikey + "&serviceurl=" + $scope.serviceurl)
                    .then(function (response) {
                        $scope.error = false;
                        $scope.success = false;
                        if (response.data.error == 1) {
                            $scope.error = true;
                            $scope.message = response.data.message;
                            $scope.loading = false;
                        }else{
                            $scope.success = true;
                            $scope.message = response.data.message;
                            installerService.sendAction("loadNextAction").then(function (response) {
                                location.reload();
                            });
                        }
                    });

            }
        };

    }]);