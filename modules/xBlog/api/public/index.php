<?php

use Cocur\Slugify\Slugify;
use Intervention\Image\ImageManagerStatic as Image;
use Symfony\Component\Yaml\Parser;
use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

require '../vendor/autoload.php';

$config = readConfig();

// Fix to get Ximdex Url without db
$scriptUrl = $_SERVER['SCRIPT_URL'];
$scriptUrl = preg_replace('/\/modules\/xBlog\/api\/public.*/', "", $scriptUrl);
$port = $_SERVER['SERVER_PORT'] == 80 ? "" : ":" . $_SERVER['SERVER_PORT'];
$config['ximdexurl'] = $_SERVER['REQUEST_SCHEME'] . '://' .  $_SERVER['SERVER_NAME'] . $port . $scriptUrl;

$client = new GuzzleHttp\Client(['base_uri' => "{$config['ximdexurl']}/api/ "]);

$basePath = "";

$configuration = [
	'settings' => [
		'displayErrorDetails' => $config["mode"] == "dev",
	],
];

$container = new \Slim\Container($configuration);

// Register component on container
$container['view'] = function ($container) {
	$view = new \Slim\Views\Twig('.', [
//		'cache' => './cache',
	]);
	$view->addExtension(new \Slim\Views\TwigExtension(
		$container['router'],
		$container['request']->getUri()
	));

	return $view;
};
$app = new \Slim\App($container);

$app->any($basePath . '/new', function (Request $request, Response $response, $args) use ($config, $client) {
	$values = [];
	$values['content'] = '<p>Escriba aquí su contenido</p>';
	$values['mode'] = 'new';
	$values["urlCSSTheme"] = $config["css_url_published"];
	$values["urlBase"] = dirname($_SERVER['SCRIPT_URL']);
	return $this->view->render($response, 'start.html', $values);
});

$app->any($basePath . '/getImages', function (Request $request, Response $response, $args) use ($config, $client, $app) {
	//return $response->withStatus(301)->withHeader('Location', '/11428');

	$respLogin = login($client, $config);

	if ($respLogin['error'] != 0) {
		$response->withStatus(500);
		return;
	}

	$token = $respLogin["data"]["ximtoken"];

	$respInfo = getInfo($client, $token, $config['images_folder_id']);

	if ($respInfo['error'] != 0) {
		$response->withStatus(500);
		return;
	}

	$resp = $respInfo['data']['children'];

	$response->getBody()->write(json_encode($resp, true));
});

$app->any($basePath . '/new/save', function (Request $request, Response $response, $args) use ($config, $client, $app, $basePath) {
	//return $response->withStatus(301)->withHeader('Location', '/11428');

	$respLogin = login($client, $config);

	if ($respLogin['error'] != 0) {
		$response->withStatus(500);
		return;
	}

	$token = $respLogin["data"]["ximtoken"];

	$parsedBody = $request->getParsedBody();

	$slugify = new Slugify();
	$title = $slugify->slugify($parsedBody['title']);

	$respCreate = createxml($client, $token, $config['posts_container_id'], $title, $config['schema_id']);

	if ($respCreate['error'] == 0 && isset($respCreate['data']) && isset($respCreate['data']['container_langs']) && isset($respCreate['data']['container_langs']['es']['nodeid'])) {
		$docId = $respCreate['data']['container_langs']['es']['nodeid'];

		saveOrPublish($request, $response, ['nodeid' => $docId], $client, $config, false);
		$resp = [
			"redirect" => dirname(dirname($_SERVER['SCRIPT_URL'])) . "/$docId",
			"reloadnode" => "{$config['posts_container_id']}",
		];
		$response->getBody()->write(json_encode($resp, true));
		return;
		return $response->withStatus(301)->withHeader('Location', $basePath . "/$docId");
	}
	$response->withStatus(500);

});

