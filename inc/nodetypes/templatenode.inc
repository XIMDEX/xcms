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

ModulesManager::file('/inc/nodetypes/filenode.inc');
ModulesManager::file('/inc/fsutils/FsUtils.class.php');
ModulesManager::component('/xslt/functions.php', 'dexT');
ModulesManager::file('/inc/io/BaseIO.class.php');
ModulesManager::file('/inc/xml/XSLT.class.php');


class templatenode extends FileNode {
	function CreateNode($name = null, $parentID = null, $nodeTypeID = null, $stateID = null, $sourcePath = null) {

		// Checks if ptd is compatible with XSLT

		if (!$this->checkXslCompliance($sourcePath, $name)) {
			XMD_Log::error("Ptd $name not compatible with XSLT");
			return NULL;
		}

		$id = parent::CreateNode($name, $parentID, $nodeTypeID, $stateID, $sourcePath);

		if ($id > 0) {
			// Creates XSLT node
			$xsltName = substr($name, 0, strpos($name, '.')) . '.xsl';
			$content = $this->getXslContent(
					FsUtils::file_get_contents($sourcePath),
					$xsltName,
					$this->parent->getSection());


			$parentNode = new Node($this->parent->get('IdParent'));

			$xsltId = $parentNode->GetChildByName($xsltName);
			if (!empty($xsltId)) {
				$node = new Node($xsltId);
				$node->SetContent($content);
			} else {

				$file = XIMDEX_ROOT_PATH . '/data/tmp/' . FsUtils::getUniqueFile(XIMDEX_ROOT_PATH . '/data/tmp/');
				FsUtils::file_put_contents($file, $content);

				$data = array(
						'NODETYPENAME' => 'XslTemplate',
						'NAME' => $xsltName,
						'PARENTID' => $parentID,
						'CHILDRENS' => array (
							array ('NODETYPENAME' => 'PATH', 'SRC' => $file)
							)
						);

				$baseIO = new baseIO();
				$baseIO->build($data);
			}
		}

		$this->updatePath();
		return $id;
	}

	/*
	 *	Translate ptd content to xslt
	 *
	 *	@param string $ptdContent Ptd content
	 *	@param string $fileName xslt file name
	 *	@return string/null
	 */

	private function getXslContent($ptdContent, $fileName) {

		if (!($ptdContent || $fileName)) {
			XMD_log::error('Any param void');
			return NULL;
		}

		$result = preTransformation($ptdContent, $fileName, $this->parent->get('IdParent'));

		$ptdContent = $result[0];
		$varsList = $result[1];
		$ptdContent = $this->_addUidAttributes($ptdContent, $fileName);

		$tmpPath = XIMDEX_ROOT_PATH . Config::GetValue('TempRoot') . '/tmpTemplateXml.xml';
		FSUtils::file_put_contents($tmpPath, $ptdContent);

		// build xslt content

		$xsltHandler = new XSLT();
		$xsltHandler->setXML($tmpPath);
		$xsltHandler->setXSL(XIMDEX_ROOT_PATH.ModulesManager::path('dexT') ."/xslt/dext_main.xsl");

		$params = array('ximdex_root_path' => XIMDEX_ROOT_PATH,
						'vars_list' => $varsList,
						'ptds_path' => $this->getXslDir());

		foreach ($params as $param => $value) {
			$xsltHandler->setParameter(array($param => $value));
		}

		return $xsltHandler->process();
	}

	function SetContent($content, $commitNode = NULL) {
		$matches = array();
		preg_match('/\s*(\<\s*ximptd\s+nodevid\=[\'\"](\d+)[\'\"]\>)/', $content, $matches);

		$dataFactory = new DataFactory($this->nodeID);
		$lastVersion = $dataFactory->AddVersion();

		// Si tenemos la cabecera que buscamos

		if (count($matches) === 3) {
			$ptdVersion = $matches[2];

			if ($ptdVersion != $lastVersion) {
				$content = preg_replace('/(\s*\<\s*ximptd\s+nodevid\=[\'\"]\d+[\'\"]\>)(.*)/',
					"<ximptd nodevid='{$lastVersion}'>$2",
					$content);
			}
		} else { // si no la incluimos
			$content = "<ximptd nodevid='{$lastVersion}'> \n" .
						$content .
						"\n</ximptd>";
		}

		parent::SetContent($content, $commitNode);
		$content = preg_replace('/\<\?\s*xml[^?]+\?\>/', '', $content);
		$content = preg_replace('/\<\s*[\/]?\s*ximptd[^>]*\>/', '', $content);
		$this->_cleanCache();

		// Updating XSLT file

		$node = new Node($this->nodeID);
		$name = $node->get('Name');

		$parentId = $node->get('IdParent');

		$xsltName = substr($name, 0, strpos($name, '.')) . '.xsl';

		$parentNode = new Node($parentId);
		$xsltId = $parentNode->GetChildByName($xsltName);

		$xml = new XML();
		$content = setNameSpace($content, $xsltName);

		$domDocument = $xml->getObject($content);

		$xslContent = $this->getXslContent($content, $xsltName);

		if ($xsltId > 0) {
			$xsltNode = new Node($xsltId);

			if (is_null($xslContent)) {
				XMD_Log::error("Error updating xsl template for ptd $name");
			} else {
				$xsltNode->setContent($xslContent);
			}
		} else {
			$file = XIMDEX_ROOT_PATH . '/data/tmp/' . FsUtils::getUniqueFile(XIMDEX_ROOT_PATH . '/data/tmp/');
			FsUtils::file_put_contents($file, $xslContent);

			$data = array(
					'NODETYPENAME' => 'XslTemplate',
					'NAME' => $xsltName,
					'PARENTID' => $parentId,
					'CHILDRENS' => array (
						array ('NODETYPENAME' => 'PATH', 'SRC' => $file)
						)
					);
			$baseIO = new baseIO();
			$baseIO->build($data);
		}

	}

