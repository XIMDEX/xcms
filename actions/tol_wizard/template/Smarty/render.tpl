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

{* Disponibles tag_name, tag_info, root *}
		{if (is_array($tag_info) && array_key_exists('children', $tag_info) && count($tag_info.children))}

			{if in_array($tag_name, array('docxap', 'toldoc'))}
				<div class="container">
			{else}
				<fieldset>
					<legend><span>{$tag_name}</span></legend>
			{/if}

			{* Iterates childern, if key is a string it is a render, if it is a number is a render_element *}
			{foreach from=$tag_info.children key=_tag_name item=children_tag_info}
				{if is_string($children_tag_info.attributes.view)}
					{include file="`$view_folder`render_element.tpl" tag_name="`$_tag_name`" 
						tag_info="`$children_tag_info`"  tag_value="`$children_tag_info._value_`" root="`$root`[`$_tag_name`]"}
				{else}
					{include file="`$view_folder`render.tpl" tag_name="`$_tag_name`" 
						root="`$root`[`$_tag_name`]" tag_info="`$children_tag_info`"}
				{/if} 
			{/foreach}


			{if in_array($tag_name, array('docxap', 'toldoc'))}
				</div>
			{else}
				</fieldset>
			{/if}
		{/if}
		
