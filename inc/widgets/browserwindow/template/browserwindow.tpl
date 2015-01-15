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

<div id="%=id%" class="browser-window %=class%">
<div class="browser-window-content">
<div class="hbox browser-hbox">
<div ng-controller="XTreeCtrl" ng-mouseleave="hideTree()" id="angular-tree"
     class="hbox-panel-container hbox-panel-container-0 hbox-panel-hideable noselect">
    {literal}
        <script id="template/tabs/tabset.html" type="text/ng-template">
            <div class="hbox-panel">
                <div class="xim-tabs-nav">
                    <div class="xim-tabs-list-selector xim-hidden-tab"></div>
                    <ul class="xim-tabs-list"></ul>
                </div>
                <div class="ui-tabs ui-widget ui-widget-content ui-corner-all tabs-container">
                    <ul class="ul ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all {{type && 'nav-' + type}}"
                        ng-class="{'nav-stacked': vertical, 'nav-justified': justified}" ng-transclude>
                        <li>
                            <div class="browser-view-title">{{'browser.view.headings.'+tab.heading | xI18n}}</div>
                        </li>
                    </ul>
                    <div class="tab-content browser-view browser-projects-view-content ui-tabs-panel ui-widget-content ui-corner-bottom">
                        <div class="browser-view ui-tabs-panel ui-widget-content ui-corner-bottom tab-pane"
                             ng-repeat="tab in tabs"
                             ng-class="{active: tab.active}"
                             tab-content-transclude="tab">
                            <div class="browser-view-title">{{'browser.view.headings.'+tab.heading | xI18n}}</div>
                        </div>
                    </div>
                </div>
            </div>

        </script>
        <script id="template/tabs/tab.html" type="text/ng-template">
            <li ng-class="{'xim-first-tab': $first, 'ui-tabs-active': active, 'ui-state-active': active}"
                class="ui-state-default ui-corner-top">
                <a ng-click="select()" tab-heading-transclude class="ui-tabs-anchor browser-{{heading}}-view"><span>{{heading}}</span></a>
            </li>
        </script>
        <script type="text/ng-template" id="tree_item_renderer.html">
            <div class="noselect" hm-doubletap="toggleNode(node,$event)" hm-press="loadActions(node,$event)"
                 hm-tap="select(node,$event)" ng-right-click="loadActions(node,$event)"
                 ng-class="{'xim-treeview-container-selected': (node | nodeSelected: selectedNodes)}">
                <span class="ui-icon xim-actions-toggle-node"
                      ng-class="{'ui-icon-triangle-1-se': node.showNodes, 'ui-icon-triangle-1-e': !node.showNodes, 'icon-hidden': !node.children || node.collection.length==0}"
                      hm-tap="toggleNode(node,$event)"></span>
                <span class="xim-treeview-icon icon-#/node.icon.split('.')[0]/#"></span>
                <span class="xim-treeview-branch" ng-bind-html="node.name"></span>
                        <span hm-tap="loadActions(node,$event)"
                              class="xim-actions-dropdown xim-treeview-actions-dropdown
                              ui-icon ui-icon-triangle-1-e"
                              ng-class="{'selected': (node | nodeSelected: selectedNodes)}"></span>
            </div>
            <ul class="xim-treeview-branch" ng-show="node.showNodes">
                <li ng-repeat="node in node.collection" ng-include="'tree_item_renderer.html'"
                    class="xim-treeview-node ui-draggable xim-treeview-expanded"></li>
            </ul>
            <ul class="xim-treeview-loading" id="treeloading-undefined" ng-show="node.showNodes && node.loading">
                <li></li>
                <img src="xmd/images/browser/hbox/loading.gif"></ul>
        </script>
    {/literal}
    <tabset class="ui-tabs ui-widget ui-widget-content ui-corner-all tabs-container">
        <tab heading="projects" select="$parent.selectedTab=1;">
            <div class="browser-projects-view-treecontainer xim-treeview-container" style="display: block;">
                <div ng-click="reloadNode()" class="xim-treeview-btnreload ui-corner-all ui-state-default">
                    {t}Reload node{/t}
                </div>
                <div ng-if="projects!='null' && projects!=null"
                     class="xim-treeview-branch-container xim-treeview-expanded">
                    <ul class="xim-treeview-branch">
                        <li ng-repeat="node in [projects]" ng-include="'tree_item_renderer.html'"
                            class="xim-treeview-node ui-draggable xim-treeview-expanded"></li>
                    </ul>
                </div>
                <p class="text_center" ng-if="filterMode && projects.collection.length==0"><br/><br/>
                    {t}There are no results{/t}
                </p>
            </div>
        </tab>
        <tab heading="ccenter" select="$parent.selectedTab=2;">
            <div class="browser-projects-view-treecontainer xim-treeview-container" style="display: block;">
                <div ng-click="reloadNode()"
                     class="xim-treeview-btnreload ui-corner-all ui-state-default">{t}Reload node{/t}</div>
                <div ng-if="ccenter!='null' && ccenter!=null"
                     class="xim-treeview-branch-container xim-treeview-expanded">
                    <ul class="xim-treeview-branch">
                        <li ng-repeat="node in ccenter.collection" ng-include="'tree_item_renderer.html'"
                            class="xim-treeview-node ui-draggable xim-treeview-expanded"></li>
                    </ul>
                </div>
            </div>
        </tab>
        <tab heading="modules" select="$parent.selectedTab=3;">
            <div class="browser-modules-view-list-container" style="display: block;">
                <ul ng-if="modules!='null' && modules!=null" class="browser-modules-view-list">
                    <li
                            {literal}
                                ng-class="{'browser-modules-view-enabled': node.enabled, 'browser-modules-view-disabled': !node.enabled}"
                            {/literal}
                            ng-repeat="node in modules"
                            ng-click="openModuleTab(node.id)">#/node.name/#
                    </li>
                </ul>
            </div>
        </tab>
    </tabset>

    <button id="angular-tree-toggle" ng-click="toggleTree($event)" class="btn btn-anchor" type="button"></button>
    <div class="filter-tree">
        <input ng-init="" ng-change="doFilter()" ng-model="filter" ng-show="selectedTab==1" type="text" class="form-control" placeholder="Filter...">
    </div>

    <div id="angular-tree-resizer"
         hm-panstart="dragStart($event)" hm-panmove="drag($event,'10')"
         ng-mouseenter="showTree()"
         class="hbox-panel-sep hbox-panel-separator-0">
    </div>
