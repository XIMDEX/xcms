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

<form method="post"  ng-controller="InstallDatabaseController" ng-cloak name="formDataBase" ui-keypress="{13:'sendForm($event)'}" xim-install-instance-name="<?php echo $ximdexName; ?>">
    <input type="hidden" name="method" value="<?php echo $goMethod ?>">    
	<h2>Installing Database</h2>
	<div ng-show="!installed">
	<p>Now it's time to install a MySQL or MariaDB database. A priviledge user (e.g. root) is required to create this database.</p>
	<p class="errors" ng-show="genericErrors">{{genericErrors}}</p>

	<div class="form_item"  >
		<label  for="db_server">Host</label>
		<input ng-model="host" type="text" name="host" id="db_server" value="{{host}}" required ng-class="{success:hostCheck==true, error_element:hostCheck == 'host'}" />
	</div>
	<div class="form_item" >
		<label for="db_port" >Port</label>
		<input ng-model="port" ng-class="{success:hostCheck==true, error_element:hostCheck == 'host'}" type="text" name="port" id="db_port" value="{{port}}" />
	</div>

	<div class="form_item" >
		<label for="db_admin">Database Admin User</label>
		<input ng-model="root_user" type="text" name="root_user" id="db_admin" value="root" required ng-class="{success:hostCheck==true, error_element:hostCheck == 'root_user'}"/>
	</div>

	<div class="form_item" >
		<label for="db_pass">Database Admin Password</label>
		<input ng-model="root_pass" type="password" name="root_pass" id="db_pass" placeholder="Insert your admin user password here" ng-class="{success:hostCheck==true, error_element:hostCheck == 'root_user'}" />
		<p class="errors" ng-show="loginErrors">{{loginErrors}}</p>
	</div>

	<div class="form_item  form_item--dbname full-width " >
		<label for="db_name">Database name</label>
		<input ng-model="name" required type="text" name="name" id="db_name" ng-class="{success:hostCheck==true, error_element:hostCheck == 'exist_db'}"  ng-pattern="/^\w+$/" />
		<p class=" warning error--inline overwrite" ng-show="dbErrors">{{dbErrors}}</p>
	</div>	

	<button ng-hide="dbErrors" class="launch_ximdex action_launcher ladda-button"  ui-ladda="loading" data-style="slide-up" xim-state="loading" ng-click="sendForm()">Create Database</button>
        
	
	<button ng-show="dbErrors" class="launch_ximdex action_launcher ladda-button"   ui-ladda="loadingOverwrite" data-style="slide-up" xim-state="loadingOverwrite" ng-click="sendForm()">Overwrite database</button>
	</div>
	<div ng-show="installed">
	<p>Once the database is created, is a good recommended security practice to use an <em>unpriviledge user</em> to access to it. Set here the name and password for this user.</p>
	<div class="form_item">
		<label for="db_user">Database user</label>
		<input ng-model="user" type="text" name="user" id="db_user" value="ximdex_user " />
	</div>
	<div class="form_item">
		<label for="db_user_pass">Database password</label>
		<input ng-model="pass" type="password" name="pass" id="db_user_pass" placeholder="Insert your user password here" />
	</div>
	
	<button class="launch_ximdex action_launcher ladda-button"  ui-ladda="loadingAddUser" data-style="slide-up" xim-state="loadingAddUser" ng-click="sendForm()">Add user</button>
	</div>
</form>
