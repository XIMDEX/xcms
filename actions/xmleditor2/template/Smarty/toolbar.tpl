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

<div class="kupu-tb" id="toolbar">
	<span id="kupu-tb-buttons" class="kupu-tb-buttons">
	
		{* File operations *}
		<span class="kupu-tb-buttongroup">
			<button type="button" class="kupu-save" id="kupu-save-button" xim:title="{t}Save{/t}" i18n:attributes="title" accesskey="s">
				&#xA0;
				<span class="triangle"></span><span class="tooltip">{t}Save{/t}</span>
			</button>
			<button type="button" class="kupu-prevdoc" id="kupu-prevdoc-button" i18n:attributes="title" xim:title="{t}Preview{/t}" accesskey="x">
				&#xA0;
				<span class="triangle"></span><span class="tooltip">{t}Preview{/t}</span>
			</button>
			<button type="button" class="kupu-publicate" id="kupu-publicate-button" xim:title="{t}Publicate{/t}" i18n:attributes="title" accesskey="s">
				&#xA0;
				<span class="triangle"></span><span class="tooltip">{t}Publish{/t}</span>
			</button>
		</span>
		
		{* Document views *}
		<span class="kupu-tb-buttongroup">		
				<button type="button" class="kupu-treeview" id="kupu-treeview-button" xim:title="{t}Tree{/t}" i18n:attributes="title" accesskey="s">
					&#xA0;
					<span class="triangle"></span><span class="tooltip">{t}Tree view{/t}</span>
				</button>
			<button type="button" class="kupu-designview" id="kupu-designview-button" xim:title="{t}Design{/t}" i18n:attributes="title" accesskey="s">
				&#xA0;
				<span class="triangle"></span><span class="tooltip">{t}Design view{/t}</span>
			</button>
{*			<button type="button" class="kupu-remoteview" id="kupu-remoteview-button" xim:title="{t}Remote{/t}" i18n:attributes="title" accesskey="s">
				&#xA0;
				<span class="triangle"></span><span class="tooltip">{t}Remote view{/t}</span>
			</button>*}
		</span>
		
		{* Edit operations *}
		<span class="kupu-tb-buttongroup">
		
			<button type="button" class="kupu-cut" id="kupu-cut-button" xim:title="{t}Cut{/t}" i18n:attributes="title" accesskey="z">
				&#xA0;
				<span class="triangle"></span><span class="tooltip">{t}Cut selected element{/t}</span>
			</button>
			<button type="button" class="kupu-copy" id="kupu-copy-button" xim:title="{t}Copy{/t}" i18n:attributes="title" accesskey="z">
				&#xA0;
				<span class="triangle"></span><span class="tooltip">{t}Copy selected element{/t}</span>
			</button>
			<button type="button" class="kupu-paste" id="kupu-paste-button" xim:title="{t}Paste{/t}" i18n:attributes="title" accesskey="z">
				&#xA0;
				<span class="triangle"></span><span class="tooltip">{t}Paste element{/t}</span>
			</button>
		
			<button type="button" class="kupu-remove" id="kupu-remove-button" xim:title="{t}Delete selected element{/t}" i18n:attributes="title" accesskey="z">
				&#xA0;
				<span class="triangle"></span><span class="tooltip">{t}Delete selected element{/t}</span>
			</button>
			<button type="button" class="kupu-scrollup" id="kupu-scrollup-button" xim:title="{t}Scroll up{/t}" i18n:attributes="title" accesskey="z">
				&#xA0;
				<span class="triangle"></span><span class="tooltip">{t}Put element up{/t}</span>
			</button>
			<button type="button" class="kupu-scrolldown" id="kupu-scrolldown-button" xim:title="{t}Scroll down{/t}" i18n:attributes="title" accesskey="z">
				&#xA0;
				<span class="triangle"></span><span class="tooltip">{t}Put element down{/t}</span>
			</button>
        </span>
		<span class="kupu-tb-buttongroup">
			<button type="button" class="kupu-undo" id="kupu-undo-button" xim:title="{t}Undo{/t}: alt-z" i18n:attributes="title" accesskey="z">
				&#xA0;
				<span class="triangle"></span><span class="tooltip">{t}Undo{/t}</span>
			</button>
			<button type="button" class="kupu-redo" id="kupu-redo-button" xim:title="{t}Redo{/t}: alt-y" i18n:attributes="title" accesskey="y">
				&#xA0;
				<span class="triangle"></span><span class="tooltip">{t}Redo{/t}</span>
			</button>
			
			
			<button type="button" class="kupu-ximletdrawer" id="kupu-ximletdrawer-button" xim:title="{t}Ximlet manager{/t}" i18n:attributes="title">
				&#xA0;
				<span class="triangle"></span><span class="tooltip">{t}Ximlet manager{/t}</span>
			</button>
		</span>
		
		{* Services *}
		<span class="kupu-tb-buttongroup">
			<button type="button" class="kupu-schemavalidator" id="kupu-schemavalidator-button" xim:title="{t}Validate schema{/t}" i18n:attributes="title">
				&#xA0;
				<span class="triangle"></span><span class="tooltip">{t}Validate schema{/t}</span>
			</button>
			<button type="button" class="kupu-spellchecker" id="kupu-spellchecker-button" xim:title="{t}Spell Check{/t}" i18n:attributes="title">
				&#xA0;
				<span class="triangle"></span><span class="tooltip">{t}Spell checker{/t}</span>
			</button>
		</span>
		
		{* Publication *}
		<span class="kupu-tb-buttongroup">
		</span>
		
		<span class="kupu-tb-buttongroup" id="kupu-newwindow">
			<button type="button" class="kupu-newwindow" id="kupu-newwindow-button" i18n:attributes="title" xim:title="{t}Open in a new window{/t}" accesskey="n">
				&#xA0;
				<span class="triangle"></span><span class="tooltip">{t}Open in a new window{/t}</span>
			</button>
		</span>
		
        	<span class="kupu-tb-buttongroup kupu-floating-toolboxes">
    			<button type="button" class="kupu-floatingtoolbox-button xedit-toolbar-toolbox-button" id="xedit-toolbar-toolbox-button" title="{t}Show/hide options{/t}"
				i18n:attributes="title">
				&#xA0;
			</button>
			<button type="button" class="kupu-floatingtoolbox-button xedit-channels-toolbox-button" id="xedit-channels-toolbox-button" title="{t}Show/hide channels{/t}" i18n:attributes="title">
				&#xA0;
			</button>
			<button type="button" class="kupu-floatingtoolbox-button xedit-attributes-toolbox-button" id="xedit-attributes-toolbox-button" title="{t}Show/hide attributes{/t}" i18n:attributes="title">
				&#xA0;
			</button>
			<button type="button" class="kupu-floatingtoolbox-button xedit-annotations-toolbox-button" id="xedit-annotations-toolbox-button" title="{t}Show/hide annotations{/t}" i18n:attributes="title">
				&#xA0;
			</button>
			<button type="button" class="kupu-floatingtoolbox-button kupu-toolbox-debuglog-button" id="kupu-toolbox-debuglog-button" title="{t}Show/hide debug log{/t}" i18n:attributes="title">
				&#xA0;
			</button>
			<button type="button" class="kupu-floatingtoolbox-button kupu-toolbox-undolog-button" id="kupu-toolbox-undolog-button" title="{t}Show/hide change history{/t}" i18n:attributes="title">
				&#xA0;
			</button>
			<button type="button" class="kupu-floatingtoolbox-button xedit-rngelements-toolbox-button" id="xedit-rngelements-toolbox-button" title="{t}Show/hide available elements{/t}" i18n:attributes="title">
				&#xA0;
			</button>
			<button type="button" class="kupu-floatingtoolbox-button xedit-info-toolbox-button" id="xedit-info-toolbox-button" title="{t}Show/hide information{/t}" i18n:attributes="title">
				&#xA0;
			</button>
        </span>
	</span>
</div>
