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
            <div class="media-body">
                <h4 class="media-heading">
                    <span class="type-icon">
                        <i class="fas fa-#/ frames.type == 'Up' ? 'upload' : 'download' /# #/ frames.type /#-icon" 
                                title="Publishing type: #/ frames.type /#"></i>
                        <!--
                        <img src="actions/managebatchs/resources/icons/#/ frames.type == 'Up' ? 'upload' : 'download' /#.png" 
                                title="Publishing type: #/ frames.type /#" class="type-icon" />
                        -->
                    </span>
                    <span title="Node ID: #/ frames.idNodeGenerator /# 
                        &#013;User: #/ frames.userName /# 
                        &#013;Created: #/ frames.creationTime /#
                        &#013;Scheduled: #/ frames.scheduledTime /# 
                        &#013;Started: #/ frames.startTime /# 
                        &#013;Visited: #/ frames.statusTime /# 
                        &#013;Ended: #/ frames.endTime /#"><strong>#/ frames.nodeName /#</strong>
                    </span>
                    <small> (#/ frames.total /# documents)</small>
                    <small ng-if="!frames.startTime"><span class="icon clock"></span> Scheduled: #/ frames.scheduledTime /#</small>
                    <small ng-if="frames.startTime && !frames.statusTime">
                        <span class="icon clock"></span> Started: #/ frames.startTime /#
                    </small>
                    <small ng-if="!frames.endTime"><span class="icon clock"></span> Visited: #/ frames.statusTime /#</small>
                    <small ng-if="frames.endTime"><span class="icon clock"></span> Ended: #/ frames.endTime /#</small>
                    <small ng-if="frames.delayed > 0" 
                            title="This portal frames has some servers delayed. Clic on show server details for more information about it" 
                            class="portal-disabled">&nbsp; Servers delayed !</small>
                    <small ng-if="frames.stopped > 0" 
                            title="This portal frames has some stopped batchs. Clic on show server details for more information about it" 
                            class="portal-disabled">&nbsp; Batchs stopped !</small>
                </h4>
	            <div class="portal-detail">
	                <div class="progress">
	                    {assign var="progress_bar_class" value=""}
	                    {include file="actions/managebatchs/template/Smarty/framesProgressBar.tpl"}
	                </div>
	            </div>
                <div class="portal-buttons">
                    <span class="portal-button play" ng-click="playPortal(frames.idPortal)" ng-if="!frames.playing && !frames.endTime" 
                            title="Play this portal frames"><i class="far fa-play-circle"></i></span>
                    <span class="portal-button pause" ng-click="pausePortal(frames.idPortal)" ng-if="frames.playing && !frames.endTime" 
                            title="Pause this portal frames"><i class="far fa-pause-circle"></i></span>
                    <span class="portal-button" ng-if="frames.endTime">
                        <span class="exclamation-icon" ng-if="frames.fatal > 0"><i class="fas fa-exclamation-circle"></i></span>
                        <span class="check-icon" ng-if="!frames.fatal"><i class="fas fa-check-circle"></i></span>
                    </span>
                </div>
                <div class="portal-frames-sep"></div>
                <div class="server-details-link">
                    <a class="aespecial" ng-if="!showing[frames.idPortal]" href="#" role="button" 
                            ng-click="showing[frames.idPortal] = !showing[frames.idPortal]">Show servers details</a>
                    <a class="aespecial" ng-if="showing[frames.idPortal]" href="#" role="button" 
                            ng-click="showing[frames.idPortal] = !showing[frames.idPortal]">Hide servers details</a>
                </div>
                <div class="boost-buttons">
                    Cycles: #/ frames.visits /# · Success rate: #/ frames.successRate * 100 | number:0 /#%
                    <span data-ng-repeat="boost in [1, 2, 4]" class="boost-icon" 
                            ng-class="{literal}{'boost-icon-#/ boost /#x-selected': frames.boost == boost}{/literal}" 
                            ng-click="boostPortal(frames.idPortal, boost);"><i class="fas fa-rocket"></i> x#/ boost /#</span>
                </div>
                <div class="portal-frames-sep"></div>
                
                <!-- Servers -->
                <ul ng-init="initShowing(frames.idPortal)" ng-show="showing[frames.idPortal]" class="media-list servers">
                    <li ng-if="frames.total > 0" class="media server-detail" 
                            ng-repeat="frames in frames.servers | orderBy: 'name' as filtered_json track by frames.id">
                        <h4 class="media-heading portal-server-info">
                            Server #/ frames.name /# <small>(#/ frames.total /# documents)</small> 
                            <small ng-if="!frames.enabled" class="server-disabled-by-user">Disabled by user</small>
                            <small ng-if="frames.enabled && !frames.activeForPumping" class="server-disabled">
                                <span ng-if="!frames.delayedTime" title="Server disabled permanently due to connection errors">
                                    Disabled !</span>
                                <span ng-if="frames.delayedTime" title="Server delayed to restart later due to connection errors">
                                    Delayed until #/ frames.delayedTime /# !</span>
                            </small>
                        </h4>
                        <div class="portal-server-detail">
	                        <div class="progress server portal-server-progress">
	                            {assign var="progress_bar_class" value="server-progress-bar"}
	                            {include file="actions/managebatchs/template/Smarty/framesProgressBar.tpl"}
	                        </div>
                        </div>
                        <div class="portal-server-buttons">
                            <span class="server-button reload" ng-click="restartServer(frames.id)" ng-if="frames.delayed > 0" 
                                    title="Restart all delayed batches for server #/ frames.name /#"><i class="fas fa-sync-alt"></i></span>
                        </div>
                    </li>
                </ul>
                
                <!--  Batchs -->
                <ul ng-init="initShowing(frames.idPortal)" ng-show="showing[frames.idPortal]" class="media-list batchs">
                    <li class="media">
                        <h4 class="media-heading batch-info">
                            BATCHS LIST
                            <small> (#/ frames.totalBatchs /#  batchs)</small>
                            <small ng-if="frames.stopped > 0" title="This portal frames has some stopped batchs" 
                                    class="server-disabled">Batchs stopped !</small>
                        </h4>
                        <div class="portal-batchs-detail">
	                        <div class="progress server portal-batchs-progress">
	                            {include file="actions/managebatchs/template/Smarty/batchsProgressBar.tpl"}
	                        </div>
                        </div>
                        <div class="portal-batchs-buttons">
                            <span class="batchs-button reload" ng-click="restartBatchs(frames.idPortal)" ng-if="frames.stopped > 0" 
                                    title="Restart all stopped batches for #/ frames.name /# portal"><i class="fas fa-sync-alt"></i></span>
                        </div>
                    </li>
                </ul>
                <div class="portal-frames-sep" ng-init="initShowing(frames.idPortal)" ng-show="showing[frames.idPortal]"></div>
            </div>
        </li>
    </ul>
</div>
<form name="frm_batchs" method="post" action="">
    <input type="hidden" name="id" id="id" value="" />
    <input type="hidden" name="value=" id="value" value="" />
</form>