$app->post($basePath . '/upload-image', function (Request $request, Response $response, $args) use ($config, $client) {
	$parsedBody = $request->getParsedBody();
	$image = $_FILES['image'];

	if(false === exif_imagetype($image)) {
		// It's not a image
		$response->withStatus(500);
		return;
	}

	$left = isset($parsedBody["left"]) ? (int) $parsedBody["left"] : null;
	$top = isset($parsedBody["top"]) ? (int) $parsedBody["top"] : null;
	$width = isset($parsedBody["width"]) ? (int) $parsedBody["width"] : null;
	$height = isset($parsedBody["height"]) ? (int) $parsedBody["height"] : null;

	$info = new SplFileInfo($image["name"]);
	$ext = $info->getExtension();
	$basename = $info->getBasename(".$ext");

	$slugify = new Slugify();
	$imageName = $slugify->slugify(date('Y-m-d-H-i-s') . "-" . $basename) . ".$ext";

	Image::configure(array('driver' => 'gd'));
	$img = Image::make($image["tmp_name"]);

	if(!is_null($left) && !is_null($left) && !is_null($left) && !is_null($left)){
		// thumb
		$img->crop($width, $height, $left, $top);
		$img->widen(300, function ($constraint) {
			$constraint->upsize();
		});
	}else{
		// post img
		$img->widen(1024, function ($constraint) {
			$constraint->upsize();
		});
	}

	$content = (string) $img->save($image["tmp_name"]);

	$content = file_get_contents($image["tmp_name"]);

	$images_id = $config['images_folder_id'];

	$respLogin = login($client, $config);

	if ($respLogin['error'] != 0) {
		$response->withStatus(500);
		return;
	}

	$token = $respLogin["data"]["ximtoken"];

	$respCreate = createImage($client, $token, $images_id, $imageName);

	if ($respCreate['error'] != 0) {
		$response->withStatus(500);
		return;
	}

	$nodeid = $respCreate['data']['nodeid'];

	$respSetContent = setContent($client, $token, $nodeid, $content);

	$respInfo = getInfo($client, $token, $nodeid);

	if ($respInfo['error'] != 0) {
		$response->withStatus(500);
		return;
	}

	$resp = [
		'url' => $respInfo['data']['url'],
		'size' => getimagesize($image["tmp_name"]),
		'id' => $nodeid,
	];

	$response->getBody()->write(json_encode($resp, true));

});

$app->any($basePath . '/{nodeid}', function (Request $request, Response $response, $args) use ($config, $client) {
	$respLogin = login($client, $config);

	if ($respLogin['error'] != 0) {
		$response->withStatus(500);
		return;
	}

	$token = $respLogin["data"]["ximtoken"];

	$nodeid = $args['nodeid'];
	$respGetContent = getContent($nodeid, $client, $token);

	$content = $respGetContent['data'];

	$values = prepareContentToBeEdited($content);
	$values['mode'] = 'edit';

	$values["urlCSSTheme"] = $config["css_url_published"];
	$values["urlBase"] = dirname($_SERVER['SCRIPT_URL']);

	return $this->view->render($response, 'start.html', $values);
});

$app->post($basePath . '/{nodeid}/save', function (Request $request, Response $response, $args) use ($config, $client) {
	saveOrPublish($request, $response, $args, $client, $config);
})->setName('savePost');

$app->any($basePath . '/{nodeid}/info', function (Request $request, Response $response, $args) use ($config, $client) {
	$respLogin = login($client, $config);

	if ($respLogin['error'] != 0) {
		$response->withStatus(500);
		return;
	}

	$token = $respLogin["data"]["ximtoken"];

	$nodeid = $args['nodeid'];
	$info = info($client, $token, $nodeid);

	$response->getBody()->write(json_encode($info, JSON_UNESCAPED_UNICODE));
});

$app->run();

function readConfig() {
	$yaml = new Parser();
	return $yaml->parse(file_get_contents('../config.yml'));
}

/**
 * @return mixed
 */
