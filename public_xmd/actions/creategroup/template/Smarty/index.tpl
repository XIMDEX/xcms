{**
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
 *}

<form method="post" name="cg_form" id="cg_form" action="{$action_url}" class='validate_ajax'>
	<input type="hidden" name="id_node" value="{$id_node}" class="ecajag"/>
	<div class="action_header">
		<h5 class="direction_header"> Name Node: {t}Group manager{/t}</h5>
		<h5 class="nodeid_header"> ID Node: {$nodeid}</h5>
		<hr>
	</div>

	<div class="action_content">
		<div class="row tarjeta">
			<div class="small-12 columns title_tarjeta">
		<h2 class="h2_general">{t}Add group{/t}</h2>
			</div>
			<div class="small-12 columns">
		<div class="input group">
			<label for="groupname" class="label_title label_general">{t}Name{/t} *</label>
			<input type="text" name="name" id="groupname" class="input_general cajag validable not_empty" placeholder="{t}Group's name{/t}">
		</div></div>
			<div class="small-12 columns">
				<fieldset class="buttons-form">
                    {button label="Create group" class='validate btn main_action' }{*message="do you want to create the group?"*}
				</fieldset></div>
			</div></div>
</form>

