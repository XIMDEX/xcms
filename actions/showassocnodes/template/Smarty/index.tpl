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


{if count($sections) > 0 }

<form method="post" id="delete_rel" action="{$action_delete}">
<input type="hidden" name="ximletid" value="{$ximletid}">
<fieldset>
	<legend><span>{t}Delete selected associations{/t}</span></legend>
	<div class="sections-container">
	<ol>
		{foreach from=$sections item=section}
		<li>
			<input type="checkbox" name="sections[]" value="{$section.idsection}" class="sections_lists" />
			<label>{$section.path}</label>
		</li>
		{/foreach}
	</ol>
	</div>
</fieldset>

<fieldset class="buttons-form">
	{button label="Remove" class="validate deleterel-button" }<!--message="Are you sure you want to remove this association?"-->
</fieldset>
</form>

{/if}

<form id="create_rel" action="{$action_create}" method="post">
<input type="hidden" name="ximletid" value="{$ximletid}">
<input name="treeroot" type="hidden" value="{$treeroot}" />
<input name="searchednodetype" type="hidden" value="{$searchednodetype}" />
<input type="hidden" name="targetid" value="" />
<fieldset>
	<legend><span>{t}Associate ximlet with section{/t}</span></legend>
	<ol>
		<li><p class="left"><label>Select node to associate</label></p>

			<treeview class="xim-treeview-selector" paginatorShow="no" />

		</li>
		<li>
			<label for="path" class="aligned">{t}The following ximLet is going to be associated{/t}</label>
			<input type="text" readonly id="path" name="path" value="" class="validable not_empty" />
		</li>
		<li>
			<input value="1" type="checkbox" id="recursive" name="recursive" />
			<label for="recursive">{t}Associate recursively {/t}</label>

		</li>
		<li class="warning hidden">
				<span class="ui-icon ui-icon-notice"></span><p>{t}The current operation is not allowed on the selected destination{/t}. <span class="ximlet_already_selected hidden">{t}This ximlet already is assigned to that target{/t}</p>
				<span class="ui-icon ui-icon-info"></span><p> &nbsp;{t}Please, select another destination.{/t}</p>
		</li>
	</ol>
</fieldset>

<fieldset class="buttons-form">
	{button label="Associate" class='validate createrel-button' }<!--message="Would you like to associate this section with this ximlet?"-->
</fieldset>
</form>
