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



if (!defined('XIMDEX_ROOT_PATH'))
	define('XIMDEX_ROOT_PATH', realpath(dirname(__FILE__) . "/../../"));

require_once(XIMDEX_ROOT_PATH . "/inc/helper/Messages.class.php");
require_once(XIMDEX_ROOT_PATH . "/inc/model/node.inc");

class BaseIOInferer {
	var $messages = NULL;

	/**
	 * Constructor
	 * @return unknown_type
	 */
	function BaseIOInferer() {
		$this->messages = new Messages();
	}

	/**
	 * Function which estimates the nodetype of a file depending on its parent
	 *
	 * @param string $type FILE|FOLDER
	 * @param int $idParent
	 * @param string $path
	 * @return array asociativo
	 */
	function infereType($type, $idParent, $path = NULL) {
		if (empty($type) || empty($idParent)) {
			return false;
		}
		switch ($type) {
			case 'FILE':
				$parent = new Node($idParent);
				return $this->_infereFileType($parent->GetNodeType(), $path);
			case 'FOLDER':
				$result = $this->_infereFolderType($idParent);
				if ($result && is_array($result)) {
					return array('NODETYPENAME' => $result['name']);
				}
		}
		return false;
	}

	/**
	 *
	 * @param $file
	 * @param $nodeTypeFilter
	 * @return unknown_type
	 */
	function infereFileType($file, $nodeTypeFilter = "common" ) {


		$filePath = isset($file) && isset($file['tmp_name']) ? $file['tmp_name'] : NULL;
		$fileName = isset($file) && isset($file['name']) ? $file['name'] : NULL;

		$fileMimeType = FsUtils::get_mime_type($filePath);
		$extension = FsUtils::get_extension($fileName);

		$query = "SELECT distinct nt.Name  FROM NodeAllowedContents nac ";
		$query .= "  INNER JOIN RelNodeTypeMimeType rntmt on nac.NodeType = rntmt.IdNodeType  ";
		$query .= "  INNER JOIN NodeTypes nt on nac.NodeType = nt.IdNodeType ";
		$query .= "  WHERE ( rntmt.mimeString like '%$fileMimeType%'  ";
		$query .= "  OR rntmt.extension like '%$extension%' ) ";
		if( $nodeTypeFilter != "common" ) {
			$query .= "  AND rntmt.filter = '$nodeTypeFilter' ";
		}
		//Maybe the query would be better with AND operator in where clause instead of OR. Sure for XSL.
		
		//For 
		$db = new DB();
		$db->Query($query);
		if ($db->numRows > 0) {
			$nodeType = $db->GetValue('Name');
			if( $nodeTypeFilter == "common" && $nodeType != "TextFile" &&  $nodeType != "ImageFile" ) {
					$nodeType = "BinaryFile";
			}

			//Added only for xsl files 
			if ($extension == "xsl" && $nodeType == "Template"){
				$nodeType = "XslTemplate";
			}
			return $nodeType;
		}
		
		XMD_Log::error(sprintf(_("Unsupported mime-type %s extension %s"), $fileMimeType, $extension));

		return '';
	}


	/**
	 *
	 * @param $parent_type
	 * @param $path
	 * @return unknown_type
	 */
	function _infereFileType($parent_type, $path) {

		$query = sprintf("SELECT nt.Name, rntmt.mimeString, rntmt.extension, rntmt.filter"
					. " FROM NodeAllowedContents nac"
					. " INNER JOIN RelNodeTypeMimeType rntmt on nac.NodeType = rntmt.IdNodeType"
					. " INNER JOIN NodeTypes nt on nac.NodeType = nt.IdNodeType"
					. " WHERE nac.IdNodeType = %s", $parent_type);
		$db = new DB();
		//error_log("DEBUG $query");
		$db->Query($query);
		$results = array();
		if (is_file($path)) {
			$fileMimeType = FsUtils::get_mime_type($path);
			$extension = strtolower(FsUtils::get_extension($path));
			if ($fileMimeType == 'application/x-empty') {
				XMD_Log::warning(_("Empty mimetype detected on _infereFileType"));
			}

			while (!$db->EOF) {
				if (strpos($db->GetValue('mimeString'), $fileMimeType) !== false) {
						// if we're coming from webdav, extension will not be received, that's why previous check is optional.
						$results[] = array('NODETYPENAME' => $db->getValue('Name'),
							'FILTER' => $db->GetValue('filter'));
				} else {
					if (!empty($extension)) {
						$extensions = explode(';', strtolower($db->GetValue('extension')));
						if (in_array(strtolower($extension), $extensions)) {
							$results[] = array('NODETYPENAME' => $db->getValue('Name'),
								'FILTER' => $db->GetValue('filter'));
						}
					}
				}

				$db->Next();
			}
		} else {
			while (!$db->EOF) {
				$results[] = array('NODETYPENAME' => $db->getValue('Name'),
								'FILTER' => $db->GetValue('filter'));
				$db->Next();
			}
		}

		$countResults = count($results);
		if ($countResults === 0) {
			$this->messages->add(sprintf(_("No nodetype allowed in %s"), $parent_type), MSG_TYPE_WARNING);
			return null;
		}


		if ($countResults == 1) {
			$nodetype = $results[0];
			return $nodetype;
		} else {
			$nodetype = $results[0];
			return $nodetype;
		}
	}

