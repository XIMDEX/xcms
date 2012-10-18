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




/**
 * DEPRECATED!
 */

class Latex_table extends Latex {
	
	// arrays with info of los rowspan y colspan
	var $array_colspan = array();
	var $array_rowspan = array();
	var $total_filas;
	var $total_columnas;
	
	function open_table($style) {
		$mayusculas = array("B","C","D","E","F","G","H");
		$buffer = "";
		if($style["columns"] !="") {
			$string_columnas = $style["columns"];
			$columnas = explode("|",$string_columnas);
			$num_columnas = count($columnas);
		} else {
			$columnas[0] = "1";
			$num_columnas = 1;
		}
		$cab = "";
		$style["columnBorder"]  = "";
		$style["rightBorder"]  = "";
		$style["topBorder"]  = "";
		if (array_key_exists("border-width", $style) && ((array_key_exists("border-color", $style) && $style["border-color"] != "1,1,1") || !array_key_exists("border-color", $style))) {
			$cab = "|";
			$style["columnBorder"]  = "|";
			$style["rightBorder"]  = "|";
			$style["topBorder"]  = "\\hline\n";
		}
		
		$offset = 0;
		
		if ($num_columnas > 1) {
			$offset = 3;
		}
		
		$color_tabla = "";
		
		if (array_key_exists ("background-color",$style)) {
			$color_tabla = ">{\\columncolor[rgb]{" . $style["background-color"] . "}}";
		}
		
		if (preg_match("/%/",$style["width"]) > 0) {
			$size  = intval($style["width"])/100;
			$size .= "\\textwidth";
		} else {
			$size = $style["width"] . "mm";
		}
		
		$buffer .= "\\setlength{\\arrayrulewidth}{" . $style["border-width"] . "mm}\n";
		$buffer .= "\\arrayrulecolor[rgb]{" . $style["border-color"] . "}\n";
		$buffer .= "\\setlength{\\colA}{" . $size ."}\n";
		$buffer .= "\\addtolength{\\colA}{-2\\tabcolsep}\n";
		$buffer .= "\\addtolength{\\colA}{-1.5\\arrayrulewidth}\n";
		
		for ($co = 0; $co < $num_columnas; $co++) {
			if(($co+1) < $num_columnas) {
				$line_border = $style["columnBorder"];
			} else {
				$line_border = $style["rightBorder"];
			}
			if (preg_match("/%/",$style["width"]) > 0) {
				$size  = (intval($style["width"]) / 100) * floatval($columnas[$co]);
				$size .= "\\textwidth";
			} else {
				$size = $style["width"] . "mm";
			}
			$buffer .= "\\setlength{\\col" .$mayusculas[$co] . "}{" . $size ."}\n";
			$buffer .= "\\addtolength{\\col" . $mayusculas[$co] . "}{-2\\tabcolsep}\n";
			$buffer .= "\\addtolength{\\col" . $mayusculas[$co] . "}{-1.5\\arrayrulewidth}\n";
			$cab .= $color_tabla . "p{\\col" . $mayusculas[$co] ."}" . $line_border;
		}
		
		$buffer  .= "\n\n\\vspace{1.5\\arrayrulewidth}\n";
		$buffer  .= "\begin{tabular}{" . $cab . "}\n";
		$buffer  .= $style["topBorder"];
		return $buffer;
	}

	function close_table($style) {
		$buffer = "";
		$buffer .= "\\hline\n\n";
		$buffer .= "\\end{tabular}\n\n";
		//$buffer .= "\\caption[]{".$style['caption']."}\n\n";

		return $buffer;
	}

	function add_row($style) {
		$style["rowBorder"]  = "";
		$buffer = "\\\\[" . $style["height"] . "mm]\n" . $style["rowBorder"];
		return $buffer;
	}
	
