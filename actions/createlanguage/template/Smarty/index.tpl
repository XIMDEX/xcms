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
	<h2>{t}Add language{/t}</h2>
	<fieldset class="buttons-form">
			{button label="Create" class='validate  btn main_action'}{*message="Do you want to create the language?"*}

	</fieldset>
</div>
<div class="action_content">
    <p class="col1_2 col_left">
        <label for="langname" class="label_title">{t}Name{/t}</label>
        <input type="text" name="langname" id="langname" class="full_size cajaxg validable not_empty" placeholder="{t}Name for the new language{/t}"/>
    </p>

    <div class="col1_2 col_right">
        <p class="col1_2 col_right">
               <label for="isoname" class="label_title">{t}ISO name{/t}</label>
               <select name="isoname" id="isoname" class="full_size cajag validable not_empty">
                   <option value="">{t}Select an ISO code{/t}</option>
                   {foreach from=$languages item=language}
                       <option value="{$language.code}">{$language.name} ( {$language.code} )</option>
                   {/foreach}
               </select>
           </p> 
 <p class="col1_2 col_right ">
        <span class="slide-element title_block">
            <input class="input-slide" type="checkbox" name="enabled" id="enabled" value="1" checked="checked"/>
                <label for="enabled" class="label-slide "><span class="label_title">{t}Activated{/t}</span></label></span>
    </p>               
    </div>

    <p>
        <label for="description" class="label_title">{t}Description{/t}</label>
        <input type="text" name="description" id="description" class="full_size cajaxg validable not_empty" placeholder="{t}Description for the new language{/t}"/>
    </p>
   
   			
</div>

</form>
