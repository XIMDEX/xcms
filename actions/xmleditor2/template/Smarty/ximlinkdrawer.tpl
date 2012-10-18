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

<div id="kupu-ximlinkdrawer" class="kupu-drawer kupu-ximlinkdrawer-window xedit-toolbox">


	<h3 i18n:translate="">{t}Link manager{/t}</h3>

	<div class="kupu-panels">
		<div class="search">
			<label for="ximlink-search" i18n:translate="">{t}Search{/t}</label>
			<input id="ximlink-search" class="kupu-toolbox-st ximlink-search long" type="text" />
		</div>
		<div id="ximlink-list">
				<label for="ximlink-list" i18n:translate="">{t}Links{/t}</label>
				<select  class="ximlink-list" multiple="true"></select>
		</div>
		<div>
				<label for="descriptions-list" i18n:translate="">{t}Available descriptions for the selected link{/t}</label>
				<div class="descriptions-list-options">
				</div>
		</div>
	</div>

	<div class="buttons">
		<button class="kupu-dialog-button save-button" type="button" i18n:translate="">{t}Accept{/t}</button>
	
		<button class="kupu-dialog-button close-button" type="button" i18n:translate="">{t}Cancel{/t}</button>
	</div>
</div>
