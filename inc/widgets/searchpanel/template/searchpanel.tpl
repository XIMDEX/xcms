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

<div id="%=id%" class="xim-search-dialog %=class%">

	<div class="xim-search-panel search_options">
			<div class="buttonset save_search">
					<button value="save" class="">{t}Save{/t}</button>
			</div>		<h2>{t}Advanced search{/t}</h2>

		<div class="filters">

			<div id="search-filters" class="xim-search-filters">

				<div class="xim-search-filter">
					<select class="xim-filter-field">
						<option value="name">{t}Name{/t}</option>
						<option value="content">{t}content{/t}</option>
						<option value="nodetype">Nodetype</option>
						<option value="creation">{t}Creation date{/t}</option>
						<option value="publication">{t}Publication date{/t}</option>
						<option value="link_url">{t}ximLINK Url{/t}</option>
						<option value="link_desc">{t}ximLINK Description{/t}</option>
					</select>
					<select class="xim-filter-comparation">
						<option value="contains">{t}contains{/t}</option>
						<option value="nocontains">{t}does not contain{/t}</option>
						<option value="equal">{t}equal to{/t}</option>
						<option value="nonequal">{t}not equal to{/t}</option>
						<option value="startswith">{t}begins with{/t}</option>
						<option value="endswith">{t}ends with{/t}</option>
					</select>
					<select class="xim-filter-nodetype-comparation">
						<option value="equal">{t}is{/t}</option>
					</select>
					<select class="xim-filter-date-comparation">
						<option value="equal">{t}equal to{/t}</option>
						<option value="previousto">{t}before than{/t}</option>
						<option value="laterto">{t}after than{/t}</option>
						<option value="inrange">{t}in the range{/t}</option>
					</select>

					<input type="text" class="xim-filter-content" />
					<select type="text" class="xim-filter-nodetype-content"></select>
					<input type="text" class="xim-filter-date-content" />
					<input type="text" class="xim-filter-date-to-content" />

					<button class="xim-filter-add">+</button>
					<button class="xim-filter-remove">-</button>
				</div>
			</div>
			<div class="xim-search-options">
				<span class="label">{t}Must be satisfied{/t}:</span>
				<!--<fieldset>
				-->
				<input id="sopt-and" type="radio" name="sopt" value="and" checked />
				<label for="sopt-and">{t}All rules{/t}</label>
				<input id="sopt-or" type="radio" name="sopt" value="or" />
				<label for="sopt-or">{t}Any rules{/t}</label>
				<!--</fieldset>--></div>

			<div class="buttonset">
				<button value="search" class="search_button">{t}Search{/t}</button>
			</div>

			<div class="xim-search-panel">
				<div class="saved-searches">
					<h3>{t}Saved searches{/t}</h3>
					<div class="saved-searches-item-container">
						<ul class="saved-searches-items"></ul>
					</div>
				</div>
				<div class="last-searches">
					<h3>{t}Last searches{/t}</h3>
					<div class="last-searches-item-container">
						<ul class="last-searches-items"></ul>
					</div>
				</div>
			</div>
		</div>

		

	</div>
<div class="results">
			<div id="loading" class="loading-icon"></div>

			<div class="results-view" />
			<paginator class="searchpanel-paginator" />

			<div class="buttonset">
				<button class="createSetButton"  value="createSet" disabled>{t}Create set{/t}</button>
				<!--<button value="addToSet">{t}Add to set{/t}</button>
			-->
			<button class="selectionButton" value="selectNodes">{t}Select nodes{/t}</button>
		</div>	

</div>

</div>