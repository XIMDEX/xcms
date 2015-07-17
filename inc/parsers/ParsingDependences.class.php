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

ModulesManager::file('/inc/model/NodeDependencies.class.php');
ModulesManager::file('/inc/dependencies/DepsManager.class.php');
ModulesManager::file('/inc/parsers/ParsingPathTo.class.php');

class ParsingDependencies {

	/**
	 * Gets all document dependencies and updates database
	 * The expected dependencies are from:
	 * Server, Section, Documents, Channels, Xslts, ximlets, links.
	 * @param int $idNode
	 * @param string $content
	 * @return boolean
	 */
	public static function parseAllDependencies($idNode, $content)
	{

		$node = new Node($idNode);
		if (!($node->get('IdNode') > 0)) {
			XMD_Log::error('Error while node loading.');
			return false;
		}

		$dataFactory = new DataFactory($idNode);
		$idVersion = $dataFactory->GetLastVersionId();

		if (!($node->nodeType->get('IsStructuredDocument') == 1)) {
			XMD_Log::info('This node is not a structured document');
			return false;
		}

		switch ($node->get("IdNodeType")) {
			case \Ximdex\Services\NodeType::XML_DOCUMENT:
				$result = self::parseXMLDependencies($node, $content, $idVersion);
				break;
			case \Ximdex\Services\NodeType::CSS_FILE:
				$result = self::parseCssDependencies($node, $content, $idVersion);
				break;
			default:
				return true;
		}

		return $result;
	}

	/**
	 * Search Dependencies in $content for $node and save it.
	 * @param Node $node
	 * @param String $content
	 * @param int $idVersion
	 * @return bool
	 */
	public static function parseXMLDependencies($node, $content, $idVersion){

		$idNode = $node->get("IdNode");
		$structuredDocument = new StructuredDocument($idNode);

		if (!self::clearDependencies($node)){
			return false;
		}

		self::buildDependenciesFromStructuredDocument($node, $structuredDocument);
		self::buildDependenciesWithXimlets($node, $structuredDocument, $content);
		self::buildDependenciesWithXsl($node, $content);
		self::buildDependenciesWithAssetsAndLinks($node, $content, $idVersion);

		return true;
	}

	/**
	 * Search Dependencies in $content for the css $node and save it.
	 * @param Node $node
	 * @param null $content
	 * @return bool
	 */
	public static function parseCssDependencies($node, $content = NULL) {

		if (is_numeric($node))
			$node = new Node($node);

		$patron = "/url\(([^\)]*)?\)/";
		$type = 'ASSET';
		$matches = array();
		$idNode = $node->getID();


		if (!($node->get('IdNode') > 0)) {
			XMD_Log::error('Error while node loading.');
			return false;
		}
		$version = $node->getVersion();
		$idServer = $node->getServer();
		$server = new Node($idServer);
		$server_path = $server->getPath();

		//Delete previous dependencies
		$nodeDependencies = new NodeDependencies();
		$nodeDependencies->deleteBySource($idNode);

		$dependencies = new dependencies();
		$dependencies->deleteMasterNodeandType($idNode, $type);

		$depsMngr = new DepsManager();
		$depsMngr->deleteBySource(DepsManager::NODE2ASSET, $idNode);

		$depsMngr = new DepsManager();

		//Search for images inside the css file content
		preg_match_all($patron, $content, $matches);
		if(!empty($matches) && !empty($matches[1]) ) {
			$images = array_unique(array_values($matches[1]));


			//inserting new dependencies between css file and images.
			foreach($images as $_image ) {
				$dependencies = new dependencies();
				$pathImage = preg_replace("/\.\.(\/\.\.)*/",$server_path, $_image);
				$idNodeDep = self::_getIdNode($pathImage);
				$res = $dependencies->find('IdDep', 'IdNodeMaster = %s and IdNodeDependent = %s', array($idNode,$idNodeDep), MONO);
				if(empty($res) ) {
					$dependencies->insertDependence($idNode, $idNodeDep, $type,$version);
					$nodeDependencies->set($idNode, $idNodeDep, NULL);
					$depsMngr->set(DepsManager::NODE2ASSET, $idNode, $idNodeDep);
				}
			}
		}
	}

