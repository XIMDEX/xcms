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

{if count($linked_ximlets) > 0 }
<form method="post" id="delete_rel" class="delete_rel" action="{$action_delete}">

<fieldset>
	<p>{t}The associated ximlets will appear in every single XML document of this section{/t}.</p>
</fieldset>

<fieldset>
	<legend><span>{t}Ximlets already associated{/t}</span></legend>
	<ol>
		<input type="hidden" name="id_node" value="{$id_node}" />
		{foreach from=$linked_ximlets item=ximlet_info}
		<li>
			<label for="{$id_node}_idximlet_{$ximlet_info.idximlet}">
			<input type="checkbox" id="{$id_node}_idximlet_{$ximlet_info.idximlet}" name="idximlet[]" value="{$ximlet_info.idximlet}" />
			<strong>{$ximlet_info.path}</strong></label>

		</li>
		{/foreach}
	</ol>
</fieldset>
<fieldset>
	<input type="checkbox" name="recursive" id="{$id_node}_recursive_delete" /> <label for="{$id_node}_recursive_delete"> {t}Disassociate recursively{/t}.</label>
	<p>{t}If the current section/server has subfolders, the disassociation will be deleted for them too{/t}.</p>
</fieldset>

<fieldset class="buttons-form">
	{button label="Disassociate" class='validate button-delete' }{*message="Are you sure you want to disassociate it?"*}
</fieldset>

</form>
{/if}

{if count($linkable_ximlets) > 0 }
<form id="create_rel" class="create_rel" action="{$action_create}" method="post">

<fieldset>
	<legend><span>{t}Available Ximlets{/t}</span> </legend>
	<ol>
		<input type="hidden" id="id_node" name="id_node" value="{$id_node}" />
		{foreach from=$linkable_ximlets item=ximlet}
                <li>
			<label for="{$id_node}_idximletavailable_{$ximlet.idximlet}">
                        <input type="checkbox" name="idximlet[]" id="{$id_node}_idximletavailable_{$ximlet.idximlet}" value="{$ximlet.idximlet}" />
                        <strong>{$ximlet.path}</strong></label>
                </li>
                {/foreach}
	</ol>
</fieldset>
<fieldset>
	<input type="checkbox" name="recursive" id="{$id_node}_recursive_add" /><label for="{$id_node}_recursive_add"> {t}Associate recursively{/t}.</label>
	<p>{t}If the current section/server has subfolders, the association will be created for them too{/t}.</p>
</fieldset>

<fieldset class="buttons-form">
	{button label="Associate" class='validate button-assoc' }{*message="Would you like to associate this section with the ximlet?"*}
</fieldset>

</form>
	{else}
		<p>{t}There aren't any ximlets to associate{/t}.</p>
	{/if}
