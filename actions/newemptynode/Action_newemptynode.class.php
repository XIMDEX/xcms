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

use Ximdex\Models\Node;
use Ximdex\Models\NodeAllowedContent;
use Ximdex\Models\NodeType;
use Ximdex\MVC\ActionAbstract;

if (!defined('XIMDEX_ROOT_PATH')) {
    define ('XIMDEX_ROOT_PATH', realpath(dirname(__FILE__)) . '/../../');
}
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
			$content = $this->getDefaultContent($nodetype,$name);
			if ($file->SetContent($content) === false)
			{
			    if ($file->msgErr)
			        $this->messages->add(sprintf(_('The operation has failed: %s'), $file->msgErr), MSG_TYPE_ERROR);
			    else
			        $this->messages->mergeMessages($file->messages);
			}
			else
			{
                $this->messages->add(sprintf('%s'._(' has been successfully created'), $name), MSG_TYPE_NOTICE);
                if ($name == 'docxap')
                {
                    $xsltNode = new xsltnode(new Node($idfile));
                    $xsltNode->add_parents_includesTemplates();
                }
			}
        } else {
            $this->messages->mergeMessages($file->messages);
        }
		$values = array('messages' => $this->messages->messages, 'parentID' => $parentId, 'nodeID' => $idfile);
		
		$this->sendJSON($values);
	}
	
	/**
	 * return each content depending on the nodetype passed
	 * @param integer $nt
	 * @param string $name
	 * @return string
	 */
	function getDefaultContent($nt, $name)
	{
		switch ($nt)
		{
		    case \Ximdex\Services\NodeType::TEXT_FILE:
		        $content = '<<< DELETE \nTHIS\n CONTENT >>>';
		        break;
		    
		    case \Ximdex\Services\NodeType::CSS_FILE:
		        $content = '/* CSS File: ' . $name . '. Write your rules here. */\n\n * {}';
		        break;

		    case \Ximdex\Services\NodeType::XSL_TEMPLATE:
		        $content = "<?xml version='1.0' encoding='UTF-8'?>";
		        $content .= "\n<xsl:stylesheet xmlns:xsl='http://www.w3.org/1999/XSL/Transform' version='1.0'>";
		        if ($name != 'templates_include')
		        {
		            $content .= "\n\t<xsl:template name='" . $name . "' match='" . $name . "'>";
		            if ($name != 'docxap')
		            {
		                $content .= "\n\t\t<!-- Insert your code here -->";
                        $content .= "\n\t\t<!-- Remember to use the <xsl:apply-templates /> tag to include content of another templates -->";
		            }
		            $content .= "\n\t</xsl:template>";
                }
		        $content .= '</xsl:stylesheet>';
		        break;

            case \Ximdex\Services\NodeType::RNG_VISUAL_TEMPLATE:
                $content = "<?xml version='1.0' encoding='UTF-8' ?>\n";
                $content .= "<grammar xmlns='http://relaxng.org/ns/structure/1.0' xmlns:xim='http://ximdex.com/schema/1.0'>";
                $content .= "\n\t<!-- Create your own grammar here -->";
                $content .= "\n\t<!-- Need help? Visit: http://relaxng.org/tutorial-20011203.html -->";
                $content .= "\n</grammar>";
				break;

		    case \Ximdex\Services\NodeType::NODE_HT:
				$content = "<html>\n\t<head>\n\t</head>\n\t<body>\n\t</body>\n</html>";
				break;
		}
		return $content;
	}
}