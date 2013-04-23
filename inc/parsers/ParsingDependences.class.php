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

require_once(XIMDEX_ROOT_PATH . '/inc/dependencies/DepsManager.class.php');
require_once(XIMDEX_ROOT_PATH . "/inc/model/NodeDependencies.class.php");

class ParsingDependences {

    private static function getXimlets($content) {

    	preg_match_all('/ximlet\((\d+)\)/i', $content, $matches);

		return  sizeof($matches[1]) > 0 ? self::setFormatArray($matches[1], 'ximlet') : array();
    }

    private static function getLinks($content, $nodeTypeName = NULL) {

	   	preg_match_all('/ a_enlaceid[_|\w|\d]*\s*=\s*[\'|"]([\d|\,]+)[\'|"]/i', $content, $matches);
		$links = sizeof($matches[1]) > 0 ? self::setFormatArray($matches[1], 'link') : array();

    	preg_match_all('/ a_import_enlaceid[_|\w|\d]*\s*=\s*[\'|"](\d+)[\'|"]/i', $content, $matches);
		$importLinks = sizeof($matches[1]) > 0 ? self::setFormatArray($matches[1], 'link') : array();

		if ($nodeTypeName == 'XimNewsNewLanguage') {

			preg_match_all('/ a_enlaceid_enlace[_|\w|\d]*\s*=\s*[\'|"]([\d|\,]+)[\'|"]/i', $content, $matches);
			$links2 = sizeof($matches[1]) > 0 ? self::setFormatArray($matches[1], 'link') : array();

			preg_match_all('/ a_enlaceid_noticia_enlace_asociado[_|\w|\d]*\s*=\s*[\'|"]([\d|\,]+)[\'|"]/i', $content,
				$matches);

			$links = array_merge($links, $links2);
		}

		if ($nodeTypeName == 'XimNewsBulletinLanguage') {

			preg_match_all('/<prev nodeid="([\d]+)"/i', $content, $matches);
			$prevs = sizeof($matches[1]) > 0 ? self::setFormatArray($matches[1], 'link') : array();

			preg_match_all('/<next nodeid="([\d]+)"/i', $content, $matches);
			$nexts = sizeof($matches[1]) > 0 ? self::setFormatArray($matches[1], 'link') : array();

			$links = array_merge($links, $prevs, $nexts);
		}

    	return array_merge($links, $importLinks);
    }

    private static function getAssets($content, $nodeTypeName = NULL) {

    	preg_match_all('/<url.*>\s*(\d+)\s*<\/url>/i', $content, $matches);

		$assets = sizeof($matches[1]) > 0 ? self::setFormatArray($matches[1], 'asset') : array();

		if ($nodeTypeName == 'XimNewsNewLanguage') {

			preg_match_all('/ a_enlaceid_noticia_imagen_asociada[_|\w|\d]*\s*=\s*[\'|"]([\d|\,]+)[\'|"]/i', $content,
				$matches);
			$images = sizeof($matches[1]) > 0 ? self::setFormatArray($matches[1], 'asset') : array();

			preg_match_all('/ a_enlaceid_noticia_video_asociado[_|\w|\d]*\s*=\s*[\'|"]([\d|\,]+)[\'|"]/i', $content,
				$matches);
			$videos = sizeof($matches[1]) > 0 ? self::setFormatArray($matches[1], 'asset') : array();

			preg_match_all('/ a_enlaceid_noticia_archivo_asociado[_|\w|\d]*\s*=\s*[\'|"]([\d|\,]+)[\'|"]/i', $content,
				$matches);
			$files = sizeof($matches[1]) > 0 ? self::setFormatArray($matches[1], 'asset') : array();

			$assets = array_merge($assets, $images, $videos, $files);
		}

		return $assets;
    }

	private static function getDotDot($content, $idServer) {

		preg_match_all("/@@@RMximdex\.dotdot\((css|common)([^\)]*)\)@@@/", $content, $matches);

		$result = array();

		if (sizeof($matches[1]) > 0) {

			$serverNode = new Node($idServer);
			$idCssFolder = $serverNode->GetChildByName('css');
			$idCommonFolder = $serverNode->GetChildByName('common');

			$cssNode = new Node($idCssFolder);
			$commonNode = new Node($idCommonFolder);

			foreach ($matches[1] as $n => $match) {

				switch ($match) {

					case 'css':

						$id = $cssNode->GetChildByName(substr($matches[2][$n], 1));

						if (!($id > 0)) {
							XMD_Log::error("Css file {$matches[2][$n]} not found");
						} else {
							$css[] = $id;
						}

						break;

					case 'common':

						$id = $commonNode->GetChildByName(substr($matches[2][$n], 1));

						if (!($id > 0)) {
							XMD_Log::error("Common file {$matches[2][$n]} not found");
						} else {
							$common[] = $id;
						}

						break;

					default:
						break;
				}
			}

			$csss = (isset($css) && sizeof($css) > 0) ? self::setFormatArray($css, 'css') : array();
			$commons = (isset($common) && sizeof($common) > 0) ? self::setFormatArray($common, 'script') : array();

			$result = array_merge($csss, $commons);
		}

		return $result;
	}