	/**
	 *
	 * @param $idParentNode
	 * @return unknown_type
	 */
	function _infereFolderType($idParentNode) {
		$parent = new Node($idParentNode);
		$nodeTypeName = $parent->nodeType->GetName();


		switch ($nodeTypeName) {

			case 'Projects':
				$newNodeTypeName ='Project';
				$friendlyName = _('Project');
				break;

			case 'Project':
				$newNodeTypeName ='Server';
				$friendlyName = _('Server');
				break;

			case 'Server':
				$newNodeTypeName ='Section';
				$friendlyName = _('Section');
				break;

			case 'Section':
				$newNodeTypeName ='Section';
				$friendlyName = _('Section');
				break;

			case 'ImagesRootFolder':
				$newNodeTypeName ='ImagesFolder';
				$friendlyName = _('Image folder');
				break;

			case 'ImagesFolder':
				$newNodeTypeName ='ImagesFolder';
				$friendlyName = _('Image folder');
				break;

			case 'XmlRootFolder':
//				$newNodeTypeName ='XmlFolder';
				$newNodeTypeName ='XmlContainer';
				$friendlyName = _('XML folder');
				break;

			case 'XmlFolder':
//				$newNodeTypeName ='XmlFolder';
				$newNodeTypeName ='XmlContainer';
				$friendlyName = _('XML folder');
				break;

			case 'ImportRootFolder':
				$newNodeTypeName ='ImportFolder';
				$friendlyName = _('Ximclude folder');
				break;

			case 'ImportFolder':
				$newNodeTypeName ='ImportFolder';
				$friendlyName = _('Ximclude folder');
				break;

			case 'CommonRootFolder':
				$newNodeTypeName ='CommonFolder';
				$friendlyName = _('Common folder');
				break;

			case 'CommonFolder':
				$newNodeTypeName ='CommonFolder';
				$friendlyName = _('Common folder');
				break;

			case 'CssRootFolder':
				$newNodeTypeName ='CssFolder';
				$friendlyName = _('CSS folder');
				break;

			case 'CssFolder':
				$newNodeTypeName ='CssFolder';
				$friendlyName = _('CSS folder');
				break;

			case 'TemplatesRootFolder':
				$newNodeTypeName ='TemplatesFolder';
				$friendlyName = _('Template folder');
				break;

			case 'TemplatesFolder':
				$newNodeTypeName ='TemplatesFolder';
				$friendlyName = _('Template folder');
				break;

			case 'TemplateViewFolder':
				$newNodeTypeName ='TemplateViewFolder';
				$friendlyName = _('Image template folder');
				break;

			case 'LinkManager':
				$newNodeTypeName ='LinkFolder';
				$friendlyName = _('Link category');
				break;

			case 'LinkFolder':
				$newNodeTypeName ='LinkFolder';
				$friendlyName = _('Link category');
				break;

			case 'XimletRootFolder':
//				$newNodeTypeName ='XimletFolder';
				$newNodeTypeName ='XimletContainer';
				$friendlyName = _('Ximlet folder');
				break;

			case 'XimletFolder':
//				$newNodeTypeName ='XimletFolder';
				$newNodeTypeName ='XimletContainer';
				$friendlyName = _('Ximlet folder');
				break;

			case 'XimNewsSection':
				$newNodeTypeName ='XimNewsNews';
				$friendlyName = _('XimNEWS new folder');
				break;

			case 'XimNewsImages':
				$newNodeTypeName ='XimNewsImages';
				$friendlyName = _('XimNEWS image root folder');
				break;

			case 'XimNewsImagesFolder':
				$newNodeTypeName ='XimNewsImagesFolder';
				$friendlyName = _('XimNEWS image batch');
				break;

			case 'ModuleInfoContainer':
				$newNodeTypeName = 'ximPortaAllowedContent';
				$friendlyName = _('Ximporta document definition node');
				break;

			default:
				// Log to user.
				return null;
		}

		$data['name'] = $newNodeTypeName;
		$data['friendlyName'] = $friendlyName;

		return $data;
	}
}
?>
