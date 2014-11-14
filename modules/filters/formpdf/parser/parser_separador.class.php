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




class Separador extends ParserRoot {
	function build() {
		//$amount = 0;
		$tipo = $this -> element -> getAttribute("tipo");
		$amount = 0;
		//$amount = $this -> element -> getText();
		$array_style = $this -> style -> get_style_element($this -> element);
		if (array_key_exists("height",$array_style)) {
			$tipo = "vertical";
			$amount = $array_style["height"];
		} elseif (array_key_exists("width",$array_style)) {
			$tipo = "horizontal";
			$amount = $array_style["width"];
		}

		$buffer = $this -> renderer -> add_space($tipo,$amount);
		fwrite($this->handler,$buffer);
	}
}

?>