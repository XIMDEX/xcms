{**
 *  \details &copy; 2018 Open Ximdex Evolution SL [http://www.ximdex.org]
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
    {include file="actions/components/title_Description.tpl"}
    <fieldset>
        
    </fieldset>
    {if {! count($targetNodes)}}
        <div class="message-warning message">
            <p>{t}There aren't any possible master document{/t}.</p>
        </div>
    {/if}
	<div class="action_content">
		<fieldset>
		    <input type="hidden" name="break_link_operation" value="{$id_target}" />
            {if $id_target > 0}
                <div class="row tarjeta card_content">
	                <h2 class="h2_general">{t}Break the link with master document{/t}</h2>
	                <div class="tarjeta_content">
	                    <input type="checkbox" id="{$id_node}_delete_link" name="delete_link" value="true" class="normal input-slide" />
	                    <label class="label-slide" for="{$id_node}_delete_link">
	                        {t}Do you want to break the link with the following document?{/t}: <strong>{$name_target}</strong></label>
	                </div>
                </div>
            {else}
                {if {count($targetNodes) > 0}}
                    <div class="row tarjeta">
	                    <h2 class="h2_general">{t}Master document{/t}</h2>
	                    <div class="tarjeta_content">
		                    {t}This document will be linked to the following master language document{/t}: <strong>{$name_target}</strong>
		                </div>
	                </div>
                    <div class="row tarjeta card_content">
                        <h2 class="h2_general">{t}Publishing options{/t}</h2>
                        <div class="tarjeta_content" style="padding-bottom: 5pt;">
		                    <input type="checkbox" id="{$id_node}_sharewf" name="sharewf" value="true" class="normal input-slide" 
		                       {if $sharewf == 1}checked{/if} />
		                    <label class="label-slide" for="{$id_node}_sharewf">
	                               {t}Share the master document workflow{/t}</label>
			            </div>
	                </div>
                {/if}
            {/if}
		</fieldset>
		{if {count($targetNodes)}}
	        <fieldset class="buttons-form">
	            <div class="small-12 columns">
	                {button class="validate btn main_action" onclick="" label="Save changes"}
	                {* message="Are you sure you want to performe the changes?" *}
	            </div>
	        </fieldset>
        {/if}
	</div>
</form>