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

<form method="post" ng-controller="InstallModulesController" name="installModulesForm" ng-submit="processForm()" ng-cloak>
    <input type="hidden" name="method" value="<?php echo $goMethod ?>">
	<h2>Installing Ximdex CMS's default modules</h2>
	<p ng-hide="loaded">Preparing default modules ...<span>{{preLoading}}</span></p>
	<p class="error" ng-show="error">{{message}}</p>
	<ul ng-hide="error">
		<li class="module-{{module.state}} " ng-repeat="module in modules">
	      <p class="title  {{module.name}} "> {{module.alias}} <span ng-show="module.processed">{{module.state}}</span></p> 
	      <p class="desc">{{module.description}}</p>
	    </li>		
	</ul>
	<button ng-hide="!loaded" ui-ladda="loading" class="launch_ximdex action_launcher ladda-button" data-style="slide-up" id="submitButton" xim-state="loading"><span class="ladda-label">Install modules</span></button>
</form>
