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
        <li class="media" ng-repeat="portal in json | orderBy: '-creationTime' as filtered_json track by portal.idNodeGenerator">
            <a class="pull-left" href="#">
                <span class="icon-new color-trans #/portal.endTime ? 'finished-task' : 'unfinished-task'/#"
                        ng-class="{literal}{'unfinished-task': !portal.Finished, 'finished-task': portal.Finished}{/literal}"></span>
            </a>
            <!--
            <p class="status-buttons">
                <a class="pull-right" href="#" ng-hide="portal.Finished">
                    <button type="button" class="increase-btn icon-new btn-unlabel-rounded" ng-click="incBatchPriority(portal.IdBatch)"></button>
                    <button type="button" class="decrease-btn icon-new btn-unlabel-rounded" ng-click="decBatchPriority(portal.IdBatch)"></button>
                    <button type="button" class="pause-btn icon-new btn-unlabel-rounded" ng-click="stopBatch(portal.IdBatch)" 
                            ng-if="portal.BatchState"></button>
                    <button type="button" class="resume-btn icon-new btn-unlabel-rounded" ng-click="startBatch(portal.IdBatch)" 
                            ng-if="!portal.BatchState"></button>
                </a>
            </p>
            -->
            <div class="media-body">
                <h4 class="media-heading">
                    #/portal.nodeName/# (#/portal.idNodeGenerator/#)
                    <small><span class="icon clock"></span> Creation time: #/portal.creationTime/#</small> 
                    <small ng-if="portal.startTime"><span class="icon clock"></span> Start time: #/portal.startTime/#</small>
                    <small ng-if="portal.endTime"><span class="icon clock"></span> End time: #/portal.endTime/#</small>
                    <small ng-if="!portal.endTime"><span class="icon clock"></span> Status time: #/portal.statusTime/#</small>
                </h4>
                <div class="progress">
                    <div ng-if="portal.sfPending > 0" class="progress-bar progress-bar-striped progress-bar-pending active" role="progressbar" 
                            style="width: #/ portal.sfPending * 100 / portal.sfTotal /#%">
                        #/portal.sfPending/# pending
                    </div>
                    <div ng-if="portal.sfActive > 0" class="progress-bar progress-bar-striped progress-bar-active active" role="progressbar" 
                            style="width: #/ portal.sfActive * 100 / portal.sfTotal /#%">
                        #/portal.sfActive/# active
                    </div>
                    <div ng-if="portal.sfSuccess > 0" class="progress-bar progress-bar-striped progress-bar-success" role="progressbar" 
                            style="width: #/ portal.sfSuccess * 100 / portal.sfTotal /#%">
                        #/portal.sfSuccess/# success
                    </div>
                    <div ng-if="portal.sfErrored > 0" class="progress-bar progress-bar-striped progress-bar-errored" role="progressbar" 
                            style="width: #/ portal.sfErrored * 100 / portal.sfTotal /#%">
                        #/portal.sfErrored/# errors
                    </div>
                </div>
                <a class="aespecial" ng-if="!showing[portal.IdPortal]" href="#" role="button" 
                        ng-click="showing[portal.IdPortal] = !showing[portal.IdPortal]">Show servers details</a>
                <a class="aespecial" ng-if="showing[portal.IdPortal]" href="#" role="button" 
                        ng-click="showing[portal.IdPortal] = !showing[portal.IdPortal]">Hide servers details</a>
                <ul ng-init="initShowing(portal.IdPortal)" ng-show="showing[portal.IdPortal]" class="media-list">
                    <li class="media" ng-repeat="element in portal.elements track by element.IdReport">
                        <a class="pull-left" href="#">
                            <span class="icon-new file-icon color-trans"></span>
                        </a>
                        <div class="media-body">
                            <h4 class="media-heading">
                                #/element.FileName/# 
                                <small>#/element.IdNode/# #/element.ChannelName != '' ? ('/ ' + element.ChannelName) : ''/#</small>
                            </h4>
                            <div class="progress">
                                <div class="progress-bar progress-bar-striped" role="progressbar" aria-valuenow="#/element.Progress/#" 
                                        aria-valuemin="0" aria-valuemax="100" style="width: #/element.Progress/#%"
                                        ng-class="{literal}{'active ximcolor': element.Progress!=100
                                            , 'progress-bar-success': element.State!='Error' && element.State!='Warning' && element.Progress==100
                                            , 'progress-bar-danger': element.State=='Error'
                                            , 'progress-bar-warning': element.State=='Warning' }{/literal}">
                                    <span ng-if="element.State!='Error' && element.State!='Warning'" 
                                            class="sr-only">#/element.Progress/#% Complete</span>
                                    <span ng-if="element.State=='Warning'" class="sr-only">Finished with warnings</span>
                                    <span ng-if="element.State=='Error'" class="sr-only">An error found</span>
                                </div>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </li>
    </ul>
</div>