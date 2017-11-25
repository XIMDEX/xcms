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

<form method="post" id="language_form" action="{$action_url}"   class='validate_ajax'>

<input type="hidden" name="nodeid" id='nodeid' value="{$nodeid}">
<div class="action_header">
    <h5 class="direction_header"> Name Node: {t}Language manager{/t}</h5>
    <h5 class="nodeid_header"> ID Node: {$nodeid}</h5>
    <hr>
</div>

<div class="action_content">
    <div class="row tarjeta">
        <div class="small-12 columns title_tarjeta">
            <h2 class="h2_general">{t}Add language{/t}</h2>
        </div>

    <div class="small-4 columns">
        <div class="input">
        <label for="langname" class="label_title label_general">{t}Name{/t} *</label>
        <input type="text" name="langname" id="langname" class="input_general full_size cajaxg validable not_empty" placeholder="{t}Name for the new language{/t}"/>
        </div></div>

    <div class="small-4 columns">
        <div class="input-select">
               <label for="isoname" class="label_title label_general">{t}ISO name{/t} *</label>
               <select name="isoname" id="isoname" class="full_size cajag validable not_empty">
                   <option value="">{t}Select an ISO code{/t}</option>
                   {foreach from=$languages item=language}
                       <option value="{$language.code}">{$language.name} ( {$language.code} )</option>
                   {/foreach}
               </select></div></div>

 <div class="small-4 columns">
        <label class="label_title label_general">{t}Activated{/t}</label>
     <div class="row-special">
        <span class="slide-element-special title_block">
            <input class="input-slide" type="checkbox" name="enabled" id="enabled" value="1" checked="checked"/>
                <label for="enabled" class="label-slide "></label></span>
     </div></div>

        <div class="small-12 columns">
            <div class="input">
        <label for="description" class="label_title label_general">{t}Description{/t} *</label>
        <input type="text" name="description" id="description" class="input_general full_size cajaxg validable not_empty" placeholder="{t}Description for the new language{/t}"/>
        </div></div>
        <div class="small-12 columns">
            <fieldset class="buttons-form">
                {button label="Create" class='validate  btn main_action'}{*message="Do you want to create the language?"*}
            </fieldset>
        </div>


</div></div>

</form>
