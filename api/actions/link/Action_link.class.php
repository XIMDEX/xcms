<?php


class Action_link extends AbstractAPIAction implements SecuredAction {
	public function index($request, $response){

	}
	public function create($request, $response) {
        $parentId = $request->getParam("nodeid");
        $name = $request->getParam("name");
        $description = $request->getParam("description");
        $url = $request->getParam("url");

        $b64decoded = base64_decode($description, true);
        $description = $b64decoded === false ? urldecode(stripslashes($content)) : urldecode($b64decoded);

        if ($parentId == "" || $name == "" || $url == "") {
            $this->createErrorResponse('Some required parameters are missing');
            return false;
        }

        $id = $this->createNodeLink($name, $url, $description, $parentId);
        if($id<=0){
        	$this->createErrorResponse("An error ocurred creating the link.");
        	return false;
        }
        $response->header_status('200');
        $respContent = array('error' => 0, 'data' => array('nodeid' => $id));
        $response->setContent($respContent);
    }


    protected function createNodeLink($name, $url, $description, $idParent){
    	if(empty($description)){
            $description = " ";    
        }

		$data = array('NODETYPENAME' => 'LINK',
				'NAME' => $name,
				'PARENTID' => $idParent,
				'IDSTATE' => 0,
				'CHILDRENS' => array (
					array ('URL' => $url),
					array ('DESCRIPTION' => $description)
				)
			);
			
		$bio = new baseIO();
		$result = $bio->build($data);
		
		if ($result > 0) {
			$link = new Link($result);
			$link->set('ErrorString','not_checked');
			$link->set('CheckTime',time());
			$link->update();
		}
		return $result;
    }




}