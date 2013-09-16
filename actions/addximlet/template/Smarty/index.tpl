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


{if count($ximlet_list) > 0 }
<form method="post" id="delete_rel" class="delete_rel" action="{$action_delete}">
	<div class="action_header">
		<h2>{t}Associated ximlets{/t}</h2>
		<fieldset class="buttons-form">
			{button label="Disassociate" class='validate button-delete' }<!--message="Are you sure you want to disassociate it?"-->
		</fieldset>
	</div>
<div class="action_content">

	<fieldset>
		<ol>
			<input type="hidden" name="id_node" value="{$id_node}" />
			{foreach from=$ximlet_list item=ximlet_info}
			<li>
				<input type="checkbox" name="idximlet[]" value="{$ximlet_info.idximlet}" />
				<label for="idximlet"><strong>{$ximlet_info.path}</strong></label>

			</li>
			{/foreach}
		</ol>
	</fieldset>
</div>


</form>
{/if}


<form id="create_rel" class="create_rel" action="{$action_create}" method="post">
	<div class="action_header">
<h2>{t}Associate ximlet with section{/t} "{$name}"</h2>
			<fieldset class="buttons-form">
	{button label="Associate" class='validate button-assoc btn main_action' }<!--message="Would you like to associate this section with the ximlet?"-->
</fieldset>
	</div>

<div class="action_content">
	<fieldset>
		<ol>
			<li>
				<label for="pathfield" class="aligned">{t}Ximlet to associate with:{/t}</label><treeview class="xim-treeview-selector"
					paginatorShow="yes" />
			</li>
			<li>

					<input type="hidden" id="id_node" name="id_node" value="{$id_node}" />
					<input type="hidden" id="contenttype" value="5056" />
					<input type="hidden" id="targetfield" name="targetfield" value="" />
					<!-- <input type="text" readonly name="pathfield" id="pathfield" value="" class="validable not_empty" /> -->
					<input value="1" type="checkbox" name="recursive" id="recursive" /> {t}Associate recursively {/t}
			</li>
		</ol>
	</fieldset>
</div>



</form>
