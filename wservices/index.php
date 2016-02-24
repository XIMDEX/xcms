<?php
use Ximdex\API\APIException;
use Ximdex\API\Request;
use Ximdex\API\Response;
use Ximdex\API\Router;
use Ximdex\Models\Group;
use Ximdex\Models\Node;
use Ximdex\Models\Language;
use Ximdex\Models\StructuredDocument;
use Ximdex\Models\User;
use Ximdex\Runtime\App;
use Ximdex\Services\NodeType;
use Ximdex\Utils\Session;

include_once '../bootstrap/start.php';

$router = new Router( new Request()  ) ;

$router->addAllowedRequest( "ping") ;

$router->addRoute( 'bad', "none" );



$router->addRoute( 'ping', function(Request $r, Response $w ) {
    $w->setStatus( 0) ;
    $w->setMessage( "" ) ;
    $w->setResponse( "PONG!") ;
    $w->send();
});

/**
 * Route to search any resource in XSIR Repositories
 * @param offset (optional) The offset
 * @param limit (optional) The max number of results
 */
$router->addRoute('search', function(Request $r, Response $w){
    $q = $r->get('q');
    ModulesManager::file('/src/SolrSearchManager.php', 'XSearch');

    $offset = $r->get('offset', true, 0);
    $limit = $r->get('limit', true, 10);

    $sm = new SolrSearchManager();

    $results = $sm->search($q, $offset, $limit);

    $resultsToSend = [];

    foreach($results['docs'] as $k => $result){
        $resultToSend = [
            'id' => $result['IdNode'],
            'name' => $result['Name'],
        ];
        switch($result['IdNodeType']){
            case NodeType::XSIR_IMAGE_FILE:
                $resultToSend['type'] = 'image';
                break;
            case NodeType::XSIR_VIDEO_FILE:
                $resultToSend['type'] = 'video';
                break;
            case NodeType::XSIR_TEXT_FILE:
                $resultToSend['type'] = 'text';
                break;
            case NodeType::XSIR_WIDGET_FILE:
                $resultToSend['type'] = 'widget';
                break;
            case NodeType::XSIR_BINARY_FILE:
                $resultToSend['type'] = 'binary';
                break;
        }
        $resultsToSend[] = $resultToSend;
    }

    $w->setResponse($resultsToSend);
    $w->send();
});


/**
 * Route to get user info
 */
$router->addRoute('me', function(Request $r, Response $w){
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
    $w->send();
});

/**
 * Route to list all books. We don't search permission in general group.
 * @param offset (optional) The offset
 * @param limit (optional) The max number of results
 */
$router->addRoute('books', function(Request $r, Response $w){
    ModulesManager::file('/actions/composer/Action_composer.class.php');

    $offset = $r->get('offset', true, 0);
    $limit = $r->get('limit', true, 50);

    $ac = new Action_composer();
    $resp = $ac->quickReadWithNodetype(10000, NodeType::XBUK_PROJECT, $offset, $limit, null, 1);

    $booksToSend = [];
    if(isset($resp["collection"])){
        foreach($resp["collection"] as $node){
            $booksToSend[] = [
                'id' => (int) $node['nodeid'],
                'name' => $node['name'],
            ];
        }
    }

    $w->setResponse($booksToSend);
    $w->send();
});

/**
 * Route to list all sections of a book
 * @param id The book id
 * @param offset (optional) The offset
 * @param limit (optional) The max number of results
 */
$router->addRoute('book/\d+/sections', function(Request $r, Response $w){
    $nodeId = $r->getPath()[1];
    $node = new Node($nodeId);
    if($node->GetNodeType() != NodeType::XBUK_PROJECT){
        $w->setMessage('Id is not for a valid book project')->setStatus(1);
        return;
    }

    ModulesManager::file('/actions/composer/Action_composer.class.php');

    $offset = $r->get('offset', true, 0);
    $limit = $r->get('limit', true, 50);

    $ac = new Action_composer();
    $resp = $ac->quickReadWithNodetype($nodeId, NodeType::XBUK_SESSION, $offset, $limit, null, 1);

    $booksToSend = [];
    if(isset($resp["collection"])){
        foreach($resp["collection"] as $node){
            $booksToSend[] = [
                'id' => (int) $node['nodeid'],
                'name' => $node['name'],
            ];
        }
    }

    $w->setResponse($booksToSend);
    $w->send();

});

