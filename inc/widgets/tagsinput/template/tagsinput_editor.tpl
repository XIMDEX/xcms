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

<div class="kupu-toolbox-container">
	<h3 id="kupu-toolbox-tags-header" style="-moz-user-select: none"
		class="kupu-toolbox-heading kupu-toolbox-heading-closed ui-draggable">
		<button i18n:attributes="title" title="Show/hide tags"
			id="kupu-toolbox-tags-button"
			class="kupu-floatingtoolbox-button kupu-toolbox-tags-button"
			type="button">&nbsp;</button>
			Tags
	</h3>
<div class="xim-tagsinput-container" id="{$id}"  style="display:none">
    <div class="xim-tagsinput-container-list">
    <ul class="xim-tagsinput-list" style="clear:both;">
       {foreach name=list item=tag key=i from=$tags}
   		<li class="xim-tagsinput-tag">	
				<input type="hidden" name="tags[{$smarty.foreach.list.index}][text]" value="{$tag.name}" />
				<input type="hidden" name="tags[{$smarty.foreach.list.index}][type]" value="{$tag.type|default:'generic'}" />
				<input type="hidden" name="tags[{$smarty.foreach.list.index}][url]"  value="{$tag.link|default:'#'}" />
				<input type="hidden" name="tags[{$smarty.foreach.list.index}][description]" value="{$tag.description}" />	
				<span>
				{$tag.name}
				</span>
				{* <a class="xim-tagsinput-tag-properties" href="#"> &infin; </a> *}

				<a class="xim-tagsinput-tag-remove" href="#"> &times; </a>
			</li>
		 {/foreach}
        <li class="xim-tagsinput-newtag"><input type="text" class="xim-tagsinput-input" /></li>
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
 </div>
