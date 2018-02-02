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
 * @author Ximdex DevTeam <dev@ximdex.com>
 * @version $Revision$
 */

namespace Ximdex\Parsers;

use Ximdex\Logger;
use Ximdex\Models\FastTraverse;
use Ximdex\Models\Node;

class ParsingPathTo
{
    private $idNode = null;
    private $pathMethod = null;
    private $channel = null;

    public function getIdNode()
    {
        return $this->idNode;
    }

    public function getPathMethod()
    {
        return $this->pathMethod;
    }

    public function getChannel()
    {
        return $this->channel;
    }

    /**
     * Get Idnode and channel from the params of the pathto method
     * @param string $pathToParams
     * @param int $nodeId
     * @param int $language
     * @return bool
     * @return Object This
     */
    public function parsePathTo(string $pathToParams, int $nodeId = null, int $language = null) : bool
    {
        Logger::info('Parsing pathTo with: ' . $pathToParams . ' and document node: ' . $nodeId);
        $params = explode(",", $pathToParams);

        //Error if there aren't any params
        if (!$params)
        {
            Logger::error('Parsing pathto need any param to work');
            return false;
        }

        //Checking the first params. It could be number, or .number or string
        $nodeValue = trim(urldecode($params[0]));

        if (is_numeric($nodeValue))
        {
            // the macro has the node ID
            $idNode = $nodeValue;
            $targetNode = new Node($idNode);
            if (!$targetNode->GetID())
            {
                Logger::error('Cannot load the node: ' . $idNode . ' in order to parse pathto');
                return false;
            }
        }
        else
        {
            // the macro has a Ximdex resource path
            if (!$nodeId)
            {
                Logger::error('Value for IdNode is needed to parse pathto: ' . $nodeValue);
                return false;
            }

            //TODO ajlucena: language ?
            
            // the node of the parsed document is given, load it
            $nodeDoc = new Node($nodeId);
            if (!$nodeDoc->GetID())
            {
                Logger::error('Cannot load the param node: ' . $idNode . ' in order to parse pathto');
                return false;
            }

            // obtain the path and file of the resource given in the macro
            $data = pathinfo($nodeValue);
            if (count($data) < 2)
            {
                Logger::error('Path info of the resource given in pathto is incomplete');
                return false;
            }
            $path = $data['dirname'];
            $resource = $data['basename'];
            
            // absolute position, to the server of the document
            if (strpos($path, '/') === 0)
            {
                // get the server node
                $idServer = $nodeDoc->getServer();
                $nodeServer = new Node($idServer);
                if (!$nodeServer->GetID())
                {
                    Logger::error('Cannot load the server node for ID: ' . $idServer);
                    return false;
                }
                
                // sanitize path
                if (!self::sanitize_pathTo($path))
                {
                    Logger::error('Cannot sanitize the path: ' . $path . ' when parsing pathto');
                    return false;
                }
                
                // get the target node with the resource name and path obtained from server + pathTo param
                $idNode = $nodeServer->GetByNameAndPath($resource, $nodeServer->GetPath() . '/' . $path);
            }
            else
            {
                // relative to the previewed document
                $nodeSection = new Node($nodeDoc->getSection());
                if (!$nodeSection->GetID())
                {
                    Logger::error('Cannot sanitize the path: ' . $idServer . ' when parsing pathto');
                    return false;
                }
                
                // get an array with the path entities (including ..)
                $pathData = explode('/', $path);
                
                // get the path to the required node
                if (current($pathData) == '..')
                {
                    // get the parent nodes of the section with node types, without the current one
                    $sectionParents = FastTraverse::get_parents($nodeSection->GetID(), 'IdNodeType', 'IdNode');
                    if (!$sectionParents)
                    {
                        Logger::error('Cannot load parents node for node: ' . $nodeSection->GetID() . ' (' . $nodeSection->GetNodeName() .  ')');
                        return false;
                    }
                    if (next($sectionParents) === false)
                    {
                        Logger('Cannot load previous parent for node: ' . $nodeSection->GetID() . ' (' . $nodeSection->GetNodeName() .  ')');
                        return false;
                    }
                    
                    // while the path entitie is .. go back into the path (or server was reached)
                    while (current($pathData) == '..')
                    {
                        // remove the current ../ entitie from the path
                        array_shift($pathData);
                        
                        // get the ID and type for the actual parent
                        $parentNode = each($sectionParents);
                        if ($parentNode['value'] == \Ximdex\NodeTypes\NodeTypeConstants::SERVER)
                        {
                            // server node has been reached
                            break;
                        }
                    }
                    
                    // get the node for reached section or server
                    if (!isset($parentNode))
                    {
                        Logger::error('Cannot load the parent node for path: ' . $path . ' in pathto macro');
                        return false;
                    }
                    $nodeSection = new Node($parentNode['key']);
                    if (!$nodeSection->GetID())
                    {
                        Logger::error('Cannot load the node section or server with ID: ' . $parentNode['key']);
                        return false;
                    }
                }
                
                // sanitize path
                $path = implode('/', $pathData);
                if (!self::sanitize_pathTo($path))
                {
                    Logger::error('Cannot sanitize the path: ' . $idServer . ' when parsing pathto');
                    return false;
                }
                
                // merge section path with cleaned path given in pathTo resource path param
                $path = $nodeSection->GetPath() . '/' . $path;
                
                // load de node ID for the path and resource obtained
                $idNode = $nodeDoc->GetByNameAndPath($resource, $path);
            }
            if (!$idNode)
            {
                Logger::error('Cannot obtain the node for resource: ' . $nodeValue);
                return false;
            }
            $idNode = $idNode[0]['IdNode'];
            Logger::info('Obtained IdNode: ' . $idNode);
        }
        
        $pathMethod = false;
        $this->pathMethod = !$pathMethod ? array("relative" => false, "absolute" => false) : $pathMethod;
        $this->idNode = $idNode;
        
        //TODO ajlucena: channel ?
        $channel = false;
        $this->channel = $channel ? $channel : null;

        return true;
    }

    /**
     * Return the given path without ../ and ./ and extra /
     * @param string $path
     * @return bool
     */
    private static function sanitize_pathTo(string & $path) : bool
    {
        $path = str_replace(' ', '', $path);
        $pathData = explode('/', $path);
        $pathData = array_diff($pathData, ['..', '.', '']);
        $path = implode('/', $pathData);
        return true;
    }
}