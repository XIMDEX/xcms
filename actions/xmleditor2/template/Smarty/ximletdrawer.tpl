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

<div id="kupu-ximletdrawer" class="kupu-drawer kupu-ximletdrawer-window">
	<h3 i18n:translate="">{t}Ximlet manager{/t}</h3>
	<div id="kupu-ximletdrawer-addximlet" class="kupu-panels kupu-ximletdrawer-addximlet">
		<span i18n:translate="">
    				{t}Associate selected ximlet with the following node ID:{/t}
  				</span>
		<input class="kupu-toolbox-st kupu-ximletdrawer-input" type="text" />
  	</div>
  	<button class="kupu-dialog-button" type="button" onclick="ximdocdrawertool.current_drawer.save()" i18n:translate="">{t}Accept{/t}</button>
  	<button class="kupu-dialog-button" type="button" onclick="ximdocdrawertool.closeDrawer()" i18n:translate="">{t}Cancel{/t}</button>
</div>
