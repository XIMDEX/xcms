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

namespace Ximdex\XML ;
use FsUtils ;

class XML {

    // Constants defining encoding
    const UTF8 = 'UTF-8';
    const ISO88591 = 'ISO-8859-1';

    // Parser xml
    protected $_xmlParser;
    protected $encoding;
    protected $xmlSrc;
    // Informaci�n parseada
    protected $data;
    // Resultado del parseo
    public $result;
    // Cadena de errores
    public $error;

    /**
     *
     */
    public function __constructor( ) {

    }


    // Getter
    public function getError () {
        return $this->error;
    }

    public function getResult () {
        return $this->result;
    }

    public function getEncoding(){
        return $this->encoding;
    }
    // Setter
    public function setEncoding ($encoding = \Ximdex\XML\XML::UTF8) {
        $this->encoding = $encoding;
    }

    public function setXmlSrc ($xmlSrc) {
        $this->xmlSrc = $xmlSrc;
    }

    public function setXmlFile($file) {
        $this->setXmlSrc(FsUtils::file_get_contents($file));
    }

    public function load () {

        $this->_xmlParser = xml_parser_create ("");

        if ($this->encoding == null){
            $this->setEncoding(\Ximdex\XML\XML::UTF8);
        }

        xml_parser_set_option($this->_xmlParser, XML_OPTION_TARGET_ENCODING, $this->encoding);
        xml_set_object ($this->_xmlParser, $this);
        // Parser options
        xml_parser_set_option ($this->_xmlParser, XML_OPTION_SKIP_WHITE, 1);
        xml_parser_set_option( $this->_xmlParser, XML_OPTION_CASE_FOLDING, 0);
        xml_set_element_handler ($this->_xmlParser, "_tag_open", "_tag_close");
        xml_set_character_data_handler ($this->_xmlParser, "_tag_data");
        // Parse the XML document
        $this->result = xml_parse ($this->_xmlParser, $this->xmlSrc, true);

        // Asigna la cadena de errores
        if (!$this->result) {
            $this->error = "Error al parsear el XML: ".xml_error_string (xml_get_error_code ($this->_xmlParser)).
                " - linea ".xml_get_current_line_number ($this->_xmlParser).
                " - columna ".xml_get_current_column_number ($this->_xmlParser).
                " - byte ".xml_get_current_byte_index ($this->_xmlParser);
        }

        xml_parser_free ($this->_xmlParser);

        return $this->result;
    }

    public function getXml () {
        return $this->data;
    }

    public function getObject($content) {
        $this->setXmlSrc($content);

        $domDocument = new DOMDocument();
        $domDocument->encoding = 'UTF-8';
        $domDocument->loadXML(utf8_encode($this->xmlSrc));

        if (!empty($domDocument)) {
            return $domDocument;
        }

        XMD_Log::error('The loaded document has some errors');
        return false;
    }

    // Procesa una etiqueta de apertura
    protected function _tag_open ($parser, $tag, $attribs) {
        $this->data .= "<".strtolower ($tag);
        foreach ($attribs as $key => $value) {
            $this->data .= ' '.$key.'="'.$value.'"';
        }
        $this->data .= ">";
    }

    // Procesa el contenido de una etiqueta
    protected function _tag_data ($parser, $cdata) {
        $this->data .= $cdata;
    }

    // Procesa las etiquetas de cierre
    protected function _tag_close ($parser, $tag) {
        $this->data .= "</".strtolower ($tag).">";
    }
}