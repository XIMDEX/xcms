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

class Action_filepreview extends ActionAbstract {

	// Main method: shows initial form for a single file
   	function index () {
		$this->response->set('Cache-Control', 
			array('no-store, no-cache, must-revalidate', 'post-check=0, pre-check=0'));
		$this->response->set('Pragma', 'no-cache');
		
    		$idNode = $this->request->getParam('nodeid');
    	
    		$version = $this->request->getParam('version');
    		$subVersion = $this->request->getParam('sub_version');
    	
    		if (is_numeric($version) && is_numeric($subVersion)) {
    			$dataFactory = new DataFactory($idNode);
    			$selectedVersion = $dataFactory->getVersionId($version, $subVersion);
    		} else {
	    		$dataFactory = new DataFactory($idNode);
	    		$selectedVersion = $dataFactory->GetLastVersionId();
    		}
    	
    		$version = new Version($selectedVersion);
    		$hash = $version->get('File');
    	
		$values = array('id_node' => $idNode,
				'path' => Config::getValue('UrlRoot') . '/data/files/' . $hash);
		$this->render($values, null, 'default-3.0.tpl');
    	}
  
	/**
     	* <p>Show all images contained in a node</p>
     	* 
     	*/
    	function showAll() {
	
    		$idNode = $this->request->getParam('nodeid');
    	
        	$node = new Node($idNode);
        	if(!($node->get('IdNode'))> 0 || ($node->get('IdNodeType') != 5016 && $node->get('IdNodeType') != 5017) ) {
        		$message = _("Forbidden access");
            		$this->render(array('mesg' => $message), null, 'default-3.0.tpl');
            		return;
        	}
        
        	/* Gets all child nodes of type image (nodetype 5040) of this node */
        	$nodes = $node->GetChildren(5040);
        	$imageNodes = array();
        	$imagePath = Config::getValue('UrlRoot').Config::getValue('FileRoot');
		$nodePath = Config::getValue('UrlRoot').Config::getValue('NodeRoot');
        	if(count($nodes) > 0) {
            		foreach($nodes as $idNode) {
                		$n = new Node($idNode);
                		if(!($n->get('IdNode') > 0))
                    			continue;
                
                		$dataFactory = new DataFactory($idNode);
	    			$selectedVersion = $dataFactory->GetLastVersionId();
                
                		$version = new Version($selectedVersion);
				$hash = $version->get('File');

				$filepath = XIMDEX_ROOT_PATH.Config::getValue('FileRoot').DIRECTORY_SEPARATOR.$hash;
                		$imageInfo = getimagesize($filepath);
                		$width = $imageInfo[0];
                		$height = $imageInfo[1];
                		$mime = $imageInfo['mime'];
				array_push($imageNodes,array('name' => $n->GetNodeName(),
                                         'original_path' => $nodePath.str_replace('/Ximdex/Projects','',$n->GetPath()),
                                         'src' => $imagePath.'/'.$hash,
                                         'width' => $width, 
                                         'height' => $height,
                                         'mime' => $mime,
                                         'dimensions' => $width." x ".$height,
                                         'size' => $this->humanReadableFilesize(filesize($filepath)),
                                         'idnode' => $n->get('IdNode'),                                                                                                 )       
                        	);
            		}
            
            		$this->addCss('/actions/filepreview/resources/css/showAll.css');
            		$this->addJs('/actions/filepreview/resources/js/showAll.js');		
            		$this->addJs('/actions/filepreview/resources/js/gallerizer.js');

            		$values = array('imageNodes' => $imageNodes);
            		$this->render($values, null, 'default-3.0.tpl');

        	}
                else {
            		$message = _("No images found in this folder");
            		$this->render(array('mesg' => $message), null, 'default-3.0.tpl');
        	}
    	}

	private function humanReadableFilesize($size) {

                $mod = 1024;

                $units = explode(' ','B KB MB GB TB PB');
                for ($i = 0; $size > $mod; $i++) {
                        $size /= $mod;
                }

                return round($size, 2) . ' ' . $units[$i];
        }
}
?>
