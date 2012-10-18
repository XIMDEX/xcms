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



class Latex_button extends Latex {

	var $x;
	var $y;
	var $width;
	var $height;
	var $style;
	var $label;
	var $name;
	var $type;

	function setPos($x,$y){
		$this->x = $x;
		$this->y = $y;
	}
	function setDim($width, $height){
		$this->width = $width;
		$this->height = $height;
	}
	function setStyle($style){
		$this->style = $style;
	}
	function setLabel($label){
		$this->label = $label;
	}
	function setName($name){
		$this->name = $name;
	}
	function setButtonType($type){
		$this->type = $type;
	}

	function create_button() {
		$exceptions = array("width", "height", "minSepLabel");
		$string_style = $this->make_attrs_string($this->style, $exceptions);
		$buffer = '';
		$script = '';

		if (empty($this->name)) {
			$this->name = 'Push';
		}

		if (!empty ($this->class)) {
			$buffer = "\\".$this->class."\n";
		}
//		$buffer .= "\\entradaButton{".$this->x."}{".$this->y."}{".$this->width."}{".$this->height."}{".$this->label."}{".$this->name."}{".$this->script."}\n";
		if (!empty ($this->script)) {
			$script = '\A{\JS{\\'.$this->script.'}}';
		}

		$this->label = $this->filterSpecialChars($this->label, true);

		switch ($this->type) {
			// Submit button
			case "submit":
				$buffer .= '\begin{textblock}{'.$this->width.'}('.$this->x.','.$this->y.')'."\n".
					'\Submit{\textbf{'.$this->label.'}}'."\n".
					'\end{textblock}'."\n".
					'}'."\n";
			break;

			// Reset button
			case "reset":
				$buffer .= '\begin{textblock}{'.$this->width.'}('.$this->x.','.$this->y.')'."\n".
					'\Reset{\textbf{'.$this->label.'}}'."\n".
					'\end{textblock}'."\n".
					'}'."\n";
			break;

			// Default button
			default:
				$buffer .= '\begin{textblock}{'.$this->width.'}('.$this->x.','.$this->y.')'."\n".
					'\pushButton[\CA{'.$this->label.'}'.$script.']{'.$this->name.'}{'.$this->width.'mm}{'.$this->height.'mm}'."\n".
					'\end{textblock}'."\n".
					'}'."\n";
			break;
		}

		return $buffer;
	}

//
}

?>
