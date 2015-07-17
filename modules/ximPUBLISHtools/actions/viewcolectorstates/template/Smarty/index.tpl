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

<tr><td align="center" class="filaclara"><br>
<table class="tabla" width="500" align="center" style="margin:5px auto;" cellpadding="0" cellspacing="0">
	<tr>
		<td align="center" class="filaclara">
			<div class="nodo_padre">
				<div class="cabecera_nodo">
					<div class="titulo_nodo_padre">
						<h1>Colectores de la sección "{$section_name}"</h1>
					</div>
				</div>
				<div class="separador"></div>
			</div>
		</td>
	</tr>
	{if $colectors|@count eq 0}
	<tr>
		<td align="center" class="filaoscura">
			<div class="mensaje_respuesta">
				<span>No hay colectores definidos en esta sección. </span>
			</div>
		</td>
	</tr>
	{else}
	<tr>
		<td align="center" class="filaclara">
		
		<div class="nodo_padre">
			<div class="cabecera_nodo">
					<div class="titulo_nodo_padre">
							<h1>Filtrado</h1>
					</div>
					<div class="detalles">
															
					</div>
			</div>
			<div class="separador"></div>
			<div class="contenido_nodo_filtro">
				<div class="titulos_combos">
					<div class="titulo1">
						Seleccione el tipo de colector que desea visualizar:
					</div>
				</div>
				<div class="combos_titulos">
					<div class="combo1">
						<select onChange="$(this).closest('.ui-tabs').canvas('get_widget', this).canvas_i('get_colectors_data', 1);" id="filter_colector_state">
							<option value="1">Todos</option>
							<option value="2">En generación</option>
							<option value="3">Pendientes de generación</option>
							<option value="4">Generados</option>
							<option value="5">En publicación</option>
						</select>
					</div>
					 <div class="flotacion"></div>
				</div>
			</div>
		</div>
		
		{foreach from=$colectors key=index item=colector}
			<div class="nodo" id="nodo_{$colector.id_colector}">
				<div class="cabecera_nodo">
					<div class="titulo_nodo">
						<h1 title="{$colector.name}"><span class="lote">{$colector.name}:</span> <span id="state_title_{$colector.id_colector}">{$colector.state}</span></h2>
					</div>
					<div class="detalles">
						<a onclick="$(this).closest('.ui-tabs').canvas('get_widget', this).canvas_i('toggle_details', {$colector.id_colector});" class="ver_detalles" id="toggle_details_{$colector.id_colector}" >Ver Detalles</a>							
					</div>
				</div>
				<div class="contenido_nodo">
					<div class="barra_progreso">
						<img id="progress_img_{$colector.id_colector}" style='width:{$colector.progress}px;height:6px;' src='{$_URL_ROOT}/xmd/images/pix_green.png' title="Pendiente">							
						<br />
						<div class="borde_cien"></div>
						<span class="mensaje_progreso"><span id="progress_{$colector.id_colector}">{$colector.progress}</span>% Completado </span>
					</div>
					<div class="botones_control">
						<div class="mensaje_estado"><span id="state_content_{$colector.id_colector}">{$colector.state}</span> <img id="state_image_{$colector.id_colector}" style="width: 15px;{if $colector.state ne 'Gener&aacute;ndose' and $colector.state ne 'Generado y Public&aacute;ndose'}display: none;{/if}" src="{$_URL_ROOT}/xmd/icons/ajax-loader.gif" /></div>
						<div class="cabecera_mensaje_generacion">
							<span id="start_generation_title_{$colector.id_colector}">{if $colector.start_generation}Comienzo de la generación:{/if}</span> <br/>
							{*<span id="end_generation_title_{$colector.id_colector}">{if $colector.end_generation}Comienzo de la publicación:{/if}</span> <br/>*}
							<span id="end_publication_title_{$colector.id_colector}">{if $colector.end_publication}Fin de la generación:{/if}</span> <br/>
						</div>
						<div class="contenido_mensaje_generacion">
							<span id="start_generation_{$colector.id_colector}">{if $colector.start_generation} {$colector.start_generation|date_format:"%d/%m/%Y %H:%M:%S"} {else} - {/if}</span><br/>
							{*<span id="end_generation_{$colector.id_colector}">{if $colector.end_generation} {$colector.end_generation|date_format:"%d/%m/%Y %H:%M:%S"} {else} - {/if}</span><br/>*}
							<span id="end_publication_{$colector.id_colector}">{if $colector.end_publication} {$colector.end_publication|date_format:"%d/%m/%Y %H:%M:%S"} {else} - {/if}</span><br/>
						</div>
					</div>
					<div class="flotacion"></div>
				</div>
				<div id="node_content_details_{$colector.id_colector}" class="contenido_nodo_detalles" style="display:none;">
					<table id="tabla_detalles_{$colector.id_colector}" class="tabla_detalles" cellpadding="0" cellspacing="0">
						<tr class="titulos">
							<td class="fichero">Noticia</td>
							<td class="fichero">Asociada por</td>
							<td class="fichero">Fecha de Asociación</td>
							<td class="fichero">Inicio Vigencia</td>
							<td class="fichero">Fin Vigencia</td>
							<td>Estado</td>													
						</tr>
						{foreach from=$colector.pending_relations key=id_new item=pending_relation}
						<tr class="pending_relation_{$colector.id_colector}" id="id_new_{$colector.id_colector}_{$id_new}" bgcolor="{cycle values="#f9f9f9,#eeeeee"}">
							<td class="fichero_pendiente"><span id="new_name_{$id_new}_{$colector.id_colector}">{$pending_relation.NewName}</span></td>
							<td class="fichero_pendiente"><span id="new_user_name_{$id_new}_{$colector.id_colector}">{$pending_relation.UserName}</span></td>
							<td class="fichero_pendiente"><span id="new_time_{$id_new}_{$colector.id_colector}">{$pending_relation.Time|date_format:"%d/%m/%Y %H:%M:%S"}</span></td>
							<td class="fichero_pendiente"><span id="new_fechain_{$id_new}_{$colector.id_colector}">{$pending_relation.FechaIn|date_format:"%d/%m/%Y %H:%M:%S"}</span></td>
							<td class="fichero_pendiente"><span id="new_fechaout_{$id_new}_{$colector.id_colector}">{$pending_relation.FechaOut|date_format:"%d/%m/%Y %H:%M:%S"}</span></td>
							<td>
								{if $pending_relation.State eq 'pending'}
									{*<span id="new_state_{$id_new}_{$colector.id_colector}">Pendiente</span>*}
									<img id="new_image_{$id_new}_{$colector.id_colector}" src="{$_URL_ROOT}{$smarty.const.MODULE_XIMPUBLISHTOOLS_PATH}/images/reports/out.gif" width="17" height="15" title="Pendiente"/>
								{/if}
								{if $pending_relation.State eq 'InBulletin' or $pending_relation.State eq 'publishable'}
									{*<span id="new_state_{$id_new}_{$colector.id_colector}">Asociada</span>*}
									<img id="new_image_{$id_new}_{$colector.id_colector}" src="{$_URL_ROOT}{$smarty.const.MODULE_XIMPUBLISHTOOLS_PATH}/images/reports/in.gif" width="17" height="15" title="Asociada"/>
								{/if}
							</td>
						</tr>
						{/foreach}
						{if $colector.pending_relations|@count eq 0}
						<tr id="pending_relation_{$colector.id_colector}" bgcolor="{cycle values="#f9f9f9,#eeeeee"}">
							<td colspan="6" align="center" class="filaoscura">
								<div class="mensaje_respuesta">
									<span>No hay noticias pendientes de asociar a este colector. </span>
								</div>
							</td>
						</tr>
						{/if}
						
						{*<tr>
							<td colspan="6">
								<div class="pagina_tabla">
									<div class="paginacion">
										<div class="anterior">
											<a href="#" title="Anterior" onClick="$(this).closest('.ui-tabs').canvas('get_widget', this).canvas_i('change_page', {$colector.id_colector},'prev'); $(this).closest('.ui-tabs').canvas('get_widget', this).canvas_i('get_colectors_data', 'prev');">Anterior</a>
										</div>
										<div class="paginas">
											Pag. <span class="page" id="page_{$colector.id_colector}">{$id_page}</span>
										</div>
										<div class="siguiente ">
											<a href="#" title="Siguiente" onClick="$(this).closest('.ui-tabs').canvas('get_widget', this).canvas_i('change_page', {$colector.id_colector},'next'); $(this).closest('.ui-tabs').canvas('get_widget', this).canvas_i('get_colectors_data', 'next');">Siguiente</a>
										</div>
									</div>
								</div>
							</td>
						</tr>*}	
					</table>
				</div>
				<div class="pie_nodo">
					<ul>
						<li class="dia"><span id="user_name_{$colector.id_colector}">{$colector.user_name}</span></li>
						<li class="fecha">Usuario:</li>
						<li class="dia"><span id="last_generation_{$colector.id_colector}">{$colector.last_generation|date_format:"%d/%m/%Y %H:%M:%S"}</span></li>
						<li class="fecha">Última generación:</li>
					</ul>
				</div>
			</div>
		{/foreach}
		</td>
	</tr>
	{/if}
	<tr>
		<td colspan="6">
			<div class="pie_tabla">
				<img src="{$_URL_ROOT}{$smarty.const.MODULE_XIMPUBLISHTOOLS_PATH}/images/reports/out.gif" width="17" height="15" title="Pendiente"/> Noticia pendiente de ser incluída en un boletín.
				<img src="{$_URL_ROOT}{$smarty.const.MODULE_XIMPUBLISHTOOLS_PATH}/images/reports/in.gif" width="17" height="15" title="Pendiente"/> Noticia incluída en un boletín. <br/>
			</div>
		</td>
	</tr>
</table>
</td>
</tr>
