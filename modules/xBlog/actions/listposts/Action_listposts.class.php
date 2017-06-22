<?php
/**
 *  \details &copy; 2011  Open Ximdex Evolution SL [http://www.ximdex.org]
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
use Ximdex\Models\Node;
use Ximdex\Models\User;
use Ximdex\MVC\ActionAbstract;
use DiDom\Document;
use Ximdex\Services\NodeType;

ModulesManager::file("/actions/browser3/Action_browser3.class.php");
ModulesManager::file('/inc/utils/XHTMLEditorUtils.php', 'xBlog');

class Action_listposts extends ActionAbstract
{
    // Main method: shows the initial form
    function index()
    {
        $this->addCss('/actions/listposts/resources/css/welcome.css', 'xBlog');
        $this->addJs('/actions/listposts/resources/js/controller.js', 'xBlog');
        $values = [];
        $idnode = $this->request->getParam('nodeid');
        $node = new Node($idnode);
        $values["documentsid"] = $node->GetChildByName("documents");
        $values["user"] = \Ximdex\Utils\Session::get("user_name");
        $this->render($values, "index.tpl", 'default-3.0.tpl');
    }

    public function getPosts(){
        $idnode = $this->request->getParam('nodeid');
        $user = new User(\Ximdex\Utils\Session::get("userID"));
        $lastestDocs = $user->getLastestNodes(NodeType::XHTML5_DOC, $idnode);
        $consultaTitle = "//x-meta[@data-key='title']";
        $consultaMeta = "//x-meta[@data-key='image']";
        $consultaIntro = "//x-meta[@data-key='intro']";
        $consultaP = "//p";
        $consultaImg = '//img';
        $htmlEditorUtils = new HTMLEditorUtils();
        foreach($lastestDocs as $k => $d){
            $node = new Node($d['IdNode']);

            $version = $node->GetLastVersion();
            if (!empty($version)) {
                $lastestDocs[$k]['Published'] = $version["Published"];
                $lastestDocs[$k]['PublishDate'] = (int) $version["Date"];
            }

            $doc = new Document('<root>' . $node->GetContent() . '</root>');

            $entradasTitle = $doc->xpath($consultaTitle);
            $title = $lastestDocs[$k]['name'];
            if(count($entradasTitle) > 0){
                $dataTitle = $entradasTitle[0]->getAttribute('data-value');
                if(!empty($dataTitle)){
                    $title = $entradasTitle[0]->getAttribute('data-value');
                }
            }
            $lastestDocs[$k]['title'] = $title;


            $entradasIntro = $doc->xpath($consultaIntro);

            if(count($entradasIntro) > 0){
                $lastestDocs[$k]['intro'] = $entradasIntro[0]->getAttribute('data-value');
            }else{
                $entradasP = $doc->xpath($consultaP);
                if(count($entradasP) > 0) {
                    $lastestDocs[$k]['intro'] = $entradasP[0]->text();
                }else{
                    $lastestDocs[$k]['intro'] = "";
                }
            }

            $entradasMeta = $doc->xpath($consultaMeta);
            if(count($entradasMeta) > 0){
                $htmlEditorUtils->checkAndReplaceMacro($entradasMeta[0], 'data-value', 'data-xid');
                $lastestDocs[$k]['imgpreview'] = $entradasMeta[0]->getAttribute('data-value');
            }else{
                $entradasImg = $doc->xpath($consultaImg);
                if(count($entradasImg) > 0){
                    $htmlEditorUtils->checkAndReplaceMacro($entradasImg[0], 'src', 'data-xid');
                    $lastestDocs[$k]['imgpreview'] = $entradasImg[0]->getAttribute('src');
                }else{
                    $lastestDocs[$k]['imgpreview'] = "";
                }
            }
            $lastestDocs[$k]['ModificationDate'] = (int) $lastestDocs[$k]['ModificationDate'];
        }
        $this->sendJSON($lastestDocs);
    }
}