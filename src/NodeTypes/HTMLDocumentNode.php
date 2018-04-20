<?php

/**
 * \details &copy; 2011 Open Ximdex Evolution SL [http://www.ximdex.org]
 *
 * Ximdex a Semantic Content Management System (CMS)
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * See the Affero GNU General Public License for more details.
 * You should have received a copy of the Affero GNU General Public License
 * version 3 along with Ximdex (see LICENSE file).
 *
 * If not, visit http://gnu.org/licenses/agpl-3.0.html.
 *
 * @author Ximdex DevTeam <dev@ximdex.com>
 * @version $Revision$
 */

namespace Ximdex\NodeTypes;

use Ximdex\Logger;
use Ximdex\Models\RelTagsNodes;
use Ximdex\Models\SectionType;
use Ximdex\Models\StructuredDocument;
use SimpleXMLElement;
use Ximdex\Runtime\App;

class HTMLDocumentNode extends AbstractStructuredDocument
{

    const DOCXIF = 'docxif';

    /**
     * This mode use a controller to resolve the requests
     */
    const MODE_DYNAMIC = 'dynamic';

    /**
     * This mode create a physical files but use include functionality to dependencies
     */
    const MODE_INCLUDE = 'include';

    /**
     * This mode create a physical files with node data and dependencies data
     */
    const MODE_STATIC = 'static';

    /**
     * This mode create a physical files with node data and dependencies data
     */
    const MODE_INDEX = 'index';


    /**
     * This flag indicate content node from HTML Document
     */
    const CONTENT_DOCUMENT = 'content';

    /**
     * This flag indicate include node from HTML Document
     */
    const INCLUDE_DOCUMENT = 'include';

    /**
     *
     * @param $docId
     *
     * @return null || array
     */
    public static function getNodesHTMLDocument(int $docId)
    {
        $nodes = false;

        if ($docId == null) {
            return false;
        }

        $doc = new StructuredDocument($docId);

        if ($doc->GetID()) {
            try {
                $nodes = static::getDocumentNodes($doc, [], true);
            } catch (\Exception $ex) {
                Logger::error('Failed to get nodes from HTMLDocumentNode class');
            }
        }

        return $nodes;
    }

    /**
     *
     * @param $doc StructuredDocument
     * @param $nodes array
     * @param $isCurrentNode bool
     *
     * @return array
     */
    public static function getDocumentNodes($doc, $nodes = [], $isCurrentNode = false)
    {
        // Layout
        $layout = $doc->getLayout();
        if ($layout && $layout->GetContent()) {
            $layout = json_decode($layout->GetContent(), true);
            if (isset($layout['template'])) {
                foreach ($layout['template'] as $templ) {
                    if (strcmp(key($templ), static::CONTENT_DOCUMENT) == 0) {
                        $data = static::getNodeData($doc, $templ[key($templ)], $layout, $isCurrentNode);
                        $nodes['xe_' . $data['id']] = $data; // Todo JS order numbers key, change it by array
                    } else {
                        $include = $doc->getInclude($templ[key($templ)]);
                        if ($include && $include->GetID()) {
                            $nodes = static::getDocumentNodes($include, $nodes);
                        } else {
                            $nodes['not_found_' . count($nodes)] = [
                                'title' => key($templ),
                                'content' => '<div>NODE EMPTY</div>'
                            ];
                            if ($doc->messages->messages[0]) {
                                Logger::error($doc->messages->messages[0]['message']);
                            }
                        }
                    }
                }
            }
        }

        return $nodes;
    }

    /**
     *
     * @param $doc StructuredDocument
     * @param $layout StructuredDocument
     * @param $isCurrentNode bool
     *
     * @return array
     */
    public static function getNodeData($doc, $sections, $layout, $isCurrentNode)
    {
        $properties = [];
        $properties['type'] = $isCurrentNode ? static::CONTENT_DOCUMENT : static::INCLUDE_DOCUMENT;
        $extraData['css'] = isset($layout['css']) ? $layout['css'] : [];
        $extraData['js'] = isset($layout['js']) ? $layout['js'] : [];

        $schemas = [];
        $content = '';

        // First get all main schemas
        foreach ($sections as $section => $data) {
            $schemas[$section] = static::getSchemaFromComponent($doc, $section, $data, $extraData);
            if (isset($schemas[$section]) && $schemas[$section]['view'] != null) {
                $content .= $schemas[$section]['view'];
            } else {
                $content .= '<div>EMPTY COMPONENT</div>';
            }
        }

        // Last get dependent schemas
        foreach ($schemas as $section => $data) {
            if (isset($data['sections'])) {
                $schemas = static::getChildSchemasBySections($doc, $schemas, $data['sections'], $extraData);
            }
        }

        if (!is_null($doc) && $doc->GetContent()) {
            $content = $doc->GetContent();
        }

        // Properties
        $properties['id'] = $doc->GetID();
        $properties['content'] = !is_null($content) ? $content : '';
        $properties['title'] = $doc->GetName();
        $properties['attributes'] = [];
        $properties['schema'] = $schemas;
        $properties['css'] = array_unique($extraData['css']);
        $properties['js'] = array_unique($extraData['js']);

        return $properties;
    }

