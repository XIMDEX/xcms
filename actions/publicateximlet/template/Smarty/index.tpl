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

{if ($docs)}
<form  method="post" name="formulario" id="formulario" action='{$action_url}'>
	<input type="hidden" name="id_node" value="{$node_id}" />	

	<fieldset>
	    <legend><span>{t}Publish ximLet{/t}</span></legend>
		<p>{t}{$actionDescription|htmlentities}{/t}</p>
		<ul>
			{foreach from=$docs key=index item=docData}
			<li>
				<span>{$docData.name}</span>
				<span class="docpath hidden path_{$docData.name}">{$docData.path}</span>
			</li>
			{/foreach}
		</ul>
		<p><input class="see_paths" name="see_paths" type="checkbox" value=""/><label>{t}Show paths{/t}</label></p>
	</fieldset>

	<fieldset class="buttons-form">
		{button class="validate start_publication" label="Accept" message="Would you like to continue with publication?"}
	</fieldset>

</form>
{else}
<fieldset>
	<legend><span>Publish ximLet</span></legend>
	<p>No ximlet to publish has been found.</p>
</fieldset>
{/if}

<!--<div id="publicando" style="display: none;">
	<img src="{$_URL_ROOT}/xmd/images/indicator.gif" alt="" border="0" id="publicando">
</div>-->
