<?php

namespace XimdexApi\actions;

use Ximdex\Models\FastTraverse;
use Ximdex\Models\Node;
use Ximdex\NodeTypes\HTMLDocumentNode;
use Ximdex\NodeTypes\NodeTypeConstants;
use Ximdex\Runtime\App;
use Ximdex\Logger;
use XimdexApi\core\Request;
use XimdexApi\core\Response;


class XeditAction extends Action
{

    const PATTERN_PATHTO = "/[[:word:]]+=\"@@@RMximdex\.pathto\(([,-_#%=\.\w\s]+)\)@@@\"/";
    const PATTERN_XE_LINK = "/xe_link\=\"([^\"]*)\"/";

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
        $response = '';
        $name = '';

        $nodes = HTMLDocumentNode::getNodesHTMLDocument($nodeId);

        // Transform data to Xedit editor
        foreach ($nodes as &$node) {
            $node['editable'] = strcmp($node['type'], HTMLDocumentNode::CONTENT_DOCUMENT) == 0 ? true : false;
            $name = strcmp($node['type'], HTMLDocumentNode::CONTENT_DOCUMENT) == 0 ? $node['title'] : $name;
            $node['content'] = static::transformContentToXedit($node['content']);
            if (strcmp($node['type'], HTMLDocumentNode::CONTENT_DOCUMENT) == 0) {
                $schemas = $node['schema'];
                foreach ($schemas as $key => $value) {
                    $schemas[$key]['view'] = static::transformContentToXedit($value['view']);
                }
                $node['schema'] = $schemas;
            }
        }


        if ($nodes === false) {
            $w->setMessage('Document not found')->setStatus(1);
        } else {
            $response = [
                'baseUrl' => App::get('UrlHost') . App::get('UrlRoot') . '/api',
                'routerMapper' => [
                    'routes' => [
                        'resource' => '_action=' . XeditAction::getPath(XeditAction::ROUTE_FILE),
                        'treeInfo' => '_action=' . XeditAction::getPath(XeditAction::ROUTE_GET_TREE_INFO),
                    ]
                ],
                'name' => $name,
                'metas' => [
                    'title' => 'Ejemplo',
                    'tags' => 'ejemplo test prueba'
                ],
                'nodes' => $nodes
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
            $nodes = $data['nodes'];

            foreach ($nodes as $nodeId => $value) {
                if (isset($value['editable']) && $value['editable']) {
                    $node = new Node(intval(str_replace('xe_', '', $nodeId)));
                    $content = static::transformContentToXimdex($value['content']);
                    $node->SetContent($content, true);
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
            'image' => NodeTypeConstants::IMAGE_FILE,
            'link' => NodeTypeConstants::LINK
        ];

        $nodeId = isset($_GET['id']) ? $_GET['id'] : null;
        $type = isset($_GET['type']) ? $_GET['type'] : null;
        $type = isset($types[$type]) ? $types[$type] : false;
        $result = [];

        if (ctype_digit($nodeId) && $type !== false) {
            $children = FastTraverse::get_children($nodeId, ['node' => ['Name'], 'nodeType' =>
                ['isFolder', 'isVirtualFolder', 'IdNodeType']], 1, null, ['IsRenderizable' => true, 'IsHidden' => false]);
            $children = !isset($children[1]) ?: $children[1];
            foreach ($children as $key => $child) {
                if ($child['nodeType']['IdNodeType'] == $types[$type] || $child['nodeType']['isFolder'] ||
                    $child['nodeType']['isVirtualFolder']) {
                    $type = $child['nodeType']['isFolder'] || $child['nodeType']['isVirtualFolder'] ? 'folder' : 'item';
                    $result[$key] = ['name' => $child['node']['Name'], 'type' => $type];
                }
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
        $replace = 'xe_link="%s"';
        return sprintf($replace, trim($matches[1]));
    }

    /**
     * @param $matches
     * @return string
     */
    private static function transformXeLinkToPathto($matches)
    {
        //TODO Realizar comprobaciones si es IMG / LINK / ...
        $replace = 'src="@@@RMximdex.pathto(%s)@@@"';
        return sprintf($replace, trim($matches[1]), trim($matches[1]));
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
     * Check if the nodes have associated actions
     *
     * @param $nodes
     * @return null
     */
    protected function checkNodeAction(&$nodes)
    {
        //TODO Copiado de la clase Action_browser3 (Sacar en común)
        $db = new \Ximdex\Runtime\Db();
        $sql = 'select count(1) as total from Actions a left join Nodes n using(IdNodeType) where IdNode = %s and a . Sort > 0';
        $sql2 = $sql . " AND a.Command='fileupload_common_multiple' ";

        if (!empty($nodes)) {
            foreach ($nodes as &$node) {
                $nodeid = $node['nodeid'];
                $_sql = sprintf($sql, $nodeid);

                $db->query($_sql);
                $total = $db->getValue('total');
                $node['hasActions'] = $total;


                $db = new \Ximdex\Runtime\Db();
                $sql2 = sprintf($sql2, $nodeid);
                $db->query($sql2);
                $total = $db->getValue('total');
                $node['canUploadFiles'] = $total;
            }

            return $nodes;
        } else {
            Logger::info(_('Empty nodes in checkNodeAction [browser3]'));
            return null;
        }
    }

}