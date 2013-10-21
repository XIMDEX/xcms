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


<form method="post" name="as_form" id="as_form" action="{$action_url}">
	<input type="hidden" name="nodeid" value="{$nodeID}">
	<input type="hidden" name="name" value="{$sectionName}">
	
	<div class="action_header">
		<h2>{t}Configure section{/t}</h2>
		<fieldset class="buttons-form">
			{button label="Save changes" class='validate btn main_action' message="Are you sure you want to proceed?"}
		</fieldset>
	</div>
<div id="warning" class="message delete-message hidden-message"></div>
	<div class="action_content section-properties">
		<div class="folder-name folder-normal icon disabled">
			<h3> {$sectionName} <span class="folder-type">{t}{$sectionType}{/t}</span></h3>
		</div>

		<div class="subfolders-available"><h3>{t}Subfolders availables{/t}</h3>
		{foreach from=$subfolders key=nt item=foldername}
			<div class="subfolder box-col1-1">
                              	<input class="hidden-focus" id="{$nt}_{$nodeID}" name="folderlst[]" type="checkbox" value="{$nt}" {if $foldername[2]=='selected' } checked{/if} {if $nt eq 5301 || $nt eq 5304} readonly {/if}/>
				<label class="icon" for="{$nt}_{$nodeID}"><strong class="icon {$foldername[0]}">{$foldername[0]}</strong></label>
                              	<span class="info">{t}{$foldername[1]}{/t}</span>
			</div>
                {/foreach}
			
		</div>
		
	</div>
</form>