</div>

<div id="angular-content" ng-cloak ng-controller="XTabsCtrl" class="angular-panel hbox-panel-container hbox-panel-container-1">
<div class="hbox-panel">
<div class="xim-tabs-nav">
    <div ng-show="menuTabsEnabled" hm-tap="showingMenu=!showingMenu" class="xim-tabs-list-selector"></div>
    <ul ng-show="showingMenu" class="xim-tabs-list">
        <li class="xim-tabs-list-item" hm-tap="closeMenu(); offAllTabs();">Welcome to the brand new Ximdex 3.5!</li>
        <li class="xim-tabs-list-item" ng-repeat="tab in tabs" hm-tap="closeMenu(); setActiveTab($index);">#/tab.name/#</li>
        <li class="xim-tabs-list-item xim-tabs-list-item-3 tabswidget-close-all" hm-tap="closeMenu(); closeAllTabs();">Cerrar todas las pestañas</li>
    </ul>
</div>
<div class="ui-tabs ui-widget ui-widget-content ui-corner-all tabs-container">
<ul class="ul ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all" role="tablist"
    style="192.168.left: 0px;">
    <li class="ui-state-default ui-corner-top xim-first-tab" aria-controls="browser-action-view-1" role="tab"
        tabindex="-1" aria-labelledby="10000_welcome" aria-selected="false">
        <div class="ui-tab-close"></div>
        <a href="#browser-action-view-1" class="ui-tabs-anchor browser-action-view" role="presentation" tabindex="-1"
           id="10000_welcome">
            <span>Welcome to the brand new Ximdex 3.5!</span></a></li>
    {literal}
    <li hm-tap="setActiveTab($index)" ng-repeat="tab in tabs" class="ui-state-default ui-corner-top"
        ng-class="{'ui-tabs-active ui-state-active': tab.active, 'xim-last-tab': $last, 'blink': tab.blink}" aria-controls="browser-action-view-2" role="tab" tabindex="0" aria-labelledby="ui-id-11" aria-selected="true">
        <div hm-tap="removeTab($index);" class="ui-tab-close"></div>
        <a class="ui-tabs-anchor browser-action-view" role="presentation" tabindex="-1"
           id="#/tab.id/#"><span>#/tab.name/#</span></a></li>
    {/literal}
</ul>
<div ng-show="getActiveIndex()<0" class="browser-action-view-content ui-tabs-panel ui-widget-content ui-corner-bottom" id="browser-action-view-1"
     aria-labelledby="10000_welcome" role="tabpanel" aria-expanded="false" aria-hidden="true"  ng-bind-html="welcomeTab">
</div>
<div ng-show="tab.active" ng-repeat="tab in tabs" class="browser-action-view-content ui-tabs-panel ui-widget-content ui-corner-bottom" id="browser-action-view-2"
     aria-labelledby="ui-id-11" role="tabpanel" aria-expanded="true" aria-hidden="false" ng-bind-html="tab.content">

</div>
</div>
</div>
</div>

</div>
</div>
</div>