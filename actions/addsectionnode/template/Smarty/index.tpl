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

<form ng-controller="addSectionCtrl" method="post" name="add_form" ng-submit="submit()" action="{$action_url}&method=addsectionnode" novalidate>
    <div class="action_header">
        <h5 class="direction_header"> Name Node: {$name}</h5>
        <h5 class="nodeid_header"> ID Node: {$nodeid}</h5>
        <hr>

    </div>

    <div class="action_content section-properties">
        <div class="row tarjeta">
            <h2 class="h2_general">{t}Add section{/t}</h2>
        <div class="folder-name folder-normal icon input-select">
            <div class="small-6 columns">
            <label  class="label_title label_general">{t}Name{/t} *
            </label>
            <input type="text" name="name" maxlength="100" ng-required="true" class="input_general_icon cajaxg full-size js_val_unique_name js_val_alphanumeric validable not_empty" placeholder="{t}Name of your section{/t}" data-idnode="{( isset($nodeID) ) ? $nodeID : '' }" />
            </div>
            <div class="small-6 columns">
            <label  class="label_title label_general">{t}Type{/t} *
            </label>
                <select ng-model="sectionTypeSelected" ng-options="stype.label for stype in sectionTypeOptions track by stype.value" ng-change="changeSubfolders()" name="nodetype" class="caja folder-type"></select>
            </div></div>



        <div class="subfolders-available small-8 columns">
            <label  class="label_title label_general" style="margin-top: 20px;">{t}Subfolders availables{/t}
            </label>

            <div ng-repeat="folder in subfoldersSelected" class="subfolder box-col1-1">
                <input id="#/$parent.$parent.tab.id + '_' + folder.NodeType/#" class="hidden-focus" type="checkbox" value="#/folder.NodeType/#" name="folderlst[]">
                <label style="border-radius: 5px; padding-left:0!important;" class="icon" for="#/$parent.$parent.tab.id + '_' + folder.NodeType/#">
                    <strong class="icon #/folder.Name/#">#/folder.Name/#</strong>
                </label>
                <span class="info">#/folder.description/#</span>
            </div>
        </div>
            <div class="languages-available small-4 columns">
                <label  class="label_title label_general" style="margin-top: 20px;">{t}Languages availables{/t}
                </label>
                <div class="language-section" ng-repeat="lang in languageOptions">
                    <input id="#/lang.IdLanguage/#" class="hidden-focus" type="checkbox" value="#/lang.IdLanguage/#" name="langidlst[]">
                    <label class="icon checkbox-label" for="#/lang.IdLanguage/#">{t}#/lang.Name/#{/t}</label>
                    <input type="text" placeholder="{t}Alternative name for paths &amp; breadcrumbs{/t}" class="alternative-name" name="namelst[#/lang.IdLanguage/#]">
                </div>
            </div>
            <div class="small-12 columns">
        <fieldset class="buttons-form ">
            {button label="Create section" class='validate btn main_action' }{*message="Would you like to add this section?"*}
        </fieldset>
            </div></div></div>

</form>