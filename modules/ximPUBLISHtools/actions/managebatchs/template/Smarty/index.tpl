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
    <h5 class="direction_header"> Name Node: {t}Publishing report{/t}</h5>
    <h5 class="nodeid_header"> ID Node: {$nodeid}</h5>
    <hr>
</div>

<div class="action_content ximPUBLISHtools" ng-controller="ximPUBLISHtools">
    <ul class="media-list">
        <li class="media" ng-repeat="portal in json | orderBy: '-PubTime' as filtered_json track by portal.IdBatch">
            <a class="pull-left" href="#">
                <span class="icon-new color-trans #/portal.Finished ? 'finished-task' : 'unfinished-task'/#"
                    ng-class="{literal}{'unfinished-task': !portal.Finished, 'finished-task': portal.Finished}{/literal}"></span>
            </a>
            <a class="pull-right" href="#" ng-hide="portal.Finished">
                <p class="status-buttons">
                    <button type="button" class="increase-btn icon-new btn-unlabel-rounded" ng-click="incBatchPriority(portal.IdBatch)"></button>
                    <button type="button" class="decrease-btn icon-new btn-unlabel-rounded" ng-click="decBatchPriority(portal.IdBatch)"></button>
                    <button type="button" class="pause-btn icon-new btn-unlabel-rounded" ng-click="stopBatch(portal.IdBatch)" ng-if="portal.BatchState"></button>
                    <button type="button" class="resume-btn icon-new btn-unlabel-rounded" ng-click="startBatch(portal.IdBatch)" ng-if="!portal.BatchState"></button>
                </p>
            </a>
            <div class="media-body">
                <h4 class="media-heading">#/portal.NodeName/# <small ng-if="!portal.Finished"><span class="icon clock"></span> #/timeFromNow(portal.EstimatedTime)/#</small><small ng-if="portal.Finished"><span class="icon clock"></span> Finished</small> <small ng-if="!portal.Finished"><span class="icon-new priority"></span> #/portal.BatchPriority/#</small></h4>
                    <div class="progress">
                        <div class="progress-bar progress-bar-striped" role="progressbar" style="width:#/portal.ProgressSuccess/#%" ng-class="{literal}{'active ximcolor': portal.Progress!=100, 'progress-bar-success': portal.Progress==100}{/literal}">
                            <span ng-if="portal.Progress!=100" class="sr-only">#/portal.NumSuccess/# in progress</span>
                            <span ng-if="portal.Progress==100" class="sr-only">#/portal.NumSuccess/# success</span>
                        </div>
                              <div class="progress-bar progress-bar-striped progress-bar-warning" role="progressbar" style="width:#/portal.ProgressWarning/#%" ng-class="{literal}{'active': portal.Progress!=100}{/literal}">
                                #/portal.NumWarnings/# with warnings
                              </div>
                              <div class="progress-bar progress-bar-striped progress-bar-danger" role="progressbar" style="width:#/portal.ProgressError/#%" ng-class="{literal}{'active': portal.Progress!=100}{/literal}">
                                  #/portal.NumErrors/# with errors
                              </div>
                        </div>
                <a class="aespecial" ng-if="!showing[portal.IdPortal]" href="#" role="button" ng-click="showing[portal.IdPortal] = !showing[portal.IdPortal]">Show details</a>
                <a class="aespecial" ng-if="showing[portal.IdPortal]" href="#" role="button" ng-click="showing[portal.IdPortal] = !showing[portal.IdPortal]">Hide details</a>
                <ul ng-init="initShowing(portal.IdPortal)" ng-show="showing[portal.IdPortal]" class="media-list">
                    <li class="media" ng-repeat="element in portal.elements track by element.IdReport">
                        <a class="pull-left" href="#">
                            <span class="icon-new file-icon color-trans"></span>
                        </a>
                        <div class="media-body">
                            <h4 class="media-heading">#/element.FileName/# <small>#/element.IdNode/# #/element.ChannelName != '' ? ('/ ' + element.ChannelName) : ''/#</small></h4>
                            <div class="progress">
                                <div class="progress-bar progress-bar-striped" role="progressbar" aria-valuenow="#/element.Progress/#" aria-valuemin="0" aria-valuemax="100" style="width: #/element.Progress/#%"
                                ng-class="{literal}{'active ximcolor': element.Progress!=100, 'progress-bar-success': element.State!='Error' && element.State!='Warning' && element.Progress==100, 'progress-bar-danger': element.State=='Error', 'progress-bar-warning': element.State=='Warning' }{/literal}"
                                >
                                    <span ng-if="element.State!='Error' && element.State!='Warning'" class="sr-only">#/element.Progress/#% Complete</span>
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
