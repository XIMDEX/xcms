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

<form method="post"  ng-controller="InstallDatabaseController" ng-cloak  ng-submit="processForm()">
    <input type="hidden" name="method" value="<?php echo $goMethod ?>">
	<h2>Installing DataBase</h2>

	<p class="errors" ng-show="error">{{error}}</p>

	<div class="form_item"  ng-class="{success:hostCheck==true}">
		<label  for="db_server">Host</label>
		<input ng-model="host" type="text" name="host" id="db_server" value="{{host}}" required />
	</div>
	<div class="form_item" ng-hide="host=='localhost'" ng-class="{success:hostCheck==true}">
		<label for="db_port" >Port</label>
		<input ng-model="port"  type="text" name="port" id="db_port" value="{{port}}" required />
	</div>

	<div class="form_item">
		<label for="db_admin">Database Admin User</label>
		<input ng-model="root_user" type="text" name="root_user" id="db_admin" value="root" required/>
	</div>

	<div class="form_item">
		<label for="db_pass">Database Admin Password</label>
		<input ng-model="root_pass" required ng- type="password" name="root_pass" id="db_pass" placeholder="Insert your admin user password here" />
	</div>

	<div class="form_item  form_item--dbname full-width">
		<label for="db_name">Database name</label>
		<input ng-model="name" required type="text" name="name" id="db_name" value="ximdex" />
	</div>

	<button class="launch_ximdex action_launcher ladda-button"  ui-ladda="loading" data-style="slide-up" xim-state="loading">Install database</button>
	
	<div ng-show="installed">
	<p>Who will access to Ximdex DataBase?. Advice you against using root user. </p>
	<div class="form_item">
		<label for="db_user">Database user</label>
		<input ng-model="user" type="text" name="user" id="db_user" value="ximdex_user " />
	</div>
	<div class="form_item">
		<label for="db_user_pass">Database password</label>
		<input ng-model="pass" type="password" name="pass" id="db_user_pass" placeholder="Insert your user password here" />
	</div>
	

	<button class="launch_ximdex action_launcher" onclick="">Add user</button>
	</div>
		
	
</form>