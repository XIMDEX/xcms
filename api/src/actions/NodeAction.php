<?php

/**
 *  \details &copy; 2018 Open Ximdex Evolution SL [http://www.ximdex.org]
 *
 *  Ximdex a Semantic Content Management System (CMS)
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as published
 *  by the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  See the Affero GNU General Public License for more details.
 *  You should have received a copy of the Affero GNU General Public License
 *  version 3 along with Ximdex (see LICENSE file).
 *
 *  If not, visit http://gnu.org/licenses/agpl-3.0.html.
 *
 * @author Ximdex DevTeam <dev@ximdex.com>
 * @version $Revision$
 */

namespace XimdexApi\actions;

use Flow\Exception;
use Xbuk\Properties\Manager as PropertiesManager;
use Ximdex\Models\FastTraverse;
use Ximdex\Models\Node;
use Ximdex\Modules\XBUK\Manager as XBUKManager;
use Ximdex\NodeTypes\NodeTypeConstants;
use Ximdex\Utils\Mail;
use XimdexApi\core\Request;
use XimdexApi\core\Response;
use Ximdex\Models\NodeType;
use Ximdex\Parsers\ParsingPathTo;
use Ximdex\Runtime\App;

class NodeAction extends Action
{
    const PREFIX = 'node';

    const GET = 'infonode';
    const GET_CONTENT = 'getContent/(.d|.*)';
    const SET_CONTENT = 'setContent/(.d|.*)';
    const GET_CHILDRENS = 'getChildrens/(.d|.*)';
    const ADD_NODE = 'addNode(.d|.*)';
    const REMOVE_NODE = 'removeNode(.d|.*)';

    protected const ROUTES = [
        self::GET => 'infonode',
        self::GET_CONTENT => 'getContent',
        self::SET_CONTENT => 'setContent',
        self::GET_CHILDRENS => 'getChildrens',
        self::ADD_NODE => 'addNode',
        self::REMOVE_NODE => 'removeNode'
    ];

    public static function getNode( $nodeId, $parent = null )
    {
        $result  = false;
        if ( !is_null($nodeId) && ctype_digit($nodeId) ) {
            $result = new Node($nodeId);
        } else if ( is_string($nodeId) && $parent ) {
            // If the resource is a route, we parse it looking for the node
            $parserPathTo = new ParsingPathTo();
            // currentNode is necessary when we search for a resource because it is from where we started looking
            if ( $parserPathTo->parsePathTo( $nodeId, $parent->getID() ) ) {
                $result = $parserPathTo->getNode();
            }
        } else if ( is_string($nodeId) && !$parent ) {
            $db = App::Db();
            $sql = "select IdNode from Nodes where Nodes.Name = ?  and deleted = 0 order by sortorder asc limit 1";
            $statement = $db->prepare( $sql );
            $statement->execute( [$nodeId] );
            $row = $statement->fetch();
            if ( $row["IdNode"] ) {
                $result = $row["IdNode"];
            }
        }
        return $result;
    }

    public static function getNodeContent( Node $node, $lang = null )
    {
        $response = null;

        if ( $node->nodeID ) {

            if ( $node->nodeType->isFolder() ) {
                $childrens = $node->getChildren();

                if( !empty( $childrens ) ) {

                    foreach ( $childrens as $children ) {
                        $childrenNode = new Node( $children );
                        $nameChildren = $childrenNode->getNodeName();
                        $nameExploded = explode('-id', $nameChildren);
                        $langExtension = end($nameExploded);

                        if ( !$lang ) {
                            $lang = App::getValue('DefaultLanguage');
                        }

                        if ( $childrenNode->nodeType->isStructuredDocument() && $lang ==  $langExtension ) {
                            $response = $childrenNode->getContent();
                            break;
                        }
                    }
                } else {
                    $response = null;
                }
            } else {
                $response = $node->getContent();
            }
        }
        $response = is_null( $response ) ? null : json_decode( $response );
        return $response;

    }

