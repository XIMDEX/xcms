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

<form id="print_form" name="frm_batchs" action="{$action_url}" method="post">

	{if isset($errorBox) && $errorBox ne ""}
	<fieldset>
		<legend><span>{t}INFO{/t}</span></legend>
		<ol>
			<li><strong>{$errorBox}</strong></li>
		</ol>
	</fieldset>
	{/if}

	<fieldset>
		<legend><span>{t}Criterios{/t}</span></legend>
		<input type="hidden" name="frm_id_batch" id="frm_id_batch" value="0">
		<input type="hidden" name="frm_deactivate_batch" id="frm_deactivate_batch" value="no">
		<input type="hidden" name="frm_activate_batch" id="frm_activate_batch" value="no">
		<input type="hidden" name="frm_prioritize_batch" id="frm_prioritize_batch" value="no">
		<input type="hidden" name="frm_deprioritize_batch" id="frm_deprioritize_batch" value="no">
		
		<input type="hidden" name="frm_filter_node_gen" id="frm_filter_node_gen" value="">
		<input type="hidden" name="frm_filter_state_batch" id="frm_filter_state_batch" value="">
		<input type="hidden" name="frm_filter_active_batch" id="frm_filter_active_batch" value="">
		<input type="hidden" name="frm_filter_up_date" id="frm_filter_up_date" value="0">
		<input type="hidden" name="frm_filter_down_date" id="frm_filter_down_date" value="0">
		<input type="hidden" name="frm_filter_batch" id="frm_filter_batch" value="no">

		<tr>
			<td align="center" class="filaclara"><br>
		
				<table align="center" class="tabla" width="500" cellpadding="0" cellspacing="1">
					<tr>
						<td align="right" colspan="4" class="cabeceratabla">
							<select name="frm_select_filter_node_gen" id="frm_select_filter_node_gen">
							<option value="">Cualquier Nodo Generador...</option>
							{if $distinctNodeGenerators}
								{foreach from=$distinctNodeGenerators item=Node key=idDistinctNode}
									<option
									{if $idDistinctNode eq $frm_select_filter_node_gen}
										selected
									{/if}
									value="{$idDistinctNode}">[{$idDistinctNode}]{$Node.Name}</option>
								{/foreach}
							{/if}
							</select>
							<select name="frm_select_filter_state_batch" id="frm_select_filter_state_batch">
								<option
								{if $frm_select_filter_state_batch eq "Any"}
									selected
								{/if}
								value="Any">Cualquier Estado</option>
								<option
								{if $frm_select_filter_state_batch eq "Waiting"}
									selected
								{/if}
								value="Waiting">En Cola</option>
								<option
								{if $frm_select_filter_state_batch eq "InTime"}
									selected
								{/if}
								value="InTime">En Curso</option>
								<option
								{if $frm_select_filter_state_batch eq "Ended"}
									selected
								{/if}
								value="Ended">Finalizado</option>
							</select>
							&nbsp;
							<select name="frm_select_filter_active_batch" id="frm_select_filter_active_batch">
								<option
								{if $frm_select_filter_active_batch eq "Any"}
									selected
								{/if}
								value="Any">Cualquiera</option>
								<option
								{if $frm_select_filter_active_batch eq "1"}
									selected
								{/if}
								value="1">Activos</option>
								<option
								{if $frm_select_filter_active_batch eq "0"}
									selected
								{/if}
								value="0">Inactivos</option>
							</select>
							&nbsp;
							<input type="button" name="frm_filter_batch_button" value="Filtrar" 
								class="boton" onClick="javascript: doFilterSubmit();">
							{button label="Filtrar"}
						</td>
					</tr>
					<tr>
						<td class="cabeceratabla">
							Fecha m&iacute;nima: 
						</td>
						<td class="cabeceratabla">
							<input name="update" id="update" style='text-align: center; font-size:11px; width: 150px; border: 1px solid black;' type=text value="{$arrayDateValues.update}" onclick='showCalendar(this, this, "yyyy-mm-dd","es",1)'>
						</td>
						<td class="cabeceratabla">
							Fecha m&aacute;xima: 
						</td>
						<td class="cabeceratabla">
							<input name="downdate" id="downdate" style='text-align: center; font-size:11px; width: 150px; border: 1px solid black;' type=text value="{$arrayDateValues.downdate}" onclick='showCalendar(this, this, "yyyy-mm-dd","es",1)'>
						</td>
					</tr>
					<tr>
						<td class="cabeceratabla">
							Hora:
						</td>
						<td class="cabeceratabla">
							<input name="uphour" id="uphour" type="text" maxlength="2" value="{$arrayDateValues.uphour}" style='text-align: center; font-size:11px; width: 20px; border: 1px solid black;'>
							 : 
							<input name="upmin" id="upmin" type="text" maxlength="2" value="{$arrayDateValues.upmin}" style='text-align: center; font-size:11px; width: 20px; border: 1px solid black;'>
						</td>
						<td class="cabeceratabla">
							Hora:
						</td>
						<td class="cabeceratabla">
							<input name="downhour" id="downhour" type="text" maxlength="2" value="{$arrayDateValues.downhour}" style='text-align: center; font-size:11px; width: 20px; border: 1px solid black;'>
							 : 
							<input name="downmin" id="downmin" type="text" maxlength="2" value="{$arrayDateValues.downmin}" style='text-align: center; font-size:11px; width: 20px; border: 1px solid black;'>
						</td>
					</tr>
				</table>
				
			</td>
		</tr>
		<tr>
			<td align="center" class="filaclara"><br>
		
				<table align="center" class="tabla" width="500" cellpadding="0" cellspacing="1">
					<tr>
						<td align="right" colspan="2" class="cabeceratabla">
							<input type="button" name="frm_deactivate_batch_button_all" value="Pausar Todos" 
							class="boton" onClick="javascript: doDeactivateSubmit('all');">
						</td>
						<td align="left" colspan="2" class="cabeceratabla">
							<input type="button" name="frm_activate_batch_button_all" value="Activar Todos" 
							class="boton" onClick="javascript: doActivateSubmit('all');">
						</td>
					</tr>
				</table>
				
			</td>
		</tr>
	</fieldset>
	
	<fieldset>
		<legend><span>{t}Resultado{/t}</span></legend>	
		{if $hasBatchs eq 'true'}
			{foreach from=$batchs key=id item=batch}
			<tr>
			<td align="center" class="filaclara"><br>
				<table align="center" class="tabla" width="500" cellpadding="0" cellspacing="1">
					<tr>
						<td class="cabeceratabla">
							Nodo: {$distinctNodeGenerators[$batch.IdNodeGenerator].Path}
							&nbsp;&nbsp;&nbsp;
							[<a href="#" onClick="javascript: showOrHideContent('{$id}_1', '{$id}', 'all');">Ver Frames</a>]
						</td>
					</tr>
				</table>
				<table align="center" class="tabla" width="500" cellpadding="0" cellspacing="1">
					<tr>
						<td class="cabeceratabla">
							Fecha:
							&nbsp;
							{$batch.TimeOn|date_format:"%d/%m/%Y %H:%M:%S"}
						</td>
						<td align="right" class="filaoscura">
							<table cellpadding='0' cellspacing='0'>
								<tr>
									<td>
										{if $batch.progress.total.percentBatchCompleted > 0}
											<img style='width:{$batch.progress.total.percentBatchCompleted}px;height:6px;' src='{$urlRoot}/xmd/images/pix_green.png'>
										{/if}
									</td>
									<td>
										{if $batch.progress.total.percentBatchCompleted < 100}
											<img style='width:{100 - $batch.progress.total.percentBatchCompleted}px;height:6px;' src='{$urlRoot}/xmd/images/pix_red.png'>
										{/if}
									</td>
								</tr>
							</table>
						</td>
						<td align="right" class="filaoscura">
							{if $batch.Playing eq 1}
								<input title="Pausar" type="button" name="frm_deactivate_batch_button" value=" || " 
								class="boton" onClick="javascript: doDeactivateSubmit({$batch.IdBatch});">
							{else}
								<input title="Activar" type="button" name="frm_activate_batch_button" value=" > " 
								class="boton" onClick="javascript: doActivateSubmit({$batch.IdBatch});">
							{/if}
							&nbsp;&nbsp;
							<a title="Priorizar Lote" href="javascript: doPrioritizeSubmit({$batch.IdBatch});">
								<img src="{$urlRoot}/xmd/images/botones/subir_p.gif" alt="" border="0">
							</a>
							&nbsp;
							<a title="Despriorizar Lote" href="javascript: doDeprioritizeSubmit({$batch.IdBatch});">
								<img src="{$urlRoot}/xmd/images/botones/bajar_p.gif" alt="" border="0">
							</a>
						</td>
					</tr>
				</table>
				{if $batch.serverFrames}
					{foreach from=$batch.serverFrames key=idPage item=serverFrame}
						<div name='{$id}' id='{$id}_{$idPage}' style='display: none;'>
							<table align="center" class="tabla" width="500" cellpadding="0" cellspacing="1">
								<tr>
									<td class="filaclara">
											{foreach from=$serverFrame key=idServer item=server}
												<br />
												<table align="center" class="tabla" width="98%" cellpadding="0" cellspacing="1">
													<tr>
														<td class="filaoscura">Fichero</td>
														<td class="filaoscura">Tama&ntilde;o (Kb.)</td>
														<td class="filaoscura">Publicaci&oacute;n</td>
														<td class="filaoscura">Despublicaci&oacute;n</td>
														<td class="filaoscura">Estado</td>
														<td class="filaoscura">Ver</td>
													</tr>
													{foreach from=$server key=idServerFrame item=serverFrame}
													<tr>
														<td class="filaclara">{$serverFrame.FileName}</td>
														<td class="filaclara">{$serverFrame.FileSize}</td>
														<td class="filaclara">{$serverFrame.DateUp|date_format:"%d/%m/%Y %H:%M:%S"}</td>
														<td class="filaclara">{$serverFrame.DateDown|date_format:"%d/%m/%Y %H:%M:%S"}</td>
														<td class="filaclara">{$arrayStates.ServerFrame[$serverFrame.State]}</td>
														<td class="filaclara">
														{if $serverFrame.State eq 'In'}
															<a target="_blank" href="{$activeServers.$idServer.Url}{$serverFrame.RemotePath}/{$serverFrame.FileName}">Ver</a>
														{/if}
														</td>
													</tr>
													{/foreach}
													<tr>
														<td align="center" colspan="6" class="filaoscura">
															[<a href="#" onClick="javascript: showOrHideContent('{$id}_1', '{$id}', '');">Primera</a>]
															{if $idPage > 1}
																[<a href="#" onClick="javascript: showOrHideContent('{$id}_{$idPage-1}', '{$id}', '');">Anterior</a>]
															{else}
																[<a href="#" onClick="javascript: showOrHideContent('{$id}_1', '{$id}', '');">Anterior</a>]
															{/if}
															Pag. {$idPage} de {$batch.totalPags}
															{if $idPage < $batch.totalPags}
																[<a href="#" onClick="javascript: showOrHideContent('{$id}_{$idPage+1}', '{$id}', '');">Siguiente</a>]
															{else}
																[<a href="#" onClick="javascript: showOrHideContent('{$id}_{$batch.totalPags}', '{$id}', '');">Siguiente</a>]
															{/if}
															[<a href="#" onClick="javascript: showOrHideContent('{$id}_{$batch.totalPags}', '{$id}', '');">Última</a>]
														</td>
													</tr>
												</table>
											{/foreach}
										<br />
									</td>
								</tr>
							</table>
						</div>
					{/foreach}
				{/if}
			</td>
		</tr>
		{/foreach}
		{else}
			<tr>
				<td align="center" class="filaclara"><br>
					<table align="center" class="tabla" width="500" cellpadding="0" cellspacing="1">
							<tr>
								<td colspan="6" class="filaoscura">
									No hay Lotes que coincidan con los criterios indicados.
								</td>
							</tr>
					</table>
				</td>
			</tr>
		{/if}
	</fieldset>

</form>

<script type="text/javascript" language="JavaScript">
	//setTimeout("doFilterSubmit()", 30000);
</script>
