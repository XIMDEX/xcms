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

namespace Ximdex\Nodeviews;

use Ximdex\Logger;
use Ximdex\Models\Channel;
use Ximdex\Models\Node;
use Ximdex\Parsers\ParsingPathTo;
use Ximdex\Runtime\App;

class ViewFilterMacrosPreview extends ViewFilterMacros implements IView
{
    private $_nodeTypeName = NULL;
    private $mode = NULL;

    /**
     * Initialize params from transformation args
     * @param array $args Arguments for transformation
     * @param int $idVersion
     * @return boolean True if everything is allright.
     */
    protected function initializeParams($args, $idVersion)
    {
        $this->mode = (isset($args['MODE']) && $args['MODE'] == 'dinamic') ? 'dinamic' : 'static';

        if (!$this->_setIdSection($args))
            return NULL;
        
        return parent::initializeParams($args, $idVersion);
    }

    /**
     * Load the node param from an idVersion
     * @param int $idVersion Version id
     * @param array $args
     * @return boolean True if exists node for selected version or the current node.
     */
    protected function _setNode($idVersion = NULL, $args = NULL)
    {
        if (is_null($idVersion)) {
            
            if ($this->idNode)
                return parent::_setNode();
            elseif (array_key_exists('NODETYPENAME', $args))
                $this->_nodeTypeName = $args['NODETYPENAME'];
        } else {
            
            return parent::_setNode($idVersion);
        }
        return true;
    }

    /**
     * Load the section id from the args array.
     * @param array $args Transformation args.
     * @return boolean True if exits the section.
     */
    private function _setIdSection($args = array())
    {
        if (array_key_exists('SECTION', $args))
            $this->_idSection = $args['SECTION'];

        // Check Params:
        if (!isset($this->_idSection) || !($this->_idSection > 0)) {
            
            Logger::error('VIEW FILTERMACROSPREVIEW: Node section not specified: ' . $args['NODENAME']);
            return NULL;
        }
        
        return true;
    }

    private function getSectionPath($matches)
    {
        //Getting section from parent function.
        $section = $this->getSectionNode($matches[1]);
        if (!$section) {
            
            return App::getValue('EmptyHrefCode');
        }
        $idTargetChannel = isset($matches[2]) ? $matches[2] : NULL;
        $dotdot = str_repeat('../', $this->_depth - 2);
        return $dotdot . $section->GetPublishedPath($idTargetChannel, true);
    }

    private function getdotdotpath($matches)
    {
        $section = new Node($this->_idSection);
        $sectionPath = $section->class->GetNodeURL() . "/";
        $targetPath = $matches[1] . '?token=' . uniqid();
        $dotdot = str_repeat('../', $this->_depth - 2);
        return $sectionPath . $dotdot . $targetPath;
    }


    private function getLinkPath($matches)
    {
        //Get parentesis content
        $pathToParams = $matches[1];
        
        // Link target-node
        $parserPathTo = new ParsingPathTo();
        if (!$parserPathTo->parsePathTo($pathToParams, $this->idNode))
        {
            Logger::error('Parse PathTo is not working for: ' . $pathToParams);
            return false;
        }

        $res["idNode"] = $parserPathTo->getIdNode();
        $res["pathMethod"] = $parserPathTo->getPathMethod();
        $res["channel"] = $parserPathTo->getChannel();

        $idNode = $res["idNode"];
        $targetNode = new Node($idNode);
        if (!$targetNode->get('IdNode'))
            return '';

        if ($this->_node && !$this->_node->get('IdNode'))
            return '';

        $isStructuredDocument = $targetNode->nodeType->GetIsStructuredDocument();

        if ($res["channel"])
            $idTargetChannel = $res["channel"];
        elseif ($this->idChannel)
            $idTargetChannel = $this->idChannel;
        else
            $idTargetChannel = null;
        $targetChannelNode = new Channel($idTargetChannel);

        // External Link
        if ($targetNode->nodeType->get('Name') == 'Link')
            return $targetNode->class->GetUrl();

        if ($isStructuredDocument) {
            
            if ($this->mode == 'dinamic')
            {
                return "javascript:parent.loadDivsPreview(" . $idNode . ")";
            }
            else {
                
                $query = App::get('\Ximdex\Utils\QueryManager');
                return $query->getPage() . $query->buildWith(array('nodeid' => $idNode, 'channelid' => $idTargetChannel));
            }
        }
        else
        {
            // generate the URL to the filemapper action
            $url = App::getValue('UrlRoot') . '/?expresion=' . (($idNode) ? $idNode : $pathToParams) 
                    . '&action=filemapper&method=nodeFromExpresion&token=' . uniqid();
            return $url;
        }
    }

    private function getLinkPathAbs($matches)
    {
        return $this->getLinkPath($matches);
    }
}