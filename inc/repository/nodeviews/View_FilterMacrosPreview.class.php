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



require_once(XIMDEX_ROOT_PATH . '/inc/model/Versions.php');
require_once(XIMDEX_ROOT_PATH . '/inc/model/node.php');
require_once(XIMDEX_ROOT_PATH . '/inc/model/channel.php');
require_once(XIMDEX_ROOT_PATH . '/inc/model/Server.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/sync/SynchroFacade.class.php');
ModulesManager::file('/inc/PAS_Conector.class.php', 'ximPAS');
require_once(XIMDEX_ROOT_PATH . '/inc/repository/nodeviews/Abstract_View.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/repository/nodeviews/View_FilterMacros.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/repository/nodeviews/Interface_View.class.php');

class View_FilterMacrosPreview extends View_FilterMacros implements Interface_View {

	private $_nodeTypeName = NULL;
	private $mode = NULL;

	/**
	 * Main method. Get a pointer content file and return a new transformed content file. This probably cames from Transformer (View_XSLT), so will be the renderized content.
	 * @param  int $idVersion Node version
	 * @param  string $pointer   file name with the content to transform
	 * @param  array $args      Params about the current node
	 * @return string file name with the transformed content.
	 */
	public function transform($idVersion = NULL, $pointer = NULL, $args = NULL) {

		//Check the conditions
		if (!$this->initializeParams($args,$idVersion))
			return NULL;

		$content = $this->transformFromPointer($pointer);
		//Return the pointer to the transformed content.
		return $this->storeTmpContent($content);
	}

	/**
	 * Initialize params from transformation args 
	 * @param array $args Arguments for transformation
	 * @param int $idVersion 
	 * @return boolean True if everything is allright.
	 */
	protected function initializeParams($args, $idVersion){

		$this->mode = (isset($args['MODE']) && $args['MODE'] == 'dinamic') ? 'dinamic' : 'static';

		if(!$this->_setNode($idVersion,$args))
			return NULL;

		if(!$this->_setIdChannel($args))
			return NULL;

		if(!$this->_setServer($args))
			return NULL;

		if(!$this->_setServerNode($args))
			return NULL;

		if(!$this->_setIdSection($args))
			return NULL;

		if(!$this->_setProjectNode($args))
			return NULL;

		if(!$this->_setDepth($args))
			return NULL;

		if(!$this->_setNodeName($args))
			return NULL;

		return true;
	}

	/**
	 * Load the node param from an idVersion.
	 * @param int $idVersion Version id
	 * @return boolean True if exists node for selected version or the current node.
	 */
	protected function _setNode ($idVersion = NULL,$args = NULL) {

		if (is_null($idVersion)){
			if (array_key_exists('NODETYPENAME', $args)) {
				$this->_nodeTypeName = $args['NODETYPENAME'];
			}
		}else{
			return parent::_setNode($idVersion);
		}
		return true;
	}


	/**
	 * Load the section id from the args array.
	 * @param array $args Transformation args.
	 * @return boolean True if exits the section.
	 */
	private function _setIdSection ($args = array()) {
		if (array_key_exists('SECTION', $args)) {
			$this->_idSection = $args['SECTION'];
		}

		// Check Params:
		if (!isset($this->_idSection) || !($this->_idSection > 0)) {
			XMD_Log::error('VIEW FILTERMACROSPREVIEW: Node section not specified: ' . $args['NODENAME']);
			return NULL;
		}

		return true;
	}

	private function getSectionPath($matches) {

		//Getting section from parent function.
		$section = $this->getSectionNode($matches[1]);
		if (!$section){
			return \App::getValue( 'EmptyHrefCode');
		}
		$idTargetChannel = isset($matches[2]) ? $matches[2] : NULL;
		$dotdot = str_repeat('../', $this->_depth - 2);
		return $dotdot . $section->GetPublishedPath($idTargetChannel, true);
	}

	private function getdotdotpath($matches) {

		$section = new Node($this->_idSection);
		$sectionPath = $section->class->GetNodeURL() . "/";

		if ($this->_node != null){
			if ($this->_node->nodeType->geddt('Name') == 'XimNewsNewLanguage'){
				 $sectionPath .= 'news/';
			}
		}else if ($this->_nodeTypeName != null){
			if ($this->_nodeTypeName == 'XimNewsNewLanguage'){
				$sectionPath .= 'news/';
			}
		}else{
			XMD_Log::error("VIEW FILTERMACROSPREVIEW:no se ha podido determinar si se trata de un node de tipo XimNewsNewLanguage");
		}

		$targetPath = $matches[1];
		$dotdot = str_repeat('../', $this->_depth - 2);
		return $sectionPath . $dotdot . $targetPath;
	}


	private function getLinkPath($matches) {
		
		//Get parentesis content
		$pathToParams = $matches[1];
		// Link target-node
		$res = $this->infererNodeAndChannel($pathToParams);
		if (!$res || !is_array($res) || !count($res)){
			return '';
		}else{
			$idNode = $res["idNode"];
			$idTargetChannel = (count($res)== 3 && isset($res["channel"])) ? $res["channel"] : NULL;

		}
		
		$targetNode = new Node($idNode);
		if (!$targetNode->get('IdNode')) {
			return '';
		}

		if($this->_node && !$this->_node->get('IdNode')) {
			return '';
		}

		$isStructuredDocument = $targetNode->nodeType->GetIsStructuredDocument();

		$targetChannelNode = new Channel($idTargetChannel);

		// External Link
		if ($targetNode->nodeType->get('Name') == 'Link') {
			return $targetNode->class->GetUrl();
		}

		if ($isStructuredDocument) {
			if ($this->mode == 'dinamic') {
				return "javascript:parent.loadDivsPreview(" . $idNode . ")";
			} else {
				$query = App::get('QueryManager');
	    		return $query->getPage() . $query->buildWith(array('nodeid' => $idNode, 'channelid' => $idTargetChannel));
			}
		} else {
			return $targetNode->class->GetNodeURL();
		}
	}

	private function getLinkPathAbs($matches){
		return $this->getLinkPath($matches);
	}
}
?>