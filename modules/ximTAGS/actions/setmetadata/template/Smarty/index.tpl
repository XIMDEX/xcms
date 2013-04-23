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
<fieldset>
<legend><span>{t}Tag this node{/t}</span></legend>
<frameset>
<div class="tagslist">
<tagsinput initialize="true" />
</div>
{if ($nube_tags)}
<div class="tagcloud">
<label class="aligned">{t}Suggested tags <br/>from another Ximdex nodes{/t} </label>
<ul class="nube_tags">
	{section name=i loop=$nube_tags}
		{math assign=font equation="8 + 2*tamano" tamano=$nube_tags[i].Total}
		<li style="font-size: {$font}px;"><span>{$nube_tags[i].Name}</span><span class="amount">{$nube_tags[i].Total}</span></li>
	{/section}
</ul>
</div>
{/if}
</frameset>
</fieldset>
<fieldset class="buttons-form">
	{button label='Guardar' class='asoc enable_checks validate'}
</fieldset>
</form>
