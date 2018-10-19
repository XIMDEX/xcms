<?php
/**
 *  \details &copy; 2018 Open Ximdex Evolution SL [http://www.ximdex.org]
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

use Ximdex\Logger;
use Ximdex\Models\Channel;
use Ximdex\Models\Node;
use Ximdex\Models\Version;
use Ximdex\MVC\ActionAbstract;
use Ximdex\Runtime\DataFactory;

class Action_poolPreview extends ActionAbstract
{
	/**
	 * Main method: shows initial form
	 */
    public function index()
    {
    	$this->insertJsFiles();
    	$this->insertCssFiles();	
    	$channel = new Channel();
    	$allChannels = $channel->find('IdChannel, Name');
    	$channels = [];
    	foreach ($allChannels as $channelInfo) {
    		$channels[$channelInfo['IdChannel']] = $channelInfo['Name'];
    	}
    	$values = array(
    		'nodeid' => $this->request->getParam('nodeid'),
    		'channels' => $channels
		);
    	$this->render($values, NULL, 'only_template.tpl');
    }
    
    // AJAX FUNCTIONS
    
	/**
	 * Returns an array with all versions and subversion for idNode
	 */
    public function getVersionsNode()
    {	
    	$verAndSubVerList = array();
    	$idNode = $this->request->getParam('idnode');
    	$datafactory = new DataFactory($idNode);
    	
    	// Get all versions for the idnode
    	$versionList = $datafactory->GetVersionList("desc");
    	if (!empty($versionList)){
    		foreach ($versionList as $value) {
    		    
    			// Get all subversion for each version
    			$subVersionList = $datafactory->GetSubVersionList($value);
    			$verAndSubVerList[$value] = $subVersionList;
    		}
    	}
    	$this->render(array('verAndSubVerList' => $verAndSubVerList));
    }
    
    /**
     * Returns the nodes that have linked in this node
     */
    public function getLinkedNodes()
    {    
        $result = [];
    	$this->render(array('links' => $result));
    }
    
    /**
     * Returns the nodes that have a link to this node
     */
    public function getLinkNodes()
    {
        $result = [];
		$this->render(array('links' => $result));
    }
    
	/**
	 * Returns all labels
	 */
    public function getLabels(){
        
    	$label = new \Ximdex\Models\ListLabel();
    	$labels = $label->find(ALL, "1=1 order by Name asc");
		$this->render(array('labels' => $labels));
	}
	
	/**
	 * Returns the versions that have associated this label
	 */
	public function getVersionsForLabel()
	{    
		$idNode = $this->request->getParam('idnode');
		if (!($idNode > 0)) {
    		return null;
		} else {
    		$rel = new  \Ximdex\Models\RelVersionsLabel();
    		$rels = $rel->find(ALL, 'idLabel = %s', array($idNode));
    		$relsInfo = array();
			if (is_array($rels)){
				foreach ($rels as $value) {
					$v = new Version($value['idVersion']);
					$idNode = $v->get('IdNode');
					$relsInfo[$value['idVersion']] = $this->_getNodeInfo(array($idNode));
				}
			}
    		Logger::info("EN getVersionsForLabel ".print_r($relsInfo,true));
    		$this->render(array('relations' => $relsInfo));
    	}
	}
	
	/**
	 * Inserts a relation between idLabel and IdVersion in the RelVersionsLabel table
	 */
	public function asociateNodeToLabel()
	{    
		Logger::info("associate");
		$idNode = $this->request->getParam('idnode');
		$idVersion = $this->request->getParam('idversion');
		$idSubVersion = $this->request->getParam('idsubversion');
		$labels = explode(',', $this->request->getParam('labels'));
		$sms = array();
		
		// This var has the version id from the version table
		$versionid = null;
		if (is_null($idNode)) {
			array_push($sms, _("Label cannot be associated with version, empty idnode"));
		} else {
		    if (is_null($labels) || (!is_array($labels) && ($labels <= 0))) {
				array_push($sms, _("Wrong label value"));
		    } else {
				if (is_null($idVersion) || (is_null($idSubVersion))){
				    
					// If it has not version or subversion, i get it the last version for this idnode
					$dataFactory = new DataFactory($idNode);
					$versionid = $dataFactory->GetLastVersionId();
				} else {
					$versionid = $dataFactory->getVersionId($idVersion, $idSubVersion);
				}
				if (is_null($versionid)) {
					array_push($sms, _("Id version has not been found in the association of labels with versions"));
				}
				Logger::info("Labels are going to be associated with version " . $versionid);
				if (is_array($labels)){
				    
					// I have a label array, insert a relation for each
					foreach ($labels as $value) {
						Logger::info("Associated IdVersion " . $versionid . " and label " . $value);
						$rel = new  \Ximdex\Models\RelVersionsLabel();
						$rel->set('idVersion',$versionid);
						$rel->set('idLabel', $value);
						$rel->add();
					}
				} else {
				    
					// Only have a label, insert the relation 
					Logger::info("Associated IdVersion " . $versionid." and label " . $labels);
					$rel = new  \Ximdex\Models\RelVersionsLabel();
					$rel->set('idVersion',$versionid);
					$rel->set('idLabel', $labels);
					$rel->add();
				}
				array_push($sms, _("Correct association"));
			}
		}
		Logger::debug(print_r($sms,true));
		$sms = \Ximdex\XML\Base::encodeArrayElement($sms, \Ximdex\XML\XML::UTF8);
		$this->render(array('sms' => $sms));
	}
	
	/**
	 * Deletes a label
	 */
	public function deleteLabel()
	{}
    
    /**
     * Returns info about a node
     */
	public function getInfoForPreview()
	{   
    	$idNode = $this->request->getParam('idnode');
    	/*
    	$idversion = $this->request->getParam('idversion');
    	$idsubversion = $this->request->getParam('idsubversion');
    	*/
		$node = new Node($idNode);
		// Only show links to structuredDocument document
		if ($node->nodeType != null && $node->nodeType->get('IsStructuredDocument')){
			$path = $node->GetPath();
		}
    	$this->render(array('info' => array('url' => $path)));
    }
    
    // AUXILIARY FUNCTIONS
    
    private function _getNodeInfo($nodeList)
    {    
        if (!is_array($nodeList)) {
            return array();	
        }
		$processedNodeList = array();
		foreach ($nodeList as $idNode) {
			$node = new Node($idNode);
			
			// Only show links to structuredDocument document
			if ($node->nodeType != null && $node->nodeType->get('IsStructuredDocument')) {
				$processedNodeList[$idNode] = $node->GetPath();
			}
		}
		return $processedNodeList;
	}

	private function insertJsFiles()
	{
		// Jquery core
		$this->addJs(Extensions::JQUERY);
		$this->addJs(Extensions::JQUERY_UI);
		
		// Jquery plugins
		$this->addJs(Extensions::JQUERY_PATH.'/plugins/panel/ui.panel.js');
		$this->addJs(Extensions::JQUERY_PATH.'/plugins/jquery.blockUI.js');
    	$this->addJs(Extensions::JQUERY_PATH.'/plugins/slidebox/slidebox.js');
    	$this->addJs(Extensions::JQUERY_PATH.'/plugins/ui.dropdownchecklist.js');
    	$this->addJs(Extensions::JQUERY_PATH.'/plugins/thickbox/thickbox.js');
    	$this->addJs('/actions/poolPreview/resources/js/poolPreview.js');
    	$this->addJs('/assets/js/helper/query_manager.js');
    	$this->addJs('/actions/manageList/resources/js/common.js');
	}
	
	private function insertCssFiles()
	{    
		$this->addCss('/actions/poolPreview/resources/css/resources/css/default.css');
    	$this->addCss('/actions/poolPreview/resources/css/slidebox.css');
    	$this->addCss('/actions/poolPreview/resources/css/ui.panel.css');
    	$this->addCss('/actions/poolPreview/resources/css/jquery-ui-1.8.2.custom.css');
    	$this->addCss('/actions/poolPreview/resources/css/poolPreview.css');
    	$this->addCss('/actions/poolPreview/resources/css/ui.dropdownchecklist.css');
    	$this->addCss('/vendors/jquery/plugins/thickbox/thickbox.css');
    	$this->addCss('/actions/manageList/resources/css/common.css');
		$this->addCss('/assets/style/jquery/ximdex_theme/jquery-ui-1.8.2.custom.css');
		$this->addCss('/assets/style/main_extended.css');	
	}
}
