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
    define ('XIMDEX_ROOT_PATH', realpath(dirname(__FILE__)) . '/../../');
}
require_once XIMDEX_ROOT_PATH . '/inc/model/NodeAllowedContent.class.php';
require_once XIMDEX_ROOT_PATH . '/inc/model/RelNodeTypeMimeType.class.php';

class Action_newemptynode extends ActionAbstract {

    // Main Method: it shows the initial form
    function index() {
		$nodeID = $this->request->getParam("nodeid");
		$node = new Node($nodeID);
		$nodetype = $node->nodeType->GetID();		

		$nac = new NodeAllowedContent();
		$allowedchildsId=$nac->getAllowedChilds($nodetype);
		$childs=array();
		foreach($allowedchildsId as $ch){
			$nt = new NodeType($ch);
			$name = $nt->GetName();
			if($nt->GetIsPlainFile() && !(strcmp($name,'ImageFile')==0 || strcmp($name,'BinaryFile')==0 || strcmp($name,'VisualTemplate')==0 || strcmp($name,'Template')==0)){
				$childs[]=array("idnodetype"=>$ch,"nodetypename"=>$name);
			}
		}
		$countChilds=count($childs);		
		$this->request->setParam("go_method", "addNode");

		$values = array ("childs" => $childs,
				"countChilds" => $countChilds,
				"nodeID" => $nodeID,"name" => $node->get('Name'));

		$this->render($values, 'addNode', 'default-3.0.tpl');
    }	

	// Adds a new empty node to Ximdex
	function addNode() {
		$parentId = $this->request->getParam("nodeid");
		$nodetype = $this->request->getParam("nodetype");
		$name = $this->request->getParam("name");

		//getting and adding file extension
		$rntmt = new RelNodeTypeMimeType();
		$ext = $rntmt->getFileExtension($nodetype);
		if(strcmp($ext,'')!=0){
			$name_ext = $name.".".$ext;		
		}
		else{
			$name_ext=$name;
		}
		// creating new node and refreshing the tree
		$file = new Node();
        $idfile = $file->CreateNode($name_ext, $parentId, $nodetype, null);

        if ($idfile > 0) {
			$content=$this->getDefaultContent($nodetype,$name);
			$file->SetContent($content);
                $this->messages->add(sprintf('%s '._('has been successfully created'), $name), MSG_TYPE_NOTICE);
                    $this->reloadNode($parentId);
                } else {
                    $this->messages->add(sprintf(_('The operation has failed: %s'), $file->msgErr), MSG_TYPE_ERROR);
                }
		$values = array('messages' => $this->messages->messages, 'parentID' => $parentId, 'nodeID' => $idfile);
		
		$this->sendJSON($values);
	}
	
	//return each content depending on the nodetype passed
	function getDefaultContent($nt,$name){
		switch($nt){
			case 5039: 
			$content="<<< DELETE \nTHIS\n CONTENT >>>";
			break;

			case 5028: 
			$content="/* CSS File: ".$name.". Write your rules here. */\n\n * {}";
			break;

			case 5077: 
			$content="<?xml version='1.0' encoding='utf-8'?>\n<xsl:stylesheet xmlns:xsl='http://www.w3.org/1999/XSL/Transform' version='1.0'>\n<xsl:template name='".$name."' match='".$name."'>\n<!-- Insert your code here -->\n</xsl:template>\n</xsl:stylesheet>";
			break;

			case 5078: 
			$content="<?xml version='1.0' encoding='UTF-8' ?>\n<grammar xmlns='http://relaxng.org/ns/structure/1.0' xmlns:xim='http://ximdex.com/schema/1.0'>\n<!-- Create your own grammar here -->\n<!-- Need help? Visit: http://relaxng.org/tutorial-20011203.html -->\n</grammar>";
			break;

			case 5076: 
			$content="<html>\n<head>\n</head>\n<body>\n</body>\n</html>";
			break;
		}
		return $content;
	}
}
?>
