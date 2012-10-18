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


<fieldset>
	<legend><span>{t}Add{/t}</span></legend>
	<input type="hidden" name="nodeid" value="{$nodeID}"><br>
	<ol>
		<li>
			{if $ret eq 'true'}
				{t  name=$name}<strong>%1</strong> has been successfully created{/t}
			{elseif $ret eq 'false'}
				{t msgError=$msgError}The operation has failed{/t}:
			{/if}
		</li>
		<li>
			{if $ret eq 'true'}
				&nbsp;
			{elseif $ret eq 'false'}
				{button label="Go back" class='form_reset' onclick='history.back();'}
			{/if}
		</li>
	</ol>
</fieldset>
