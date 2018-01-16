<?php

use Ximdex\Models\NodeType;
use Ximdex\MVC\ActionAbstract;


class Action_setextensions extends ActionAbstract
{
    public function index()
    {
        $commonFolderNodeType = new NodeType(\Ximdex\NodeTypes\NodeType::COMMON_ROOT_FOLDER);
        $commonAllowedExtensions = $commonFolderNodeType->getAllowedExtensions();

        $this->addCss('/actions/setextensions/resources/css/style.css');

        $values = array('commonAllowedExtensions' => json_encode($commonAllowedExtensions));

        $this->render($values, null, 'default-3.0.tpl');
    }

    public function update_extensions()
    {
        $post = file_get_contents('php://input');
        $extensions = json_decode($post, true)["states"];
        $patt = '/^((\w+|\*))?(,(\w+|\*))*\s*$/i';
        $res = true;

        foreach($extensions as $id => $ext){
            $res &= preg_match($patt, trim($ext["extension"]));
            $extensions[$id]["extension"] = preg_split("/,/", trim($ext["extension"]), 0, PREG_SPLIT_NO_EMPTY);
            if(in_array("*", $extensions[$id]["extension"])){
                $extensions[$id]["extension"] = array("*");
            }
        }
        if(!$res){
            $values = array(
                'result' => "fail",
                'message' => _("There are errors on extensions")
            );
            $this->sendJSON($values);
        }

        foreach($extensions as $id => $ext){
            foreach($extensions as $id2 => $ext2){
                if($id < $id2){
                    $count = count(array_intersect($ext["extension"], $ext2["extension"]));
                    $res &= $count == 0;
                }
            }
        }

        if(!$res){
            $values = array(
                'result' => "fail",
                'message' => _("Extensions have ambiguous values")
            );
            $this->sendJSON($values);
        }

        foreach($extensions as $ext){
            $e = new \Ximdex\Models\RelNodeTypeMimeType($ext["id"]);
            if(count($ext["extension"])==0){
                $e->set("extension","");
            }elseif(isset($ext["extension"][0]) && $ext["extension"][0]=="*"){
                $e->set("extension","*");
            }else{
                $e->set("extension",";".implode(";",$ext["extension"]).";");
            }
            $e->update();
        }

        $values = array(
            'result' => "ok",
            'message' => _("The extensions have been updated")
        );

        $this->sendJSON($values);
    }

}