	/**
	 * Save dependencies for channels, languages and schema.
	 * @param $node
	 * @param $structuredDocument
	 * @return array
	 */
	private static function buildDependenciesFromStructuredDocument($node, $structuredDocument){

		$channels = $structuredDocument->GetChannels();
		$schemas = (array) $structuredDocument->get('IdTemplate');
		$languages = (array) $structuredDocument->get('IdLanguage');

		$result = self::addDependencies($node, $channels, "channel");
		$result = self::addDependencies($node, $languages, "language") && $result;
		$result = self::addDependencies($node, $schemas, "schema") && $result;

		if ($result){
			$result = array("channels" => $channels,
							"languages" => $languages,
							"schemas" => $schemas);
		}
		return $result;
	}

	/**
	 * Search dependencies with ximlets in $content for $node and save these.
	 * @param $node
	 * @param $structuredDocument
	 * @param $content
	 * @return array
	 */
	private static function buildDependenciesWithXimlets($node, $structuredDocument, $content){

		$sectionXimlets = self::getSectionXimlets($node, $structuredDocument->get('IdLanguage'));
		$ximlets = self::getXimletsInContent($content);
		$ximlets = array_unique(array_merge($ximlets, $sectionXimlets));

		return self::addDependencies($node, $ximlets, "ximlet")? $ximlets: false;
	}

	/**
	 * Search dependencies with xsl templates in $content and save these.
	 * @param $node
	 * @param $content
	 * @return array
	 */
	private static function buildDependenciesWithXsl($node, $content){

		$xslTemplates = self::getXslDependencies($node, $content);
		return self::addDependencies($node, $xslTemplates, "template")? $xslTemplates: false;
	}

	/**
	 * Get dependencies with any linkable element.
	 * This elements are getting from xml and transformed xml in every available channel.
	 * @param Node $node master Node
	 * @param String $content
	 * @param $idVersion
	 * @return array|bool
	 */
	private static function buildDependenciesWithAssetsAndLinks($node, $content, $idVersion){


		$dotDots = $pathTos =array();
		$idNode = $node->get("IdNode");
		$idServer = $node->getServer();
		$strDoc = new StructuredDocument($node->get("IdNode"));
		$channels = $strDoc->GetChannels();
		$transformer = $node->getProperty('Transformer');

		$assets = self::getAssets($content, $node->nodeType->get('Name'));
		$links = self::getLinks($content, $node->nodeType->get('Name'));

		$pipelineManager = new PipelineManager();
		$pathToByChannel = array();

		//Transforming the content for each defined channel.
		foreach ($channels as $idChannel) {
			$postContent = $pipelineManager->getCacheFromProcessAsContent($idVersion, 'StrDocToDexT',
				array('CHANNEL' => $idChannel, 'TRANSFORMER' => $transformer[0]));

			// post-transformation dependencies
			$pathToByChannel[$idChannel] = self::getPathTo($postContent, $idNode);
			$pathTos = array_merge($pathTos, $pathToByChannel[$idChannel]);
			$dotDots = array_merge($dotDots, self::getDotDot($postContent, $idServer));
		}

		$links = array_unique(array_merge($assets, $links, $pathTos, $dotDots));
		//Add dependencies between nodes for every channel in NodeDependencies
		self::addIntoNodeDependencies($idNode, $pathToByChannel);
		unset($pathToByChannel);
		return self::addDependencies($node, $links)? $links: false;
	}

	/**
	 * Save into NodeDependencies relations between nodes by channel.
	 * The relations depends on the transformation for a specific channel.
	 * @param $idNode
	 * @param $nodesByChannel
	 */
	private static function addIntoNodeDependencies($idNode, $nodesByChannel){

		$nodeDependencies = new NodeDependencies();
		foreach ($nodesByChannel as $idChannel => $nodes) {
			foreach ($nodes as $idDep) {
				$nodeDependencies->set($idNode, $idDep, $idChannel);
			}
		}
	}

	/**
	 * Get xsl Dependencies from Content
	 * @param $node
	 * @param $content
	 * @return array
	 */
	private static function getXslDependencies($node, $content){


		$section = new Node($node->GetSection());
		$sectionTemplates = new Node($section->GetChildByName('templates'));

		$project = new Node($node->GetProject());
		$projectTemplates = new Node($project->GetChildByName('templates'));

		$nodeType = new NodeType();
		$nodeType->SetByName('XslTemplate');

		// Gets document tags
		$domDoc = new DOMDocument();
		$domDoc->validateOnParse = true;

		$domDoc->loadXML(\Ximdex\XML\Base::recodeSrc("<docxap>$content</docxap>", \Ximdex\XML\XML::UTF8));

		$xpath = new DOMXPath($domDoc);
		$nodeList = $xpath->query('//*');

		// Searchs xslt nodes
		$xslDependencies = array();
		$parsedTags = array();
		foreach ($nodeList as $element) {
			$tagName = $element->nodeName;
			if (!in_array($tagName, $parsedTags)) {
				$xsltId = $sectionTemplates->GetChildByName($tagName . '.xsl');
				// If not found in section it searchs in project
				if (!($xsltId > 0)) {
					$xsltId = $projectTemplates->GetChildByName($tagName . '.xsl');
				}
				if ($xsltId > 0) {
					$xslDependencies[] = $xsltId;
				}
				$parsedTags[] = $tagName;
			}
		}
		return $xslDependencies;
	}

