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
 *  @version $Revision: 7740 $
 */



class Latex_form extends Latex {

	var $x;
	var $y;
	var $width;
	var $height;
	var $url;
	var $method;
	var $indep;

	function Latex_form () {
	}

	function setDim ($width, $height) {
		$this->width = $width;
		if ((int)$height > 0) {
			$this->height = "-".$height;
		} else {
			$this->height = $height;
		}
	}
	function setUrl($url){
		$this->url = $url;
	}
	function setMethod($method){
		$this->method = $method;
	}
	function setIndep($indep){
		$this->indep = $indep;
	}

	function open_form () {
		if (!empty ($this->class)) {
			$res = "\\".$this->class."\n";
		}
		if ($this->indep) {
			return $res."\\begin{Form}".
				"[action=".$this->url.
				",encoding=html,".
				"method=".$this->method."]\n";
		}
	}

	function close_form () {
		if ($this->indep) {
			return "\\end{Form}\n";
		}
	}
}

?>
