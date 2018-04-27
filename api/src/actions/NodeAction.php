<?php

namespace XimdexApi\actions;

use Ximdex\Models\Node;
use XimdexApi\core\Request;
use XimdexApi\core\Response;


class NodeAction extends Action
{

    const PREFIX = 'node';

    const ROUTE_GET = 'infonode';

    protected const ROUTES = [
        self::ROUTE_GET => 'infonode',
    ];

    /********************************************* API METHODS *********************************************/

    /**
     * @param Request $r
     * @param Response $w
     */
    public static function infonode(Request $r, Response $w)
    {
        $pathElements = explode('/', $r->getPath());
        $nodeId = $_GET['id'];
        $response = null;

        if (!is_null($nodeId) && ctype_digit($nodeId)) {
            $node = new Node($nodeId);
            if ($node->GetID()) {
                $response = $node->loadData(true);
            }
        }

        if (is_null($response)) {
            $w->setMessage('Node info not found')->setStatus(1);
        }

        $w->setResponse($response);
        $w->send();
    }

}