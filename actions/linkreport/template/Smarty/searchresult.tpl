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

<div class="action_header">
	<h5 class="direction_header"> Name Node: {$node_name}</h5>
	<h5 class="nodeid_header"> ID Node: {$nodeid}</h5>
	<hr>
</div>

<div class="action_content">
	<div class="row tarjeta">
		<h2 class="h2_general">{t}Search results{/t}</h2>
    <fieldset>
	    <input type="hidden" name="stringsearch" value="{$stringsearch}" />
        <label class="label_title label_general">{t}External links found{/t}: {$totalLinks}</label>
		<div class="small-12 columns">
    {if $totalLinks>0}
        {foreach from=$links item=link}
		<div class="result_info row-item text-border">
			<span class="result_name">{$link.name}</span>
			<span class="result_url" data-idnode="{$link.nodeid}">{$link.url}</span>
			<div class="row-item-actions">			
			    <div class="description_btn">
				    <span class="result_description icon btn-unlabel-rounded btn-unlabel-rounded-margin">
					    <span class="xtooltip">
						    <p>{t}Description{/t}</p>
						    <p>{$link.desc}</p>
					    </span>
				    </span>
			    </div>
                {if $link.type eq "web"}
			 <a href="" class="icon btn-unlabel-rounded js_check checked_{$link.status}">
                        <span class="status_tooltip">
                        	<p class="status">{t}{$link.status}{/t}</p>
                        	{if $link.status eq "NOT CHECKED"}
					<p class="date_check">{t}Created{/t} {$link.lastcheck|date_format:'%d/%m/%Y - %H:%M'}h.</p>
				{else}
					<p class="date_check">{t}Last check{/t} {$link.lastcheck|date_format:'%d/%m/%Y - %H:%M'}h.</p>
				{/if}
                   	</span>
                    </a>
                {else}
			    <a href="#" class="icon btn-unlabel-rounded  "><span>{t}Uncheckable{/t}</span></a>
                {/if}
			</div>
		</div>
        {/foreach}
    {else}
		<div class="alert alert-info">
			<strong>Info!</strong> {t}There aren't any links matching with the current search. Please, try again with another search string{/t}.
		</div>
	{/if}</div>
{*	<paginator class="links-paginator" />*}

    </fieldset>
		<div class="small-12 columns">
		<fieldset class="buttons-form">

            {button label="Check all" class="btn main_action ladda-button js_check_all" }
            {button label="Go back" type="goback"  class="btn main_action"}
			</div>
		</fieldset>
	</div></div>



