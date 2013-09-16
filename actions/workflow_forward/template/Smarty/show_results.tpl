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

{if (count($result)) }
<h2>{t}Publication result{/t}</h2>
<fieldset>
	<table class='tabla'>
	{foreach name=options from=$result key=option item=node_info}
		{if (count($node_info)) }
			<caption>{t}The following nodes{/t} {$options[$option]}</caption>
			<thead>
			{if ($option == "unchanged" || $synchronizer_to_use == "default")}
				<tr>
					<th class=filaclara>{t}Node{/t}</th>
				</tr>
			{else}
				<tr>
					<th class=filaclara>{t}Node{/t}</th>
					<th class=filaclara>{t}Path{/t}</th>
					<th class=filaclara>{t}Server{/t}</th>
					<th class=filaclara>{t}Channel{/t}</th>
				</tr>
			{/if}
			</thead>
			<tbody>
			{foreach name=values from=$node_info key=id_value item=value_info}
				{if ($option == "unchanged" || $synchronizer_to_use == "default")}
					<tr>
						<td class='filaoscura' colspan="3">{$value_info.NODE}</td>
					</tr>
				{else}
					<tr>
						<td class='filaoscura'>{$value_info.NODE}</td>
						<td class='filaoscura'>{$value_info.PATH}</td>
						<td class='filaoscura'>{$value_info.SERVER}</td>
						<td class='filaoscura'>{$value_info.CHANNEL}</td>
					</tr>
				{/if}
			{/foreach}
			</tbody>
		{/if}
	{/foreach}
	</table>
</fieldset>
{/if}

{if (count($messages)) }
<h2>{t}Messages{/t}</h2>

	<ol>
	{foreach name=messages from=$messages key=message_id item=message}
		<li>{$message.message}</li>
	{/foreach}
	</table>

{/if}

<fieldset class="buttons-form">
{if ($goback) }
	{button class="goback-button  btn main_action" label="Go back"}
{else}
	{button class="close-button  btn main_action" label="Close"}
{/if}
</fieldset>