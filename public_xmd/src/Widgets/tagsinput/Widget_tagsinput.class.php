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

use Ximdex\Models\Node;
use Ximdex\Models\RelSemanticTagsNodes;
use Xmd\Widgets\WidgetAbstract;

class Widget_tagsinput extends WidgetAbstract
{
    public function process($params)
    {
        if ("true" == $params["initialize"]) {
            $relTags = new RelSemanticTagsNodes();
            $params["tags"] = json_encode($relTags->getTags($params["_enviroment"]["id_node"]));
        }
        $node = new Node($params["_enviroment"]["id_node"]);
        $params["isStructuredDocument"] = $node->nodeType->get('IsStructuredDocument');
        if (array_key_exists("editor", $params)) {
            $this->setTemplate("tagsinput_editor");
        }
        $params['namespaces'] = json_encode($this->getAllNamespaces());
        return parent::process($params);
    }

    private function getAllNamespaces()
    {
        $result = array();
        
        // Load from Xowl Service
        $namespacesArray = \Ximdex\Rest\Services\Xowl\OntologyService::getAllNamespaces();
        
        // For every namespace build an array. This will be a json object
        foreach ($namespacesArray as $namespace) {
            $array = array(
                "id" => $namespace->get("idNamespace"),
                "type" => $namespace->get("type"),
                "isSemantic" => $namespace->get("isSemantic"),
                "nemo" => $namespace->get("nemo"),
                "category" => $namespace->get("category"),
                "uri" => $namespace->get("uri")
            );
            $result[] = $array;
        }
        return $result;
    }
}