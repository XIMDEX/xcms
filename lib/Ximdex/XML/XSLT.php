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


use Ximdex\Logger;

class XSLT  {

    protected $xsltprocessor;
    protected $xml;
    protected $xsl;
    protected $xsd;

    public function __construct() {

        $this->xsltprocessor = new \XSLTProcessor();
        $this->xml = new \DOMDocument();
        $this->xsl = new \DOMDocument();
    }

    public function setXML($xml_file) {
        $res = @$this->xml->load($xml_file);
        if ($res === false)
        {
            return false;
        }
        return true;
    }

    public function setXSL($xsl_file) {
        //Warnings when $xsl_file doesn't exist
        if(file_exists($xsl_file)){
            if (@$this->xsl->load($xsl_file) === false)
            {
                $error = \Ximdex\Error::error_message();
                if ($error)
                    $error = str_replace('DOMDocument::loadXML(): ', '', $error);
                $error = 'Error loading file ' . $xsl_file . ' (' . $error . ')';
                if (isset($GLOBALS['InBatchProcess']) and $GLOBALS['InBatchProcess'])
                    logger::error($error);
                $GLOBALS['errorInXslTransformation'] = $error;
                return false;
            }
            if (@$this->xsltprocessor->importStyleSheet($this->xsl) === false)
            {
                $error = \Ximdex\Error::error_message();
                if ($error)
                    $error = str_replace('XSLTProcessor::importStylesheet(): ', '', $error);
                $error = 'Error processing file ' . $xsl_file . ' (' . $error . ')';
                if (isset($GLOBALS['InBatchProcess']) and $GLOBALS['InBatchProcess'])
                    Logger::error($error);
                $GLOBALS['errorInXslTransformation'] = $error;
                return false;
            }
        }
        return true;
    }

    public function setXSD($xsd) {
        // TODO: implement.
    }

    public function setParameter($options, $namespace = '') {

        return $this->xsltprocessor->setParameter($namespace, $options);
    }

    public function validate() {

    }

    public function process() {
        error_reporting(E_ALL^E_WARNING);
        return $this->xsltprocessor->transformToXML($this->xml);
        error_reporting(E_ALL);
    }

}

