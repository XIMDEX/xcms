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

<form method="post" action="{$action_url}" id="channel_form" class='form_active validate_ajax'>
    <input type="hidden" name="id_node" value="{$id_node}">
   {include file="actions/components/title_Description.tpl"}
    <div class="action_content">
        <div class="row tarjeta">
            <h2 class="h2_general">{t}Add channel{/t}</h2>
            <div class="small-12 columns">
                <div class="input">
                    <label for="name" class="label_title label_general">{t}Name{/t} *</label>
                    <input type="text" name="name" id="channelname" class="full_size cajag validable not_empty input_general"/>
                </div>
            </div>
            <div class="small-12 columns">
                <div class="input">
                    <label for="extension" class="label_title label_general">{t}File extension{/t} *</label>
                    <input type="text" name="extension" id="extension" class="cajag validable not_empty full_size input_general"/>
                </div>
            </div>
            <div class="small-12 columns">
                <div class="input">
                    <label for="description" class="label_title label_general">{t}Description{/t} *</label>
                    <input type="text" name="description" id="description" class="full_size cajag validable not_empty input_general"/>
                </div>
            </div>
            <div class="small-12 columns">
                <label for="rendermode" class="label_title label_general">{t}Output{/t} *</label>
                <div class="row">
                    <div class="small-2 columns">
                        <label class="input-form label_general" for="web_{$id_node_parent}">
                            <input type='radio' id="web_{$id_node_parent}" name="output_type" value='web'> {t}Web{/t}
                        </label>
                    </div>
                    <div class="small-2 columns">
                        <label class="input-form label_general" for="xml_{$id_node_parent}">
                            <input type='radio' id="xml_{$id_node_parent}" name="output_type" value='xml'> {t}Xml{/t}
                        </label>
                    </div>
                    <div class="small-6 columns end">
                        <label class="input-form label_general" for="other_{$id_node_parent}">
                            <input type='radio' id="other_{$id_node_parent}" name="output_type" value='other'>
                            {t}Other{/t} (JSON, RDF, SQL, ...)
                        </label>
                    </div>
                </div>
            </div>
            <div class="small-12 columns disabled" id="render_type_mode">
                <label for="rendertype" class="label_title label_general">{t}Render type for selected output{/t} *</label>
                <div class="row">
                    <div class="small-2 columns">
                        <label class="input-form label_general" for="render_type_static">
                            <input type='radio' id="render_type_static" name="render_type" value="static" /> Static
                        </label>
                    </div>
                    <div class="small-2 columns">
                        <label class="input-form label_general" for="render_type_include">
                            <input type='radio' id="render_type_include" name="render_type" value="include" /> Include
                        </label>
                    </div>
                    <div class="small-2 columns">
                        <label class="input-form label_general" for="render_type_dynamic">
                            <input type='radio' id="render_type_dynamic" name="render_type" value='dynamic' /> Dynamic
                        </label>
                    </div>
                    <div class="small-2 columns end">
                        <label class="input-form label_general" for="render_type_index">
                            <input type='radio' id="render_type_index" name="render_type" value='index' /> Index
                        </label>
                    </div>
                </div>
            </div>
            <div class="small-12 columns disabled" id="code_language">
                <div class="input">
	                <label for="language" class="label_title label_general">{t}Code language for selected render type{/t} *</label>
	                <select name="language" id="language" class="validable not_empty">
	                    <option></option>
	                    {foreach from=$code_languages item=language}
	                         <option id="{$language.id}" value="{$language.id}">{$language.id|upper} ({$language.description})</option>
	                    {/foreach}
	                </select>
                </div>
            </div>
            <div class="small-12 columns">
                <label for="rendermode" class="label_title label_general">{t}Rendering XSLT in{/t}</label>
                <div class="row">
                    <div class="small-2 columns">
                        <label for="default_channel label_general" class="input-form disabled">
                            <input disabled type="radio" id="rendermode" name="renderMode" checked value='ximdex' /> {t}Ximdex{/t}
                        </label>
                    </div>
                    <div class="small-2 columns end">
                        <label for="default_channel label_general" class="input-form disabled">
                            <input disabled type="radio" id="rendermode" name="renderMode" value="client" /> {t}Client{/t}
                        </label>
                    </div>
                </div>
            </div>
            <div class="small-8 columns">
                <div class="alert alert-info">
                    <strong>Info!</strong> {t}Ximdex renderizes documents at the <i>Ximdex</i> local server by default. If your website it's going to be dynamic, then select the <i>Client</i> rederized mode{/t}.
                </div>
            </div>
            <div class="small-4 columns">
                <fieldset class="buttons-form">
                    {button label="Create channel" class='validate btn main_action btn_margin' }{*message=Would you like to add a channel?"*}
                </fieldset>
            </div>
        </div>
    </div>
</form>