/**
 * Route to get info about a DAM node
 * @param id The DAM node id
 */

$router->addRoute('DAM/\d+/info', function(Request $r, Response $w){
    $nodeId = $r->getPath()[1];
    $node = new Node($nodeId);

    $allowedNodetypes = [NodeType::XSIR_BINARY_FILE, NodeType::XSIR_IMAGE_FILE, NodeType::XSIR_VIDEO_FILE, NodeType::XSIR_TEXT_FILE, NodeType::XSIR_WIDGET_FILE];
    if(!in_array($node->GetNodeType(), $allowedNodetypes)){
        $w->setMessage('Id is not for a valid DAM file')->setStatus(1);
        return;
    }

    $resp = [];
    $info = $node->GetLastVersion();
    $resp['last_modified'] = $info['Date'];

    $resp['url'] = App::get('UrlRoot') . '/data/files/' . $info['File'];

    $resp['id'] = (int) $node->IdNode;

    $resp['name'] = $node->Name;

    $mm = new MetadataManager($node->IdNode);
    $metadata_nodes = $mm->getMetadataNodes();

    foreach ($metadata_nodes as $metadata_node_id) {
        $structuredDocument = new StructuredDocument($metadata_node_id);
        $idLanguage = $structuredDocument->get('IdLanguage');
        $language = new Language($idLanguage);
        $langIsoName = $language->GetIsoName();
        $metadata_node = new Node($metadata_node_id);
        $contentMetadata = $metadata_node->getContent();
        $domDoc = new DOMDocument();
        if ($domDoc->loadXML("<root>" . $contentMetadata . "</root>")) {
            $xpathObj = new DOMXPath($domDoc);
            $custom_info = $xpathObj->query("//custom_info/*");
            if ($custom_info->length > 0) {
                foreach ($custom_info as $value) {
                    if(!isset($resp['metadata'])){
                        $resp['metadata'] = [];
                    }
                    if(!isset($resp['metadata'][$langIsoName])){
                        $resp['metadata'][$langIsoName] = [];
                    }
                    $resp['metadata'][$langIsoName][$value->nodeName] = $value->nodeValue;
                }
            }
            $file_data = $xpathObj->query("//file_data/*");
            if ($file_data->length > 0) {
                foreach ($file_data as $value) {
                    $name = "{$value->nodeName}";
                    $resp[$name] = $value->nodeValue;
                }
            }
            $tagsNodes = $xpathObj->query("//tags/*");
            if ($tagsNodes->length > 0) {
                $tags = [];
                foreach ($tagsNodes as $tag) {
                    $tags[] = $tag->nodeValue;
                }
                $resp['tags'] = $tags;
            }
        }
    }
    $w->setResponse($resp);
    $w->send();
});

/**
 * Get the users related with a book
 * @param id The book id
 */
$router->addRoute('book/\d+/users', function(Request $r, Response $w){
    $nodeId = $r->getPath()[1];
    $node = new Node($nodeId);

    if($node->GetNodeType() != NodeType::XBUK_PROJECT){
        throw new APIException('Id is not for a valid book', 1);
    }

    $groups = $node->GetGroupList();

    // We won't use general group
    if(($key = array_search('101', $groups)) !== false) {
        array_splice($groups, $key, 1);
    }

    $idUsers = [];
    foreach($groups as $q){
        $group = new Group($q);
        $idUsers = array_merge($idUsers, $group->GetUserList());
    }
    $idUsers = array_unique($idUsers);

    //Check if I have permissions
    $myUserID = Session::get('userID');
    if(!in_array($myUserID, $idUsers)){
        throw new APIException('You haven\'t got enough permissions to see this', -2);
    }

    $users = [];
    foreach($idUsers as $idUser){
        $user = new User($idUser);
        $locale = $user->get('Locale');
        $locale = !is_null($locale) ? $locale : 'en_US';
        $users [] = [
            'id' => (int) $idUser,
            'username' => $user->get('Login'),
            'name' => $user->get('Name'),
            'email' => $user->get('Email'),
            'locale' => $locale,
        ];
    }

    $w->setResponse($users);
    $w->send();
});

 $router->execute() ;

