<?php

use Ximdex\Models\Language;
use Ximdex\Models\Node;
use Ximdex\Models\StructuredDocument;
use Ximdex\Runtime\App;
use DB_legacy as DB;
use Ximdex\Services\NodeType;

ModulesManager::file('/inc/metadata/MetadataManager.class.php');
ModulesManager::file('/src/Exporter.php', 'XSearch');

class SolrExporter implements Exporter
{
    const AVOIDED_NODETYPES = [NodeType::METADATA_DOCUMENT, NodeType::RNG_VISUAL_TEMPLATE];

    /**
     * @var Solarium\Core\Client\Client
     */
    private $client;
    private $server;
    private $port;
    private $path;
    private $core;

    public function __construct()
    {
        $this->GetClient();
    }

    private function GetClient()
    {
        $this->server = App::get('SolrServer');
        $this->core = App::get('SolrCore');
        $this->port = App::get('SolrPort');
        $this->path = App::get('SolrPath');

        $config = [
            'endpoint' =>
                ['localhost' =>
                    ['host' => $this->server,
                        'port' => $this->port,
                        'path' => $this->path,
                        'core' => $this->core
                    ]
                ]
        ];
        $this->client = new Solarium\Client($config);
    }

    public function DeleteAll()
    {
        $update = $this->client->createUpdate();
        $update->addDeleteQuery('*:*');
        $update->addCommit();
        $result = $this->client->update($update);

    }

    public function ExportAll()
    {
        //Select all XSIR repo
        $node = new Node();
        $XSIRIdNodes = $node->find('IdNode', 'IdNodeType = %s', ['9010'], MONO);

        $sql = "SELECT n.IdNode FROM FastTraverse f INNER JOIN Nodes n on f.IdChild = n.IdNode INNER JOIN NodeTypes nt ON nt.IdNodeType = n.IdNodeType WHERE f.IdNode in (%s) AND nt.IsPlainFile AND nt.IdNodeType NOT IN (%s)";

        $sql = sprintf($sql, implode(',', $XSIRIdNodes), implode(',', self::AVOIDED_NODETYPES));
        $db = new DB();
        $db->query($sql);
        $update = $this->client->createUpdate();
        $docs = [];
        while (!$db->EOF) {
            $n = new Node($db->getValue('IdNode'));

            $doc = $this->GetDataNode($update, $n);

            $docs[] = $doc;

            $db->Next();
        }
        $update->addDocuments($docs);
        $update->addCommit();
        $result = $this->client->update($update);

    }

    public function ExportByNodeId($nodeid)
    {
        $n = new Node($nodeid);

        if (in_array($n->IdNodeType, self::AVOIDED_NODETYPES) && $n->IsOnNodeWithNodeType(NodeType::XSIR_REPOSITORY)) {
            return;
        }

        $update = $this->client->createUpdate();

        $doc = $this->GetDataNode($update, $n);

        $update->addDocuments([$doc]);
        $update->addCommit();
        $result = $this->client->update($update);

        $docs[] = $doc;
    }

    /**
     *
     * @param $update
     * @param $node
     * @return mixed
     */
    private function GetDataNode($update, $node)
    {
        $doc = $update->createDocument();

        $info = $node->GetLastVersion();
        $doc->IdVersion = $info['IdVersion'];
        $doc->Version = $info['Version'];
        $doc->SubVersion = $info['SubVersion'];
        $doc->Date = $info['Date'];

        $doc->id = $node->IdNode;
        $doc->IdNode = $node->IdNode;
        $doc->IdNodeType = $node->IdNodeType;
        $doc->IdParent = $node->IdParent;
        $doc->Name = $node->Name;
        $doc->Path = $node->GetPath();
        $doc->Content = $node->GetContent();

        $mm = new MetadataManager($node->IdNode);
        $metadata_nodes = $mm->getMetadataNodes();

        $metadata = [];
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
                        $name = "{$value->nodeName}_metadata_{$langIsoName}";
                        $doc->$name = $value->nodeValue;
                    }
                }
            }
        }
        return $doc;
    }

    public function CreateSchema()
    {
        $command = [
            'add-field' => [
                ["name" => 'idversion', "type" => 'int'],
                ["name" => 'version', "type" => 'int'],
                ["name" => 'subversion', "type" => 'int'],
                ["name" => 'date', "type" => 'int'],
                ["name" => 'idnode', "type" => 'int'],
                ["name" => 'idnodetype', "type" => 'int'],
                ["name" => 'idparent', "type" => 'int'],
                ["name" => 'name', "type" => 'text_es'],
                ["name" => 'path', "type" => 'string'],
                ["name" => 'content', "type" => 'text_en']
            ],
            'add-dynamic-field' => [
                ["name" => '*_metadata_es', "type" => 'text_es'],
                ["name" => '*_metadata_en', "type" => 'text_en']
            ]
        ];

        echo json_encode($command);

        $this->launchCommand("/schema", $command);
    }

    public function DeleteSchema(){
        $resp = $this->launchCommand("/schema/fields", [], "GET");

        $command = [
            'delete-field' => []
        ];

        if(!isset($resp->fields) || count($resp) == 0){
            return true;
        }

        foreach($resp->fields as $field){
            if(in_array($field->name, ['_root_', '_text_', '_version_', 'id'])){
                continue;
            }
            $command['delete-field'][] = ['name' => $field->name];
        }

        $command['delete-dynamic-field'] = [
            ['name' => '*_metadata_es'],
            ['name' => '*_metadata_en'],
        ];

        $this->launchCommand("/schema", $command);


    }

    /**
     * @param string $endPoint string
     * @param array $command
     * @param string $method
     * @return mixed
     */
    private function launchCommand($endPoint, $command = [], $method = "POST")
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_PORT => "8983",
            CURLOPT_URL => "http://{$this->server}:{$this->port}{$this->path}{$this->core}{$endPoint}",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_POSTFIELDS => json_encode($command),
            CURLOPT_HTTPHEADER => array(
                "cache-control: no-cache",
                "content-type: application/json",
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            echo "cURL Error #:" . $err;
            return null;
        } else {
            echo $response;
            return json_decode($response);
        }

    }

}