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

{function name=lang_name id="" label="" code=""}
{if ($user_locale.ID == $id|| ( null == $user_locale && $id == $smarty.const.DEFAULT_LOCALE) )}
	<strong>{$label|gettext} ({$code})</strong>
{else}
	{$label|gettext} ({$code})
{/if}

{/function}

{*
valid menu items:
<command>, <menuitem>, <hr>, <span>, <p> <input [text, radio, checkbox]>, <textarea>, <select> and of course <menu>.
*}

<menu id="{$id}" type="context" style="display:none" class="showcase xim-contextmenu-container %=class%">
  <command label="{t}Contact us{/t}" data-action="contactus" icon="workflow" ></command> 
  <command label="{t}Modify your user{/t}" data-action="modifyuser" data-nodeid="{$userid}" icon="rol"></command> 
  <menu label="{t}Change your Language{/t}" icon="language">
	{section name=i loop=$locales}
	    <command label="{lang_name id=$locales[i].ID  label=$locales[i].Name code=$locales[i].Lang}"
		data-action="changelang" data-params="code={$locales[i].Code}" icon="language"></command>
	{/section}
  </menu>
  <hr />  
  <menu label="{t}Help{/t}" icon="folder_system_properties">
    <command label="{t}About Ximdex{/t}" data-action="welcome" icon="folder_system_properties"></command>
  </menu>

</menu>