	/**
	 * Method of adition of columns in tabular enviroment of latex.
	 * This method use the arrays array_colspan and array_rowspan which
	 * contain info about cell distribution.
	 *
	 * @param string $element Element to put in the column
	 * @param number $col  number of actual column
	 * @param array $style Array of styles to apply on cell
	 */
	function add_col($element, $fila, $columna, $style) {
		// Actual tuplas rowspans y colspans
		$tupla_rowspan = $this->obtener_tupla($this->array_rowspan, $fila, $columna);
		$tupla_colspan = $this->obtener_tupla($this->array_colspan, $fila, $columna);
		
		$rowspan = $this->get_rowspan($tupla_rowspan);
		$colspan = $this->get_colspan($tupla_colspan);
		
		if ($fila == 0) {
			// Fist row receives a special treatment
			if (!is_null($rowspan)) {
				// write a multirow
				$element = "\\multirow{".$rowspan."}{*|}{".$element."}";
			} elseif (!is_null($colspan)) {
				// write a multicolumn
				//$element = "\\multicolumn{".$colspan."}{l|}{".$element."}";
				$mayusculas = array("B","C","D","E","F","G","H");
				if(array_key_exists ("multicolum", $style)){
					$multicolum = $style["multicolum"];
					$formato = "p{\\colA}";
				}
				else{
					$multicolum = 1;
					$formato = "p{\\col".$mayusculas[$col]."}";
				}   
				$element = "\\multicolumn{".$colspan."}{|>{\columncolor[rgb]{" . $style["background-color"] . "}}" . $formato . "|}{ " . $element . "}";
			} else {
				// write a normal cell (multicolumn 1)
				//$element = "\\multicolumn{1}{l|}{".$element."}";
				$mayusculas = array("B","C","D","E","F","G","H");
				if(array_key_exists ("multicolum", $style)){
					$multicolum = $style["multicolum"];
					$formato = "p{\\colA}";
				}
				else{
					$multicolum = 1;
					$formato = "p{\\col".$mayusculas[$col]."}";
				}   
				$element = "\\multicolumn{1}{|>{\columncolor[rgb]{" . $style["background-color"] . "}}" . $formato . "|}{ " . $element . "}";
			}
		} else {
			// the other rows are treated normally
			if (!is_null($rowspan)) {
				// write a multirow
				$element = "\\multirow{".$rowspan."}{*|}{".$element."}";
				for ($c = $columna; $c >= 0; $c--) {
					if ($this->despues_de_rowspan($fila, $c)) {
						$element = " & ".$element;
					}
				}
			} elseif (!is_null($colspan)) {
				// write a multicolumn
				$mayusculas = array("B","C","D","E","F","G","H");
				if(array_key_exists ("multicolum", $style)){
					$multicolum = $style["multicolum"];
					
					$size = (1 / $this->total_columnas) * ($colspan);
					
					if ($size == 1) {
						$formato = "p{\\colA}";
					} else {
						// reducing size...
						$size = $size - 0.056;
						$formato = "p{".$size."\\textwidth}";
					}
				
				} else {
					$multicolum = 1;
					$formato = "p{\\col".$mayusculas[$col]."}";
				}
				//$element = "\\multicolumn{".$colspan."}{l|}{".$element."}";
				$element = "\\multicolumn{".$colspan."}{|>{\columncolor[rgb]{" . $style["background-color"] . "}}" . $formato . "|}{ " . $element . "}";
				for ($c = $columna; $c >= 0; $c--) {
					if ($this->despues_de_rowspan($fila, $c)) {
						$element = " & ".$element;
					}
				}
			} else {
				// first cell is puts as multicolum 1, so it ensures
				// multicolumn for all the cells.
				$mayusculas = array("B","C","D","E","F","G","H");
				if(array_key_exists ("multicolum", $style)){
					$multicolum = $style["multicolum"];
					$formato = "p{\\colA}";
				}
				else{
					$multicolum = 1;
					//$formato = "p{\\col".$mayusculas[$col]."}";
					$formato = "p{\\colB}";
				}
				//$element = "\\multicolumn{1}{|l|}{".$element."}";
				$element = "\\multicolumn{1}{|>{\columncolor[rgb]{" . $style["background-color"] . "}}" . $formato . "|}{ " . $element . "}";
				
				// We should identify if a cell is into a rowspan or it is "free".
				// It traverses rows in a decreasing order until first row (row 0)
				// to check for any rowspan above which it begins.
				if ($this->despues_de_rowspan($fila, $columna)) {
					// Here it is a free cell which it is not into a rowspand or colspan.
					// First of all is check how many previous rowspan there are.
					$num_rowspan = $this->calcular_rowspan($fila, $columna);
					
					// Now it puts & before the element
					for ($i = 1; $i <= $num_rowspan; $i++) {
						$element = " & ".$element;
					}
					
				} else {
					$acumulador = 1;
					for ($f = $fila - 1; $f >= 0; $f--) {
						$acumulador++;
						// obtaining tuplas of rowspan y colspan
						$tupla_rowspan = $this->obtener_tupla($this->array_rowspan, $f, $columna);
						// there is a tupla with rowspan data.
						if ($tupla_rowspan) {
							$rowspan = $this->get_rowspan($tupla_rowspan);
							if ($acumulador == $rowspan) {
								// This cell which is going to be written,
								// is part of a rowspan, then it is not written.
								// and it attach a & to the element.
								$element = " & ".$element;
							}
						}
					}
				}
			}
		}
		
		// After the first column it always go to the next 
		// with a &
		if ($columna > 0) {
			$buffer = " & " . $element;
		} else {
			$buffer = $element;
		}
		
		// it returns the buffer for latex writting
		return $buffer;
	}

