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
 *  @version $Revision: 8735 $
 */

ModulesManager::file('/inc/model/Links.inc');
include_once( XIMDEX_ROOT_PATH . "/inc/helper/String.class.php" );

class FormValidation {
    
    /**
    * Check if exists a node with a specific name like child of the selected one.
    * 
    * @return boolean True if not exists this name under the current node.
    */
    public static function isUniqueName($params){
        $result = "false";
        $idnode = $params["nodeid"];
        $inputName = $params["inputName"];
        $name=$params[$inputName];
        if (!empty($params["process"]) && $params["process"] == "normalize") {
            $name = String::normalize($name);
        }
        $node = new Node($idnode);
        if($node->nodeType->get("IsFolder")==0){
            $idnode=$node->get("IdParent");
        }
        $names = $node->find("Name","idparent=%s",array($idnode),MONO);
        $names = $names? $names: array();
        $result = in_array($name, $names)? "false": "true";
        die($result);
    }
    
    /**
    * Check if exists a link with a specific url.
    * 
    * @return boolean True if not exists this url.
    */
    public static function isUniqueUrl($params){
        
        $inputName = $params["inputName"];
        $idNode = $params["nodeid"];

        $url = trim($params[$inputName]);
        $link = new Link();
        $links = $link->find("IdLink", "url=%s AND (IdLink <> %s)", array($url,$idNode), MONO);
        if (!$links)
            die (true);
        else
        die(!self::inSameProject($idNode, $links)?"true":"false");
        
    }
    
    /**
     * 
     * @param type $idNode
     * @param type $links
     * @return boolean True if some link is in the same project than idnode.
     */
    private static function inSameProject($idNode, $links){
        
        $node = new Node($idNode);
        $idProject = $node->getProject();
        foreach ($links as $idLink) {
            $linkNode = new Node($idLink);
            if ($linkNode->getProject() == $idProject)
                return true;
        }
        return false;        
    }
}
?>
