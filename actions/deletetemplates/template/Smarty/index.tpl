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
	<form method="post" id="msr_action" action="{$action_url}">
<div class="action_header" style="padding-bottom: 90px!important;">
	<h5 class="direction_header"> Name Node: {t}Delete templates{/t}</h5>
	<h5 class="nodeid_header"> ID Node: {$nodeid}</h5>
	<hr>

	<fieldset class="buttons-form">
		{button label="Select all" class="button-select-all btn main_action"}
		{button label="Select none" class="button-deselect-all btn main_action"}
		{button label="Delete" class="validate button-modify btn main_action"}
	</fieldset>
</div>
<div class="action_content">
{if ($templates)}





			<ol>
				{section name=i loop=$templates}
					<li class="box-item box_item-template">
						<span>
							<input type="checkbox" name="templates[]" value="{$templates[i].Id}" class="hidden-focus" id="delete_{$templates[i].Id}"/> 
							<label for="delete_{$templates[i].Id}" class="checkbox-label icon">{$templates[i].Name}</label>
						</span>
					</li>
				{/section}
			</ol>




{else}

	<div class="small-12 columns">
		<div class="alert alert-info" >
			<strong>Info!</strong> {t}Templates not found{/t}</div></div>

{/if}</div>
	</form>