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
<form method="post" name="publication_form" id="publication_form" action="{$action_url}">
	<input type="hidden" name="nodeid" value="{$id_node}" class="ecajag" />

<div class="action_header">
<h2>{t}Publish section{/t}</h2>
	<fieldset class="buttons-form">
		{button label="Accept" class="validate btn main_action" }<!--message="You are going to publish $node_name. Would you like to continue?"-->
	</fieldset>
</div>
	<div class="action_content">
		<fieldset>
		    <ol class="numbered">
		    	<li><span>{t}This action cannot be undone{/t}.</span></li>
		        <li><span>{t}Scheduled publications for these files will be cancelled.{/t}</span></li>
		       <p>{t}You have selected to publish contents of {/t} <strong>{$node_name}</strong>.</p><!-- <p>{t}You are selected to publish contents of {if $folderName == 'sección'} of {else} of {/if} {$folderName}:{/t} "<strong>{$node_name}</strong>"</p> -->
		     <p>   {t}Would you like to publish just{/t} <strong>{$node_name}</strong> {t}or also the contained subsections?{/t}</p><!-- <p>   {t}Would you like to publish just {if $folderName == 'sección'} this {else} this {/if} {$folderName} or contained subsections in {if $folderName == 'sección'} also, ella{else} él{/if}?{/t}</p> -->

			<ol>
				<li>
					<input type="radio" name="rec" value="" checked id="nonrecursive">
					<label label="nonrecursive">{t}Publish just{/t} <strong>{$node_name}</strong>.</label>
				</li>
				<li>
					<input type="radio" name="rec" value="rec" id="recursive">
					<label for="recursive">{t}Publish{/t} <strong>{$node_name}</strong> {t}and its subsections{/t}.</label>
				</li>
				{if $synchronizer_to_use eq 'ximSYNC' && $ximpublish_tools_enabled}
					<li>
					<label>{t}Node types to publish{/t}:</label>
					<select name="types" id="types">
						<option value="0">{t}All{/t}</option>
						{foreach from=$publishabledtypes item=type}
							<option value="{$type.id}">{$type.name}</option>
						{/foreach}
					</select>
					</li>
				{/if}
		            </ol>

		        </ol>

		</fieldset>
	</div>



</form>

<div id="publishing_message" style="display:none;">
	<p><img src="{$_URL_ROOT}/xmd/images/publicando_seccion.gif" alt="" border="0" /></p>
	<p>Publishing...</p>
	<p class="small">Please, wait</p>
</div>
<div id="div_log" style="text-align: center; display: none; width: 98%; height: 30px; overflow: auto; padding: 3px; vertical-align: top; alig: center;" />