    /**
     * Get string for render document
     *
     * @param int $docId
     * @param string $content
     * @param string $mode
     * @return string || bool
     */
    public static function renderHTMLDocument(int $docId, string $content = null, string $channel = null, string $mode = null)
    {
        $render = '';
        $css = $js = [];
        $name = '';
        if ($mode === null) {
            $mode = HTMLDocumentNode::MODE_STATIC;
        }

        if ($docId == null || !in_array($mode, [
                static::MODE_DYNAMIC,
                static::MODE_INCLUDE,
                static::MODE_STATIC,
                static::MODE_INDEX
            ])) {
            return false;
        }
        $docHTML = static::getNodesHTMLDocument($docId);

        if ($docHTML === false) {
            return false;
        }

        if (strcmp($mode, static::MODE_DYNAMIC) == 0 || strcmp($mode, static::MODE_INDEX) == 0) {
            foreach ($docHTML as $node) {
                if ($node['type'] == static::CONTENT_DOCUMENT) {
                    $render .= !is_null($content) ? $content : $node['content'];
                }
            }
            if (strcmp($mode, static::MODE_INDEX) == 0) {
                $render = static::createXIF($docId, $content, $channel);
            }
        } else if (strcmp($mode, static::MODE_INCLUDE) == 0) {
            $body = '';
            $name = '';
            foreach ($docHTML as $node) {
                $css = isset($node['css']) ? array_merge($css, $node['css']) : $css;
                $js = isset($node['css']) ? array_merge($js, $node['js']) : $js;
                if (isset($node['type']) and $node['type'] == static::CONTENT_DOCUMENT) {
                    $body .= !is_null($content) ? $content : $node['content'];
                    $name = $node['title'];
                } else {
                    if (isset($node['id']) and $node['id']) {
                        $body .= '@@@RMximdex.code(include, @@@RMximdex.pathto(' . $node['id'] . ')@@@)@@@';
                    }
                }
            }
            $render = $body;

            // TODO
            $pos = strpos($name, "_");
            if ($pos !== 0) {
                $render = static::createBasicHTMLTemplate($body, $css, $js);
            }
        } else {
            $body = '';
            foreach ($docHTML as $node) {
                $css = isset($node['css']) ? array_merge($css, $node['css']) : $css;
                $js = isset($node['css']) ? array_merge($js, $node['js']) : $js;
                if (isset($node['type']) && $node['type'] == static::CONTENT_DOCUMENT) {
                    $body .= !is_null($content) ? $content : $node['content'];
                    $name = $node['title'];
                } else {
                    $body .= isset($node['content']) ? $node['content'] : '';
                }
            }

            $pos = strpos($name, "_");
            if ($pos !== 0) {
                $render = static::createBasicHTMLTemplate($body, $css, $js);
            }

            $render = static::createBasicHTMLTemplate($body, $css, $js);
        }

        return $render;
    }

    /**
     * ******************************************* AUX METHODS ********************************************
     */

    /**
     *
     * @param
     *            $doc
     * @param
     *            $schemas
     * @param
     *            $sections
     * @param $extraData array
     *
     * @return array
     */
    private static function getChildSchemasBySections($doc, $schemas, $sections, &$extraData)
    {
        foreach ($sections as $section => $data) {
            if (!array_key_exists($section, $schemas)) {
                $schemas = static::getChildSchemasBySection($doc, $section, $data, $schemas, $extraData);
            }
        }
        return $schemas;
    }

