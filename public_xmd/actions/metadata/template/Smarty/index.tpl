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

<form method="post" id="m_form" name="m_form" class="validate_ajax" action="{$action_url}" ng-init="status=[]">
    <input type="hidden" name='nodeid' value="{$nodeid}"/>
    {include file = "actions/components/title_Description.tpl"}
    <div class="message-error message" id="metadatanode_error_message" style="display: none;"></div>
    <div ng-cloak class="action_content">
        {if count($info) > 0}
	        <fieldset>
	            <accordion close-others="true" ng-init="firstOpen = true; firstDisabled = false;">
	                {$count = 0}
	                {foreach from = $info item = metaSection}
	                    <accordion-group class="" heading="{t}{$metaSection['name']}{/t}" 
	                            {if ! $count}
	                                ng-init="$parent.status.push(true)" is-disabled="firstDisabled"
	                            {else}
	                                ng-init="$parent.status.push(false)"
	                            {/if}
	                            is-open="$parent.status[{$count}]">
	                        {foreach from = $metaSection['groups'] item = group}
	                            <div class="clearfix">
	                                <h5 class="direction_header">{t}{$group['name']}{/t}</h5>
	                                <hr />
	                                <div id="metadataLoad">
	                                    {include file = "./metadata.tpl"}
	                                </div>
	                            </div>
	                        {/foreach}
	                    </accordion-group>
	                    {$count = $count + 1}
	                {/foreach}
	            </accordion>
	            <div class="small-12 columns">
	                <fieldset class="buttons-form">
	                    {button label="Save metadata" class="validate btn main_action" id="save_metadata"}
	                </fieldset>
	            </div>
	        </fieldset>
	    {else}
	       <p>{t}There are not metadata defined for this node{/t}.</p>
        {/if}
    </div>
</form>