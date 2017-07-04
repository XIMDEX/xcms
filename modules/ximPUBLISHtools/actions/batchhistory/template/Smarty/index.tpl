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
    <h5 class="direction_header"> Name Node: {t}Publishing history{/t}</h5>
    <h5 class="nodeid_header"> ID Node: {$nodeid}</h5>
    <hr>
</div>

<div class="ximPUBLISHtools" ng-controller="ximPUBLISHtools">
    <div class="action_content">
        <div class="row tarjeta">
            <h2 class="h2_general">{t}Add channel{/t}</h2>
            <div class="small-12 columns">
    <form>
        <div class="input">
            <label class="label_title label_general">{t}From{/t} </label>
            <input type="text" ng-model="searchObj.dateFrom">
        </div>
        <div class="input">
            <label class="label_title label_general">{t}To{/t} </label>  <input type="text" ng-model="searchObj.dateTo">
        </div>
        <div class="input">
           <label class="label_title label_general"> {t}Text to search{/t} </label>  <input type="text" ng-model="searchObj.searchText">
        </div>
        <div class="small-12 columns">
            <fieldset class="buttons-form">
            <button class="btn main_action btn_margin" ng-click="updateSearch(searchObj)">{t}Search{/t}</button>
            </fieldset></div>
    </form>
            </div></div>
    <fieldset>
        <label class="label_title label_general">{t}Document list{/t}</label>

        <div id="frame_list" ng-if="1">
            <div class="batch_container" ng-repeat="portal in json">
                <div class="frame_filename">
                    <strong>
                        <em>#/portal.NodeName/#</em>
                    </strong>
                    <em>{t}This publication{/t}<strong>#/portal.BatchStateText/#</strong></em>
                </div>
                <div class="frame_default"></div>

                <div ng-repeat="frame in portal.elements">
                    <div class="frame_filename">#/frame.PubTime*1000 | date : 'dd-MM H:mm'/#
                        <strong>
                            <em>#/frame.FilePath + '/' + frame.FileName/#</em>
                        </strong>
                    </div>
                    <div class="frame_default"></div>
                </div>
            </div>
        </div>
    </fieldset>
    </div></div>
