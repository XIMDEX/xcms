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

class ParsingPathTo {


    private $idNode = null;
    private $pathMethod = null;
    private $channel = null;

    public function getIdNode(){
        return $this->idNode;
    }

    public function getPathMethod(){
        return $this->pathMethod;
    }

    public function getChannel(){
        return $this->channel;
    }
    
    /**
     * Get Idnode and channel from the params of the pathto method
     * @param string $pathToParams
     * @return Object This
     */
    public function parsePathTo($pathToParams, $nodeId=null)
    {
        $currentNodeServer = null;
        if ($nodeId){
            $node = new Node($nodeId);
            $currentNodeServer = $node->getServer();    
        }
        
        $result = null;
        $nodeName = "";
        $language = false;
        $channel = false;
        $nodetype = false;
        $ancestorIds = array();
        $ancestorNodeNames = array();
        $pathMethod = false;
        $uniqueElements = array("language", "channel", "nodetype", "pathmethod");
        $idNode = false;

        $params = explode(",", $pathToParams);

        //Error if there aren't any params
        if (!is_array($params) || count($params) == 0) {
            return false;
        }

        //Checking the first params. It could be number, or .number or string
        $firstParam = trim(urldecode($params[0]));
        $nodeValue = $this->getNodeValue($firstParam);

        if (is_numeric($nodeValue)) {
            $idNode = $nodeValue;
        } else {
            $nodeName = $nodeValue;
        }

        for ($i = 1, $length = count($params); $i < $length; $i++) {
            $param = trim(urldecode($params[$i]));
            $paramType = $this->inferParamType($param);
            if (!in_array($paramType, $uniqueElements) || !$$paramType) {

                $method = "get{$paramType}Value";
                if (method_exists(__CLASS__, $method)) {
                    $paramValue = $this->$method($param);
                }
                if ($paramValue) {
                    switch (strtolower($paramType)) {
                        case 'language':
                        case 'channel':
                        case 'nodetype':
                            //Double $ is ok. We build dinamically the varname.
                            $$paramType = $paramValue;
                            break;
                        case 'pathmethod':
                            $pathMethod = $paramValue;
                            break;
                        case 'node':
                            $ancestorIds[] = $paramValue;
                            break;
                        case 'nodename':
                        default:
                            $ancestorNodeNames[] = $paramValue;
                            break;
                    }
                }
            }
        }

        //At the end, idnode is required
        if (!$idNode) {
            $idNode = $this->inferIdNode($nodeName, $ancestorIds, $ancestorNodeNames, $language, $nodetype, $currentNodeServer);
        }
        $channel = $this->inferIdChannel($channel);


        $this->pathMethod = !$pathMethod ? array("relative" => false, "absolute" => false) : $pathMethod;
        $this->idNode = $idNode;
        $this->channel = $channel ? $channel : null;

        return $this;
    }

    /**
     * Get the id for a node param.
     * @param  string $param Can be an integer too. It has the node value
     * @return string Or integer. With node value.
     */
    private function getNodeValue($param)
    {
        return (is_numeric($param)) && (strpos($param, ".") === FALSE) ? $param : $this->getPathToParamValue($param, array("id"), ".");
    }

    /**
     * Get the param type from the pathto param.
     * This method is useful for every param but the first one.
     * @param  string $param A param in pathto call method.
     * @return string param Type. It can be: channel, node, language, nodetype or pathMethod.
     */
    private function inferParamType($param)
    {
        $result = null;

        /**
         * Params format:
         * * integer (32568) => channel
         * * .integer (.32568) => node
         * * @something(@php, @32568) => channel
         * * #something(#es, #32568) => language
         * * $something($image, $32568) => nodetype
         * * lang=something => language
         * * nodetype=something => nodetype
         * * absolute=something => pathMethod
         * * relative=something => pathMethod
         * * id=something => node
         * * name=something => node
         * * path=something => node
         * * true | false => pathMethod
         * * !(integer | true | false) => node
         */

        $firstCharacter = substr($param, 0, 1);
        if (is_numeric($param) && $firstCharacter != ".") {
            return "channel";
        }

        $paramValue = substr($param, 1);

        switch ($firstCharacter) {
            case '.':
                if (is_numeric($paramValue)) {
                    return "node";
                }
                break;
            case '@':
                return "channel";
                break;
            case '#':
                return "language";
                break;
            case '$':
                return "nodetype";
                break;
            default:
                $arrayParam = explode("=", $param);
                if (is_array($arrayParam) && count($arrayParam) == 2) {
                    switch (trim($arrayParam[0])) {
                        case 'channel':
                            return "channel";
                        case 'lang':
                            return "language";
                        case 'nodetype':
                            return "nodetype";
                        case 'absolute':
                        case 'relative':
                            return "pathMethod";
                        case 'id':
                            return "node";
                        case 'name':
                        case 'path':
                        default:
                            return "nodeName";
                    }
                } else {
                    if ($param == "true" || $param == "false") {
                        return "pathMethod";
                    } else return "nodeName";
                }
                break;
        }

        return $result;
    }

    /**
     * Get a channel id from a channel array. Only gets the first correct one.
     * @param string $foundedChannel Channels found in pathto function
     * @return int           idChannel or false
     */
    private function inferIdChannel($foundedChannel)
    {
        if (!$foundedChannel)
            return false;
        $channel = new Channel();
        $idChannels = $channel->find("IdChannel", "IdChannel=%s or Name=%s", array($foundedChannel, $foundedChannel), MONO);
        if ($idChannels && is_array($idChannels) && count($idChannels))
            return $idChannels[0];

        return false;
    }

