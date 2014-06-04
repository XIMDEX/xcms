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
<div class="action_header">
	<h2>{t}Move node{/t} {$name}</h2>
	<fieldset class="buttons-form">
		{button label="Move node" class="validate btn main_action" }{*message="Are you sure you want to move this node to selected destination?"*}
	</fieldset>
</div>
<div class="action_content">
<fieldset>
	<label for="id_node"><span>{t}Select new node destination{/t}</span></label>
	<div class="copy_options">
		{foreach from=$targetNodes key=index item=targetNode}
				<div>
							<input id="move_{$id_node}_{$targetNode.idnode}" type="radio" name="targetid" value="{$targetNode.idnode}" />
							<label for="move_{$id_node}_{$targetNode.idnode}" class="icon folder">{$targetNode.path}</label>
								
							
				</div>	
		{/foreach}
	</div>		
</fieldset>
    	{if ($isPublished)}
<fieldset>
	<legend><span>{t}Warning about publication{/t} </span> </legend>
	<p>¡{t}This node, or one or more of its children, are published{/t}!.{t}If you do not want to keep your nodes published on current location{/t}:</p>
	<ul>
		<li nowrap>- {t}Edit publication life of your nodes previously of node movement.{/t}</li>
		<li>- {t}Or if you prefer, delete your nodes by hand later in the publication zone{/t}.</li>
	</ul>
</fieldset>
	{/if}</div>
</form>
