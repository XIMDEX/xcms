<?php
namespace Ximdex\MetaInfo;

use ModulesManager;
use RelNodeMetadata;
use Ximdex\Models\Node;
use Ximdex\Runtime\App;

ModulesManager::file('/inc/model/RelNodeMetadata.class.php');

class Manager
{

    // establecemos como container de los metadatos:

    private $nodetype = \Ximdex\Services\NodeType::TEXT_FILE;
    private $node = null;
    private $container = null;
    private $data = [];
    private $id = null ;

    // el nodetype: TEXT_FILE
    public function __construct($nodeid , $create = false )
    {

        $this->node  =new Node( $nodeid  ) ;

        $db = App::Db( );
        $stm = $db->prepare( 'select IdNode from Nodes where Name= ?  ' );
        $query= [ $this->getName()  ] ;

        $stm->execute( $query ) ;
        $metadata_container = null ;

        $row = $stm->fetch() ;
        if ( !empty( $row )) {
            $metadata_container = $row['IdNode'];

        }




        if ($metadata_container) {
            $this->container = new Node($metadata_container);
            $this->id = $this->container->GetID() ;
            $this->data = json_decode($this->container->getContent(), true);
        } else {
            if ( $create === true ) {
                $this->createContainer() ;
            }
            $this->data = array();
        }
    }

    /**
     * Returns the NodeId of metadata manager node 
     * @return string
     */
    public function getId() {
        return $this->id ;
    }

    public function getInfo( ) {
        $result =  json_decode( $this->container->GetContent(), true ) ;
        if ( empty( $result )) {
            $result = [];
        }
        return $result ;

    }
    public function setInfo( $data ) {

        $newData  = json_encode( $data , JSON_PRETTY_PRINT );
        $oldData = json_encode( $this->data , JSON_PRETTY_PRINT);

        if ( $newData != $oldData ) {
            $this->container->SetContent(  $newData ) ;
        }

    }

    public function createContainer()
    {


        $idm = $this->getParentContainerNode();

        $this->container = new Node( );
        $name = $this->getName() ;
        $this->id  = $this->container->CreateNode($name, $idm , $this->nodetype , null);
        $this->container->SetContent( "{}");
        $rnm = new RelNodeMetadata();
        $rnm->set('IdNode', $this->node->GetID());
        $rnm->set('IdMetadata', $this->id );
        $rnm->add();
    }

    private function getParentContainerNode()
    {

        //determinamos si este node puede contener metadata, si es asÃ­ es el container

        $db = App::Db( );
        $stm = $db->prepare( 'select * from NodeAllowedContents where IdNodeType = ? and  NodeType = ?' );
        $query= [ $this->node->GetNodeType() , $this->nodetype] ;
        $stm->execute( $query ) ;
        $row = $stm->fetch() ;
        if ( !empty( $row )) {
            return $this->node->GetID();

        }


        $idServer = $this->node->getServer();
        if ($idServer) {
            $nodeServer = new Node($idServer);
            $idSection = $nodeServer->GetChildByName("metadata");
            return $idSection;
        } else {
            $idProject = $this->node->getProject();
            $nodeProject = new Node($idProject);
            $idSection = $nodeProject->GetChildByName("metadata");
            return $idSection;
        }
    }


    private function getName()
    {
        $idnode = $this->node->GetID();
        return $idnode . "-metainfo";
    }

}