function login($client, $config) {
	try {
		$resp = $client->post('login', [
			'form_params' => ['user' => $config['user'], 'pass' => $config['pass']],
		]);
	} catch (BadResponseException $e) {
		return ['error' => 1];
	}
	$respLogin = json_decode($resp->getBody(), true);
	return $respLogin;
}

/**
 * @param $client
 * @param $token
 * @param $nodeid
 * @param $content
 * @return mixed
 */
function setContent($client, $token, $nodeid, $content) {
	try {
		$resp = $client->post('node/content', [
			'form_params' => [
				'ximtoken' => $token,
				'nodeid' => $nodeid,
				'content' => urlencode(base64_encode($content)),
			],
		]);
	} catch (BadResponseException $e) {
		return ['error' => 1];
	}
	return json_decode($resp->getBody(), true);
}

/**
 * @param $client
 * @param $token
 * @param $nodeid
 * @param $content
 * @return mixed
 */
function setContentXML($client, $token, $nodeid, $content) {
	try {
		$resp = $client->post('node/contentxml', [
			'form_params' => [
				'ximtoken' => $token,
				'nodeid' => $nodeid,
				'content' => urlencode(base64_encode($content)),
			],
		]);
	} catch (BadResponseException $e) {
		return ['error' => 1];
	}
	return json_decode($resp->getBody(), true);
}

/**
 * @param $args
 * @param $client
 * @param $token
 * @return mixed
 */
function getContent($nodeid, $client, $token) {
	try {
		$respContentRaw = $client->post('node/getcontent', [
			'form_params' => ['ximtoken' => $token, 'nodeid' => $nodeid],
		]);
	} catch (BadResponseException $e) {
		return ['error' => 1];
	}

	$respGetContent = json_decode($respContentRaw->getBody(), true);
	return $respGetContent;
}

/**
 * @param $client
 * @param $token
 * @param $images_id
 * @param $imageName
 */
function createImage($client, $token, $images_id, $imageName) {
	$respCreateRaw = $client->post('node/create', [
		'form_params' => [
			'ximtoken' => $token,
			'nodeid' => $images_id,
			'nodetype' => 5040,
			'name' => $imageName,
		],
	]);
	return json_decode($respCreateRaw->getBody(), true);
}

function getInfo($client, $token, $nodeid) {
	try {
		$respInfoRaw = $client->post('node', [
			'form_params' => [
				'ximtoken' => $token,
				'nodeid' => $nodeid,
			],
		]);
	} catch (BadResponseException $e) {
		return ['error' => 1];
	}
	return json_decode($respInfoRaw->getBody(), true);
}

function publish($client, $token, $nodeid) {
	try {
		$respInfoRaw = $client->post('node/publish', [
			'form_params' => [
				'ximtoken' => $token,
				'nodeid' => $nodeid,
			],
		]);
	} catch (BadResponseException $e) {
		return ['error' => 1];
	}
	return json_decode($respInfoRaw->getBody(), true);
}

function info($client, $token, $nodeid) {
	try {
		$respInfoRaw = $client->post('node/info', [
			'form_params' => [
				'ximtoken' => $token,
				'nodeid' => $nodeid,
			],
		]);
	} catch (BadResponseException $e) {
		return ['error' => 1];
	}
	return json_decode($respInfoRaw->getBody(), true);
}

function createxml($client, $token, $parentid, $name, $idSchema) {
	global $config;
	try {
		$respInfoRaw = $client->post('node/createxml', [
			'form_params' => [
				'ximtoken' => $token,
				'nodeid' => $parentid,
				'channels' => $config['ch_html_id'] . "," . $config['ch_solr_id'],
				'name' => $name,
				'id_schema' => $idSchema,
				'languages' => "es",
			],
		]);
	} catch (BadResponseException $e) {
		return ['error' => 1];
	}
	return json_decode($respInfoRaw->getBody(), true);
}

