{**
 *  \details &copy; 2019 Open Ximdex Evolution SL [http://www.ximdex.org]
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
    <input type="hidden" name='id_node' value="{$id_node_parent}" />
    {include file="actions/components/title_Description.tpl"}
    <div class="action_content">
        <div class="row tarjeta">
            <h2 class="h2_general">{t}Modify channel{/t}</h2>
            <div class="small-12 columns">
                <div class="input">
                    <label for="channel-name" class="label_title label_general">{t}Name{/t}</label>
                    <input name="channel-name" id="channel-name" value="{$name}" type="text" readonly>
                </div>
            </div>
            <div class="small-12 columns">
                <div class="input">
                    <label for="channel-extension" class="label_title label_general">{t}File extension{/t}</label>
                    <input name="channel-extension" id="channel-extension" value="{$extension}" type="text" readonly>
                </div>
            </div>
            <div class="small-12 columns">
                <div class="input">
                    <label for="description" class="label_title label_general">{t}Description{/t} *</label>
                    <input type="text" name="Description" id="description" value="{$description|gettext}" 
                            class='cajag validable not_empty input_general' />
                </div>
            </div>
            {** Names must be in the same case that database fields. *}
            <div class="small-12 columns">
                <label class="label_title label_general">{t}Output{/t}</label>
                <div class="row">
                    <div class="small-2 columns">
                        <label class="input-form label_general" for="web_{$id_node}">
                            <input type='radio' id="web_{$id_node}" name="output_type_{$id_node}" 
                                    {$output_check.web} value='web'> {t}Web{/t}
                        </label>
		            </div>
		            <div class="small-2 columns">
		                <label class="input-form label_general" for="xml_{$id_node}">
		                    <input type='radio' id="xml_{$id_node}" name="output_type_{$id_node}" 
                                    {$output_check.xml} value='xml'> {t}Xml{/t}
		                </label>
		            </div>
		            <div class="small-6 columns end">
		                <label class="input-form label_general" for="other_{$id_node}">
		                    <input type='radio' id="other_{$id_node}" name="output_type_{$id_node}" {$output_check.other} 
                                    value='other'> {t}Other{/t} (JSON, RDF, SQL, ...)
		                </label>
		            </div>
		        </div>
		    </div>
		    <div class="small-12 columns" id="render_type_mode_{$id_node}">
                <label class="label_title label_general">{t}Render type for selected output{/t} *</label>
                <div class="row">
                    <div class="small-2 columns">
                        <label class="input-form label_general" for="render_type_static_{$id_node}">
                            <input type='radio' id="render_type_static_{$id_node}" name="render_type_{$id_node}" {$render_type.static} 
                                    value="static" /> Static
                        </label>
                    </div>
                    <div class="small-2 columns">
                        <label class="input-form label_general" for="render_type_include_{$id_node}">
                            <input type='radio' id="render_type_include_{$id_node}" name="render_type_{$id_node}" {$render_type.include} 
                                    value="include" /> Include
                        </label>
                    </div>
                    <div class="small-2 columns">
                        <label class="input-form label_general" for="render_type_dynamic_{$id_node}">
                            <input type='radio' id="render_type_dynamic_{$id_node}" name="render_type_{$id_node}" {$render_type.dynamic} 
                                    value='dynamic' /> Dynamic
                        </label>
                    </div>
                    <div class="small-2 columns end">
                        <label class="input-form label_general" for="render_type_index_{$id_node}">
                            <input type='radio' id="render_type_index_{$id_node}" name="render_type_{$id_node}" {$render_type.index} 
                                    value='index' /> Index
                        </label>
                    </div>
                </div>
            </div>
            <div class="small-12 columns" id="code_language_{$id_node}">
                <div class="input">
                    <label for="language_{$id_node}" class="label_title label_general">{t}Code language for selected render type{/t} *</label>
                    <select name="language" id="language_{$id_node}" class="validable not_empty">
                        <option></option>
                        {foreach from=$code_languages item=_language}
                            <option id="language_{$_language.id}_{$id_node}" value="{$_language.id}" 
                                {if $language == $_language.id} selected="selected" {/if}>{$_language.id|upper} ({$_language.description})</option>
                        {/foreach}
                    </select>
                </div>
            </div>
            <div class="small-12 columns">
                <div class="row">
                    <div class="small-12 columns">
                        <input type="checkbox" name="Default_Channel" id="default_channel" {if $default_channel == 1}checked="checked"{/if} 
                                class="hidden-focus"/>
                        <label class="input-form checkbox-label" for="default_channel">{t}Default channel{/t}</label>
                    </div>
                </div>
            </div>
		    <div class="small-12 columns">
	           <label for="rendermode" class="label_title label_general">{t}Rendering XSLT in{/t}</label>
	           <div class="row">
                    <div class="small-2 columns">
	                    <label for="default_channel label_general" class="input-form disabled">
	                        <input disabled type="radio" id="rendermode" name="RenderMode" {$render_check.ximdex} value="ximdex" /> {t}Ximdex{/t}
	                    </label>
	                </div>
	                <div class="small-2 columns end">
	                    <label for="default_channel label_general" class="input-form disabled">
	                        <input disabled type="radio" id="rendermode" name="RenderMode" {$render_check.client} value="client" /> {t}Client{/t}
	                    </label>
	                </div>
	            </div>
	        </div>
	        <div class="small-12 columns">
	            <fieldset class="buttons-form">
	                {button label="Modify" class='validate btn main_action' } {*message="Would you like to modify this channel?"*}
	            </fieldset>
	        </div>
	    </div>
    </div>
</form>