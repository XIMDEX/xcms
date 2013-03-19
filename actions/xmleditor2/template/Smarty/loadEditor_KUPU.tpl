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

<form action="{$actionUrlShowPost}" method="POST">

	<div class="kupu-fulleditor">

		<div class="kupu-ximparams" style="display: none;">
			<span id="kupu-nodeId">{$nodeid}</span>
			<span id="kupu-actionId">{$actionid}</span>
			<span id="kupu-xslIncludesOnServer">{$xslIncludesOnServer}</span>
		</div>
		{include file="`$_APP_ROOT`/actions/xmleditor2/template/Smarty/toolbar.tpl"}
		{include file="`$_APP_ROOT`/actions/xmleditor2/template/Smarty/ximletdrawer.tpl"}
		{include file="`$_APP_ROOT`/actions/xmleditor2/template/Smarty/ximlinkdrawer.tpl"}
		{include file="`$_APP_ROOT`/actions/xmleditor2/template/Smarty/tabledrawer.tpl"}
		{include file="`$_APP_ROOT`/actions/xmleditor2/template/Smarty/toolboxes.tpl"}
        	<div class="kupu-editorframe">
				{include file="`$_APP_ROOT`/actions/xmleditor2/template/Smarty/tagnavbar.tpl"} 
			<div class="iwrapper"><!--Added for autoscrolling-->
				<div class="scrollup autoscroll"><span>{t}Scroll up{/t}</span></div>
				<iframe id="kupu-editor" class="kupu-editor-iframe" frameborder="0" src="{$_URL_ROOT}/actions/xmleditor2/index.html" scrolling="auto">
				</iframe>
				<div class="scrolldown autoscroll"><span>{t}Scroll down{/t}</span></div>
			</div>
			<div class="kupu-toolboxes-container">

				<div class="kupu-toolboxes-collapser"></div>
				<div class="kupu-toolboxes-container-container">
					<tagsinput editor="true" initialize="true" />
				</div>
			</div>

			<textarea class="kupu-editor-textarea" id="kupu-editor-textarea"> </textarea>
		</div>


	</div>

</form>
