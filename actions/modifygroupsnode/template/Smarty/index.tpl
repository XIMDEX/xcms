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
<form method="post" id="groups_form" action="{$action_url}">
	<input type="hidden" name="id_node" value="{$id_node}">
	<div class="action_header">
		<h2>{t}Manage groups{/t}</h2>
	</div>
        <div class="action_content"><fieldset>
<h2>{t}Add group to section{/t}</h2>

        		{if (!$new_groups)}
        	       	<p>{t}No groups to add have been found{/t}.</p>
        	{else}
        	        <ol>
        	        	<li><label for="id_group" class="aligned">{t}Available groups{/t}</label>
        					<select name='id_group' id="id_group" class='cajag' onchange='comprobar_vacio();'>
        						<option value="">{t}Select group{/t}</option>
        						{foreach from=$new_groups key=id_group item=group}
        							<option value="{$id_group}">{$group}</option>
        						{/foreach}
        	                        </select>
        	                </li>
        				<li><label for="is_recursive" class="aligned">{t}Recursive{/t}</label>
        					<input name='is_recursive' type='checkbox' value='newrec' id="is_recursive">
        				</li>
        				<li><label for="id_role" class="aligned">{t}Force role{/t}</label>
        					<select name='id_role' class='caja' id="id_role">
        		 				<option value=''>{t}None{/t}</option>
        		 				{foreach from=$roles item=rol_info}
        		 					<option value="{$rol_info.IdRole}">{$rol_info.Name}</option>
        		 				{/foreach}
        		 			</select>
        				</li>
        	   	</ol>
        	       	{/if}
        	</fieldset>

        		{if ($new_groups)}
        		<fieldset class="buttons-form">
        			{button label="Add group" onclick="call_submit('addgroupnode');" class="validate btn main_action" }{*message="Are you sure you want to perform this association?"*}
        		</fieldset>
        		{/if}
        					<h2>{t}Delete group of a section{/t}</h2>
        	<fieldset>
        	{if (!$all_groups)}
        	       	<p>{t}No associated groups have been found{/t}</p>
        	{else}
        			<table>
        				<tr>
        				<th>{t}Delete{/t}</th>
        				<th>{t}Group{/t}</th>
        				<th>{t}Force role{/t}</th>
        				</tr>
        			{foreach from=$all_groups item=groupInfo}
        				<tr>
        					<td class='filaclara' align='center'>
        						<input name='id_group_checked[]' type='checkbox' value='{$groupInfo.IdGroup}'>
        					</td>
        					<td class='filaclara' align='center'>
        						{$groupInfo.Name}
        					</td>
        					<td class='filaclara' align='center'>
        						<input type='hidden' name='idGroups[]' value='{$groupInfo.IdGroup}'>
        						<input type='hidden' name='idRoleOld[]' value='{$groupInfo.IdRoleOnNode}'>
        		 				<select name='idRole[]' class='caja'>
        		 					<option value=''>{t}None{/t}</option>
        		 					{foreach from=$roles item=rol_info}
        		 						<option value="{$rol_info.IdRole}"{if $groupInfo.IdRoleOnNode == $rol_info.IdRole} selected="selected"{/if}>{$rol_info.Name}</option>
        							{/foreach}
        						</select>
        					</td>
        				</tr>

        			{/foreach}
        			</table>
        		{/if}
        	</fieldset>


        	       {if ($all_groups)}
        		<fieldset class="buttons-form">
        			{button label="Delete subscriptions" class="validate btn" onclick="call_submit('deletegroupnode')" message="Are you sure you want to delete selected subscriptions?"}
        			{button class="validate btn main_action" label="Save changes" onclick="call_submit('updategroupnode');"  message="Are you sure you want to update selected subscriptions?" }
        		</fieldset>
        		{/if}</div>
</form>
