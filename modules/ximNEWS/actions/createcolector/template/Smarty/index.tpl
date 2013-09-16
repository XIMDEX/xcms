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

<form method="post" name="ca_form" id="ca_form"  action="{$action_url}">
	<fieldset>
		<input type="hidden" name="nodeid" value="{$id_node}"/>
		<input type="hidden" id="inactive" name="inactive" value="{$colector_data.inactive}"/>
		<legend>
			<span>{if $go_method == 'editColector'}{t}Edit{/t}{else}{t}Create{/t}{/if} {t}news colector{/t}</span>
		</legend>
		<ol>
			<li>
				<label for="colector" class="aligned">{t}Name{/t}</label>
				<input type="text" id="colector" class="colectorname cajag validable not_empty" name="colector" value="{$colector_data.name}">
			</li>
			<li>
				<label for="template" class="aligned">{t}Template{/t}</label></td>
				<select name="template" id="template" class="cajag validable not_empty">
					<option value="">{t}Select template{/t}</option>

					{foreach from=$templates key=ene item=template}

						<option value="{$template.id}" {if $template.id == $colector_data.id_template}selected{/if}>{$template.name}</option>

					{/foreach}

				</select>
			</li>
			<li>
				<label class="aligned">{t}Colector type{/t}</label>
				<select name="tipo" class="cajag">
					<option value="numeronoticias">{t}Without grouping{/t}</option>
					<option value="fechaDia" {if $colector_data.filter == "fechaDia"}selected{/if}>{t}By date (Day){/t}</option>
					<option value="fechaSemana" {if $colector_data.filter == "fechaSemana"}selected{/if}>{t}By date (Week){/t}</option>
					<option value="fechaMes" {if $colector_data.filter == "fechaMes"}selected{/if}>{t}By date (Month){/t}</option>
				</select>
			</li>
			<li>
				<label class="aligned">{t}Category{/t}</label>
				<select name="idarea" class="cajag">
					<option value=""></option>
					{foreach from=$areas key=ene item=area}
						<option value="{$area.IdArea}" {if $colector_data.id_area == $area.IdArea}selected{/if}>{$area.Name}</option>
					{/foreach}
				</select>
			</li>
			<li>
				<label class="aligned">{t}Global{/t}</label>
				<select name="global" class="cajag">
					<option value="0">{t}No{/t}</option>
					<option value="1" {if $colector_data.global == 1}selected{/if}>{t}Si{/t}</option>
				</select>
			</li>
			{if $otfAvailable}
				<li>
					<label class="aligned">{t}Colector type{/t}</label>
					<select name="typeColector" class="cajag">
						<option value="estatico">{t}Static{/t}</option>
						<option value="otf" {if ($isOTF)}selected{/if}>{t}OTF{/t}</option>
						<option value="hibrido">{t}Hybrid{/t}</option>
					</select>
				</li>
			{/if}
			<li>
				<label class="aligned">{t}Page bulletins{/t}</label>
				<input class="unlockpagination" type="radio" name="paginacion" value="si" {if $colector_data.paginacion == 'si'}checked="checked"{/if} />
				<span>{t}Yes{/t}</span>
				<input class="lockpagination" type="radio" name="paginacion" value="no" {if $colector_data.paginacion == 'no'}checked="checked"{/if} /> <span>{t}No{/t}</span>
			</li>
			<li class="numnotbulletin">
				<label class="aligned">{t}Number of news per page{/t}</label>
				<input type="text" name="newsperbull" id="newsperbull" maxlength="4" class="newsperpage cajap" value="{$colector_data.news_per_bulletin}">
			</li>
			<li>
				<label class="aligned">{t}Order news{/t}</label>
				<div class="inline-block"><input name='order' type='radio' value='desc' {if $colector_data.order_news_in_bulletins == "desc"}checked{/if}>
				{t}New ones at the beginning (desc){/t}<br />
				<input name='order' type='radio' value='asc' {if $colector_data.order_news_in_bulletins == "asc"}checked{/if}> {t}New ones at the end (asc){/t}</div>
			</li>
			<li>
				<label class="aligned">{t}Send bulletins by email{/t}</label>
				<select class="cajag emailbulls">
					<option value="0">{t}Select{/t}</option>
					{foreach from=$lists key=id item=name}
						<option value="{$id}">{$name}</option>
					{/foreach}
				</select>
			</li>
			<li>
				<label class="aligned">{t}Enter a new address{/t}</label>
				<input type="text" name="listaid" maxlength="100" class="cajag"
				 value="{$colector_data.mail_list}"/>
			</li>
			<li>
				<label class="aligned">{t}Channel to send the mail{/t}</label>
				<select class="cajag" name="canal_correo">
					{foreach from=$channels key=ene item=channel}
						<option value="{$channel.id}" {if $colector_data.mail_channel == $channel.id}selected{/if}>{$channel.name}</option>
					{/foreach}
				</select>
			</li>
			<li>
				<label class="aligned">{t}Generate bulletins each{/t}</label>
				<input type="text" name="timetogenerate" maxlength="4" class="cajap" value="{$colector_data.time_to_generate}"> {t}hours{/t}
				<input type="checkbox" class="timetogenerate" id="timetogenerateenable" name="timetogenerateenable" value='1'/>
				<label for="timetogenerateenable">{t}Defuse{/t}</label>
			</li>
			<li>
				<label class="aligned">{t}Generate bulletins each{/t}</label>
				<input type="text" name="newstogenerate" maxlength="4" class="cajap" value="{$colector_data.news_to_generate}"> {t}news{/t}
				<input type="checkbox" class="newstogenerate" id="newstogenerateenable" name="newstogenerateenable" value='2'/>
				<label for="newstogenerateenable">{t}Defuse{/t}</label>
				</td>
			</li>
	</fieldset>

	<fieldset>
		<legend><span>{t}Languages{/t}</span></legend>
		<ol>
			{foreach from=$languages key=ene item=language}
				<li>
				<label class="aligned">{$language.name}</label>
					<input name="langidlst[]" class="langlist" type="checkbox" value="{$language.id}" {$colector_data.colector_langs[$language.id]} {if ($colector_data.master_lang ==  $language.id)}disabled="disabled"{/if}>
					<input type="text" name="namelst[{$language.id}]" class="cajag" value="{$colector_data.lang_alias[$language.id]}">
				</li>
			{/foreach}

			<li {if $go_method == 'editColector'}class="novisible"{/if}>
				<label class="aligned">{t}Select master language{/t}</label>
				<select {if $go_method == 'editColector'}disabled{/if} class="cajaxg" name='master'>
					<option value="">&laquo;{t}None{/t}&raquo;</option>
					{foreach from=$languages item=language}
						<option  {if $colector_data.master_lang eq $language.id}selected="selected"{/if} value="{$language.id}">
							{$language.name}</option>
					{/foreach}
				</select>
			</li>
		</ol>
		{if ($go_method != "createColector")}
			<p>{t}In order to defuse idiomatic versions of document previously published, defuse the corresponding language and unpublish the implicated documents (bulletins and news){/t}.</p>
		{/if}
	</fieldset>

	<fieldset>
		<legend><span>{t}Channels{/t} </span></legend>
		<ol>
			{foreach from=$channels key=ene item=channel}
				<li>
					<input name="channellst[]" class="channellist" type="checkbox" value="{$channel.id}" {$colector_data.colector_channels[$channel.id]}>
					<label>{$channel.name}</label>
				</li>
			{/foreach}
		</ol>
		{if ($go_method != "createColector")}
			<p>{t}In order to defuse publication channels of document previously published, defuse the corresponding channel and unpublish the implicated documents (bulletins and news){/t}.</p>
		{/if}
	</fieldset>

	<fieldset class="buttons-form">
		{if $go_method == "createColector"}
			{button label="Create" class="validate btn main_action" }	
		{else}
			{button label="Edit" class="validate btn main_action" }	
		{/if}
	</fieldset>
</form>
