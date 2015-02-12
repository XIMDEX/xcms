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
        <h2>{t}Add section{/t}</h2>
    </div>

    <div class="action_content section-properties">
        <div class="folder-name folder-normal icon input-select">
            <input type="text" name="name" maxlength="100" ng-required="true" class="cajaxg full-size js_val_unique_name js_val_alphanumeric" placeholder="{t}Name of your section{/t}" data-idnode="{$nodeID}" />
            <select ng-model="sectionTypeSelected" ng-options="stype.label for stype in sectionTypeOptions track by stype.value" ng-change="changeSubfolders()" name="nodetype" class="caja validable not_empty folder-type"></select>
        </div>

        <div class="languages-available col1-3 right">
            <h3>{t}Languages availables{/t}</h3>
            <div class="language-section" ng-repeat="lang in languageOptions">
                <input id="#/lang.IdLanguage/#" class="hidden-focus" type="checkbox" value="#/lang.IdLanguage/#" name="langidlst[]">
                <label class="icon checkbox-label" for="#/lang.IdLanguage/#">{t}#/lang.Name/#{/t}</label>
                <input type="text" placeholder="{t}Alternative name for paths &amp; breadcrumbs{/t}" class="alternative-name" name="namelst[#/lang.IdLanguage/#]">
            </div>
        </div>

        <div class="subfolders-available col2-3">
            <h3>{t}Subfolders availables{/t}</h3>
            <div ng-repeat="folder in subfoldersSelected" class="subfolder box-col1-1">
                <input id="#/folder.NodeType/#" class="hidden-focus" type="checkbox" value="#/folder.NodeType/#" name="folderlst[]">
                <label class="icon" for="#/folder.NodeType/#">
                    <strong class="icon #/folder.Name/#">#/folder.Name/#</strong>
                </label>
                <span class="info">#/folder.description/#</span>
            </div>
        </div>
    </div>
    <fieldset class="buttons-form positioned_btn">
        {button label="Create section" class='validate btn main_action' }{*message="Would you like to add this section?"*}
    </fieldset>	
</form>
