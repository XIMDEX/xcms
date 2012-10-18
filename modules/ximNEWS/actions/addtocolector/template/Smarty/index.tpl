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

<form method="POST" name="ca_form" id="ca_form" action="{$action_url}">
	<fieldset>
		<input type="hidden" id="nodeid" name="nodeid" value="{$id_node}"/>
		<legend><span>{t}Available colectors{/t}</span></legend>
<p>{t}The news can be associated to one, or several, of the following colectors{/t}</p>
		<!--<label style="display:none" for="colectores">Colector</label>-->
		<table class='tabla'>
			<tr><tr><th>{t}Colector{/t}</th><th>{t}Associated category{/t}</th></tr>

				{foreach from=$colectors key=ene item=colector}
					<tr>
						<td>
							<input {$colector.checked} {$asoc_disabled}
								type='checkbox'	name='colectorsidlst[]'
								value='{$colector.id}' id='colectorsidlst'
								class='validable colectores check_group__colectores'>
							{$colector.name}

							{if $colector.global neq '0'}
								<sup> (3) </sup>
							{/if}
						</td>
						<td>
							{if $colector.area neq ''}

								{$colector.area}

								{if $colector.compatible neq ''}
									{if $attempts eq '0'}
										<sup> (2) </sup>
									{else}
										<sup> (1) </sup>
									{/if}
								{/if}

							{/if}
						</td>
					</tr>
				{/foreach}
			</table>
</fieldset>
<fieldset>
		<legend><span>{t}News version{/t}</span></legend>

		<ol>
			<li>
				<label>{t}Last edited version{/t}</label>
				<input checked type='radio' {$asoc_disabled} id='versiones'
					name='versiones'
				value='{$versions.lastversion} - {$versions.lastsubversion}'>
				{t}Version{/t}
				{$versions.lastversion} - {$versions.lastsubversion}
			</li>

			{if $versions.publishedversion neq ''}
				<li>
					<label>{t}Last published version{/t}</label>
					<input type='radio' {$asoc_disabled} id='versiones'
						name='versiones'
					value='{$versions.publishedversion} - {$versions.publishedsubversion}'> {t}Version{/t}
						{$versions.publishedversion} - {$versions.publishedsubversion}
				</li>
			{/if}
		</ol>
</fieldset>
<fieldset
		<legend><span>{t}Association period life{/t}</span></legend>
		<div class="xim-calendar-container">
			<calendar
				timestamp="{$time_stamp}"
				date_field_name="date"
				hour_field_name="hour"
				min_field_name="min"
				sec_field_name="sec"
				format="d-m-Y H:i:s"
				type="from"
				rel='{$news_name}
				cname="fechainicio"
			/>


			<calendar
				date_field_name="date"
				hour_field_name="hour"
				min_field_name="min"
				sec_field_name="sec"
				format="d-m-Y H:i:s"
				rel='{$news_name}
				type="to"Asociar
				cname="fechafin"
			/>
		</div>
		<p>
			<sup>(1)</sup>
			{t}This colector has a category compatible with the news{/t}
		</p>
		<p><sup>(2)</sup>{t}Suggested association{/t}</p>
		<p><sup>(3)</sup>{t}Global colector{/t}</p>

	</fieldset>

	<fieldset class="buttons-form">
		{if $asoc_disabled}
			{button label='Enable' class='enable_checks'}
		{/if}
		{button label='Associate' class='asoc enable_checks validate'}
	</fieldset>
</form>
