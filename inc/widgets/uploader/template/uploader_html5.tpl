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

 <div class="xim-uploader-container" id="{$id}">


  <div class="xim-loader-list-container">
  	  <div class="xim-loader-header">
  	  		<span class="xim-loader-name">{t}Name{/t}:</span>
  	  		<span class="xim-loader-size">{t}Size{/t}:</span>
  	  		<span class="xim-loader-progress">{t}Progress{/t}:</span>
  	  		<span class="xim-loader-options">{t}Options{/t}:</span>
  	  </div>
	  <ul class="xim-loader-list">

	  </ul>
  </div>
  <ul class="xim-loader-list-actions">
 <li><a href="#" class="xim-uploader-selected">{t}Add{/t}</a> 
    <a href="#" class="xim-uploader-link">{t}Add{/t}<input name='file[]' type='file' multiple='true' class="xim-uploader" style="display: none;" class="xim-uploader"></a> 
 </li>
   <li><a href="#" class="xim-uploader-delete">{t}Remove{/t}</a></li>
 </div>
