<?php
/**
 * Created by PhpStorm.
 * User: jvargas
 * Date: 19/02/16
 * Time: 14:39
 */

namespace XimdexApi\actions;

use Ximdex\Models\Node;
use Ximdex\Models\StructuredDocument;
use XimdexApi\core\Request;
use XimdexApi\core\Response;


class XeditAction extends Action
{

    const PREFIX = 'xedit';

    const CONTENT_DOCUMENT = 'content';

    const ROUTE_GET = '\d+/get';
    const ROUTE_SET = 'set';

    protected const ROUTES = [
        self::ROUTE_GET => 'get',
        self::ROUTE_SET => 'set'
    ];

    protected const PUBLIC = [
    ];

    /********************************************* API METHODS *********************************************/

    /**
     * @param Request $r
     * @param Response $w
     */
    public static function get(Request $r, Response $w)
    {
        $pathElements = explode('/', $r->getPath());
        $nodeId = $pathElements[1];
        $doc = new StructuredDocument($nodeId);
        $response = '';

        if ($doc->GetID()) {

            // Layout
            $layout = $doc->getLayout();
            if ($layout && $layout->GetContent()) {
                $layout = json_decode($layout->GetContent(), true);
                $nodes = [];
                foreach ($layout as $schema => $value) {
                    $node = null;
                    $properties = [];
                    if ($schema == static::CONTENT_DOCUMENT) {
                        // Node content
                        $node = $doc;
                        $properties['editable'] = true;
                    } else {
                        // Includes
                        $node = $doc->getInclude($value);
                        $properties['editable'] = false;
                    }


                    $schemas = [];
                    if ($schema == static::CONTENT_DOCUMENT) {
                        $content = '';

                        // First get all main schemas
                        foreach ($value as $section => $data) {
                            $schemas[$section] = XeditAction::getSchemaFromComponent($doc, $section, $data);
                            if (isset($schemas[$section]) && $schemas[$section]['template'] != null && !empty($schemas[$section]['template'])) {
                                $content .= $schemas[$section]['template'];
                            } else {
                                $content .= '<div>EMPTY COMPONENT</div>';
                            }
                        }

                        // Last get dependent schemas
                        foreach ($schemas as $section => $data) {
                            if (isset($data['sections'])) {
                                $schemas = XeditAction::getChildSchemasBySections($doc, $schemas, $data['sections']);
                            }
                        }
                    } else {
                        $content = '<div>EMPTY NODE</div>';
                    }

                    if ($node && $node->GetContent()) {
                        $content = $node->GetContent();
                    }


                    // Properties
                    $properties['content'] = $content;
                    $properties['title'] = $node ? $node->get('Name') : '';
                    $properties['attributes'] = [];
                    $properties['js'] = [];
                    $properties['css'] = [
                        "http://lab03/files/css/ficha.min.css",
                        "http://lab03/files/js/owlcarousel/owl.carousel.min.css",
                        "http://lab03/files/js/owlcarousel/owl.theme.default.min.css",
                        "https://use.fontawesome.com/releases/v5.0.6/css/all.css"
                    ];
                    $properties['schema'] = $schemas;

                    $nodes[$node ? $node->GetID() : 'not_found_' . count($nodes)] = $properties;
                }


                $response = [
                    'resourceUrl' => '',
                    'metas' => [
                        'title' => 'Ejemplo',
                        'tags' => 'ejemplo test prueba'
                    ],
                    'nodes' => $nodes
                ];

            }

        } else {
            $w->setMessage('Document not found')->setStatus(1);
        }
        $w->setResponse($response);
        $w->send();
    }


    public static function set(Request $r, Response $w)
    {
        $data = json_decode(file_get_contents('php://input'), true);
        if (isset($data['nodes'])) {
            $nodes = $data['nodes'];

            foreach ($nodes as $nodeId => $value) {
                if (isset($value['editable']) && $value['editable']) {
                    $node = new Node(($nodeId));
                    $node->SetContent($value['content']);
                }
                $w->setResponse(file_get_contents('php://input'));
            }
        } else {
            $w->setMessage("Nodes not found")->setStatus(0);
        }
        $w->send();
    }

    /********************************************* AUX METHODS *********************************************/

    /**
     * @param $doc
     * @param $schemas
     * @param $sections
     *
     * @return array
     */
    private static function getChildSchemasBySections($doc, $schemas, $sections)
    {
        foreach ($sections as $section => $data) {
            if (!array_key_exists($section, $schemas)) {
                $schemas = XeditAction::getChildSchemasBySection($doc, $section, $data, $schemas);
            }
        }
        return $schemas;
    }

    /**
     * @param $doc StructuredDocument
     * @param $section string
     * @param $schemas array
     * @param $data array
     *
     * @return array
     */
    private static function getChildSchemasBySection($doc, $section, $data, $schemas): array
    {
        $schema = XeditAction::getSchemaFromComponent($doc, $section, $data);
        if ($schema != null) {
            $schemas[$section] = $schema;
            if (array_key_exists('sections', $schemas[$section])) {
                $schemas = XeditAction::getChildSchemasBySections($doc, $schemas, $schemas[$section]['sections']);
            }
        }
        return $schemas;
    }

    /**
     * @param $doc StructuredDocument
     * @param $compName string
     * @param $data array
     *
     * @return array
     */
    private static function getSchemaFromComponent($doc, $compName, $data)
    {
        $schema = null;
        $comp = $doc->getComponent($compName);
        if ($comp && $comp->GetContent()) {
            $schemaComp = json_decode($comp->GetContent(), true);
            $schema = array_merge($schemaComp[$compName], $data);
            $schema['name'] = $compName;
            $view = $doc->getView($schemaComp[$compName]['template']);
            if ($view && $view->GetContent()) {
                $schema['template'] = $view->GetContent();
            } else {
                $schema['template'] = '';
            }
        }
        return $schema;
    }
}