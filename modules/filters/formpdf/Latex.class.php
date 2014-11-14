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




/**
 * XIMDEX_ROOT_PATH
 */
if (!defined("XIMDEX_ROOT_PATH")) {
	define("XIMDEX_ROOT_PATH", realpath(dirname(__FILE__) . "/../../../"));
}

ModulesManager::file('/inc/xml/XmlBase.class.php');

class Latex {

	var $style;
	var $columns = array();
	var $handler;
	var $renderer;
	var $element;
	var $class;
	var $script;

	var $open_table;
	var $body_table = array();

	var $str_cab = "";
	var $str_cab_aux = "";

	function latex() {
	}

	function setClass($className) {
		$this->class = trim($className);
	}

	function setPos($x, $y) {
		$this->x = $x;
		$this->y = $y;
	}

	function setStyle($style) {
		$this->style = $style;
	}

	function setScript($script) {
		$this->script = $script;
	}

	function style_label($label, $style) {
		$color = "0,0,0";
		if (array_key_exists("colorlabel",$style)) {
			$color = $style["colorlabel"];
		}
		$resultado = "{\\color[rgb]{" . $color . "}" . $label . "}";
		return $resultado;
	}


	function trans_element($attr, $value) {
		$resultado = $value;
		if (preg_match("/size/",$attr) > 0) {
			$resultado = intval($value);
		}
		if(preg_match("/color/",$attr) > 0) {
			$append = "";
			if($attr == "color") {
				$append = " rg";
			}
			$colores = explode(",",$value);
			$resultado = $colores[0] . " " . $colores[1] . " " . $colores[2] . $append;
		}
		if ($attr == "border-style") {
			$value = ucfirst($value);
			$resultado = substr($value,0,1);
		}
		if ($attr == "font-family") {
			$mapper = array();
			if (array_key_exists($value,$mapper)) {
				$mapper["helvetica"] = "Helv";
				$resultado = $mapper[$value];
			} else {
				$resultado = "Helv";
			}
		}
		return $resultado;
	}

	function make_attrs_string($style, $exceptions) {
		$mapper = array();
		$mapper["background-color"] = "\\BG";
		$mapper["border-color"] = "\\BC";
		$mapper["color"] = "\\textColor";
		$mapper["border-style"] = "\\S";
		$mapper["symbol"] = "\\symbolchoice";
		$mapper["length"] = "\\MaxLen";
		$mapper["border-size"] = "\\W";
		$mapper["font-size"] = "\\textSize";
		$mapper["font-family"] = "\\textFont";

		$string_style = "";

		foreach($style as $key=>$value) {
			if (!in_array($key, $exceptions)) {
				if(array_key_exists($key,$mapper)) {
				$string_style .= $mapper[$key] . "{" . $this->trans_element($key,$value) . "}";
				}
			}
		}
		return $string_style;
	}

	function font_family_translate($family) {
		$mapper = array();
		$mapper["serif"] = "sf";
		$mapper["sans-serif"] = "sf";
		$mapper["helvetica"] = "phv";
		$mapper["times"] = "ptm";
		$mapper["bookman"] = "pbk";
		if (array_key_exists($family,$mapper)) {
			$resultado = $mapper[$family];
		} else {
			$resultado = "phv";
		}
		return $resultado;
	}

	function font_size_translate($size) {
		$size = round($size,10);
		$resultado = "\\fontsize{" . ($size) . "mm}{11mm}\\selectfont";
/*
		$resultado = "";
		if ($size <= 11) {
			$resultado = "\\tiny";
		} elseif ($size < 12) {
			$resultado = "\\scriptsize";
		} elseif($size < 14) {
			$resultado = "\\footnotesize";
		} elseif($size < 15) {
			$resultado = "\\small";
		} elseif($size < 17) {
			$resultado = "\\normalsize";
		} elseif($size < 18) {
			$resultado = "\\large";
		} elseif($size < 22) {
			$resultado = "\\Large";
		} elseif($size < 24) {
			$resultado = "\\LARGE";
		} elseif($size < 27) {
			$resultado = "\\huge";
		} else {
			$resultado = "\\Huge";
		}
*/
		return $resultado;
	}

	function font_weight_translate($weight) {
		$mapper = array();
		$mapper["bold"] = "\\bfseries ";
		$mapper["normal"] = "\\mdseries ";
		return $mapper[$weight];
	}

