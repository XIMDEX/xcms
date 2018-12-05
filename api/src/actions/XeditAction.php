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

namespace XimdexApi\actions;

use Ximdex\Logger;
use Ximdex\Models\Node;
use Ximdex\Runtime\App;
use XimdexApi\core\Request;
use XimdexApi\core\Response;
use Ximdex\Models\FastTraverse;
use Ximdex\Models\StructuredDocument;
use Ximdex\NodeTypes\HTMLDocumentNode;
use Ximdex\NodeTypes\NodeTypeConstants;

class XeditAction extends Action
{
    const PATTERN_PATHTO = "/[[:word:]]+=[\"']@@@RMximdex\.pathto\(([,-_#%=\.\w\s]+)\)@@@[\"']/";
    const PATTERN_XE_LINK = "/<([a-zA-Z]+)([^>]*?(?=xe_link))xe_link\=[\"']([^\"]*)[\"']([^>]*)>/";
    const PREFIX = 'xedit';
    const CONTENT_DOCUMENT = 'content';
    const ROUTE_GET = '\d+/get';
    const ROUTE_SET = 'set';
    const ROUTE_FILE = 'file';
    const ROUTE_GET_TREE_INFO = 'get_tree_info';
    protected const ROUTES = [
        self::ROUTE_GET => 'get',
        self::ROUTE_SET => 'set',
        self::ROUTE_FILE => 'file',
        self::ROUTE_GET_TREE_INFO => 'getTreeInfo'
    ];
    const LINK_TYPES = [
        'a' => 'href',
        'applet' => 'codebase',
        'area' => 'href',
        'base' => 'href',
        'blockquote' => 'cite',
        'del' => 'cite',
        'form' => 'action',
        'frame' => 'src',
        'head' => 'profile',
        'iframe' => 'src',
        'img' => 'src',
        'input' => 'src',
        'ins' => 'cite',
        'link' => 'href',
        'object' => 'data',
        'q' => 'cite',
        'script' => 'src',
        'audio' => 'src',
        'button' => 'formaction',
        'command' => 'icon',
        'embed' => 'src',
        'source' => 'src',
        'html' => 'manifest',
        'track' => 'src',
        'video' => 'src'
    ];
    protected const PUBLIC = [];

    /********************************************* API METHODS *********************************************/

    /**
     * @param Request $r
     * @param Response $w
     */
    public static function get(Request $r, Response $w)
    {
        $pathElements = explode('/', $r->getPath());
        $nodeId = $pathElements[1];
        $response = '';
        $name = '';
        $nodes = HTMLDocumentNode::getNodesHTMLDocument($nodeId);
        $metadata = [];
        
        if ($nodes === false) {
            $w->setMessage('Document not found')->setStatus(1);
        } else {
            // Transform data to Xedit editor
            foreach ($nodes as $key => &$node) {
                if (isset($node['id'])) {
                    $node['editable'] = strcmp($node['type'], HTMLDocumentNode::CONTENT_DOCUMENT) == 0 ? true : false;
                    $name = strcmp($node['type'], HTMLDocumentNode::CONTENT_DOCUMENT) == 0 ? $node['title'] : $name;
                    $node['content'] = static::transformContentToXedit($node['content']);
                    $schemas = $node['schema'];
                    foreach ($schemas as $_key => $value) {
                        $schemas[$_key]['view'] = static::transformContentToXedit($value['view']);
                    }
                    if (strcmp($node['type'], HTMLDocumentNode::CONTENT_DOCUMENT) == 0) {
                        $metadata = static::getMetadata($nodeId);
                    }
                    $node['schema'] = $schemas;
                } elseif (strpos($key, 'not_found') === 0) {
                    $node['content'] = '<div>No encontrado</div>';
                }
            }
            $action = '_action';
            $response = [
                'name' => $name,
                'document' => [
                    'id' => $nodeId,
                ],
                'router' => [
                    'token' => [
                        'type' => 'url',
                        'field' => 'token',
                        'value' => static::getUserToken(),
                    ],
                    'baseUrl' => App::get('UrlHost') . App::get('UrlRoot') . '/api',
                    'attrs' => [
                        'token' => static::getUserToken(),
                        'id' => $nodeId,
                    ],
                    'endpoints' => [
                        'documents' => [
                            'get' => [
                                'method' => 'get',
                                'path' => '',
                                'params' => [
                                    $action => str_replace('\d+', $nodeId, XeditAction::getPath(XeditAction::ROUTE_GET))
                                ]
                            ],
                            'save' => [
                                'method' => 'post',
                                'path' => '',
                                'params' => [
                                    $action => XeditAction::getPath(XeditAction::ROUTE_SET),
                                    'id' => $nodeId
                                ]
                            ]
                        ],
                        'resources' => [
                            'tree' => [
                                'method' => 'get',
                                'path' => '',
                                'params' => [
                                    $action => XeditAction::getPath(XeditAction::ROUTE_GET_TREE_INFO),
                                    'id' => $nodeId,
                                    'type' => '{type}',
                                ]
                            ],
                            'get' => [
                                'method' => 'get',
                                'path' => '',
                                'params' => [
                                    $action => NodeAction::getPath(NodeAction::ROUTE_GET),
                                    'id' => $nodeId,
                                    'token' => static::getUserToken()
                                ]
                            ],
                            'image' => [
                                'method' => 'get',
                                'path' => '',
                                'params' => [
                                    $action => XeditAction::getPath(XeditAction::ROUTE_FILE),
                                    'id' => $nodeId,
                                    'token' => static::getUserToken()
                                ]
                            ]
                        ]
                    ]
                ],
                'metas' => $metadata,
                'nodes' => $nodes,
            ];
        }
        $w->setResponse($response);
        $w->send();
    }

