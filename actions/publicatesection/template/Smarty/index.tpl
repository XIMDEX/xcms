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
		{button label="Accept" class="validate btn main_action" }{*message="You are going to publish $node_name. Would you like to continue?"*}
	</fieldset>
</div>
<div class="message warning-message">
		        <p>{t}Scheduled publications for these files will be cancelled.{/t}</p></div>
	<div class="action_content">
		<fieldset>
		    	
		       <p>{t}You have selected to publish contents of {/t} <strong>{$node_name}</strong>. {t}Would you like to publish the contained subsections?{/t}</p>
		
			<label label="nonrecursive"  class="col1-2"><input type="radio" name="rec" value="" checked id="nonrecursive">
				{t}Publish just{/t} <strong>{$node_name}</strong>.</label>
			
			<label for="recursive" class="col1-2">	<input type="radio" name="rec" value="rec" id="recursive">
					{t}Publish{/t} <strong>{$node_name}</strong> {t}and its subsections{/t}.</label>
			
			{if $synchronizer_to_use eq 'ximSYNC' && $ximpublish_tools_enabled}
					
					<label>{t}Node types to publish{/t}:</label>
					<select name="types" id="types">
						<option value="0">{t}All{/t}</option>
						{foreach from=$publishabledtypes item=type}
							<option value="{$type.id}">{$type.name}</option>
						{/foreach}
					</select>
					
				{/if}
		           


		</fieldset>
	</div>



</form>

<div id="publishing_message" style="display:none;">
	<p><img src="{$_URL_ROOT}/xmd/images/publicando_seccion.gif" alt="" border="0" /></p>
	<p>Publishing...</p>
	<p class="small">Please, wait</p>
</div>
<div id="div_log" style="text-align: center; display: none; width: 98%; height: 30px; overflow: auto; padding: 3px; vertical-align: top; alig: center;" />