	private static function getPathTo($content) {

		preg_match_all("/@@@RMximdex\.pathto\((([0-9]+),([0-9]*))\)@@@/", $content, $matches);
		$links = sizeof($matches[1]) > 0 ? self::setFormatArray($matches[1], 'link') : array();

		preg_match_all("/@@@RMximdex\.pathto\(([0-9]+)\)@@@/", $content, $matches);
		$assets = sizeof($matches[1]) > 0 ? self::setFormatArray($matches[1], 'asset') : array();

    	return array_merge($links, $assets);
	}


	private static function setFormatArray($matches, $depType) {

		foreach ($matches as $id) {
			$result[] = array('id' => $id, 'type' => $depType);
		}

		return is_array($result) ? $result : array();
	}


    /**
     * Function which obtain the structuredDocument indentifier
     * (normal if it is resolved, or exportation one if its not resolved yet)
     *
     * @param string $content contenido del xml
     * @return array
     */
    function GetStructuredDocumentXimletsExtended($content) {
    	$regExp = '';
    	if (preg_match_all('/<ximlet(\s*idExportationXimlet\="(\d*?)"\s*)?>@@@GMximdex.ximlet\((\d+)\)/i', $content, $matches) > 0) {
    		// Looking for in all results in a iterative way
    		$totalMatches = count($matches[0]); // por ejemplo, deberían de tener todos la misma cantidad de resultados WARNING!!
    		/* In position 2 of the array: IdExportationXimlet
    		 * In position 3 of the array: Gmximdex.ximlet
    		 *
    		 * Position 2 is mandatory over the position 3 always this method is execueted (as we're coming from ximIO)
    		 */
    		$results = array();
    		$results[0] = array();
    		$results[1] = array();
    		for ($i = 0; $i < $totalMatches; $i++) {
				$results[0][] = $matches[0][$i];
				// Previous node which is included in the xml
    			$results[2][] = $matches[3][$i];
    			if (!empty($matches[2][$i])) {
    				$results[1][] = $matches[2][$i];
    				continue;
    			}
    			$results[1][] = $matches[3][$i];
    		}

    		return $results;
    	}
    	return array();
    }

/*
    Function which return a content deleting its links to a determined nodeId
*/
    function RemoveLinks($nodeId,$content){
	$pattern = '/(<enlace a_enlaceid_url="'.$nodeId.')(,[\w|\d|\s|"|_|=]+>)([\w|\W]+)(<\/enlace>)/i';
	$replacement = "${1}${2}\${3}${4}";
        $content = preg_replace($pattern,$replacement,$content);

	return $content;
    }

/*
    Function which replace a data arry in the xml for a pvd, returning the document content
*/
    function ReplaceDataInPvd($xmlPvd,$data){
	foreach($data as $nameTag => $contentTag){
	    if($contentTag['type'] == 'attribute'){
		$pattern = '/'.$nameTag.'=""/';
		$replacement = ''.$nameTag.'="'.$contentTag['value'].'"';
	    }
	    else if($contentTag['type'] == 'element'){
		$pattern = '/(<[\w|\s|\d|"|_|=]+)(name="'.$nameTag.'")([\w|\s|\d|"|_|=]+>)(\[[\w|\s]+\])(<\/[\w|_]+>)/i';
		$replacement = "\${1}\${2}\${3}${4}".$contentTag['value']."\${5}";
	    }
	    else{
		XMD_Log::info("Type no soportado");
	    }

	    $xmlPvd = preg_replace($pattern,$replacement,$xmlPvd);
	}

	return $xmlPvd;
    }

/*
	Gets all document dependencies and updates database
*/
	public static function getAll($idNode, $content, $idVersion) {

		$node = new Node($idNode);
		if (!($node->get('IdNode') > 0)) {
			XMD_Log::error('Error durante la carga del nodo');
			return false;
		}

		if (!($node->nodeType->get('IsStructuredDocument') == 1)) {
			XMD_Log::info('Node is not structured document');
			return false;
		}

		$idServer = $node->getServer();
		$idSection = $node->GetSection();
		$transformer = $node->getProperty('Transformer');

		$strDoc = new StructuredDocument($idNode);
		$version = is_null($strDoc->GetLastVersion()) ? 0 : $strDoc->GetLastVersion();
		$channels = $strDoc->GetChannels();

		// pre-transformation dependencies

		$pvdDeps = self::setFormatArray((array) $strDoc->get('IdTemplate'), 'pvd');
		$channelDeps = self::setFormatArray($channels, 'channel');
		$languageDeps = self::setFormatArray((array) $strDoc->get('IdLanguage'), 'language');
		$ximlets = self::getXimlets($content);
		$sectionXimlets = self::getSectionXimlets($idSection, $strDoc->get('IdLanguage'));
		$assets = self::getAssets($content, $node->nodeType->get('Name'));
		$links = self::getLinks($content, $node->nodeType->get('Name'));

		$result = $strDoc->find('TargetLink', 'IdDoc = %s and TargetLink != 0', array($idNode), MONO);
		$symlinks = sizeof($result) > 0 ? self::setFormatArray($result, 'symlink') : array();

		$templateDeps = dependencies::getXslDependencies($content, $idNode);
		$templateDeps = sizeof($templateDeps) > 0 ? self::setFormatArray($templateDeps, 'ptd') : array();

		$pipelineManager = new PipelineManager();

		$dotDots = array();
		$pathTos = array();

		foreach ($channels as $idChannel) {
			$postContent = $pipelineManager->getCacheFromProcessAsContent($idVersion, 'StrDocToDexT',
				array('CHANNEL' => $idChannel, 'TRANSFORMER' => $transformer[0]));

			// post-transformation dependencies

			$pathTos = array_unique(array_merge($pathTos, self::getPathTo($postContent)));
			$dotDots = array_unique(array_merge($dotDots, self::getDotDot($postContent, $idServer)));
		}

		// group dependencies css, asset and script in metadependencie 'structure'

		$structures = array_unique(array_merge($dotDots, $pathTos, $assets));

		$structuralDeps = explode(',', Config::GetValue('StructuralDeps'));

		foreach ($structures as $dep) {
			if (in_array($dep['type'], $structuralDeps)) {
				$structure[] = array('id' => $dep['id'], 'type' => 'structure');
			}
		}

		$structure = isset($structure) && count($structure) > 0 ? $structure : array();

		$totalDependencies = array_merge($links, $assets, $ximlets, $symlinks, $pvdDeps, $channelDeps, $languageDeps,
			$templateDeps, $dotDots, $sectionXimlets, $pathTos, $structure);

		// deletes old dependency (if exists)

		$nodeDependencies = new NodeDependencies();
		$nodeDependencies->deleteBySource($idNode);

		$dependencies = new dependencies();
		$dependencies->BorrarDependenciasNodoDependiente($idNode, $version);

		$depsMngr = new DepsManager();
		$depsMngr->deleteBySource(DepsManager::STRDOC_NODE, $idNode);
		$depsMngr->deleteBySource(DepsManager::STRDOC_TEMPLATE, $idNode);
		$depsMngr->deleteBySource(DepsManager::STRDOC_XIMLET, $idNode);
		$depsMngr->deleteBySource(DepsManager::STRDOC_ASSET, $idNode);
		$depsMngr->deleteBySource(DepsManager::STRDOC_CSS, $idNode);
		$depsMngr->deleteBySource(DepsManager::STRDOC_SCRIPT, $idNode);

		// insert dependencies

		if (sizeof($totalDependencies) > 0) {

			foreach($totalDependencies as $dep) {

				switch ($dep['type']) {

					case 'link':
						$pair = explode(",",$dep['id']);
						$idDep = $pair[0];
						$table = DepsManager::STRDOC_NODE;
						$type = 'LINK';
						$idChannel = array_key_exists(1, $pair) ? $pair[1] : NULL;

						// insert in nodeDependencies table

						$nodeDependencies->set($idNode, $idDep, $idChannel);
						break;

					case 'asset':
						$idDep = $dep['id'];
						$table = DepsManager::STRDOC_ASSET;
						$type = 'ASSET';
						break;

					case 'ximlet':
						$idDep = $dep['id'];
						$table = DepsManager::STRDOC_XIMLET;
						$type = 'XIMLET';
						break;

					case 'symlink':
						$idDep = $dep['id'];
						$type = 'SYMLINK';
						$table = '';
						break;

					case 'pvd':
						$idDep = $dep['id'];
						$type = 'PVD';
						$table = '';
						break;

					case 'language':
						$idDep = $dep['id'];
						$type = 'LANGUAGE';
						$table = '';
						break;

					case 'channel':
						$idDep = $dep['id'];
						$type = 'CHANNEL';
						$table = '';
						break;

					case 'ptd':
						$idDep = $dep['id'];
						$type = 'TEMPLATE';
						$table = DepsManager::STRDOC_TEMPLATE;
						break;

					case 'css':
						$idDep = $dep['id'];
						$type = 'CSS';
						$table = DepsManager::STRDOC_CSS;
						break;

					case 'script':
						$idDep = $dep['id'];
						$type = 'SCRIPT';
						$table = DepsManager::STRDOC_SCRIPT;
						break;

					case 'structure':
						$idDep = $dep['id'];
						$type = 'STRUCTURE';
						$table = DepsManager::STRDOC_STRUCTURE;
						break;

				}

				// avoid the insertion of unexisting nodes

				$depNode = new Node($idDep);

				if ($depNode->get('IdNode') > 0){

				// insert in Dependencies table

					$dependencies = new dependencies();
					$dependencies->InsertaDependencia($idDep, $idNode, $type, $version);

					// insert in Rel table

					if (!empty($table)) {
						$depsMngr->set($table, $idNode, $idDep);
					}
				}
			}
		}

		return true;
	}