    /**
     *
     * @param $doc StructuredDocument
     * @param $section string
     * @param $schemas array
     * @param $data array
     * @param $extraData array
     *
     * @return array
     */
    private static function getChildSchemasBySection($doc, $section, $data, $schemas, &$extraData): array
    {
        $schema = static::getSchemaFromComponent($doc, $section, $data, $extraData);
        if ($schema != null) {
            $schemas[$section] = $schema;
            if (array_key_exists('sections', $schemas[$section])) {
                $schemas = static::getChildSchemasBySections($doc, $schemas, $schemas[$section]['sections'], $extraData);
            }
        }
        return $schemas;
    }

    /**
     *
     * @param $doc StructuredDocument
     * @param $compName string
     * @param $data array
     *
     * @return array
     */
    private static function getSchemaFromComponent($doc, $compName, $data, &$extraData)
    {
        $schema = null;
        $comp = $doc->getComponent($compName);
        if ($comp && $comp->GetContent()) {

            $jsonComp = json_decode($comp->GetContent(), true);
            $schema = array_merge($jsonComp['schema'], $data);
            $schema['name'] = $compName;
            $view = $doc->getView($jsonComp['schema']['view']);

            if ($view && $view->GetContent()) {
                $schema['view'] = $view->GetContent();
            } else {
                $schema['view'] = '';
            }

            $extraData['css'] = isset($jsonComp['css']) ? array_merge($extraData['css'], $jsonComp['css']) : $extraData['css'];
            $extraData['js'] = isset($jsonComp['js']) ? array_merge($extraData['js'], $jsonComp['js']) : $extraData['js'];
        }
        return $schema;
    }

    /**
     * Create a basic html template with header and body passed by strings
     *
     * @param $css
     * @param $js
     * @param $body
     * @return string
     */
    private static function createBasicHTMLTemplate($body, $css, $js)
    {
        $header = '';
        foreach ($css as $file) {
            $header .= "<link rel='stylesheet' type='text/css' href='@@@RMximdex.pathto($file)@@@'>" . PHP_EOL;
        }
        foreach ($js as $file) {
            $header .= "<script type='text/javascript' src='@@@RMximdex.pathto($file)@@@'></script>" . PHP_EOL;
        }
        $html = '<!DOCTYPE html>' . PHP_EOL;
        $html .= '<html><head><meta charset="UTF-8">';
        $html .= '<meta name="viewport" content="width=device-width, initial-scale=1.0">' . PHP_EOL;
        $html .= '<meta http-equiv="X-UA-Compatible" content="ie=edge">' . PHP_EOL;
        $html .= $header;
        $html .= '</head>';
        $html .= '<body>' . PHP_EOL;
        $html .= $body;
        return $html . '</body>' . PHP_EOL . '</html>';
    }

    /**
     * Create XIF format from HTML DOCUMENT NODE
     *
     * @param $content
     * @return mixed
     */
    private static function createXIF($nodeID, $content, $channel)
    {

        $node = new \Ximdex\Models\Node($nodeID);
        $relTagsNodes = new RelTagsNodes();
        $tags = $relTagsNodes->getTags($node->GetID());
        $sectionId = $node->GetSection();
        $section = new \Ximdex\Models\Node($sectionId);
        $ximID = App::getValue('ximid');
        $version = $node->GetLastVersion() ?? [];
        $sd = new StructuredDocument($nodeID);
        $hDoc = new HTMLDocumentNode($nodeID);

        $docxif = $hDoc->getDocHeader($channel, $sd->GetLanguage(), $sd->GetDocumentType(), static::DOCXIF);


        $xml = new SimpleXMLElement("$docxif</" . static::DOCXIF . '>');
        $xml->addChild('id', implode(":", [$ximID, $node->GetID()]));
        $xml->addChild('file_version', $version["Version"] ?? '');
        if (is_array($tags)) {
            foreach ($tags as $tag) {
                $xml->addChild('tags', $tag);
            }
        }
        $xml->addChild('repository_id', $ximID);
        $xml->addChild('content_flat', strip_tags($content));
        $xml->addChild('content_render', $content);
        $xml->addChild('creation_date', $node->get('CreationDate'));
        $xml->addChild('update_date', $node->get('ModificationDate'));
        $xml->addChild('section', $section->GetNodeName());
        $xml->addChild('state', "publish"); // TODO state
        $xml->addChild('author', $version["UserName"] ?? ''); //TODO Author
        $xml->addChild('date', '2018-06-10 08:40:34'); //TODO date
        $xml->addChild('type', $section->nodeType->get('name'));//TODO tipo del documento

        return $xml->asXML();
    }
}