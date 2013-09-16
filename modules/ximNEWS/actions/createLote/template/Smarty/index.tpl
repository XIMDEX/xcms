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

<form method="post" name="ca_form" id="ca_form"  action="{$action_url}">
	<fieldset>
		<input type="hidden" name="nodeid" value="{$id_node}"/>
		<ol>
			<li>
				<label class="aligned">{t}Name{/t}</label>
				<input type="text" id="lotename" class="cajag validable not_empty" name="lotename">
			</li>
			<li>
				<label class="aligned">{t}Type{/t}</label>
				<select name='tipo' class='caja'>";
				   <option value='normal'>{t}Normal{/t}</option>
				   <option value='fechaCarpetas'>{t}Date in folders{/t}</option>
				   <option value='fechaNombre'>{t}Date in name{/t}</option>
				</select>
			</li>
			<li>
				<label class="aligned">{t}Date{/t}</label>
				<input id="boxfecha" name="boxfecha" type="text" value="{$cadenafecha}" class="caja">
			</li>
		</ol>
	</fieldset>

	<fieldset class="buttons-form">
		{button label="Reset" class='form_reset btn' type="reset"}
		{button label="Create" class="validate btn main_action"}
	</fieldset>
</form>
