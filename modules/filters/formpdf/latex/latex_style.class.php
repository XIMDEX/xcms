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




class Latex_style extends Latex {

	var $handler;
	var $array_style = array();

	function make_def_style($name, $style) {
		$background_color = "1,1,1";
		$border_color = "0,0,0";
		$margin_color = "1,1,1";
		$border_size = "0mm";
		$text_color = "0,0,0";
		$parindent = "0mm";
		$font_family = "ptm";
		$font_size = "\\large";
		$font_weight = "\\mdseries";
		$font_style = false;
		$text_decoration = false;
		$font_weight = "";
		$border_style = "";

		if (array_key_exists ("font-family",$style)) {
			$font_family = $style["font-family"];
			$font_family = $this->font_family_translate($font_family);
		}
		if (array_key_exists ("font-size",$style)) {
			$font_size = $style["font-size"];
			$font_size = $this->font_size_translate($font_size);
		}
		if (array_key_exists ("font-weight",$style)) {
			$font_weight = $style["font-weight"];
			$font_weight = $this->font_weight_translate($font_weight);
		}
		else {
			$font_weight = "\\mdseries";
		}
		if (array_key_exists ("font-style",$style)) {
			$font_shape = $style["font-style"];
			$font_shape = $this->font_style_translate($font_shape);
			$item = $font_shape . $item;
		}
		if (array_key_exists ("text-decoration",$style)) {
			$text_decoration = $style["text-decoration"];
			$text_decoration = $this->text_decoration_translate($text_decoration);
		}
		if (array_key_exists ("background-color",$style)) {
			$background_color = $this->_hexToLatex($style["background-color"]);
			$margin_color = $background_color;
		}
		if (array_key_exists ("pagecolor", $style)) {
			$pagecolor = $this->_hexToLatex($style["pagecolor"]);
		}
		if (array_key_exists ("border-color",$style)) {
			$border_color = $this->_hexToLatex($style["border-color"]);
		}
		if (array_key_exists ("color",$style)) {
			$text_color = $this->_hexToLatex($style["color"]);
		}
		if (array_key_exists ("text-indent",$style)) {
			$parindent = $style["text-indent"];
		}
/*
		if (array_key_exists ("border-style",$style)) {
			$border_style = $style["border-style"];
		}
*/

		$estilo = "";
		$estilo .= "\\def\\".$name."{%\n";
		$estilo .= "\\definecolor{background}{rgb}{".$background_color."}\n";
		$estilo .= "\\definecolor{margin}{rgb}{".$margin_color."}\n";
		$estilo .= "\\definecolor{border}{rgb}{".$border_color."}\n";
		$estilo .= "\\definecolor{text}{rgb}{".$text_color."}\n";
		$estilo .= "\\color{text}\n";
		$estilo .= "\\fontfamily{".$font_family."}\n";
		$estilo .= $font_weight ."\n";
//		$estilo .= $font_shape ."\n";
		$estilo .= $font_size ."\n";
//		$estilo .= $text_align."\n";
//		$estilo .= $border_style."\n";
		$estilo .= "\\selectfont\n";
		if (!empty($pagecolor)) {
			$estilo .= "\\definecolor{".$name."_color}{rgb}{".$pagecolor."}\n";
			$estilo .= "\\pagecolor{".$name."_color}\n";
		}
		$estilo .= "}\n";

		$this -> handler = fopen("/tmp/styles.tex","a");
		fwrite($this -> handler,$estilo);
		fclose($this -> handler);
	}

	function make_acro_style($name, $style, $command) {
		$background_color = "1 1 1";
		$margin_color = "1,1,1";
		$border_color = "1 1 1";
		$border_size = "0mm";
		$text_color = "0 0 0";
		$parindent = "0mm";
		$font_family = "ptm";
		$font_size = "\\large";
		$font_weight = "\\mdseries";
		$font_style = false;
		$text_decoration = false;

		if (array_key_exists ("font-family",$style)) {
			$font_family = $style["font-family"];
			$font_family = $this->font_family_translate($font_family);
		}
		if (array_key_exists ("font-size",$style)) {
			$font_size = $style["font-size"];
			$font_size = $this->font_size_translate($font_size);
		}
		if (array_key_exists ("font-weight",$style)) {
			$font_weight = $style["font-weight"];
			$font_weight = $this->font_weight_translate($font_weight);
		}
		if (array_key_exists ("font-style",$style)) {
			$font_shape = $style["font-style"];
			$font_shape = $this->font_style_translate($font_shape);
			$item = $font_shape . $item;
		}
		if (array_key_exists ("text-decoration",$style)) {
			$text_decoration = $style["text-decoration"];
			$text_decoration = $this->text_decoration_translate($text_decoration);
		}
		if (array_key_exists ("background-color",$style)) {
			$background_color = $this->_hexToLatex($style["background-color"]);
			$margin_color = $background_color;
		}
		if (array_key_exists ("pagecolor", $style)) {
			$pagecolor = $this->_hexToLatex($style["pagecolor"]);
		}
		if (array_key_exists ("border-color",$style)) {
			$border_color = $this->_hexToLatex($style["border-color"]);
		}
		if (array_key_exists ("color",$style)) {
			$text_color = $this->_hexToLatex($style["color"]);
		}
		if (array_key_exists ("text-indent",$style)) {
			$parindent = $style["text-indent"];
		}

		$estilo = "";
		$estilo .= "\\def\\".$name."{%\n";
		$estilo .= "\\$command{\\BG{" . str_replace(',', ' ', $background_color) . "}\n";
		$estilo .= "\\textColor{".str_replace(',', ' ', $text_color)."}}}\n";
		$estilo .= "\\definecolor{background}{rgb}{".$background_color. "}\n";
		$estilo .= "\\definecolor{margin}{rgb}{".$margin_color."}\n";
		$estilo .= "\\definecolor{border}{rgb}{".$border_color."}\n";
		$estilo .= "\\definecolor{text}{rgb}{".$text_color."}\n";
		$estilo .= "\\color{text}\n";
		$estilo .= "\\fontfamily{".$font_family."}\n";
		$estilo .= $font_weight ."\n";
//		$estilo .= $font_shape ."\n";
		$estilo .= $font_size ."\n";
		$estilo .= "\\selectfont\n";
		if (!empty($pagecolor)) {
			$estilo .= "\\definecolor{".$name."_color}{rgb}{".$pagecolor."}\n";
			$estilo .= "\\pagecolor{".$name."_color}\n";
		}
		$estilo .= "}\n";

		$this -> handler = fopen("/tmp/styles.tex","a");
		fwrite($this -> handler,$estilo);
		fclose($this -> handler);
	}
}

?>
