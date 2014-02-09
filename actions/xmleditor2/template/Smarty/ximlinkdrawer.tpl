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

	<div class="kupu-panels js_search_link_panel">
		<div class="search">
			<label for="ximlink-search" i18n:translate="">{t}Search{/t}</label>
			<input id="ximlink-search" class="kupu-toolbox-st ximlink-search long" type="text" />
			<a href="#" class="js_add_link">{t}or add a new link{/t}</a>
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
<!--Hasta aquí busca enlaces-->
<!--Desde aquí, los crea-->
	<div class="kupu-panels js_add_link_panel" style="display:none">
		<div class="search">
			<label for="link_name" i18n:translate="">{t}Name{/t}</label>
			<input name="link_name" class="kupu-toolbox-st ximlink-search long" type="text" />
		</div>
		<div class="search">
			<label for="link_url" i18n:translate="">{t}Url{/t}</label>
			<input name="link_url" class="kupu-toolbox-st ximlink-search long" type="text" />
		</div>
		<div class="search">
			<label for="link_description" i18n:translate="">{t}Descripción{/t}</label>
			<input name="link_description" class="kupu-toolbox-st ximlink-search long" type="text" />
		</div>
		<div class="search" style="position:relative; height:300px;">
			<label for="link_idparent" i18n:translate="">{t}Folder{/t}</label>
			<treeview class="xim-treeview-selector"	/>
			<input name="link_id_parent" type="hidden" />
		</div>		
	</div>

	<div class="buttons" style="display:none">
		<button class="kupu-dialog-button create-button" type="button" i18n:translate="">{t}Create{/t}</button>
	
		<button class="kupu-dialog-button cancel-button" type="button" i18n:translate="">{t}Cancel{/t}</button>
	</div>
</div>