	function DeleteNode() {
		if (parent::DeleteNode()) {
			$this->_cleanCache();
		}
	}

	function _cleanCache() {
		$cacheFolder = XIMDEX_ROOT_PATH . '/data/cache/pipelines/';
		// Vaciamos la cache
		$tablesToTruncate = array('PipeCaches', 'PipePropertyValues');

		$db = new DB();
		foreach ($tablesToTruncate as $table) {
			$query = sprintf('TRUNCATE %s', $table);
			if (!$db->execute($query)) {
				XMD_Log::error("Error al vaciar la tabla $table no se ha vaciado correctamente la cache");
			}
		}

		if ($handler = opendir($cacheFolder)) {
		    while (false !== ($file = readdir($handler))) {
		    	if ($file == '.' || $file == '..') {
		    		continue;
		    	}
		        if (!FsUtils::delete($cacheFolder . $file)) {
		        	XMD_Log::error("No se ha podido eliminar el archivo de cache $file");
		        }
		    }
		    closedir($handler);
		}
	}

	/**
	 * Checks XSLT compatibility
	 *
	 * @param string $tmpPath
	 * @param string $fileName
	 * @return true / false
	 */

	function checkXslCompliance($tmpPath, $fileName) {

		$content = FsUtils::file_get_contents($tmpPath);

		$result = preTransformation($content, $fileName);
		$content = $result[0];

		$doc = new DOMDocument;
		$result = $doc->loadXML($content);

		if (!$result) {
			$this->parent->messages->add(_('XML incorrecto, la xsl equivalente no se ha creado'), MSG_TYPE_NOTICE);
			XMD_Log::info("Incorrect XML in Ptd $fileName");
			return false;
		}

		$nodeList = $doc->getElementsByTagName('*');

		$rootNode = $nodeList->item(0)->nodeName;

		// Concat namespace

		$ptdName = 'dext:' . substr($fileName, 0, strpos($fileName, '.'));

		// Checks if root tag is equal to ptd name

		if ($rootNode != $ptdName) {
			XMD_Log::error("Root tag not equal to name $ptdName");
			$this->parent->messages->add(_('El nombre del archivo no coincide con el de su etiqueta inicial'),
				MSG_TYPE_ERROR);
			return false;
		}

		return true;
	}

	/**
	 *	Inserts uid attributtes (kupu)
	 *
	 *	@param string $content
	 *	@param string $fileName
	 *	@return string $content
	 */

	function _addUidAttributes($content, $fileName) {
		$content = '<?xml version="1.0" ?>' . $content;
		$doc = new DOMDocument;
		$result = $doc->loadXML($content);
		$xpath = new DOMXPath($doc);

		// docxap template must have uid attribute in tag body

		if ($fileName == 'docxap.xsl') {
			$nodeList = $xpath->query('//html:body');
			if (!($nodeList->length > 0)) {
				// Searchs tags body in comments
				$nodeList = $xpath->query('//comment()');
				if ($nodeList->length > 0) {
					foreach ($nodeList as $entry) {
						$entry->nodeValue = str_replace('<html:body', '<html:body uid="{@uid}"', $entry->nodeValue);
					}
				}
				$content = $doc->saveXML();
				return $content;
			}
		} else {

			// If template have apply-templates uid attribute must be in parent html tag

			$nodeList = $xpath->query('//html:*[dext:applytemplates|dext:var_body]');

			if ($nodeList && !($nodeList->length > 0)) {

				// uid attribute in all html tags with a parent of dext namespace

				$nodeList = $xpath->query('//html:*[parent::dext:*]');

				if ($nodeList && !($nodeList->length > 0)) {

					// Template with a unique html uid attribute must be in this tag

					$nodeList = $xpath->query('//*[starts-with(name(.), "html")]');

					if ($nodeList && !($nodeList->length == 1)) {

						// Template with a unique %%%_BODY_%%%, uid attribute must be in a span tag

						$nodeList = $xpath->query('//*[starts-with(name(.), "dext")]');

						// root_node + dext:var_body = 2 (total nodes)

						if ($nodeList &&  $nodeList->length == 2) {

							$spanNode = $doc->createElement("html:span");
							$spanNode->setAttribute('uid', '{@uid}');

							$nodeList->item(0)->insertBefore($spanNode, $nodeList->item(1));
							$content = $doc->saveXML();

							return $content;
						}

					}

				}
			}
		}

		$notAllowedTags = array(
			'html:html',
			'html:head',
			'html:title',
			'html:meta',
			'html:link',
			'html:script',
			'html:noscript',
		);

		foreach ($nodeList as $entry) {
			if (!in_array(strtolower($entry->nodeName), $notAllowedTags)) {
				$entry->setAttribute('uid', '{@uid}');
			}
		}

		$content = $doc->saveXML();
		return $content;
	}
	/**
	*  Return the directory that contain xslt templates
	*/

	function getXslDir() {
		$node = new Node($this->nodeID);
		$sectionId = $node->GetSection();

		$projectId = $node->GetProject();
		$project = new Node($node->GetProject());

		if (!($sectionId > 0)) {
			$relativePath = $project->get('Name');
		} else {
			$section = new Node($sectionId);
			$relativePath = $section->GetRelativePath($projectId);
		}

		$xslDir = Config::GetValue('UrlRoot') . Config::GetValue('NodeRoot') . '/' . $relativePath . '/' .
			Config::getValue("GeneratorTemplateDir") . '/';

		return $xslDir;
	}

}
?>
