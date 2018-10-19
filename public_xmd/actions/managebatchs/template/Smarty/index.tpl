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

{include file="actions/components/title_Description.tpl"}
<div class="action_content ximPUBLISHtools" ng-controller="ximPUBLISHtools">
    <ul class="media-list">
        <li class="media" ng-repeat="frames in json | orderBy: 'order' as filtered_json track by frames.idPortal">
            <span class="pull-left">
                <img src="actions/managebatchs/resources/icons/#/ frames.type == 'Up' ? 'upload' : 'download' /#.png" 
                        title="Publishing type: #/ frames.type /#" class="type-icon" />
            </span>
            <!--
            <p class="status-buttons">
                <a class="pull-right" href="#" ng-hide="frames.Finished">
                    <button type="button" class="increase-btn icon-new btn-unlabel-rounded" 
                            ng-click="incBatchPriority(frames.IdBatch)"></button>
                    <button type="button" class="decrease-btn icon-new btn-unlabel-rounded" 
                            ng-click="decBatchPriority(frames.IdBatch)"></button>
                    <button type="button" class="pause-btn icon-new btn-unlabel-rounded" ng-click="stopBatch(frames.IdBatch)" 
                            ng-if="!frames.BatchState"></button>
                    <button type="button" class="resume-btn icon-new btn-unlabel-rounded" ng-click="startBatch(frames.IdBatch)" 
                            ng-if="!frames.BatchState"></button>
                </a>
            </p>
            -->
            <div class="media-body">
                <h4 class="media-heading">
                    <span title="Node ID: #/frames.idNodeGenerator/#
                        &#013;User: #/frames.userName/#
                        &#013;Created at: #/frames.creationTime/#
                        &#013;Started at: #/frames.startTime/#
                        &#013;Status time: #/frames.statusTime/#
                        &#013;Ended at: #/frames.endTime/#">#/frames.nodeName/#</span>
                    <small ng-if="!frames.startTime"><span class="icon clock"></span> Created at: #/frames.creationTime/#</small>
                    <small ng-if="frames.startTime && !frames.statusTime">
                        <span class="icon clock"></span> Started at: #/frames.startTime/#
                    </small>
                    <small ng-if="!frames.endTime"><span class="icon clock"></span> Status time: #/frames.statusTime/#</small>
                    <small ng-if="frames.endTime"><span class="icon clock"></span> Ended at: #/frames.endTime/#</small>
                </h4>
                <div class="progress">
                    {include file="actions/managebatchs/template/Smarty/framesProgressBar.tpl"}
                </div>
                <small>
                    <a class="aespecial" ng-if="!showing[frames.idPortal]" href="#" role="button" 
                            ng-click="showing[frames.idPortal] = !showing[frames.idPortal]">Show servers details</a>
                    <a class="aespecial" ng-if="showing[frames.idPortal]" href="#" role="button" 
                            ng-click="showing[frames.idPortal] = !showing[frames.idPortal]">Hide servers details</a>
                </small>
                <ul ng-init="initShowing(frames.idPortal)" ng-show="showing[frames.idPortal]" class="media-list servers">
                    <li class="media" ng-repeat="frames in frames.servers | orderBy: 'name' as filtered_json track by frames.id">
                        <h4 class="media-heading">
                            Server #/frames.name/# 
                            <small ng-if="!frames.enabled" class="server-disabled-by-user">Disabled by user</small>
                            <small ng-if="frames.enabled && !frames.activeForPumping" class="server-disabled">
                                Disabled
                                <span ng-if="frames.delayedTime"> (Restart at #/frames.delayedTime/#)</span>
                            </small>
                        </h4>
                        <div class="progress server">
                            {include file="actions/managebatchs/template/Smarty/framesProgressBar.tpl"}
                        </div>
                    </li>
                </ul>
            </div>
        </li>
    </ul>
</div>