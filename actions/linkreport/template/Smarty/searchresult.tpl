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
    <h2>{t}Search results{/t}</h2>
</div>

<div class="action_content">
    <fieldset>
	    <input type="hidden" name="stringsearch" value="{$stringsearch}" />
        <p>{t}External links found{/t}: {$totalLinks}</p>
    {if $totalLinks>0}
        {foreach from=$links item=link}
		<div class="result_info row-item">
			<span class="result_name">{$link.name}</span>
			<span class="result_url" data-idnode="{$link.nodeid}">{$link.url}</span>
			<div class="row-item-actions">			
			    <div class="description_btn">
				    <span class="result_description icon btn-unlabel-rounded">
					    <span class="tooltip">
						    <p>{t}Description{/t}</p>
						    <p>{$link.desc}</p>
						    <p>{t}Last check{/t}</p>
						    <p>{$link.lastcheck|date_format:'%d/%m/%Y - %H:%M'}h.</p>
					    </span>
				    </span>
			    </div>
                {if $link.type eq "web"}
			        <a href="" class="icon btn-unlabel-rounded js_check checked_{$link.status}"><span>{t}{$link.status}{/t}</span></a>
                {else}
			    <a href="#" class="icon btn-unlabel-rounded"><span>{t}Uncheckable{/t}</span></a>
                {/if}
			</div>
		</div>
        {/foreach}
    {else}
        <p>{t}There aren't any links matching with the current search. Please, try again with another search string{/t}.</p>
    {/if}
{*	<paginator class="links-paginator" />*}

    </fieldset>
</div>

<fieldset class="buttons-form positioned_btn">
	{button label="Go back" type="goback" class="btn"}
</fieldset>

