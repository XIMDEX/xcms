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
 * @version $Revision: $                                                      *  
 *                                                                            *
 *                                                                            *
 ******************************************************************************/











if (!defined('XIMDEX_ROOT_PATH')) {
	define ('XIMDEX_ROOT_PATH', realpath(dirname(__FILE__) . '/../../'));
}

require_once(XIMDEX_ROOT_PATH . "/inc/nodetypes/filenode.inc");
require_once(XIMDEX_ROOT_PATH . "/inc/mvc/App.class.php");
require_once(XIMDEX_ROOT_PATH . "/inc/parsers/pvd2rng/PVD2RNG.class.php");

class VisualTemplateNode extends FileNode {

	const RNG_SUFFIX = 'rng-';

	function CreateNode($name = null, $parentID = null, $nodeTypeID = null, $stateID = null, $sourcePath = null, $templateType = null) {

		$content = FsUtils::file_get_contents($sourcePath);

		$data = new DataFactory($this->parent->get('IdNode'));
		$this->_saveUUID($content);
		$data->SetContent($content);

		// first we set the template_type property

		$storableTypes = array('news_template', 'bulletin_template');
		if (in_array($templateType, $storableTypes)) {
			$this->parent->setProperty('TemplateType', $templateType);
		}
		// Adds rng node

		$pvdt = new PVD2RNG();
		if (!$pvdt->loadPVD($this->parent->get('IdNode'))) {
			XMD_Log::error("(1) Pvd " . $this->parent->get('IdNode') . " not compatible RNG");
		} else {
			if ($pvdt->transform()) {
				$content = htmlspecialchars_decode($pvdt->getRNG()->saveXML());
			} else {
				XMD_Log::error("(2) Pvd " . $this->parent->get('IdNode') . " not compatible RNG");
			}
		}

		$rngName = VisualTemplateNode::RNG_SUFFIX . $name;
		$rngSourcePath = XIMDEX_ROOT_PATH . Config::GetValue('TempRoot') . '/' . $rngName;
		$parentNode = new Node($this->parent->get('IdParent'));
		$idRng = $parentNode->GetChildByName($rngName);
		if ($idRng > 0) {
			$rng = new Node($idRng);
			$rng->SetContent($content);
			if (in_array($templateType, $storableTypes)) {
				$rng->setProperty('TemplateType', $templateType);
			}
		} else {
			FsUtils::file_put_contents($rngSourcePath, $content);

			$data = array(
					'NODETYPENAME' => 'RngVisualTemplate',
					'NAME' => $rngName,
					'PARENTID' => $parentID,
					'CHILDRENS' => array (
						array ('NODETYPENAME' => 'PATH', 'SRC' => $rngSourcePath)
						)
					);

			$baseIO = new baseIO();
			$result = $baseIO->build($data);
			if ($result > 0) {
				$node = new Node($result);
				$node->setProperty('TemplateType', $templateType);
			}
		}

		$this->updatePath();
	}


	function _estimateUUID(&$content) {
		if (empty($content)) {
			$content = Utils::generateRandomChars(30, true, true, true);
			return md5($content);
		}

		if (preg_match('/UUID=\"([0-9a-z]+)\"/', $content, $matches) > 0) {
			return $matches[1];
		}

		$UUID = md5($content);
		$content = str_replace('<editviews', sprintf('<editviews UUID="%s"', $UUID), $content);
		return $UUID;
	}

	function SetContent($content, $commitNode = NULL) {
		$this->_saveUUID($content);
		parent::SetContent($content, $commitNode);

		// Adds rng node

		$pvdt = new PVD2RNG();
		$pvdt->loadPVD($this->parent->get('IdNode'));

		if ($pvdt->transform()) {
			$content = htmlspecialchars_decode($pvdt->getRNG()->saveXML());
		} else {
			XMD_Log::error("Pvd $templateID not compatible RNG");
		}

		$rngName = VisualTemplateNode::RNG_SUFFIX . $this->parent->get('Name');
		$rngSourcePath = XIMDEX_ROOT_PATH . Config::GetValue('TempRoot') . '/' . $rngName;
		$parentNode = new Node($this->parent->get('IdParent'));
		$idRng = $parentNode->GetChildByName($rngName);
		if ($idRng > 0) {
			$rng = new Node($idRng);
			$rng->SetContent($content);
		} else {
			FsUtils::file_put_contents($rngSourcePath, $content);

			$data = array(
					'NODETYPENAME' => 'RngVisualTemplate',
					'NAME' => $rngName,
					'PARENTID' => $this->parent->get('IdParent'),
					'CHILDRENS' => array (
						array ('NODETYPENAME' => 'PATH', 'SRC' => $rngSourcePath)
						)
					);

			$baseIO = new baseIO();
			$baseIO->build($data);
		}

	}

	function _saveUUID(&$content){
		$UUID = $this->_estimateUUID($content);
		if (empty($UUID)) {
			return false;
		}
		return $this->parent->setProperty('UUID', $UUID);
	}

	function getDefaultContent() {
		$content = $this->GetContent();
		$parts = explode('##########', $content);
		if (count($parts) > 2) {
			XMD_Log::error("La pvd {$this->nodeID} tiene más de una vez la cadena ########## lo que hace que no se pueda obtener el contenido por defecto correctamente");
			return false;
		}
		if (!(count($parts) == 2)) {
			XMD_Log::error("La pvd {$this->nodeID} parece que no tiene contenido por defecto");
			return false;
		}
		return $parts[1];
	}
}
?>
