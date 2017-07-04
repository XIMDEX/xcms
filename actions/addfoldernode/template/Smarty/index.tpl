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

<form method="post" id="print_form" action="{$action_url}">
	<div class="action_header">
		<h5 class="direction_header"> Name Node: {$name}</h5>
		<h5 class="nodeid_header"> ID Node: {$nodeid}</h5>
		<hr>
	</div>
	<div class="action_content">
		<div class="row tarjeta">
		<h2 class="h2_general">{t}Add{/t} {$friendlyName|gettext}</h2>
			<div class="small-12 columns">
		<div class="input">
			<label for="name" class="label_title label_general">{t}Name{/t} *</label>
		<p class="icon icon-positioned server input">
			<input type="text" name="name" id="foldername" class="input_general cajaxg validable js_val_alphanumeric not_empty full-size {$friendlyName|replace:" ":"_"}_icon" placeholder="{t}{$friendlyName} name{/t}">
		</p></div></div>

		<!-- show disclaimer if node CanAttachGroups -->
		{if ($CanAttachGroups)}
            <p class="herachy-disclaimer">
				<strong>* {t}Warning{/t}:</strong> {t}Folder permissions is going to inherit from the parent{/t}.
			</p>
        {/if}

	<div class="small-12 columns">
	<fieldset class="buttons-form">
	    {button label="Create" class='validate btn main_action'}
	</fieldset></div></div></div>
</form>

