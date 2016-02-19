<?php

use Solarium\QueryType\Select\Result\Result;

ModulesManager::file('/src/SolrConnection.php', 'XSearch');

class SolrSearchManager
{

    private $client;
    public function __construct()
    {
        $solr = new SolrConnection();
        $this->client = $solr->GetClient();
    }

    public function search($q, $offset = 0, $limit = 10){
        $query = $this->client->createSelect();
        $helper = $query->getHelper();
        $query->setQuery($helper->escapePhrase($q));
        $query->setStart($offset);
        $query->setRows($limit);
        $query->setFields(['idnode','name','path','idnodetype']);
        return $this->prepareResults($this->client->select($query));
    }

    private function prepareResults(Result $resultset){
        $res= [];

        $docs = [];
        foreach($resultset as $doc) {
            $newDoc = [];

            $newDoc['IdNode'] = $doc->idnode;
            $newDoc['Name'] = $doc->name;
            $newDoc['Path'] = $doc->path;
            $newDoc['IdNodeType'] = $doc->idnodetype;

            $docs[] = $newDoc;
        }
        $res['total'] = $resultset->getNumFound();
        $res['docs'] = $docs;
        return $res;
    }
}