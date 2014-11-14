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



require("XSLT.iface.php");

class XSLT implements I_XSLT {

	protected $xsltprocessor;
	protected $xml;
	protected $xsl;
	protected $xsd;

	public function __construct() {

		$this->xsltprocessor = new XSLTProcessor();
		$this->xml = new DOMDocument();
		$this->xsl = new DOMDocument();
	}

	public function __destruct() {

	}

	public function setXML($xml_file) {
		
		$this->xml->load($xml_file);
	}

	public function setXSL($xsl_file) {

		$this->xsl->load($xsl_file);
		$this->xsltprocessor->importStyleSheet($this->xsl);
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

?>