	// This method find the tupla where data of passed column and row
	// have been saved. Valid for rowspan and colspan
	function obtener_tupla($a, $f, $c) {
		for ($i = 0; $i < count($a); $i++) {
			if (($a[$i]['fila'] == $f) && ($a[$i]['columna'] == $c)) {
				return $a[$i];
			}
		}
	}
	
	// It obtains the rowspan tupla of the array rowspan $a
	// from a cell which could not be who 
	// start the rowspan.
	function obtener_tupla_rowspan($a, $f, $c) {
		$tupla_rowspan = $this->obtener_tupla($a, $f, $c);
		if (!$tupla_rowspan) {
			if ($f >= 0) {
				return $this->obtener_tupla_rowspan($a, $f - 1, $c);
			} else {
				return null;
			}
		} else {
			return $tupla_rowspan;
		}
	}
	
	function get_rowspan($tupla) {
		return $tupla['rowspan'];
	}
	
	function get_colspan($tupla) {
		return $tupla['colspan'];
	}
	
	/**
	 * Given a cell in (f, c) it returns true if cell (f, c-1)
	 * is part of a rowspan. It is necessary to know
	 * if we need to put & or not before the cell
	 */
	function despues_de_rowspan($fila, $columna) {
		$acumulador = 1;
		for ($f = $fila - 1; $f >= 0; $f--) {
			$acumulador++;
			$tupla_rowspan = $this->obtener_tupla($this->array_rowspan, $f, $columna);
			if ($tupla_rowspan) {
				$rowspan = $this->get_rowspan($tupla_rowspan);
				if ($acumulador <= $rowspan) {
					return true;
				}
			}
		}
		return false;
	}
	
	/**
	 * This method returns the number of cells filled by a rowspan
	 * in row which passes through ($fila, $columna)
	 */
	function calcular_rowspan($fila, $columna) {
		
		$num_rowspan = 0;
		// To each column, from first, it checks the array_rowspan cada columna, desde la primera, se chequea el array_rowspan
		// obtaining the tupla for this row. If row does not start a rowspan 
		// it up a row
		for ($c = 0; $c <= $this->total_columnas - 1; $c++) {
			
			// It obtains the rowspan tupla which passes by that row
			$tupla_rowspan = $this->obtener_tupla_rowspan($this->array_rowspan, $fila, $c);
			$rowspan = $this->get_rowspan($tupla_rowspan);
			$rowspan--;
			if ($rowspan >= $fila) {
				$num_rowspan++;
			}
		}
		
		return $num_rowspan;
	}

//
} 

?>
