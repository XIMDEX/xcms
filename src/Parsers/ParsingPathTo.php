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
use Ximdex\NodeTypes\NodeTypeConstants;
use Ximdex\NodeTypes\XmlContainerNode;
use Ximdex\Models\StructuredDocument;

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
     */
    public function parsePathTo(string $pathToParams, int $nodeId = null, int $language = null) : bool
    {
        $msg = 'Parsing pathTo with: ' . $pathToParams;
        if ($nodeId)
            $msg .= ' and document node: ' . $nodeId;
        Logger::info($msg);
        $params = explode(",", $pathToParams);

        // Error if there aren't any params
        if (!$params)
        {
            Logger::error('Parsing pathto need any param to work');
            return false;
        }

        // Checking the first params. It could be number, or .number or string
        $nodeValue = trim(urldecode($params[0]));

        if (is_numeric($nodeValue))
        {
            // The macro has the node ID
            $id = $nodeValue;
            $targetNode = new Node($id);
            if (!$targetNode->GetID())
            {
                Logger::warning('Cannot load the node: ' . $id . ' in order to parse pathto');
                return false;
            }
        }
        else
        {
            // The macro has a Ximdex resource path
            if (!$nodeId)
            {
                Logger::error('Value for IdNode is needed to parse pathto: ' . $nodeValue);
                return false;
            }
            
            // The node of the parsed document is given, load it
            $nodeDoc = new Node($nodeId);
            if (!$nodeDoc->GetID())
            {
                Logger::error('Cannot load the param node: ' . $id . ' in order to parse pathto');
                return false;
            }

            // Obtain the path and file of the resource given in the macro
            $data = pathinfo($nodeValue);
            if (count($data) < 2)
            {
                Logger::error('Path info of the resource given in pathto is incomplete');
                return false;
            }
            $path = $data['dirname'];
            $resource = $data['basename'];
            
            // Absolute position, to the server of the document
            if (strpos($path, '/') === 0)
            {
                // Get the server node
                $idServer = $nodeDoc->getServer();
                $nodeServer = new Node($idServer);
                if (!$nodeServer->GetID())
                {
                    Logger::error('Cannot load the server node for ID: ' . $idServer);
                    return false;
                }
                
                // Sanitize path
                if (!self::sanitize_pathTo($path))
                {
                    Logger::error('Cannot sanitize the path: ' . $path . ' when parsing pathto');
                    return false;
                }
                
                // Get the target node with the resource name and path obtained from server + pathTo param
                $id = $nodeServer->GetByNameAndPath($resource, $nodeServer->GetPath() . '/' . $path);
            }
            else
            {
                // Relative to the previewed document
                $nodeSection = new Node($nodeDoc->getSection());
                if (!$nodeSection->GetID())
                {
                    Logger::error('Cannot sanitize the path: ' . $idServer . ' when parsing pathto');
                    return false;
                }
                
                // Get an array with the path entities (including ..)
                $pathData = explode('/', $path);
                
                // Get the path to the required node
                if (current($pathData) == '..')
                {
                    // Get the parent nodes of the section with node types, without the current one
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
                    
                    // While the path entitie is .. go back into the path (or server was reached)
                    while (current($pathData) == '..')
                    {
                        // Remove the current ../ entitie from the path
                        array_shift($pathData);
                        
                        // Get the ID and type for the actual parent
                        $parentNode = each($sectionParents);
                        if ($parentNode['value'] == \Ximdex\NodeTypes\NodeTypeConstants::SERVER)
                        {
                            // Server node has been reached
                            break;
                        }
                    }
                    
                    // Get the node for reached section or server
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
                
                // Sanitize path
                $path = implode('/', $pathData);
                if (!self::sanitize_pathTo($path))
                {
                    Logger::error('Cannot sanitize the path: ' . $idServer . ' when parsing pathto');
                    return false;
                }
                
                // Merge section path with cleaned path given in pathTo resource path param
                $path = $nodeSection->GetPath() . '/' . $path;
                
                // Load de node ID for the path and resource obtained
                $id = $nodeDoc->GetByNameAndPath($resource, $path);
            }
            if (!$id)
            {
                Logger::error('Cannot obtain the node for resource: ' . $nodeValue);
                return false;
            }
            $id = $id[0]['IdNode'];
            $node = new Node($id);
            if (!$node->GetID()) {
                Logger::error('Cannot load the node with ID:' . $id);
                return false;
            }
            if ($node->GetNodeType() == NodeTypeConstants::XML_CONTAINER or $node->GetNodeType() == NodeTypeConstants::HTML_CONTAINER) {
                
                // Load the document language version
                if (!$language) {
                    $strDoc = new StructuredDocument($nodeId);
                    if (!$strDoc->GetID()) {
                        Logger::error('Cannot load the structured document with ID: ' . $nodeId);
                        return false;
                    }
                    if (!$strDoc->GetLanguage()) {
                        Logger::error('The structured document with ID: ' . $nodeId . ' has not any language value');
                        return false;
                    }
                    $language = $strDoc->GetLanguage();
                }
                $docContainer = new XmlContainerNode($id);
                $id = $docContainer->GetChildByLang($language);
                if (!$id) {
                    Logger::error('Cannot load the language version for contaniner: ' . $nodeId . ' and language: ' . $language);
                    return false;
                }
                $name = $strDoc->GetName();
            }
            else {
                $name = $node->GetNodeName();
            }
            Logger::info('ParsingPathTo: Obtained node with ID: ' . $id . ' and name: ' . $name, true);
        }
        
        $pathMethod = false;
        $this->pathMethod = !$pathMethod ? array("relative" => false, "absolute" => false) : $pathMethod;
        $this->idNode = $id;
        
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