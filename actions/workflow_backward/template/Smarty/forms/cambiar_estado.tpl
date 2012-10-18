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
	<legend><span>{t}Process result{/t} </span></legend>
	<ol>
		<li>
			&middot; {t}It is going to be changed the document{/t}
			<span class=destacada>&laquo; <strong>{$node_name} &raquo;</strong></span>
			 {t}from the state{/t} 
			 <u><strong>{$estado2_name}</strong></u>
			  {t}to the state{/t} 
			  <u><strong>{$estado1_name}</strong></u>
		</li>
	{if ($errors)}
		<li>
			&middot; <span style='color: red'>{t}Error changing state: {/t}<strong>{$errorsMSG}</strong></span>.
		</li>
	{else}
		<li>
			&middot; {t}State change successfull{/t}.
		</li>
	{/if}
		<li>
			&middot; {t}Sending notification to the following users: {/t}<strong><u>{$user_to}</u></strong>
		</li>
		{if ($errorsMail)}
		<li>
			<span style='color: red'>&middot; {t}Error sending the message: {/t}<strong>{$errorsMailMSG}</strong></span>.
		</li>
		{else}
		<li>
			&middot; {t}Message successfully sent{/t}
		</li>
		{/if}
		{foreach from=$users item=_user}
			{if ($_user.exito)}
		<li>
			&middot; {t}Message successfully sent to{/t} {$user.email}
		</li>
			{else}
		<li>
			<span style='color: red'>
				&middot; {t}Error sending e-mail{/t}: <strong>{$user.email}</strong>
			</span>
		</li>
			{/if}
		{/foreach}
	</ol>
</fieldset>