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

<form method="POST" name="ca_form" id="ca_form"  action="{$action_url}">
	<fieldset>
		<input type="hidden" name="idNode" value="{$id_node}" />
		<input type="hidden" name="idAction" value="{$id_action}" />
		<input type="hidden" name="area_id" value="{$area_data.id}" />

		<legend><span>{t}Edit category{/t} {$area_data.name}</span></legend>
		<ol>
			<li>
				<label class="aligned">{t}Name{/t}</label>
				<input type="text" name="area_name" value="{$area_data.name}" class="cajag validable not_empty"/>
			</li>
			<li>
				<label class="aligned">{t}Description{/t}</label>
				<input type="text" name="area_description" value="{$area_data.description}" class="cajag validable not_empty"/>
			</li>
		</ol>
	</fieldset>

	<fieldset class="buttons-form">
		{button label="Reset" class='form_reset btn' type="reset"}
		{button label="Edit category" class="validate btn main_action" }<!--message="Do you want to edit the category?"-->
	</fieldset>
</form>
