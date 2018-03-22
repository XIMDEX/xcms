<?php
namespace Ximdex\NodeTypes;

use Ximdex\Logger;
use Ximdex\Models\StructuredDocument;

class HTMLDocumentNode extends FileNode
{

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
     * This flag indicate content node from HTML Document
     */
    const CONTENT_DOCUMENT = 'content';

    /*
     * This flag indicate include node from HTML Document
     */
    const INCLUDE_DOCUMENT = 'include';

    /**
     *
     * @param
     *            $docId
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
        
        if (! is_null($doc) && $doc->GetContent()) {
            $content = $doc->GetContent();
        }
        
        // Properties
        $properties['id'] = $doc->GetID();
        $properties['content'] = ! is_null($content) ? $content : '';
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
     *
     * @return string || bool
     */
    public static function renderHTMLDocument(int $docId, string $content = null, string $mode = HTMLDocumentNode::MODE_STATIC)
    {
        $render = null;
        $css = $js = [];
        $name = '';
        
        if ($docId == null || ! in_array($mode, [
            static::MODE_DYNAMIC,
            static::MODE_INCLUDE,
            static::MODE_STATIC
        ])) {
            return false;
        }
        $docHTML = static::getNodesHTMLDocument($docId);
        
        if ($docHTML === false) {
            return false;
        }
        
        if (strcmp($mode, static::MODE_DYNAMIC) == 0) {
            foreach ($docHTML as $node) {
                if ($node['type'] == static::CONTENT_DOCUMENT) {
                    $render .= ! is_null($content) ? $content : $node['content'];
                }
            }
        } else if (strcmp($mode, static::MODE_INCLUDE) == 0) {
            $body = '';
            $name = '';
            foreach ($docHTML as $node) {
                $css = isset($node['css']) ? array_merge($css, $node['css']) : $css;
                $js = isset($node['css']) ? array_merge($js, $node['js']) : $js;
                if ($node['type'] == static::CONTENT_DOCUMENT) {
                    $body .= ! is_null($content) ? $content : $node['content'];
                    $name = $node['title'];
                } else {
                    $body .= '@@@GMximdex.code(include, @@@RMximdex.pathto(' . $node['id'] . ')@@@)@@@';
                }
            }
            $render = $body;
            
            // TODo
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
                    $body .= ! is_null($content) ? $content : $node['content'];
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
            if (! array_key_exists($section, $schemas)) {
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
     * @param
     *            $css
     * @param
     *            $js
     * @param
     *            $body
     * @return string
     */
    private static function createBasicHTMLTemplate($body, $css, $js)
    {
        $header = '';
        
        foreach ($css as $file) {
            $header .= "<link rel='stylesheet' type='text/css' href='@@@RMximdex.pathto($file)@@@'>";
        }
        
        foreach ($js as $file) {
            $header .= "<script type='text/javascript' src='@@@RMximdex.pathto($file)@@@'>";
        }
        
        $html = '<!DOCTYPE html>';
        $html .= '<html><head><meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">';
        $html .= $header;
        $html .= '</head><body> ';
        $html .= $body;
        return $html . "</body></html>";
    }
}