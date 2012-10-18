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

<script type="text/javascript" language="JavaScript" src="..{$smarty.const.MODULE_XIMSYNC_PATH}/js/managebatchs.js"></script>

<form name="frm_batchs" method="post">

<input type="hidden" name="frm_id_batch" id="frm_id_batch" value="0">
<input type="hidden" name="frm_deactivate_batch" id="frm_deactivate_batch" value="no">
<input type="hidden" name="frm_activate_batch" id="frm_activate_batch" value="no">
<input type="hidden" name="frm_prioritize_batch" id="frm_prioritize_batch" value="no">
<input type="hidden" name="frm_deprioritize_batch" id="frm_deprioritize_batch" value="no">

<input type="hidden" name="frm_filter_node_gen" id="frm_filter_node_gen" value="">
<input type="hidden" name="frm_filter_state_batch" id="frm_filter_state_batch" value="">
<input type="hidden" name="frm_filter_active_batch" id="frm_filter_active_batch" value="">
<input type="hidden" name="frm_filter_batch" id="frm_filter_batch" value="no">

<table class="tabla" width="560" align="center" cellpadding="2">
	<tr>
		<td class="filacerrar" align="right">
			<a href="javascript:parent.deletetabpage(parent.selected);" class="filacerrar">
				{t}Close window{/t} <img src="../../../../xmd/images/botones/cerrar.gif" alt="" border="0">
			</a>
		</td>
	</tr>
	{if isset($errorBox) && $errorBox ne ""}
		<tr>
			<td align="center" class="filaclara"><br>

				<table align=center class=tabla width=500 cellpadding="0" cellspacing="1">
						<tr>
							<td class="cabeceratabla">
								{t}INFO{/}
							</td>
						</tr>
						<tr>
							<td class="filaoscura">
								<br />
								<strong>{$errorBox}</strong>
								<br /><br />
							</td>
						</tr>
				</table>

			</td>
		</tr>
	{/if}
	<tr>
		<td align="center" class="filaclara"><br>

			<table align=center class=tabla width=500 cellpadding="0" cellspacing="1">
				<tr>
					<td align="right" colspan="3" class=cabeceratabla>
						{if $hasBatchs eq 'true'}
							<select name="frm_select_filter_node_gen" id="frm_select_filter_node_gen">
							<option value="">{t}Any node generates{/t}...</option>
							{foreach from=$distinctNodeGenerators item=Node key=idDistinctNode}
								<option
								{if $idDistinctNode eq $frm_select_filter_node_gen}
									selected
								{/if}
								value="{$idDistinctNode}">{$Node.Name}</option>
							{/foreach}
							</select>
						{/if}
						<select name="frm_select_filter_state_batch" id="frm_select_filter_state_batch">
							<option
							{if $frm_select_filter_state_batch eq "Any"}
								selected
							{/if}
							value="Any">{t}Any state{/t}</option>
							<option
							{if $frm_select_filter_state_batch eq "Waiting"}
								selected
							{/if}
							value="Waiting">{t}Waiting{/t}</option>
							<option
							{if $frm_select_filter_state_batch eq "In Time"}
								selected
							{/if}
							value="In Time">{t}In Time{/t}</option>
							<option
							{if $frm_select_filter_state_batch eq "Ended"}
								selected
							{/if}
							value="Ended">{t}Ended{/t}</option>
						</select>
						&nbsp;
						<select name="frm_select_filter_active_batch" id="frm_select_filter_active_batch">
							<option
							{if $frm_select_filter_active_batch eq "Any"}
								selected
							{/if}
							value="Any">{t}Any{/t}</option>
							<option
							{if $frm_select_filter_active_batch eq "1"}
								selected
							{/if}
							value="1">{t}Active{/t}</option>
							<option
							{if $frm_select_filter_active_batch eq "0"}
								selected
							{/if}
							value="0">{ŧ}Inactive{/t}</option>
						</select>
						&nbsp;
						<input type="button" name="frm_filter_batch_button" value="{t}Filter now{/t}"
							class="boton" onClick="javascript: doFilterSubmit();">
					</td>
				</tr>
			</table>

		</td>
	</tr>
	<tr>
		<td align="center" class="filaclara"><br>

			<table align=center class=tabla width=500 cellpadding="0" cellspacing="1">
				<tr>
					<td align="right" colspan="2" class=cabeceratabla>
						<input type="button" name="frm_deactivate_batch_button_all" value="{t}Pause All{/t}"
						class="boton" onClick="javascript: doDeactivateSubmit('all');">
					</td>
					<td align="left" colspan="2" class=cabeceratabla>
						<input type="button" name="frm_activate_batch_button_all" value="{t}Active All{/t}"
						class="boton" onClick="javascript: doActivateSubmit('all');">
					</td>
				</tr>
			</table>

		</td>
	</tr>

	{if $hasBatchs eq 'true'}
		{foreach from=$batchs key=id item=batch}
	<tr>
		<td align="center" class="filaclara"><br>
			<table align=center class=tabla width=500 cellpadding="0" cellspacing="1">
				<tr>
					<td class=cabeceratabla>
						{$distinctNodeGenerators[$batch.IdNodeGenerator].Path}
						&nbsp;&nbsp;&nbsp;
						[<a href="#" onClick="javascript: showOrHideContent('batch{$batch.IdBatch}');">See frames</a>]
					</td>
					<td class=cabeceratabla>
						{t}Date{/t}:
						&nbsp;
						{$batch.TimeOn|date_format:"%d/%m/%Y %H:%M:%S"}
					</td>
					<td class=cabeceratabla>
						{t}User{/t}:
						&nbsp;
						{t}Publisher{/t}
					</td>
				</tr>
			</table>
			<table align=center class=tabla width=500 cellpadding="0" cellspacing="1">
				<tr>
					<td class=cabeceratabla>{t}Activation{/t}</td>
					<td class=cabeceratabla>{t}State{/t}</td>
					<td class=cabeceratabla>{t}Type{/t}</td>
					<td class=cabeceratabla>{t}Active{/(t}</td>
					<td class=cabeceratabla>{t}Node{/t}</td>
					<td class=cabeceratabla>{t}Action{/t}</td>
				</tr>
				<tr>
					<td class="filaoscura">
						{$batch.TimeOn|date_format:"%d/%m/%Y %H:%M:%S"}
						-
						{$batch.downBatch.TimeOn|date_format:"%d/%m/%Y %H:%M:%S"}
					</td>
					<td class="filaoscura">
						{$arrayStates.Batch[$batch.State]}
					</td>
					<td class="filaoscura">
						{if $batch.Type eq "Down"}
							{t}Unpublishing{/t}
						{else}
							{t}Publication{/t}
						{/if}
					</td>
					<td class="filaoscura">
						{if $batch.Playing eq 1}
							{t}Activo{/t}
						{else}
							{t}Inactive{/t}
						{/if}
					</td>
					<td class="filaoscura">
						{$distinctNodeGenerators[$batch.IdNodeGenerator].Path}
					</td>
					<td align="center" class="filaoscura">
						{if $batch.Playing eq 1}
							<input type="button" name="frm_deactivate_batch_button" value="{t}Pause{/t}"
							class="boton" onClick="javascript: doDeactivateSubmit({$batch.IdBatch});">
						{else}
							<input type="button" name="frm_activate_batch_button" value="{t}Activate{/t}"
							class="boton" onClick="javascript: doActivateSubmit({$batch.IdBatch});">
						{/if}
						&nbsp;&nbsp;
							<a title="{t}Prioritize Batch{/t}" href="javascript: doPrioritizeSubmit({$batch.IdBatch});">
								<img src="{$urlRoot}/xmd/images/botones/subir_p.gif" alt="" border="0">
							</a>
							<a title="{t}Unprioritize Batch{/t}" href="javascript: doDeprioritizeSubmit({$batch.IdBatch});">
								<img src="{$urlRoot}/xmd/images/botones/bajar_p.gif" alt="" border="0">
							</a>
					</td>
				</tr>
			</table>
			{if $batch.serverFrames}
			<div id='batch{$batch.IdBatch}' style='display: none;'>
				<table align=center class=tabla width=500 cellpadding="0" cellspacing="1">
					<tr>
						<td colspan="6" class="filaclara">
							{foreach from=$batch.serverFrames key=idServer item=server}
							<br />
							<table align=center class=tabla width=98% cellpadding="0" cellspacing="1">
								<tr>
									<td colspan="6" class="filaoscura">
										<strong>{t}Sub-batch{/t} #{$batch.IdBatch}.{$iSubBatch}</strong></td>
								</tr>
								<tr>
									<td class="filaoscura">{t}File{/t}</td>
									<td class="filaoscura">{t}Size{/t} (Kb.)</td>
									<td class="filaoscura">{t}Publication{/t}</td>
									<td class="filaoscura">{t}Unpublishing{/t}</td>
									<td class="filaoscura">{t}State{/t}</td>
									<td class="filaoscura">{t}View{/t}</td>
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
							</table>
							{/foreach}
							<br />
						</td>
					</tr>
				</table>
			</div>
			{/if}

			<table align=center class=tabla width=500 cellpadding="0" cellspacing="1">
				<?php $iSubBatch = 1 ?>
				{if $batch.progress}
				<tr>
					<td colspan="6" class="filaclara">
						<br />
						<table align=center class=tabla width=98% cellpadding="0" cellspacing="1">
							<tr>
								<td colspan="8" class="filaoscura">{t}Progress{/t}</td>
							</tr>
							<tr>
								<td class="filaoscura">{t}Sub-batch{/t}</td>
								<td class="filaoscura">{t}Files{/t}</td>
								<td class="filaoscura">{t}Total{/t} (Kb.)</td>
								<td class="filaoscura">{t}Avg.{/t}. (Kb.)</td>
								<td colspan="2" class="filaoscura">% (Kb.)</td>
								<td colspan="2" class="filaoscura">% (#)</td>
							</tr>
							{foreach from=$batch.progress key=subBatch item=progress}
								{if $subBatch ne 'total'}
								<tr>
									<td class="filaoscura">
										<strong>{t}Sub-batch{/t} #{$batch.IdBatch}.{$iSubBatch}</strong>
									</td>
									<td class="filaclara">
										{$progress.totalBatch}
									</td>
									<td class="filaclara">
										{$progress.totalBatchSize}
									</td>
									<td class="filaclara">
										{$progress.avgBatchSize}
									</td>
									<td class="filaclara">
										{$progress.percentBatchSizeCompleted}
									</td>
									<td class="filaclara">
										showProgressBar({$progress.percentBatchSizeCompleted})
									</td>
									<td class="filaclara">
										{$progress.percentBatchCompleted}
									</td>
									<td class="filaclara">
										showProgressBar({$progress.percentBatchCompleted})
									</td>
								</tr>
								{/if}
							{/foreach}
							<tr>
								<td class="filaoscura">
									<strong>{t}Total{/t}</strong>
								</td>
								<td class="filaclara">
									{$batch.progress.total.totalBatch}
								</td>
								<td class="filaclara">
									{$batch.progress.total.totalBatchSize}
								</td>
								<td class="filaclara">
									{$batch.progress.total.avgBatchSize}
								</td>
								<td class="filaclara">
									{$batch.progress.total.percentBatchSizeCompleted}
								</td>
								<td class="filaclara">
									showProgressBar({$batch.progress.total.percentBatchSizeCompleted})
								</td>
								<td class="filaclara">
									{$batch.progress.total.percentBatchCompleted}
								</td>
								<td class="filaclara">
									showProgressBar({$batch.progress.total.percentBatchCompleted})
								</td>
							</tr>
						</table>
						<br />
					</td>
				</tr>
				{/if}
			</table>
		</td>
	</tr>
	{/foreach}
	{else}
		<tr>
			<td align="center" class="filaclara"><br>
				<table align=center class=tabla width=500 cellpadding="0" cellspacing="1">
						<tr>
							<td colspan="6" class="filaoscura">
								{t}There are no batches matching with the indicated criteria.{/t}.
							</td>
						</tr>
				</table>
			</td>
		</tr>
	{/if}
			</table>
		</td>
	</tr>
</table>

</form>
