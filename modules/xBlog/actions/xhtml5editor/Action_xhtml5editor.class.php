<?php

use Ximdex\MVC\ActionAbstract;
use Ximdex\Runtime\App;

class Action_xhtml5editor  extends ActionAbstract
{
 public function index()
 {
     $idnode = $this->request->getParam('nodeid');
     $action = App::getValue("UrlRoot") . "/modules/xBlog/api/public/$idnode";
     $this->render(array('action' => $action), NULL, 'iframe.tpl');
 }
}