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

<div class="xim-tagsinput-container js-tagsinput" id="{$id}">
	<div class="xim-tagsinput-container-list">
		<div class="xim-tagsinput-newtag  col2-3">
			<input type="text" class="xim-tagsinput-input" id="tag_input" placeholder="{t}Create new tags here{/t}..."/>
			<select class="hidden vertical collapsable ximdexInput icon button type-selector tag-type btn-rounded icon" name="type"></select>
			<button class="btn-unlabel-rounded icon add-btn">{t}Add{/t}</button>
		</div>
    </div>

	<div class="xim-tagsinput-container col2-3">
		<div class="title-box">{t}Document tags{/t}</div>
   		<ul class="xim-tagsinput-list">
		{if count($tags)!=0}
       			{foreach name=list item=tag key=i from=$tags}
   			<li class="xim-tagsinput-tag">
   				<select class="hidden vertical collapsable ximdexInput icon button type-selector" name="type"></select>
				<input type="hidden" id="text" name="tags[{$smarty.foreach.list.index}][text]" value="{$tag.name|utf8_decode}" />
				<input type="hidden" id="type" name="tags[{$smarty.foreach.list.index}][type]" value="{$tag.type|default:'generic'}" />
				<input type="hidden" id="url" name="tags[{$smarty.foreach.list.index}][url]" value="{$tag.link|default:'#'}" />
				<input type="hidden" id="description" name="tags[{$smarty.foreach.list.index}][description]" value="{$tag.description}" />
				<span class="xim-tagsinput-text" data-tooltip="{$tag.name|utf8_decode}">
				{$tag.name|utf8_decode}
				</span>
				<a class="xim-tagsinput-tag-remove icon" href="#"> &times; </a>
			</li>
	 		{/foreach}
		{else}
			<p>{t}There aren't any tags defined yet{/t}.</p>
		{/if}
    		</ul>
    </div>

	<div class="suggested col1-3">
		{if $isStructuredDocument}
    		<div class="xim-tagsinput-container-related">
    			<div class="title-box">{t}Suggested Semantic Tags{/t}</div>
    	 		<ul class="xim-tagsinput-list-related loading" style="clear:both;"></ul>
    		</div>
    	{/if}
    	
