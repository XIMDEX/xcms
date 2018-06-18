

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


<form method="post" id="m_form" name="m_form" class="validate_ajax"
      action="{$action_url} ng-init=" status=[]">
    <input type="hidden" name='nodeid' value="{$nodeid}"/>
    {include file="actions/components/title_Description.tpl"}

    <div ng-cloak class="action_content">

        <fieldset>

            <accordion close-others="false" ng-init="firstOpen=true; firstDisabled=false;">
                {foreach from=$info item=metaSection}
                    <accordion-group class="" heading="{t}{$metaSection['name']}{/t}"
                                     ng-init="$parent.status.push(true)">

                        {foreach from=$metaSection['groups'] item=group}
                            <h5 class="direction_header">{$group['name']}</h5>
                            <hr/>
                            <div id="metadataLoad">
                                {include file="./metadata.tpl"}
                            </div>
                        {/foreach}

                    </accordion-group>
                {/foreach}

            </accordion>

            <div class="small-12 columns">
                <fieldset class="buttons-form">
                    {button label="Save" class='validate btn main_action btn_margin'}
                </fieldset>
            </div>

        </fieldset>

    </div>

</form>
