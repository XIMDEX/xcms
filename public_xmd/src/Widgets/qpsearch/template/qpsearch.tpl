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

<div id="%=id%" class="xim-qpsearch-container %=class%">
	<suggester
		label=""
		class="xim-qpsearch-suggester"
		size="%=size%"
		maxlength="%=maxlength%"
		value="%=value%"
	/>
	<!--input
		type="text"
		id="xim-qpsearch-textfield"
		class="xim-qpsearch-textfield"
		value="%=value%"
		size="%=size%"
		maxlength="%=maxlength%"
	/-->
	<button id="xim-qpsearch-searchbutton" class="xim-qpsearch-button">%=text%</button>
	<br />
	<a id="xim-qpsearch-advanced" href="#">%=advancedText%</a>
	<div id="xim-qpsearch-filters" class="xim-qpsearch-filters xim-qpsearch-filters-hidden">
		<div>
			<label for="xim-qpsearch-filter-nodetype">%=nodeTypeFilterLabel%:</label>
			<select id="xim-qpsearch-filter-nodetype"></select>
		</div>
		<br />
		<div>
			<label for="xim-qpsearch-filter-date">%=dateFilterLabel%:</label>
			<input type="text" id="xim-qpsearch-datefield-from" size="8" readonly />
			<img id="xim-qpsearch-filter-datebutton-from" src="images/icons/calendar.gif" />
			hasta
			<input type="text" id="xim-qpsearch-datefield-to" size="8" readonly />
			<img id="xim-qpsearch-filter-datebutton-to" src="images/icons/calendar.gif" />
			<div id="xim-qpsearch-filter-date"></div>
		</div>
		<br />
		<div>
			<label for="xim-qpsearch-filter-publication">%=publicationFilterLabel%:</label>
			<input type="text" id="xim-qpsearch-publicationfield-from" size="8" readonly />
			<img id="xim-qpsearch-filter-publicationbutton-from" src="images/icons/calendar.gif" />
			hasta
			<input type="text" id="xim-qpsearch-publicationfield-to" size="8" readonly />
			<img id="xim-qpsearch-filter-publicationbutton-to" src="images/icons/calendar.gif" />
			<div id="xim-qpsearch-filter-publication"></div>
		</div>
		<div>
			<button id="xim-qpsearch-reset" class="xim-qpsearch-button">Reset</button>
		</div>
	</div>
</div>