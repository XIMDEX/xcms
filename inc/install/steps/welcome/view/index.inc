<?php
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
?>

<form method="post" ng-controller="WelcomeController" ng-cloak>
    <h2>Welcome to the Ximdex CMS's installation process!</h2>
    <p>The semantic Ximdex CMS and its modules -Xowl, Xlyre, Xnews,...- help you to build semantic, secures and structured sites.</p>

    <p ng-repeat-start="(index, error) in errors" ng-class="error.state">{{error.message}}</p>
    <p ng-repeat-end class="desc">{{error.help}}</p>
    <br/>
    <button class="launch_ximdex action_launcher" ng-click="checkInstaller()" ng-hide="checked">Check configuration</button>
    <button class="launch_ximdex action_launcher" ng-click="nextStep()" ng-show="checked">Start installation</button>
</form>
