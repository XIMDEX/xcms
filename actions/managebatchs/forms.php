<?php
/**
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
 */



	function batchListForm ($errorMsg = "", $printMode = false) {
		
		ob_start();
		
		$iSubBatch = 1;
		
		$arrayStates = array (
			'Batch' => array (
				'Waiting' => _('Waiting'),
				'InTime' => _('On time'),
				'Ended' => _('Finished')
			),
			'ServerFrame' => array (
				'Pending' => _('Pending'),
				'Due2In' => _('Ready to publish'),
				'Due2PumpedWithError' => _('With error'),
				'Due2In_' => _('Being published'),
				'Due2Out' => _('Ready to unpublish'),
				'Due2OutWithError' => _('With error'),
				'Due2Out_' => _('Being unpublished'),
				'In' => _('Published'),
				'Pumped' => _('Pumped'),
				'Out' => _('Unpublished'),
				'Replaced' => _('Replaced'),
				'Removed' => _('Deleted'),
				'Canceled' => _('Cancelled')
			)
		);
		
		if (isset($_POST['frm_select_filter_state_batch'])) {
			
			$arraySelects['frm_select_filter_state_batch'][$_POST['frm_select_filter_state_batch']] = "selected";
		}
		if (isset($_POST['frm_select_filter_active_batch'])) {
			
			$arraySelects['frm_select_filter_active_batch'][$_POST['frm_select_filter_active_batch']] = "selected";
		}
		
		$errorBox = ($errorMsg != "") ? batchShowErrorBox($errorMsg) : "";
	
		$doFilter = (isset($_POST['frm_filter_batch']) && $_POST['frm_filter_batch'] == "yes") ? true : false;
		$stateCryteria = (isset($_POST['frm_filter_state_batch'])) ? $_POST['frm_filter_state_batch'] : null;
		$activeCryteria = (isset($_POST['frm_filter_active_batch'])) ? $_POST['frm_filter_active_batch'] : null;
		
		$batchObj = new Batch();
		$batchs = $batchObj->getAllBatchs($doFilter ? $stateCryteria : null, $doFilter ? $activeCryteria : null, 'Up', MANAGEBATCHS_BATCHS_PER_PAGE);
		$hasBatchs = (is_array($batchs) && count($batchs) > 0) ? true : false;
		
		if ($hasBatchs) {
			
			$serverFrameObj = new ServerFrame();
			
			$activeServers = $serverFrameObj->getServers("complete");
			
			foreach ($batchs as $id => $batch) {
				
				$progress = array();
				$serverFrames = $serverFrameObj->getFramesOnBatch($batch['IdBatch'], 
								(($batch['Type'] == 'Up') ? 'IdBatch' : 'IdBatchDown'), 
								"extended", & $progress, MANAGEBATCHS_FRAMES_PER_PAGE);
				$hasServerFrames = (is_array($serverFrames) && count($serverFrames) > 0) ? true : false;
				
				if ($hasServerFrames) {
					
					$batchs[$id]['serverFrames'] = $serverFrames;
					$batchs[$id]['progress'] = $progress;
				}
				
				$downBatch = $batchObj->getDownBatch($batch['IdBatch']);
				if (is_array($downBatch) && count($downBatch) > 0) {
					
					$batchs[$id]['downBatch'] = $downBatch;
				}
			}
		}
	
		?>
		
		<form name="frm_batchs" method="post">
		
		<input type="hidden" name="frm_id_batch" id="frm_id_batch" value="0">
		<input type="hidden" name="frm_deactivate_batch" id="frm_deactivate_batch" value="no">
		<input type="hidden" name="frm_activate_batch" id="frm_activate_batch" value="no">
		
		<input type="hidden" name="frm_filter_state_batch" id="frm_filter_state_batch" value="">
		<input type="hidden" name="frm_filter_active_batch" id="frm_filter_active_batch" value="">
		<input type="hidden" name="frm_filter_batch" id="frm_filter_batch" value="no">
		
		<table class="tabla" width="560" align="center" cellpadding="2">
			<tr>
				<td class="filacerrar" align="right">
					<a href="javascript:parent.deletetabpage(parent.selected);" class="filacerrar"><?php echo
						_('Close window');?> <img src="<?php

 echo Config::getValue('UrlRoot'); ?>/xmd/images/botones/cerrar.gif" alt="" border="0">
					</a>
				</td>
			</tr>
			
			<?php

 echo $errorBox; ?>
	
			<tr>
				<td align="center" class="filaclara"><br>
			
					<table align=center class=tabla width=500 cellpadding="0" cellspacing="1">
						<tr>
							<td colspan="3" class=cabeceratabla><?php echo _('Batch list'); ?></td>
							<td align="right" colspan="3" class=cabeceratabla>
								<select name="frm_select_filter_state_batch" id="frm_select_filter_state_batch">
									<option <?php

 echo (isset($arraySelects['frm_select_filter_state_batch']["Any"])) ? $arraySelects['frm_select_filter_state_batch']["Any"] : "" ?> value="Any"><?php echo _('Any state'); ?></option>
									<option <?php

 echo (isset($arraySelects['frm_select_filter_state_batch']["Waiting"])) ? $arraySelects['frm_select_filter_state_batch']["Waiting"] : ""  ?> value="Waiting"><?php echo _('Waiting'); ?></option>
									<option <?php

 echo (isset($arraySelects['frm_select_filter_state_batch']["InTime"])) ? $arraySelects['frm_select_filter_state_batch']["InTime"] : ""  ?> value="InTime"><?php echo _('On time'); ?></option>
									<option <?php

 echo (isset($arraySelects['frm_select_filter_state_batch']["Ended"])) ? $arraySelects['frm_select_filter_state_batch']["Ended"] : ""  ?> value="Ended"><?php echo _('Finished'); ?></option>
								</select>
								&nbsp;
								<select name="frm_select_filter_active_batch" id="frm_select_filter_active_batch">
									<option <?php

 echo (isset($arraySelects['frm_select_filter_active_batch']["Any"])) ? $arraySelects['frm_select_filter_active_batch']["Any"] : "" ?> value="Any"><?php echo _('Any'); ?></option>
									<option <?php

 echo (isset($arraySelects['frm_select_filter_active_batch']["1"])) ? $arraySelects['frm_select_filter_active_batch']["1"] : "" ?> value="1"><?php echo _('Active'); ?></option>
									<option <?php

 echo (isset($arraySelects['frm_select_filter_active_batch']["0"])) ? $arraySelects['frm_select_filter_active_batch']["0"] : "" ?> value="0"><?php echo _('Inactive'); ?></option>
								</select>
								&nbsp;
								<input type="button" name="frm_filter_batch_button" value="Filtrar" 
									class="boton" onClick="javascript: doFilterSubmit();">
							</td>
						</tr>
					</table>
					
				</td>
			</tr>
			<?php

 if ($hasBatchs) : 

 foreach ($batchs as $id => $batch) : 

 $iSubBatch = 1 ?>
			<tr>
				<td align="center" class="filaclara"><br>
					<table align=center class=tabla width=500 cellpadding="0" cellspacing="1">
						<tr>
							<td class=cabeceratabla>
								<?php echo _('Batch');?> #<?php

 echo $batch['IdBatch']; ?>
								&nbsp;&nbsp;&nbsp;
								[<a href="#" onClick="javascript: showOrHideContent('batch<?php

 echo $batch['IdBatch']; ?>');"><?php echo _('See frames'); ?></a>]
							</td>
						</tr>
						<tr>
							<td class=filaoscura>
								<?php

 echo showTimeLine(array(array($batch['TimeOn'], (isset($batch['downBatch']['TimeOn'])) ? $batch['downBatch']['TimeOn'] : 0)));?>
							</td>
						</tr>
					</table>
					<table align=center class=tabla width=500 cellpadding="0" cellspacing="1">
						<tr>
							<td class=cabeceratabla><?php echo _('Activation'); ?></td>
							<td class=cabeceratabla><?php echo _('Status'); ?></td>
							<td class=cabeceratabla><?php echo _('Type'); ?></td>
							<td class=cabeceratabla><?php echo _('Active'); ?></td>
							<td class=cabeceratabla><?php echo _('Node'); ?></td>
							<td class=cabeceratabla><?php echo _('Action'); ?></td>
						</tr>
						<tr>
							<td class="filaoscura">
								<?php

 echo date("d-m-Y H:i:s", $batch['TimeOn']); ?>
								-
								<?php

 echo (isset($batch['downBatch']['TimeOn'])) ? date("d-m-Y H:i:s", $batch['downBatch']['TimeOn']) : 'Indefinida'; ?>
							</td>
							<td class="filaoscura">
								<?php

 echo $arrayStates['Batch'][$batch['State']]; ?>
							</td>
							<td class="filaoscura">
								<?php

 if ($batch['Type'] == "Down") : ?>
									<?php echo _('Publishing'); ?>
								<?php

 else: ?>
									<?php echo _('Unpublishing'); ?>
								<?php

 endif; ?>
							</td>
							<td class="filaoscura">
								<?php

 if ($batch['Playing'] == 1) : ?>
									<?php echo _('Active'); ?>
								<?php

 else: ?>
									<?php echo _('Inactive'); ?>
								<?php

 endif; ?>
							</td>
							<td class="filaoscura">
								<?php

 echo $batch['IdNodeGenerator']; ?>
							</td>
							<td align="center" class="filaoscura">
								<?php

 if ($batch['Playing'] == 1) : ?>
									<input type="button" name="frm_deactivate_batch_button" value="Desactivar" 
									class="boton" onClick="javascript: doDeactivateSubmit(<?php echo $batch['IdBatch']; ?>);">
								<?php

 else: ?>
									<input type="button" name="frm_activate_batch_button" value="Activar" 
									class="boton" onClick="javascript: doActivateSubmit(<?php echo $batch['IdBatch']; ?>);">
								<?php

 endif; ?>
							</td>
						</tr>
					</table>
					<?php

 if (isset($batch['serverFrames'])) :?>
					<div id='batch <?php echo $batch['IdBatch']; ?>' style='display: none;'>
						<table align=center class=tabla width=500 cellpadding="0" cellspacing="1">
							<tr>
								<td colspan="6" class="filaclara">
									<?php

 foreach ($batch['serverFrames'] as $idServer => $server) : ?>
									<br />
									<table align=center class=tabla width=98% cellpadding="0" cellspacing="1">
										<tr>
											<td colspan="6" class="filaoscura"><strong><?php echo _('Sub-Batch'); ?> #<?php

 echo $batch['IdBatch']; ?>.<?php

 echo $iSubBatch++; ?></strong></td>
										</tr>
										<tr>
											<td class="filaoscura"><?php echo _('File'); ?></td>
											<td class="filaoscura"><?php echo _('Size (Kb.)'); ?></td>
											<td class="filaoscura"><?php echo _('Published'); ?></td>
											<td class="filaoscura"><?php echo _('Unpublished'); ?></td>
											<td class="filaoscura"><?php echo _('Status'); ?></td>
											<td class="filaoscura"><?php echo _('View'); ?></td>
										</tr>
										<?php

 foreach ($server as $idServerFrame => $serverFrame) : ?>
										<tr>
											<td class="filaclara"><?php

 echo $serverFrame['FileName']; ?></td>
											<td class="filaclara"><?php

 echo $serverFrame['FileSize']; ?></td>
											<td class="filaclara"><?php

 echo date("d-m-Y H:i:s", $serverFrame['DateUp']); ?></td>
											<td class="filaclara"><?php

 echo ($serverFrame['DateDown'] > 0) ? date("d-m-Y H:i:s", $serverFrame['DateDown']) : '-'; ?></td>
											<td class="filaclara"><?php

 echo $arrayStates['ServerFrame'][$serverFrame['State']]; ?></td>
											<td class="filaclara">
											<?php

 if ($arrayStates['ServerFrame'][$serverFrame['State']] == 'Publicado') :?>
												<a target="_blank" href="<?php

 echo $activeServers[$idServer]['Url'] . $serverFrame['RemotePath']; ?>/<?php

 echo $serverFrame['FileName'];?>">Ver</a>
											<?php

 endif; ?>
											</td>
										</tr>
										<?php

 endforeach; ?>
									</table>
									<?php

 endforeach; ?>
									<br />
								</td>
							</tr>
						</table>
					</div>
					<?php

 endif; ?>
					<table align=center class=tabla width=500 cellpadding="0" cellspacing="1">
						<?php

 $iSubBatch = 1;

 if (isset($batch['progress'])) :?>
						<tr>
							<td colspan="6" class="filaclara">
								<br />
								<table align=center class=tabla width=98% cellpadding="0" cellspacing="1">
									<tr>
										<td colspan="8" class="filaoscura"><?php echo _('Progress'); ?></td>
									</tr>
									<tr>
										<td class="filaoscura"><?php echo _('Sub-Batch'); ?></td>
										<td class="filaoscura"><?php echo _('Files'); ?></td>
										<td class="filaoscura"><?php echo _('Total (Kb.)'); ?></td>
										<td class="filaoscura"><?php echo _('Avg. (Kb.)'); ?></td>
										<td colspan="2" class="filaoscura"><?php echo _('% (Kb.)'); ?></td>
										<td colspan="2" class="filaoscura"><?php echo _('% (#)'); ?></td>
									</tr>
									<?php

 foreach ($batch['progress'] as $subBatch => $progress) : 

 if ($subBatch != 'total') : ?>
										<tr>
											<td class="filaoscura"><strong><?php echo _('Sub-Batch #'); ?><?php

 echo $batch['IdBatch']; ?>.<?php

 echo $iSubBatch ++; ?></strong></td>
											<td class="filaclara"><?php

 echo $progress['totalBatch'] ?></td>
											<td class="filaclara"><?php

 echo $progress['totalBatchSize'] ?></td>
											<td class="filaclara"><?php

 echo $progress['avgBatchSize'] ?></td>
											<td class="filaclara">
												<?php

 echo $progress['percentBatchSizeCompleted'] ?>
											</td>
											<td class="filaclara">
												<?php

 echo showProgressBar($progress['percentBatchSizeCompleted']); ?>
											</td>
											<td class="filaclara">
												<?php

 echo $progress['percentBatchCompleted'] ?>
											</td>
											<td class="filaclara">
												<?php

 echo showProgressBar($progress['percentBatchCompleted']); ?>
											</td>
										</tr>
										<?php

 endif; 

 endforeach; ?>
									<tr>
										<td class="filaoscura"><strong><?php echo _('Total'); ?></strong></td>
										<td class="filaclara"><?php

 echo $batch['progress']['total']['totalBatch'] ?></td>
										<td class="filaclara"><?php

 echo $batch['progress']['total']['totalBatchSize'] ?></td>
										<td class="filaclara"><?php

 echo $batch['progress']['total']['avgBatchSize'] ?></td>
										<td class="filaclara">
											<?php

 echo $batch['progress']['total']['percentBatchSizeCompleted'] ?>
										</td>
										<td class="filaclara">
											<?php

 echo showProgressBar($batch['progress']['total']['percentBatchSizeCompleted']); ?>
										</td>
										<td class="filaclara">
											<?php

 echo $batch['progress']['total']['percentBatchCompleted'] ?>
										</td>
										<td class="filaclara">
											<?php

 echo showProgressBar($batch['progress']['total']['percentBatchCompleted']); ?>
										</td>
									</tr>
								</table>
								<br />
							</td>
						</tr>
						<?php

 endif; ?>
					</table>
				</td>
			</tr>
			<?php

 endforeach; 

 else : ?>
			<tr>
				<td align="center" class="filaclara"><br>
					<table align=center class=tabla width=500 cellpadding="0" cellspacing="1">
							<tr>
								<td colspan="6" class="filaoscura">
									<?php echo _('There are no Batches matching with the indicated criteria.'); ?>
								</td>
							</tr>
					</table>
				</td>
			</tr>
						<?php

 endif; ?>
					</table>
				</td>
			</tr>
		</table>
		
		</form>
		
		<?php


		
		$outPut = ob_get_contents();
		ob_end_clean();
		
		if ($printMode) {
			
			echo $outPut;
		} else {
			
			return $outPut;
		}
	
	}
	
	function batchShowErrorBox ($errorMsg) {
		
		$out = "
			<tr>
				<td align=\"center\" class=\"filaclara\"><br>
			
					<table align=center class=tabla width=500 cellpadding=\"0\" cellspacing=\"1\">
							<tr>
								<td class=\"cabeceratabla\">
									INFO
								</td>
							</tr>
							<tr>
								<td class=\"filaoscura\"><br /><strong>" . $errorMsg . "</strong><br /><br /></td>
							</tr>
					</table>
					
				</td>
			</tr>
		";
		
		return $out;
	}
	
	function showTimeLine ($arrayTimes) {
		
		$out = "";
		$maxEndTime = 0;
		$minStartTime = 9194377119;
		
		foreach ($arrayTimes as $times) {
			
			$startTime = $times[0];
			$endTime = $times[1];
			
			$maxEndTime = ($endTime > $maxEndTime) ? $endTime : $maxEndTime;
			
		}
		
		$minStartTime = mktime();
		$maxEndTime += 7200;
		$htmlLineWidth = 480;
		$realLineWidth = $maxEndTime - $minStartTime;
		
		foreach ($arrayTimes as $times) {

			$startTime = $times[0];
			$startTime = ($startTime <= $minStartTime) ? $minStartTime : $startTime;
			$endTime = $times[1];
			$lines = array();
			$lines[] = array (
				'width' => round((($startTime - $minStartTime) / $realLineWidth) * $htmlLineWidth),
				'color' => 'red'
			);
			
			if ($endTime > 0) {
				
				$lines[] = array (
					'width' => round((($endTime - $startTime) / $realLineWidth) * $htmlLineWidth),
					'color' => 'green'
				);
				$lines[] = array (
					'width' => round((($maxEndTime - $endTime) / $realLineWidth) * $htmlLineWidth),
					'color' => 'red'
				);
			} else {
				
				$lines[] = array (
					'width' => round((($maxEndTime - $startTime) / $realLineWidth) * $htmlLineWidth),
					'color' => 'green'
				);
			}
			
			$out .= "<table align='center' cellspacing='0' cellpadding='0'>\n";
			$out .= "<tr>\n";
			foreach ($lines as $line) {
				
				$out .= "<td valign='middle'><img style='width:" . $line['width'] . "px;height:3px;' " .
						"src='../../xmd/images/pix_" . $line['color'] . ".png'></td>\n";
			}
			$out .= "</tr>\n";
			$out .= "</table><br />\n";
		}
		
		return $out;
	}
	
	function showProgressBar($percent) {
		
		$lineWidth = 60;
		$finishedLineWidth = round(($percent / 100) * $lineWidth);
		$pendingLineWidth = $lineWidth - $finishedLineWidth;
		$out = "";
		$out .= "<table cellpadding='0' cellspacing='0'>\n";
		$out .= "<tr>";
		$out .= "<td><img style='width:" . $finishedLineWidth . "px;height:6px;' src='../../xmd/images/pix_green.png'></td>";
		$out .= "<td><img style='width:" . $pendingLineWidth . "px;height:6px;' src='../../xmd/images/pix_red.png'></td>";
		$out .= "</tr>";
		$out .= "</table>";
		
		return $out;
	}
?>
