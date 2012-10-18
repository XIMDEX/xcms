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
 *  @version $Revision: 7825 $
 */



Class Latex_checkbox extends Latex {

	var $x;
	var $y;
	var $width;
	var $height;
	var $style;
	var $enabled;
	var $name;

	function setPos($x,$y){
		$this->x = $x;
		$this->y = $y;
	}
	function setDim($width, $height){
		$this->width = $width;
		$this->height = $height;
	}
	function setEnabled($enabled){
		$this->enabled = $enabled;
	}
	function setName($name){
		$this->name = $name;
	}

	function add_checkbox() {
		// if name is empty , it generates a pseudorandom name
		if (empty($this->name)) {
			$this->name = 'checkbox' . $this->x . $this->y;
		}
		$buffer = '';
		if (!empty($this->class)) {
			$buffer = "\\" . $this->class . "\n";
		}
//		$buffer .= "\\entradaCheck{".$this->x."}{".$this->y."}{".$this->width."}{".$this->height."}{true}{".$this ->name ."}{" . $this->script . "}\n";
		$this->width -= 2;
		$this->height -= 2;
		if ($this->script != "") {
			$script = '\A{\JS{\\'.$this->script.'}}';
		}else{
			$script='';
		}
		$buffer .= '\begin{textblock}{'.$this->width.'}('.$this->x.', '.$this->y.')'."\n".
			'\checkBox[\DV{true}'.$script.']{'.$this ->name.'}{'.$this->width.'mm}{'.$this->height.'mm}{true}'."\n".
			'\end{textblock}'."\n";
		return $buffer;
	}

}

?>