    /**
     * Get the Idnode from all params found in pathto.
     * @param $nodeName
     * @param $ancestorIds
     * @param $ancestorNodeNames
     * @param $language
     * @param $nodetype
     * @return bool|unknown_type
     */
    private function inferIdNode($nodeName, $ancestorIds, $ancestorNodeNames, $language, $nodetype, $currentNodeServer)
    {
        $nodeNames = array("'$nodeName'");
        $languageCondition = $nodeNameCondition = $nodetypeCondition = " 1 ";

        if ($language) {
            $langNode = new Language();
            $arrayLanguages = $langNode->find("IdLanguage, IsoName", "idlanguage = %s or name = %s or isoname = %s", array($language, $language, $language));

            if (is_array($arrayLanguages) && count($arrayLanguages)) {
                $isoName = $arrayLanguages[0]["IsoName"];
                $idLanguage = $arrayLanguages[0]["IdLanguage"];
                $nodeNames[] = "'{$nodeName}-id{$isoName}'";
                $languageCondition = sprintf(" l.idlanguage=%s", $idLanguage);
            }
        }

        if ($nodetype) {
            $nodetypeCondition = sprintf("ispublicable and (nt.idnodetype='%s' or n.class='%snode')", $nodetype, $nodetype);
        }

        $nodePathCondition = " 1 ";
        foreach ($ancestorNodeNames as $node) {

            $nodePathCondition .= " AND n.path like '%{$node}%' ";
        }

        $fastTraverseCondition = " 1 ";
        foreach ($ancestorIds as $idnode) {
            $fastTraverseCondition .= sprintf(" AND exists (select idnode from FastTraverse ft where ft.idchild=n.idnode and ft.idnode = %s) ", $idnode);
        }

        $nodeNameCondition = sprintf(" n.name in (%s) ", implode(", ", $nodeNames));


        $sql = "select distinct n.idnode as IdNode
				from Nodes n
				left join StructuredDocuments sd on sd.iddoc=n.idnode
				inner join NodeTypes nt on nt.idnodetype=n.idnodetype
				left join Languages l on sd.idlanguage=l.idlanguage
				where
					({$nodeNameCondition}) AND
					({$languageCondition}) AND
					({$nodetypeCondition})AND
					{$nodePathCondition} AND
					{$fastTraverseCondition} ORDER BY LENGTH(n.path)";
        $dbObj = new DB();
        $dbObj->query($sql);
        if ($dbObj->numErr != 0) {
            return false;
        }
        while (!$dbObj->EOF) {
            $nodeId = $dbObj->GetValue("IdNode");
            $nodeAux = new Node($nodeId);
            if ($nodeAux->getServer() == $currentNodeServer || !$currentNodeServer)
                return $nodeId;
            $dbObj->next();
        }

        return false;
    }

    /**
     * Get a name or path from a nodename param.
     * @param  string $param Nodename or path for the selected node.
     * @return string        Or integer. With node value.
     */
    private function getNodeNameValue($param)
    {
        return $this->getPathToParamValue($param, array("node", "path"));
    }

    /**
     * Get the value for a param. This param can be in different formats.
     * @param  string $param Has the param value in different formats.
     * @param  array $paramNames Possible identificators for the param type.
     * @param  string $symbol Prefix for the param.
     * @return string            The param value.
     */
    private function getPathToParamValue($param, $paramNames, $symbol = null)
    {

        $result = $param;
        $symbolPosition = FALSE;

        if ($symbol)
            $symbolPosition = strpos($param, $symbol);

        if ($symbolPosition === 0) {
            $result = substr($param, 1);
        } else if ($symbolPosition === FALSE) { //Supposing id=int
            $arrayParam = explode("=", $param);
            if (is_array($arrayParam) && count($arrayParam) == 2) {
                if (in_array(trim($arrayParam[0]), $paramNames)) {
                    $result = trim($arrayParam[1]);
                }
            }
        }

        return $result;
    }

    private function getLanguageValue($param)
    {
        return $this->getPathToParamValue($param, array("lang"), "#");
    }

    private function getChannelValue($param)
    {
        $result = is_numeric($param) ? $param : $this->getPathToParamValue($param, array("channel"), "@");
        return $result;
    }

    private function getNodetypeValue($param)
    {
        return $this->getPathToParamValue($param, array("nodetype"), "$");
    }

    private function getPathMethodValue($param)
    {

        if ($param == "true") {
            return array("absolute" => true, "relative" => false);
        } else if ($param == "false") {
            return array("absolute" => false, "relative" => true);
        } else {
            $arrayParam = explode("=", $param);
            if (count($arrayParam) == 2) {
                if (($arrayParam[0] == "absolute" && $arrayParam[1] = "true") ||
                    ($arrayParam[0] == "relative" && $arrayParam[1] = "false")
                ) {

                    return array("absolute" => true, "relative" => false);

                } else if (($arrayParam[0] == "absolute" && $arrayParam[1] = "false") ||
                    ($arrayParam[0] == "relative" && $arrayParam[1] = "true")
                ) {

                    return array("absolute" => false, "relative" => true);

                }
            }
        }

        return false;
    }

}