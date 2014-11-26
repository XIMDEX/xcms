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
ModulesManager::file('/inc/nodetypes/foldernode.php');

/**
*   @brief Handles folders of images for purpose of its use on the ximNEWS browser.
*/

class XimNewsImagesFolder extends FolderNode {

	/**
	*  Creates the folders directory in the data/nodes.
	*  @return int|null
	*/

	function RenderizeNode() {

		parent::RenderizeNode();

		// create thumbnails folder and file

		$this->setThumbnailsPath();

		if (!mkdir($this->thumbnailsPath, 0755, true)) {
			XMD_Log::error("Creating thumbnail folder");
			return NULL;
		}

		if (!FsUtils::file_put_contents($this->thumbnailsPath . 'thumbnails.xml', \App::getValue( 'EncodingTag') .
			"\n<thumbnails>\n</thumbnails>")) {

			XMD_Log::error("Creating thumbnail file");
			return NULL;
		}

		return $this->parent->get('IdNode');
	}

	/**
	*  Renames the folder and updates the thumbnail.xml.
	*  @param string name
	*  @return bool
	*/

	function RenameNode($name) {
		//this line produced an error. It doesn't return de correct path. It was a PHP Fatal error.
		//$newLotePath = \App::getValue( 'AppRoot') . \App::getValue( 'NodeRoot') .$this->parent->GetPublishPath()."/$name/";

		$newLotePath = \App::getValue( 'AppRoot') . \App::getValue( 'NodeRoot') .dirname($this->GetPathList())."/$name/";
		$this->setThumbnailsPath();
		$thumbnails = $this->thumbnailsPath . 'thumbnails.xml';

		$content = FsUtils::file_get_contents($thumbnails);

		$domDoc = new DOMDocument();
		$domDoc->validateOnParse = true;
		$domDoc->preserveWhiteSpace = false;
		$domDoc->loadXML(XmlBase::recodeSrc($content, XML::UTF8));

		$nodeList = $domDoc->getElementsByTagName('thumbnail');

		if (!($nodeList->length > 0)) {
			return false;
		}

		foreach ($nodeList as $thumbnailNode) {
			$title = $thumbnailNode->childNodes->item(0)->nodeValue;
			$thumbnailNode->childNodes->item(1)->nodeValue = $newLotePath . $title;
		}

		$resultContent = $domDoc->saveXML();
		FsUtils::file_put_contents($thumbnails, $resultContent);

        $this->updatePath();
		return true;
	}

	/**
	*	Get the image from ximnewsimagesfolder at position given
	*	@param int position
	*	@return array|null
	*/

	function GetImageFromLote($position = 0) {

		$this->setThumbnailsPath();
		$thumbnails = $this->thumbnailsPath . 'thumbnails.xml';

		if (!is_file($thumbnails)) {
			XMD_log::error("No such file $thumbnails");
			return NULL;
		}

		$content = FsUtils::file_get_contents($thumbnails);

		$domDoc = new DOMDocument();
		$domDoc->validateOnParse = true;
		$domDoc->preserveWhiteSpace = false;

		if (!$domDoc->loadXML(XmlBase::recodeSrc($content, XML::UTF8))) return NULL;


		$thumbNailNode = $domDoc->getElementsByTagName('thumbnail')->item($position);

		if (is_null($thumbNailNode)) return NULL;

		return array('id' => $thumbNailNode->getAttribute('nodeid'),
			'name' => $thumbNailNode->getElementsByTagName('title')->item(0)->nodeValue);
	}

	/**
		@deprecated
	   Devuelve html con los thumbnails del lote de im�genes para el browser
	 */

	function getThumbnails($flag, $scale) {

		$this->setThumbnailsPath();
		$thumbnails = $this->thumbnailsPath . 'thumbnails.xml';

		if (!is_file($thumbnails)) {
			XMD_log::error("No such file $thumbnails");
			return NULL;
		}

		$content = FsUtils::file_get_contents($thumbnails);

		$domDoc = new DOMDocument();
		$domDoc->validateOnParse = true;
		$domDoc->preserveWhiteSpace = false;

		if (!$domDoc->loadXML(XmlBase::recodeSrc($content, XML::UTF8))) return NULL;

		$thumbnailNodes = $domDoc->getElementsByTagName('thumbnail');

		if (!($thumbnailNodes->length > 0)) return NULL;

		$xpath = new DOMXPath($domDoc);
		$t = 0;

		foreach ($thumbnailNodes as $thumbnailNode) {

			$title = $thumbnailNode->getElementsByTagName('title')->item(0)->nodeValue;

			foreach ($thumbnailNode->childNodes as $childNode) {

				if ($childNode->getAttribute('scale') == $scale) {

					$path_tn = $childNode->nodeValue;
					$width_tn = $childNode->getAttribute('width');
					$height_tn = $childNode->getAttribute('height');
				}
			}

			// todo: se mantiene esto en espera de arreglar la accion

			switch($flag) {
				case "1":
					$resultado[$t] = $imgID . "####";
					$resultado[$t] .='<img name="' .$title. '" class="thumb" border="0" src="' . $path_tn . '" width="' .
					$width_tn . '" height="' . $height_tn . '" >';
					break;

				case "2":
					$resultado[$t] ='<img name="' .$title. '" class="thumb" border="0" src="' . $path_tn . '" width="' .
					$width_tn . '" height="' . $height_tn . '" >';
					break;

				case "0":
					$resultado[$t] = "";
					$resultado[$t] .='<img name="' .$title. '" onclick="selectI(this);" class="thumb" border="0" src="' .
					$path_tn . '" width="' . $width_tn . '" height="' . $height_tn . '"  >';
					$resultado[$t] .='<input type="hidden" name="nodeid_img'.$t.'" value="'.$imgID.'" class="ecajag">';
					break;
			}

			$t++;
		}

		return $resultado;
	}

	/**
	*	Set the directory which contains the thumbnails
	*/

	private function setThumbnailsPath() {

		if (empty($this->thumbnailsPath)) {

			$this->thumbnailsPath = \App::getValue( 'AppRoot') . \App::getValue( 'NodeRoot') . $this->GetPathList() .
				'/thumbnails/';
		}
	}

}
?>
