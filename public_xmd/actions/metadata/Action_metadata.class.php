<?php

use Ximdex\MVC\ActionAbstract;
use Ximdex\Models\Node;
use Ximdex\Models\Metadata;


class Action_metadata extends ActionAbstract
{
    public function index()
    {


        $idNode = $this->request->getParam('nodes')[0];
        $node = new Node($idNode);

        if ($node->GetID() != null) {
            $metadata = new Metadata();
            $metagroups = $metadata->getMetadataGroupByNodeType($node->GetNodeType());
            $values = array(
                'metagroups' => $metagroups,
                'metadata' => array(),
                'node_Type' => $node->nodeType->GetName(),
                'go_method' => 'saveMetadata',
                'nodeid' => $idNode,

            );

        } else {
            $this->messages->add(_('The operation has failed'), MSG_TYPE_ERROR);
            $values = array(
                'parentID' => $idNode,
                'messages' => $this->messages->messages
            );
            $this->sendJSON($values);
        }
        $this->addJs('/actions/metadata/resources/js/index.js');
        $this->render($values, 'index.tpl', 'default-3.0.tpl');
    }

    function saveMetadata()
    {

        $idNode = $this->request->getParam('nodeid');
        $idGroup = $this->request->getParam('idGroup');
        $metadataArray = $this->request->getParam('metadata');
        $resultAdd = false;

        $node = new Node($idNode);

        if ($node->GetID() != null) {

            $metadata = new Metadata();
            $groups = array_column($metadata->getMetadataGroupByNodeType($node->GetNodeType()), 'id');

            if (in_array($idGroup, $groups)) {
                $metadata->deleteMetadataValuesByNodeIdAndGroupId($idNode, $idGroup);
                $resultAdd = $metadata->addMetadataValuesByNodeId($metadataArray, $idNode);

                if ($resultAdd) {
                    $this->messages->add(_('The metadata has been successfully added'), MSG_TYPE_NOTICE);

                }
            }
        }

        if (!$resultAdd) {
            $this->messages->add(_('The operation has failed'), MSG_TYPE_ERROR);
        }

        $values = array(
            'parentID' => $idNode,
            'messages' => $this->messages->messages
        );

        $this->sendJSON($values);
    }


    function getMetadataByGroup()
    {
        $idGroup = $this->request->getParam('idGroup');
        $metadata = new Metadata();
        $metagroups = $metadata->getMetadataByMetagroup($idGroup);
        $values = array('metadata' => $metagroups);


        $this->render($values, 'metadata.tpl', 'only_template.tpl');


    }
}

