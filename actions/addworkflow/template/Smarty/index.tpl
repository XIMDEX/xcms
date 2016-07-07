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

/*
  DEPRECATED
*/

<form method="post" name="addworkflow" id="addworkflow" action="{$action_url}">
	<input type="hidden" name="nodeid" value="{$id_node}">

	<div class="action_header">
		<h2>{t}Add workflow{/t}</h2>

		<fieldset class="buttons-form">
			<!-- <a href="{$_MESSAGES_PATH}" class="lbOn validate"></a> -->
			{button label="Add" class='validate btn main_action'}{*message="Would you like to add a new workflow?"*}
		</fieldset>
	</div>

	<div class="action_content">
		<p>
			<label for="workflow" class="label_title">{t}Name of the new workflow{/t}</label>
			<input type="text" id="workflow" name="workflow" class="full_size cajag validable not_empty" placeholder="{t}Name of the new workflow{/t}"/>
		</p>
	</div>
</form>
