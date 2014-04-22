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

<form method="post" action=".">
    <input type="hidden" name="method" value="<?php echo $goMethod ?>">
	<h2>Installing DataBase</h2>

	<p>Change and fill the fields below you need to connect to your data base.</p>

	<div class="form_item">
		<label for="db_server">Server</label>
		<input type="text" name="server" id="db_server" value="Localhost" />
	</div>
	<div class="form_item">
		<label for="db_port">Port</label>
		<input type="text" name="port" id="db_port" value="3306" />
	</div>

	<div class="form_item">
		<label for="db_admin">Database Admin User</label>
		<input type="text" name="user" id="db_admin" value="root"/>
	</div>

	<div class="form_item">
		<label for="db_pass">Database Admin Password</label>
		<input type="password" name="pass" id="db_pass" placeholder="Insert your admin user password here" />
	</div>

	<div class="form_item  form_item--dbname full-width">
		<label for="db_name">Database name</label>
		<input type="text" name="pass" id="db_name" value="ximdex" />
	</div>
	<div class="form_item">
		<label for="db_user">Database user</label>
		<input type="text" name="pass" id="db_user" value="ximdex_user " />
	</div>
	<div class="form_item">
		<label for="db_user_pass">Database password</label>
		<input type="password" name="pass" id="db_user_pass" placeholder="Insert a new database password here" />
	</div>
	

	<a href="" class="launch_ximdex action_launcher" onclick="">Connect to database</a>
		
	
</form>