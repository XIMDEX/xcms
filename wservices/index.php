<?php
use Ximdex\API\Request;
use Ximdex\API\Response;
use Ximdex\API\Router;
use Ximdex\Models\Node;
use Ximdex\Models\Language;
use Ximdex\Models\StructuredDocument;
use Ximdex\Models\User;
use Ximdex\Runtime\App;
use Ximdex\Services\NodeType;
use Ximdex\Utils\Session;

include_once '../bootstrap/start.php';

$router = new Router;
/**
 * Route to search any resource in XSIR Repositories
 * @param q The query
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
 * @param offset (optional) The offset
 * @param size (optional) The max number of results
 */
$router->Route('/books', function(Request $r, Response $w){
    ModulesManager::file('/actions/composer/Action_composer.class.php');

    $offset = $r->Get('offset', true, 0);
    $size = $r->Get('size', true, 50);

    $ac = new Action_composer();
    $resp = $ac->quickReadWithNodetype(10000, NodeType::XBUK_PROJECT, $offset, $size, null, 1);

    $w->setResponse(isset($resp["collection"]) ? $resp["collection"] : []);
});

/**
 * Route to list all sections of a book
 * @param id The book id
 * @param offset (optional) The offset
 * @param size (optional) The max number of results
 */
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

/**
 * Route to get info about a DAM node
 * @param id The DAM node id
 */

$router->Route('/DAM/\d+/info', function(Request $r, Response $w){
    $nodeId = $r->getPath()[1];
    $node = new Node($nodeId);

    $allowedNodetypes = [NodeType::XSIR_BINARY_FILE, NodeType::XSIR_IMAGE_FILE, NodeType::XSIR_VIDEO_FILE, NodeType::XSIR_TEXT_FILE, NodeType::XSIR_WIDGET_FILE];
    if(!in_array($node->GetNodeType(), $allowedNodetypes)){
        $w->setMessage('Id is not for a valid DAM file')->setStatus(-1);
        return;
    }

    $resp = [];
    $info = $node->GetLastVersion();
    $resp['idversion'] = $info['IdVersion'];
    $resp['version'] = $info['Version'];
    $resp['subversion'] = $info['SubVersion'];
    $resp['date'] = $info['Date'];

    $resp['url'] = App::get('UrlRoot') . '/data/files/' . $info['File'];

    $resp['id'] = $node->IdNode;

    $resp['idnode'] = $node->IdNode;
    $resp['idnodetype'] = $node->IdNodeType;
    $resp['idparent'] = $node->IdParent;
    $resp['name'] = $node->Name;
    $resp['path'] = $node->GetPath();

    // TODO: Filter text files to add content
    //$resp['content'] = $node->GetContent();

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
});
