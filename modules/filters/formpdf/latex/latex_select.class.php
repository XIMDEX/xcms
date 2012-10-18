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



class Latex_select extends Latex {
	var $x;
	var $y;
	var $width;
	var $height;
	var $options;
	var $style;
	var $type;

	function setPos($x,$y){
		$this->x = $x;
		$this->y = $y;
	}

	function setDim($width, $height){
		$this->width = $width;
		$this->height = $height;
	}

	function setType($type) {
		$this->type = $type;
	}

	function setStyle($style){
		$this->style = $style;
	}

	function setDefault($default){
		$this->default = $default;
	}

	function setName($name){
		$this->name = $name;
	}

	function setOptions($options){
		$this->options = $options;
	}

	function create_checkbox() {
	  	$exceptions = array("width","height","minSepLabel","rowspan","heightRow");
		$string_style  = $this->make_attrs_string($this->style,$exceptions);
		$string_style .= "\\symbolchoice{check}";
		$resultado = "\\everyCheckBox{".$string_style."}\n";
	  	$id = 0;
		if(!$this->style["minSepLabel"]){
		   $this->style["minSepLabel"] = "5mm";
		}
		if(!$this->style["minSepOpt"]){
		   $this->style["minSepOpt"] = "2mm";
		}
		$posY = $this->y;
		foreach ($this->options as $label_opt => $value) {
			$text_opt = $this->wrap_item_list($label_opt,$this->style);
			$resultado .= "\\renewcommand{\\texto}{\\hspace{".$this->style["minSepLabel"]."}".parent::special_chars($text_opt). "}\n";
			$resultado .= "\entradaCheck{".$this->x."}{".$posY."}{".$this->width."}{".$this->height."}{" . $this->name. $id . "}{".$value."}{".$this->script."}\n";
			$posY += $this->height + (-1 * $this->style["minSepOpt"]);
			$posY .= "mm";
		  	$id++;
		}
		return $resultado;
	}
	function create_radio() {

		$exceptions = array("width","height","minSepLabel","rowspan","heightRow");
		$string_style = $this->make_attrs_string($this->style, $exceptions);
		$string_style .= "\\symbolchoice{circle}";
		$resultado = "\\everyRadioButton{".$string_style."}";
	  	$id = 0;
		if(!$this->style["minSepLabel"]){
		   $this->style["minSepLabel"] = "5mm";
		}
		if(!$this->style["minSepOpt"]){
		   $this->style["minSepOpt"] = "2mm";
		}
		$posY = $this->y;

		foreach ($this->options as $label_opt => $value) {
			$text_opt = $this->wrap_item_list($label_opt,$this->style);
			$resultado .= "\\renewcommand{\\texto}{\\hspace{".$this->style["minSepLabel"]."}".parent::special_chars($text_opt). "}\n";
			$resultado .= "\entradaRadio{".$this->x."}{".$posY."}{".$this->width."}{".$this->height."}{" . $this->name . "}{".$value."}{".$this->script."}\n";
			$posY += $this->height + (-1 * $this->style["minSepOpt"]);
			$posY .= "mm";
		  $id++;
		}
		return $resultado;
	}

	function create_combo() {
		$exceptions = array("width","height","minSepLabel","colorlabel");
		$string_options = "";
		foreach ($this->options as $key => $value) {
			$string_options .= "[(" . $key . ")(" . $value . ")] ";
		}
		$this->style["minSepLabel"] = "5mm";
		$string_style = $this->make_attrs_string($this->style,$exceptions);
		$resultado = '';
		if (!empty($this->class)) {
			$resultado .= "\\" . $this->class . "\n";
		}
		if (empty($this->name)) {
			$this->name = 'select' . $this->x . $this->y;
		}
//		$resultado .= "\entradaCombo{".$this->x."}{".$this->y."}{".$this->width."}{".$this->height."}{" . $string_options . "}{".$this->name . $id."}{".$this->script."}\n";
		if ($this->script != "") {
			$script = '[\A{\JS{\\'.$this->script.'}}]';
		}else{
			$script='';
		}
		$resultado .= '\begin{textblock}{'.$this->width.'}('.$this->x.', '.$this->y.')'."\n".
			'\comboBox'.$script.'{'.$this ->name.'}{'.$this->width.'mm}{'.$this->height.'mm}{'.$string_options.'}'."\n".
			'\end{textblock}'."\n";
		return $resultado;
	}

}

?>
