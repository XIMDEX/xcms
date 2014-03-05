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

<form method="POST" name="tags_form" class="setmetadata-tags_form" action="{$action_url}">
	<div class="action_header">
		<h2>{t}Tag this node{/t}</h2>
		<fieldset class="buttons-form">
			{button label='Guardar' class='asoc enable_checks validate btn main_action'}
		</fieldset>
	</div>

	<div class="action_content">
		<tagsinput initialize="true" />
		
	{if ($nube_tags)}
		<div class="tagcloud">
			<div class="title-box">{t}Suggested tags from Ximdex CMS{/t}</div>
			
			<ul class="nube_tags">
		{section name=i loop=$nube_tags}
			{math assign=font equation="16 + 10*(tamano/$max_value)" tamano=$nube_tags[i].Total}
				<li class="xim-tagsinput-taglist icon custom">
                    <span class="tag-text">{$nube_tags[i].Name}</span>
                    <span class="amount right">{$nube_tags[i].Total}</span>
                </li>
		{/section}
			</ul>
		</div>
	{/if}
		<ontologyBrowser />
	</div>
{*</div>
</div>*}
</form>
