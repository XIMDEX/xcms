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

{* Shows form to users who have general permit of cascade deletion *}

<form method="post" name="formulario" id='formulario' action='{$action_url}'>
	<input type="hidden" name="nodeid" value="{$id_node}">
	<div class="action_header">
		<h2>{t}Would you like to delete{/t} {$nameNode}?</h2>

	<fieldset class="buttons-form">
		{button class="delete_button  btn main_action" label="Delete"}
	</fieldset>
	</div>
	<div class="action_content">
		<fieldset>
				 <ol class="numbered">
		             	<li><span>{t}This action cannot be undone{/t}.</span></li>
		               <li><span>{t}If a long time is elapsed before action is executed, list of dependencies and children of node may change{/t}.</span></li>
					<li><span>{t}Deleting this node all its children will be deleted too{/t}.</span></li>
					{if ($isPublished || $pendingTasks)}
						<li><span>{t}This action does not carry on expiration of nodes{/t}. </span></li>
					{/if}
					</ol>
					{if ($depList)}
					<p class="delete">{t}The following nodes will be deleted on cascade{/t}:</p>
						<div class="deletenodes">
							<ul>
							{foreach from=$depList item=dep}
								<li nowrap><span>{$dep}</span></li>
							{foreachelse}
								<li><span>{t}No dependencies were found{/t}</span></li>
							{/foreach}
							</ul>
						</div>
						<span><input type="checkbox" name="unpublishnode" id="asegurado" />
						<span>{t}Tick to delete all dependencies{/t}</span></span>
				{else}&nbsp;
				<div class="deletenodes">
					<ul>
						<li nowrap class="no-node"><span>{t}This node has not dependecies at the moment{/t}.</span></li>
					</ul>
					<input type="hidden" name="unpublishnode" id="asegurado" value="1"/>
				</div>
				{/if}
		</ol>
				{if ($pendingTasks)}
				<p>¡{t}This node or its children have pending task to publish, if you continue these tasks will be interrupted{/t}!</p>
				{/if}

				{* Checking if node is published *}
				{if ($isPublished)}
					<p>¡{t}This node, or one or more of its children, are published{/t}!.{t}If you do not want to keep your nodes published{/t}:
					<ul>
					<li nowrap>- {t}Edit publication life of your nodes previously deletion.{/t}</li>
					<li>- {t}Or if you prefer, delete your nodes by hand later in the publication zone{/t}.</li>
					</ul>
				{/if}
			</p>
		</fieldset>
	</div>

</form>
