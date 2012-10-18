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

<tr>
	<td>
		<table class="list">
			<tr>
				<th>{t}Name{/t}</th>
				<th>{t}Description{/t}</th>
				<th>{t}Operactions{/t}</th>
			</tr>
			{foreach from=$list key=key item=listInfo}
			<tr class="list_{$listInfo.id}">
			{include file="`$_APP_ROOT`/actions/manageList/template/Smarty/_element.tpl"}
			</tr>
			{/foreach}
		</table>
		{button label="Add new element"
			onclick="getWidget(this).canvas_i('load_add');"}

		<div class="list_info_manager" style="display:none">
			<input type="hidden" name="type" value="{$type}" />
			<input type="hidden" class="id_updater" name="id" />
			<div class="list_property">
				<label>{t}Name{/t}:</label>
				<input type="text" class="name_updater" />
			</div>
			<div class="list_property">
				<label>{t}Description{/t}:</label>
				<input type="text" class="description_updater" />
			</div>
			<div class="list_actions">
				{button class="add_button" 
					label="Confirm and add" 
					onclick="getWidget(this).canvas_i('add');"} 
				{button class="update_button" 
					label="Confirm and update" 
					onclick="getWidget(this).canvas_i('update', this);"} 
				{button class="cancel_button" 
					label="Cancel" 
					onclick="getWidget(this).canvas_i('cancel', `$listInfo.id`, this);"}
			</div> 
		</div>		
	</td>
</tr>