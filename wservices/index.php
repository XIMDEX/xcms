<?php
use Ximdex\API\Request;
use Ximdex\API\Response;
use Ximdex\API\Router;
use Ximdex\Models\Node;
use Ximdex\Models\User;
use Ximdex\Services\NodeType;
use Ximdex\Utils\Session;

include_once '../bootstrap/start.php';

$router = new Router;
/**
 * Route to search any resource in XSIR Repositories
 */
$router->Route('/search', function(Request $request, Response $response){
    $q = $request->Get('q');
    ModulesManager::file('/src/SolrSearchManager.php', 'XSearch');
    $sm = new SolrSearchManager();

    $response->setStatus(0)->setMessage('');
    $response->setResponse($sm->search($q));
});

/**
 * Route to get user info
 */
$router->Route('/me', function(Request $r, Response $w){
    $userID = (int) Session::get('userID');
    $user = new User($userID);
    $locale = $user->get('Locale');
    $locale = !is_null($locale) ? $locale : 'en_US';
    $response = [
        'id' => $userID,
        'username' => $user->get('Login'),
        'name' => $user->get('Name'),
        'email' => $user->get('Email'),
        'locale' => $locale,
    ];
    $w->setResponse($response);
});

/**
 * Route to list all books. We don't search permission in general group.
 */
$router->Route('/books', function(Request $r, Response $w){
    ModulesManager::file('/actions/composer/Action_composer.class.php');

    $offset = $r->Get('offset', true, 0);
    $size = $r->Get('size', true, 50);

    $ac = new Action_composer();
    $resp = $ac->quickReadWithNodetype(10000, NodeType::XBUK_PROJECT, $offset, $size, null, 1);

    $w->setResponse(isset($resp["collection"]) ? $resp["collection"] : []);
});

$router->Route('/books/\d+/sections', function(Request $r, Response $w){
    $nodeId = $r->getPath()[1];
    $node = new Node($nodeId);
    if($node->GetNodeType() != NodeType::XBUK_PROJECT){
        $w->setMessage('Id is not for a valid book project')->setStatus(-1);
        return;
    }

    ModulesManager::file('/actions/composer/Action_composer.class.php');

    $offset = $r->Get('offset', true, 0);
    $size = $r->Get('size', true, 50);

    $ac = new Action_composer();
    $resp = $ac->quickReadWithNodetype($nodeId, NodeType::XBUK_SESSION, $offset, $size, null, 1);

    $w->setResponse(isset($resp["collection"]) ? $resp["collection"] : []);
});

