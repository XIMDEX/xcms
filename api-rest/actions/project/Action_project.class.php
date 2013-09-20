<?php

class Action_project implements NoSecuredAction{
	public function get($request,$response){
	
		$id = $request->getParam('id');
		if(!empty($id)){
			$response->setContent(array("msg"=>"Tiene id $id"));
		}	
		else{
			$response->setContent(array("msg"=>"NO Tiene id"));

		}	

	}

	public function post($request,$response){

                $id = $request->getParam('body');
                if(!empty($id)){
                        $response->setContent(array("msg"=>"Tiene content $id"));
                }
                else{
                        $response->setContent(array("msg"=>"NO Tiene content"));

                }

        }		

}

?>
