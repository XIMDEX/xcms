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

<div class="xim-uploader-container" id="{$id}" xim-is-structured="%=is_structured%" 
  flow-init = "{literal}{target:'/mipuntofinal'}{/literal}"
  flow-name="uploader.flow" 
  flow-file-added="$parent.fileAdded($event, $file)"
  flow-file-error="$parent.fileError($file, $message)"
  flow-file-success="$parent.fileSuccess($file, $message)"
  flow-complete="$parent.uploadComplete()"
  flow-drop
  ng-cloak>
  <div class="xim-loader-list-container">
    <div class="guide" ng-hide="$flow.files.length">
      <span class="icon document">Documents</span>
      <span class="icon image">Images</span>
      <span class="icon video">Videos</span>
      <p> {t}Drag your files here or add them using the 'Add' button below{/t}.</p>
    </div>
	  <ul class="xim-loader-list" ng-show="$flow.files.length">
      <li ng-repeat="file in $flow.files">
        <xim-file xim-model="file" xim-node-id="{$nodeid}"></xim-file>
      </li>
    </ul>
  </div>
    <ul class="xim-loader-list-actions" >
      <a href="#" class="xim-uploader-selected btn-labeled icon btn">
        {t}Add{/t}
        <input type="file" class="xim-uploader" flow-btn />
        <!-- <input name='file[]' type='file' multiple='true' class="xim-uploader" style="display: none;" class="xim-uploader"> -->
      </a>
    </ul>
 </div>
