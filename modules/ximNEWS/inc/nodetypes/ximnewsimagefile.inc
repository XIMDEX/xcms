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

ModulesManager::file('/inc/nodetypes/filenode.php');
ModulesManager::file('/inc/model/XimNewsColector.php', 'ximNEWS');

/**
*   @brief Handles images for purpose of its use on the ximNEWS browser.
*
*/

class XimNewsImageFile extends FileNode {

	var $thumbnailsPath;

	/**
	*  Creates the XimNewsImage file and its thumbnails
	*  @param string $name
	*  @param int $idParent
	*  @param int $nodeTypeID
	*  @param int $stateID
	*  @param int $sourcePath
	*  @return unknown
	*/

	function CreateNode($name, $idParent, $nodeTypeID, $stateID = NULL, $sourcePath) {

		parent::CreateNode($name, $idParent, $nodeTypeID, $stateID = NULL, $sourcePath);

		$this->createThumbnail($this->parent->get('IdNode'), $name, $idParent, $sourcePath);
	}

	/**
	*	Add image to lote and create the thumbnails
	*	@param int idImage
	*	@param string imageName
	*	@param int idLote
	*	@param string tmpImagePath
	*	@return bool
	*/

	private function createThumbnail($idImage, $imageName, $idLote, $tmpImagePath) {

		$this->setThumbnailsPath();
		$thumbnails = $this->thumbnailsPath . 'thumbnails.xml';
		$content = FsUtils::file_get_contents($thumbnails);

		$domDoc = new DOMDocument();
		$domDoc->validateOnParse = true;
		$domDoc->preserveWhiteSpace = false;

		if (!$domDoc->loadXML(XmlBase::recodeSrc($content, XML::UTF8))) return false;

		$scale = array("0.1", "0.25", "0.5", "0.75");

		$imageSize = getimagesize($tmpImagePath);
		$scaleData = $this->createImageFiles($tmpImagePath, $imageName, $scale, $imageSize);

		// xml thumbnail creation

		$thumbnailNode = $domDoc->createElement('thumbnail');
		$thumbnailNode->setAttribute('nodeid', $idImage);

		$titleNode = $domDoc->createElement('title');
		$titleNode->nodeValue = $imageName;
		$thumbnailNode->appendChild($titleNode);

		$pathNode = $domDoc->createElement('path');
		$pathNode->nodeValue = substr($this->thumbnailsPath, 0, -11) . $imageName;
		$pathNode->setAttribute('width', $imageSize[0]);
		$pathNode->setAttribute('height', $imageSize[1]);
		$thumbnailNode->appendChild($pathNode);

		foreach ($scaleData as $sc) {

			$pathNode = $domDoc->createElement('path_tn');
			$pathNode->nodeValue = $sc['name'];
			$pathNode->setAttribute('width', $sc['width']);
			$pathNode->setAttribute('height', $sc['height']);
			$pathNode->setAttribute('scale', $sc['scale']);

			$thumbnailNode->appendChild($pathNode);
		}

		// update thumbnails XML

		$thumbnailsNode = $domDoc->getElementsByTagName('thumbnails')->item(0);
		$thumbnailsNode->appendChild($thumbnailNode);

		$resultContent = $domDoc->saveXML();
        FsUtils::file_put_contents($this->thumbnailsPath . 'thumbnails.xml', $resultContent);

		return true;
	}

	/**
	*  Wrapper for DeleteNode.
	*  @return bool
	*/

	function delete() {
		return $this->DeleteNode();
	}

	/**
	*  Delete the thumbnails files and update the thumbnails data in the XML.
	*  @return bool
	*/

	function DeleteNode() {

		$idImage = $this->parent->get('IdNode');
		$idLote = $this->parent->get('IdParent');

		$loteNode = new Node($idLote);
		$images_path = $loteNode->GetPublishedPath();

		$this->setThumbnailsPath();
		$thumbnails = $this->thumbnailsPath . 'thumbnails.xml';

		if (!is_file($thumbnails)) {
			XMD_log::error("No such file $thumbnails");
			return false;
		}

		$content = FsUtils::file_get_contents($thumbnails);

		$domDoc = new DOMDocument();
		$domDoc->validateOnParse = true;
		$domDoc->preserveWhiteSpace = false;

		if (!$domDoc->loadXML(XmlBase::recodeSrc($content, XML::UTF8))) return false;

		$xpath = new DOMXPath($domDoc);
		$nodeList = $xpath->query("//thumbnail[@nodeid = '$idImage']");

		if (!($nodeList->length > 0)) {
			XMD_Log::error("Thumbnail for $idImage not found");
			return false;
		}

		$nodeListPaths  = $nodeList->item(0)->getElementsByTagName('path_tn');

		// delete thumbnails files

		foreach ($nodeListPaths as $pathNode) {

			$fileThumb = $this->thumbnailsPath . $pathNode->nodeValue;

			if(!FsUtils::delete($fileThumb)) XMD_Log::info("Error eliminando imagen $fileThumb");
		}

		// update XML

		$thumbnailsNode = $domDoc->getElementsByTagName('thumbnails')->item(0);
		$thumbnailsNode->removeChild($nodeList->item(0));

		$resultContent = $domDoc->saveXML();
        FsUtils::file_put_contents($this->thumbnailsPath . 'thumbnails.xml', $resultContent);

		return true;
	}