    /**
     * @param Request $r
     * @param Response $w
     */
    public static function set(Request $r, Response $w)
    {
        $data = json_decode(file_get_contents('php://input'), true);
        if (isset($data['nodes'])) {
            $metadata = null;
            if (isset($data['metas'])) {
                $metadata = [];
                foreach ($data['metas'] as $meta) {
                    if (isset($meta['value']) && isset($meta['name'])) {
                        $metadata[$meta['name']] = static::transformContentToXimdex($meta['value']);
                    }
                }
                $metadata = json_encode($metadata, JSON_PRETTY_PRINT);
            }
            $nodes = $data['nodes'];
            foreach ($nodes as $nodeId => $value) {
                if (isset($value['editable']) && $value['editable']) {
                    $node = new StructuredDocument(intval(str_replace('xe_', '', $nodeId)));
                    $content = static::transformContentToXimdex($value['content']);
                    $node->SetContent($content, true, $metadata);
                }
            }
        } else {
            $w->setMessage("Nodes not found")->setStatus(0);
        }
        $w->setResponse('Saved');
        $w->send();
    }

    /**
     * Get file from nodeid
     *
     * @param Request $r
     * @param Response $w
     */
    public static function file(Request $r, Response $w)
    {
        $nodeId = $_GET['id'];
        $response = '';
        $headers = [];
        if (intval($nodeId)) {
            $node = new Node($nodeId);
            if ($node->GetID() !== null) {
                $data = $node->filemapper();
                $headers = $data['headers'];
                $response = $data['content'];
            }
        }
        $w->setResponse($response);
        $w->send($headers);
    }

    /**
     * Get children nodes from parent node
     *
     * @param Request $r
     * @param Response $w
     */
    public static function getTreeInfo(Request $r, Response $w)
    {
        $types = [
            'xml' => [NodeTypeConstants::XML_DOCUMENT],
            'html' => [NodeTypeConstants::HTML_DOCUMENT],
            'binary' => [NodeTypeConstants::BINARY_FILE],
            'image' => [NodeTypeConstants::IMAGE_FILE],
            'link' => [NodeTypeConstants::LINK, NodeTypeConstants::HTML_DOCUMENT, NodeTypeConstants::BINARY_FILE,
                NodeTypeConstants::IMAGE_FILE, NodeTypeConstants::XML_DOCUMENT],
            'video' => [NodeTypeConstants::VIDEO_FILE]
        ];
        $nodeId = isset($_GET['id']) ? $_GET['id'] : null;
        $type = isset($_GET['type']) ? $_GET['type'] : null;
        $type = isset($types[$type]) ? $types[$type] : false;
        $level = isset($_GET['level']) && ctype_digit($_GET['level']) ? (int)$_GET['level'] : 1;
        if ($types !== null and ctype_digit($nodeId)) {
            $filters = null;
            if ($type) {
                $filters = ["include" => ["nt.IdNodeType" => $type]];
            }
            $children = FastTraverse::getChildren($nodeId, ['node' => ['Name', 'idParent'], 'nodeType' =>
                ['isFolder', 'isVirtualFolder', 'IdNodeType', 'icon']], null, $filters, ['IsRenderizable' => true,
                'IsHidden' => false]);
            $result = static::buildCompleteTree($children, $type);
            $count = count($result) - 1;
            
            if ($level !== null && $count > $level) {
                $result["l{$level}"]['resources_count'] = count(reset($children));
                $result = array_intersect_key($result, array_flip(["l{$level}"]));
            }
            $w->setResponse($result);
        } else {
            $w->setStatus(1)->setMessage('Id and type are required');
        }
        $w->send();
    }

    /********************************************* AUX METHODS *********************************************/
    /**
     * @param $content
     * @return string
     */
    public static function transformContentToXedit($content)
    {
        $content = preg_replace_callback(static::PATTERN_PATHTO, array(
            XeditAction::class,
            'transformPathtoToXeLink'
        ), $content);
        return $content;
    }

