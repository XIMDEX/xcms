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

<form method="post" name="cln_form" id="cln_form" action="{$action_url}">
<input type="hidden" id="nodeid" name="nodeid" value="{$id_node}">
<input type="hidden" name="nodetypeid" value="{$nodetypeid}">
<input type="hidden" name="filtertype" value="{$filtertype}">
<input type="hidden" name="targetid" id="targetid">
<input type="hidden" id="editor">
	<fieldset>
			<legend><span>{t}Move node{/t} <strong>{$name}</strong></span></legend>
		<ol>
		<li>
				<label for="id_node" class="aligned"><span>{t}Select new node destination{/t}</span></label>
				<div class="right-block">
					<treeview class="xim-treeview-selector"	paginatorShow="yes" handler="hndCopy_treeSelector" />
				</div>
		</li>
				<li class="ui-state-highlight ui-corner-all msg-warning">
			<!--<input type="text" readonly name="pathfield" id="pathfield" value="" >-->

			<!--For now ximIO automatically renames copies on same level.
			    Allow user to insert directly the name will be a improvement.-->
			<!--<div class="changename hidden">
				<p>{t}A node with same name already exists on selected destination. Please, insert a new name:{/t}</p>
				<label for="id_node"><span>{t}Nuevo nombre{/t}</span></label>
				<input type="text" name="newname" /> 
			</div>-->
			<div class="warning hidden">
				<span class="ui-icon ui-icon-notice"></span><p>{t}Move operation is not allowed on selected destination.{/t}</p>
				<span class="ui-icon ui-icon-info"></span><p> &nbsp;{t}Please, select a different destination to move the node.{/t}</p>
			</div>
		</li>
		</ol>
	</fieldset>
    	{if ($isPublished)}
			<fieldset>
				<legend><span>{t}Warning about publication{/t} </span> </legend>
				<p>¡{t}This node, or one or more of its children, are published{/t}!.{t}If you do not want to keep your nodes published on current location{/t}:
				<ul>
				<li nowrap>- {t}Edit publication life of your nodes previously of node movement.{/t}</li>
				<li>- {t}Or if you prefer, delete your nodes by hand later in the publication zone{/t}.</li>
				</ul>
			</fieldset>
			{/if}
	<fieldset class="buttons-form">

				<!--{button label="Reset" onclick='cln_form.reset(); return false;' type="reset"}-->
				{button label="Move node" class="validate" }<!--message="Are you sure you want to move this node to selected destination?"-->

	</fieldset>	
</form>
