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

<form method="post" name="mc_form" id="mc_form" action="{$action_url}" class='validate_ajax'>
    <input type="hidden" name='id_node' value="{$id_node_parent}">
    <div class="action_header">
        <h2>{t}Modify channel{/t}</h2>
        <fieldset class="buttons-form">
          {button label="Modify" class='validate btn main_action' }
          {*message="Would you like to modify this channel?"*}
        </fieldset>
    </div>
    <div class="action_content">
        <p>
            <label class="label_title">{t}Name{/t}:</label>{$name}
        </p>
        <p>
            <label class="label_title">{t}File extension{/t}:</label>{$extension}
        </p>
{**
Names must be in the same case that database fields.
*}
        <p>    
            <label for="rendermode" class="label_title">{t}Output{/t}</label>
            <input type='radio' id="web_{$id_node_parent}" name="OutputType" {$output_check.web} value='web'><label for="web_{$id_node_parent}">{t}Web{/t}</label>
            <input type='radio' id="xml_{$id_node_parent}" name="OutputType" {$output_check.xml} value='xml'><label for="xml_{$id_node_parent}">{t}Xml{/t}</label>
            <input type='radio' id="other_{$id_node_parent}" name="OutputType" {$output_check.other} value='other'><label for="other_{$id_node_parent}">{t}Other{/t}</label>
        </p>
        
        <p>
            <label for="description" class="label_title">{t}Description{/t}</label>
            <input type="text" name="Description" id="description" value="{$description|gettext}" class='cajag validable not_empty' />
        </p>
        
        <p>
            <label for="rendermode" class="label_title">{t}Rendering in{/t}</label>
            <input type="radio" id="rendermode" name="RenderMode" {$render_check.ximdex} value="ximdex"/>{t}Ximdex{/t}
            <input type="radio" id="rendermode" name="RenderMode" {$render_check.client} value="client"/>{t}Client{/t}
        </p>
    </div>
</form>