	function font_style_translate($style) {
		$mapper = array();
		$mapper["italic"] = "\\itshape ";
		$mapper["normal"] = "\\upshape ";
		return $mapper[$style];
	}

	function text_decoration_translate($decoration) {
		$mapper = array();
		$mapper["underline"] = "\\underline ";
		$mapper["none"] = " ";
		return $mapper[$decoration];
	}

	function wrap_item_list($item, $style = 0) {
		$background_color = "1,1,1";
		$border_color = "1,1,1";
		$border_size = "0mm";
		$text_color = "0,0,0";
		$parindent = "0mm";
		$font_family = "ptm";
		$font_size = "\\large";
		$font_weight = "\\mdseries";

		if (is_array($style)) {
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
			if (array_key_exists ("color",$style)) {
				$text_color = $style["color"];
			}
		}
		$item = trim($item);
		$item = "\\textcolor[rgb]{" . $text_color . "}{" . $item . "}";
		$item = "\\cambiafuente{" . $font_family . "}{" . $font_size . "}{" . $font_weight . "}{" . $item . "}";
		return $item;
	}

	function wrap_frame_list($item, $style) {
		$background_color = "1,1,1";
		$border_color = "1,1,1";
		$border_size = "0mm";

		if (array_key_exists ("background-color",$style)) {
			$background_color = $style["background-color"];
		}
		if (array_key_exists ("border-color",$style)) {
			$border_color = $style["border-color"];
		}

		if (array_key_exists ("border-size", $style)) {
			$border_size = $style["border-size"] . "mm";
		}

		$buffer  = "\\setlength{\\anchocaja}{0.9\\textwidth}\n";
		$buffer .= "\\definecolor{background}{rgb}{" . $background_color ."}\n";
		$buffer .= "\\definecolor{border}{rgb}{" . $border_color ."}\n";
		$buffer .= "\\setlength{\\fboxrule}{" . $border_size . "}\n";
		$buffer .= "\\addtolength{\\anchocaja}{-2\\fboxsep}\n";
		$buffer .= "\\addtolength{\\anchocaja}{-2\\fboxrule}\n";
		$buffer .= "\\fcolorbox{border}{background}{%\n";
		$buffer .= "\\begin{minipage}{\\anchocaja}\n";
		$buffer .= $item . "\n";
		$buffer .= "\\end{minipage}}\n";
		return $buffer;
	}

	function wrap_link($text, $url, $style) {
		$text = $this->wrap_span($text,$style);
		$item = "\\href{" . $url . "}{". $text . "}";
		return $item;
	}

	function save_cmd($string) {
		$handler = fopen("/tmp/command.tex","a");
		fwrite($handler,$string);
		fclose($handler);
	}

	function add_space($type, $amount = 0) {
		switch ($type) {
			case "vertical":
				$buffer = "\n\\vspace{" . $amount . "mm}\n";
			break;

			case "page":
				$buffer = "\\newpage\n";
			break;
		}
		return $buffer;
	}

	function put_javascript($jsElements) {
		$javascript = "";
		foreach ($jsElements as $name => $codeScript) {
			$javascript .= "\\begin{defineJS}{\\" . trim ($name) . "}\n";
			//$javascript .= "// code inserted automatically";
			$javascript .= $codeScript;
			$javascript .= "\\end{defineJS}\n";
		}
		return $javascript;
	}

	function add_script($name, $content) {
		if (!is_null($name)) {
			$javascript  = "\\begin{defineJS}{\\" . trim ($name) . "}\n";
			$javascript .= $content;
			$javascript .= "\n\\end{defineJS}\n";
			return $javascript;
		}
	}

	function special_chars($string) {
		$busqueda = array("@á@", "@é@", "@í@", "@ó@", "@ú@", "@ñ@", "@Á@", "@É@", "@Í@", "@Ó@", "@Ú@", "@Ñ@");
		$reemplazar = array("'a", "'e", "'i", "'o", "'u", "~n", "'A", "'E", "'I", "'O", "'U", "~N");
		$string = preg_replace($busqueda, $reemplazar, $string);
		return $string;
	}

