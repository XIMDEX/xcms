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

<form method="POST" name="ca_form" enctype="multipart/form-data"
	class="creation_form" id="ca_form" action="{$action_url}">
	<fieldset>
		<input type="hidden" id="id_node" name="nodeid" value="{$id_node}"/>
		<input type="hidden" id="id_action" name="id_action" value="{$id_action}"/>
		<input type="hidden" id="id_section" name="id_section" value="{$id_section}"/>
		<input type="hidden" name="template" id="id_template" value="{$template}"/>
		<input type="hidden" name="master" id="master" value="{$master}"/>
		<input id="datesystem" type="hidden" name="datesystem" VALUE="{$datesystem}"/>
		<input type="hidden" name="form_elements" id="form_elements" value="{$form_elements}"/>

		{foreach from=$languages key=id item=alias}
			<input type="hidden" name="alias_lst[{$id}]" value="{$alias}" class="ecajag"/>
		{/foreach}

		{foreach from=$languages_table key=ene item=lang}
			<input type="hidden" name="langs_lst[{$lang.iso}]" id="{$lang.id}" value="{$lang.name}"/>
		{/foreach}

		{foreach from=$channels key=ene item=channel}
			<input type="hidden" name="channellst[]" value="{$channel}" class="ecajag"/>
		{/foreach}

		<input id="lote" type="hidden" value="{$loteid}" name="lote"/>
<legend><span>{t}Properties news{/t}</span></legend>
		<ol>
			<li>
				<label for="nombrenoticia" class="aligned">{t}Name{/t}</label>
				<input id="nombrenoticia"
					class="cajaxg nombrenoticia validable not_empty"
					type="text" name="nombrenoticia"/>
			</li>
			<li>
				<label class="aligned">{t}Date{/t}</label>
				<input name="noticia_fecha" class="datepickerhandler" type="text" value="{$smarty.now|date_format:"%d/%m/%Y"}" autocomplete="off">
			</li>
		</ol>
</fieldset>
<fieldset class="langs">
		<!--<legend><span>{t}Contenido de la noticia{/t}</span></legend>-->
		<div class="languages-news">
			<tabs class="language-tabs"
				nodeid="{$id_node}" actionid="{$id_action}"
				selector_class="tab_selector" scroller_class="tab_scroller"/>
		</div>

		<p class="sub-section">{t}Multimedia{/t}</p>
		<ol>
			{foreach from=$form_elements key=ene item=element}
				{if $element.type eq 'file_upload'}
					<li>
						<label class="aligned">{$element.label}</label>
						<input name="{$element.name}" value="{$element.label}"
							class="input_file" type="file"/>
					</li>
				{/if}
			{/foreach}
			<li>
				<label class="aligned">{t}Associated link{/t}</label>
				<input class="linksuggest" type="text"/>
				<input type="hidden" name="a_enlaceid_noticia_enlace_asociado"/>
			</li>
			<li>
				<label class="aligned">{t}Associated image{/t}</label>
				<input class="input_file" type="file" name="a_enlaceid_noticia_imagen_asociada" />
			</li>
		</ol>

		{if $num_areas > 0}
		<div class="left">
			<p class="sub-section">{t}Select the categories{/t}</p>

			<ol>
				{foreach from=$areas key=ene item=area}
					<li>
						<input name="areas[]" type="checkbox" value="{$area.IdArea}">
						<label>{$area.Name}</label>
					</li>
				{/foreach}
			</ol>
			</div>
		{/if}

		{if $num_colectors > 0}
		<div class="left">
			<p class="sub-section">{t}Select the colectors{/t}</p>

			<ol>
				{foreach from=$colectors key=id item=name}
					<li>
						<input name="colectorsidlst[]" type="checkbox" value="{$id}">
						<label>{$name}</label>
					</li>
				{/foreach}
			</ol>
			</div>
		{/if}
			<p class="sub-section">{t}Define news publication validity{/t}</p>

			<calendar
				timestamp="{$time_stamp}"
				date_field_name="date"
				hour_field_name="hour"
				min_field_name="min"
				sec_field_name="sec"
				format="d-m-Y H:i:s"
				type="from"
				cname="fechainicio"
			/>


			<calendar
				date_field_name="date"
				hour_field_name="hour"
				min_field_name="min"
				sec_field_name="sec"
				format="d-m-Y H:i:s"
				type="to"Asociar
				cname="fechafin"
			/>

	</fieldset>

	<fieldset class="buttons-form">
		{button label='Reset' class='form_reset' type='reset'}
		{button label='Create news' class='create_news validate'}
	</fieldset>
</form>
