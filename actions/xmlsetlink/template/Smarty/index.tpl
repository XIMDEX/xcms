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


<form method="post" name="sl_form" class="sl_form" id="sl_form" action="{$action_url}">
	<input type="hidden" name="id_node" value="{$id_node}" class="id_node" />
	<div class="action_header">
        {if $id_target > 0}
            <h2>{t}Break the link with master document{/t}</h2>
        {else}
            <h2>{t}Select master document{/t}</h2>
        {/if}
        {if {count($targetNodes)}}
        <fieldset class="buttons-form">
				{button class="validate btn main_action" onclick="" label="Save changes" }{*message="Are you sure you want to performe the changes?"*}
		</fieldset>
        {/if}
	</div>
    {if {!count($targetNodes)}}
        <div class="message-warning message">
            <p>{t}There aren't any possible master document{/t}.</p>
        </div>
    {/if}
	<div class="action_content">
		<fieldset>
            {if $id_target > 0}
                <span class="recursive_control">
                    <input type="checkbox" id="{$id_node}_delete_link" name="delete_link" value="true" class="normal input-slide" />
                    <label class="label-slide" for="{$id_node}_delete_link">{t}Do you want to break the link with the following document?{/t}: {$name_target}</label>
                </span>

                <div class="translation_box hidden">
                    <br/>
                    <input type="checkbox" id="{$id_node}_delete_method" name="delete_method" value="unlink" class="normal input-slide delete_method" />
                    <label class="label-slide" for="{$id_node}_delete_method">{t}Do you want to copy the master document content when a link would be deleted?{/t}</label>
                </div>
            {else}
                {if {count($targetNodes)}}
                    <label for="id_node" class="label_title">{t}This document can be linked to one of the following documents{/t}:</label>
                        <div class="copy_options" tabindex="1">
                            {foreach from=$targetNodes key=index item=targetNode}
                                <div>
                                    <input id="{$id_node}_{$targetNode.idnode}" type="radio" name="targetid" value="{$targetNode.idnode}" {if $targetNode.idnode == $id_target}checked{/if}   />
                                    <label for="{$id_node}_{$targetNode.idnode}" class="icon folder">{$targetNode.path}</label>
                                </div>
                            {/foreach}
                        </div>

                <span class="recursive_control">
                    <br />
                    <input type="checkbox" id="{$id_node}_sharewf" name="sharewf" value="true" class="normal input-slide" {if $sharewf == 1}checked{/if} />
                    <label class="label-slide" for="{$id_node}_sharewf">{t}Do you want to share the master document workflow?{/t}</label>
                </span>
                {/if}
            {/if}
			
				
		</fieldset>
	</div>

</form>
