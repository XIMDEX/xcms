<?php

use Ximdex\Models\Language;
use Ximdex\Models\Node;
use Ximdex\Models\StructuredDocument;
use Ximdex\Runtime\Db;
use\Ximdex\NodeTypes\NodeType;

ModulesManager::file('/inc/metadata/MetadataManager.class.php');
ModulesManager::file('/src/Exporter.php', 'XSearch');
ModulesManager::file('/src/SolrConnection.php', 'XSearch');

class SolrExporter implements Exporter
{
    const AVOIDED_NODETYPES = [NodeType::METADATA_DOCUMENT, NodeType::RNG_VISUAL_TEMPLATE];

    private $client;
    public function __construct()
    {
        $solr = new SolrConnection();
        $this->client = $solr->GetClient();
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
        $XSIRIdNodes = $node->find('IdNode', 'IdNodeType = %s', [\Ximdex\NodeTypes\NodeType::XSIR_REPOSITORY], MONO);

        $sql = "SELECT n.IdNode FROM FastTraverse f INNER JOIN Nodes n on f.IdChild = n.IdNode INNER JOIN NodeTypes nt ON nt.IdNodeType = n.IdNodeType WHERE f.IdNode in (%s) AND nt.IsPlainFile AND nt.IdNodeType NOT IN (%s)";

        $sql = sprintf($sql, implode(',', $XSIRIdNodes), implode(',', self::AVOIDED_NODETYPES));
        $db = new \Ximdex\Runtime\Db();
        $db->query($sql);

        while (!$db->EOF) {

            $n = new Node($db->getValue('IdNode'));

            $update = $this->client->createUpdate();
            $doc = $this->GetDataNode($n);
            $update->addDocument($doc);
            $result = $this->client->update($update);
            $db->Next();

        }

        $update = $this->client->createUpdate();
        $update->addCommit();
        $result = $this->client->update($update);
    }

    public function ExportByNodeId($nodeid)
    {
        $n = new Node($nodeid);

        if($n->IdNodeType == NodeType::METADATA_DOCUMENT) {
            $n = $this->getSourceNodeFromMetadataDoc($nodeid);
            if(is_null($n)){
                return;
            }
        }

        if(!$n->IsOnNodeWithNodeType(NodeType::XSIR_REPOSITORY)){
            return;
        }

        $nt = new \Ximdex\Models\NodeType($n->IdNodeType);
        if($nt->isFolder()){
            foreach($n->GetChildren() as $idChild){
                $this->ExportByNodeId($idChild);
            }
            return;
        }

        if (in_array($n->IdNodeType, self::AVOIDED_NODETYPES) || !$nt->IsPlainFile) {
            return;
        }



        $update = $this->client->createUpdate();

        $doc = $this->GetDataNode($n);

        $update->addDocument($doc);
        try{
            $result = $this->client->update($update);
        }catch(Solarium\Exception\HttpException $e){
            error_log('Error al hacer la petición addDocument a Solr');
            error_log($e->getMessage());
        }

        $update = $this->client->createUpdate();
        $update->addCommit();

        try {
            $result = $this->client->update($update);
        }catch(Solarium\Exception\HttpException $e){
            error_log('Error al hacer la petición commit a Solr');
            error_log($e->getMessage());
        }
    }

    /**
     * @param $node
     * @return \Solarium\QueryType\Update\Query\Document\Document
     */
    private function GetDataNode($node)
    {
        $doc = new \Solarium\QueryType\Update\Query\Document\Document();
        $info = $node->GetLastVersion();
        $doc->idversion = $info['IdVersion'];
        $doc->version = $info['Version'];
        $doc->subversion = $info['SubVersion'];
        $doc->date = $info['Date'];

        $doc->id = $node->IdNode;

        $doc->idnode = $node->IdNode;
        $doc->idnodetype = $node->IdNodeType;
        $doc->idparent = $node->IdParent;
        $doc->name = $node->Name;
        $doc->path = $node->GetPath();

        // TODO: Filter text files to add content
        //$doc->content = $node->GetContent();

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
                        $name = "{$value->nodeName}_metadata_{$langIsoName}";
                        $doc->$name = $value->nodeValue;
                    }
                }
                $file_data = $xpathObj->query("//file_data/*");
                if ($file_data->length > 0) {
                    foreach ($file_data as $value) {
                        $name = "{$value->nodeName}_metadata_i";
                        $doc->$name = $value->nodeValue;
                    }
                }
                $tagsNodes = $xpathObj->query("//tags/*");
                if ($tagsNodes->length > 0) {
                    $tags_ss = [];
                    foreach ($tagsNodes as $tag) {
                        $tags_ss[] = $tag->nodeValue;
                    }
                    $doc->tags_ss = $tags_ss;
                }
            }
        }

        //$relTags = new RelTagsNodes();
        //$tags = $relTags->getTags($idNode);

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

    /**
     * @param $nodeid
     * @return Node
     */
    private function getSourceNodeFromMetadataDoc($nodeid)
    {
        $node = new Node($nodeid);
        $rnm = new RelNodeMetadata();
        $resp = $rnm->find('IdRel, IdNode', 'IdMetadata = %s', [$node->GetParent()], MULTI);
        if (count($resp) == 1) {
            $id = $resp[0]['IdNode'];
            $n = new Node($id);
            return $n;
        }
        return null;
    }

}