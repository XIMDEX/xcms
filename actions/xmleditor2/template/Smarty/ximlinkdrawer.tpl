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

<div id="kupu-ximlinkdrawer" class="kupu-drawer kupu-ximlinkdrawer-window xedit-toolbox  ximlink_manager">


	<div class="title_panel">
		<h3 i18n:translate="">{t}Link manager{/t}</h3>
 		<a href="#" class="js_add_link new_link icon add">{t}Add a new link{/t}</a>

	</div>

	<div class="kupu-panels js_search_link_panel search_panel">
		<div class="search_links">
			<label for="ximlink-search" i18n:translate="" class="title">{t}Search existing links{/t}</label>
			<input id="ximlink-search" class="kupu-toolbox-st ximlink-search xlinks_input" type="text" />
		</div>

		<div id="ximlink-list">
			<label for="ximlink-list" i18n:translate="" class="title icon links">{t}Links{/t}</label>
			<select  class="ximlink-list xlinks_list" multiple="true"></select>
		</div>

		<label for="descriptions-list" i18n:translate="" class="title icon descriptions">{t}Available descriptions for the selected link{/t}</label>
		<div class="descriptions-list-options description_list"></div>
	</div>

	<div class="buttons">
		<button class="kupu-dialog-button save-button btn main_action" type="button" i18n:translate="">{t}Accept{/t}</button>
		<button class="kupu-dialog-button close-button btn" type="button" i18n:translate="">{t}Cancel{/t}</button>
	</div>


<!--Hasta aquí busca enlaces-->
<!--Desde aquí, los crea-->
	<div class="kupu-panels js_add_link_panel add_panel" style="display:none">
		<div class="search">
			<label for="link_name" i18n:translate="" class="title">{t}Name{/t}</label>
			<input name="link_name" class="kupu-toolbox-st ximlink-search" type="text" />
		</div>
		<div class="search">
			<label for="link_url" i18n:translate="" class="title">{t}Url{/t}</label>
			<input name="link_url" class="kupu-toolbox-st ximlink-search" type="text" />
		</div>
		<div class="search">
			<label for="link_description" i18n:translate="" class="title">{t}Descripción{/t}</label>
			<input name="link_description" class="kupu-toolbox-st ximlink-search" type="text" />
		</div>
		<div class="search">
			<label for="link_idparent" i18n:translate="" class="title">{t}Folder{/t}</label>
			<treeview class="xim-treeview-selector"	/>
			<input name="link_id_parent" type="hidden" />
		</div>
	</div>

	<div class="buttons" style="display:none">
		<button class="kupu-dialog-button create-button btn main_action" type="button" i18n:translate="">{t}Create{/t}</button>

		<button class="kupu-dialog-button cancel-button btn" type="button" i18n:translate="">{t}Cancel{/t}</button>
	</div>
</div>
