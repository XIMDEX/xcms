<?php
use DiDom\Document;
use DiDom\Element;
use Ximdex\Models\Link;
use Ximdex\Models\Node;
use Ximdex\Runtime\App;

class HTMLEditorUtils
{

    public function getContentToEditor($content){
        // FIX: to avoid 13 ascii char
        $content = preg_replace('/\r\n/', "\n", $content);
        $doc = new Document($content);
        // Search for Macros in img tags
        $images = $doc->find('img');
        foreach($images as $image){
            $this->checkAndReplaceMacro($image, 'src', 'data-xid');
        }

        // Search for Macros in a tags
        $as = $doc->find('a');
        foreach($as as $a){
            $this->checkAndReplaceLinks($a);
        }

        $xmetas = $doc->find('x-meta[data-value]');
        foreach($xmetas as $xmeta){
            $this->checkAndReplaceMacro($xmeta, 'data-value', 'data-xid');
        }

        $tidy = new \tidy();
        $content = $tidy->repairString($doc->html(), array(
            'output-xhtml' => true,
            'show-body-only' => true,
            'new-inline-tags' => 'x-meta'
        ), 'utf8');

        return $content;
    }
    public function checkAndReplaceMacro(Element $element, $attr, $destAttr){
        $regex = "/@@@RMximdex\.pathto\(([,-_#%=\.\w\s]+)\)@@@/";
        if(0 >= preg_match_all($regex, $element->$attr, $matches, PREG_PATTERN_ORDER)) {
            return;
        }
        if(!isset($matches[1]) || !isset($matches[1][0])) {
            return;
        }
        $nodeid = $matches[1][0];
        $node = new Node($nodeid);
        if($node->GetID() <= 0){
            return;
        }
        $info = $node->GetLastVersion();
        $dataFilesPath = App::getValue('UrlRoot') . App::getValue('FileRoot') . '/' . $info['File'];
        $element->$attr = $dataFilesPath;
        $element->$destAttr = $nodeid;
    }
    private function checkAndReplaceLinks(Element $element){
        $regex = "/@@@RMximdex\.pathto\(([,-_#%=\.\w\s]+)\)@@@/";
        if(0 >= preg_match_all($regex, $element->href, $matches, PREG_PATTERN_ORDER)) {
            return;
        }
        if(!isset($matches[1]) || !isset($matches[1][0])) {
            return;
        }
        $nodeid = $matches[1][0];
        $link = new Link($nodeid);
        $url = $link->get('Url');
        $element->setAttribute('href', $url);
        $element->setAttribute('data-xid', $nodeid);
    }
    private function reverseCheckAndReplaceLinks(Element $element){
        $nodeid = $element->getAttribute('data-xid');
        $element->href = "@@@RMximdex.pathto({$nodeid})@@@";
        $element->removeAttribute('data-xid');
    }
    public function setContentFromEditor($content){
        $doc = new Document($content);
        // Search for Macros in img tags
        $images = $doc->find('img[data-xid]');
        foreach($images as $image){
            $this->reverseReplaceMacro($image, 'src');
        }

        // Search for Macros in a tags
        $as = $doc->find('a[data-xid]');
        foreach($as as $a){
            $this->reverseCheckAndReplaceLinks($a);
        }

        $xmetas = $doc->find('x-meta[data-xid]');
        foreach($xmetas as $xmeta){
            $this->reverseReplaceMacro($xmeta, 'data-value');
        }

        $tidy = new \tidy();
        $content = $tidy->repairString($doc->html(), array(
            'output-xhtml' => true,
            'show-body-only' => true,
            'new-inline-tags' => 'x-meta',
            'quote-nbsp' => false,
            'preserve-entities' => false,
            'quote-ampersand' => false,
        ), 'utf8');

        return $content;
    }
    private function reverseReplaceMacro(Element $element, $destAttr){
        $nodeid = $element->getAttribute('data-xid');
        $node = new Node($nodeid);
        if($node->GetID() <= 0){
            return;
        }
        $info = $node->GetLastVersion();
        $dataFilesPath = App::getValue('UrlRoot') . App::getValue('TempRoot') . $info['File'];
        $element->$destAttr = "@@@RMximdex.pathto({$nodeid})@@@";
        $element->removeAttribute('data-xid');
    }
}