	/**
	 * Remove from Data Base every dependence for current node
	 * @param Node $node Node master
	 * @return bool
	 */
	private static function clearDependencies($node){

		// deletes old dependency (if exists)
		$idNode = $node->get("IdNode");
		$nodeDependencies = new NodeDependencies();
		$nodeDependencies->deleteBySource($idNode);

		$strDoc = new StructuredDocument($idNode);
		$version = is_null($strDoc->GetLastVersion()) ? 0 : $strDoc->GetLastVersion();

		//Removing all dependencies from Dependencies Table
		$dependencies = new Dependencies();
		$dependencies->deleteByMasterAndVersion($idNode, $version);

		$depsMngr = new DepsManager();

		if (!$depsMngr->deleteBySource(DepsManager::STRDOC_TEMPLATE, $idNode)){
			return false;
		}

		if (!$depsMngr->deleteBySource(DepsManager::NODE2ASSET, $idNode)){
			return false;
		}

		if (!$depsMngr->deleteBySource(DepsManager::XML2XML, $idNode)){
			return false;
		}

		return true;
	}

	/**
	 * Add into Data Base every dependence between master and dependent nodes.
	 * @param int $master
	 * @param array/int $idDeps
	 * @param string $type
	 * @return bool
	 */
	private static function addDependencies($master, $idDeps, $type=null)
	{

		$result = true;

		$idMaster = $master->get("IdNode");
		if (!is_array($idDeps)) {
			$idDeps = (array)$idDeps;
		}

		if (count($idDeps)) {
			foreach ($idDeps as $idDep) {
				if ($idMaster == $idDeps)
					continue;
				$dependencies = new Dependencies();
				$depsMngr = new DepsManager();
				$table = false;
				//if we don't know the type, we'll get it from IdNodeType
				$currentType = $type ? strtolower($type) : self::inferType($idDep);
				if ($currentType) {
					switch ($currentType) {
						case Dependencies::ASSET:
							$table = DepsManager::NODE2ASSET;
							break;
						case Dependencies::TEMPLATE:
							$table = DepsManager::STRDOC_TEMPLATE;
							break;
						case Dependencies::XIMLET:
						case Dependencies::XML:
							$table = DepsManager::XML2XML;
							break;
					}

					$dependencies->insertDependence($idMaster, $idDep, $currentType);
					if ($table){
						$result = $depsMngr->set($table, $idMaster, $idDep) && $result;
					}
				}
			}
		}

		return $result;
	}

	/**
	 * Infer the type for dependence of $idNode
	 * @param int $idNode
	 * @return bool|string
	 */
	private static function inferType ($idNode){
		$type = false;
		$depNode = new Node($idNode);
		if ($depNode->get('IdNode') > 0) {
			switch ((int)$depNode->get("IdNodeType")) {
				case Ximdex\Services\NodeType::LINK:
					$type = Dependencies::XIMLINK;
					break;
				case Ximdex\Services\NodeType::CSS_FILE:
				case Ximdex\Services\NodeType::BINARY_FILE:
				case Ximdex\Services\NodeType::TEXT_FILE:
				case \Ximdex\Services\NodeType::NODE_HT:
				case Ximdex\Services\NodeType::IMAGE_FILE:
					$type = Dependencies::ASSET;
					break;
				case Ximdex\Services\NodeType::XIMLET_CONTAINER:
				case Ximdex\Services\NodeType::XIMLET:
					$type = Dependencies::XIMLET;
					break;
				case Ximdex\Services\NodeType::XML_DOCUMENT:
				case \Ximdex\Services\NodeType::METADATA_DOCUMENT:
					$type = Dependencies::XML;
					break;
				case Ximdex\Services\NodeType::XSL_TEMPLATE:
					$type = Dependencies::TEMPLATE;
					break;
				default:
					break;
			}
		}
		return $type;
	}

