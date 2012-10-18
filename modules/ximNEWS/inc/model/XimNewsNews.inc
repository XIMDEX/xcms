<?php

/******************************************************************************
 *  Ximdex a Semantic Content Management System (CMS)    							*
 *  Copyright (C) 2011  Open Ximdex Evolution SL <dev@ximdex.org>	      *
 *                                                                            *
 *  This program is free software: you can redistribute it and/or modify      *
 *  it under the terms of the GNU Affero General Public License as published  *
 *  by the Free Software Foundation, either version 3 of the License, or      *
 *  (at your option) any later version.                                       *
 *                                                                            *
 *  This program is distributed in the hope that it will be useful,           *
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of            *
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the             *
 *  GNU Affero General Public License for more details.                       *
 *                                                                            *
 * See the Affero GNU General Public License for more details.                *
 * You should have received a copy of the Affero GNU General Public License   *
 * version 3 along with Ximdex (see LICENSE).                                 *
 * If not, see <http://gnu.org/licenses/agpl-3.0.html>.                       *
 *                                                                            *
 * @brief Handles News.                                                       *  
 *                                                                            *
 *                                                                            *
 ******************************************************************************/

ModulesManager::file('/inc/model/orm/XimNewsNews_ORM.class.php', 'ximNEWS');
ModulesManager::file('/inc/model/RelNewsColector.php', 'ximNEWS');
ModulesManager::file('/inc/model/XimNewsCache.class.php', 'ximNEWS');


class XimNewsNew extends XimNewsNews_ORM {

	/**
	*  A wrapper for InsertNews
	*/

	function add() {
		$data['fecha'] = $this->get('Fecha');
		$data['timestamp'] = $this->get('TimeStamp');
		$data['name'] = $this->get('Name');
		$data['titular'] = $this->get('Titular');
		$data['IdSection'] = $this->get('IdSection');
		
		return $this->InsertNews($this->get('IdNew'), $data);
	}

	/**
	*  Adds a row to XimNewsNews table.
	*  @param int newsID
	*  @param array data
	*  @return int|false
	*/

	function InsertNews($newsID,$data) {
		$this->set('IdNew', $newsID);
		$this->set('Fecha', $data['fecha']);
		$this->set('Name', $data['name']);
		$this->set('Titular', strlen($data['titular']) > 0 ? $data['titular'] : ' ');
		$this->set('TimeStamp', $data['timestamp']);
		$this->set('IdSection', $data['IdSection']);
		

		$insertedId = parent::add();
		
		return $insertedId ? $insertedId : false;
	}
	
	/**
	*  Updates the data of the news after its publication.
	*  @param int colectorID
	*  @param int newsVersion
	*/

	function updateDataAfterPublish($colectorID, $newsVersion) {
		$nodeID = $this->get('IdNew');

		$relNewsColector = new RelNewsColector();
		$idRel = $relNewsColector->hasNews($colectorID, $nodeID);

		if ($idRel > 0) {
			$dataFactory = new datafactory($nodeID);
			$newsVersionID = $dataFactory->getVersionId($newsVersion, 0);

			$relNewsColector = new RelNewsColector($idRel);
			$idCache = $relNewsColector->get('IdCache');	
			$relNewsColector->set('Version', $newsVersion);
			$relNewsColector->set('SubVersion', 0);

			if (!$relNewsColector->update()) {
				XMD_Log::info(_('Updating version'));
			}

			if($idCache > 0){
				$ximNewsCache = new XimNewsCache($idCache);
				$ximNewsCache->set('IdVersion',$newsVersionID);

				if (!$ximNewsCache->update()) {
					XMD_Log::info(_('Updating cache version'));
				}
			}
		}

	}

}
?>
