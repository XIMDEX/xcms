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

<div class="xim-tagsinput-container" id="{$id}">
	<div class="xim-tagsinput-container-list">
		<label class="aligned" for="tag_input">{t}Insert here your tags{/t}</label>
    	<ul class="xim-tagsinput-list">
       {foreach name=list item=tag key=i from=$tags}
   		<li class="xim-tagsinput-tag">
   			<select class="hidden vertical collapsable ximdexInput icon button"></select>
			<input type="hidden" name="tags[{$smarty.foreach.list.index}][text]" value="{$tag.name|utf8_decode}" />
			<input type="hidden" name="tags[{$smarty.foreach.list.index}][type]" value="{$tag.type|default:'generic'}" />
			<input type="hidden" name="tags[{$smarty.foreach.list.index}][url]"  value="{$tag.link|default:'#'}" />
			<input type="hidden" name="tags[{$smarty.foreach.list.index}][description]" value="{$tag.description}" />
			<span class="xim-tagsinput-text" contentEditable="true">
			{$tag.name|utf8_decode}
			</span><span class="amount">1</span>
			{* <a class="xim-tagsinput-tag-properties" href="#"> &infin; </a> *}

			<a class="xim-tagsinput-tag-remove" href="#"> &times; </a>
		</li>
	 {/foreach}
	 <li class="xim-tagsinput-newtag"><input type="text" class="xim-tagsinput-input" id="tag_input"/></li>
    </ul>
    </div>


	 <div class="xim-tagsinput-container-related">
	  <strong>Relativos:</strong>
     <ul class="xim-tagsinput-list-related" style="clear:both;"></ul>
    </div>

    <div class="xim-tagsinput-container-suggested">
     <strong>Sugeridos:</strong>
     <ul class="xim-tagsinput-list-suggested" style="clear:both;"></ul>
    </div>

</div>
