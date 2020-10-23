<?php

/**
 * \details &copy; 2019 Open Ximdex Evolution SL [http://www.ximdex.org]
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
use Ximdex\Models\Node;
use Ximdex\Models\NodeType;
use Ximdex\Runtime\App;
use Ximdex\Models\Channel;
use Ximdex\Models\Section;
use Ximdex\Models\Language;
use Ximdex\Models\Metadata;
use Ximdex\Models\Server;
use Ximdex\Runtime\Constants;
use Ximdex\Models\SectionType;
use Ximdex\Runtime\Session;
use Ximdex\Utils\SimpleXMLExtended;
use Ximdex\Models\StructuredDocument;
use Ximdex\Models\RelSemanticTagsNodes;

class JSONDocument extends AbstractStructuredDocument
{
    const DOCXIF = 'docxif';

    /**
     * This mode use a controller to resolve the requests
     */
    const MODE_DYNAMIC = Channel::RENDERTYPE_DYNAMIC;

    /**
     * This mode create a physical files but use include functionality to dependencies
     */
    const MODE_INCLUDE = Channel::RENDERTYPE_INCLUDE;

    /**
     * This mode create a physical files with node data and dependencies data
     */
    const MODE_STATIC = Channel::RENDERTYPE_STATIC;

    /**
     * This mode create a physical files with node data and dependencies data
     */
    const MODE_INDEX = Channel::RENDERTYPE_INDEX;

    /**
     * This flag indicate content node from HTML Document
     */
    const CONTENT_DOCUMENT = 'content';

    /**
     * This flag indicate include node from HTML Document
     */
    const INCLUDE_DOCUMENT = 'include';

    /**
     * Start ximdex body content flag
     */
    const START_XIMDEX_BODY_CONTENT = PHP_EOL . '<!----------START_XIMDEX_BODY_CONTENT---------->' . PHP_EOL;

    /**
     * End ximdex body content flag
     */
    const END_XIMDEX_BODY_CONTENT = PHP_EOL . '<!----------END_XIMDEX_BODY_CONTENT---------->' . PHP_EOL;

    /**
     * @param int $docId
     * @return bool|array
     */
    public static function getNodesHTMLDocument(int $docId)
    {
        $nodes = false;
        if ($docId == null) {
            return false;
        }
        $doc = new StructuredDocument($docId);
        if ($doc->getID()) {
            try {
                $nodes = static::getDocumentNodes($doc, [], true);
            } catch (\Exception $ex) {
                Logger::error('Failed to get nodes from HTMLDocumentNode class');
            }
        }
        return $nodes;
    }

    /**
     * @param StructuredDocument $doc
     * @param array $nodes
     * @param bool $isCurrentNode
     * @return array
     */
    public static function getDocumentNodes(StructuredDocument $doc, array $nodes = [], bool $isCurrentNode = false): array
    {
        // Layout
        $layout = $doc->getLayout();
        if ($layout && $layout->getContent()) {
            $layout = json_decode($layout->getContent(), true);
            if (isset($layout['template'])) {
                foreach ($layout['template'] as $templ) {
                    if (strcmp(key($templ), static::CONTENT_DOCUMENT) == 0) {
                        $data = static::getNodeData($doc, $templ[key($templ)], $layout, $isCurrentNode);
                        $nodes['xe_' . $data['id']] = $data; // TODO JS order numbers key, change it by array
                    } else {
                        $include = $doc->getInclude($templ[key($templ)]);
                        if ($include && $include->getID()) {
                            $nodes = static::getDocumentNodes($include, $nodes);
                        } else {
                            $nodes['not_found_' . count($nodes)] = [
                                'title' => key($templ),
                                'content' => '<div></div>'
                            ];
                            if ($doc->messages->messages[0]) {
                                Logger::warning($doc->messages->messages[0]['message']);
                            }
                        }
                    }
                }
            }
        }
        return $nodes;
    }

    /**
     * @param StructuredDocument $doc
     * @param array $sections
     * @param array $layout
     * @param bool $isCurrentNode
     * @return array
     */
    public static function getNodeData(StructuredDocument $doc, array $sections, array $layout, bool $isCurrentNode): array
    {
        $properties = [];
        $properties['type'] = $isCurrentNode ? static::CONTENT_DOCUMENT : static::INCLUDE_DOCUMENT;
        $extraData = [];
        $extraData['css'] = isset($layout['css']) ? $layout['css'] : [];
        $extraData['js'] = isset($layout['js']) ? $layout['js'] : [];
        $schemas = [];
        $content = '';
        $metadata = [];

        // First get all main schemas
        foreach ($sections as $section => $data) {
            $schemas[$section] = static::getSchemaFromComponent($doc, $section, $data, $extraData);
            if (isset($schemas[$section]) && $schemas[$section]['view'] != null) {
                $content .= $schemas[$section]['view'];
            } else {
                $content .= '<div></div>';
            }
        }
        if ($isCurrentNode) {
            $metadata = $doc->getMetadata();
        }

        // Last get dependent schemas
        foreach ($schemas as $section => $data) {
            if (isset($data['sections'])) {
                $schemas = static::getChildSchemasBySections($doc, $schemas, $data['sections'], $extraData);
            }
        }
        if (!is_null($doc)) {
            $content = $doc->getContent() ?: $content;
        }

        // Properties
        $properties['id'] = $doc->getID();
        $properties['content'] = !is_null($content) ? $content : '';
        $properties['title'] = $doc->getName();
        $properties['metadata'] = $metadata;
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
    public static function renderHTMLDocument(int $docId, string $content = null, string $channel = null, string $mode = null
        , Server $server = null)
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
        $info = [];
        if ($server) {
            $info['server'] = $server;
        }
        if (strcmp($mode, static::MODE_DYNAMIC) == 0) {

            // Dynamic mode
            $body = '';
            $name = '';
            foreach ($docHTML as $node) {
                $css = isset($node['css']) ? array_merge($css, $node['css']) : $css;
                $js = isset($node['css']) ? array_merge($js, $node['js']) : $js;
                if (isset($node['type']) && $node['type'] == static::CONTENT_DOCUMENT) {
                    $name = $node['title'];
                    $body .= !is_null($content) ? $content : $node['content'];
                    if (strpos($name, '_') !== 0) {
                        $body = static::START_XIMDEX_BODY_CONTENT . $body . static::END_XIMDEX_BODY_CONTENT;
                    }
                } else {
                    if (isset($node['id']) and $node['id']) {
                        $body .= PHP_EOL . self::generateMacroExec('include', '@@@RMximdex.include(' . $node['id'] . ')@@@') . PHP_EOL;
                    }
                }
            }
            $render = $body;
            if (strpos($name, '_') !== 0) {
                $info += static::getInfo($docId);
                $info['id'] = $docId;
                $info['channel'] = $channel;
                $render = static::createDynamic($info, $render, $css, $js);
            }
        } elseif (strcmp($mode, static::MODE_INCLUDE) == 0) {

            // Include mode
            $body = '';
            $name = '';
            foreach ($docHTML as $node) {
                $css = isset($node['css']) ? array_merge($css, $node['css']) : $css;
                $js = isset($node['css']) ? array_merge($js, $node['js']) : $js;
                if (isset($node['type']) and $node['type'] == static::CONTENT_DOCUMENT) {
                    $name = $node['title'];
                    $body .= ! is_null($content) ? $content : $node['content'];
                    if (strpos($name, '_') !== 0) {
                        $body = static::START_XIMDEX_BODY_CONTENT . $body . static::END_XIMDEX_BODY_CONTENT;
                    }
                } else {
                    if (isset($node['id']) and $node['id']) {
                        $body .= PHP_EOL . self::generateMacroExec('include', '@@@RMximdex.include(' . $node['id'] . ')@@@') . PHP_EOL;
                    }
                }
            }
            $render = $body;
            $pos = strpos($name, '_');
            if ($pos !== 0) {
                $info += static::getInfo($docId);
                $info['id'] = $docId;
                $info['channel'] = $channel;
                $render = static::createBasicHTMLTemplate($info, $body, $css, $js, true);
            }
        } else {

            // Static mode
            $body = '';
            foreach ($docHTML as $node) {
                $css = isset($node['css']) ? array_merge($css, $node['css']) : $css;
                $js = isset($node['css']) ? array_merge($js, $node['js']) : $js;
                if (isset($node['type']) && $node['type'] == static::CONTENT_DOCUMENT) {
                    $body .= static::START_XIMDEX_BODY_CONTENT;
                    $body .= !is_null($content) ? $content : $node['content'];
                    $body .= static::END_XIMDEX_BODY_CONTENT;
                    $name = $node['title'];
                } else {
                    $body .= isset($node['content']) ? $node['content'] : '';
                }
            }
            $render = $body;
            $pos = strpos($name, '_');
            if ($pos !== 0) {
                $tags = implode(',', array_map(function ($tag) {
                    return $tag['Name'];
                }, static::getTags($docId)));
                $info += static::getInfo($docId);
                if (! empty($tags)) {
                    $info['metadata']['keywords'] = $tags;
                }
                $render = static::createBasicHTMLTemplate($info, $body, $css, $js);
            }
        }
        return $render;
    }

    /**
     * Create XIF format from HTML DOCUMENT NODE
     *
     * @param \Ximdex\Models\Node $node
     * @param string $content
     * @param Channel $channel
     * @return mixed
     */
    public static function createXIF(\Ximdex\Models\Node $node, string $content, Channel $channel)
    {
        $tags = static::getTags($node->getID());
        $sectionId = $node->getSection();
        $ximID = App::getValue('ximid');
        $version = $node->getLastVersion() ?? [];
        $section = new Section($sectionId);
        $sectionNode = new \Ximdex\Models\Node($section->getIdNode());
        $sectionType = new SectionType($section->getIdSectionType());
        $info = static::getInfo($node->getID());
        $hDoc = new static($node->getID());
        $docxif = $hDoc->getDocHeader($channel->getID(), $info['languageId'], $info['type'], static::DOCXIF);

        // Create XML
        $xml = new SimpleXMLExtended("$docxif</" . static::DOCXIF . '>');
        $xml->addChild('id', implode(':', [$ximID, $node->getID()]));
        $xml->addChild('file_version', $version['Version'] ?? '');
        if (is_array($tags)) {
            foreach ($tags as $tag) {
                $xml->addChild('tags', $tag['Name']);
            }
        }
        $xml->addChild('id_ximdex', $ximID);
        $xml->addChild('name', $info['metadata']['title']);
        $xml->addChild('filename', $node->getNodeName());
        $xml->addChild('slug', '@@@RMximdex.pathto(THIS)@@@');
        $xml->addChildCData('content_flat', html_entity_decode(preg_replace('/((\n)(\s{2,}))/', '', strip_tags($content))));
        $xml->addChildCData('content_render', $content);
        $xml->addChild('creation_date', date('Y-m-d H:i:s', $node->get('CreationDate')));
        $xml->addChild('update_date', date('Y-m-d H:i:s', $node->get('ModificationDate')));
        $xml->addChild('section', $sectionNode->getNodeName());
        $xml->addChild('id_section', $sectionNode->getID());
        $xml->addChild('state', 'publish');

        // Add all metadatas availables
        $content_payload = $xml->addChild('content-payload');
        $content_payload->addChild('type', $sectionType->get('sectionType'));

        foreach ($info['metadata'] as $key => $value) {
            if ($key === 'image') {
                $value = !empty($value) ? $value : 'null';
            } else if ($key === 'author') {
                $value = !empty($value) ? $value : 'No author';
            } else if ($key === 'date') {
                $value = !empty($value) ? date('Y-m-d H:i:s', strtotime($value)) : date('Y-m-d H:i:s');
            }
            $content_payload->addChild($key, $value);
        }
        return $xml->asXML();
    }

    /**
     * ******************************************* AUX METHODS ********************************************
     */

    /**
     * @param StructuredDocument $doc
     * @param array $schemas
     * @param array $sections
     * @param array $extraData
     * @return array|NULL
     */
    private static function getChildSchemasBySections(StructuredDocument $doc, array $schemas, array $sections, array &$extraData)
    {
        foreach ($sections as $section => $data) {
            if (!array_key_exists($section, $schemas)) {
                $schemas = static::getChildSchemasBySection($doc, $section, $data, $schemas, $extraData);
            }
        }
        return $schemas;
    }

    /**
     * @param StructuredDocument $doc
     * @param string $section
     * @param array $data
     * @param array $schemas
     * @param array $extraData
     * @return array
     */
    private static function getChildSchemasBySection(
        StructuredDocument $doc,
        string $section,
        array $data,
        array $schemas,
        array &$extraData
    ): array {
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
     * @param StructuredDocument $doc
     * @param string $compName
     * @param array $data
     * @param array $extraData
     * @return NULL|array
     */
    private static function getSchemaFromComponent(StructuredDocument $doc, string $compName, array $data, array &$extraData)
    {
        $schema = null;
        $comp = $doc->getComponent($compName);
        if ($comp && $comp->getContent()) {
            $jsonComp = json_decode($comp->getContent(), true);
            $schema = array_merge($jsonComp['schema'], $data);
            $schema['name'] = $compName;
            $view = $doc->getView($jsonComp['schema']['view']);
            if ($view && $view->getContent()) {
                $schema['view'] = $view->getContent();
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
     * @param array $info
     * @param string $body
     * @param array $css
     * @param array $js
     * @param bool $isInclude
     * @return string
     */
    private static function createBasicHTMLTemplate(array $info, string $body, array $css, array $js, bool $isInclude = false)
    {
        $header = '';
        foreach ($css as $file) {
            $header .= "<link rel='stylesheet' type='text/css' href='@@@RMximdex.pathto($file)@@@'>" . PHP_EOL;
        }
        foreach ($js as $file) {
            $header .= "<script type='text/javascript' src='@@@RMximdex.pathto($file)@@@'></script>" . PHP_EOL;
        }
        $html = '<!DOCTYPE html>' . PHP_EOL;
        $html .= '<html lang="' . $info['language'] . '">' . PHP_EOL . '<head>' . PHP_EOL;
        if (isset($info['server'])) {
            $charset = $info['server']->get('idEncode');
        } else {
            $charset = App::getValue('displayEncoding');
        }
        $html .= "<meta charset=\"{$charset}\">" . PHP_EOL;
        $html .= '<meta name="viewport" content="width=device-width, initial-scale=1.0">' . PHP_EOL;
        $html .= '<meta http-equiv="X-UA-Compatible" content="ie=edge">' . PHP_EOL;
        if (Constants::HTML_GENERATOR_TAG) {
            $html .= '<meta name="generator" content="' . Constants::HTML_GENERATOR_TAG . '">' . PHP_EOL;
        }
        if (App::getValue('Owner')) {
            $html .= '<meta name="owner" content="' . App::getValue('Owner') . '">' . PHP_EOL;
        }
        if (isset($info['metadata'])) {
            foreach ($info['metadata'] as $meta => $value) {
                if (empty($value)) {
                    continue;
                }
                if ($meta == 'title') {
                    $html .= "<title>{$value}</title>" . PHP_EOL;
                } else {
                    $html .= "<meta name=\"$meta\" content=\"$value\">" . PHP_EOL;
                }
            }
        }
        if ($isInclude) {

            // Path to current node without language
            $node = new Node($info['id']);
            $path = trim($node->getPublishedPath($info['channel'], true, false, false), '/');
            $html .= self::generateMacroExec('const', 'xim_path', $path);
        }
        $html .= $header;
        $html .= '</head>' . PHP_EOL;
        $html .= '<body>' . PHP_EOL;
        $html .= $body;
        return $html . '</body>' . PHP_EOL . '</html>';
    }

    private static function createDynamic(array $info, string $body, array $css = [], array $js = [])
    {
        $head = self::headTemplate($css, $js, $info);
        $metadata = isset($info['metadata']) ? self::metadataTemplate($info['metadata']) : [];
        $html = self::generateMacroExec('var', 'xim_head', str_replace(PHP_EOL, '<ximeol>', $head));
        $html .= self::generateMacroExec('var', 'xim_lang', $info['language']);

        // Path to current node without language
        $node = new Node($info['id']);
        $path = trim($node->getPublishedPath($info['channel'], true, false, false), '/');
        $html .= self::generateMacroExec('const', 'xim_path', $path);

        // Metadata
        $html .= self::generateMacroExec('var', 'xim_metadata', $metadata);
        $html .= self::generateMacroExec('var', 'xim_tpl', '<!DOCTYPE html><html lang="' . $info['language']
            . '"><head>%s</head><body>%s</body></html>');
        $html .= self::generateMacroExec('obstart');
        $html .= PHP_EOL . $body;
        $html .= self::generateMacroExec('obgetclean', 'xim_content');
        $html .= self::generateMacroExec('sprintf1', 'xim_head_metadata', 'xim_head', 'xim_metadata');
        $html .= self::generateMacroExec('sprintf2', 'xim_document', 'xim_tpl', 'xim_head_metadata', 'xim_content');
        $html .= self::generateMacroExec('echo', 'xim_document');
        return $html;
    }

    private static function getTags(int $nodeId)
    {
        $relSemanticTagsNodes = new RelSemanticTagsNodes();
        return $relSemanticTagsNodes->getTags($nodeId) ?? [];
    }

    private static function getInfo(int $nodeId): array
    {
        $sd = new StructuredDocument($nodeId);
        $lang = new Language($sd->getLanguage());
        $info = [];
        $info['languageId'] = $lang->getID();
        $info['language'] = $lang->getIsoName();
        $info['type'] = $sd->getDocumentType();
        $info['metadata'] = static::prepareMetadata($sd->getMetadata(), $nodeId);
        return $info;
    }

    private static function prepareMetadata(array $metadata, int $nodeId = null)
    {
        if ($nodeId) {
            $tags = (new RelSemanticTagsNodes)->getTags($nodeId);
            $keywords = '';
            foreach ($tags as $tag) {
                $keywords .= ",{$tag['Name']}";
            }
            $keywords = trim($keywords, ',');
        }
        $result = [];
        foreach ($metadata as $meta) {
            if (key_exists('groups', $meta) || key_exists('metadata', $meta)) {
                $result = array_merge($result, static::prepareMetadata($meta['groups'] ?? $meta['metadata'] ?? []));
                continue;
            }
            if (!$meta['value']) {
                continue;
            }
            if ($meta['type'] == Metadata::TYPE_DATE) {
                $meta['value'] = "{$meta['value']}T00:00:00Z";
            } elseif ($meta['type'] == Metadata::TYPE_IMAGE) {
                $meta['value'] = "@@@RMximdex.pathto({$meta['value']})@@@";
            }
            $result[$meta['name']] = $meta['value'];
        }
        if (isset($keywords)) {
            if (isset($result['keywords'])) {
                $result['keywords'] .= ',' . $keywords;
            } else {
                $result['keywords'] = $keywords;
            }
        }
        return $result;
    }

    private static function headTemplate(array $css = [], array $js = [], array $info = []): string
    {
        if (isset($info['server'])) {
            $charset = $info['server']->get('idEncode');
        } else {
            $charset = App::getValue('displayEncoding');
        }
        $tpl = "<meta charset=\"{$charset}\">" . PHP_EOL;
        $tpl .= '<meta name="viewport" content="width=device-width, initial-scale=1.0">' . PHP_EOL;
        $tpl .= '<meta http-equiv="X-UA-Compatible" content="ie=edge">' . PHP_EOL;
        if (Constants::HTML_GENERATOR_TAG) {
            $tpl .= '<meta name="generator" content="' . Constants::HTML_GENERATOR_TAG . '">' . PHP_EOL;
        }
        if (App::getValue('Owner')) {
            $tpl .= '<meta name="owner" content = "' . App::getValue('Owner') . '">' . PHP_EOL;
        }
        $tpl .= '%s' . PHP_EOL;
        foreach ($css as $file) {
            $tpl .= '<link rel="stylesheet" type="text/css" href="@@@RMximdex.pathto(' . $file . ')@@@" >' . PHP_EOL;
        }
        foreach ($js as $file) {
            $tpl .= '<script type="text/javascript" src="@@@RMximdex.pathto(' . $file . ')@@@"></script>' . PHP_EOL;
        }
        return $tpl;
    }

    private static function metadataTemplate(array $metadata): string
    {
        $result = '';
        foreach ($metadata as $meta => $value) {
            if (!empty($value)) {
                $result .= "<meta name=\"$meta\" content=\"$value\"><ximeol>";
            }
        }
        return $result;
    }

    private static function generateMacroExec(string $command, ...$vars): string
    {
        $params = '';
        $macro = '@@@GMximdex.exec(' . $command . '%s)GMximdex@@@';
        if (is_array($vars) && count($vars) > 0) {
            $params = implode(', ximparam=', $vars);
            $params = ', ximparam=' . $params;
        }
        return sprintf($macro, $params) . PHP_EOL;
    }


}
