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

<form method="post" name="ml_form" id="ml_form" action="{$action_url}">
	<div class="action_header">
		<h5 class="direction_header"> Name Node: {$name}</h5>
		<h5 class="nodeid_header"> ID Node: {$nodeid}</h5>
		<hr>
	</div>

	<div class="action_content">
		<div class="row tarjeta">
			<div class="small-12 columns title_tarjeta">
				<h2 class="h2_general">{t}Modify language{/t}</h2>
			</div>
			<div class="small-12 columns">
				<div class="input">
            <label for="name" class="label_title label_general">{t}Language name{/t}</label>
			<input type="text" name="Name" id="name" value="{$name}" class="input_general cajag validable not_empty full_size"/>
				</div></div>
			<div class="small-12 columns">
				<div class="input">
            <label for="description" class="label_title label_general">{t}Description{/t}</label>
			<input type="text" name="Description" id="description" value="{$description}" class="input_general full_size cajag validable not_empty"/>
				</div></div>
			<div class="small-12 columns">
				<div class="input">
            <label class="label_title label_general">{t}ISO code{/t}: {$iso_name}</label>

			<input class="hidden-focus" type="checkbox" name="enabled" id="enabled_{$iso_name}" value="1"{if $enabled == 1} checked="checked"{/if}/>
		    <label for="enabled_{$iso_name}" class="icon checkbox-label">{t}Activated{/t}</label>
		</div></div>
			<div class="small-12 columns">
				<fieldset class="buttons-form">
                    {button label="Modify" class="validate btn main_action" }{*message="Would you like to modify this language?"*}
				</fieldset>
			</div>

		</div></div>
</form>


