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

<div class="action_header" ng-cloak>
	<h2>{t}Search results for: {/t}<span class="search-criteria">#/viewData.query.filters[0].content/#</span> <span class="results-number">#/viewData.data.length/#/#/viewData.records/#</span></h2>
	<h2 ng-show="viewData.pages>0"><span ng-click="downPage()" ng-hide="page<=1">&lt;</span>{t}Page{/t} #/page/# {t}of{/t} #/viewData.pages/#<span ng-click="upPage()" ng-hide="page>=viewData.pages">&gt;</span></h2>
	
	<div class="filter">
		#/'ui.search.filter_by' | xI18n/#: 
		<input type="text" ng-disabled="searching || viewData.pages<1" ng-model="filterText"/>
	</div>

</div>
	
<div class="action_content fullwidth">
	<div ng-show="searching" class="loading_background">
		<img class="loading_image" src="./actions/xmleditor2/gfx/loading.gif" />
	</div>
	<xim-grid xim-list="viewData" xim-init-fields='{$fields}' xim-filter='filterText' xim-actual-page="page" xim-total-pages="pages" xim-up-page="upPage" xim-down-page="downPage" xim-searching="searching"><xim-grid/>
</div>
