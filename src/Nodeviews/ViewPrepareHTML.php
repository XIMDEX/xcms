<?php

/**
 *  \details &copy; 2018 Open Ximdex Evolution SL [http://www.ximdex.org]
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
use Ximdex\NodeTypes\HTMLDocumentNode;
use Ximdex\Properties\InheritedPropertiesManager;
use Ximdex\Models\Channel;
use Ximdex\Models\ProgrammingCode;

class ViewPrepareHTML extends AbstractView implements IView
{
    const MACRO_CODE = '/@@@GMximdex.exec\(([a-zA-Z0-9_]+),?(.*)\)@@@/m';

    private $nodeID;
    private $channelID;

    /**
     * {@inheritdoc}
     * @see \Ximdex\Nodeviews\AbstractView::transform()
     */
    public function transform($idVersion = NULL, $pointer = NULL, $args = NULL)
    {
        if (!isset($args['NODEID']) || empty($args['NODEID'])) {
            Logger::error('Argument nodeId not found in ViewPrepareHTML');
            return false;
        }
        $this->nodeID = $args['NODEID'];

        // Channel
        if (isset($args['CHANNEL']) and $args['CHANNEL']) {
            $channel = new Channel($args['CHANNEL']);
            if (!$channel->GetID()) {
                Logger::error('Channel not found for ID: ' . $args['CHANNEL']);
                return false;
            }
            $this->channelID = $args['CHANNEL'];
            if ($channel->getRenderType()) {
                $mode = $channel->getRenderType();
            } else {
                $mode = HTMLDocumentNode::MODE_STATIC;
            }
        } else {
            $this->channelID = null;
            $mode = HTMLDocumentNode::MODE_STATIC;
        }

        // Get the content
        $content = $this->retrieveContent($pointer);
        $document = ($content !== false) ? HTMLDocumentNode::renderHTMLDocument($this->nodeID, $content, $this->channelID, $mode) : false;

        // Process macros
        if ($document !== false) {
            $document = preg_replace_callback(self::MACRO_CODE, array(
                $this,
                'getCodeTranslation'
            ), $document);
            $document = str_replace('<ximeol>', PHP_EOL, $document);
        }

        // Return the pointer to the transformed content
        return $this->storeTmpContent($document);
    }

    /**
     * @param array $matches
     * @return string
     */
    private function getCodeTranslation(array $matches): string
    {
        if (!$this->channelID) {

            // Get channel if there is not one specified
            $properties = InheritedPropertiesManager::getValues($this->nodeID, true);
            if (!isset($properties['Channel']) or !$properties['Channel']) {
                Logger::warning('There is not a channel defined for the document with ID: ' . $this->nodeID);
                return '';
            }
            $channelProp = current($properties['Channel']);
            $channelID = $channelProp['Id'];
        } else {
            $channelID = $this->channelID;
        }
        $channel = new Channel($channelID);
        if (!$channel->GetID()) {
            Logger::error('Cannot load the channel with ID: ' . $channelID);
            return '';
        }
        if (!$channel->getIdLanguage()) {
            Logger::warning('There is not a programming language defined for the channel ' . $channel->GetName());
            return '';
        }

        // Get command function
        $data = explode(',', $matches[1]);
        if (!$data or !$data[0]) {
            Logger::error('Command not defined to generate the code');
            return '';
        }
        $function = trim($data[0]);

        // Generate the command in the specified language
        $programCode = new ProgrammingCode();
        $programCode->setIdLanguage($channel->getIdLanguage());
        $programCode->setIdCommand($function);
        if (isset($matches[2]) and $matches[2]) {
            $params = explode('ximparam=', $matches[2]);
            if ($params and !trim($params[0])) {
                unset($params[0]);
            }
            foreach ($params as & $param) {
                $param = trim(trim($param), ',');
            }
        } else {
            $params = array();
        }
        if (!$programCode->translate($params)) {
            Logger::error('Cannot translate the code for the ' . $function . ' command in the ' . strtoupper($channel->getIdLanguage())
                . ' language: ' . $programCode->messages->messages[0]['message']);
            return '';
        }
        return $programCode->getCode();
    }
}