	/**
	*	Rename the thumbnails files and update thumbnail.xml.
	*	@param  string name
	*	@return bool
	*/

	function RenameNode($name) {

		$oldName = $this->parent->get('Name');
		$idLote = $this->parent->get('IdParent');

		$this->setThumbnailsPath();
		$content = FsUtils::file_get_contents($this->thumbnailsPath . 'thumbnails.xml');

		$domDoc = new DOMDocument();
		$domDoc->validateOnParse = true;
		$domDoc->preserveWhiteSpace = false;

		if (!$domDoc->loadXML(XmlBase::recodeSrc($content, XML::UTF8))) return false;

		// get the node correspond to name

		$xpath = new DOMXPath($domDoc);
		$nodeList = $xpath->query('//thumbnail[@nodeid = "' . $this->parent->get('IdNode') . '"]');

		if (!($nodeList->length > 0)) {
			return false;
		}

		$childNodes = $nodeList->item(0)->childNodes;

		$newTitle = substr($name, 0, strlen($name) - 4);
		$oldTitle = substr($childNodes->item(0)->nodeValue, 0, strlen($childNodes->item(0)->nodeValue) - 4);

		// update xml

		foreach ($childNodes as $childNode) {

			$oldValue = $childNode->nodeValue;
			$newValue = preg_replace("/$oldTitle/", $newTitle, $oldValue);
			$childNode->nodeValue = $newValue;

			if ($childNode->nodeName == 'path_tn') {

				// rename the thumbnails files

				if (!rename($this->thumbnailsPath . $oldValue, $this->thumbnailsPath . $newValue)) {
					XMD_Log::error("Error renaming thumbnail $oldThumbnail");
				}
			}
		}

		$resultContent = $domDoc->saveXML();
        FsUtils::file_put_contents($this->thumbnailsPath . 'thumbnails.xml', $resultContent);

        $this->updatePath();
		return true;
	}

	/**
	 *	Create the thumbnail files.
	 *	@param string imagePath
	 *	@param string imageTitle
	 *	@param string escale
	 *	@param array datos
	 *	@return array
	 */

	private function createImageFiles($imagePath, $imageTitle, $escala, $datos) {
		$scale = array();

		$this->setThumbnailsPath();

		foreach ($escala as $valor) {

			switch($datos[2]) {
				case 1:
					$temp_img = imagecreatefromgif($imagePath);//GIF
					break;
				case 2:
					$temp_img = imagecreatefromjpeg($imagePath);//JPEG
					break;
				case 3:
					$temp_img = imagecreatefrompng($imagePath);//PNG
					break;
			}

			$thumbnailName = 'tn_' . $valor . '_'. substr($imageTitle, 0, strlen($imageTitle) - 3) . 'jpg';
			$altura = round($datos[1] * $valor);
			$anchura = round($datos[0] * $valor);
			$thumb = imagecreatetruecolor($anchura,$altura);

			imagecopyresampled ($thumb, $temp_img, 0, 0, 0, 0, $anchura, $altura, $datos[0], $datos[1]);
			imagejpeg($thumb, $this->thumbnailsPath . $thumbnailName);
			imagedestroy($temp_img);

			$scale[] = array('scale' => $valor, 'width' => $anchura, 'height' => $altura, 'name' => $thumbnailName);
		}

		return $scale;
	}

	/**
	*	Set the directory which contains the thumbnails
	*	@return unknown
	*/

	private function setThumbnailsPath() {

		if (empty($this->thumbnailsPath)) {

			$idLote = $this->parent->get('IdParent');

			$node = new Node($idLote);
			$this->thumbnailsPath = Config::GetValue('AppRoot') . Config::GetValue('NodeRoot') . $node->GetPathList() .
				'/thumbnails/';
		}
	}
}
?>