    public static function setNodeContent( Node $node, $content, $filename = null, $paramNodeType = null, $lang = null )
    {
        $nodeType = $node->nodeType;
        $isFolder = $nodeType->getIsFolder();

        if ( !$isFolder ) {
            $response = $node->setContent( $content );
        } else {
            if ( !$filename ) {
                return null;
            }
            // Is a folder so, we look inside for a child that is not a folder
            $childrensTree = FastTraverse::getChildren($node->getID(), ['node' => ['Name', 'idParent'], 'nodeType' =>
                ['isFolder', 'isVirtualFolder', 'IdNodeType', 'icon']], null, null, []);

            // delete the first element of the array, because it is the node itself, not its children
            array_shift($childrensTree);

            if ( !$paramNodeType ) {
                $targetIdNodeType = null;

                if ( !empty( $childrensTree) ) {
                    // We get its nodetype
                    foreach ( $childrensTree as $childrenArr ) {
                        foreach ( $childrenArr as $children ) {
                            if ( !$children["nodeType"]["isFolder"] ) {
                                $targetIdNodeType = $children["nodeType"]["IdNodeType"];
                                break 2;
                            }
                        }
                    }
                } else {
                    $allowedChildrens = $node->getCurrentAllowedChildren();
                    foreach ( $allowedChildrens as $childId ) {
                        $childNodeType = new NodeType($childId);
                        if ( !$childNodeType->isFolder() ) {
                            $targetIdNodeType = $childNodeType->getID();
                            break;
                        }
                    }
                }

                if ( !$targetIdNodeType && $paramNodeType ) {
                    return null;
                }
            } else {
                $targetIdNodeType = $paramNodeType;
            }

            // we will a child to the folder with nodetype
            $newChildId = $node->CreateNode($filename, $node->getID(), $targetIdNodeType, null);
            $childNode = new Node( $newChildId );
            $childNode->setContent( $content);
            $response = $newChildId;
        }

        return $response;
    }

    public static function getChildren( $nodeId, $onlyFirstLevel = false, $filterNodeType = null) {
        $db = App::Db();
        $result  = [];
        $sqlString = "select IdNode, IdNodeType, Name from Nodes where IdParent = ? ";

        if ( $filterNodeType ) {
            $sqlString .= " and IdNodeType = ? ";
        }

        $sqlString .= " and deleted = 0 order by sortorder asc ";
        $statement = $db->prepare( $sqlString );
        $filterParams = [ $nodeId ];

        if ( $filterNodeType ) {
            $filterParams [] = $filterNodeType;
        }

        $statement->execute( $filterParams );

        foreach ( $statement as $row )
        {
            $item = [
                'type'   => $row['IdNodeType'],
                'id'     => $row['IdNode'],
                'name'   => $row['Name'],

            ];

            if ( ! $onlyFirstLevel ) {
                $item["children"] = self::getChildren( $row['IdNode'], $onlyFirstLevel, $filterNodeType );
            }

            $result[] = $item;
        }

        return $result;
    }


    /********************************************* API METHODS *********************************************/

    public static function infonode( Request $r, Response $w )
    {
        $nodeId = $_GET['id'];
        $response = null;
        if ( !is_null($nodeId) && ctype_digit($nodeId) ) {
            $node = new Node($nodeId);
            if ( $node->GetID() ) {
                $response = $node->loadData(true);
            }
        }
        if ( is_null($response) ) {
            $w->setMessage('Node info not found')->setStatus(1);
        }
        $w->setResponse($response);
        $w->send();
    }

    public static function getContent( Request $r, Response $w )
    {
        $pathElements = explode( '/', $r->getPath() );
        $nodeId = $pathElements[2];

        $node = self::getNode($nodeId);
        $response = self::getNodeContent($node);

        if ( is_null($response) ) {
            $w->setMessage('Node content not found')->setStatus(1);
        }

        $w->setResponse($response);
        $w->send();
    }