	private static function getLinks($content, $nodeTypeName = NULL) {
	   	preg_match_all('/ a_enlaceid[_|\w|\d]*\s*=\s*[\'|"](\d)(,\d)?[\'|"]/i', $content, $matches);
		$links = sizeof($matches[1]) > 0 ? $matches[1] : array();
	
		preg_match_all('/ a_import_enlaceid[_|\w|\d]*\s*=\s*[\'|"](\d+)[\'|"]/i', $content, $matches);
		$importLinks = sizeof($matches[1]) > 0 ? $matches[1] : array();

		if ($nodeTypeName == 'XimNewsNewLanguage') {

			preg_match_all('/ a_enlaceid_enlace[_|\w|\d]*\s*=\s*[\'|"]([\d|\,]+)[\'|"]/i', $content, $matches);
			$links2 = sizeof($matches[1]) > 0 ? $matches[1] : array();

			preg_match_all('/ a_enlaceid_noticia_enlace_asociado[_|\w|\d]*\s*=\s*[\'|"]([\d|\,]+)[\'|"]/i', $content,$matches);
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
		$assets = sizeof($matches[1]) > 0 ? $matches[1] : array();

		if ($nodeTypeName == 'XimNewsNewLanguage') {
			preg_match_all('/ a_enlaceid_noticia_imagen_asociada[_|\w|\d]*\s*=\s*[\'|"]([\d|\,]+)[\'|"]/i', $content,$matches);
			$images = sizeof($matches[1]) > 0 ? $matches[1] : array();

			preg_match_all('/ a_enlaceid_noticia_video_asociado[_|\w|\d]*\s*=\s*[\'|"]([\d|\,]+)[\'|"]/i', $content,$matches);
			$videos = sizeof($matches[1]) > 0 ? $matches[1] : array();

			preg_match_all('/ a_enlaceid_noticia_archivo_asociado[_|\w|\d]*\s*=\s*[\'|"]([\d|\,]+)[\'|"]/i', $content,$matches);
			$files = sizeof($matches[1]) > 0 ? $matches[1] : array();

			$assets = array_merge($assets, $images, $videos, $files);
		}

		return $assets;
    	}

	private static function getDotDot($content, $idServer) {

		preg_match_all("/@@@RMximdex\.dotdot\((css|common)([^\)]*)\)@@@/", $content, $matches);

		$result = array();

		if (count($matches[1]) > 0) {

			$serverNode = new Node($idServer);
			$idCssFolder = $serverNode->GetChildByName('css');
			$idCommonFolder = $serverNode->GetChildByName('common');

			$cssNode = new Node($idCssFolder);
			$commonNode = new Node($idCommonFolder);
			$css = $common = array();
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

			$result = array_merge($css, $common);
		}

		return $result;
	}

	private static function getPathTo($content, $nodeId) {

		$links = array();
		$parserPathTo = new ParsingPathTo();
		preg_match_all("/@@@RMximdex\.pathto\(([^\)]*)\)@@@/", $content, $matches);
		if (count($matches[1]))
			foreach ($matches[1] as $pathTo) {
				$parserPathTo->parsePathTo($pathTo, $nodeId);
				$links[] = $parserPathTo->getIdNode();
			}

    	return $links;
	}

	/**
	 * @param $_path
	 * @return null
	 */
	private static function _getIdNode($_path) {

		//Building file and path
		$file = pathinfo($_path);
		$filename = $file["filename"].".".$file['extension'];
		$path = $file["dirname"];

		//Searching in Nodes by name and path.
		$node = new Node();
		$foundNodes = $node->find("IdNode", "Path =%s and Name=%s", array($path, $filename), MONO);
		if ($foundNodes and is_array($foundNodes) && count($foundNodes))
			return $foundNodes[0];
		else
			return null;
	}

	/**
	 * Checks if section has ximlet dependencies and returns these dependencies
	 * @param int $idSection
	 * @param int $idLanguage
	 * @return array
	 */
	private static function getSectionXimlets($node, $idLanguage) {

		$sectionId = $node->getSection();
		$depsManager = new DepsManager();
		$ximletContainers = $depsManager->getBySource(DepsManager::SECTION_XIMLET, $sectionId);

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

		return  sizeof($ximlets) > 0 ? $ximlets : array();
	}

	/**
	 * Find ximlets id in content by Regexp ximlet([0-9]+)
	 * @param String $content
	 * @return array Dependencies found.
	 */
	private static function getXimletsInContent($content) {
		preg_match_all('/ximlet\((\d+)\)/i', $content, $matches);
		return  sizeof($matches[1]) > 0 ? $matches[1] : array();
	}
}
