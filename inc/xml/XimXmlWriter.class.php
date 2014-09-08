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
 *  @author Ximdex DevTeam <dev@ximdex.com>
 *  @version $Revision$
 */




if (!defined ("XIMDEX_ROOT_PATH"))
	define ("XIMDEX_ROOT_PATH", realpath (dirname (__FILE__)."/../../"));

include_once (XIMDEX_ROOT_PATH."/inc/xml/XML.class.php");
include_once (XIMDEX_ROOT_PATH."/inc/xml/XmlBase.class.php");

define ('MARK_QUOTE', 'MARK_QUOTE');
define ('MARK_LESS_THAN', 'MARK_LESS_THAN');
define ('MARK_GREATER_THAN', 'MARK_GREATER_THAN');


class XimXmlWriter {

    var $xml;
    var $xmlHeader;
    var $docType;
    var $encoding;
    var $indent;
    var $stack = array();

    function XimXmlWriter($encoding = XML::UTF8, $indent = "  ") {
        $this->encoding = $encoding;
        $this->indent = $indent;
        $this->xmlHeader = sprintf("%s?xml version=%s1.0%s encoding=%s$encoding%s?%s\n",
        		MARK_LESS_THAN, MARK_QUOTE, MARK_QUOTE, MARK_QUOTE, MARK_QUOTE, MARK_GREATER_THAN);
		$this->_setDocType ();
    }

    function _setDocType($root = "document") {
    	$entities = ($this->encoding == XML::ISO88591) ?
    		' ['."\n".XmlBase::generateHtmlEntities().']' : "";
		$this->docType = '<!DOCTYPE '.$root.' '.$entities.'>'."\n";
    }

    function push($element, $attributes = array(), $_stack = true) {
        $this->_indent();
        $this->xml .= MARK_LESS_THAN.$element;
        foreach ($attributes as $key => $value) {
            $this->xml .= sprintf(" %s=%s%s%s", $key, MARK_QUOTE, $value, MARK_QUOTE);
        }
        if ($_stack) {
	        $this->xml .= MARK_GREATER_THAN . "\n";
	        $this->stack[] = $element;
        }
    }

    function element($element, $content, $attributes = array()) {
    	$this->push($element, $attributes, false);

        if (!isset($content)) {
        	$content = '';
        }

        $this->xml .= MARK_GREATER_THAN.$content.MARK_LESS_THAN . '/'.$element.MARK_GREATER_THAN."\n";
    }

    function emptyelement($element, $attributes = array()) {
    	$this->element($element, '', $attributes);
    }

    function pop() {
        $element = array_pop($this->stack);
        $this->_indent();
        $this->xml .= sprintf("%s/$element%s\n", MARK_LESS_THAN, MARK_GREATER_THAN);
    }

    function getXml() {
        $ret = $this->xmlHeader.
        	(!empty ($this->docType) ? $this->docType : "").
        	(!empty ($this->xml) ? $this->xml : "");
        $search = array(MARK_LESS_THAN, MARK_GREATER_THAN, MARK_QUOTE);
        $replace = array('<', '>', '"');
        return str_replace($search, $replace, XmlBase::recodeSrc($ret, $this->encoding));
    }

    function _indent() {
        $this->xml .= str_repeat($this->indent, count($this->stack));
    }

    function injectXml($xmlSrc) {

		$xmlParser = new XML();
		
		if(!XmlBase::isUtf8($xmlSrc)) {
			$xmlSrc = XmlBase::recodeSrc($xmlSrc, $this->encoding);
		}
		$baseTag = "root_".Utils::generateRandomChars(10);
		$tmpXml = "<".$baseTag.">".$xmlSrc."</".$baseTag.">";
		$xmlParser->setEncoding($this->encoding);
		$xmlParser->setXmlSrc($tmpXml);
		$ret = $xmlParser->load();
		if($ret) {
			$xmlSrc = $xmlParser->getXml();
			$search = array("<".$baseTag.">", "</".$baseTag.">", '<', '>', '"');
			$replace = array('', '', MARK_LESS_THAN, MARK_GREATER_THAN, MARK_QUOTE);
			$xmlSrc = str_replace($search, $replace, $xmlSrc);
			$this->xml .= $xmlSrc;
		}
		else {
			XMD_Log::info("Fragmento XML no inyectado: ".$xmlSrc);
		}
		return $ret;
    }
}
?>