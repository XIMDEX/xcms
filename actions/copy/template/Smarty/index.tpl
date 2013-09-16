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



<form name="copy" id="copy" method="post" action="{$action_url}">
	<div class="action_header">
		<h2>{t}Copy element{/t}</h2>
		<fieldset class="buttons-form">
			{button class="validate btn main_action" label="Copy" }<!--message="Are you sure you want to copy this node to selected destination?"-->
        </fieldset>
	</div>
<div class="action_content">
	<fieldset>
	<input type="hidden" id="nodeid" name="nodeid" value="{$id_node}">
	<input type="hidden" name="nodetypeid" value="{$nodetypeid}">
	<input type="hidden" name="filtertype" value="{$filtertype}">
	<input type="hidden" name="targetid" id="targetid">
	<input type="hidden" id="editor">
		<ol>
			<li>
				<label for="id_node" class="aligned-left"><span>{t}Choose destination{/t}</span></label>
			<div class="right-block">
				<treeview class="xim-treeview-selector"	paginatorShow="yes" handler="hndCopy_treeSelector" /></div>
			</li>
			<li class="ui-state-highlight ui-corner-all msg-warning">
				<!--<input type="text" readonly name="pathfield" id="pathfield" value="" >-->

				<!--For now it allows ximIO to renames automatically copies with same level.
				    To Allow the user to insert directly the name will be a improvement.-->
				<!--<div class="changename hidden">
					<p>In selected destination already exists a node with the same name. Please, insert a new name:</p>
					<label for="id_node"><span>{t}New name{/t}</span></label>
					<input type="text" name="newname" />
				</div>-->
				<div class="warning hidden">
					<span class="ui-icon ui-icon-notice"></span><p>{t}The copy operation is not allowed on selected destination.{/t}</p>
					<span class="ui-icon ui-icon-info"></span><p> &nbsp;{t}Please, select another destination to copy.{/t}</p>
				</div>
			</li>
			<li>
				<!--<br/><br/><input type="checkbox" name="recursive" id="recursive" checked="checked" /><label for="recursive"> {t}Recursive process{/t}</label>-->
				<br/><br/><input type="checkbox" name="recursive" id="recursive" checked="checked" /><label for="recursive"> {t}Execute this action for all files and subfolders{/t}.</label>
			</li>

		</ol>
	    </fieldset>
</div>

</form>