	function parseCssDependencies($idNode, $content = NULL) {
		$patron = "/url\((.*)?\)/";
		$matches = array();

		$node = new Node($idNode);
		if (!($node->get('IdNode') > 0)) {
			XMD_Log::error('Error durante la carga del nodo');
			return false;
		}
		$version = $node->getVersion();
		$idServer = $node->getServer();
		$server = new Node($idServer);
		$server_path = $server->getPath();

		//Delete previous dependdencies
		$nodeDependencies = new NodeDependencies();
		$nodeDependencies->deleteBySource($idNode);
		$dependencies = new dependencies();
		$dependencies->BorrarDependenciasNodoDependiente($idNode, $version);
		$depsMngr = new DepsManager();
		$depsMngr->deleteBySource(DepsManager::STRDOC_NODE, $idNode);


		$DB = new DB();
		$depsMngr = new DepsManager();
		//Search images in css content
		preg_match_all($patron, $content, $matches);
		if(!empty($matches) && !empty($matches[1]) ) {
				$images = array_unique(array_values($matches[1]));
				$type = 'LINK';
				//insert new dependencies(css<-images)
				foreach($images as $_image ) {
					$path_image = str_replace("..",$server_path, $_image);
					$idNodeDep = $this->_getIdNode($path_image);
					if(!empty($idNodeDep) ) {
						$dependencies->InsertaDependencia($idNode, $idNodeDep, $type,$version);
						$nodeDependencies->set($idNode, $idNodeDep, NULL);
						$depsMngr->set(DepsManager::STRDOC_NODE, $idNode, $idNodeDep);
					}
				}
		}

	}

