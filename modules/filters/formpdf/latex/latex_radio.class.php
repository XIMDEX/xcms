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



class Latex_radio extends Latex {

	function create_radio($data, $options, $style, $label_style) {
		
		$exceptions = array("width","height","minSepLabel","rowspan","heightRow");
		$string_style = $this -> make_attrs_string($style,$exceptions);
		$string_style .= "\\symbolchoice{circle}";
	
		$id = 0;
		$style["minSepLabel"] = "5mm";
		$cmdName = "button" . $data["name"];
		$cmd  = "\\newcommand{\\" . $cmdName. "}[3]{%\n";
		$cmd .= "\\radioButton[" . $string_style . "]{#1}{" . $style["width"] ."mm}{". $style["height"] ."mm}{#2}\hspace{" . $style["minSepLabel"] . "}#3}\n";
		$cuadro_opciones = "";
		$marco_opciones[$id++] = "#" . ($id);
		
		foreach ($options as $label_opt => $value) {
			$arg1 = "{" . $data["name"] . "}";
			$arg2 = "{" . $value . "}";
			$arg3 = "{" . $label_opt . "}";
			$marco_opciones[$id++] = "#" . ($id);
			$cuadro_opciones .= "{\\" . $cmdName . $arg1 . $arg2 . $arg3 . "}";
		}
		
		$content = $this -> wrap_table_inline($marco_opciones,$style);
		$this->save_cmd($cmd);
		$wrap_cmd  ="\\newcommand{\\tabular" . $cmdName . "}[" . $id ."]{" . $content ."}\n";
		$this->save_cmd($wrap_cmd);
		$data["label"] = "\\rule{0pt}{4ex}" . $data["label"];
		$head = $this -> wrap_item_list($data["label"],$label_style);
		
		if (array_key_exists("align",$label_style)) {
			$primer = substr($label_style["align"],0,1);
			$primer = strtolower($primer);
			$head = "\\multicolumn{1}{" . $primer . "}{" . $head . "}";
		}
		
		$resultado = "\\tabular" . $cmdName .  "{" . $head . "}" . $cuadro_opciones;
		return $resultado;
	}

//
}

?>
