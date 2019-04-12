<?php

/**
 *  \details &copy; 2019 Open Ximdex Evolution SL [http://www.ximdex.org]
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

use Ximdex\Models\Link;
use Ximdex\Models\Node;

class FormValidation
{    
    /**
     * Check if exists a node with a specific name like child of the selected one
     * 
     * @param array $params
     * @param bool $returnText
     * @throws Exception
     * @return bool
     */
    public static function isUniqueName(array $params, bool $returnText = true) : bool
    {       
        if (! isset($params['nodeid']) or ! $params['nodeid']) {
            throw new Exception('Node Id is needed for unique name operation');
        }
        $idnode = $params['nodeid'];
        $inputName = $params['inputName'];
        $name = trim($params[$inputName]);
        if (! empty($params['process']) && $params['process'] == 'normalize') {
            $name = \Ximdex\Utils\Strings::normalize($name);
        }
        $node = new Node($idnode);
        if (! $node->nodeType->get('IsFolder')){
            $parentId = $node->get('IdParent');
        } else {
            $parentId = $node->getID();
        }
        $names = $node->find('Name', 'IdParent = %s AND Name LIKE %s AND IdNode <> %s',  [$parentId, $name, $idnode], MONO);
        if ($returnText) {
            die($names ? 'false' : 'true');
        }
        return ! $names;
    }
    
    /**
     * Check if exists a link with a specific url
     * 
     * @param array $params
     * @param bool $returnText
     * @throws Exception
     * @return bool
     */
    public static function isUniqueUrl(array $params, bool $returnText = true) : bool
    {
        if (! isset($params['nodeid']) or ! $params['nodeid']) {
            throw new Exception('Node ID is needed for unique URL operation');
        }
        $inputName = $params['inputName'];
        $idNode = $params['nodeid'];
        $url = trim($params[$inputName]);
        $link = new Link();
        $links = $link->find('IdLink', 'url = %s AND IdLink <> %s', array($url, $idNode), MONO);
        if (! $links) {
            if ($returnText) {
                die('true');
            }
            return true;
        }
        $res = ! self::inSameProject($idNode, $links);
        if ($returnText) {
            die($res ? 'false' : 'true');
        }
        return ! $res;
    }
    
    /**
     * @param int $idNode
     * @param array $links
     * @return boolean True if some link is in the same project than idnode
     */
    private static function inSameProject(int $idNode, array $links) : bool
    {    
        $node = new Node($idNode);
        $idProject = $node->getProject();
        foreach ($links as $idLink) {
            $linkNode = new Node($idLink);
            if ($linkNode->getProject() == $idProject) {
                return true;
            }
        }
        return false;        
    }
}