	private function _getIdNode($_path) {
		$file = pathinfo($_path);

		$filename = $file["filename"].".".$file['extension'];
		$path = $file["dirname"];
		$dbObj = new DB();
		$query = sprintf("SELECT IdNode FROM Nodes WHERE Path = '%s' AND Name = '%s'", $path, $filename);


		$dbObj->Query($query);
		if($dbObj->numRows) {
			return $dbObj->GetValue('IdNode');
		}else {
			return null;
		}

	}



	/**
	 * Checks if section has ximlet dependencies and returns these dependencies
	 * @param int $idSection
	 * @param int $idLanguage
	 * @return array
	 */

	public static function getSectionXimlets($sectionId, $idLanguage) {

		$depMngr = new DepsManager();
		$ximletContainers = $depMngr->getBySource(DepsManager::SECTION_XIMLET, $sectionId);

		if (!(sizeof($ximletContainers) > 0)) {
			return array();
		}

		foreach ($ximletContainers as $idXimletContainer) {

			$ximletContainer = new Node($idXimletContainer);
			$ximlets = $ximletContainer->GetChildren();

			foreach ($ximlets as $ximletId) {

				$ximlet = new StructuredDocument($ximletId);

				if ($ximlet->get('IdLanguage') == $idLanguage) {

					$ximlets[] = $ximletId;
				}
			}
		}

		return  sizeof($ximlets) > 0 ? self::setFormatArray($ximlets, 'ximlet') : array();
	}
}
?>
