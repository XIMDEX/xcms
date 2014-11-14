<?php

class Action_project implements APIRestAction, SecuredAction
{

    private static $NODE_TYPE_PROJECT = 5013;

    /**
     * <p>Read project information</p>
     * @param type $request
     * @param type $response
     */
    public function get($request, $response)
    {
        $id = $request->getParam('id');
        $username = $request->getParam('XimUser');

        if (!empty($id)) {
            $nodeService = new NodeService();
            if ($nodeService->existsNode($id) && $nodeService->hasPermissionOnNode($username, $id)) {
                $nodeInfo = $nodeService->getNodeInfo($id);
                unset($nodeInfo['nodeType']);
                $response->header_status(200);
                $response->setContent(array("error" => 0, "data" => $nodeInfo));
            } else {
                $response->setContent(array("error" => 1, "msg" => "The requested project with id " . $id . " does not exist"));
                $response->header_status(404);
            }
        } else {
            /* get all projects for the user */
            /* TODO: How to do it, query DB directly or do it through NodeService */
        }
    }

    /**
     * <p>Create project</p>
     * @param type $request
     * @param type $response 
     */
    public function post($request, $response)
    {

        $id = $request->getParam('id');
        $name = $request->getParam('name');
        $nodeService = new NodeService();

        $rootNode = $nodeService->getRootNode();

        if (empty($name)) {
            $response->setContent(array("error" => 1, "msg" => "The name for the project is missing"));
            $response->header_status(400);
            return;
        }

        if (empty($id)) {
            $newId = $this->addProject($rootNode, $name);
            if ($newId == null || $newId < 0) {
                $response->setContent(array("error" => 1, "msg" => "The new project could not be created"));
                $response->header_status(400);
            } else {
                // Created header
                $response->header_status(201);
                $response->setContent(array("error" => 0, "data" => array("id" => $newId, "name" => $name)));
            }
        } else {

            /* Create node with the supplied id */
            /* TODO: How to do it. Create a method in NodeService to do it,
             *  modifiying Node->createNode to allow an Id for the new to-be-created node
             */
        }
    }

    /**
     * <p>Update project information</p>
     * @param type $request
     * @param type $response 
     */
    public function put($request, $response)
    {
         echo "asdasd";
        $id = $request->getParam('id');
        $username = $request->getParam('XimUser');
        $name = $request->getParam('name');
        $nodeService = new NodeService();

        if (empty($id)) {
            $response->setContent(array("error" => 1, "msg" => "The id for the project is missing"));
            $response->header_status(400);
            return;
        }

       
        $node = $nodeService->getNode($id);

        if ($node == null) {
            $response->header_status(404);
            $response->setContent(array("error" => 1, "msg" => "The project does not exist"));
            return;
        }

        if (empty($name)) {
            $response->setContent(array("error" => 0, "data" => "The project has not been modified because the name for the project is missing"));
            $response->header_status(304);
            return;
        }

        if (!$nodeService->hasPermissionOnNode($username, $id)) {
            $response->header_status(400);
            $response->setContent(array("error" => 1, "msg" => "You don't have permission to modify the project"));
            return;
        }
        

        if ($node->SetNodeName($name)) {
            $response->header_status(200);
            $response->setContent(array("error" => 0, "data" => "The project has been modified successfully"));
        } else {
            $response->header_status(304);
            $response->setContent(array("error" => 0, "data" => "The project has not been modified because another project has got the same name"));
        }
    }

    /**
     * <p>Delete project information</p>
     * @param type $request
     * @param type $response 
     */
    public function delete($request, $response)
    {
        $id = $request->getParam('id');
        $username = $request->getParam('XimUser');
        $nodeService = new NodeService();

        if (!empty($id)) {
            if (!$nodeService->existsNode($id)) {
                $response->setContent(array("error" => 1, "msg" => "The requested project with id " . $id . " does not exist and it can not be deleted"));
                $response->header_status(404);
                return;
            }

            if ($nodeService->hasPermissionOnNode($username, $id)) {
                $removed = $nodeService->deleteNode($id);
                if ($removed) {
                    $response->header_status(200);
                    $response->setContent(array("error" => 0, "data" => "The project has been deleted successfully"));
                } else {
                    $response->setContent(array("error" => 1, "msg" => "The requested project with id " . $id . " does not exist and it can not be deleted"));
                    $response->header_status(404);
                }
            } else {
                $response->header_status(400);
                $response->setContent(array("error" => 1, "msg" => "You don't have permission to delete the project"));
            }
        } else {
            /* get all projects for the user */
            /* TODO: How to do it, query DB directly or do it through NodeService */
        }
    }

    /**
     * <p>Adds a new project</p>
     * 
     * @return the new id for the created node or null if node could not be created
     */
    private function addProject($rootProjectsNode, $name)
    {
        $folder = new Node();
        $idFolder = $folder->CreateNode($name, $rootProjectsNode->GetID(), self::$NODE_TYPE_PROJECT, null);

        // Adding channel and language properties
        if ($idFolder > 0) {
            $node = new Node($idFolder);
            $node->setProperty('channel', 10001); // By default only html
        }
        return $idFolder;
    }

}

?>