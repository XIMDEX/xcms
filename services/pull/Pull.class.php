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




if (!defined('XIMDEX_ROOT_PATH')) {
	define('XIMDEX_ROOT_PATH', realpath(dirname(__FILE__) .  "/../../"));
}

require_once(XIMDEX_ROOT_PATH . '/inc/model/node.php');
require_once(XIMDEX_ROOT_PATH . '/inc/repository/nodeviews/View_FilterMacrosPreview.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/repository/nodeviews/View_PreviewInServer.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/auth/Authenticator.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/model/PortalVersions.class.php');
require_once XIMDEX_ROOT_PATH . '/inc/serializer/Serializer.class.php';

class Pull {

	function __construct() {
	
	}
	
	function get_portal_versions($args) {		
		
		$portal = new PortalVersions();
		$portalVersions = $portal->getAllVersions($args['idportal']);
			
		return Serializer::encode(SZR_JSON, $portalVersions);
	}

	private function showStructuredDocument($idVersion, $args) {

		$pipelineManager = new PipelineManager();
		$content = $pipelineManager->getCacheFromProcessAsContent($idVersion, 'StrDocToDexT', $args);

		// channel has preview in server

		$server = new Node($args['SERVERNODE']);

		if ($server->class->GetPreviewServersForChannel($args['CHANNEL'])) {
			$viewPreviewInServer = new View_PreviewInserver();
			$content = $viewPreviewInServer->transform($idVersion, $content, $args);
		}

		// Specific FilterMacros View for previsuals

		$viewFilterMacrosPreview = new View_FilterMacrosPreview();
		$content = $viewFilterMacrosPreview->transform($idVersion, $content, $args);

		return $content;
	}

	private function showCommonFile($idVersion, $args) {

		$pipelineManager = new PipelineManager();
		$content = $pipelineManager->getCacheFromProcessAsContent($idVersion, 'NotStrDocToFinal', $args);

		if (in_array($args['FILETYPE'], array('ImageFile', 'XimNewsImageFile'))) {
			header("Content-Type: image");
		} else {
			header("Content-Disposition: attachment; filename={$args['NODENAME']}");	
		}

		return $content;
	}

	function getContent($args) {

		$nodeId = $args['idnode'];
		$channelId = $args['idchannel'];
		$idPortalVersion = $args['idportalversion'];
		$serverId = $args['idportal'];

		$nodeId = empty($nodeId) ? $this->getHome($idPortalVersion) : $nodeId;
		
		$node = new Node($nodeId);

		if (!($node->get('IdNode') > 0)) {
			
			$content = "Unexisting node $nodeId\n";

		} else {

			// gets a random channel

			$channelId = empty($channelId) ? array_shift($node->GetProperty('channel')) : $channelId;

			// gets node version

			$idVersion = RelFramesPortal::getNodeVersion($idPortalVersion, $nodeId);

			if (is_null($idVersion)) {

				$content = "Document $nodeId not found in portal $serverId at version $idPortalVersion"; 
			
			} else {

				// populates variables and view/pipelines args

				$args['NODENAME'] = $node->get('Name');
				$args['CHANNEL'] = empty($channelId) ? NULL : $channelId;
				$args['SECTION'] = $node->GetSection();
				$args['PROJECT'] = $node->GetProject();
				$args['SERVERNODE'] = $serverId;
				$args['DEPTH'] = $node->GetPublishedDepth();

				// gets content

				$strDoc = new StructuredDocument($nodeId);

				if (!($strDoc->get('IdDoc') > 0)) {

					$args['FILETYPE'] = $node->nodeType->get('Name');
					$content = $this->showCommonFile($idVersion, $args);

				} else {
					
					$template = $strDoc->get('IdTemplate');
					$idLanguage = $strDoc->get('IdLanguage');
					$docXapHeader = $node->class->_getDocXapHeader($channelId, $idLanguage, $template);

					$root = new Node(10000);
					$transformer = $root->getProperty('Transformer');

					$args['TRANSFORMER'] = $transformer[0];
					$args['LANGUAGE'] = $idLanguage;
					$args['DOCXAPHEADER'] = $docXapHeader;

					$content = $this->showStructuredDocument($idVersion, $args);
				}

				$content = empty($content) ? "Returns void\n" : $content;
			}
		}

		return $content;
	}

/*
*	Returns the idnode of the home page (must be named as 'index')
*	@param idPortalVersion in
*	@return int / NULL
*/

	private function getHome($idPortalVersion) {
		
		$portal = new PortalVersions($idPortalVersion);
		$serverId = $portal->get('IdPortal');

		$serverNode = new Node($serverId);
		$folderId = $serverNode->GetChildByName('documents');

		if (!($folderId > 0)) return NULL;
		
		$folderNode = new Node($folderId);
		$homeContainerId = $folderNode->GetChildByName('index');
		
		if (!($homeContainerId > 0)) return NULL;

		$langs = $folderNode->getProperty('language');

		// gets language (random)

		$language = new Language($langs[0]);
		$homeName = 'index-id' . $language->get('IsoName');
		
		$containerNode = new Node($homeContainerId);
		$homeId = $containerNode->GetChildByName($homeName);

		return $homeId;
	}

/*
*	Returns the last portal version
*	@param array
*	@return int / NULL
*/

	function get_current_portal_version($args) {
		$portal = new PortalVersions();
		$portalVersion = $portal->getLastVersion($args['idportal']);

		return $portal->getId($args['idportal'], $portalVersion);
	}

}

?>