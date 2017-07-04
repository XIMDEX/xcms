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
			<h5 class="direction_header"> Name Node: {$nameNode}</h5>
			<h5 class="nodeid_header"> ID Node: {$nodeid}</h5>
			<hr>
		</div>
		<div class="message message-warning">
			<p class="icon">{t}This action cannot be undone{/t}.</p>
			{if ($isPublished || $pendingTasks)}
			<p>{t}This action does not carry on expiration of nodes{/t}. </p>
			{/if}
		</div>

		<div class="action_content">
			<div class="row tarjeta">
				<div class="small-12 columns title_tarjeta">
					<h2 class="h2_general">{t}Delete{/t}</h2>
				</div>
			{if ($depList)}
			<label class="label_title label_general">{t}The following nodes will be deleted on cascade{/t}. {t}Deleting this node all its children will be deleted too{/t}.</label>
			<div class="deletenodes">
				<ul>
					{foreach from=$depList key=id item=dep}
					<li class="box_short" data-tooltip="{{$dep.path|gettext}} "><div class="name">{$dep.name|gettext}</div> <span class="node_id">({$id})</span></li>
					{foreachelse}
					<li><span>{t}No dependencies were found{/t}</span></li>
					{/foreach}
				</ul>
			</div>
				<div class="small-12 columns">
					<input type="checkbox" name="unpublishnode" id="asegurado" checked="checked" class="hidden-focus"/>
					<label class="input-form checkbox-label" for="asegurado">{t}Tick to delete all dependencies{/t}</label>
				</div>



				{else}
				<div class="deletenodes">
					<label class="label_title label_general">{t}This node has not dependecies at the moment{/t}.</label>
					<input type="hidden" name="unpublishnode" id="asegurado" value="1"/>
				</div>
				{/if}
				{if ($pendingTasks)}
				<div class="small-12 columns">
					<div class="alert alert-info">
						<strong>Info!</strong> {t}This node or its children have pending task to publish, if you continue these tasks will be interrupted{/t}!</p>
					</div></div>
						{/if}

				{* Checking if node is published *}
				{if ($isPublished)}
				<div class="small-12 columns">
					<div class="alert alert-info">
						<strong>Info!</strong> {t}This node, or one or more of its children, are published{/t}!.{t}If you do not want to keep your nodes published{/t}:
					<ul>
						<li>- {t}Edit publication life of your nodes previously deletion.{/t}</li>
						<li>- {t}Or if you prefer, delete your nodes by hand later in the publication zone{/t}.</li>
					</ul></div></div>
					{/if}

				<div class="small-12 columns">
				<div class="alert alert-info">
					<strong>Info!</strong> {t}If a long time is elapsed before action is executed, list of dependencies and children of node may change{/t}.
				</div></div>
				<div class="small-12 columns">
			<fieldset class="buttons-form">
                {button class="delete_button  btn main_action" label="Delete"}
			</fieldset>
				</div></div></div>
		</form>