    /**
     * @param $content
     * @return string
     */
    public static function transformContentToXimdex($content)
    {
        $content = preg_replace_callback(static::PATTERN_XE_LINK, [
            XeditAction::class,
            'transformXeLinkToPathto'
        ], $content);
        return $content;
    }

    /**
     * @param $matches
     * @return string
     */
    private static function transformPathtoToXeLink($matches)
    {
        $replace = 'xe_link="%s" ';
        return sprintf($replace, trim($matches[1]));
    }

    /**
     * @param $matches
     * @return string
     */
    private static function transformXeLinkToPathto($matches)
    {
        $attribute = static::LINK_TYPES[$matches[1]] ?? 'href';
        $replace = '<%s %s %s="@@@RMximdex.pathto(%s)@@@" %s>';
        return sprintf($replace, trim($matches[1]), trim($matches[2]), $attribute, trim($matches[3]), trim($matches[4]));
    }

    /**
     * Get user token
     * @return string
     */
    public static function getUserToken()
    {
        $token = isset($_SERVER['PHP_AUTH_USER']) ? $_SERVER['PHP_AUTH_USER'] : null;
        if (is_null($token)) {
            $token = isset($_GET['token']) ? $_GET['token'] : null;
        }
        return $token;
    }

    /**
     * Get metadata from node
     *
     * @param int $nodeId
     * @return array
     */
    public static function getMetadata(int $nodeId) : array
    {
        $node = new StructuredDocument($nodeId);
        $metadata = $node->getMetadata();
        return $metadata;
    }

    /**
     * Check if the nodes have associated actions
     *
     * @param $nodes
     * @return null
     */
    protected function checkNodeAction(& $nodes)
    {
        //TODO Copiado de la clase Action_browser3 (Sacar en común)
        $db = new \Ximdex\Runtime\Db();
        $sql = 'select count(1) as total from Actions a left join Nodes n using(IdNodeType) where IdNode = %s and a . Sort > 0';
        $sql2 = $sql . ' AND a.Command=\'fileupload_common_multiple\'';
        if (!empty($nodes)) {
            foreach ($nodes as & $node) {
                $nodeid = $node['nodeid'];
                $_sql = sprintf($sql, $nodeid);
                $db->query($_sql);
                $total = $db->getValue('total');
                $node['hasActions'] = $total;
                // $db = new \Ximdex\Runtime\Db();
                $sql2 = sprintf($sql2, $nodeid);
                $db->query($sql2);
                $total = $db->getValue('total');
                $node['canUploadFiles'] = $total;
            }
            return $nodes;
        } else {
            Logger::info('Empty nodes in checkNodeAction [browser3]');
            return null;
        }
    }

    private static function buildCompleteTree($nodes, $types)
    {
        $tree = [];
        $processedNodes = [];
        foreach ($nodes as $level => $children) {
            $keys = array_keys($children);
            foreach ($keys as $nodeId) {
                list($tree, $processedNodes) = static::buildBranch($tree, $processedNodes, $nodeId, $level, $types);
            }
        }

        return $tree;
    }

    private static function buildBranch($tree, $processedNodes, $nodeId, $level, $types)
    {
        $node = new Node($nodeId);
        
        // Create node in tree
        $sheet = static::createSheet(
            $node->GetNodeName(),
            $node->nodeType->GetID(),
            $node->nodeType->isFolder(),
            $node->nodeType->get('IsVirtualFolder'),
            $types
        );

        $sheet['icon'] = $node->nodeType->GetIcon();

        if ($sheet) {
            if (!isset($tree["l$level"])) {
                $tree["l$level"] = [
                    'level' => $level,
                    'nodes' => []
                ];
            }
            $tree["l{$level}"]['nodes'][$nodeId] = $sheet;
        }

        $processedNodes[] = $nodeId;
        $idParent = $node->GetParent();
        
        if ($level > 0 && $idParent && !in_array((int)$idParent, $processedNodes)) {
            list($tree, $processedNodes) = static::buildBranch($tree, $processedNodes, $idParent, $level - 1, $types);
        }

        return [$tree, $processedNodes];
    }

    private static function createSheet($name, $idNodeType, $isFolder, $isVirtualFolder, $types)
    {
        if (in_array($idNodeType, array_values($types)) || $isFolder || $isVirtualFolder) {
            $ele = ($isFolder || $isVirtualFolder) &&
            !in_array($idNodeType, [NodeTypeConstants::XML_CONTAINER, NodeTypeConstants::HTML_CONTAINER])
                ? 'folder' : 'item';
            return ['name' => $name, 'type' => $ele];
        }
        return null;
    }
}