    public static function setContent( Request $r, Response $w )
    {
        $filename = $_GET[ "filename" ];
        $dataToSet = file_get_contents( 'php://input' );

        if ( empty($dataToSet) ) {
            $w->setMessage("Nodes not found")->setStatus( 1 );
        }

        $pathElements = explode( '/', $r->getPath() );
        $nodeId = $pathElements[2];
        $response = null;

        $node = self::getNode( $nodeId );

        $response = self::setNodeContent( $node, $dataToSet, $filename );

        if ( !$response ) {
            $w->setResponse('Failed to set content to node');
        } else {
            $w->setResponse('Send');
        }

        $w->send();
    }

    public static function getChildrens(  Request $r, Response $w )
    {
        $pathElements = explode( '/', $r->getPath() );
        $nodeId = $pathElements[2];

        $result = self::getChildren( $nodeId );

        if ( !empty( $result ) ) {
            $w->setResponse( $result );

        } else {
            $w->setResponse('Node not contains childrens');
        }

        $w->send();
    }

    /**
     * @param Request $r
     * @param Response $w
     * Method to add a new node under a certain parent, the node can be of type file or of type folder
     */
    public static function addNode( Request $r, Response $w )
    {
        define ("TYPE_FILE",  'file');
        define ("TYPE_FOLDER",  'folder');


        // input parameters nodename and type
        $nodeName = $_GET[ "nodeName" ];
        $type = $_GET[ "type" ];

        if ( !$type ) {
            $w->setMessage('Type is not defined, can be FOLDER OR FILE');
            $w->setResponse( false );
            $w->send();
        }

        // input parameters parentId from path of the request
        $pathElements = explode( '/', $r->getPath() );
        $parentNodeID = $pathElements[2];
        $parentNode = self::getNode( $parentNodeID );

        if ( !$parentNode->nodeID ) {
            $w->setMessage("Target folder node not found")->setStatus( 1 );
            $w->setResponse( false );
            $w->send();
        }

        // files cannot be created out a folder
        $parentIsAFolder = $parentNode->nodeType->isFolder();
        if ( $type == TYPE_FILE && !$parentIsAFolder ) {
            $w->setMessage('Cannot create node file outside a folder');
            $w->setResponse( false );
            $w->send();
        }

        $node = new Node();

        // depending on the type of file we create a folder or a binary
        if ( $type == TYPE_FILE ) {
            $newIdNode = $node->CreateNode( $nodeName, $parentNode->nodeID, NodeTypeConstants::BINARY_FILE );
        } else {
            $newIdNode = $node->CreateNode( $nodeName, $parentNode->nodeID, NodeTypeConstants::COMMON_FOLDER );
        }

        // if an error has occurred in the creation show the error
        if ( !$newIdNode ) {
            $w->setStatus( 1 );
            $errorMsg = "";
            foreach ( $node->messages->messages as $message ){
                $errorMsg .= $message["message"] . " ";
            }
            $w->setMessage( $errorMsg );
            $w->setResponse( false );
            $w->send();
        } else {
            $node = new Node( $newIdNode );
        }

        // if the type is file and we have content to add we modify the node
        if ( $type == TYPE_FILE ) {

            // input parameters content
            $dataToSet = file_get_contents( 'php://input' );
            $response = null;

            if ( empty($dataToSet) ) {
                $w->setMessage("Content for node not found")->setStatus( 1 );
                $w->setResponse( false );
                $w->send();
            }

            $response = self::setNodeContent( $node, $dataToSet, $nodeName );
        }

        if ( !$response ) {
            $w->setMessage('Failed to set content to node');
            $w->setResponse( false );
        } else {
            $w->setMessage('Send');
            $w->setResponse( true );

        }

        $w->send();
    }

    /**
     * Method to remove a node
     */
    public static function removeNode( Request $r, Response $w )
    {
        $pathElements = explode( '/', $r->getPath() );
        $nodeId = $pathElements[2];

        $node = self::getNode( $nodeId );

        if ( !$node || !$node->nodeID ) {
            $w->setStatus( 1 );
            $w->setMessage('Failed to get node');
            $w->setResponse( false );
        } else {
            $node->delete();
        }

        $w->send();
    }

}
