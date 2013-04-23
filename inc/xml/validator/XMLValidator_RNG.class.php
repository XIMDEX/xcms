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



class XMLValidator_RNG {

	protected $_errors = null;

	public function getErrors() {
		return $this->_errors;
	}

	public function validate($schema, $xmldoc) {

		// ----- ¡¡¡ ÑAPA !!! -----
		// Se suprime la validacion de atributos de docxap, ya que estos no son definidos en las PVD
		// no siempre existiran los mismos en distintos XML.
		$xmldoc = preg_replace('/<docxap\s(.[^>]*)>/', '<docxap xmlns:xim="http://www.ximdex.com/">', $xmldoc);
//		debug::log($xmldoc);
		// ----- ¡¡¡ ÑAPA !!! -----


		// Clear errors...
		$this->_errors = array();

		$domdoc = new DOMDocument();
	
		$result = $domdoc->loadXML($xmldoc);

		if (!$result || strtoupper(get_class($domdoc)) != 'DOMDOCUMENT') {
			$this->_errors[] = "Se esta intentando validar un XML mal formado.";
			return false;
		}

		// We need to set the error handler, DOMDocument don't give us the errors
		set_error_handler(array($this, '_error_handler'));
		$ret = $domdoc->relaxNGValidateSource($schema);
		restore_error_handler();

		// Correct the result of the validation, see _findDTDError()
		$dtdError = $this->_findDTDError();
		if ($dtdError !== null) {
			unset($this->_errors[$dtdError]);
			if (count($this->_errors) == 0) $ret = true;
		}

		return $ret;
	}

	public function _error_handler($errno, $error) {

		$ret = preg_match('#:\s(.[^:]*)$#ims', $error, $matches);

		$this->_errors[] = $matches[1];
		// Let PHP error handler do his work!
		return false;
	}

	/**
	 * Detect if the validator was expecting a DTD.
	 * We want to validate against an RNG schema but validator always look for a DTD.
	 * PHP bug?
	 * DOMDocument options?
	 */
	private function _findDTDError() {

		$ret = null;
		$i = 0;
		$count = count($this->_errors);

		while ($i < $count && $ret === null) {
			$error = $this->_errors[$i];
			if (strpos(strtoupper($error), 'NO DTD FOUND !EXPECTING AN ELEMENT , GOT NOTHING') !== false) {
				$ret = $i;
			}
			$i++;
		}

		return $ret;
	}

}


?>
