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

<form method="POST" name="index_form" id="index_form" action="{$action_url}">
<fieldset>
	<input type="hidden" name="idNode" value="{$id_node}" />
	<input type="hidden" name="idAction" value="{$id_action}" />
	<input type="hidden" name="action_type" value="add" />
<legend><span>{t}Manage categories{/t}</span></legend>

	{if count($areas) > 0}

	<table>
<tr>
<th>{t}Category{/t}</th>
<th>{t}Description{/t}</th>
<th></th>
<th></th>
</tr>{foreach from=$areas key=ene item=area}
<tr>

			<td>
			{$area.Name} </td>
			<td>{$area.Description}</td>
			<td align="center">{button id="`$area.IdArea`" label="Modify" class="update_area"}  </td>
			<td align="center">{button id="`$area.IdArea`" label="Remove" class="delete_area"}
			</td>

		</tr>{/foreach}
		</table>
	{else}
		<p>{t}No category found{/t}</p>
	{/if}
</fieldset>

<fieldset class="buttons-form">
        {button label="Add category" class="add_area"}
</fieldset>
</form>