	/**
	 * Recieves a color with hexadecimal format and transforms it to a latex code
	 *
	 * @param hex code of colour $color
	 * @return latex code of returned colour
	 */
	function _hexToLatex($color) {
		if (strlen($color) == 7 && $color[0] == '#') {
			$components['red'] = number_format(hexdec($color[1] . $color[2])/255, 2, '.', '.');
			$components['green'] = number_format(hexdec($color[3] . $color[4])/255, 2, '.', '.');
			$components['blue'] = number_format(hexdec($color[5] . $color[6])/255, 2, '.', '.');
			$color = implode(',', $components);
		}
		return $color;
	}

	function filterSpecialChars($content, $emptyReplace = false) {
		$specialChars = array('"' => '``',
								"¿" => "?`",
								"¡" => "!`",
								"\\" => "\\textbackslash",
								"<BR />" => "\\linebreak[4]",
								"<br />" => "\\linebreak[4]",
								"<BR>" => "\\linebreak[4]",
								"<br>" => "\\linebreak[4]",
								"{" => "\\textbraceleft",
								"}" => "\\textbraceright",
								'$' => "\\textdollar",
								"&" => "\\&",
								"#" => "\\#",
								"%" => "\\%",
								"_" => "\\textunderscore",
								"^" => "\\textasciicircum",
								"~" => "\\~{}",
								"|" => "\\textbar",
								"<" => "\\textless",
								">" => "\\textgreater",
								"·" => "$\\cdot$",
								"©" => "\\textcopyright",
								"®" => "\\textregistered",
								"º" => "\\textordmasculine",
								"ª" => "\\textordfeminine",
								"¬" => "\\neg",
								"ø" => "\\O",
								"¤" => "\\textcurrency",
								"¢" => "\\textcent",
								"þ" => "\\th",
								"ç" => "\\c{c}",
								"Ç" => "\\c{C}",
								"á" => "\\'{a}",
								"é" => "\\'{e}",
								"í" => "\\'{i}",
								"ó" => "\\'{o}",
								"ú" => "\\'{u}",
								"Á" => "\\'{A}",
								"É" => "\\'{E}",
								"Í" => "\\'{I}",
								"Ó" => "\\'{O}",
								"Ú" => "\\'{U}",
								"â" => "\\^{a}",
								"ê" => "\\^{e}",
								"î" => "\\^{i}",
								"ô" => "\\^{o}",
								"û" => "\\^{u}",
								"Â" => "\\^{A}",
								"Ê" => "\\^{E}",
								"Î" => "\\^{I}",
								"Ô" => "\\^{O}",
								"Û" => "\\^{U}",
								"à" => "\\`{a}",
								"è" => "\\`{e}",
								"ì" => "\\`{i}",
								"ò" => "\\`{o}",
								"ù" => "\\`{u}",
								"À" => "\\`{A}",
								"È" => "\\`{E}",
								"Ì" => "\\`{I}",
								"Ò" => "\\`{O}",
								"Ù" => "\\`{U}",
								"ä" => '\\"{a}',
								"ë" => '\\"{e}',
								"ï" => '\\"{i}',
								"ö" => '\\"{o}',
								"ü" => '\\"{u}',
								"ÿ" => '\\"{y}',
								"Ä" => '\\"{A}',
								"Ë" => '\\"{E}',
								"Ï" => '\\"{I}',
								"Ö" => '\\"{O}',
								"Ü" => '\\"{U}',
								"¾" => '\\"{Y}',
								"ý" => "\\'{y}",
								"Ý" => "\\'{Y}",
								"¶" => "\\textparagraph",
								"þ" => "\\TH",
								"æ" => "\\ae",
								"ß" => "\\ss",
								"ð" => "\\dh",
								"ñ" => "\\~n",
								"Ñ" => "\\~N",
								"«" => "\\guillemotleft",
								"»" => "\\guillemotright",
								"¢" => "\\mathcent",
								"µ" => "\\mu",
								"¬" => "$\\neg$");

		if (XmlBase::isUtf8 ($content)) {
			$content = utf8_decode ($content);
		}
		$content = html_entity_decode ($content);

		if (!$emptyReplace) {
			$keys = array_keys($specialChars);
			$values = array_values($specialChars);
			$content = str_replace($keys, $values, $content);
		}
		else {
			$keys = array_keys($specialChars);
			$values = array_fill(0, count($specialChars), "");
			$content = str_replace($keys, $values, $content);
		}

		return $content;
	}
// Latex
}
?>