function prepareContentToBeEdited($content) {
	$doc = new DOMDocument;
	@$doc->loadHTML($content);
	$xpath = new DOMXPath($doc);
	$query = '/html/body/x-meta';
	$xmetas = $xpath->query($query);

	$resp = [];
	for ($i = 0; $i < $xmetas->length; $i++) {
		$xmeta = $xmetas->item($i);
		$key = $xmeta->getAttribute("data-key");
		$value = utf8_decode($xmeta->getAttribute("data-value"));
		$resp[$key] = $value;
		if ($key == 'image') {
			$resp['xid'] = $xmeta->getAttribute("data-xid");
		}
		$xmeta->parentNode->removeChild($xmeta);
	}

	$tidy = new tidy();
	$content = $tidy->repairString($content, array(
		'output-xhtml' => true,
		'show-body-only' => true,
	), 'utf8');

	$resp['content'] = $content;

	return $resp;
}

function prepareContentToBeSaved($parsedBody, $idThumb) {
	$doc = new DOMDocument;
	$doc->loadHTML(mb_convert_encoding($parsedBody['content'], 'HTML-ENTITIES', 'UTF-8'));

	$xpath = new DOMXPath($doc);
	$query = '/html/body';
	$body = $xpath->query($query);
	if ($body->length == 0) {
		return null;
	}

	$bodyE = $body->item(0);

	$xMetaTitle = $doc->createElement('x-meta');
	$xMetaTitle->setAttribute('data-key', 'title');
	$xMetaTitle->setAttribute('data-value', $parsedBody['title']);
	$bodyE->insertBefore($xMetaTitle, $bodyE->firstChild);

	$xMetaIntro = $doc->createElement('x-meta');
	$xMetaIntro->setAttribute('data-key', 'intro');
	$xMetaIntro->setAttribute('data-value', $parsedBody['intro']);
	$bodyE->insertBefore($xMetaIntro, $bodyE->firstChild);

	$xMetaDate = $doc->createElement('x-meta');
	$xMetaDate->setAttribute('data-key', 'date');
	$xMetaDate->setAttribute('data-value', $parsedBody['date']);
	$bodyE->insertBefore($xMetaDate, $bodyE->firstChild);

	$xMetaImage = $doc->createElement('x-meta');
	$xMetaImage->setAttribute('data-key', 'image');
	$xMetaImage->setAttribute('data-xid', $idThumb);
	$bodyE->insertBefore($xMetaImage, $bodyE->firstChild);
	$content = $doc->saveHTML();
	$tidy = new tidy();

	$content = utf8_encode($content);

	$content = $tidy->repairString($content, array(
		'output-xhtml' => true,
		'show-body-only' => true,
		'new-inline-tags' => 'x-meta',
		'quote-nbsp' => false,
		'preserve-entities' => false,
		'quote-ampersand' => false,
	), 'utf8');

	return $content;
}

/**
 * @param Request $request
 * @param Response $response
 * @param $args
 * @param $client
 * @param $config
 */
function saveOrPublish(Request $request, Response $response, $args, $client, $config, $write = true) {
	$parsedBody = $request->getParsedBody();

	$idThumb = isset($parsedBody['imagexid']) ? $parsedBody['imagexid'] : "";

	$respLogin = login($client, $config);

	if ($respLogin['error'] != 0) {
		$response->withStatus(500);
		return;
	}

	$token = $respLogin["data"]["ximtoken"];

	if (!isset($parsedBody['content'])) {
		$response->withStatus(500);
		return;
	}

	$content = prepareContentToBeSaved($parsedBody, $idThumb);

	if (is_null($content)) {
		$response->withStatus(500);
		return;
	}

	$nodeid = $args['nodeid'];
	$tidy = new tidy();

	$resp = setContentXML($client, $token, $nodeid, $content);

	if ($parsedBody["publish"] != "true") {
		error_log("no publica");
		if($write){
			$response->getBody()->write(json_encode($resp, JSON_UNESCAPED_UNICODE));
		}
		return;
	}

	$respPub = publish($client, $token, $nodeid);
	if($write){
		$response->getBody()->write(json_encode($respPub, JSON_UNESCAPED_UNICODE));
	}
}
