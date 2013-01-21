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

{if $nodescount neq '0'}

	<form method="POST" name="ca_form" id="ca_form" action="{$action_url}">

	<fieldset>
		<legend><span>{t}Delete news from colector{/t}</span></legend>
		<p>{t}A single calendar will be shown for each selected news{/t}.</p> 
		<p>{t}Pick up a date and time to unlink each news from the colector. If you want to dissasociate them now, just set the date and time with a pair of minutes in the future{/t}.</p> 

		<input type="hidden" name="nodeid" value="{$id_node}" />

		{if $nodetype eq 'XimNewsColector'}
			<p class="left"><label>{t}News associated to colector{/t}:</label></p>
		{else}
			<p class="left"><label>{t}Delete news from{/t}:</label></p>
		{/if}

		<div class="left">
		<ol>

			{foreach from=$nodeslist key=i item=node}
				<li>
				<input name="nodeslist[{$i}]" class="validable nodes check_group_nodes nodelst" id="chknew{$i}" type="checkbox" value="{$node.id}">
				<label>{$node.name}</label>
				<div class="xim-calendar-container">

					{*<  ***calendar
						timestamp="{$timestamp_from}"
						date_field_name="date"
						hour_field_name="hour"
						min_field_name="min"
						sec_field_name="sec"
						format="d-m-Y H:i:s"
						rel="{$node.name}"
						type="from"
						cname="fechainicio[{$node.id}]"
					/>*}


					{*<   *****calendar
						timestamp="{$timestamp_to}"
						date_field_name="date"
						hour_field_name="hour"
						min_field_name="min"
						sec_field_name="sec"
						format="d-m-Y H:i:s"
						rel="{$node.name}"
						type="to"
						cname="fechafin[{$node.id}]"
					/>*}

				</div>
				</li>
			{/foreach}
		</ol>
		</div>
	</fieldset>

	<fieldset class="buttons-form">
		{button label="Accept" class="validate"}
	</fieldset>

	</form>

{else}

	{if $nodetype eq 'XimNewsColector'}
		<fieldset>
		<legend><span>{t}Associated news{/t}</span></legend>
		<p>{t}The colector has not associated news{/t}.</p>
		</fieldset>
	{else}
		<fieldset>
		<legend><span>{t}Associated colector{/t}</span></legend>
		<p>{t}The news is not associated to any colector{/t}.</p>
		</fieldset>
	{/if}

{/if}
